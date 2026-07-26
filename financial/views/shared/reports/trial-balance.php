<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Trial Balance';

$periods = $pdo->query("SELECT period_id, period_name FROM fiscalperiods ORDER BY start_date DESC")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/reports/reports.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Reports
    </a>

    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-balance'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Trial Balance</h1>
            </div>

            <div class="flex items-center gap-2">
                <label for="periodSelect" class="text-xs font-semibold text-textMid whitespace-nowrap">Fiscal Period</label>
                <select id="periodSelect"
                        class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">-- Select a period --</option>
                    <?php foreach ($periods as $p): ?>
                        <option value="<?= $p['period_id'] ?>"><?= htmlspecialchars($p['period_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div id="reportArea">
        <div class="bg-card rounded-2xl border border-line shadow-brandSm p-10 text-center text-textMid">
            <i class='bx bx-calendar-check text-3xl text-textSoft mb-3'></i>
            <div class="font-semibold text-textDark">Select a fiscal period</div>
            <div class="text-sm mt-1">Choose a period above to view its trial balance.</div>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

function currency(val) {
    return parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

async function loadTrialBalance(periodId) {
    const area = document.getElementById('reportArea');

    if (!periodId) {
        area.innerHTML = `
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-10 text-center text-textMid">
                <i class='bx bx-calendar-check text-3xl text-textSoft mb-3'></i>
                <div class="font-semibold text-textDark">Select a fiscal period</div>
                <div class="text-sm mt-1">Choose a period above to view its trial balance.</div>
            </div>`;
        return;
    }

    area.innerHTML = `
        <div class="bg-card rounded-2xl border border-line shadow-brandSm p-10 text-center text-textSoft">
            Loading...
        </div>`;

    const formData = new FormData();
    formData.append('action', 'trial_balance');
    formData.append('period_id', periodId);

    // Step 1: network call + JSON parse.
    let result;
    try {
        const res = await fetch(BASE_URL + '/controllers/ReportController.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const rawText = await res.text();

        if (!res.ok) {
            throw new Error(`Server returned ${res.status} ${res.statusText}. Body: ${rawText.slice(0, 300)}`);
        }

        try {
            result = JSON.parse(rawText);
        } catch (parseErr) {
            throw new Error(`Response wasn't valid JSON. First 300 chars: ${rawText.slice(0, 300)}`);
        }

    } catch (err) {
        console.error('Trial balance fetch/parse failed:', err);
        area.innerHTML = `
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-8 text-center">
                <i class='bx bx-error-circle text-2xl text-rose-500 mb-2'></i>
                <div class="font-semibold text-textDark">Something went wrong loading the trial balance.</div>
                <div class="text-xs text-textSoft mt-1 break-all">${escapeHtml(err.message)}</div>
            </div>`;
        return;
    }

    if (!result.success) {
        area.innerHTML = `
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-8 text-center text-rose-600 font-medium">
                Error: ${escapeHtml(result.message)}
            </div>`;
        return;
    }

    // Step 2: rendering.
    try {
        const data = result.data;

        if (data.accounts.length === 0) {
            area.innerHTML = `
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-10 text-center text-textMid">
                    <i class='bx bx-folder-open text-3xl text-textSoft mb-3'></i>
                    <div class="font-semibold text-textDark">No posted activity</div>
                    <div class="text-sm mt-1">This fiscal period has no posted journal activity yet.</div>
                </div>`;
            return;
        }

        const balancedBanner = data.is_balanced
            ? `<div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm font-semibold">
                   <i class='bx bx-check-circle text-lg'></i> Trial balance is in balance.
               </div>`
            : `<div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-sm font-semibold">
                   <i class='bx bx-error text-lg'></i> Trial balance is OUT OF BALANCE. This should never happen if journals are validated correctly — investigate immediately.
               </div>`;

        const rows = data.accounts.map(a => `
            <tr class="border-b border-line last:border-b-0 hover:bg-[#f8fafc]">
                <td class="px-5 py-3 font-semibold text-textDark whitespace-nowrap">${escapeHtml(a.account_code)}</td>
                <td class="px-2 py-3 text-textDark">${escapeHtml(a.account_name)}</td>
                <td class="px-2 py-3"><span class="inline-block text-[11px] px-2.5 py-1 rounded-full bg-[#eef1f6] text-textMid font-semibold">${escapeHtml(a.account_type)}</span></td>
                <td class="px-2 py-3 text-right font-semibold text-textDark whitespace-nowrap">${currency(a.debit_balance)}</td>
                <td class="px-5 py-3 text-right font-semibold text-textDark whitespace-nowrap">${currency(a.credit_balance)}</td>
            </tr>
        `).join('');

        area.innerHTML = `
            <div class="space-y-4">
                ${balancedBanner}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Debit</span>
                            <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-trending-down'></i></div>
                        </div>
                        <div class="text-2xl font-extrabold text-textDark">₱ ${currency(data.total_debit)}</div>
                    </div>
                    <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-[.7rem] font-bold text-textMid uppercase tracking-wide">Total Credit</span>
                            <div class="w-9 h-9 rounded-lg bg-navy900 flex items-center justify-center text-orange text-base shrink-0"><i class='bx bx-trending-up'></i></div>
                        </div>
                        <div class="text-2xl font-extrabold text-textDark">₱ ${currency(data.total_credit)}</div>
                    </div>
                </div>

                <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-separate border-spacing-0">
                            <thead>
                                <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                                    <th class="px-5 py-3 font-semibold whitespace-nowrap">Account Code</th>
                                    <th class="px-2 py-3 font-semibold">Account Name</th>
                                    <th class="px-2 py-3 font-semibold">Type</th>
                                    <th class="px-2 py-3 font-semibold text-right">Debit</th>
                                    <th class="px-5 py-3 font-semibold text-right">Credit</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div>
            </div>`;

    } catch (err) {
        console.error('Trial balance render failed:', err);
        area.innerHTML = `
            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-8 text-center text-rose-600">
                Data loaded, but the page failed to render it. Check the console for details.
            </div>`;
    }
}

document.getElementById('periodSelect').addEventListener('change', function () {
    loadTrialBalance(this.value);
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';