<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../../models/User.php';

requireRole(['ROLE_ADMIN']);

$pageTitle = 'Edit User';

$userId = (int) ($_GET['user_id'] ?? 0);
$user = $userId ? getUserById($pdo, $userId) : null;

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/users/user-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to User List
    </a>

    <?php if (!$user): ?>

        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            User not found.
        </div>

    <?php else: ?>

        <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>
        <div id="successBox" class="hidden bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm font-medium rounded-xl px-4 py-3"></div>

        <div class="max-w-2xl space-y-6">

            <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                        <i class='bx bx-user-pin'></i>
                    </div>
                    <h1 class="text-xl font-bold text-textDark">Edit User</h1>
                </div>

                <form id="profileForm" class="space-y-5">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Username</label>
                            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
                                   class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textSoft bg-[#f5f7fa]">
                            <p class="text-xs text-textSoft mt-1">Usernames can't be changed</p>
                        </div>

                        <div>
                            <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Role</label>
                            <select name="role" required
                                    class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                                <option value="ROLE_ADMIN"      <?= $user['role'] === 'ROLE_ADMIN' ? 'selected' : '' ?>>Admin</option>
                                <option value="ROLE_FINANCE"    <?= $user['role'] === 'ROLE_FINANCE' ? 'selected' : '' ?>>Finance</option>
                                <option value="ROLE_ACCOUNTANT" <?= $user['role'] === 'ROLE_ACCOUNTANT' ? 'selected' : '' ?>>Accountant</option>
                                <option value="ROLE_AUDITOR"    <?= $user['role'] === 'ROLE_AUDITOR' ? 'selected' : '' ?>>Auditor</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                                   class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        </div>

                        <div>
                            <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required
                                   class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                            <i class='bx bx-save'></i>Save Profile
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5 space-y-5">
                <div class="flex items-center gap-2">
                    <i class='bx bx-lock-alt text-orange text-lg'></i>
                    <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">Reset Password</h2>
                </div>

                <form id="passwordForm" class="space-y-5">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                    <div>
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">New Password</label>
                        <input type="password" name="new_password" required minlength="8"
                               class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <p class="text-xs text-textSoft mt-1">Minimum 8 characters</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#eef1f5] text-textDark text-sm font-bold hover:bg-[#e4e8ee] transition-colors">
                            <i class='bx bx-refresh text-orange'></i>Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const BASE_URL = '<?= BASE_URL ?>';
            const errorBox = document.getElementById('errorBox');
            const successBox = document.getElementById('successBox');

            function showMessage(box, text) {
                errorBox.classList.add('hidden');
                successBox.classList.add('hidden');
                box.textContent = text;
                box.classList.remove('hidden');
            }

            document.getElementById('profileForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'update_profile');

                const res = await fetch(BASE_URL + '/controllers/UserController.php', { method: 'POST', body: formData });
                const data = await res.json();

                showMessage(data.success ? successBox : errorBox, data.message);
            });

            document.getElementById('passwordForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!confirm('Reset this user\'s password?')) return;

                const formData = new FormData(this);
                formData.append('action', 'reset_password');

                const res = await fetch(BASE_URL + '/controllers/UserController.php', { method: 'POST', body: formData });
                const data = await res.json();

                showMessage(data.success ? successBox : errorBox, data.message);
                if (data.success) this.reset();
            });
        </script>

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';