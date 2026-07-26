<?php
/**
 * Financial Reports Model
 * Read-only queries against posted GL data and open AP/AR invoices.
 * No writes here — this module only summarizes what other modules posted.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Trial Balance for a given fiscal period.
 * For each account with posted activity, shows debit/credit totals and the
 * ending balance in the correct column based on the account's normal_balance.
 * Only accounts with non-zero activity are included.
 */
function getTrialBalance(PDO $pdo, int $periodId): array
{
    $stmt = $pdo->prepare(
        "SELECT 
            coa.account_id, coa.account_code, coa.account_name, coa.account_type, coa.normal_balance,
            COALESCE(SUM(gl.debit), 0) AS total_debit,
            COALESCE(SUM(gl.credit), 0) AS total_credit
         FROM chartofaccounts coa
         JOIN gl_journalline gl ON gl.account_id = coa.account_id
         JOIN gl_journalheader gh ON gh.journal_id = gl.journal_id
         WHERE gh.period_id = ? AND gh.status = 'Posted'
         GROUP BY coa.account_id, coa.account_code, coa.account_name, coa.account_type, coa.normal_balance
         HAVING total_debit != 0 OR total_credit != 0
         ORDER BY coa.account_code"
    );
    $stmt->execute([$periodId]);
    $rows = $stmt->fetchAll();

    $totalDebitBalance  = 0.0;
    $totalCreditBalance = 0.0;

    foreach ($rows as &$row) {
        $netDebit = round((float) $row['total_debit'] - (float) $row['total_credit'], 2);

        // A positive net means a debit balance, negative means a credit balance —
        // this is true regardless of the account's normal_balance, since debit/credit
        // are already correctly signed by however the entries were posted.
        if ($netDebit >= 0) {
            $row['debit_balance']  = $netDebit;
            $row['credit_balance'] = 0.00;
        } else {
            $row['debit_balance']  = 0.00;
            $row['credit_balance'] = -$netDebit;
        }

        $totalDebitBalance  += $row['debit_balance'];
        $totalCreditBalance += $row['credit_balance'];
    }
    unset($row);

    return [
        'accounts'      => $rows,
        'total_debit'   => round($totalDebitBalance, 2),
        'total_credit'  => round($totalCreditBalance, 2),
        'is_balanced'   => abs($totalDebitBalance - $totalCreditBalance) < 0.01,
    ];
}

/**
 * AP Aging report — buckets open/partially paid AP invoices by how overdue they are.
 */
function getAPAging(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT ap.ap_id, ap.invoice_no, ap.due_date, ap.balance, s.supplier_name,
                DATEDIFF(CURDATE(), ap.due_date) AS days_overdue
         FROM accountspayable ap
         JOIN suppliers s ON s.supplier_id = ap.supplier_id
         WHERE ap.status IN ('Open', 'Partially Paid')
         ORDER BY ap.due_date ASC"
    );
    $rows = $stmt->fetchAll();

    return bucketAgingRows($rows);
}

/**
 * AR Aging report — same idea, for open/partially collected AR invoices.
 */
function getARAging(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT ar.ar_id, ar.invoice_no, ar.due_date, ar.balance, c.customer_name,
                DATEDIFF(CURDATE(), ar.due_date) AS days_overdue
         FROM accountsreceivable ar
         JOIN customers c ON c.customer_id = ar.customer_id
         WHERE ar.status IN ('Open', 'Partially Collected')
         ORDER BY ar.due_date ASC"
    );
    $rows = $stmt->fetchAll();

    return bucketAgingRows($rows);
}

/**
 * Shared bucketing logic: Current, 1-30, 31-60, 61-90, 90+ days overdue.
 */
function bucketAgingRows(array $rows): array
{
    $buckets = [
        'current' => [], // not yet overdue
        '1_30'    => [],
        '31_60'   => [],
        '61_90'   => [],
        'over_90' => [],
    ];

    foreach ($rows as $row) {
        $days = (int) $row['days_overdue'];

        if ($days <= 0)       $buckets['current'][] = $row;
        elseif ($days <= 30)  $buckets['1_30'][] = $row;
        elseif ($days <= 60)  $buckets['31_60'][] = $row;
        elseif ($days <= 90)  $buckets['61_90'][] = $row;
        else                  $buckets['over_90'][] = $row;
    }

    $totals = [];
    foreach ($buckets as $key => $items) {
        $totals[$key] = round(array_sum(array_column($items, 'balance')), 2);
    }

    return ['buckets' => $buckets, 'totals' => $totals];
}