<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Collection Management';

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-archive-in'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Collection Management</h1>
            </div>
            <?php if (hasRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN'])): ?>
                <a href="<?= BASE_URL ?>/views/shared/collection/create.php"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                    <i class='bx bx-plus'></i>Record New Collection
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Collected</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-wallet'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalCollected">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statCollectionCount">0 records</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Pending Application</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-time-five'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statPendingAmount">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statPendingCount">0 pending</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Applied</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-check-double'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statAppliedCount">0</div>
                <div class="text-xs text-textSoft">Fully applied to AR</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="statusTabs">
                <button type="button" data-status="" class="status-tab px-4 py-2 rounded-full bg-navy900 text-white">All</button>
                <button type="button" data-status="Pending" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Pending</button>
                <button type="button" data-status="Applied" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Applied</button>
                <button type="button" data-status="Voided" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Voided</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                    <input type="text" id="searchInput" placeholder="Search customer or reference..."
                           class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
                <select id="statusFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Applied">Applied</option>
                    <option value="Voided">Voided</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Date</th>
                        <th class="px-2 py-3 font-semibold">Customer</th>
                        <th class="px-2 py-3 font-semibold text-right">Amount</th>
                        <th class="px-2 py-3 font-semibold">Method</th>
                        <th class="px-2 py-3 font-semibold">Reference</th>
                        <th class="px-2 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="collTableBody">
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
const canApply = <?= hasRole(['ROLE_FINANCE', 'ROLE_ADMIN']) ? 'true' : 'false' ?>;
let currentCollections = [];

const STATUS_BADGE = {
    Pending: 'bg-amber-100 text-amber-600',
    Applied: 'bg-emerald-100 text-emerald-600',
    Voided:  'bg-rose-100 text-rose-600',
};

function currency(val) {
    return '₱ ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateStats(collections) {
    const totalCollected = collections.reduce((sum, c) => sum + parseFloat(c.amount || 0), 0);
    const pending = collections.filter(c => c.status === 'Pending');
    const pendingAmount = pending.reduce((sum, c) => sum + parseFloat(c.amount || 0), 0);
    const appliedCount = collections.filter(c => c.status === 'Applied').length;

    document.getElementById('statTotalCollected').textContent = currency(totalCollected);
    document.getElementById('statCollectionCount').textContent = collections.length + ' record' + (collections.length === 1 ? '' : 's');
    document.getElementById('statPendingAmount').textContent = currency(pendingAmount);
    document.getElementById('statPendingCount').textContent = pending.length + ' pending';
    document.getElementById('statAppliedCount').textContent = appliedCount;
}

function renderRows(collections) {
    const tbody = document.getElementById('collTableBody');

    if (collections.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">No collections found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    collections.forEach(c => {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';

        let actions = '<span class="text-textSoft">-</span>';
        if (c.status === 'Pending' && canApply) {
            actions = `<a href="${BASE_URL}/views/shared/collection/apply.php?collection_id=${c.collection_id}"
                          class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-navy900 text-white text-xs font-semibold hover:bg-navyDark no-underline">
                          <i class='bx bx-link'></i>Apply</a>`;
        }

        const badgeClass = STATUS_BADGE[c.status] || 'bg-[#eef1f6] text-textMid';

        tr.innerHTML = `
            <td class="px-5 py-3 align-top text-textMid whitespace-nowrap">${c.collection_date}</td>
            <td class="px-2 py-3 align-top text-textDark font-semibold">${c.customer_name}</td>
            <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(c.amount)}</td>
            <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${c.payment_method}</td>
            <td class="px-2 py-3 align-top text-textMid">${c.reference_no ?? ''}</td>
            <td class="px-2 py-3 align-top"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${c.status}</span></td>
            <td class="px-5 py-3 align-top text-right whitespace-nowrap">${actions}</td>
        `;
        tbody.appendChild(tr);
    });
}

function applySearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    if (!term) {
        renderRows(currentCollections);
        return;
    }
    const filtered = currentCollections.filter(c =>
        c.customer_name.toLowerCase().includes(term) ||
        (c.reference_no ?? '').toLowerCase().includes(term)
    );
    renderRows(filtered);
}

async function loadCollections(status = '') {
    const tbody = document.getElementById('collTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');
    if (status) formData.append('status', status);

    try {
        const res = await fetch(BASE_URL + '/controllers/CollectionController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
            return;
        }

        currentCollections = data.collections;
        updateStats(currentCollections);
        renderRows(currentCollections);

    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-rose-600">Something went wrong loading collections.</td></tr>';
    }
}

// ── Status tabs stay in sync with the select ──
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
        loadCollections(status);
    });
});

statusFilter.addEventListener('change', function () {
    setActiveTab(this.value);
    loadCollections(this.value);
});

document.getElementById('searchInput').addEventListener('input', applySearch);

loadCollections();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';