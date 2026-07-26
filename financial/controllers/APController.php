<?php
/**
 * Accounts Payable Controller
 * JSON API endpoint. Accountant/Admin create AP invoices.
 * Creating an invoice automatically creates a matching DRAFT journal entry
 * (Debit: chosen expense/asset account, Credit: Accounts Payable control account).
 * Finance still has to approve/post that journal separately via JournalController.
 *
 * Expects POST with:
 *   action = 'create' | 'list' | 'view'
 *
 * create expects:
 *   supplier_id, invoice_no, invoice_date, due_date, amount,
 *   expense_account_id, ap_account_id, period_id, description
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/AccountsPayable.php';
require_once __DIR__ . '/../services/JournalService.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $required = ['supplier_id', 'invoice_no', 'invoice_date', 'due_date', 'amount', 'expense_account_id', 'ap_account_id', 'period_id'];
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
                // 1. Create the AP invoice record first (without journal_id yet)
                $apId = createAPInvoice($pdo, [
                    'supplier_id' => $_POST['supplier_id'],
                    'invoice_no'  => $_POST['invoice_no'],
                    'invoice_date'=> $_POST['invoice_date'],
                    'due_date'    => $_POST['due_date'],
                    'amount'      => $amount,
                    'created_by'  => $_SESSION['user_id'],
                ]);

                // 2. Find the AP source module id
                $modStmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = 'AP'");
                $modStmt->execute();
                $moduleId = $modStmt->fetchColumn();
                if (!$moduleId) {
                    throw new Exception("Source module 'AP' not found. Run the reference data seeder first.");
                }

                // 3. Create the matching draft journal: Debit expense/asset, Credit AP
                $journalHeader = [
                    'period_id'        => $_POST['period_id'],
                    'source_module_id' => $moduleId,
                    'reference_no'     => $_POST['invoice_no'],
                    'description'      => $_POST['description'] ?? ('AP Invoice ' . $_POST['invoice_no']),
                    'prepared_by'      => $_SESSION['user_id'],
                ];

                $journalLines = [
                    [
                        'account_id'  => $_POST['expense_account_id'],
                        'debit'       => $amount,
                        'credit'      => 0,
                        'description' => 'AP Invoice ' . $_POST['invoice_no'],
                    ],
                    [
                        'account_id'  => $_POST['ap_account_id'],
                        'debit'       => 0,
                        'credit'      => $amount,
                        'description' => 'AP Invoice ' . $_POST['invoice_no'],
                    ],
                ];

                $journalId = createJournalDraft($pdo, $journalHeader, $journalLines);

                // 4. Link the journal back to the AP invoice
                setAPJournalId($pdo, $apId, $journalId);

                $pdo->commit();

                logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'accountspayable', $apId, null, [
                    'invoice_no' => $_POST['invoice_no'],
                    'amount'     => $amount,
                    'journal_id' => $journalId,
                ]);

                echo json_encode([
                    'success'    => true,
                    'message'    => 'AP invoice created with draft journal entry.',
                    'ap_id'      => $apId,
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
            $invoices = getAllAP($pdo, $status);

            echo json_encode([
                'success'  => true,
                'invoices' => $invoices,
            ]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $apId = (int) ($_GET['ap_id'] ?? $_POST['ap_id'] ?? 0);
            if (!$apId) {
                throw new Exception('ap_id is required.');
            }

            $invoice = getAPById($pdo, $apId);
            if (!$invoice) {
                throw new Exception('AP invoice not found.');
            }

            echo json_encode([
                'success' => true,
                'invoice' => $invoice,
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}