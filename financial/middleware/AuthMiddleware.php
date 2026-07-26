<?php
/**
 * Auth Middleware
 * Include this at the TOP of any page that requires a logged-in user.
 *
 * Usage:
 *   require_once __DIR__ . '/../middleware/AuthMiddleware.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

define('SESSION_TIMEOUT', 1800); // 30 minutes

function requireAuth(): void
{
    // Not logged in at all
    if (!isset($_SESSION['user_id'])) {
        redirectToLogin('Please log in to continue.');
    }

    // Session expired due to inactivity
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        redirectToLogin('Session expired. Please log in again.');
    }

    // Still active — refresh timestamp
    $_SESSION['last_activity'] = time();
}

function redirectToLogin(string $message = ''): never
{
    if ($message !== '') {
        session_start();
        $_SESSION['flash_error'] = $message;
    }
    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}

// Run the check immediately when this file is included
requireAuth();