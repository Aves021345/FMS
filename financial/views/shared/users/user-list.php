<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN']);

$pageTitle = 'User Management';

ob_start();
?>

<div class="p-6 space-y-6">

    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-group'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">User Management</h1>
            </div>
            <a href="<?= BASE_URL ?>/views/shared/users/create.php"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                <i class='bx bx-plus'></i>New User
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Users</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-group'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalUsers">0</div>
                <div class="text-xs text-textSoft" id="statAdminCount">0 admins</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Active Accounts</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-check-circle'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statActiveUsers">0</div>
                <div class="text-xs text-textSoft" id="statActivePct">0% of total</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Inactive / Locked</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-lock-alt'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statFlaggedUsers">0</div>
                <div class="text-xs text-textSoft" id="statLockedCount">0 locked</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="statusTabs">
                <button type="button" data-status="" class="status-tab px-4 py-2 rounded-full bg-navy900 text-white">All</button>
                <button type="button" data-status="Active" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Active</button>
                <button type="button" data-status="Inactive" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Inactive</button>
                <button type="button" data-status="Locked" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Locked</button>
            </div>
            <div class="relative">
                <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                <input type="text" id="searchInput" placeholder="Search username, name, or email..."
                       class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20 w-72">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold whitespace-nowrap">Username</th>
                        <th class="px-2 py-3 font-semibold">Full Name</th>
                        <th class="px-2 py-3 font-semibold">Email</th>
                        <th class="px-2 py-3 font-semibold">Role</th>
                        <th class="px-2 py-3 font-semibold">Status</th>
                        <th class="px-2 py-3 font-semibold whitespace-nowrap">Last Login</th>
                        <th class="px-5 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-textSoft">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const currentUserId = <?= (int) $_SESSION['user_id'] ?>;
let currentUsers = [];
let activeStatus = '';

const ROLE_LABELS = {
    ROLE_ADMIN: 'Admin',
    ROLE_FINANCE: 'Finance',
    ROLE_ACCOUNTANT: 'Accountant',
    ROLE_AUDITOR: 'Auditor',
};

const STATUS_BADGE = {
    Active:   'bg-emerald-100 text-emerald-600',
    Inactive: 'bg-amber-100 text-amber-600',
    Locked:   'bg-rose-100 text-rose-600',
};

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function updateStats(users) {
    const total = users.length;
    const admins = users.filter(u => u.role === 'ROLE_ADMIN').length;
    const active = users.filter(u => u.status === 'Active').length;
    const locked = users.filter(u => u.status === 'Locked').length;
    const flagged = users.filter(u => u.status !== 'Active').length;
    const activePct = total ? Math.round((active / total) * 100) : 0;

    setText('statTotalUsers', total);
    setText('statAdminCount', admins + ' admin' + (admins === 1 ? '' : 's'));
    setText('statActiveUsers', active);
    setText('statActivePct', activePct + '% of total');
    setText('statFlaggedUsers', flagged);
    setText('statLockedCount', locked + ' locked');
}

function renderRows(users) {
    const tbody = document.getElementById('userTableBody');

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">No users found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    users.forEach(u => {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';

        let statusActions = '';
        if (u.user_id == currentUserId) {
            statusActions = '<span class="text-textSoft italic">(you)</span>';
        } else if (u.status === 'Active') {
            statusActions = `
                <button onclick="changeStatus(${u.user_id}, 'Inactive')" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-textMid" title="Deactivate"><i class='bx bx-pause-circle'></i></button>
                <button onclick="changeStatus(${u.user_id}, 'Locked')" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-rose-600" title="Lock"><i class='bx bx-lock-alt'></i></button>`;
        } else {
            statusActions = `<button onclick="changeStatus(${u.user_id}, 'Active')" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-emerald-600" title="Reactivate"><i class='bx bx-play-circle'></i></button>`;
        }

        const badgeClass = STATUS_BADGE[u.status] || 'bg-[#eef1f6] text-textMid';
        const roleLabel = ROLE_LABELS[u.role] || u.role;

        tr.innerHTML = `
            <td class="px-5 py-3 font-semibold text-textDark whitespace-nowrap">${escapeHtml(u.username)}</td>
            <td class="px-2 py-3 text-textDark">${escapeHtml(u.full_name)}</td>
            <td class="px-2 py-3 text-textMid">${escapeHtml(u.email ?? '')}</td>
            <td class="px-2 py-3"><span class="inline-block text-[11px] px-2.5 py-1 rounded-full bg-[#eef1f6] text-textMid font-semibold">${escapeHtml(roleLabel)}</span></td>
            <td class="px-2 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${escapeHtml(u.status)}</span></td>
            <td class="px-2 py-3 text-textMid whitespace-nowrap">${escapeHtml(u.last_login ?? 'Never')}</td>
            <td class="px-5 py-3 text-right whitespace-nowrap">
                <a href="${BASE_URL}/views/shared/users/edit.php?user_id=${u.user_id}" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-orange" title="Edit"><i class='bx bx-edit'></i></a>
                ${statusActions}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function applyFilters() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();

    let filtered = currentUsers;
    if (activeStatus) {
        filtered = filtered.filter(u => u.status === activeStatus);
    }
    if (term) {
        filtered = filtered.filter(u =>
            (u.username || '').toLowerCase().includes(term) ||
            (u.full_name || '').toLowerCase().includes(term) ||
            (u.email || '').toLowerCase().includes(term)
        );
    }
    renderRows(filtered);
}

async function loadUsers() {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');

    // Step 1: network call + JSON parse. Only failures here are real "couldn't load" errors.
    let data;
    try {
        const res = await fetch(BASE_URL + '/controllers/UserController.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const rawText = await res.text();

        if (!res.ok) {
            throw new Error(`Server returned ${res.status} ${res.statusText}. Body: ${rawText.slice(0, 300)}`);
        }

        try {
            data = JSON.parse(rawText);
        } catch (parseErr) {
            throw new Error(`Response wasn't valid JSON. First 300 chars of raw response: ${rawText.slice(0, 300)}`);
        }

    } catch (err) {
        console.error('User list fetch/parse failed:', err);
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">
            Something went wrong loading users.<br>
            <span class="text-xs text-textSoft break-all">${escapeHtml(err.message)}</span>
        </td></tr>`;
        return;
    }

    if (!data.success) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Error: ${escapeHtml(data.message)}</td></tr>`;
        return;
    }

    currentUsers = data.users;

    // Step 2: rendering. Kept separate so a stat/DOM bug never masquerades as a network error.
    try {
        updateStats(currentUsers);
        applyFilters();
    } catch (err) {
        console.error('User list render failed:', err);
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Data loaded, but the page failed to render it. Check the console for details.</td></tr>';
    }
}

async function changeStatus(userId, status) {
    if (!confirm(`Change this user's status to ${status}?`)) return;

    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('user_id', userId);
    formData.append('status', status);

    const res = await fetch(BASE_URL + '/controllers/UserController.php', { method: 'POST', body: formData });
    const data = await res.json();

    alert(data.message);
    if (data.success) loadUsers();
}

// ── Status tabs ──
document.querySelectorAll('.status-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        activeStatus = this.dataset.status;
        document.querySelectorAll('.status-tab').forEach(b => {
            const isActive = b === this;
            b.classList.toggle('bg-navy900', isActive);
            b.classList.toggle('text-white', isActive);
            b.classList.toggle('bg-[#eef1f5]', !isActive);
            b.classList.toggle('text-textMid', !isActive);
        });
        applyFilters();
    });
});

document.getElementById('searchInput').addEventListener('input', applyFilters);

loadUsers();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';