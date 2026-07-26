<?php
/**
 * Main Layout — CRE8TED template version
 * Usage from any protected page:
 *
 *   require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
 *   require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
 *   requireRole(['ROLE_ADMIN', 'ROLE_ACCOUNTANT']);
 *
 *   $pageTitle = 'Dashboard';
 *   ob_start();
 *   ?>
 *   <p>Your page content goes directly here...</p>
 *   <?php
 *   $content = ob_get_clean();
 *   require __DIR__ . '/../layouts/main.php';
 *
 * The including page must set $content (a string of HTML) before requiring this layout.
 */

require_once __DIR__ . '/../../middleware/AuthMiddleware.php'; // enforce login on every page using this layout

$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Travel &amp; Tours</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,900&family=Pinyon+Script&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              navy900: '#0c1f3f',
              navy800: '#0f2a52',
              orange: '#f5a524',
              orangeDark: '#e0930f',
              card: '#ffffff',
              textDark: '#0f2242',
              textMid: '#5b6b85',
              textSoft: '#94a3bb',
              line: '#e7ecf3',
              navy: '#0f246c',
              navyDark: '#0a1a50',
              blue500: '#3B82F6',
              blue600: '#2563EB',
              blue700: '#1E40AF',
              blueLight: '#93C5FD',
              bgLight: '#F0F4FF',
              text900: '#0F1E4A',
              text600: '#4B5E8A',
              text400: '#8EA0C4',
            },
            fontFamily: {
              display: ['Plus Jakarta Sans', 'sans-serif'],
              sans: ['DM Sans', 'sans-serif'],
            },
            boxShadow: {
              menu: '0 12px 32px rgba(15,42,82,.12)',
              brandSm: '0 2px 8px rgba(15,36,108,0.08)',
              brandMd: '0 6px 20px rgba(15,36,108,0.12)',
            },
            keyframes: {
              pulseDot: {
                '0%, 100%': { opacity: 1, transform: 'scale(1)' },
                '50%': { opacity: 0.6, transform: 'scale(0.95)' },
              },
            },
            animation: {
              pulseDot: 'pulseDot 2s infinite',
            },
          }
        }
      }
    </script>
</head>
<body class="font-sans bg-bgLight text-text900 antialiased overflow-x-hidden">

<div id="overlay" class="fixed inset-0 bg-black/50 z-[998] opacity-0 invisible transition-opacity duration-300"></div>

<?php require __DIR__ . '/../partials/sidebar.php'; ?>

<div id="pageWrapper" class="ml-[300px] transition-[margin] duration-300 ease-in-out flex flex-col min-h-screen">

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <main class="main-content flex-1 p-6" id="mainContent">
        <?= $content ?? '<p>No content set.</p>' ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar     = document.getElementById('sidebar');
    const pageWrapper = document.getElementById('pageWrapper');
    const toggleBtn   = document.getElementById('toggleBtn');
    const overlay     = document.getElementById('overlay');

    // ── Topbar time display ───────────────────────────────
    function updateTopbarTime() {
        const now = new Date();
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true, timeZone: 'Asia/Manila' };
        const dateOptions = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', timeZone: 'Asia/Manila' };
        document.getElementById('topbarTime').textContent = now.toLocaleTimeString(undefined, timeOptions);
        document.getElementById('topbarDate').textContent = now.toLocaleDateString(undefined, dateOptions);
    }
    updateTopbarTime();
    setInterval(updateTopbarTime, 1000);

    // ── Sidebar layout (fixed sidebar + margin-shifted content, matches template) ──
    function layout() {
        if (window.innerWidth <= 900) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            pageWrapper.classList.remove('ml-[300px]');
            pageWrapper.classList.add('ml-0');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'invisible');
        } else {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            pageWrapper.classList.remove('ml-0');
            pageWrapper.classList.add('ml-[300px]');
            overlay.classList.add('opacity-0', 'invisible');
            overlay.classList.remove('opacity-100');
        }
    }
    layout();

    function showSidebarMobile() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('opacity-0', 'invisible');
        overlay.classList.add('opacity-100');
    }
    function hideSidebarMobile() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('opacity-0', 'invisible');
        overlay.classList.remove('opacity-100');
    }

    toggleBtn.addEventListener('click', e => {
        e.stopPropagation();
        if (window.innerWidth <= 900) {
            sidebar.classList.contains('-translate-x-full') ? showSidebarMobile() : hideSidebarMobile();
        } else {
            const hidden = sidebar.classList.contains('-translate-x-full');
            if (hidden) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                pageWrapper.classList.remove('ml-0');
                pageWrapper.classList.add('ml-[300px]');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                pageWrapper.classList.add('ml-0');
                pageWrapper.classList.remove('ml-[300px]');
            }
        }
    });

    overlay.addEventListener('click', hideSidebarMobile);
    window.addEventListener('resize', layout);

    // ── Notifications (placeholder) ───────────────────────
    const notifBtn = document.getElementById('notifBtn');
    if (notifBtn) {
        notifBtn.addEventListener('click', () => {
            alert('Notifications panel will open here.');
        });
    }

    // ── Profile dropdown ──────────────────────────────────
    const profileBtn  = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');
    const HIDDEN_MENU = ['opacity-0', 'invisible', '-translate-y-2'];
    const SHOWN_MENU  = ['opacity-100', 'visible', 'translate-y-0'];

    function toggleProfileMenu(show) {
        const isOpen = profileMenu.classList.contains('opacity-100');
        const open = show === undefined ? !isOpen : show;
        profileMenu.classList.remove(...(open ? HIDDEN_MENU : SHOWN_MENU));
        profileMenu.classList.add(...(open ? SHOWN_MENU : HIDDEN_MENU));
    }

    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', e => {
            e.stopPropagation();
            toggleProfileMenu();
        });
        document.addEventListener('click', e => {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                toggleProfileMenu(false);
            }
        });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') toggleProfileMenu(false); });
    }
});
</script>

</body>
</html>