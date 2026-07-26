<?php
/**
 * All-Roles Seeder
 * Creates one test account for each role: Admin, Finance, Accountant, Auditor.
 * Run this ONCE: http://localhost/financial/database/seeders/all_roles_seeder.php
 *
 * DELETE this file after running it — leaving a user-creation script
 * publicly accessible is a security risk.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

// ---- Accounts to create ----
$seedUsers = [
    [
        'username'  => 'admin',
        'password'  => 'Admin@123',
        'full_name' => 'System Administrator',
        'email'     => 'admin@traveltours.local',
        'role'      => 'ROLE_ADMIN',
        'status'    => 'Active',
    ],
    [
        'username'  => 'finance',
        'password'  => 'Finance@123',
        'full_name' => 'Finance Manager',
        'email'     => 'finance@traveltours.local',
        'role'      => 'ROLE_FINANCE',
        'status'    => 'Active',
    ],
    [
        'username'  => 'accountant',
        'password'  => 'Accountant@123',
        'full_name' => 'Staff Accountant',
        'email'     => 'accountant@traveltours.local',
        'role'      => 'ROLE_ACCOUNTANT',
        'status'    => 'Active',
    ],
    [
        'username'  => 'auditor',
        'password'  => 'Auditor@123',
        'full_name' => 'Internal Auditor',
        'email'     => 'auditor@traveltours.local',
        'role'      => 'ROLE_AUDITOR',
        'status'    => 'Active',
    ],
];

echo "<h2>Seeding user accounts...</h2><ul>";

foreach ($seedUsers as $seedUser) {
    $existing = getUserByUsername($pdo, $seedUser['username']);

    if ($existing) {
        echo "<li>⚠️ '{$seedUser['username']}' already exists (user_id: {$existing['user_id']}) — skipped.</li>";
        continue;
    }

    $newUserId = createUser($pdo, $seedUser);

    echo "<li>✅ Created <strong>{$seedUser['role']}</strong> — 
          username: <code>{$seedUser['username']}</code>, 
          password: <code>{$seedUser['password']}</code> 
          (user_id: {$newUserId})</li>";
}

echo "</ul>";
echo "<p>Done. You can now log in with any of the accounts above.</p>";
echo "<p><strong>Remember to delete this seeder file when done.</strong></p>";