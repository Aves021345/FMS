<?php
/**
 * Entry Point / Login Page
 * If already logged in -> redirect straight to role dashboard.
 * If not -> show the login form (minimal, function over form for now).
 */

session_start();
require_once __DIR__ . '/../config/database.php';

// ---- Already logged in? Send to dashboard ----
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/views/shared/dashboard/dashboard.php');
    exit;
}

$errorMessage = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Travel & Tours FMS</title>
</head>
<body>

    <h2>Login</h2>

    <div id="errorBox" style="color:red;"><?= htmlspecialchars($errorMessage) ?></div>

    <form id="loginForm">
        <label>Username</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Log In</button>
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const errorBox = document.getElementById('errorBox');
            errorBox.textContent = '';

            const formData = new FormData();
            formData.append('username', document.getElementById('username').value);
            formData.append('password', document.getElementById('password').value);

            try {
                const response = await fetch('<?= BASE_URL ?>/auth/login.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorBox.textContent = result.message;
                }
            } catch (err) {
                errorBox.textContent = 'Something went wrong. Please try again.';
            }
        });
    </script>

</body>
</html>