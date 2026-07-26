<?php
/**
 * Reports Controller
 * Read-only. All roles can view reports (Admin/Finance/Accountant/Auditor).
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Report.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'trial_balance':
            $periodId = (int) ($_GET['period_id'] ?? $_POST['period_id'] ?? 0);
            if (!$periodId) {
                throw new Exception('period_id is required.');
            }
            echo json_encode(['success' => true, 'data' => getTrialBalance($pdo, $periodId)]);
            break;

        case 'ap_aging':
            echo json_encode(['success' => true, 'data' => getAPAging($pdo)]);
            break;

        case 'ar_aging':
            echo json_encode(['success' => true, 'data' => getARAging($pdo)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}