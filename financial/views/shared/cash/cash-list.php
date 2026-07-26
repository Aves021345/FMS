<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']);

$pageTitle = 'Cash Management';

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-money'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Cash Management</h1>
            </div>
            <?php if (hasRole(['ROLE_FINANCE', 'ROLE_ADMIN'])): ?>
                <a href="<?= BASE_URL ?>/views/shared/cash/create.php"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                    <i class='bx bx-plus'></i>New Cash Transaction
                </a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Deposits</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-trending-up'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalDeposits">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statDepositsCount">0 transactions</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Withdrawals</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-trending-down'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalWithdrawals">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statWithdrawalsCount">0 transactions</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Net Cash Flow</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-wallet'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statNetFlow">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statTxnCount">0 total transactions</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold" id="typeTabs">
                <button type="button" data-type="" class="type-tab px-4 py-2 rounded-full bg-navy900 text-white">All</button>
                <button type="button" data-type="Deposit" class="type-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Deposit</button>
                <button type="button" data-type="Withdrawal" class="type-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Withdrawal</button>
                <button type="button" data-type="Transfer In" class="type-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Transfer In</button>
                <button type="button" data-type="Transfer Out" class="type-tab px-4 py-2 rounded-full bg-[#eef1f5] text-textMid hover:bg-[#e4e8ee]">Transfer Out</button>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                    <input type="text" id="searchInput" placeholder="Search account, description, or ref..."
                           class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
                <select id="typeFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Types</option>
                    <option value="Deposit">Deposit</option>
                    <option value="Withdrawal">Withdrawal</option>
                    <option value="Transfer In">Transfer In</option>
                    <option value="Transfer Out">Transfer Out</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Date</th>
                        <th class="px-2 py-3 font-semibold">Bank Account</th>
                        <th class="px-2 py-3 font-semibold">Type</th>
                        <th class="px-2 py-3 font-semibold text-right">Amount</th>
                        <th class="px-2 py-3 font-semibold">Description</th>
                        <th class="px-5 py-3 font-semibold">Reference</th>
                    </tr>
                </thead>
                <tbody id="cashTableBody">
                    <tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let currentTransactions = [];

const TYPE_BADGE = {
    'Deposit':       'bg-emerald-100 text-emerald-600',
    'Withdrawal':    'bg-rose-100 text-rose-600',
    'Transfer In':   'bg-blue-100 text-blue-600',
    'Transfer Out':  'bg-amber-100 text-amber-600',
};

const INFLOW_TYPES = ['Deposit', 'Transfer In'];

function currency(val) {
    return '₱ ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function updateStats(transactions) {
    const deposits = transactions.filter(t => t.transaction_type === 'Deposit');
    const withdrawals = transactions.filter(t => t.transaction_type === 'Withdrawal');
    const depositsTotal = deposits.reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);
    const withdrawalsTotal = withdrawals.reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);
    const netFlow = transactions.reduce((sum, t) => {
        const amt = parseFloat(t.amount || 0);
        return sum + (INFLOW_TYPES.includes(t.transaction_type) ? amt : -amt);
    }, 0);

    setText('statTotalDeposits', currency(depositsTotal));
    setText('statDepositsCount', deposits.length + ' transaction' + (deposits.length === 1 ? '' : 's'));
    setText('statTotalWithdrawals', currency(withdrawalsTotal));
    setText('statWithdrawalsCount', withdrawals.length + ' transaction' + (withdrawals.length === 1 ? '' : 's'));
    setText('statNetFlow', currency(netFlow));
    setText('statTxnCount', transactions.length + ' total transactions');
}

function renderRows(transactions) {
    const tbody = document.getElementById('cashTableBody');

    if (transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">No cash transactions found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    transactions.forEach(tx => {
        const badge = TYPE_BADGE[tx.transaction_type] || 'bg-[#eef1f6] text-textMid';
        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';
        tr.innerHTML = `
            <td class="px-5 py-3 text-textMid whitespace-nowrap">${tx.transaction_date}</td>
            <td class="px-2 py-3 text-textDark font-medium">${tx.bank_account_name}</td>
            <td class="px-2 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badge}">${tx.transaction_type}</span></td>
            <td class="px-2 py-3 text-right font-semibold text-textDark whitespace-nowrap">${currency(tx.amount)}</td>
            <td class="px-2 py-3 text-textMid max-w-[220px] truncate">${tx.description ?? ''}</td>
            <td class="px-5 py-3 text-textMid whitespace-nowrap">${tx.reference_no ?? ''}</td>
        `;
        tbody.appendChild(tr);
    });
}

function applySearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    if (!term) {
        renderRows(currentTransactions);
        return;
    }
    const filtered = currentTransactions.filter(tx =>
        (tx.bank_account_name || '').toLowerCase().includes(term) ||
        (tx.description || '').toLowerCase().includes(term) ||
        (tx.reference_no || '').toLowerCase().includes(term)
    );
    renderRows(filtered);
}

async function loadCashTransactions(type = '') {
    const tbody = document.getElementById('cashTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');
    if (type) formData.append('type', type);

    // Step 1: network call + JSON parse. Only failures here are real "couldn't load" errors.
    let data;
    try {
        const res = await fetch(BASE_URL + '/controllers/CashController.php', {
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
        console.error('Cash transactions fetch/parse failed:', err);
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">
            Something went wrong loading transactions.<br>
            <span class="text-xs text-textSoft break-all">${(err.message || '').replace(/</g, '&lt;')}</span>
        </td></tr>`;
        return;
    }

    if (!data.success) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
        return;
    }

    currentTransactions = data.transactions;

    // Step 2: rendering. Kept separate so a stat/DOM bug never masquerades as a network error.
    try {
        updateStats(currentTransactions);
        renderRows(currentTransactions);
    } catch (err) {
        console.error('Cash transactions render failed:', err);
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">Data loaded, but the page failed to render it. Check the console for details.</td></tr>';
    }
}

// ── Type tabs stay in sync with the select ──
const typeTabs = document.querySelectorAll('.type-tab');
const typeFilter = document.getElementById('typeFilter');

function setActiveTab(type) {
    typeTabs.forEach(btn => {
        const isActive = btn.dataset.type === type;
        btn.classList.toggle('bg-navy900', isActive);
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('bg-[#eef1f5]', !isActive);
        btn.classList.toggle('text-textMid', !isActive);
    });
}

typeTabs.forEach(btn => {
    btn.addEventListener('click', function () {
        const type = this.dataset.type;
        setActiveTab(type);
        typeFilter.value = type;
        loadCashTransactions(type);
    });
});

typeFilter.addEventListener('change', function () {
    setActiveTab(this.value);
    loadCashTransactions(this.value);
});

document.getElementById('searchInput').addEventListener('input', applySearch);

loadCashTransactions();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';