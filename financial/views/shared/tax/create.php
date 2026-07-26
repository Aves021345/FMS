<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

$pageTitle = 'New Tax Record';

$taxTypes = $pdo->query("SELECT tax_type_id, tax_code, tax_name, tax_rate FROM taxtypes ORDER BY tax_code")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/tax/tax-list.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Tax List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <?php if (empty($taxTypes)): ?>
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl px-4 py-3 flex items-center gap-2">
            <i class='bx bx-error-circle text-lg'></i>
            No tax types found. Add one first (e.g. VAT 12%, Withholding Tax 2%).
        </div>
    <?php endif; ?>

    <form id="taxForm" class="space-y-6">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-calculator'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New Tax Record</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Tax Type</label>
                    <select name="tax_type_id" id="tax_type_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($taxTypes as $t): ?>
                            <option value="<?= $t['tax_type_id'] ?>" data-rate="<?= $t['tax_rate'] ?>">
                                <?= htmlspecialchars($t['tax_code'] . ' - ' . $t['tax_name'] . ' (' . number_format($t['tax_rate'], 2) . '%)') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Tax Period <span class="normal-case font-normal text-textSoft">(e.g. "2026-07" or "Q3 2026")</span></label>
                    <input type="text" name="tax_period" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Transaction Reference <span class="normal-case font-normal text-textSoft">(optional — AP/AR invoice no.)</span></label>
                    <input type="text" name="transaction_ref"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Taxable Amount</label>
                    <input type="number" step="0.01" min="0.01" name="taxable_amount" id="taxable_amount" required
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div class="flex flex-col justify-end">
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Computed Tax Amount</label>
                    <div class="w-full px-3 py-2.5 rounded-lg bg-[#eef1f5] text-sm font-bold text-textDark">
                        ₱ <span id="computedTax">0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-check-double'></i>Save Tax Record
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

function computeTax() {
    const select = document.getElementById('tax_type_id');
    const rate = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-rate')) || 0;
    const taxable = parseFloat(document.getElementById('taxable_amount').value) || 0;
    document.getElementById('computedTax').textContent = (taxable * rate / 100).toFixed(2);
}

document.getElementById('tax_type_id').addEventListener('change', computeTax);
document.getElementById('taxable_amount').addEventListener('input', computeTax);

document.getElementById('taxForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create');

    try {
        const res = await fetch(BASE_URL + '/controllers/TaxController.php', {
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
            alert(data.message + ' (Tax Record #' + data.tax_id + ')');
            window.location.href = BASE_URL + '/views/shared/tax/tax-list.php';
        } else {
            errorBox.textContent = data.message;
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        console.error('Tax record create failed:', err);
        errorBox.textContent = 'Something went wrong. Please try again.';
        errorBox.classList.remove('hidden');
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';