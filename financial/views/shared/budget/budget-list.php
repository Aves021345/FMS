<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']);

$pageTitle = 'Budget Management';

$periods = $pdo->query("SELECT period_id, period_name FROM fiscalperiods ORDER BY start_date DESC")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">
    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-pie-chart-alt-2'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Budget Management</h1>
            </div>
            <?php if (hasRole(['ROLE_FINANCE', 'ROLE_ADMIN'])): ?>
                <div class="flex items-center gap-3">
                    <button type="button" id="refreshActualsBtn"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#eef1f5] text-textMid text-sm font-semibold hover:bg-[#e4e8ee] transition-colors">
                        <i class='bx bx-refresh text-orange'></i>Refresh Actuals
                    </button>
                    <a href="<?= BASE_URL ?>/views/shared/budget/create.php"
                       class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors no-underline">
                        <i class='bx bx-plus'></i>New Budget Line
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Budget</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-wallet'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalBudget">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statLineCount">0 budget lines</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Actual</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-receipt'></i></div>
                </div>
                <div class="text-2xl font-extrabold text-textDark mb-1" id="statTotalActual">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statUtilization">0% of budget used</div>
            </div>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Variance</span>
                    <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-git-compare'></i></div>
                </div>
                <div class="text-2xl font-extrabold mb-1" id="statTotalVariance">₱ 0.00</div>
                <div class="text-xs text-textSoft" id="statOverBudgetCount">0 lines over budget</div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 border-b border-line">
            <div class="flex items-center gap-2 text-sm font-semibold text-textDark">
                <i class='bx bx-filter-alt text-orange text-lg'></i> Budget Lines
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-orange'></i>
                    <input type="text" id="searchInput" placeholder="Search account or department..."
                           class="pl-9 pr-3 py-2 rounded-lg bg-[#eef1f5] border-none text-sm text-textDark placeholder:text-textSoft focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
                <select id="periodFilter" class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All Periods</option>
                    <?php foreach ($periods as $p): ?>
                        <option value="<?= $p['period_id'] ?>"><?= htmlspecialchars($p['period_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold">Account</th>
                        <th class="px-2 py-3 font-semibold">Period</th>
                        <th class="px-2 py-3 font-semibold">Department</th>
                        <th class="px-2 py-3 font-semibold text-right">Budget</th>
                        <th class="px-2 py-3 font-semibold text-right">Actual</th>
                        <th class="px-5 py-3 font-semibold text-right">Variance</th>
                    </tr>
                </thead>
                <tbody id="budgetTableBody">
                    <tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let currentBudgets = [];

function currency(val) {
    return '₱ ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function setClass(id, addClasses, removeClasses) {
    const el = document.getElementById(id);
    if (!el) return;
    if (removeClasses) el.classList.remove(...removeClasses);
    if (addClasses) el.classList.add(...addClasses);
}

function updateStats(budgets) {
    const totalBudget = budgets.reduce((sum, b) => sum + parseFloat(b.budget_amount || 0), 0);
    const totalActual = budgets.reduce((sum, b) => sum + parseFloat(b.actual_amount || 0), 0);
    const totalVariance = budgets.reduce((sum, b) => sum + parseFloat(b.variance || 0), 0);
    const overBudget = budgets.filter(b => parseFloat(b.variance || 0) < 0);
    const utilization = totalBudget > 0 ? (totalActual / totalBudget * 100) : 0;

    setText('statTotalBudget', currency(totalBudget));
    setText('statLineCount', budgets.length + ' budget line' + (budgets.length === 1 ? '' : 's'));
    setText('statTotalActual', currency(totalActual));
    setText('statUtilization', utilization.toFixed(1) + '% of budget used');
    setText('statTotalVariance', currency(totalVariance));
    setText('statOverBudgetCount', overBudget.length + ' line' + (overBudget.length === 1 ? '' : 's') + ' over budget');

    setClass('statTotalVariance',
        totalVariance < 0 ? ['text-rose-600'] : ['text-emerald-600'],
        totalVariance < 0 ? ['text-emerald-600'] : ['text-rose-600']);
}

function renderRows(budgets) {
    const tbody = document.getElementById('budgetTableBody');

    if (budgets.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">No budget lines found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    budgets.forEach(b => {
        const variance = parseFloat(b.variance || 0);
        const varianceBadge = variance < 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600';

        const tr = document.createElement('tr');
        tr.className = 'border-b border-line hover:bg-[#f8fafc]';
        tr.innerHTML = `
            <td class="px-5 py-3 text-textDark font-medium">${b.account_code} - ${b.account_name}</td>
            <td class="px-2 py-3 text-textMid whitespace-nowrap">${b.period_name}</td>
            <td class="px-2 py-3 text-textMid">${b.department ?? ''}</td>
            <td class="px-2 py-3 text-right font-semibold text-textDark whitespace-nowrap">${currency(b.budget_amount)}</td>
            <td class="px-2 py-3 text-right font-semibold text-textDark whitespace-nowrap">${currency(b.actual_amount)}</td>
            <td class="px-5 py-3 text-right whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${varianceBadge}">${currency(variance)}</span>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function applySearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    if (!term) {
        renderRows(currentBudgets);
        return;
    }
    const filtered = currentBudgets.filter(b =>
        `${b.account_code} ${b.account_name}`.toLowerCase().includes(term) ||
        (b.department || '').toLowerCase().includes(term)
    );
    renderRows(filtered);
}

async function loadBudgets(periodId = '') {
    const tbody = document.getElementById('budgetTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-textSoft">Loading...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'list');
    if (periodId) formData.append('period_id', periodId);

    // Step 1: network call + JSON parse. Only failures here are real "couldn't load" errors.
    let data;
    try {
        const res = await fetch(BASE_URL + '/controllers/BudgetController.php', {
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
        console.error('Budget list fetch/parse failed:', err);
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">
            Something went wrong loading budgets.<br>
            <span class="text-xs text-textSoft break-all">${(err.message || '').replace(/</g, '&lt;')}</span>
        </td></tr>`;
        return;
    }

    if (!data.success) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">Error: ${data.message}</td></tr>`;
        return;
    }

    currentBudgets = data.budgets;

    // Step 2: rendering. Kept separate so a stat/DOM bug never masquerades as a network error.
    try {
        updateStats(currentBudgets);
        renderRows(currentBudgets);
    } catch (err) {
        console.error('Budget list render failed:', err);
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-rose-600">Data loaded, but the page failed to render it. Check the console for details.</td></tr>';
    }
}

async function refreshActuals() {
    const periodId = document.getElementById('periodFilter').value;
    if (!periodId) {
        alert('Please select a specific fiscal period first to refresh its actuals.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'refresh');
    formData.append('period_id', periodId);

    try {
        const res = await fetch(BASE_URL + '/controllers/BudgetController.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });
        const rawText = await res.text();
        const data = JSON.parse(rawText);

        alert(data.message);
        if (data.success) loadBudgets(periodId);
    } catch (err) {
        console.error('Refresh actuals failed:', err);
        alert('Something went wrong refreshing actuals. Check the console for details.');
    }
}

document.getElementById('refreshActualsBtn')?.addEventListener('click', refreshActuals);
document.getElementById('periodFilter').addEventListener('change', function () {
    loadBudgets(this.value);
});
document.getElementById('searchInput').addEventListener('input', applySearch);

loadBudgets();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';