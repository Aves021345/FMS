<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Tax Management';

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-calculator'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Tax Management</h1>
            </div>
            <?php if (hasRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN'])): ?>
                <a href="<?= BASE_URL ?>/views/shared/tax/create.php"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                    <i class='bx bx-plus'></i>New Tax Record
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Tax Liability</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-calculator'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalTax">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statRecordCount">0 records</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Pending / Filed</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-time-five'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statPendingAmount">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statPendingCount">0 awaiting payment</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Paid</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-check-double'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statPaidAmount">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statPaidCount">0 settled</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="statusTabs">
                <button type="button" data-status="" class="status-tab px-4 py-2 rounded-full bg-navy900 text-white">All</button>
                <button type="button" data-status="Pending" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Pending</button>
                <button type="button" data-status="Filed" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Filed</button>
                <button type="button" data-status="Paid" class="status-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Paid</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                    <input type="text" id="searchInput" placeholder="Search tax type or reference..."
                           class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
                <select id="statusFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Filed">Filed</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Tax Type</th>
                        <th class="px-2 py-3 font-semibold">Period</th>
                        <th class="px-2 py-3 font-semibold">Transaction Ref</th>
                        <th class="px-2 py-3 font-semibold text-right">Taxable Amount</th>
                        <th class="px-2 py-3 font-semibold text-right">Tax Amount</th>
                        <th class="px-2 py-3 font-semibold">Status</th>
                        <th class="px-2 py-3 font-semibold">Filed Date</th>
                        <th class="px-5 py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="taxTableBody">
                    <tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const canManage = <?= hasRole(['ROLE_FINANCE', 'ROLE_ADMIN']) ? 'true' : 'false' ?>;
let currentRecords = [];

const STATUS_BADGE = {
    Pending: 'bg-amber-100 text-amber-600',
    Filed:   'bg-blue-100 text-blue-600',
    Paid:    'bg-emerald-100 text-emerald-600',
};

function currency(val) {
    return '₱ ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

// Central helper so fetch calls behave consistently: reads raw text first so
// a non-JSON response (redirect, PHP warning, fatal error) is visible instead
// of throwing an opaque "Unexpected token" error.
async function postAction(action, params = {}) {
    const formData = new FormData();
    formData.append('action', action);
    Object.entries(params).forEach(([key, val]) => formData.append(key, val));

    const res = await fetch(BASE_URL + '/controllers/TaxController.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
    });

    const rawText = await res.text();

    if (!res.ok) {
        throw new Error(`Server returned ${res.status} ${res.statusText}. Body: ${rawText.slice(0, 300)}`);
    }

    try {
        return JSON.parse(rawText);
    } catch (parseErr) {
        throw new Error(`Response wasn't valid JSON. First 300 chars: ${rawText.slice(0, 300)}`);
    }
}

function updateStats(records) {
    const totalTax = records.reduce((sum, t) => sum + parseFloat(t.tax_amount || 0), 0);
    const pendingOrFiled = records.filter(t => t.status === 'Pending' || t.status === 'Filed');
    const pendingAmount = pendingOrFiled.reduce((sum, t) => sum + parseFloat(t.tax_amount || 0), 0);
    const paid = records.filter(t => t.status === 'Paid');
    const paidAmount = paid.reduce((sum, t) => sum + parseFloat(t.tax_amount || 0), 0);

    setText('statTotalTax', currency(totalTax));
    setText('statRecordCount', records.length + ' record' + (records.length === 1 ? '' : 's'));
    setText('statPendingAmount', currency(pendingAmount));
    setText('statPendingCount', pendingOrFiled.length + ' awaiting payment');
    setText('statPaidAmount', currency(paidAmount));
    setText('statPaidCount', paid.length + ' settled');
}

function renderRows(records) {
    const tbody = document.getElementById('taxTableBody');

    if (records.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">No tax records found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    records.forEach(t => {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';

        let actions = '<span class="text-textSoft">-</span>';
        if (canManage) {
            if (t.status === 'Pending') {
                actions = `<button onclick="fileTax(${t.tax_id})"
                              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-navy900 text-white text-xs font-semibold hover:bg-navyDark">
                              <i class='bx bx-file'></i>Mark Filed</button>`;
            } else if (t.status === 'Filed') {
                actions = `<button onclick="markPaid(${t.tax_id})"
                              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-orange text-navy900 text-xs font-bold hover:bg-orangeDark">
                              <i class='bx bx-check'></i>Mark Paid</button>`;
            }
        }

        const badgeClass = STATUS_BADGE[t.status] || 'bg-[#eef1f6] text-textMid';

        tr.innerHTML = `
            <td class="px-5 py-3 align-top text-textDark font-medium">${t.tax_code} - ${t.tax_name} (${parseFloat(t.tax_rate).toFixed(2)}%)</td>
            <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${t.tax_period}</td>
            <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${t.transaction_ref ?? ''}</td>
            <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(t.taxable_amount)}</td>
            <td class="px-2 py-3 align-top text-right font-semibold text-textDark whitespace-nowrap">${currency(t.tax_amount)}</td>
            <td class="px-2 py-3 align-top"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${t.status}</span></td>
            <td class="px-2 py-3 align-top text-textMid whitespace-nowrap">${t.filed_date ?? ''}</td>
            <td class="px-5 py-3 align-top text-right whitespace-nowrap">${actions}</td>
        `;
        tbody.appendChild(tr);
    });
}

function applySearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    if (!term) {
        renderRows(currentRecords);
        return;
    }
    const filtered = currentRecords.filter(t =>
        `${t.tax_code} ${t.tax_name}`.toLowerCase().includes(term) ||
        (t.transaction_ref || '').toLowerCase().includes(term)
    );
    renderRows(filtered);
}

async function loadTaxRecords(status = '') {
    const tbody = document.getElementById('taxTableBody');
    tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    let data;
    try {
        data = await postAction('list', status ? { status } : {});
    } catch (err) {
        console.error('Tax records fetch/parse failed:', err);
        tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-8 text-center text-rose-600">
            Something went wrong loading tax records.<br>
            <span class="text-xs text-textSoft break-all">${(err.message || '').replace(/</g, '&lt;')}</span>
        </td></tr>`;
        return;
    }

    if (!data.success) {
        tbody.innerHTML = `<tr><td colspan="8" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
        return;
    }

    currentRecords = data.records;

    try {
        updateStats(currentRecords);
        renderRows(currentRecords);
    } catch (err) {
        console.error('Tax records render failed:', err);
        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-8 text-center text-rose-600">Data loaded, but the page failed to render it. Check the console for details.</td></tr>';
    }
}

async function fileTax(taxId) {
    if (!confirm('Mark tax record #' + taxId + ' as Filed?')) return;
    try {
        const data = await postAction('file', { tax_id: taxId });
        alert(data.message);
        if (data.success) loadTaxRecords(document.getElementById('statusFilter').value);
    } catch (err) {
        console.error('File tax failed:', err);
        alert('Something went wrong. Check the console for details.');
    }
}

async function markPaid(taxId) {
    if (!confirm('Mark tax record #' + taxId + ' as Paid?')) return;
    try {
        const data = await postAction('mark_paid', { tax_id: taxId });
        alert(data.message);
        if (data.success) loadTaxRecords(document.getElementById('statusFilter').value);
    } catch (err) {
        console.error('Mark paid failed:', err);
        alert('Something went wrong. Check the console for details.');
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
        loadTaxRecords(status);
    });
});

statusFilter.addEventListener('change', function () {
    setActiveTab(this.value);
    loadTaxRecords(this.value);
});

document.getElementById('searchInput').addEventListener('input', applySearch);

loadTaxRecords();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';