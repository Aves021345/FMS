<?php
/**
 * Cash Management Controller
 * Finance/Admin only. Unlike AP/AR, there's no separate draft/approve step here —
 * Finance enters the transaction directly and it posts to the GL immediately.
 *
 * create expects:
 *   bank_account_id, transaction_date, transaction_type ('Deposit'|'Withdrawal'|'Transfer In'|'Transfer Out'),
 *   amount, description, reference_no, period_id, bank_gl_account_id, contra_account_id
 *
 * For Deposit/Transfer In:  Debit bank_gl_account_id, Credit contra_account_id
 * For Withdrawal/Transfer Out: Debit contra_account_id, Credit bank_gl_account_id
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/CashManagement.php';
require_once __DIR__ . '/../services/JournalService.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $required = ['bank_account_id', 'transaction_date', 'transaction_type', 'amount', 'period_id', 'bank_gl_account_id', 'contra_account_id'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $validTypes = ['Deposit', 'Withdrawal', 'Transfer In', 'Transfer Out'];
            if (!in_array($_POST['transaction_type'], $validTypes, true)) {
                throw new Exception('Invalid transaction_type.');
            }

            $amount = (float) $_POST['amount'];
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero.');
            }

            $isIncoming = in_array($_POST['transaction_type'], ['Deposit', 'Transfer In'], true);

            $pdo->beginTransaction();

            try {
                $modStmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = 'CASH'");
                $modStmt->execute();
                $moduleId = $modStmt->fetchColumn();
                if (!$moduleId) {
                    throw new Exception("Source module 'CASH' not found. Run the reference data seeder first.");
                }

                $journalHeader = [
                    'period_id'        => $_POST['period_id'],
                    'source_module_id' => $moduleId,
                    'reference_no'     => $_POST['reference_no'] ?? null,
                    'description'      => $_POST['description'] ?? ($_POST['transaction_type'] . ' transaction'),
                    'prepared_by'      => $_SESSION['user_id'],
                ];

                // Build lines based on direction of money flow
                if ($isIncoming) {
                    $journalLines = [
                        ['account_id' => $_POST['bank_gl_account_id'], 'debit' => $amount, 'credit' => 0, 'description' => $_POST['transaction_type']],
                        ['account_id' => $_POST['contra_account_id'],  'debit' => 0, 'credit' => $amount, 'description' => $_POST['transaction_type']],
                    ];
                } else {
                    $journalLines = [
                        ['account_id' => $_POST['contra_account_id'],  'debit' => $amount, 'credit' => 0, 'description' => $_POST['transaction_type']],
                        ['account_id' => $_POST['bank_gl_account_id'], 'debit' => 0, 'credit' => $amount, 'description' => $_POST['transaction_type']],
                    ];
                }

                $journalId = createJournalDraft($pdo, $journalHeader, $journalLines);
                postJournal($pdo, $journalId, $_SESSION['user_id']); // Finance entering directly = auto-post

                // Update the bank's cash balance
                adjustBankBalanceForCashTransaction($pdo, $_POST['bank_account_id'], $_POST['transaction_type'], $amount);

                // Record the cash transaction, linked to the posted journal
                $cashId = createCashTransaction($pdo, [
                    'bank_account_id'  => $_POST['bank_account_id'],
                    'transaction_date' => $_POST['transaction_date'],
                    'transaction_type' => $_POST['transaction_type'],
                    'amount'           => $amount,
                    'description'      => $_POST['description'] ?? null,
                    'reference_no'     => $_POST['reference_no'] ?? null,
                    'journal_id'       => $journalId,
                    'created_by'       => $_SESSION['user_id'],
                ]);

                $pdo->commit();

                logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'cashmanagement', $cashId, null, [
                    'transaction_type' => $_POST['transaction_type'],
                    'amount'           => $amount,
                    'journal_id'       => $journalId,
                ]);

                echo json_encode([
                    'success'    => true,
                    'message'    => 'Cash transaction recorded and posted.',
                    'cash_id'    => $cashId,
                    'journal_id' => $journalId,
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'list':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']);

            $type = $_GET['type'] ?? $_POST['type'] ?? null;
            echo json_encode(['success' => true, 'transactions' => getAllCashTransactions($pdo, $type)]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']);

            $cashId = (int) ($_GET['cash_id'] ?? $_POST['cash_id'] ?? 0);
            if (!$cashId) {
                throw new Exception('cash_id is required.');
            }

            $tx = getCashTransactionById($pdo, $cashId);
            if (!$tx) {
                throw new Exception('Cash transaction not found.');
            }

            echo json_encode(['success' => true, 'transaction' => $tx]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}