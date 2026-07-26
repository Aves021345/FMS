<?php
/**
 * Accounts Payable - Create Invoice
 * Accountant/Admin only. Creates the AP record AND its matching draft GL journal.
 */

require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

$pageTitle = 'New AP Invoice';

$suppliers = $pdo->query("SELECT supplier_id, supplier_code, supplier_name FROM suppliers WHERE status = 'Active' ORDER BY supplier_name")->fetchAll();
$accounts  = $pdo->query("SELECT account_id, account_code, account_name, account_type FROM chartofaccounts WHERE is_active = 1 ORDER BY account_code")->fetchAll();
$periods   = $pdo->query("SELECT period_id, period_name FROM fiscalperiods WHERE status = 'Open' ORDER BY start_date DESC")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/ap/ap-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to AP List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <?php if (empty($suppliers)): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            No active suppliers found. Add a supplier first before creating an AP invoice.
        </div>
    <?php endif; ?>

    <form id="apForm" class="space-y-6">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-credit-card'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New AP Invoice</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Supplier</label>
                    <select name="supplier_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['supplier_id'] ?>"><?= htmlspecialchars($s['supplier_code'] . ' - ' . $s['supplier_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Invoice No</label>
                    <input type="text" name="invoice_no" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Amount</label>
                    <input type="number" step="0.01" min="0.01" name="amount" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Invoice Date</label>
                    <input type="date" name="invoice_date" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Due Date</label>
                    <input type="date" name="due_date" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
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

                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Description</label>
                    <input type="text" name="description"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
            </div>
        </div>

        <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5 space-y-5">
            <div class="flex items-center gap-2">
                <i class='bx bx-git-branch text-orange text-lg'></i>
                <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">GL Distribution</h2>
                <span class="text-xs text-textSoft font-normal normal-case">— generates the matching draft journal entry</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Expense/Asset Account (Debit)</label>
                    <select name="expense_account_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name'] . ' (' . $a['account_type'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Accounts Payable Account (Credit)</label>
                    <select name="ap_account_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name'] . ' (' . $a['account_type'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-save'></i>Save Invoice
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

document.getElementById('apForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create');

    try {
        const res = await fetch(BASE_URL + '/controllers/APController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            alert(data.message + ' (AP #' + data.ap_id + ', Journal #' + data.journal_id + ')');
            window.location.href = BASE_URL + '/views/shared/ap/ap-list.php';
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