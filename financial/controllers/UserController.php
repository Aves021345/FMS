<?php
/**
 * User Management Controller
 * Admin only. Handles creating, editing, activating/deactivating,
 * and password resets for system users.
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/AuditLogger.php';

requireRole(['ROLE_ADMIN']);

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            $required = ['username', 'password', 'full_name', 'email', 'role'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            $validRoles = ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR'];
            if (!in_array($_POST['role'], $validRoles, true)) {
                throw new Exception('Invalid role.');
            }

            if (strlen($_POST['password']) < 8) {
                throw new Exception('Password must be at least 8 characters.');
            }

            if (isUsernameOrEmailTaken($pdo, $_POST['username'], $_POST['email'])) {
                throw new Exception('Username or email is already in use.');
            }

            $newUserId = createUser($pdo, [
                'username'  => $_POST['username'],
                'password'  => $_POST['password'],
                'full_name' => $_POST['full_name'],
                'email'     => $_POST['email'],
                'role'      => $_POST['role'],
                'status'    => 'Active',
            ]);

            logAudit($pdo, $_SESSION['user_id'], 'CREATE', 'users', (int) $newUserId, null, [
                'username' => $_POST['username'],
                'role'     => $_POST['role'],
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'User created successfully.',
                'user_id' => $newUserId,
            ]);
            break;

        case 'update_profile':
            $userId = (int) ($_POST['user_id'] ?? 0);
            $required = ['full_name', 'email', 'role'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '{$field}' is required.");
                }
            }

            if (!$userId) {
                throw new Exception('user_id is required.');
            }

            $validRoles = ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR'];
            if (!in_array($_POST['role'], $validRoles, true)) {
                throw new Exception('Invalid role.');
            }

            $existingUser = getUserById($pdo, $userId);
            if (!$existingUser) {
                throw new Exception('User not found.');
            }

            if (isUsernameOrEmailTakenByOther($pdo, $existingUser['username'], $_POST['email'], $userId)) {
                throw new Exception('Email is already in use by another user.');
            }

            // Prevent an admin from demoting their own last-admin account by accident
            if ($userId === (int) $_SESSION['user_id'] && $_POST['role'] !== 'ROLE_ADMIN') {
                $adminCountStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'ROLE_ADMIN' AND status = 'Active'");
                if ((int) $adminCountStmt->fetchColumn() <= 1) {
                    throw new Exception('You cannot change your own role away from Admin — you are the only active Admin account.');
                }
            }

            updateUserProfile($pdo, $userId, [
                'full_name' => $_POST['full_name'],
                'email'     => $_POST['email'],
                'role'      => $_POST['role'],
            ]);

            logAudit($pdo, $_SESSION['user_id'], 'UPDATE', 'users', $userId,
                ['full_name' => $existingUser['full_name'], 'email' => $existingUser['email'], 'role' => $existingUser['role']],
                ['full_name' => $_POST['full_name'], 'email' => $_POST['email'], 'role' => $_POST['role']]
            );

            echo json_encode(['success' => true, 'message' => 'User profile updated.']);
            break;

        case 'update_status':
            $userId = (int) ($_POST['user_id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if (!$userId || !$status) {
                throw new Exception('user_id and status are required.');
            }

            $existingUser = getUserById($pdo, $userId);
            if (!$existingUser) {
                throw new Exception('User not found.');
            }

            if ($userId === (int) $_SESSION['user_id'] && $status !== 'Active') {
                throw new Exception('You cannot deactivate or lock your own account.');
            }

            updateUserStatus($pdo, $userId, $status);

            logAudit($pdo, $_SESSION['user_id'], 'UPDATE_STATUS', 'users', $userId,
                ['status' => $existingUser['status']],
                ['status' => $status]
            );

            echo json_encode(['success' => true, 'message' => "User status changed to {$status}."]);
            break;

        case 'reset_password':
            $userId = (int) ($_POST['user_id'] ?? 0);
            $newPassword = $_POST['new_password'] ?? '';

            if (!$userId || !$newPassword) {
                throw new Exception('user_id and new_password are required.');
            }
            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters.');
            }

            $existingUser = getUserById($pdo, $userId);
            if (!$existingUser) {
                throw new Exception('User not found.');
            }

            resetUserPassword($pdo, $userId, $newPassword);

            logAudit($pdo, $_SESSION['user_id'], 'RESET_PASSWORD', 'users', $userId, null, null);

            echo json_encode(['success' => true, 'message' => 'Password reset successfully.']);
            break;

        case 'list':
            echo json_encode(['success' => true, 'users' => getAllUsers($pdo)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}