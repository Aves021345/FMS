<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel & Tours</title>

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
        <span class="section-title first:mt-0 mt-4 pt-6 pb-3 px-4 flex items-center gap-2 relative text-[0.7rem] font-bold text-white/65 uppercase tracking-[1.5px]
                     before:content-[''] before:w-2 before:h-2 before:bg-blue600 before:rounded-full before:shadow-[0_0_12px_#2563EB] before:animate-pulseDot
                     after:content-[''] after:flex-1 after:h-px after:ml-2 after:bg-gradient-to-r after:from-blue600/30 after:to-transparent">Reporting</span>
        <ul class="list-none flex flex-col gap-2">
            <li class="relative">
                <a href="#" data-page="reports" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-line-chart text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Reports &amp; Analytics</span><span class="text-[0.7rem] opacity-70 font-normal">View insights &amp; generate reports</span></span>
                    </span>
                </a>
            </li>
        </ul>

        <span class="section-title mt-4 pt-6 pb-3 px-4 flex items-center gap-2 relative text-[0.7rem] font-bold text-white/65 uppercase tracking-[1.5px]
                     before:content-[''] before:w-2 before:h-2 before:bg-blue600 before:rounded-full before:shadow-[0_0_12px_#2563EB] before:animate-pulseDot
                     after:content-[''] after:flex-1 after:h-px after:ml-2 after:bg-gradient-to-r after:from-blue600/30 after:to-transparent">Core Accounting</span>
        <ul class="list-none flex flex-col gap-2">
            <li class="relative">
                <a href="#" data-page="ledger" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-book-content text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">General Ledger</span><span class="text-[0.7rem] opacity-70 font-normal">Track journal entries &amp; balances</span></span>
                    </span>
                </a>
            </li>
        </ul>

        <span class="section-title mt-4 pt-6 pb-3 px-4 flex items-center gap-2 relative text-[0.7rem] font-bold text-white/65 uppercase tracking-[1.5px]
                     before:content-[''] before:w-2 before:h-2 before:bg-blue600 before:rounded-full before:shadow-[0_0_12px_#2563EB] before:animate-pulseDot
                     after:content-[''] after:flex-1 after:h-px after:ml-2 after:bg-gradient-to-r after:from-blue600/30 after:to-transparent">Financial Operations</span>
        <ul class="list-none flex flex-col gap-2">
            <li class="relative">
                <a href="#" data-page="ap" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-credit-card text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Accounts Payable (AP)</span><span class="text-[0.7rem] opacity-70 font-normal">Manage vendor bills &amp; payments</span></span>
                    </span>
                </a>
            </li>
            <li class="relative">
                <a href="#" data-page="ar" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-receipt text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Accounts Receivable (AR)</span><span class="text-[0.7rem] opacity-70 font-normal">Track invoices &amp; payments</span></span>
                    </span>
                </a>
            </li>
            <li class="relative">
                <a href="#" data-page="collections" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-archive-in text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Collection Management</span><span class="text-[0.7rem] opacity-70 font-normal">Follow up on overdue accounts</span></span>
                    </span>
                </a>
            </li>
            <li class="relative">
                <a href="#" data-page="disbursement" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-transfer-alt text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Disbursement Management</span><span class="text-[0.7rem] opacity-70 font-normal">Process outgoing fund transfers</span></span>
                    </span>
                </a>
            </li>
        </ul>

        <span class="section-title mt-4 pt-6 pb-3 px-4 flex items-center gap-2 relative text-[0.7rem] font-bold text-white/65 uppercase tracking-[1.5px]
                     before:content-[''] before:w-2 before:h-2 before:bg-blue600 before:rounded-full before:shadow-[0_0_12px_#2563EB] before:animate-pulseDot
                     after:content-[''] after:flex-1 after:h-px after:ml-2 after:bg-gradient-to-r after:from-blue600/30 after:to-transparent">Financial Control</span>
        <ul class="list-none flex flex-col gap-2">
            <li class="relative">
                <a href="#" data-page="cash" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-money text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Cash Management</span><span class="text-[0.7rem] opacity-70 font-normal">Monitor cash flow &amp; balances</span></span>
                    </span>
                </a>
            </li>
            <li class="relative">
                <a href="#" data-page="budget" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-pie-chart-alt-2 text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Budget Management</span><span class="text-[0.7rem] opacity-70 font-normal">Plan &amp; track departmental budgets</span></span>
                    </span>
                </a>
            </li>
            <li class="relative">
                <a href="#" data-page="tax" class="nav-link group flex items-center justify-between px-5 py-4 text-[#EFF6FF] no-underline rounded-xl transition-all duration-300 ease-in-out text-[0.9rem] font-medium relative overflow-hidden cursor-pointer border border-transparent
                          before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-blue600 before:to-blueLight before:scale-y-0 before:transition-transform before:duration-300 before:ease-in-out before:shadow-[0_0_15px_#2563EB]
                          hover:bg-white/[0.06] hover:text-white hover:translate-x-2 hover:border-white/10 hover:shadow-[0_6px_20px_rgba(0,0,0,0.25)] hover:before:scale-y-100">
                    <span class="flex items-center gap-2">
                        <div class="icon-wrapper w-9 h-9 bg-blue600/15 text-orange rounded-[10px] flex items-center justify-center transition-all duration-300 ease-in-out group-hover:bg-white/[0.12] group-hover:scale-[1.15] group-hover:rotate-[8deg] group-hover:shadow-[0_4px_12px_rgba(0,0,0,0.25)]"><i class='bx bx-calculator text-[1.3rem] transition-all duration-300 ease-in-out'></i></div>
                        <span class="flex flex-col gap-[0.15rem]"><span class="text-[0.9rem] font-medium">Tax Management</span><span class="text-[0.7rem] opacity-70 font-normal">Compute &amp; file tax obligations</span></span>
                    </span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<div id="pageWrapper" class="ml-[300px] transition-[margin] duration-300 ease-in-out flex flex-col min-h-screen">

    <!-- Topbar -->
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
                <div class="date font-semibold text-[.9rem]" id="topbarDate">Loading date</div>
                <span class="font-semibold text-[.9rem]">,&nbsp;</span>
                <div class="time font-semibold text-[.9rem]" id="topbarTime">--:--</div>
            </div>
            <button class="icon-btn relative w-10 h-10 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="notifBtn" title="Notifications">
                <i class='bx bx-bell'></i><span class="dot absolute top-[9px] right-[10px] w-2 h-2 bg-orangeDark rounded-full border-2 border-card"></span>
            </button>
            <div class="profile-menu-wrapper relative ml-1">
                <button class="icon-btn profile-btn w-10 h-10 min-w-10 p-0 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="profileBtn" title="Profile">
                    <i class='bx bx-user-circle'></i>
                </button>
                <div class="profile-menu absolute top-[calc(100%+0.5rem)] right-0 bg-white border border-line rounded-2xl shadow-menu min-w-[220px] overflow-hidden opacity-0 invisible -translate-y-2 transition-all duration-200 z-[10000]" id="profileMenu">
                    <div class="profile-menu-header flex items-center gap-3 px-4 pt-4 pb-3 border-b border-[rgba(14,27,60,.08)]">
                        <div class="profile-menu-avatar w-10 h-10 rounded-xl flex items-center justify-center bg-[#eaf1fb] text-textMid text-[1.25rem]"><i class='bx bx-user-circle'></i></div>
                        <div>
                            <div class="profile-menu-name font-bold text-textDark">Admin User</div>
                            <div class="profile-menu-role text-[.8rem] text-textSoft">Admin</div>
                        </div>
                    </div>
                    <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="profileViewBtn"><i class='bx bx-user text-[1.1rem]'></i>Profile</button>
                    <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="logoutBtn"><i class='bx bx-log-out text-[1.1rem]'></i>Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content flex-1" id="mainContent"></main>

</div>

<script>
    // ── Active-state utility classes (applied/removed via JS instead of a semantic "active" CSS class) ──
    const ACTIVE_LINK = ['bg-white/10', 'text-white', 'font-semibold', 'border-white/10',
                          'shadow-[0_4px_16px_rgba(0,0,0,0.25),inset_0_1px_0_rgba(255,255,255,0.1)]', 'before:scale-y-100'];
    const ACTIVE_ICON  = ['bg-white/[0.14]', 'shadow-[0_0_15px_rgba(0,0,0,0.2)]'];

    function setActivePage(page) {
        document.querySelectorAll('.nav-link').forEach(a => {
            a.classList.remove(...ACTIVE_LINK);
            a.querySelector('.icon-wrapper')?.classList.remove(...ACTIVE_ICON);
        });
        const target = document.querySelector(`.nav-link[data-page="${page}"]`);
        if (target) {
            target.classList.add(...ACTIVE_LINK);
            target.querySelector('.icon-wrapper')?.classList.add(...ACTIVE_ICON);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar     = document.getElementById('sidebar');
        const pageWrapper = document.getElementById('pageWrapper');
        const toggleBtn   = document.getElementById('toggleBtn');
        const overlay     = document.getElementById('overlay');

        setActivePage('ledger');

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

        // ── Sidebar layout (mirrors the old collapsed/show/expanded CSS states via Tailwind classes) ──
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

        // ── Nav link clicks (just set active state; no page rendering) ──
        document.querySelectorAll('.nav-link[data-page]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                setActivePage(page);
                if (window.innerWidth <= 900) hideSidebarMobile();
            });
        });

        window.addEventListener('resize', layout);

        // ── Notifications ─────────────────────────────────────
        document.getElementById('notifBtn').addEventListener('click', () => {
            alert('Notifications panel will open here.');
        });

        // ── Profile dropdown ──────────────────────────────────
        const profileBtn     = document.getElementById('profileBtn');
        const profileMenu    = document.getElementById('profileMenu');
        const profileViewBtn = document.getElementById('profileViewBtn');
        const logoutBtn      = document.getElementById('logoutBtn');
        const HIDDEN_MENU = ['opacity-0', 'invisible', '-translate-y-2'];
        const SHOWN_MENU  = ['opacity-100', 'visible', 'translate-y-0'];

        function toggleProfileMenu(show) {
            const isOpen = profileMenu.classList.contains('opacity-100');
            const open = show === undefined ? !isOpen : show;
            profileMenu.classList.remove(...(open ? HIDDEN_MENU : SHOWN_MENU));
            profileMenu.classList.add(...(open ? SHOWN_MENU : HIDDEN_MENU));
        }

        profileBtn.addEventListener('click', e => {
            e.stopPropagation();
            toggleProfileMenu();
        });
        profileViewBtn.addEventListener('click', () => {
            alert('Opening profile settings...');
            toggleProfileMenu(false);
        });
        logoutBtn.addEventListener('click', () => {
            alert('Logging out...');
            toggleProfileMenu(false);
        });
        document.addEventListener('click', e => {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                toggleProfileMenu(false);
            }
        });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') toggleProfileMenu(false); });
    });
</script>

</body>
</html>