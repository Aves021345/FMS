<?php
/**
 * Collection Controller
 * JSON API endpoint.
 *
 * create - Accountant/Admin records a customer payment received (status: Pending)
 * apply  - Finance/Admin allocates the payment across one or more AR invoices. This:
 *            1. Validates allocations sum to the collection amount
 *            2. Creates a GL journal (Debit Bank/Cash, Credit AR) and posts it immediately
 *            3. Records each allocation in collectionapplication + reduces each AR balance
 *            4. Increases the bank account's current_balance
 *            5. Marks the collection as Applied
 *          All wrapped in one transaction.
 *
 * apply expects:
 *   collection_id, period_id, ar_account_id, bank_gl_account_id, bank_account_id,
 *   allocations[] = array of { ar_id, amount }
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Collection.php';
require_once __DIR__ . '/../models/AccountsReceivable.php';
require_once __DIR__ . '/../services/JournalService.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $required = ['customer_id', 'amount', 'collection_date', 'payment_method'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $amount = (float) $_POST['amount'];
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero.');
            }

            $collectionId = createCollection($pdo, [
                'customer_id'     => $_POST['customer_id'],
                'collection_date' => $_POST['collection_date'],
                'amount'          => $amount,
                'payment_method'  => $_POST['payment_method'],
                'reference_no'    => $_POST['reference_no'] ?? null,
                'received_by'     => $_SESSION['user_id'],
            ]);

            echo json_encode([
                'success'       => true,
                'message'       => 'Collection recorded (Pending allocation).',
                'collection_id' => $collectionId,
            ]);
            break;

        case 'apply':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $collectionId    = (int) ($_POST['collection_id'] ?? 0);
            $periodId        = $_POST['period_id'] ?? null;
            $arAccountId     = $_POST['ar_account_id'] ?? null;
            $bankGlAccountId = $_POST['bank_gl_account_id'] ?? null;
            $bankAccountId   = $_POST['bank_account_id'] ?? null;
            $allocations     = $_POST['allocations'] ?? [];

            if (!$collectionId || !$periodId || !$arAccountId || !$bankGlAccountId || !$bankAccountId) {
                throw new Exception('collection_id, period_id, ar_account_id, bank_gl_account_id, and bank_account_id are all required.');
            }
            if (!is_array($allocations) || empty($allocations)) {
                throw new Exception('At least one AR allocation is required.');
            }

            $collection = getCollectionById($pdo, $collectionId);
            if (!$collection) {
                throw new Exception('Collection not found.');
            }
            if ($collection['status'] !== 'Pending') {
                throw new Exception("Only Pending collections can be applied. Current status: {$collection['status']}.");
            }

            $totalAllocated = 0.0;
            foreach ($allocations as $alloc) {
                $totalAllocated += (float) ($alloc['amount'] ?? 0);
            }
            $totalAllocated = round($totalAllocated, 2);
            $collectionAmount = round((float) $collection['amount'], 2);

            if (abs($totalAllocated - $collectionAmount) > 0.01) {
                throw new Exception("Allocations ({$totalAllocated}) must equal the collection amount ({$collectionAmount}).");
            }

            $pdo->beginTransaction();

            try {
                // 1. Create and post the GL journal for the full collection amount
                $modStmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = 'COLL'");
                $modStmt->execute();
                $moduleId = $modStmt->fetchColumn();
                if (!$moduleId) {
                    throw new Exception("Source module 'COLL' not found. Run the reference data seeder first.");
                }

                $journalHeader = [
                    'period_id'        => $periodId,
                    'source_module_id' => $moduleId,
                    'reference_no'     => $collection['reference_no'] ?? ('COLL-' . $collectionId),
                    'description'      => 'Collection from ' . $collection['customer_name'],
                    'prepared_by'      => $_SESSION['user_id'],
                ];

                $journalLines = [
                    [
                        'account_id'  => $bankGlAccountId,
                        'debit'       => $collectionAmount,
                        'credit'      => 0,
                        'description' => 'Collection - ' . $collection['customer_name'],
                    ],
                    [
                        'account_id'  => $arAccountId,
                        'debit'       => 0,
                        'credit'      => $collectionAmount,
                        'description' => 'Collection - ' . $collection['customer_name'],
                    ],
                ];

                $journalId = createJournalDraft($pdo, $journalHeader, $journalLines);
                postJournal($pdo, $journalId, $_SESSION['user_id']);

                // 2. Apply each allocation to its AR invoice
                foreach ($allocations as $alloc) {
                    $arId = (int) ($alloc['ar_id'] ?? 0);
                    $amt  = (float) ($alloc['amount'] ?? 0);

                    if (!$arId || $amt <= 0) {
                        continue; // skip blank rows
                    }

                    addCollectionApplication($pdo, $collectionId, $arId, $amt);
                    applyARCollection($pdo, $arId, $amt);
                }

                // 3. Increase the bank account balance
                increaseBankBalance($pdo, $bankAccountId, $collectionAmount);

                // 4. Mark collection as Applied
                markCollectionApplied($pdo, $collectionId, $journalId);

                $pdo->commit();

                logAudit($pdo, $_SESSION['user_id'], 'APPLY', 'collectionmanagement', $collectionId,
                    ['status' => 'Pending'],
                    ['status' => 'Applied', 'journal_id' => $journalId, 'allocations' => $allocations]
                );

                echo json_encode([
                    'success'    => true,
                    'message'    => 'Collection applied and journal posted.',
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
            echo json_encode(['success' => true, 'collections' => getAllCollections($pdo, $status)]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $collectionId = (int) ($_GET['collection_id'] ?? $_POST['collection_id'] ?? 0);
            if (!$collectionId) {
                throw new Exception('collection_id is required.');
            }

            $collection = getCollectionById($pdo, $collectionId);
            if (!$collection) {
                throw new Exception('Collection not found.');
            }

            echo json_encode(['success' => true, 'collection' => $collection]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}