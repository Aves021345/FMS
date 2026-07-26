<?php
/**
 * Tax Management Model
 * Tracks tax computed on transactions and their filing/payment status.
 * Note: this table has no journal_id — tax amounts are already embedded
 * within the AP/AR/Cash journal entries that generated them. This module
 * is a tracking/filing record, not a separate GL-posting transaction.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a new tax record. tax_amount is calculated from the tax type's rate.
 */
function createTaxRecord(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare("SELECT tax_rate FROM taxtypes WHERE tax_type_id = ?");
    $stmt->execute([$data['tax_type_id']]);
    $rate = $stmt->fetchColumn();

    if ($rate === false) {
        throw new Exception('Tax type not found.');
    }

    $taxableAmount = (float) $data['taxable_amount'];
    $taxAmount = round($taxableAmount * ((float) $rate / 100), 2);

    $insert = $pdo->prepare(
        "INSERT INTO taxmanagement (tax_type_id, transaction_ref, tax_period, taxable_amount, tax_amount, status)
         VALUES (?, ?, ?, ?, ?, 'Pending')"
    );
    $insert->execute([
        $data['tax_type_id'],
        $data['transaction_ref'] ?? null,
        $data['tax_period'],
        $taxableAmount,
        $taxAmount,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Mark a tax record as Filed.
 */
function fileTaxRecord(PDO $pdo, int $taxId, int $filedBy): void
{
    $tax = getTaxById($pdo, $taxId);
    if (!$tax) {
        throw new Exception('Tax record not found.');
    }
    if ($tax['status'] !== 'Pending') {
        throw new Exception("Only Pending tax records can be filed. Current status: {$tax['status']}.");
    }

    $stmt = $pdo->prepare(
        "UPDATE taxmanagement SET status = 'Filed', filed_by = ?, filed_date = CURDATE() WHERE tax_id = ?"
    );
    $stmt->execute([$filedBy, $taxId]);
}

/**
 * Mark a Filed tax record as Paid.
 */
function markTaxPaid(PDO $pdo, int $taxId): void
{
    $tax = getTaxById($pdo, $taxId);
    if (!$tax) {
        throw new Exception('Tax record not found.');
    }
    if ($tax['status'] !== 'Filed') {
        throw new Exception("Only Filed tax records can be marked Paid. Current status: {$tax['status']}.");
    }

    $stmt = $pdo->prepare("UPDATE taxmanagement SET status = 'Paid' WHERE tax_id = ?");
    $stmt->execute([$taxId]);
}

/**
 * Get a single tax record with tax type name joined in.
 */
function getTaxById(PDO $pdo, int $taxId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT tm.*, tt.tax_code, tt.tax_name, tt.tax_rate
         FROM taxmanagement tm
         JOIN taxtypes tt ON tt.tax_type_id = tm.tax_type_id
         WHERE tm.tax_id = ?"
    );
    $stmt->execute([$taxId]);
    return $stmt->fetch();
}

/**
 * List all tax records, optionally filtered by status.
 */
function getAllTaxRecords(PDO $pdo, ?string $status = null): array
{
    $sql = "SELECT tm.*, tt.tax_code, tt.tax_name, tt.tax_rate
            FROM taxmanagement tm
            JOIN taxtypes tt ON tt.tax_type_id = tm.tax_type_id";

    if ($status) {
        $sql .= " WHERE tm.status = ? ORDER BY tm.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql .= " ORDER BY tm.created_at DESC";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}