<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';

requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

$pageTitle = 'Financial Reports';

$reportLinks = [
    [
        'href'  => BASE_URL . '/views/shared/reports/trial-balance.php',
        'icon'  => 'bx-balance',
        'title' => 'Trial Balance',
        'desc'  => 'Verify debits equal credits for a fiscal period',
    ],
    [
        'href'  => BASE_URL . '/views/shared/reports/ap-aging.php',
        'icon'  => 'bx-credit-card',
        'title' => 'Accounts Payable Aging',
        'desc'  => 'See what you owe suppliers, grouped by how overdue',
    ],
    [
        'href'  => BASE_URL . '/views/shared/reports/ar-aging.php',
        'icon'  => 'bx-receipt',
        'title' => 'Accounts Receivable Aging',
        'desc'  => 'See what customers owe you, grouped by how overdue',
    ],
];

ob_start();
?>

<div class="p-6 space-y-6">

    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                <i class='bx bx-line-chart'></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-textDark">Financial Reports</h1>
                <p class="text-xs text-textSoft mt-0.5">Select a report to view</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <?php foreach ($reportLinks as $r): ?>
            <a href="<?= $r['href'] ?>"
               class="group bg-card rounded-2xl border border-line shadow-brandSm p-5 no-underline hover:border-orange/50 hover:shadow-brandMd transition-all duration-200 flex flex-col gap-4">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0 group-hover:scale-110 transition-transform duration-200">
                    <i class='bx <?= $r['icon'] ?>'></i>
                </div>
                <div>
                    <div class="text-base font-bold text-textDark"><?= htmlspecialchars($r['title']) ?></div>
                    <div class="text-sm text-textSoft mt-1"><?= htmlspecialchars($r['desc']) ?></div>
                </div>
                <div class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-orange">
                    View Report <i class='bx bx-right-arrow-alt'></i>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';