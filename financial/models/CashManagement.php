<?php
/**
 * Cash Management Model
 * Handles standalone bank transactions: deposits, withdrawals, transfers.
 * Unlike AP/AR, this is Finance's direct domain — no separate draft/approve
 * step, since Finance is the one entering these directly.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a cash transaction record, already linked to its posted journal.
 */
function createCashTransaction(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO cashmanagement 
            (bank_account_id, transaction_date, transaction_type, amount, description, reference_no, journal_id, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $data['bank_account_id'],
        $data['transaction_date'],
        $data['transaction_type'],
        $data['amount'],
        $data['description'] ?? null,
        $data['reference_no'] ?? null,
        $data['journal_id'],
        $data['created_by'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Adjust a bank account's balance based on the transaction type.
 * Deposit / Transfer In  -> increase
 * Withdrawal / Transfer Out -> decrease
 */
function adjustBankBalanceForCashTransaction(PDO $pdo, int $bankAccountId, string $transactionType, float $amount): void
{
    $stmt = $pdo->prepare("SELECT current_balance FROM bankaccounts WHERE bank_account_id = ?");
    $stmt->execute([$bankAccountId]);
    $currentBalance = $stmt->fetchColumn();

    if ($currentBalance === false) {
        throw new Exception('Bank account not found.');
    }

    $increasing = in_array($transactionType, ['Deposit', 'Transfer In'], true);
    $newBalance = $increasing
        ? round($currentBalance + $amount, 2)
        : round($currentBalance - $amount, 2);

    if (!$increasing && $newBalance < 0) {
        throw new Exception('This transaction would overdraw the bank account balance.');
    }

    $update = $pdo->prepare("UPDATE bankaccounts SET current_balance = ? WHERE bank_account_id = ?");
    $update->execute([$newBalance, $bankAccountId]);
}

/**
 * Get a single cash transaction with bank account name joined in.
 */
function getCashTransactionById(PDO $pdo, int $cashId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT cm.*, ba.account_name AS bank_account_name 
         FROM cashmanagement cm
         JOIN bankaccounts ba ON ba.bank_account_id = cm.bank_account_id
         WHERE cm.cash_id = ?"
    );
    $stmt->execute([$cashId]);
    return $stmt->fetch();
}

/**
 * List all cash transactions, optionally filtered by transaction_type.
 */
function getAllCashTransactions(PDO $pdo, ?string $type = null): array
{
    $sql = "SELECT cm.*, ba.account_name AS bank_account_name 
            FROM cashmanagement cm
            JOIN bankaccounts ba ON ba.bank_account_id = cm.bank_account_id";

    if ($type) {
        $sql .= " WHERE cm.transaction_type = ? ORDER BY cm.transaction_date DESC, cm.cash_id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$type]);
    } else {
        $sql .= " ORDER BY cm.transaction_date DESC, cm.cash_id DESC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}