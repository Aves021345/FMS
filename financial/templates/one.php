<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRE8TED — Admin Portal</title>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,900&family=Pinyon+Script&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          navy900: '#0c1f3f',
          navy800: '#0f2a52',
          navy700: '#173868',
          orange: '#f5a524',
          orangeDark: '#e0930f',
          bg: '#eef2f7',
          card: '#ffffff',
          textDark: '#0f2242',
          textMid: '#5b6b85',
          textSoft: '#94a3bb',
          line: '#e7ecf3',
          greenBg: '#e3f8ec',
          greenTx: '#1a9b58',
          amberBg: '#fdf1dd',
          amberTx: '#c07d10',
          redBg: '#fdeaea',
          redTx: '#d5362f',
        },
        fontFamily: {
          sans: ['Inter', 'sans-serif'],
          display: ['Plus Jakarta Sans', 'sans-serif'],
          script: ['Playfair Display', 'serif'],
        },
        borderRadius: {
          card: '16px',
        },
        boxShadow: {
          sidebar: '6px 0 24px rgba(9,20,46,.18)',
          brandmark: '0 6px 16px rgba(245,165,36,.35)',
          navactive: '0 8px 20px rgba(245,165,36,.3)',
          panel: '0 4px 20px rgba(15,42,82,.06)',
          btn: '0 8px 18px rgba(15,42,82,.25)',
          btnhover: '0 10px 22px rgba(15,42,82,.32)',
          filteractive: '0 8px 18px rgba(15,42,82,.25)',
          menu: '0 12px 32px rgba(15,42,82,.12)',
        },
      }
    }
  }
</script>
<style>
  * { transition-timing-function: cubic-bezier(.4,0,.2,1); }

  .sidebar::-webkit-scrollbar{width:3px;}
  .sidebar::-webkit-scrollbar-track{background:transparent;}
  .sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:10px;}
  .sidebar::-webkit-scrollbar-thumb:hover{background:rgba(255,255,255,.18);}
  .sidebar{ scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.08) transparent; }

  .sidebar.collapsed{ width:0; overflow:hidden; }

  .overlay.show{ opacity:1; visibility:visible; }

  .nav-item.active{
    background:linear-gradient(135deg,#f5a524,#e0930f);
    box-shadow:0 8px 20px rgba(245,165,36,.3);
  }
  .nav-item.active .nav-icon{ background:rgba(255,255,255,.22); color:#3a2306; }
  .nav-item.active .nav-text .main{ color:#2c1a04; }
  .nav-item.active .nav-text .sub{ color:#5b3c0c; }

  .main-content.expanded{ margin-left:0; }

  .menu-toggle:active,
  .icon-btn:active{
    background:#b9c2d1 !important;
    color:#0f2242 !important;
  }

  .profile-menu.show{
    opacity:1; visibility:visible; transform:translateY(0);
  }

  .filter-pill.active{
    background:linear-gradient(135deg,#173868,#0c1f3f);
    color:#fff;
    box-shadow:0 8px 18px rgba(15,42,82,.25);
  }

  .gl-stat-fill{ transition: width .6s cubic-bezier(.4,0,.2,1); }

  .gl-table tbody tr:hover{ background:#f7f9fc; }

  .gl-page-btn.active{
    background:linear-gradient(135deg,#f5a524,#e0930f);
    color:#3a2306;
    box-shadow:0 6px 14px rgba(245,165,36,.3);
  }

  @media (max-width:900px){
    .sidebar{ position:fixed; top:0; left:0; height:100vh; transform:translateX(-100%); overflow-y:auto; }
    .sidebar.show{ transform:translateX(0); }
    .topbar{ padding:0 1.2rem; }
    .main-content{ padding:1.5rem 1.2rem 3rem; }
  }
</style>
</head>
<body class="font-sans bg-bg text-textDark overflow-x-hidden">
<div class="flex h-screen overflow-hidden">

<div class="overlay fixed inset-0 bg-black/40 z-[999] opacity-0 invisible transition-all duration-300" id="overlay"></div>

<aside class="sidebar shrink-0 w-[290px] h-full overflow-y-auto bg-gradient-to-b from-navy900 to-navy800 flex flex-col z-[1000] transition-all duration-300 shadow-sidebar" id="sidebar">
  <div class="sidebar-top flex items-center justify-center px-[1.4rem] pt-[2.8rem] pb-[1.4rem]">
    <div class="brand flex items-center gap-2">
      <div class="brand-name">
        <svg class="w-full h-auto max-w-[232px]" viewBox="0 -45 320 205" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
          <defs>
            <linearGradient id="eightGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#f5a524"/>
              <stop offset="100%" stop-color="#e0930f"/>
            </linearGradient>
          </defs>
          <!-- Cursive logo C -->
          <text x="-2" y="90" style="font-family:'Pinyon Script',cursive; font-size:135px; fill:white;">C</text>
          <!-- RE8TED wordmark -->
          <text x="186" y="66" text-anchor="middle" style="font-family:'Fraunces',serif; font-weight:700; font-size:38px; letter-spacing:1px;"><tspan fill="white">RE</tspan><tspan fill="url(#eightGrad)">8</tspan><tspan fill="white">TED</tspan></text>
          <!-- Tagline -->
          <text x="186" y="83" text-anchor="middle" style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:600; font-size:10px; fill:rgba(255,255,255,0.45); letter-spacing:4px;">TRAVEL &amp; TOURS</text>
        </svg>
      </div>
    </div>
  </div>

  <div class="w-full h-px bg-white/10 mb-4"></div>

  <nav class="sidebar-nav px-[.9rem] py-2 pb-8" id="sidebarNav">
    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-orange shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Reporting</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Reports &amp; Analytics">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-line-chart'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Reports &amp; Analytics</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Performance Insights</span></div>
    </a>

    <div class="-mx-[.9rem] mt-[1.2rem] mb-[1.6rem] h-px bg-white/10"></div>

    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-orange shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Core Accounting</span>
    </div>
    <a class="nav-item active group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="General Ledger">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-book-content'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">General Ledger</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Transaction Records</span></div>
    </a>

    <div class="-mx-[.9rem] mt-[1.2rem] mb-[1.6rem] h-px bg-white/10"></div>

    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-orange shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Financial Operations</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Accounts Payable">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-credit-card'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Accounts Payable (AP)</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Vendor Bills</span></div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Accounts Receivable">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-receipt'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Accounts Receivable (AR)</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Client Invoices</span></div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Collection Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-archive-in'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Collection Management</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Overdue Tracking</span></div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Disbursement Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-transfer-alt'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Disbursement Management</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Fund Releases</span></div>
    </a>

    <div class="-mx-[.9rem] mt-[1.2rem] mb-[1.6rem] h-px bg-white/10"></div>

    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-orange shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Financial Control</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Cash Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-money'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Cash Management</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Liquidity Overview</span></div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Budget Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-pie-chart-alt-2'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Budget Management</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Spending Plans</span></div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white/[.06] hover:translate-x-[3px]" data-page="Tax Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.06] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-calculator'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-[#e7edf9]">Tax Management</span><span class="sub text-[.68rem] font-medium text-[#7f93b8]">Compliance &amp; Filing</span></div>
    </a>
  </nav>
</aside>

<div class="flex flex-col flex-1 min-w-0 h-full overflow-y-auto">
<nav class="topbar sticky top-0 h-[65px] shrink-0 grow-0 bg-card border-b border-line flex items-center justify-between px-5 z-[900] transition-all duration-300" id="topbar">
  <div class="topbar-left flex items-center gap-4">
    <button class="menu-toggle w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-textMid cursor-pointer mr-1 transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="menuToggle" title="Toggle sidebar">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <line x1="5" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="5" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
    <div>
      <h2 class="font-display text-[1.35rem] font-extrabold text-textDark" id="pageTitle"></h2>
    </div>
  </div>
  <div class="topbar-right flex items-center gap-0">
    <div class="topbar-time flex items-center gap-0 text-[.95rem] text-textDark mr-[9px]">
      <div class="date font-light text-[.9rem]" id="topbarDate">Loading date</div>
      <span class="font-light text-[.9rem]">,&nbsp;</span>
      <div class="time font-light text-[.9rem]" id="topbarTime">--:--</div>
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
            <div class="profile-menu-name font-bold text-textDark">Avesxz</div>
            <div class="profile-menu-role text-[.8rem] text-textSoft">Accountant</div>
          </div>
        </div>
        <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="profileViewBtn" title="Profile"><i class='bx bx-user text-[1.1rem]'></i>Profile</button>
        <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="logoutBtn" title="Logout"><i class='bx bx-log-out text-[1.1rem]'></i>Logout</button>
      </div>
    </div>
  </div>
</nav>

<div class="main-content flex-1 px-8 pt-[2.2rem] pb-12 transition-all duration-300" id="mainContent">

  <!-- ===== General Ledger header ===== -->
  <div class="gl-header bg-card rounded-card shadow-panel border border-line p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-xl bg-navy800 flex items-center justify-center text-orange text-[1.3rem] shrink-0"><i class='bx bx-book-content'></i></div>
      <h1 class="font-display text-[1.3rem] font-extrabold text-textDark">General Ledger</h1>
    </div>
    <div class="flex items-center gap-2">
      <button id="importBtn" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#eef2f7] text-textDark text-[.85rem] font-semibold border border-line cursor-pointer transition-all duration-200 hover:bg-[#dde3ec]">
        <i class='bx bx-download text-[1.05rem]'></i> Import
      </button>
      <button id="newEntryBtn" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-br from-orange to-orangeDark text-[#3a2306] text-[.85rem] font-bold cursor-pointer border-none shadow-btn transition-all duration-200 hover:shadow-btnhover hover:-translate-y-px">
        <i class='bx bx-plus text-[1.1rem]'></i> New Journal Entry
      </button>
    </div>
  </div>

  <!-- ===== Stat cards ===== -->
  <div class="gl-stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-5">
    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Total Debit</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-trending-up'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statTotalDebit">₱ 0.00</div>
      <div class="text-[.78rem] text-textSoft mb-2" id="statDebitCaption">0 Active</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:0%" id="statDebitBar"></div></div>
    </div>

    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Total Credit</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-layer'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statTotalCredit">₱ 0.00</div>
      <div class="text-[.78rem] text-textSoft mb-2" id="statCreditCaption">0 Voided</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:0%" id="statCreditBar"></div></div>
    </div>

    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Net Balance</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-pause-circle'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statNetBalance">₱ 0.00</div>
      <div class="text-[.78rem] text-textSoft mb-2">Balance</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:100%" id="statBalanceBar"></div></div>
    </div>

    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Journal Entries</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-crop'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statTotalEntries">0</div>
      <div class="text-[.78rem] text-textSoft mb-2">Recorded this period</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:100%"></div></div>
    </div>

    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Posted</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-transfer'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statPosted">0</div>
      <div class="text-[.78rem] text-textSoft mb-2">Confirmed entries</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:0%" id="statPostedBar"></div></div>
    </div>

    <div class="bg-card rounded-card shadow-panel border border-line p-5">
      <div class="flex items-start justify-between mb-3">
        <span class="text-[.78rem] font-semibold text-textMid">Draft</span>
        <div class="w-9 h-9 rounded-lg bg-navy800 flex items-center justify-center text-orange text-[1.05rem]"><i class='bx bx-star'></i></div>
      </div>
      <div class="text-[1.5rem] font-extrabold font-display text-textDark mb-1" id="statDraft">0</div>
      <div class="text-[.78rem] text-textSoft mb-2">Awaiting posting</div>
      <div class="h-1.5 w-full bg-[#eef2f7] rounded-full overflow-hidden"><div class="gl-stat-fill h-full bg-gradient-to-r from-orange to-orangeDark rounded-full" style="width:0%" id="statDraftBar"></div></div>
    </div>
  </div>

  <!-- ===== Entries table panel ===== -->
  <div class="bg-card rounded-card shadow-panel border border-line overflow-hidden">
    <div class="flex items-center justify-between flex-wrap gap-3 px-5 py-4 border-b border-line">
      <div class="flex items-center gap-2 flex-wrap" id="filterPills">
        <button class="filter-pill active px-4 py-2 rounded-xl text-[.82rem] font-semibold bg-[#eef2f7] text-textDark cursor-pointer border-none transition-all duration-200" data-filter="all">All Entries</button>
        <button class="filter-pill px-4 py-2 rounded-xl text-[.82rem] font-semibold bg-[#eef2f7] text-textDark cursor-pointer border-none transition-all duration-200" data-filter="posted">Posted</button>
        <button class="filter-pill px-4 py-2 rounded-xl text-[.82rem] font-semibold bg-[#eef2f7] text-textDark cursor-pointer border-none transition-all duration-200" data-filter="draft">Draft</button>
        <button class="filter-pill px-4 py-2 rounded-xl text-[.82rem] font-semibold bg-[#eef2f7] text-textDark cursor-pointer border-none transition-all duration-200" data-filter="voided">Voided</button>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <div class="relative">
          <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-textSoft text-[1rem]'></i>
          <input id="searchInput" type="text" placeholder="Search Entries..." class="pl-9 pr-3 py-2 rounded-xl border border-line text-[.85rem] text-textDark bg-bg/40 outline-none w-[190px] focus:border-navy700">
        </div>
        <select id="accountFilter" class="px-3 py-2 rounded-xl border border-line text-[.85rem] text-textDark bg-white outline-none cursor-pointer">
          <option value="all">All Accounts</option>
        </select>
        <select id="monthFilter" class="px-3 py-2 rounded-xl border border-line text-[.85rem] text-textDark bg-white outline-none cursor-pointer">
          <option value="all">All Months</option>
        </select>
        <button id="exportBtn" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-navy800 text-white text-[.82rem] font-semibold cursor-pointer border-none transition-all duration-200 hover:bg-navy700">
          <i class='bx bx-export text-[1rem]'></i> Export CSV
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="gl-table w-full border-collapse min-w-[900px]">
        <thead>
          <tr class="text-left border-b border-line">
            <th class="px-5 py-3 w-10"><input type="checkbox" id="selectAll" class="w-[15px] h-[15px] cursor-pointer accent-navy800"></th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft">JE No.</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft">Date</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft">Accounts</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft">Description</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft text-right">Debit (₱)</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft text-right">Credit (₱)</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft">Status</th>
            <th class="px-3 py-3 text-[.72rem] font-bold tracking-[.5px] uppercase text-textSoft text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="glTableBody"></tbody>
      </table>
    </div>

    <div class="flex items-center justify-between flex-wrap gap-3 px-5 py-4 border-t border-line">
      <div class="text-[.8rem] text-textSoft" id="paginationSummary">Showing 0 entries</div>
      <div class="flex items-center gap-1.5" id="paginationControls"></div>
      <select id="pageSizeSelect" class="px-3 py-1.5 rounded-lg border border-line text-[.82rem] text-textDark bg-white outline-none cursor-pointer">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');
  const topbar = document.getElementById('topbar');
  const menuToggle = document.getElementById('menuToggle');
  const overlay = document.getElementById('overlay');

  function toggleSidebar(){
    if(window.innerWidth <= 900){
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    } else {
      sidebar.classList.toggle('collapsed');
    }
  }

  menuToggle.addEventListener('click', toggleSidebar);
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
  });

  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(){
      document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      document.getElementById('pageTitle').textContent = this.dataset.page;
      if(window.innerWidth <= 900){
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
      }
    });
  });

  function updateTopbarTime(){
    const now = new Date();
    const timeOptions = { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true, timeZone:'Asia/Manila' };
    const dateOptions = { weekday:'short', month:'short', day:'numeric', year:'numeric', timeZone:'Asia/Manila' };
    const timeString = now.toLocaleTimeString(undefined, timeOptions);
    const dateString = now.toLocaleDateString(undefined, dateOptions);
    const timeEl = document.getElementById('topbarTime');
    const dateEl = document.getElementById('topbarDate');
    if(timeEl) timeEl.textContent = timeString;
    if(dateEl) dateEl.textContent = dateString;
  }
  updateTopbarTime();
  setInterval(updateTopbarTime, 1000);

  document.getElementById('notifBtn').addEventListener('click', () => {
    alert('Notifications panel will open here.');
  });
  const profileBtn = document.getElementById('profileBtn');
  const profileMenu = document.getElementById('profileMenu');
  const profileViewBtn = document.getElementById('profileViewBtn');
  const logoutBtn = document.getElementById('logoutBtn');
  profileBtn.addEventListener('click', () => {
    profileMenu.classList.toggle('show');
  });
  profileViewBtn.addEventListener('click', () => {
    alert('Opening profile settings...');
    profileMenu.classList.remove('show');
  });
  logoutBtn.addEventListener('click', () => {
    alert('Logging out...');
    profileMenu.classList.remove('show');
  });
  document.addEventListener('click', (event) => {
    if (!profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
      profileMenu.classList.remove('show');
    }
  });

  window.addEventListener('resize', () => {
    if(window.innerWidth > 900){
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    }
  });

  // ============================================================
  //  GENERAL LEDGER MODULE
  // ============================================================
  const glEntries = [
    { id:'JE-2025-1001', date:'2025-01-05', accounts:['Cash in Bank','Sales Revenue'], description:'Tour package sale — Palawan 3D2N', tag:'General', debit:85000, credit:85000, status:'posted' },
    { id:'JE-2025-1002', date:'2025-01-12', accounts:['Accounts Receivable','Sales Revenue'], description:'Booking invoice — corporate client', tag:'General', debit:120000, credit:120000, status:'posted' },
    { id:'JE-2025-1003', date:'2025-01-20', accounts:['Travel Expense','Cash in Bank'], description:'Hotel booking payment — supplier settlement', tag:'General', debit:45000, credit:45000, status:'posted' },
    { id:'JE-2025-1004', date:'2025-01-28', accounts:['Cash in Bank','Accounts Receivable'], description:'Client payment received', tag:'General', debit:120000, credit:120000, status:'posted' },
    { id:'JE-2025-1005', date:'2025-02-05', accounts:['Depreciation Expense','Accumulated Depreciation'], description:'Depreciation — office equipment (Feb)', tag:'Adjusting', debit:8500, credit:8500, status:'draft' },
    { id:'JE-2025-1006', date:'2025-02-12', accounts:['Utilities Expense','Accounts Payable'], description:'Utility expense — electricity (MERALCO)', tag:'General', debit:12300, credit:12300, status:'posted' },
    { id:'JE-2025-1007', date:'2025-03-01', accounts:['Petty Cash Fund','Cash in Bank'], description:'Petty cash replenishment', tag:'General', debit:5000, credit:5000, status:'voided' },
  ];

  const statusMeta = {
    posted: { label:'Posted', cls:'bg-greenBg text-greenTx' },
    draft:  { label:'Draft',  cls:'bg-amberBg text-amberTx' },
    voided: { label:'Void',   cls:'bg-redBg text-redTx' },
  };

  const fmt = n => '₱ ' + n.toLocaleString('en-PH', { minimumFractionDigits:2, maximumFractionDigits:2 });

  let currentFilter = 'all';
  let currentPage = 1;
  let pageSize = 10;

  // populate account filter options
  const accountFilterEl = document.getElementById('accountFilter');
  const uniqueAccounts = [...new Set(glEntries.flatMap(e => e.accounts))].sort();
  uniqueAccounts.forEach(acc => {
    const opt = document.createElement('option');
    opt.value = acc; opt.textContent = acc;
    accountFilterEl.appendChild(opt);
  });

  // populate month filter options
  const monthFilterEl = document.getElementById('monthFilter');
  const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const uniqueMonths = [...new Set(glEntries.map(e => e.date.slice(0,7)))].sort();
  uniqueMonths.forEach(m => {
    const [y, mo] = m.split('-');
    const opt = document.createElement('option');
    opt.value = m; opt.textContent = monthNames[parseInt(mo,10)-1] + ' ' + y;
    monthFilterEl.appendChild(opt);
  });

  function getFilteredEntries(){
    const search = document.getElementById('searchInput').value.trim().toLowerCase();
    const accFilter = accountFilterEl.value;
    const monthFilterVal = monthFilterEl.value;

    return glEntries.filter(e => {
      if(currentFilter !== 'all' && e.status !== currentFilter) return false;
      if(accFilter !== 'all' && !e.accounts.includes(accFilter)) return false;
      if(monthFilterVal !== 'all' && e.date.slice(0,7) !== monthFilterVal) return false;
      if(search){
        const haystack = (e.id + ' ' + e.accounts.join(' ') + ' ' + e.description + ' ' + e.tag).toLowerCase();
        if(!haystack.includes(search)) return false;
      }
      return true;
    });
  }

  function renderStats(){
    const totalDebit = glEntries.reduce((s,e)=>s+e.debit,0);
    const totalCredit = glEntries.reduce((s,e)=>s+e.credit,0);
    const netBalance = totalDebit - totalCredit;
    const activeCount = glEntries.filter(e=>e.status!=='voided').length;
    const voidedCount = glEntries.filter(e=>e.status==='voided').length;
    const postedCount = glEntries.filter(e=>e.status==='posted').length;
    const draftCount = glEntries.filter(e=>e.status==='draft').length;
    const total = glEntries.length || 1;

    document.getElementById('statTotalDebit').textContent = fmt(totalDebit);
    document.getElementById('statTotalCredit').textContent = fmt(totalCredit);
    document.getElementById('statNetBalance').textContent = fmt(Math.abs(netBalance));
    document.getElementById('statDebitCaption').textContent = activeCount + ' Active';
    document.getElementById('statCreditCaption').textContent = voidedCount + ' Voided';
    document.getElementById('statTotalEntries').textContent = glEntries.length;
    document.getElementById('statPosted').textContent = postedCount;
    document.getElementById('statDraft').textContent = draftCount;

    document.getElementById('statDebitBar').style.width = '100%';
    document.getElementById('statCreditBar').style.width = '100%';
    document.getElementById('statPostedBar').style.width = Math.round((postedCount/total)*100) + '%';
    document.getElementById('statDraftBar').style.width = Math.round((draftCount/total)*100) + '%';
  }

  function renderTable(){
    const filtered = getFilteredEntries();
    const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
    if(currentPage > totalPages) currentPage = totalPages;
    const start = (currentPage - 1) * pageSize;
    const pageItems = filtered.slice(start, start + pageSize);

    const tbody = document.getElementById('glTableBody');
    tbody.innerHTML = '';

    if(pageItems.length === 0){
      tbody.innerHTML = '<tr><td colspan="9" class="px-5 py-10 text-center text-textSoft text-[.9rem]">No journal entries match your filters.</td></tr>';
    }

    pageItems.forEach(e => {
      const meta = statusMeta[e.status];
      const tr = document.createElement('tr');
      tr.className = 'border-b border-line last:border-b-0';
      tr.innerHTML = `
        <td class="px-5 py-3.5"><input type="checkbox" class="row-check w-[15px] h-[15px] cursor-pointer accent-navy800"></td>
        <td class="px-3 py-3.5 text-[.85rem] font-bold text-navy800">${e.id}</td>
        <td class="px-3 py-3.5 text-[.85rem] text-textMid">${e.date}</td>
        <td class="px-3 py-3.5 text-[.85rem] text-textDark">${e.accounts.join(', ')}</td>
        <td class="px-3 py-3.5 text-[.85rem] text-textDark">
          <div>${e.description}</div>
          <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-[#eef2f7] text-textSoft text-[.68rem] font-semibold">${e.tag}</span>
        </td>
        <td class="px-3 py-3.5 text-[.85rem] text-textDark text-right font-semibold">${fmt(e.debit)}</td>
        <td class="px-3 py-3.5 text-[.85rem] text-textDark text-right font-semibold">${fmt(e.credit)}</td>
        <td class="px-3 py-3.5"><span class="inline-block px-2.5 py-1 rounded-full text-[.72rem] font-bold ${meta.cls}">${meta.label}</span></td>
        <td class="px-3 py-3.5 text-right">
          <button class="w-8 h-8 rounded-lg bg-transparent border-none text-textMid cursor-pointer hover:bg-[#eef2f7] hover:text-textDark" title="View"><i class='bx bx-show'></i></button>
          ${e.status !== 'voided' ? `<button class="w-8 h-8 rounded-lg bg-transparent border-none text-textMid cursor-pointer hover:bg-[#eef2f7] hover:text-textDark" title="Edit"><i class='bx bx-edit'></i></button>` : ''}
        </td>
      `;
      tbody.appendChild(tr);
    });

    document.getElementById('paginationSummary').textContent =
      filtered.length === 0 ? 'Showing 0 entries' :
      `Showing ${start+1}–${Math.min(start+pageSize, filtered.length)} of ${filtered.length} entries`;

    renderPagination(totalPages);
    document.getElementById('selectAll').checked = false;
  }

  function renderPagination(totalPages){
    const controls = document.getElementById('paginationControls');
    controls.innerHTML = '';

    const prevBtn = document.createElement('button');
    prevBtn.className = 'w-8 h-8 rounded-lg bg-[#eef2f7] text-textDark border-none cursor-pointer flex items-center justify-center hover:bg-[#dde3ec] disabled:opacity-40 disabled:cursor-not-allowed';
    prevBtn.innerHTML = "<i class='bx bx-chevron-left'></i>";
    prevBtn.disabled = currentPage === 1;
    prevBtn.addEventListener('click', () => { currentPage--; renderTable(); });
    controls.appendChild(prevBtn);

    for(let p = 1; p <= totalPages; p++){
      const btn = document.createElement('button');
      btn.className = 'gl-page-btn w-8 h-8 rounded-lg bg-[#eef2f7] text-textDark border-none cursor-pointer text-[.82rem] font-semibold' + (p === currentPage ? ' active' : '');
      btn.textContent = p;
      btn.addEventListener('click', () => { currentPage = p; renderTable(); });
      controls.appendChild(btn);
    }

    const nextBtn = document.createElement('button');
    nextBtn.className = 'w-8 h-8 rounded-lg bg-gradient-to-br from-orange to-orangeDark text-[#3a2306] border-none cursor-pointer flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed';
    nextBtn.innerHTML = "<i class='bx bx-chevron-right'></i>";
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.addEventListener('click', () => { currentPage++; renderTable(); });
    controls.appendChild(nextBtn);
  }

  document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function(){
      document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.dataset.filter;
      currentPage = 1;
      renderTable();
    });
  });

  document.getElementById('searchInput').addEventListener('input', () => { currentPage = 1; renderTable(); });
  accountFilterEl.addEventListener('change', () => { currentPage = 1; renderTable(); });
  monthFilterEl.addEventListener('change', () => { currentPage = 1; renderTable(); });
  document.getElementById('pageSizeSelect').addEventListener('change', function(){
    pageSize = parseInt(this.value, 10);
    currentPage = 1;
    renderTable();
  });

  document.getElementById('selectAll').addEventListener('change', function(){
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
  });

  document.getElementById('importBtn').addEventListener('click', () => alert('Import journal entries from a file will open here.'));
  document.getElementById('newEntryBtn').addEventListener('click', () => alert('New journal entry form will open here.'));

  document.getElementById('exportBtn').addEventListener('click', () => {
    const rows = getFilteredEntries();
    const header = ['JE No.','Date','Accounts','Description','Tag','Debit','Credit','Status'];
    const csvLines = [header.join(',')];
    rows.forEach(e => {
      csvLines.push([
        e.id, e.date, `"${e.accounts.join(' / ')}"`, `"${e.description}"`, e.tag,
        e.debit.toFixed(2), e.credit.toFixed(2), statusMeta[e.status].label
      ].join(','));
    });
    const blob = new Blob([csvLines.join('\n')], { type:'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'general-ledger.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
  });

  renderStats();
  renderTable();
});
</script>
</div>
</div>
</body>
</html>