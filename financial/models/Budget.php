<?php
/**
 * Budget Management Model
 * budget_amount is set manually. actual_amount is calculated FROM posted GL
 * journal lines for that account + period — never entered by hand, so it
 * always reflects what actually happened in the ledger.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Create a new budget line for an account + period.
 */
function createBudget(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO budgetmanagement (account_id, period_id, department, budget_amount, actual_amount, created_by)
         VALUES (?, ?, ?, ?, 0, ?)"
    );
    $stmt->execute([
        $data['account_id'],
        $data['period_id'],
        $data['department'] ?? null,
        $data['budget_amount'],
        $data['created_by'],
    ]);

    $budgetId = (int) $pdo->lastInsertId();

    // Immediately calculate actuals in case postings already exist for this account/period
    recalculateBudgetActual($pdo, $budgetId);

    return $budgetId;
}

/**
 * Recalculate actual_amount for a single budget line, based on POSTED journal
 * lines only (Draft/Voided entries don't count). Respects the account's
 * normal_balance so Assets/Expenses and Liabilities/Equity/Revenue both
 * come out as a sensible positive "actual spent/earned" figure.
 */
function recalculateBudgetActual(PDO $pdo, int $budgetId): void
{
    $stmt = $pdo->prepare(
        "SELECT b.account_id, b.period_id, coa.normal_balance
         FROM budgetmanagement b
         JOIN chartofaccounts coa ON coa.account_id = b.account_id
         WHERE b.budget_id = ?"
    );
    $stmt->execute([$budgetId]);
    $budget = $stmt->fetch();

    if (!$budget) {
        throw new Exception('Budget line not found.');
    }

    $sumStmt = $pdo->prepare(
        "SELECT COALESCE(SUM(gl.debit), 0) AS total_debit, COALESCE(SUM(gl.credit), 0) AS total_credit
         FROM gl_journalline gl
         JOIN gl_journalheader gh ON gh.journal_id = gl.journal_id
         WHERE gl.account_id = ? AND gh.period_id = ? AND gh.status = 'Posted'"
    );
    $sumStmt->execute([$budget['account_id'], $budget['period_id']]);
    $sums = $sumStmt->fetch();

    $actual = ($budget['normal_balance'] === 'Debit')
        ? ((float) $sums['total_debit'] - (float) $sums['total_credit'])
        : ((float) $sums['total_credit'] - (float) $sums['total_debit']);

    $update = $pdo->prepare("UPDATE budgetmanagement SET actual_amount = ? WHERE budget_id = ?");
    $update->execute([round($actual, 2), $budgetId]);
}

/**
 * Recalculate actuals for every budget line in a given period.
 * Useful as a "Refresh" action after new journals get posted.
 */
function recalculateAllActualsForPeriod(PDO $pdo, int $periodId): int
{
    $stmt = $pdo->prepare("SELECT budget_id FROM budgetmanagement WHERE period_id = ?");
    $stmt->execute([$periodId]);
    $budgetIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($budgetIds as $id) {
        recalculateBudgetActual($pdo, (int) $id);
    }

    return count($budgetIds);
}

/**
 * Get a single budget line with account name and period name joined in.
 */
function getBudgetById(PDO $pdo, int $budgetId): array|false
{
    $stmt = $pdo->prepare(
        "SELECT b.*, coa.account_code, coa.account_name, fp.period_name
         FROM budgetmanagement b
         JOIN chartofaccounts coa ON coa.account_id = b.account_id
         JOIN fiscalperiods fp ON fp.period_id = b.period_id
         WHERE b.budget_id = ?"
    );
    $stmt->execute([$budgetId]);
    return $stmt->fetch();
}

/**
 * List all budgets, optionally filtered by period.
 */
function getAllBudgets(PDO $pdo, ?int $periodId = null): array
{
    $sql = "SELECT b.*, coa.account_code, coa.account_name, fp.period_name
            FROM budgetmanagement b
            JOIN chartofaccounts coa ON coa.account_id = b.account_id
            JOIN fiscalperiods fp ON fp.period_id = b.period_id";

    if ($periodId) {
        $sql .= " WHERE b.period_id = ? ORDER BY coa.account_code";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$periodId]);
    } else {
        $sql .= " ORDER BY fp.start_date DESC, coa.account_code";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}