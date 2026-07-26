<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN']);

$pageTitle = 'New User';

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/users/user-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to User List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <form id="userForm" class="space-y-6 max-w-2xl">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-user-plus'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New User</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Username</label>
                    <input type="text" name="username" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <p class="text-xs text-textSoft mt-1">Minimum 8 characters</p>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Full Name</label>
                    <input type="text" name="full_name" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Email</label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Role</label>
                    <select name="role" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <option value="ROLE_ADMIN">Admin</option>
                        <option value="ROLE_FINANCE">Finance</option>
                        <option value="ROLE_ACCOUNTANT">Accountant</option>
                        <option value="ROLE_AUDITOR">Auditor</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-check'></i>Create User
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

document.getElementById('userForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create');

    try {
        const res = await fetch(BASE_URL + '/controllers/UserController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            alert(data.message);
            window.location.href = BASE_URL + '/views/shared/users/user-list.php';
        } else {
            errorBox.textContent = data.message;
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        errorBox.textContent = 'Something went wrong. Please try again.';
        errorBox.classList.remove('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';