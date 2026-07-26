<?php
/**
 * Role Middleware
 * Include AFTER AuthMiddleware.php on pages restricted to specific roles.
 *
 * Usage:
 *   require_once __DIR__ . '/../middleware/AuthMiddleware.php';
 *   require_once __DIR__ . '/../middleware/RoleMiddleware.php';
 *   requireRole(['ROLE_ADMIN', 'ROLE_FINANCE']);
 */

require_once __DIR__ . '/../config/database.php'; // ensures BASE_URL is available

function requireRole(array $allowedRoles): void
{
    $userRole = $_SESSION['role'] ?? null;

    if (!$userRole || !in_array($userRole, $allowedRoles, true)) {
        http_response_code(403);

        $backUrl = BASE_URL . '/views/shared/dashboard/dashboard.php';

        die('403 Forbidden: You do not have permission to access this page. 
             <a href="' . $backUrl . '">Go back to your dashboard</a>');
    }
}

/**
 * Convenience helper to check role in view logic (e.g. show/hide a button)
 * without killing the page.
 */
function hasRole(string|array $roles): bool
{
    $userRole = $_SESSION['role'] ?? null;
    if (is_array($roles)) {
        return in_array($userRole, $roles, true);
    }
    return $userRole === $roles;
}