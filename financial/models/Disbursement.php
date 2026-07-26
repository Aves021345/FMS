<?php
/**
 * Disbursement Model
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a disbursement request (status: Pending). Accountant proposes this.
 */
function createDisbursement(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO disbursementmanagement 
            (supplier_id, ap_id, bank_account_id, disbursement_date, amount, payment_method, reference_no, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')"
    );
    $stmt->execute([
        $data['supplier_id'],
        $data['ap_id'],
        $data['bank_account_id'],
        $data['disbursement_date'],
        $data['amount'],
        $data['payment_method'],
        $data['reference_no'] ?? null,
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Mark a disbursement as Released after journal posting + balance updates are done.
 */
function releaseDisbursement(PDO $pdo, int $disbursementId, int $journalId, int $approvedBy): void
{
    $stmt = $pdo->prepare(
        "UPDATE disbursementmanagement 
         SET journal_id = ?, approved_by = ?, status = 'Released' 
         WHERE disbursement_id = ?"
    );
    $stmt->execute([$journalId, $approvedBy, $disbursementId]);
}

/**
 * Get a single disbursement with supplier, AP invoice, and bank account details joined in.
 */
function getDisbursementById(PDO $pdo, int $disbursementId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT d.*, s.supplier_name, ap.invoice_no, ap.balance AS ap_balance, ba.account_name AS bank_account_name
         FROM disbursementmanagement d
         JOIN suppliers s ON s.supplier_id = d.supplier_id
         JOIN accountspayable ap ON ap.ap_id = d.ap_id
         JOIN bankaccounts ba ON ba.bank_account_id = d.bank_account_id
         WHERE d.disbursement_id = ?"
    );
    $stmt->execute([$disbursementId]);
    return $stmt->fetch();
}

/**
 * List all disbursements, optionally filtered by status.
 */
function getAllDisbursements(PDO $pdo, ?string $status = null): array
{
    $sql = "SELECT d.*, s.supplier_name, ap.invoice_no, ba.account_name AS bank_account_name
            FROM disbursementmanagement d
            JOIN suppliers s ON s.supplier_id = d.supplier_id
            JOIN accountspayable ap ON ap.ap_id = d.ap_id
            JOIN bankaccounts ba ON ba.bank_account_id = d.bank_account_id";

    if ($status) {
        $sql .= " WHERE d.status = ? ORDER BY d.disbursement_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql .= " ORDER BY d.disbursement_date DESC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * Reduce a bank account's current_balance after a disbursement.
 */
function reduceBankBalance(PDO $pdo, int $bankAccountId, float $amount): void
{
    $stmt = $pdo->prepare("SELECT current_balance FROM bankaccounts WHERE bank_account_id = ?");
    $stmt->execute([$bankAccountId]);
    $currentBalance = $stmt->fetchColumn();

    if ($currentBalance === false) {
        throw new Exception('Bank account not found.');
    }

    $newBalance = round($currentBalance - $amount, 2);

    $update = $pdo->prepare("UPDATE bankaccounts SET current_balance = ? WHERE bank_account_id = ?");
    $update->execute([$newBalance, $bankAccountId]);
}