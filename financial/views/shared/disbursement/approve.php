<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../../models/Disbursement.php';

requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

$pageTitle = 'Approve Disbursement';

$disbursementId = (int) ($_GET['disbursement_id'] ?? 0);
$disb = $disbursementId ? getDisbursementById($pdo, $disbursementId) : null;

$accounts = $pdo->query("SELECT account_id, account_code, account_name, account_type FROM chartofaccounts WHERE is_active = 1 ORDER BY account_code")->fetchAll();
$periods  = $pdo->query("SELECT period_id, period_name FROM fiscalperiods WHERE status = 'Open' ORDER BY start_date DESC")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/disbursement/disbursement-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Disbursement List
    </a>

    <?php if (!$disb): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i> Disbursement not found.
        </div>
    <?php elseif ($disb['status'] !== 'Pending'): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            This disbursement is not Pending (current status: <?= htmlspecialchars($disb['status']) ?>). It cannot be approved again.
        </div>
    <?php else: ?>

        <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-transfer-alt'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">Approve Disbursement</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">Supplier</div>
                    <div class="text-base font-bold text-textDark"><?= htmlspecialchars($disb['supplier_name']) ?></div>
                </div>
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">AP Invoice</div>
                    <div class="text-base font-bold text-textDark"><?= htmlspecialchars($disb['invoice_no']) ?></div>
                    <div class="text-xs text-textSoft mt-1">Balance: ₱ <?= number_format($disb['ap_balance'], 2) ?></div>
                </div>
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">Bank Account</div>
                    <div class="text-base font-bold text-textDark"><?= htmlspecialchars($disb['bank_account_name']) ?></div>
                </div>
                <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5">
                    <div class="text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1">Amount</div>
                    <div class="text-base font-bold text-textDark">₱ <?= number_format($disb['amount'], 2) ?></div>
                    <div class="text-xs text-textSoft mt-1"><?= htmlspecialchars($disb['payment_method']) ?></div>
                </div>
            </div>
        </div>

        <form id="approveForm" class="space-y-6">
            <input type="hidden" name="disbursement_id" value="<?= $disb['disbursement_id'] ?>">

            <div class="bg-card rounded-2xl border border-line shadow-brandSm p-5 space-y-5">
                <div class="flex items-center gap-2">
                    <i class='bx bx-git-branch text-orange text-lg'></i>
                    <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">GL Distribution</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
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
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Accounts Payable Account <span class="normal-case font-normal text-textSoft">(Debit — reduces the liability)</span></label>
                        <select name="ap_account_id" required
                                class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                            <option value="">-- Select --</option>
                            <?php foreach ($accounts as $a): ?>
                                <option value="<?= $a['account_id'] ?>"><?= htmlspecialchars($a['account_code'] . ' - ' . $a['account_name'] . ' (' . $a['account_type'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Bank/Cash Account <span class="normal-case font-normal text-textSoft">(Credit — money going out)</span></label>
                        <select name="bank_gl_account_id" required
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
                    <i class='bx bx-check-double'></i>Approve & Release Payment
                </button>
            </div>
        </form>

        <script>
            const BASE_URL = '<?= BASE_URL ?>';

            document.getElementById('approveForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                if (!confirm('This will post the GL entry and release the payment immediately. Continue?')) return;

                const errorBox = document.getElementById('errorBox');
                errorBox.textContent = '';
                errorBox.classList.add('hidden');

                const formData = new FormData(this);
                formData.append('action', 'approve');

                try {
                    const res = await fetch(BASE_URL + '/controllers/DisbursementController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await res.json();

                    if (data.success) {
                        alert(data.message + ' (Journal #' + data.journal_id + ')');
                        window.location.href = BASE_URL + '/views/shared/disbursement/disbursement-list.php';
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