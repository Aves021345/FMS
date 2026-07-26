<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

$pageTitle = 'New Disbursement Request';

// Only show AP invoices that still have a balance to pay
$openInvoices = $pdo->query(
    "SELECT ap.ap_id, ap.invoice_no, ap.balance, s.supplier_name 
     FROM accountspayable ap
     JOIN suppliers s ON s.supplier_id = ap.supplier_id
     WHERE ap.status IN ('Open', 'Partially Paid')
     ORDER BY ap.due_date ASC"
)->fetchAll();

$bankAccounts = $pdo->query(
    "SELECT bank_account_id, account_name, bank_name, current_balance FROM bankaccounts WHERE status = 'Active' ORDER BY account_name"
)->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/disbursement/disbursement-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Disbursement List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <?php if (empty($openInvoices)): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            No open AP invoices found. Create an AP invoice first.
        </div>
    <?php endif; ?>

    <form id="disbForm" class="space-y-6">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-transfer-alt'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New Disbursement Request</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">AP Invoice <span class="normal-case font-normal text-textSoft">(unpaid balance shown)</span></label>
                    <select name="ap_id" id="ap_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($openInvoices as $ap): ?>
                            <option value="<?= $ap['ap_id'] ?>" data-balance="<?= $ap['balance'] ?>">
                                <?= htmlspecialchars($ap['invoice_no'] . ' - ' . $ap['supplier_name'] . ' (Balance: ' . number_format($ap['balance'], 2) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Bank Account</label>
                    <select name="bank_account_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($bankAccounts as $ba): ?>
                            <option value="<?= $ba['bank_account_id'] ?>">
                                <?= htmlspecialchars($ba['account_name'] . ' - ' . $ba['bank_name'] . ' (Balance: ' . number_format($ba['current_balance'], 2) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Amount to Pay</label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Disbursement Date</label>
                    <input type="date" name="disbursement_date" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Payment Method</label>
                    <select name="payment_method" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Check">Check</option>
                        <option value="Cash">Cash</option>
                        <option value="Online">Online</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Reference No <span class="normal-case font-normal text-textSoft">(optional)</span></label>
                    <input type="text" name="reference_no"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-send'></i>Submit Request
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// Auto-fill amount with the full balance when an invoice is selected (editable for partial payments)
document.getElementById('ap_id').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const balance = selected.getAttribute('data-balance');
    if (balance) {
        document.getElementById('amount').value = balance;
    }
});

document.getElementById('disbForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create');

    try {
        const res = await fetch(BASE_URL + '/controllers/DisbursementController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            alert(data.message + ' (Disbursement #' + data.disbursement_id + ')');
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

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';