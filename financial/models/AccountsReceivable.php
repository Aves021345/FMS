<?php
/**
 * Accounts Receivable Model
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a new AR invoice record (status: Open, balance = full amount).
 */
function createARInvoice(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO accountsreceivable 
            (customer_id, invoice_no, invoice_date, due_date, amount, amount_collected, balance, status, journal_id, created_by)
         VALUES (?, ?, ?, ?, ?, 0, ?, 'Open', ?, ?)"
    );
    $stmt->execute([
        $data['customer_id'],
        $data['invoice_no'],
        $data['invoice_date'],
        $data['due_date'],
        $data['amount'],
        $data['amount'],
        $data['journal_id'] ?? null,
        $data['created_by'],
    ]);
    return (int) $pdo->lastInsertId();
}

/**
 * Link an AR invoice to its GL journal after the journal is created.
 */
function setARJournalId(PDO $pdo, int $arId, int $journalId): void
{
    $stmt = $pdo->prepare("UPDATE accountsreceivable SET journal_id = ? WHERE ar_id = ?");
    $stmt->execute([$journalId, $arId]);
}

/**
 * Get a single AR invoice with customer name joined in.
 */
function getARById(PDO $pdo, int $arId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT ar.*, c.customer_name, c.customer_code 
         FROM accountsreceivable ar
         JOIN customers c ON c.customer_id = ar.customer_id
         WHERE ar.ar_id = ?"
    );
    $stmt->execute([$arId]);
    return $stmt->fetch();
}

/**
 * List all AR invoices with customer name joined in, optionally filtered by status.
 */
function getAllAR(PDO $pdo, ?string $status = null): array
{
    $sql = "SELECT ar.*, c.customer_name, c.customer_code 
            FROM accountsreceivable ar
            JOIN customers c ON c.customer_id = ar.customer_id";

    if ($status) {
        $sql .= " WHERE ar.status = ? ORDER BY ar.due_date ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql .= " ORDER BY ar.due_date ASC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}

/**
 * List all open/partially collected AR invoices for a specific customer
 * (used when allocating a collection payment).
 */
function getOpenARByCustomer(PDO $pdo, int $customerId): array
{
    $stmt = $pdo->prepare(
        "SELECT ar_id, invoice_no, balance FROM accountsreceivable 
         WHERE customer_id = ? AND status IN ('Open', 'Partially Collected')
         ORDER BY due_date ASC"
    );
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}

/**
 * Apply a collection/payment to an AR invoice — reduces balance, updates status.
 */
function applyARCollection(PDO $pdo, int $arId, float $amountCollected): void
{
    $ar = getARById($pdo, $arId);
    if (!$ar) {
        throw new Exception('AR invoice not found.');
    }

    $newAmountCollected = round($ar['amount_collected'] + $amountCollected, 2);
    $newBalance         = round($ar['amount'] - $newAmountCollected, 2);

    if ($newBalance < -0.01) {
        throw new Exception('Collection amount exceeds the remaining AR balance.');
    }

    $newStatus = 'Partially Collected';
    if ($newBalance <= 0.01) {
        $newStatus = 'Collected';
        $newBalance = 0.00;
    }

    $stmt = $pdo->prepare(
        "UPDATE accountsreceivable SET amount_collected = ?, balance = ?, status = ? WHERE ar_id = ?"
    );
    $stmt->execute([$newAmountCollected, $newBalance, $newStatus, $arId]);
}