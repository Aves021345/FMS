<?php
/**
 * Budget Management Controller
 * Finance/Admin only (per your role structure — budgeting is a Finance function).
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $required = ['account_id', 'period_id', 'budget_amount'];
            foreach ($required as $field) {
                if (empty($_POST[$field]) && $_POST[$field] !== '0') {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $budgetAmount = (float) $_POST['budget_amount'];
            if ($budgetAmount <= 0) {
                throw new Exception('Budget amount must be greater than zero.');
            }

            $budgetId = createBudget($pdo, [
                'account_id'    => $_POST['account_id'],
                'period_id'     => $_POST['period_id'],
                'department'    => $_POST['department'] ?? null,
                'budget_amount' => $budgetAmount,
                'created_by'    => $_SESSION['user_id'],
            ]);

            logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'budgetmanagement', $budgetId, null, [
                'account_id'    => $_POST['account_id'],
                'period_id'     => $_POST['period_id'],
                'budget_amount' => $budgetAmount,
            ]);

            echo json_encode([
                'success'   => true,
                'message'   => 'Budget line created.',
                'budget_id' => $budgetId,
            ]);
            break;

        case 'refresh':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $periodId = (int) ($_POST['period_id'] ?? 0);
            if (!$periodId) {
                throw new Exception('period_id is required.');
            }

            $count = recalculateAllActualsForPeriod($pdo, $periodId);

            echo json_encode([
                'success' => true,
                'message' => "Refreshed actuals for {$count} budget line(s).",
            ]);
            break;

        case 'list':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']);

            $periodId = (int) ($_GET['period_id'] ?? $_POST['period_id'] ?? 0) ?: null;
            echo json_encode(['success' => true, 'budgets' => getAllBudgets($pdo, $periodId)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}