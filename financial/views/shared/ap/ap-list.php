<?php
/**
 * Accounts Payable - Invoice List
 */

require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Accounts Payable';

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-credit-card'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Accounts Payable</h1>
            </div>
            <?php if (hasRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN'])): ?>
                <a href="<?= BASE_URL ?>/views/shared/ap/create.php"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                    <i class='bx bx-plus'></i>New AP Invoice
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5" id="statCards">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Invoiced</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-receipt'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalInvoiced">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statInvoiceCount">0 invoices</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Outstanding Balance</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-wallet'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statOutstanding">₱ 0.00</div>
                <div class="text-xs text-textSoft">Open + Partially Paid</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Overdue</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-error-circle'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statOverdue">0</div>
                <div class="text-xs text-textSoft">Past due date, unpaid</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="statusTabs">
                <button type="button" data-status="" class="status-tab px-4 py-2 rounded-full bg-navy900 text-white">All</button>
                <button type="button" data-status="Open" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Open</button>
                <button type="button" data-status="Partially Paid" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Partially Paid</button>
                <button type="button" data-status="Paid" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Paid</button>
                <button type="button" data-status="Voided" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Voided</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                    <input type="text" id="searchInput" placeholder="Search invoice or supplier..."
                           class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
                <select id="statusFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Statuses</option>
                    <option value="Open">Open</option>
                    <option value="Partially Paid">Partially Paid</option>
                    <option value="Paid">Paid</option>
                    <option value="Voided">Voided</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Invoice No.</th>
                        <th class="px-2 py-3 font-semibold">Supplier</th>
                        <th class="px-2 py-3 font-semibold">Invoice Date</th>
                        <th class="px-2 py-3 font-semibold">Due Date</th>
                        <th class="px-2 py-3 font-semibold text-right">Amount</th>
                        <th class="px-2 py-3 font-semibold text-right">Paid</th>
                        <th class="px-2 py-3 font-semibold text-right">Balance</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody id="apTableBody">
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-textSoft">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let currentInvoices = [];

const STATUS_BADGE = {
    'Open':            'bg-blue-100 text-blue-600',
    'Partially Paid':  'bg-amber-100 text-amber-600',
    'Paid':            'bg-emerald-100 text-emerald-600',
    'Voided':          'bg-rose-100 text-rose-600',
};

function currency(val) {
    return '₱ ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateStats(invoices) {
    const totalInvoiced = invoices.reduce((sum, ap) => sum + parseFloat(ap.amount || 0), 0);
    const outstanding = invoices
        .filter(ap => ap.status === 'Open' || ap.status === 'Partially Paid')
        .reduce((sum, ap) => sum + parseFloat(ap.balance || 0), 0);
    const today = new Date().toISOString().slice(0, 10);
    const overdueCount = invoices.filter(ap =>
        ap.due_date < today && (ap.status === 'Open' || ap.status === 'Partially Paid')
    ).length;

    document.getElementById('statTotalInvoiced').textContent = currency(totalInvoiced);
    document.getElementById('statInvoiceCount').textContent = invoices.length + ' invoice' + (invoices.length === 1 ? '' : 's');
    document.getElementById('statOutstanding').textContent = currency(outstanding);
    document.getElementById('statOverdue').textContent = overdueCount;
}

function renderRows(invoices) {
    const tbody = document.getElementById('apTableBody');

    if (invoices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">No AP invoices found.</td></tr>';
        return;
    }

    const today = new Date().toISOString().slice(0, 10);

    tbody.innerHTML = '';
    invoices.forEach(ap => {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';

        const isOverdue = ap.due_date < today && (ap.status === 'Open' || ap.status === 'Partially Paid');
        const badgeClass = STATUS_BADGE[ap.status] || 'bg-[#eef1f6] text-textMid';

        tr.innerHTML = `
            <td class="px-5 py-3 align-top font-semibold text-textDark whitespace-nowrap">${ap.invoice_no}</td>
            <td class="px-2 py-3 align-top text-textDark">${ap.supplier_name}</td>
            <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${ap.invoice_date}</td>
            <td class="px-2 py-3 align-top whitespace-nowrap ${isOverdue ? 'text-rose-600 font-semibold' : 'text-textMid'}">${ap.due_date}</td>
            <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(ap.amount)}</td>
            <td class="px-2 py-3 align-top text-right text-textMid whitespace-nowrap">${currency(ap.amount_paid)}</td>
            <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(ap.balance)}</td>
            <td class="px-5 py-3 align-top"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${ap.status}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

function applySearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    if (!term) {
        renderRows(currentInvoices);
        return;
    }
    const filtered = currentInvoices.filter(ap =>
        ap.invoice_no.toLowerCase().includes(term) ||
        ap.supplier_name.toLowerCase().includes(term)
    );
    renderRows(filtered);
}

async function loadAP(status = '') {
    const tbody = document.getElementById('apTableBody');
    tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');
    if (status) formData.append('status', status);

    try {
        const res = await fetch(BASE_URL + '/controllers/APController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
            return;
        }

        currentInvoices = data.invoices;
        updateStats(currentInvoices);
        renderRows(currentInvoices);

    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-rose-600">Something went wrong loading AP invoices.</td></tr>';
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
        loadAP(status);
    });
});

statusFilter.addEventListener('change', function () {
    setActiveTab(this.value);
    loadAP(this.value);
});

document.getElementById('searchInput').addEventListener('input', applySearch);

loadAP();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';