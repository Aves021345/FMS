<?php
/**
 * Topbar Partial — CRE8TED template version
 * Expects: session already started, user logged in (enforced by AuthMiddleware).
 */

$roleLabels = [
    'ROLE_ADMIN'      => 'Admin',
    'ROLE_FINANCE'    => 'Finance',
    'ROLE_ACCOUNTANT' => 'Accountant',
    'ROLE_AUDITOR'    => 'Auditor',
];
$roleDisplay = $roleLabels[$_SESSION['role'] ?? ''] ?? ($_SESSION['role'] ?? '');
?>
<nav id="topbar" class="sticky top-0 flex items-center justify-between px-5 z-[900] transition-all duration-300 h-[65px] bg-white border-b border-blue500/[0.14]">
    <div class="topbar-left flex items-center gap-4">
        <button class="menu-toggle w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-textMid cursor-pointer mr-1 transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="toggleBtn" title="Toggle sidebar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="5" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="5" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    <div class="topbar-right flex items-center gap-0">
        <div class="topbar-time flex items-center gap-0 text-[.95rem] text-textDark mr-[9px]">
            <div class="date font-light text-[.9rem]" id="topbarDate">Loading date</div>
            <span class="font-light text-[.9rem]">,&nbsp;</span>
            <div class="time font-light text-[.9rem]" id="topbarTime">--:--</div>
        </div>
        <button class="icon-btn relative w-10 h-10 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="notifBtn" title="Notifications">
            <i class='bx bx-bell'></i><span class="dot absolute top-[9px] right-[10px] w-2 h-2 bg-red-500 rounded-full border-2 border-card shadow-[0_0_8px_2px_rgba(239,68,68,0.7)] animate-pulseDot"></span>
        </button>
        <div class="profile-menu-wrapper relative ml-1">
            <button class="icon-btn profile-btn w-10 h-10 min-w-10 p-0 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="profileBtn" title="Profile">
                <i class='bx bx-user-circle'></i>
            </button>
            <div class="profile-menu absolute top-[calc(100%+0.5rem)] right-0 bg-white border border-line rounded-2xl shadow-menu min-w-[220px] overflow-hidden opacity-0 invisible -translate-y-2 transition-all duration-200 z-[10000]" id="profileMenu">
                <div class="profile-menu-header flex items-center gap-3 px-4 pt-4 pb-3 border-b border-[rgba(14,27,60,.08)]">
                    <div class="profile-menu-avatar w-10 h-10 rounded-xl flex items-center justify-center bg-[#eaf1fb] text-textMid text-[1.25rem]"><i class='bx bx-user-circle'></i></div>
                    <div>
                        <div class="profile-menu-name font-bold text-textDark"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></div>
                        <div class="profile-menu-role text-[.8rem] text-textSoft"><?= htmlspecialchars($roleDisplay) ?></div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)] no-underline">
                    <i class='bx bx-log-out text-[1.1rem]'></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>