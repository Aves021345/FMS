<?php
/**
 * User Model
 * Handles DB operations for the `users` table.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Find a single active user by username.
 */
function getUserByUsername(PDO $pdo, string $username): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

/**
 * Find a single user by ID.
 */
function getUserById(PDO $pdo, int $userId): array|false
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Attempt to authenticate a user.
 * Returns the user row on success, or false on failure.
 */
function attemptLogin(PDO $pdo, string $username, string $password): array|false
{
    $user = getUserByUsername($pdo, $username);

    if (!$user) {
        return false; // no such user
    }

    if ($user['status'] !== 'Active') {
        return false; // Inactive or Locked account
    }

    if (!password_verify($password, $user['password'])) {
        return false; // wrong password
    }

    return $user;
}

/**
 * Update last_login timestamp.
 */
function updateLastLogin(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
    $stmt->execute([$userId]);
}

/**
 * Check if a username or email is already taken (used when creating a new user).
 */
function isUsernameOrEmailTaken(PDO $pdo, string $username, string $email): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    return (int) $stmt->fetchColumn() > 0;
}

/**
 * Create a new user. Password is hashed here.
 */
function createUser(PDO $pdo, array $data): string
{
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, full_name, email, role, status)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $data['username'],
        password_hash($data['password'], PASSWORD_DEFAULT),
        $data['full_name'],
        $data['email'],
        $data['role'] ?? 'ROLE_ACCOUNTANT',
        $data['status'] ?? 'Active',
    ]);
    return $pdo->lastInsertId();
}

/**
 * Get all users (for admin management screens later).
 */
function getAllUsers(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT user_id, username, full_name, email, role, status, last_login FROM users ORDER BY full_name");
    return $stmt->fetchAll();
}

/**
 * Update a user's profile info (full_name, email, role). Does NOT touch password.
 */
function updateUserProfile(PDO $pdo, int $userId, array $data): void
{
    $stmt = $pdo->prepare(
        "UPDATE users SET full_name = ?, email = ?, role = ? WHERE user_id = ?"
    );
    $stmt->execute([$data['full_name'], $data['email'], $data['role'], $userId]);
}

/**
 * Change a user's status (Active / Inactive / Locked).
 */
function updateUserStatus(PDO $pdo, int $userId, string $status): void
{
    $validStatuses = ['Active', 'Inactive', 'Locked'];
    if (!in_array($status, $validStatuses, true)) {
        throw new Exception('Invalid status.');
    }

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $stmt->execute([$status, $userId]);
}

/**
 * Reset a user's password (Admin action — sets a new password directly).
 */
function resetUserPassword(PDO $pdo, int $userId, string $newPassword): void
{
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
}

/**
 * Check if a username or email is already taken by a DIFFERENT user
 * (used when editing, to allow saving without tripping on the user's own value).
 */
function isUsernameOrEmailTakenByOther(PDO $pdo, string $username, string $email, int $excludeUserId): bool
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND user_id != ?"
    );
    $stmt->execute([$username, $email, $excludeUserId]);
    return (int) $stmt->fetchColumn() > 0;
}