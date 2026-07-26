<?php
/**
 * Journal Service
 * The core GL engine. Every AP, AR, Cash, Disbursement, and Collection
 * transaction eventually calls createJournalDraft() here, and Finance
 * calls postJournal() to approve it into the ledger.
 *
 * Workflow: Draft (Accountant creates) -> Posted (Finance approves) -> Voided (optional, Finance/Admin only)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

/**
 * Generate a unique journal number, e.g. JV-202607-0001
 */
function generateJournalNo(PDO $pdo): string
{
    $prefix = 'JV-' . date('Ym') . '-';

    $stmt = $pdo->prepare(
        "SELECT journal_no FROM gl_journalheader 
         WHERE journal_no LIKE ? 
         ORDER BY journal_id DESC LIMIT 1"
    );
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetchColumn();

    if ($last) {
        $lastSeq = (int) substr($last, -4);
        $nextSeq = $lastSeq + 1;
    } else {
        $nextSeq = 1;
    }

    return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
}

/**
 * Create a DRAFT journal entry with its lines.
 *
 * @param array $header  ['period_id', 'source_module_id', 'reference_no', 'description', 'prepared_by']
 * @param array $lines   list of ['account_id', 'debit', 'credit', 'description']
 * @return int  the new journal_id
 * @throws Exception if debits != credits, or lines are empty
 */
function createJournalDraft(PDO $pdo, array $header, array $lines): int
{
    if (empty($lines)) {
        throw new Exception('A journal entry must have at least one line.');
    }

    $totalDebit  = 0.0;
    $totalCredit = 0.0;

    foreach ($lines as $line) {
        $totalDebit  += (float) ($line['debit'] ?? 0);
        $totalCredit += (float) ($line['credit'] ?? 0);
    }

    // Round to avoid float precision issues (e.g. 100.000000001)
    $totalDebit  = round($totalDebit, 2);
    $totalCredit = round($totalCredit, 2);

    if ($totalDebit <= 0 || $totalCredit <= 0) {
        throw new Exception('Journal entry must have non-zero debit and credit amounts.');
    }

    if (abs($totalDebit - $totalCredit) > 0.001) {
        throw new Exception("Journal entry is unbalanced: total debit ({$totalDebit}) does not equal total credit ({$totalCredit}).");
    }

    $pdo->beginTransaction();

    try {
        $journalNo = generateJournalNo($pdo);

        $stmt = $pdo->prepare(
            "INSERT INTO gl_journalheader 
                (journal_no, journal_date, period_id, source_module_id, reference_no, description, total_debit, total_credit, status, prepared_by)
             VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?, 'Draft', ?)"
        );
        $stmt->execute([
            $journalNo,
            $header['period_id'],
            $header['source_module_id'],
            $header['reference_no'] ?? null,
            $header['description'] ?? null,
            $totalDebit,
            $totalCredit,
            $header['prepared_by'],
        ]);

        $journalId = (int) $pdo->lastInsertId();

        $lineStmt = $pdo->prepare(
            "INSERT INTO gl_journalline (journal_id, line_no, account_id, debit, credit, description)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $lineNo = 1;
        foreach ($lines as $line) {
            $lineStmt->execute([
                $journalId,
                $lineNo,
                $line['account_id'],
                $line['debit']  ?? 0,
                $line['credit'] ?? 0,
                $line['description'] ?? null,
            ]);
            $lineNo++;
        }

        $pdo->commit();

        logAudit($pdo, $header['prepared_by'], 'CREATE', 'gl_journalheader', $journalId, null, [
            'journal_no' => $journalNo,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'status' => 'Draft',
        ]);

        return $journalId;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Approve and POST a draft journal entry. Only Finance/Admin should call this
 * (enforce that check in the controller via requireRole()).
 *
 * Re-validates debit = credit against the actual line data before posting,
 * in case lines were edited after the draft was created.
 */
function postJournal(PDO $pdo, int $journalId, int $approvedBy): void
{
    $header = getJournalById($pdo, $journalId);

    if (!$header) {
        throw new Exception('Journal entry not found.');
    }

    if ($header['status'] !== 'Draft') {
        throw new Exception("Only Draft journals can be posted. Current status: {$header['status']}.");
    }

    $lines = getJournalLines($pdo, $journalId);

    $totalDebit  = round(array_sum(array_column($lines, 'debit')), 2);
    $totalCredit = round(array_sum(array_column($lines, 'credit')), 2);

    if (abs($totalDebit - $totalCredit) > 0.001 || $totalDebit <= 0) {
        throw new Exception("Cannot post: journal is unbalanced (debit {$totalDebit} vs credit {$totalCredit}).");
    }

    $stmt = $pdo->prepare(
        "UPDATE gl_journalheader 
         SET status = 'Posted', approved_by = ?, posted_at = NOW(), total_debit = ?, total_credit = ?
         WHERE journal_id = ?"
    );
    $stmt->execute([$approvedBy, $totalDebit, $totalCredit, $journalId]);

    logAudit($pdo, $approvedBy, 'POST', 'gl_journalheader', $journalId, ['status' => 'Draft'], ['status' => 'Posted']);
}

/**
 * Void a journal entry. Only allowed while still Draft, OR if Posted,
 * only Admin/Finance should call this (enforce in controller) — voiding a
 * posted entry does NOT delete it, it just marks status for audit purposes.
 * A proper reversing entry should be created separately if it was already Posted.
 */
function voidJournal(PDO $pdo, int $journalId, int $voidedBy): void
{
    $header = getJournalById($pdo, $journalId);

    if (!$header) {
        throw new Exception('Journal entry not found.');
    }

    if ($header['status'] === 'Voided') {
        throw new Exception('Journal entry is already voided.');
    }

    $stmt = $pdo->prepare("UPDATE gl_journalheader SET status = 'Voided' WHERE journal_id = ?");
    $stmt->execute([$journalId]);

    logAudit($pdo, $voidedBy, 'VOID', 'gl_journalheader', $journalId, ['status' => $header['status']], ['status' => 'Voided']);
}

/**
 * Fetch a single journal header.
 */
function getJournalById(PDO $pdo, int $journalId): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM gl_journalheader WHERE journal_id = ?");
    $stmt->execute([$journalId]);
    return $stmt->fetch();
}

/**
 * Fetch all lines for a journal, ordered by line_no.
 */
function getJournalLines(PDO $pdo, int $journalId): array
{
    $stmt = $pdo->prepare("SELECT * FROM gl_journalline WHERE journal_id = ? ORDER BY line_no");
    $stmt->execute([$journalId]);
    return $stmt->fetchAll();
}

/**
 * List journals, optionally filtered by status ('Draft', 'Posted', 'Voided').
 */
function getAllJournals(PDO $pdo, ?string $status = null): array
{
    if ($status) {
        $stmt = $pdo->prepare("SELECT * FROM gl_journalheader WHERE status = ? ORDER BY journal_date DESC, journal_id DESC");
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query("SELECT * FROM gl_journalheader ORDER BY journal_date DESC, journal_id DESC");
    }
    return $stmt->fetchAll();
}