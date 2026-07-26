<?php
/**
 * Disbursement Controller
 * JSON API endpoint.
 *
 * create   - Accountant/Admin requests a payment against an AP invoice (status: Pending)
 * approve  - Finance/Admin approves it. This single action:
 *              1. Creates a GL journal (Debit AP, Credit Bank/Cash) and posts it immediately
 *              2. Reduces the AP invoice's balance (applyAPPayment)
 *              3. Reduces the bank account's current_balance
 *              4. Marks the disbursement as Released
 *            All wrapped in one transaction — if any step fails, nothing changes.
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Disbursement.php';
require_once __DIR__ . '/../models/AccountsPayable.php';
require_once __DIR__ . '/../services/JournalService.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $required = ['ap_id', 'bank_account_id', 'amount', 'disbursement_date', 'payment_method'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $amount = (float) $_POST['amount'];
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero.');
            }

            $ap = getAPById($pdo, (int) $_POST['ap_id']);
            if (!$ap) {
                throw new Exception('AP invoice not found.');
            }
            if ($amount > (float) $ap['balance'] + 0.01) {
                throw new Exception('Payment amount exceeds the AP invoice balance (' . $ap['balance'] . ').');
            }

            $disbursementId = createDisbursement($pdo, [
                'supplier_id'       => $ap['supplier_id'],
                'ap_id'             => $ap['ap_id'],
                'bank_account_id'   => $_POST['bank_account_id'],
                'disbursement_date' => $_POST['disbursement_date'],
                'amount'            => $amount,
                'payment_method'    => $_POST['payment_method'],
                'reference_no'      => $_POST['reference_no'] ?? null,
            ]);

            echo json_encode([
                'success'         => true,
                'message'         => 'Disbursement request created (Pending approval).',
                'disbursement_id' => $disbursementId,
            ]);
            break;

        case 'approve':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $disbursementId = (int) ($_POST['disbursement_id'] ?? 0);
            $apAccountId     = $_POST['ap_account_id'] ?? null;
            $bankGlAccountId = $_POST['bank_gl_account_id'] ?? null;
            $periodId        = $_POST['period_id'] ?? null;

            if (!$disbursementId || !$apAccountId || !$bankGlAccountId || !$periodId) {
                throw new Exception('disbursement_id, ap_account_id, bank_gl_account_id, and period_id are all required.');
            }

            $disb = getDisbursementById($pdo, $disbursementId);
            if (!$disb) {
                throw new Exception('Disbursement not found.');
            }
            if ($disb['status'] !== 'Pending') {
                throw new Exception("Only Pending disbursements can be approved. Current status: {$disb['status']}.");
            }

            $amount = (float) $disb['amount'];

            $pdo->beginTransaction();

            try {
                // 1. Create and immediately post the GL journal
                $journalHeader = [
                    'period_id'        => $periodId,
                    'source_module_id' => (function () use ($pdo) {
                        $stmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = 'DISB'");
                        $stmt->execute();
                        $id = $stmt->fetchColumn();
                        if (!$id) throw new Exception("Source module 'DISB' not found. Run the reference data seeder first.");
                        return $id;
                    })(),
                    'reference_no'     => $disb['reference_no'] ?? ('DISB-' . $disbursementId),
                    'description'      => 'Payment for AP Invoice ' . $disb['invoice_no'],
                    'prepared_by'      => $_SESSION['user_id'],
                ];

                $journalLines = [
                    [
                        'account_id'  => $apAccountId,
                        'debit'       => $amount,
                        'credit'      => 0,
                        'description' => 'Payment - ' . $disb['invoice_no'],
                    ],
                    [
                        'account_id'  => $bankGlAccountId,
                        'debit'       => 0,
                        'credit'      => $amount,
                        'description' => 'Payment - ' . $disb['invoice_no'],
                    ],
                ];

                $journalId = createJournalDraft($pdo, $journalHeader, $journalLines);
                postJournal($pdo, $journalId, $_SESSION['user_id']); // Finance approving = auto-post

                // 2. Reduce the AP invoice balance
                applyAPPayment($pdo, $disb['ap_id'], $amount);

                // 3. Reduce the bank account balance
                reduceBankBalance($pdo, $disb['bank_account_id'], $amount);

                // 4. Mark disbursement as Released
                releaseDisbursement($pdo, $disbursementId, $journalId, $_SESSION['user_id']);

                $pdo->commit();

                logAudit($pdo, $_SESSION['user_id'], 'APPROVE', 'disbursementmanagement', $disbursementId, 
                    ['status' => 'Pending'], 
                    ['status' => 'Released', 'journal_id' => $journalId, 'amount' => $amount]
                );

                echo json_encode([
                    'success'    => true,
                    'message'    => 'Disbursement approved and released. Journal posted.',
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
            $disbursements = getAllDisbursements($pdo, $status);

            echo json_encode([
                'success'       => true,
                'disbursements' => $disbursements,
            ]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $disbursementId = (int) ($_GET['disbursement_id'] ?? $_POST['disbursement_id'] ?? 0);
            if (!$disbursementId) {
                throw new Exception('disbursement_id is required.');
            }

            $disb = getDisbursementById($pdo, $disbursementId);
            if (!$disb) {
                throw new Exception('Disbursement not found.');
            }

            echo json_encode(['success' => true, 'disbursement' => $disb]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}