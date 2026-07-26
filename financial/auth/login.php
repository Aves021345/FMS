<?php
/**
 * Login Handler
 * Processes POST login requests. No UI here — connect this to your form later.
 * Expects POST: username, password
 */

session_start();
require_once __DIR__ . '/../models/User.php';

header('Content-Type: application/json'); // remove this line once you wire it to an actual HTML form

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

$user = attemptLogin($pdo, $username, $password);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

// ---- Success: set session ----
session_regenerate_id(true); // prevent session fixation

$_SESSION['user_id']    = $user['user_id'];
$_SESSION['username']   = $user['username'];
$_SESSION['full_name']  = $user['full_name'];
$_SESSION['role']       = $user['role'];
$_SESSION['last_activity'] = time();

updateLastLogin($pdo, $user['user_id']);

// ---- Role-based redirect target (frontend will use this) ----
echo json_encode([
    'success'  => true,
    'message'  => 'Login successful',
    'user'     => [
        'user_id'   => $user['user_id'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ],
    'redirect' => BASE_URL . '/views/shared/dashboard.php',
]);