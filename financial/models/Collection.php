<?php
/**
 * Collection Model
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a collection request (status: Pending). Accountant/Cashier records this
 * when a customer payment comes in, before it's allocated to specific invoices.
 */
function createCollection(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO collectionmanagement 
            (customer_id, collection_date, amount, payment_method, reference_no, received_by, status)
         VALUES (?, ?, ?, ?, ?, ?, 'Pending')"
    );
    $stmt->execute([
        $data['customer_id'],
        $data['collection_date'],
        $data['amount'],
        $data['payment_method'],
        $data['reference_no'] ?? null,
        $data['received_by'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Record how much of a collection was applied to a specific AR invoice.
 */
function addCollectionApplication(PDO $pdo, int $collectionId, int $arId, float $amountApplied): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO collectionapplication (collection_id, ar_id, amount_applied) VALUES (?, ?, ?)"
    );
    $stmt->execute([$collectionId, $arId, $amountApplied]);
}

/**
 * Mark a collection as Applied after journal posting + AR allocation is done.
 */
function markCollectionApplied(PDO $pdo, int $collectionId, int $journalId): void
{
    $stmt = $pdo->prepare(
        "UPDATE collectionmanagement SET journal_id = ?, status = 'Applied' WHERE collection_id = ?"
    );
    $stmt->execute([$journalId, $collectionId]);
}

/**
 * Get a single collection with customer name joined in.
 */
function getCollectionById(PDO $pdo, int $collectionId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT col.*, c.customer_name 
         FROM collectionmanagement col
         JOIN customers c ON c.customer_id = col.customer_id
         WHERE col.collection_id = ?"
    );
    $stmt->execute([$collectionId]);
    return $stmt->fetch();
}

/**
 * List all collections, optionally filtered by status.
 */
function getAllCollections(PDO $pdo, ?string $status = null): array
{
    $sql = "SELECT col.*, c.customer_name 
            FROM collectionmanagement col
            JOIN customers c ON c.customer_id = col.customer_id";

    if ($status) {
        $sql .= " WHERE col.status = ? ORDER BY col.collection_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql .= " ORDER BY col.collection_date DESC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * Increase a bank account's current_balance after a collection is received.
 */
function increaseBankBalance(PDO $pdo, int $bankAccountId, float $amount): void
{
    $stmt = $pdo->prepare("SELECT current_balance FROM bankaccounts WHERE bank_account_id = ?");
    $stmt->execute([$bankAccountId]);
    $currentBalance = $stmt->fetchColumn();

    if ($currentBalance === false) {
        throw new Exception('Bank account not found.');
    }

    $newBalance = round($currentBalance + $amount, 2);

    $update = $pdo->prepare("UPDATE bankaccounts SET current_balance = ? WHERE bank_account_id = ?");
    $update->execute([$newBalance, $bankAccountId]);
}