<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../../models/Report.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Accounts Payable Aging';

$report = getAPAging($pdo);

$buckets = [
    'current' => ['label' => 'Current', 'sub' => 'Not yet due',        'icon' => 'bx-check-circle',   'color' => 'emerald'],
    '1_30'    => ['label' => '1-30 Days', 'sub' => 'Overdue',          'icon' => 'bx-time-five',      'color' => 'blue'],
    '31_60'   => ['label' => '31-60 Days', 'sub' => 'Overdue',         'icon' => 'bx-time',            'color' => 'amber'],
    '61_90'   => ['label' => '61-90 Days', 'sub' => 'Overdue',         'icon' => 'bx-error',           'color' => 'orange'],
    'over_90' => ['label' => 'Over 90 Days', 'sub' => 'Overdue',       'icon' => 'bx-error-circle',    'color' => 'rose'],
];

$colorClasses = [
    'emerald' => 'bg-emerald-100 text-emerald-600',
    'blue'    => 'bg-blue-100 text-blue-600',
    'amber'   => 'bg-amber-100 text-amber-600',
    'orange'  => 'bg-orange-100 text-orange-600',
    'rose'    => 'bg-rose-100 text-rose-600',
];

ob_start();
?>

<div class="p-6 space-y-6">

    <a href="<?= BASE_URL ?>/views/shared/reports/reports.php"
       class="inline-flex items-center gap-2 text-sm font-semibold text-textMid hover:text-textDark no-underline">
        <i class='bx bx-arrow-back'></i> Back to Reports
    </a>

    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5 space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                <i class='bx bx-credit-card'></i>
            </div>
            <h1 class="text-xl font-bold text-textDark">Accounts Payable Aging</h1>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <?php foreach ($buckets as $key => $b): ?>
                <div class="bg-card rounded-xl border border-line shadow-brandSm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[.65rem] font-bold text-textMid uppercase tracking-wide"><?= $b['label'] ?></span>
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm shrink-0 <?= $colorClasses[$b['color']] ?>">
                            <i class='bx <?= $b['icon'] ?>'></i>
                        </div>
                    </div>
                    <div class="text-lg font-extrabold text-textDark">₱ <?= number_format($report['totals'][$key], 2) ?></div>
                    <div class="text-[11px] text-textSoft"><?= $b['sub'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ($buckets as $key => $b): ?>
        <?php if (!empty($report['buckets'][$key])): ?>
            <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-line">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full <?= $colorClasses[$b['color']] ?>"></span>
                        <h2 class="text-sm font-bold text-textDark"><?= htmlspecialchars($b['label']) ?> <?= $key === 'current' ? '(Not Yet Due)' : 'Overdue' ?></h2>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full <?= $colorClasses[$b['color']] ?>">
                        <?= count($report['buckets'][$key]) ?> invoice<?= count($report['buckets'][$key]) === 1 ? '' : 's' ?>
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-separate border-spacing-0">
                        <thead>
                            <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                                <th class="px-5 py-3 font-semibold whitespace-nowrap">Invoice No</th>
                                <th class="px-2 py-3 font-semibold">Supplier</th>
                                <th class="px-2 py-3 font-semibold whitespace-nowrap">Due Date</th>
                                <th class="px-2 py-3 font-semibold text-right whitespace-nowrap">Days Overdue</th>
                                <th class="px-5 py-3 font-semibold text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['buckets'][$key] as $row): ?>
                                <tr class="border-b border-line last:border-b-0 hover:bg-[#f8fafc]">
                                    <td class="px-5 py-3 font-semibold text-textDark whitespace-nowrap"><?= htmlspecialchars($row['invoice_no']) ?></td>
                                    <td class="px-2 py-3 text-textDark"><?= htmlspecialchars($row['supplier_name']) ?></td>
                                    <td class="px-2 py-3 text-textMid whitespace-nowrap"><?= htmlspecialchars($row['due_date']) ?></td>
                                    <td class="px-2 py-3 text-right text-textMid whitespace-nowrap"><?= (int) $row['days_overdue'] ?></td>
                                    <td class="px-5 py-3 text-right font-semibold text-textDark whitespace-nowrap">₱ <?= number_format($row['balance'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';