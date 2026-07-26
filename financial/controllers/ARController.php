<?php
/**
 * Accounts Receivable Controller
 * Mirrors APController. Creating an AR invoice automatically creates a
 * matching DRAFT journal entry (Debit: Accounts Receivable, Credit: Revenue account).
 * Finance approves/posts that journal separately via JournalController.
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/AccountsReceivable.php';
require_once __DIR__ . '/../services/JournalService.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $required = ['customer_id', 'invoice_no', 'invoice_date', 'due_date', 'amount', 'ar_account_id', 'revenue_account_id', 'period_id'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $amount = (float) $_POST['amount'];
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero.');
            }

            $pdo->beginTransaction();

            try {
                $arId = createARInvoice($pdo, [
                    'customer_id'  => $_POST['customer_id'],
                    'invoice_no'   => $_POST['invoice_no'],
                    'invoice_date' => $_POST['invoice_date'],
                    'due_date'     => $_POST['due_date'],
                    'amount'       => $amount,
                    'created_by'   => $_SESSION['user_id'],
                ]);

                $modStmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = 'AR'");
                $modStmt->execute();
                $moduleId = $modStmt->fetchColumn();
                if (!$moduleId) {
                    throw new Exception("Source module 'AR' not found. Run the reference data seeder first.");
                }

                $journalHeader = [
                    'period_id'        => $_POST['period_id'],
                    'source_module_id' => $moduleId,
                    'reference_no'     => $_POST['invoice_no'],
                    'description'      => $_POST['description'] ?? ('AR Invoice ' . $_POST['invoice_no']),
                    'prepared_by'      => $_SESSION['user_id'],
                ];

                $journalLines = [
                    [
                        'account_id'  => $_POST['ar_account_id'],
                        'debit'       => $amount,
                        'credit'      => 0,
                        'description' => 'AR Invoice ' . $_POST['invoice_no'],
                    ],
                    [
                        'account_id'  => $_POST['revenue_account_id'],
                        'debit'       => 0,
                        'credit'      => $amount,
                        'description' => 'AR Invoice ' . $_POST['invoice_no'],
                    ],
                ];

                $journalId = createJournalDraft($pdo, $journalHeader, $journalLines);
                setARJournalId($pdo, $arId, $journalId);

                $pdo->commit();

                logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'accountsreceivable', $arId, null, [
                    'invoice_no' => $_POST['invoice_no'],
                    'amount'     => $amount,
                    'journal_id' => $journalId,
                ]);

                echo json_encode([
                    'success'    => true,
                    'message'    => 'AR invoice created with draft journal entry.',
                    'ar_id'      => $arId,
                    'journal_id' => $journalId,
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
            break;

        case 'list':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $status = $_GET['status'] ?? $_POST['status'] ?? null;
            $invoices = getAllAR($pdo, $status);

            echo json_encode(['success' => true, 'invoices' => $invoices]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $arId = (int) ($_GET['ar_id'] ?? $_POST['ar_id'] ?? 0);
            if (!$arId) {
                throw new Exception('ar_id is required.');
            }

            $invoice = getARById($pdo, $arId);
            if (!$invoice) {
                throw new Exception('AR invoice not found.');
            }

            echo json_encode(['success' => true, 'invoice' => $invoice]);
            break;

        case 'open_by_customer':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']);

            $customerId = (int) ($_GET['customer_id'] ?? $_POST['customer_id'] ?? 0);
            if (!$customerId) {
                throw new Exception('customer_id is required.');
            }

            echo json_encode(['success' => true, 'invoices' => getOpenARByCustomer($pdo, $customerId)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}