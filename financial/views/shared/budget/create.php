<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

$pageTitle = 'New Budget Line';

$accounts = $pdo->query("SELECT account_id, account_code, account_name, account_type FROM chartofaccounts WHERE is_active = 1 ORDER BY account_code")->fetchAll();
$periods  = $pdo->query("SELECT period_id, period_name FROM fiscalperiods ORDER BY start_date DESC")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/budget/budget-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Budget List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <form id="budgetForm" class="space-y-6">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-pie-chart-alt-2'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New Budget Line</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Account</label>
                    <select name="account_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name'] . ' (' . $a['account_type'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Fiscal Period</label>
                    <select name="period_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($periods as $p): ?>
                            <option value="<?= $p['period_id'] ?>"><?= htmlspecialchars($p['period_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Department <span class="normal-case font-normal text-textSoft">(optional)</span></label>
                    <input type="text" name="department"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Budget Amount</label>
                    <input type="number" step="0.01" min="0.01" name="budget_amount" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
            </div>

            <div class="flex items-start gap-2 bg-[#eef1f5] rounded-xl px-4 py-3">
                <i class='bx bx-info-circle text-orange text-lg shrink-0 mt-0.5'></i>
                <p class="text-xs text-textMid leading-relaxed">
                    Actual amount is calculated automatically from posted GL journal lines for this account and period — you don't enter it manually.
                </p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-check-double'></i>Save Budget Line
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

document.getElementById('budgetForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create');

    try {
        const res = await fetch(BASE_URL + '/controllers/BudgetController.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });

        const rawText = await res.text();
        let data;
        try {
            data = JSON.parse(rawText);
        } catch (parseErr) {
            throw new Error(`Response wasn't valid JSON. First 300 chars: ${rawText.slice(0, 300)}`);
        }

        if (data.success) {
            alert(data.message);
            window.location.href = BASE_URL + '/views/shared/budget/budget-list.php';
        } else {
            errorBox.textContent = data.message;
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        console.error('Budget create failed:', err);
        errorBox.textContent = 'Something went wrong. Please try again.';
        errorBox.classList.remove('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';