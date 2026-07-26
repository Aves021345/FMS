<?php
/**
 * Tax Management Controller
 * create - Accountant/Admin records tax computed on a transaction (Pending)
 * file   - Finance/Admin marks it as officially Filed with the BIR/tax authority
 * mark_paid - Finance/Admin marks a Filed record as Paid
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Tax.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $required = ['tax_type_id', 'tax_period', 'taxable_amount'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            if ((float) $_POST['taxable_amount'] <= 0) {
                throw new Exception('Taxable amount must be greater than zero.');
            }

            $taxId = createTaxRecord($pdo, [
                'tax_type_id'     => $_POST['tax_type_id'],
                'transaction_ref' => $_POST['transaction_ref'] ?? null,
                'tax_period'      => $_POST['tax_period'],
                'taxable_amount'  => $_POST['taxable_amount'],
            ]);

            logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'taxmanagement', $taxId, null, [
                'tax_type_id'    => $_POST['tax_type_id'],
                'taxable_amount' => $_POST['taxable_amount'],
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Tax record created.',
                'tax_id'  => $taxId,
            ]);
            break;

        case 'file':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $taxId = (int) ($_POST['tax_id'] ?? 0);
            if (!$taxId) {
                throw new Exception('tax_id is required.');
            }

            fileTaxRecord($pdo, $taxId, $_SESSION['user_id']);

            logAudit($pdo, $_SESSION['user_id'], 'FILE', 'taxmanagement', $taxId, ['status' => 'Pending'], ['status' => 'Filed']);

            echo json_encode(['success' => true, 'message' => "Tax record #{$taxId} marked as Filed."]);
            break;

        case 'mark_paid':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $taxId = (int) ($_POST['tax_id'] ?? 0);
            if (!$taxId) {
                throw new Exception('tax_id is required.');
            }

            markTaxPaid($pdo, $taxId);

            logAudit($pdo, $_SESSION['user_id'], 'MARK_PAID', 'taxmanagement', $taxId, ['status' => 'Filed'], ['status' => 'Paid']);

            echo json_encode(['success' => true, 'message' => "Tax record #{$taxId} marked as Paid."]);
            break;

        case 'list':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $status = $_GET['status'] ?? $_POST['status'] ?? null;
            echo json_encode(['success' => true, 'records' => getAllTaxRecords($pdo, $status)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}