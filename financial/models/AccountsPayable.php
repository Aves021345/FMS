<?php
/**
 * Accounts Payable Model
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a new AP invoice record (status: Open, balance = full amount).
 */
function createAPInvoice(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO accountspayable 
            (supplier_id, invoice_no, invoice_date, due_date, amount, amount_paid, balance, status, journal_id, created_by)
         VALUES (?, ?, ?, ?, ?, 0, ?, 'Open', ?, ?)"
    );
    $stmt->execute([
        $data['supplier_id'],
        $data['invoice_no'],
        $data['invoice_date'],
        $data['due_date'],
        $data['amount'],
        $data['amount'], // balance starts equal to amount
        $data['journal_id'] ?? null,
        $data['created_by'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Link an AP invoice to its GL journal after the journal is created.
 */
function setAPJournalId(PDO $pdo, int $apId, int $journalId): void
{
    $stmt = $pdo->prepare("UPDATE accountspayable SET journal_id = ? WHERE ap_id = ?");
    $stmt->execute([$journalId, $apId]);
}

/**
 * Get a single AP invoice with supplier name joined in.
 */
function getAPById(PDO $pdo, int $apId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT ap.*, s.supplier_name, s.supplier_code 
         FROM accountspayable ap
         JOIN suppliers s ON s.supplier_id = ap.supplier_id
         WHERE ap.ap_id = ?"
    );
    $stmt->execute([$apId]);
    return $stmt->fetch();
}

/**
 * List all AP invoices with supplier name joined in, optionally filtered by status.
 */
function getAllAP(PDO $pdo, ?string $status = null): array
{
    $sql = "SELECT ap.*, s.supplier_name, s.supplier_code 
            FROM accountspayable ap
            JOIN suppliers s ON s.supplier_id = ap.supplier_id";

    if ($status) {
        $sql .= " WHERE ap.status = ?";
        $sql .= " ORDER BY ap.due_date ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql .= " ORDER BY ap.due_date ASC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * Apply a payment to an AP invoice — reduces balance, updates status.
 * Called by the Disbursement module when a payment is made.
 */
function applyAPPayment(PDO $pdo, int $apId, float $amountPaid): void
{
    $ap = getAPById($pdo, $apId);
    if (!$ap) {
        throw new Exception('AP invoice not found.');
    }

    $newAmountPaid = round($ap['amount_paid'] + $amountPaid, 2);
    $newBalance    = round($ap['amount'] - $newAmountPaid, 2);

    if ($newBalance < -0.01) {
        throw new Exception('Payment amount exceeds remaining balance.');
    }

    $newStatus = 'Partially Paid';
    if ($newBalance <= 0.01) {
        $newStatus = 'Paid';
        $newBalance = 0.00;
    }

    $stmt = $pdo->prepare(
        "UPDATE accountspayable SET amount_paid = ?, balance = ?, status = ? WHERE ap_id = ?"
    );
    $stmt->execute([$newAmountPaid, $newBalance, $newStatus, $apId]);
}