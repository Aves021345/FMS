<?php
/**
 * Shared Dashboard Entry Point
 * All roles land here after login. This file picks the correct
 * role-specific partial (_admin.php, _finance.php, _accountant.php, _auditor.php)
 * based on the logged-in user's role.
 */

require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';

// Any logged-in user with a valid role can reach this file —
// the role check just determines WHICH partial loads below.
requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

// Map role -> partial file + page title
$roleMap = [
    'ROLE_ADMIN'      => ['file' => '_admin.php',      'title' => 'Admin Dashboard'],
    'ROLE_FINANCE'    => ['file' => '_finance.php',     'title' => 'Finance Dashboard'],
    'ROLE_ACCOUNTANT' => ['file' => '_accountant.php',  'title' => 'Accountant Dashboard'],
    'ROLE_AUDITOR'    => ['file' => '_auditor.php',     'title' => 'Auditor Dashboard'],
];

$userRole = $_SESSION['role'] ?? null;

if (!isset($roleMap[$userRole])) {
    http_response_code(403);
    die('403 Forbidden: Unrecognized role.');
}

$pageTitle   = $roleMap[$userRole]['title'];
$partialFile = __DIR__ . '/' . $roleMap[$userRole]['file'];

ob_start();
if (file_exists($partialFile)) {
    require $partialFile;
} else {
    echo '<p>Dashboard content not found for this role.</p>';
}
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';