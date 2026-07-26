<?php
/**
 * General Ledger - Create Journal Entry (Draft)
 * Accountant/Admin only.
 */

require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

$pageTitle = 'New Journal Entry';

// Fetch accounts and periods for the dropdowns
$accounts = $pdo->query("SELECT account_id, account_code, account_name FROM chartofaccounts WHERE is_active = 1 ORDER BY account_code")->fetchAll();
$periods  = $pdo->query("SELECT period_id, period_name FROM fiscalperiods WHERE status = 'Open' ORDER BY start_date DESC")->fetchAll();
$modules  = $pdo->query("SELECT module_id, module_name FROM sourcemodules ORDER BY module_name")->fetchAll();

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/ledger/journal.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Journal List
    </a>

    <div id="errorBox" class="hidden bg-rose-50 border border-rose-200 text-rose-600 text-sm font-medium rounded-xl px-4 py-3"></div>

    <form id="journalForm" class="space-y-6">

        <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-book-content'></i>
                </div>
                <h1 class="text-xl font-bold text-textDark">New Journal Entry</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Fiscal Period</label>
                    <select name="period_id" id="period_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($periods as $p): ?>
                            <option value="<?= $p['period_id'] ?>"><?= htmlspecialchars($p['period_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Source Module</label>
                    <select name="source_module_id" id="source_module_id" required
                            class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                        <option value="">-- Select --</option>
                        <?php foreach ($modules as $m): ?>
                            <option value="<?= $m['module_id'] ?>"><?= htmlspecialchars($m['module_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Reference No <span class="normal-case font-normal text-textSoft">(optional)</span></label>
                    <input type="text" name="reference_no" id="reference_no"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>

                <div>
                    <label class="block text-[.7rem] font-bold text-textMid uppercase tracking-wide mb-1.5">Description</label>
                    <input type="text" name="description" id="description"
                           class="w-full px-3 py-2.5 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                </div>
            </div>
        </div>

        <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-line">
                <h2 class="text-sm font-bold text-textDark uppercase tracking-wide">Journal Lines</h2>
                <button type="button" onclick="addLine()"
                        class="flex items-center gap-2 px-4 py-2 rounded-full bg-[#eef1f5] text-textMid text-sm font-semibold hover:bg-[#e4e8ee] transition-colors">
                    <i class='bx bx-plus text-orange'></i>Add Line
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border-separate border-spacing-0" id="linesTable">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                            <th class="px-5 py-3 font-semibold">Account</th>
                            <th class="px-2 py-3 font-semibold text-right">Debit</th>
                            <th class="px-2 py-3 font-semibold text-right">Credit</th>
                            <th class="px-2 py-3 font-semibold">Line Description</th>
                            <th class="px-5 py-3 font-semibold text-right">Remove</th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <!-- rows added by JS -->
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-6 px-5 py-4 border-t border-line bg-[#f8fafc]">
                <div class="text-sm">
                    <span class="text-textSoft">Total Debit:</span>
                    <span id="totalDebit" class="font-bold text-textDark ml-1">₱ 0.00</span>
                </div>
                <div class="text-sm">
                    <span class="text-textSoft">Total Credit:</span>
                    <span id="totalCredit" class="font-bold text-textDark ml-1">₱ 0.00</span>
                </div>
                <div class="text-sm">
                    <span class="text-textSoft">Balance:</span>
                    <span id="balanceDiff" class="font-bold ml-1">₱ 0.00</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange text-navy900 text-sm font-bold hover:bg-orangeDark transition-colors">
                <i class='bx bx-save'></i>Save as Draft
            </button>
        </div>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

const accountOptions = <?= json_encode(array_map(fn($a) => [
    'id' => $a['account_id'],
    'label' => $a['account_code'] . ' - ' . $a['account_name']
], $accounts)) ?>;

let lineCount = 0;

const inputClass = 'w-full px-2.5 py-2 rounded-lg border border-line text-sm text-textDark bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20';
const selectClass = inputClass;

function addLine() {
    const tbody = document.getElementById('linesBody');
    const row = document.createElement('tr');
    row.className = 'border-b border-line hover:bg-[#f8fafc]';
    const idx = lineCount++;

    let accountSelect = `<select name="lines[${idx}][account_id]" required class="${selectClass}">
        <option value="">-- Select --</option>`;
    accountOptions.forEach(a => {
        accountSelect += `<option value="${a.id}">${a.label}</option>`;
    });
    accountSelect += `</select>`;

    row.innerHTML = `
        <td class="px-5 py-3 align-top min-w-[220px]">${accountSelect}</td>
        <td class="px-2 py-3 align-top w-32">
            <input type="number" step="0.01" min="0" name="lines[${idx}][debit]" value="0" onchange="calculateTotals()" class="${inputClass} text-right">
        </td>
        <td class="px-2 py-3 align-top w-32">
            <input type="number" step="0.01" min="0" name="lines[${idx}][credit]" value="0" onchange="calculateTotals()" class="${inputClass} text-right">
        </td>
        <td class="px-2 py-3 align-top min-w-[180px]">
            <input type="text" name="lines[${idx}][description]" class="${inputClass}">
        </td>
        <td class="px-5 py-3 align-top text-right">
            <button type="button" onclick="this.closest('tr').remove(); calculateTotals();"
                    class="w-8 h-8 rounded-lg hover:bg-[#eef1f6] inline-flex items-center justify-center text-rose-600">
                <i class='bx bx-trash'></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function currency(val) {
    return '₱ ' + val.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function calculateTotals() {
    let debitTotal = 0;
    let creditTotal = 0;

    document.querySelectorAll('[name^="lines"][name$="[debit]"]').forEach(input => {
        debitTotal += parseFloat(input.value) || 0;
    });
    document.querySelectorAll('[name^="lines"][name$="[credit]"]').forEach(input => {
        creditTotal += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebit').textContent = currency(debitTotal);
    document.getElementById('totalCredit').textContent = currency(creditTotal);

    const diff = debitTotal - creditTotal;
    const balanceEl = document.getElementById('balanceDiff');
    balanceEl.textContent = currency(Math.abs(diff));
    balanceEl.classList.toggle('text-emerald-600', diff === 0);
    balanceEl.classList.toggle('text-rose-600', diff !== 0);
}

// Start with 2 blank lines (minimum for a balanced entry)
addLine();
addLine();

document.getElementById('journalForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = '';
    errorBox.classList.add('hidden');

    const formData = new FormData(this);
    formData.append('action', 'create_draft');

    try {
        const res = await fetch(BASE_URL + '/controllers/JournalController.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            alert(data.message + ' (Journal #' + data.journal_id + ')');
            window.location.href = BASE_URL + '/views/shared/ledger/journal.php';
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