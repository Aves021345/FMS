<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../../models/Collection.php';
require_once __DIR__ . '/../../../models/AccountsReceivable.php';

requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

$pageTitle = 'Apply Collection to AR Invoices';

$collectionId = (int) ($_GET['collection_id'] ?? 0);
$collection = $collectionId ? getCollectionById($pdo, $collectionId) : null;

$openInvoices = $collection ? getOpenARByCustomer($pdo, $collection['customer_id']) : [];

$accounts     = $pdo->query("SELECT account_id, account_code, account_name, account_type FROM chartofaccounts WHERE is_active = 1 ORDER BY account_code")->fetchAll();
$periods      = $pdo->query("SELECT period_id, period_name FROM fiscalperiods WHERE status = 'Open' ORDER BY start_date DESC")->fetchAll();
$bankAccounts = $pdo->query("SELECT bank_account_id, account_name, bank_name, current_balance FROM bankaccounts WHERE status = 'Active' ORDER BY account_name")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/collection/collection-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Collection List
    </a>

    <?php if (!$collection): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i> Collection not found.
        </div>
    <?php elseif ($collection['status'] !== 'Pending'): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            This collection is not Pending (current status: <?= htmlspecialchars($collection['status']) ?>).
        </div>
    <?php elseif (empty($openInvoices)): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            This customer has no open AR invoices to apply the payment to.
        </div>
    <?php else: ?>

        <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-archive-in'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Apply Collection</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">Customer</div>
                    <div class="text-lg font-bold text-textDark"><?= htmlspecialchars($collection['customer_name']) ?></div>
                </div>
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">Amount to Allocate</div>
                    <div class="text-lg font-bold text-textDark" id="collectionAmount">₱ <?= number_format($collection['amount'], 2) ?></div>
                </div>
            </div>
        </div>

        <form id="applyForm" class="space-y-6">
            <input type="hidden" name="collection_id" value="<?= $collection['collection_id'] ?>">

            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5 space-y-5">
                <div class="flex items-center gap-2">
                    <i class='bx bx-git-branch text-orange text-lg'></i>
                    <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">Deposit & GL Distribution</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Deposit To (Bank Account)</label>
                        <select name="bank_account_id" required
                                class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                            <option value="">-- Select --</option>
                            <?php foreach ($bankAccounts as $ba): ?>
                                <option value="<?= $ba['bank_account_id'] ?>"><?= htmlspecialchars($ba['account_name'] . ' - ' . $ba['bank_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Bank/Cash GL Account (Debit)</label>
                        <select name="bank_gl_account_id" required
                                class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                            <option value="">-- Select --</option>
                            <?php foreach ($accounts as $a): ?>
                                <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Accounts Receivable Account (Credit)</label>
                        <select name="ar_account_id" required
                                class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                            <option value="">-- Select --</option>
                            <?php foreach ($accounts as $a): ?>
                                <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
                <div class="px-5 py-4 border-b border-line">
                    <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">Allocate to Open Invoices</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-separate border-spacing-0">
                        <thead>
                            <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                                <th class="px-5 py-3 font-semibold">Invoice No.</th>
                                <th class="px-2 py-3 font-semibold text-right">Balance</th>
                                <th class="px-5 py-3 font-semibold text-right">Amount to Apply</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($openInvoices as $i => $inv): ?>
                                <tr class="border-b border-line hover:bg-[#f8fafc]">
                                    <td class="px-5 py-3 align-top font-semibold text-textDark whitespace-nowrap">
                                        <?= htmlspecialchars($inv['invoice_no']) ?>
                                        <input type="hidden" name="allocations[<?= $i ?>][ar_id]" value="<?= $inv['ar_id'] ?>">
                                    </td>
                                    <td class="px-2 py-3 align-top text-right text-textMid whitespace-nowrap">₱ <?= number_format($inv['balance'], 2) ?></td>
                                    <td class="px-5 py-3 align-top text-right">
                                        <input type="number" step="0.01" min="0" max="<?= $inv['balance'] ?>"
                                               name="allocations[<?= $i ?>][amount]" value="0"
                                               class="allocInput w-32 px-2.5 py-2 rounded-lg border border-line text-sm text-textDark text-right bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-line bg-[#f8fafc] text-sm">
                    <span class="text-textSoft">Total Allocated:</span>
                    <span id="totalAllocated" class="font-bold text-textDark">₱ 0.00</span>
                    <span class="text-textSoft">/</span>
                    <span id="targetAmount" class="font-bold text-textDark">₱ <?= number_format($collection['amount'], 2) ?></span>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                    <i class='bx bx-check-double'></i>Apply Collection
                </button>
            </div>
        </form>

        <script>
            const BASE_URL = '<?= BASE_URL ?>';
            const collectionAmount = <?= (float) $collection['amount'] ?>;

            function currency(val) {
                return '₱ ' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function recalcTotal() {
                let total = 0;
                document.querySelectorAll('.allocInput').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                const totalEl = document.getElementById('totalAllocated');
                totalEl.textContent = currency(total);
                totalEl.classList.toggle('text-emerald-600', Math.abs(total - collectionAmount) < 0.01);
                totalEl.classList.toggle('text-rose-600', Math.abs(total - collectionAmount) >= 0.01);
            }
            document.querySelectorAll('.allocInput').forEach(input => {
                input.addEventListener('input', recalcTotal);
            });
            recalcTotal();

            document.getElementById('applyForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const errorBox = document.getElementById('errorBox');
                errorBox.textContent = '';
                errorBox.classList.add('hidden');

                let total = 0;
                document.querySelectorAll('.allocInput').forEach(input => {
                    total += parseFloat(input.value) || 0;
                });

                if (Math.abs(total - collectionAmount) > 0.01) {
                    errorBox.textContent = 'Total allocated (' + total.toFixed(2) + ') must equal the collection amount (' + collectionAmount.toFixed(2) + ').';
                    errorBox.classList.remove('hidden');
                    return;
                }

                if (!confirm('This will post the GL entry and apply the payment immediately. Continue?')) return;

                const formData = new FormData(this);
                formData.append('action', 'apply');

                try {
                    const res = await fetch(BASE_URL + '/controllers/CollectionController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if (data.success) {
                        alert(data.message + ' (Journal #' + data.journal_id + ')');
                        window.location.href = BASE_URL + '/views/shared/collection/collection-list.php';
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

    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';