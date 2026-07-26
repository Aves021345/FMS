<?php
/**
 * Sidebar Partial — CRE8TED template version
 * Menu items are filtered by role using hasRole() from RoleMiddleware.php.
 * Expects RoleMiddleware.php to already be loaded by the calling page.
 */

// Each item: label, subtitle, icon (boxicons class), url, and which roles can see it.
$menuItems = [
    ['label' => 'Dashboard',            'subtitle' => 'Overview of your workspace',        'icon' => 'bx-grid-alt',        'url' => '/views/shared/dashboard.php',            'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']],
    ['label' => 'Financial Reports',  'subtitle' => 'View insights & generate reports',  'icon' => 'bx-line-chart',      'url' => '/views/shared/reports/reports.php',                'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']],
    ['label' => 'General Ledger',       'subtitle' => 'Track journal entries & balances',  'icon' => 'bx-book-content',    'url' => '/views/shared/ledger/journal.php',                 'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']],
    ['label' => 'Accounts Payable (AP)',        'subtitle' => 'Manage vendor bills & payments',      'icon' => 'bx-credit-card',    'url' => '/views/shared/ap/ap-list.php',                     'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']],
    ['label' => 'Accounts Receivable (AR)',     'subtitle' => 'Track invoices & payments',           'icon' => 'bx-receipt',        'url' => '/views/shared/ar/ar-list.php',                     'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']],
    ['label' => 'Collection Management',        'subtitle' => 'Follow up on overdue accounts',       'icon' => 'bx-archive-in',     'url' => '/views/shared/collection/collection-list.php',     'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']],
    ['label' => 'Disbursement Management',      'subtitle' => 'Process outgoing fund transfers',     'icon' => 'bx-transfer-alt',   'url' => '/views/shared/disbursement/disbursement-list.php', 'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']],
    ['label' => 'Cash Management',      'subtitle' => 'Monitor cash flow & balances',      'icon' => 'bx-money',           'url' => '/views/shared/cash/cash-list.php',                 'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']],
    ['label' => 'Budget Management',    'subtitle' => 'Plan & track departmental budgets', 'icon' => 'bx-pie-chart-alt-2', 'url' => '/views/shared/budget/budget-list.php',             'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_AUDITOR']],
    ['label' => 'Tax Management',       'subtitle' => 'Compute & file tax obligations',     'icon' => 'bx-calculator',      'url' => '/views/shared/tax/tax-list.php',                   'roles' => ['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT']],
    ['label' => 'Audit Log',            'subtitle' => 'Review system activity history',     'icon' => 'bx-history',         'url' => '/views/shared/audit/audit-log.php',                'roles' => ['ROLE_ADMIN', 'ROLE_AUDITOR']],
    ['label' => 'User Management',      'subtitle' => 'Manage accounts & permissions',      'icon' => 'bx-user',            'url' => '/views/shared/users/user-list.php',                'roles' => ['ROLE_ADMIN']],
];

// Group items into sections, matching the template's section-title pattern
$sections = [
    'Overview'             => ['Dashboard'],
    'Core Accounting'      => ['General Ledger', 'Chart of Accounts'],
    'Financial Operations' => ['Accounts Payable (AP)', 'Accounts Receivable (AR)', 'Collection Management', 'Disbursement Management'],
    'Financial Control'    => ['Financial Reports', 'Cash Management', 'Budget Management', 'Tax Management'],
    'Administration'       => ['Audit Log', 'User Management'],
];

$currentFile = basename($_SERVER['PHP_SELF'] ?? '');

// Utility classes applied when a nav link is the active page (mirrors the template's JS-driven ACTIVE_LINK/ACTIVE_ICON,
// but applied server-side here since this is a real multi-page app, not a client-side SPA)
$activeLinkClasses = 'bg-white/10 text-white font-semibold border-white/10 shadow-[0_4px_16px_rgba(0,0,0,0.25),inset_0_1px_0_rgba(255,255,255,0.1)] before:scale-y-100';
$activeIconClasses  = 'bg-white/[0.14] shadow-[0_0_15px_rgba(0,0,0,0.2)]';

function renderSidebarNavItem(array $item, string $currentFile, string $activeLinkClasses, string $activeIconClasses): void
{
    $isActive = basename($item['url']) === $currentFile;
    ?>
    <li class="relative">
        <a href="<?= BASE_URL . htmlspecialchars($item['url']) ?>"
           class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                  before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                  hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100
                  <?= $isActive ? $activeLinkClasses : '' ?>">
            <span class="flex items-center gap-2">
                <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)] <?= $isActive ? $activeIconClasses : '' ?>">
                    <i class='bx <?= htmlspecialchars($item['icon']) ?> text-[1.3rem] transition-all duration-300 ease-in-out'></i>
                </div>
                <span class="flex flex-col gap-[0.15rem]">
                    <span class="text-[0.9rem] font-medium"><?= htmlspecialchars($item['label']) ?></span>
                    <span class="text-[0.7rem] opacity-70 font-normal"><?= htmlspecialchars($item['subtitle']) ?></span>
                </span>
            </span>
        </a>
    </li>
    <?php
}
?>
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-[300px] translate-x-0 bg-gradient-to-b from-[#0c1f3f] to-[#0f2a52] border-r border-blue500/25 overflow-y-auto overflow-x-hidden z-[1000] transition-transform duration-300 ease-in-out shadow-[4px_0_20px_rgba(0,0,0,0.4)] [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:bg-black/15 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:bg-white/15 [&::-webkit-scrollbar-thumb]:rounded-full">

    <!-- Sidebar header / logo -->
    <div class="flex items-center justify-center px-[1.4rem] pt-[2.8rem] pb-[1.4rem]">
        <svg viewBox="0 -45 320 205" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" class="w-full h-auto max-w-[232px]">
            <defs>
                <linearGradient id="eightGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#f5a524"/>
                    <stop offset="100%" stop-color="#e0930f"/>
                </linearGradient>
            </defs>
            <text x="-2" y="90" style="font-family:'Pinyon Script',cursive; font-size:135px; fill:white;">C</text>
            <text x="186" y="66" text-anchor="middle" style="font-family:'Fraunces',serif; font-weight:700; font-size:38px; letter-spacing:1px;"><tspan fill="white">RE</tspan><tspan fill="url(#eightGrad)">8</tspan><tspan fill="white">TED</tspan></text>
            <text x="186" y="83" text-anchor="middle" style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:600; font-size:10px; fill:rgba(255,255,255,0.45); letter-spacing:4px;">TRAVEL &amp; TOURS</text>
        </svg>
    </div>
    <div class="h-px bg-white/10 mb-4"></div>

    <nav class="pt-4 px-4 pb-6">
        <?php foreach ($sections as $sectionTitle => $labels): ?>
            <?php
            $visibleItems = array_filter($menuItems, fn($item) => in_array($item['label'], $labels, true) && hasRole($item['roles']));
            if (empty($visibleItems)) continue;
            ?>
            <span class="section-title first:mt-0 mt-4 pt-6 pb-3 px-4 flex items-center gap-2 relative text-[0.7rem] font-bold text-white/65 uppercase tracking-[1.5px]
                         before:content-[''] before:w-2 before:h-2 before:bg-blue600 before:rounded-full before:shadow-[0_0_12px_#2563EB] before:animate-pulseDot
                         after:content-[''] after:flex-1 after:h-px after:ml-2 after:bg-gradient-to-r after:from-blue600/30 after:to-transparent"><?= htmlspecialchars($sectionTitle) ?></span>
            <ul class="list-none flex flex-col gap-2">
                <?php foreach ($visibleItems as $item): ?>
                    <?php renderSidebarNavItem($item, $currentFile, $activeLinkClasses, $activeIconClasses); ?>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </nav>
</aside>

<script>
    // ── Preserve sidebar scroll position across full page navigations ──
    // Each nav click causes a real page load (this isn't a client-side SPA),
    // which normally resets #sidebar's scrollTop to 0. We stash the scroll
    // position in sessionStorage right before navigating, and restore it as
    // soon as the new page's sidebar mounts, so an item near the bottom
    // stays in view instead of snapping back to the top.
    (function () {
        const SCROLL_KEY = 'sidebarScrollPos';
        const sidebarEl = document.getElementById('sidebar');
        if (!sidebarEl) return;

        // Restore as early as possible (before paint) to avoid a visible jump.
        const savedScroll = sessionStorage.getItem(SCROLL_KEY);
        if (savedScroll !== null) {
            sidebarEl.scrollTop = parseInt(savedScroll, 10) || 0;
        }

        // Keep the saved value fresh while the user scrolls manually.
        let scrollSaveTimeout = null;
        sidebarEl.addEventListener('scroll', function () {
            clearTimeout(scrollSaveTimeout);
            scrollSaveTimeout = setTimeout(function () {
                sessionStorage.setItem(SCROLL_KEY, sidebarEl.scrollTop);
            }, 50);
        });

        // Save immediately on click too, so we don't lose the position if the
        // browser navigates away before the debounced scroll handler fires.
        sidebarEl.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                sessionStorage.setItem(SCROLL_KEY, sidebarEl.scrollTop);
            });
        });
    })();
</script>