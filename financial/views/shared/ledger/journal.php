<?php
/**
 * General Ledger - Journal List
 * All roles can view. Accountant/Admin can create drafts.
 * Finance/Admin can post or void drafts.
 */

require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'General Ledger - Journal Entries';

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-book-content'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">General Ledger</h1>
            </div>
            <?php if (hasRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN'])): ?>
                <div class="flex items-center gap-3">
                    <a href="<?= BASE_URL ?>/views/shared/ledger/create.php"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                        <i class='bx bx-plus'></i>New Journal Entry
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="statusTabs">
                <button type="button" data-status="" class="status-tab px-4 py-2 rounded-full bg-navy900 text-white">All Entries</button>
                <button type="button" data-status="Draft" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Draft</button>
                <button type="button" data-status="Posted" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Posted</button>
                <button type="button" data-status="Voided" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Voided</button>
            </div>
            <div class="flex items-center gap-2">
                <select id="statusFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Statuses</option>
                    <option value="Draft">Draft</option>
                    <option value="Posted">Posted</option>
                    <option value="Voided">Voided</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0" id="journalTable">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Journal No.</th>
                        <th class="px-2 py-3 font-semibold">Date</th>
                        <th class="px-2 py-3 font-semibold">Description</th>
                        <th class="px-2 py-3 font-semibold text-right">Total Debit</th>
                        <th class="px-2 py-3 font-semibold text-right">Total Credit</th>
                        <th class="px-2 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="journalTableBody">
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
const userCanPost = <?= hasRole(['ROLE_FINANCE', 'ROLE_ADMIN']) ? 'true' : 'false' ?>;

const STATUS_BADGE = {
    Draft:  'bg-amber-100 text-amber-600',
    Posted: 'bg-emerald-100 text-emerald-600',
    Voided: 'bg-rose-100 text-rose-600',
};

function currency(val) {
    return '₱ ' + parseFloat(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function loadJournals(status = '') {
    const tbody = document.getElementById('journalTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');
    if (status) formData.append('status', status);

    try {
        const res = await fetch(BASE_URL + '/controllers/JournalController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
            return;
        }

        if (data.journals.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">No journal entries found.</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        data.journals.forEach(j => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-line hover:bg-[#f8fafc]';

            let actions = `<button onclick="viewJournal(${j.journal_id})" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-orange"><i class='bx bx-show'></i></button>`;
            if (j.status === 'Draft' && userCanPost) {
                actions += `
                    <button onclick="postJournal(${j.journal_id})" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-emerald-600" title="Post"><i class='bx bx-check-double'></i></button>
                    <button onclick="voidJournal(${j.journal_id})" class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-rose-600" title="Void"><i class='bx bx-x-circle'></i></button>`;
            }

            const badgeClass = STATUS_BADGE[j.status] || 'bg-[#eef1f6] text-textMid';

            tr.innerHTML = `
                <td class="px-5 py-3 align-top font-semibold text-textDark whitespace-nowrap">${j.journal_no}</td>
                <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${j.journal_date}</td>
                <td class="px-2 py-3 align-top text-textMid max-w-[260px] truncate">${j.description ?? ''}</td>
                <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(j.total_debit)}</td>
                <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(j.total_credit)}</td>
                <td class="px-2 py-3 align-top"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${j.status}</span></td>
                <td class="px-5 py-3 align-top text-right whitespace-nowrap">${actions}</td>
            `;
            tbody.appendChild(tr);
        });

    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Something went wrong loading journals.</td></tr>';
    }
}

function viewJournal(journalId) {
    window.location.href = BASE_URL + '/views/shared/ledger/view.php?id=' + journalId;
}

async function postJournal(journalId) {
    if (!confirm('Post journal #' + journalId + '? This cannot be undone.')) return;

    const formData = new FormData();
    formData.append('action', 'post_journal');
    formData.append('journal_id', journalId);

    const res = await fetch(BASE_URL + '/controllers/JournalController.php', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();

    alert(data.message);
    if (data.success) loadJournals(document.getElementById('statusFilter').value);
}

async function voidJournal(journalId) {
    if (!confirm('Void journal #' + journalId + '?')) return;

    const formData = new FormData();
    formData.append('action', 'void_journal');
    formData.append('journal_id', journalId);

    const res = await fetch(BASE_URL + '/controllers/JournalController.php', {
        method: 'POST',
        body: formData
    });
    const data = await res.json();

    alert(data.message);
    if (data.success) loadJournals(document.getElementById('statusFilter').value);
}

// ── Status tabs (pill buttons) stay in sync with the select ──
const statusTabs = document.querySelectorAll('.status-tab');
const statusFilter = document.getElementById('statusFilter');

function setActiveTab(status) {
    statusTabs.forEach(btn => {
        const isActive = btn.dataset.status === status;
        btn.classList.toggle('bg-navy900', isActive);
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('bg-[#eef1f5]', !isActive);
        btn.classList.toggle('text-textMid', !isActive);
    });
}

statusTabs.forEach(btn => {
    btn.addEventListener('click', function () {
        const status = this.dataset.status;
        setActiveTab(status);
        statusFilter.value = status;
        loadJournals(status);
    });
});

statusFilter.addEventListener('change', function () {
    setActiveTab(this.value);
    loadJournals(this.value);
});

loadJournals();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';