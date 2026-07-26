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

  /* ── Layout: sidebar fixed, content scrolls independently ── */
  html, body { height: 100%; overflow: hidden; }

  .layout-shell {
    display: flex;
    height: 100vh;
    overflow: hidden;
  }

  /* Sidebar: fixed height, own scroll, scrollbar hidden */
  .sidebar {
    width: 300px;
    flex-shrink: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none; /* Firefox */
  }
  .sidebar::-webkit-scrollbar { display: none; } /* Chrome/Safari */

  .sidebar.collapsed { width: 0; overflow: hidden; }

  /* Right column: own scroll */
  .right-col {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
  }

  .main-content {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    /* custom thin scrollbar for main content */
    scrollbar-width: thin;
    scrollbar-color: #c5cfe0 transparent;
  }
  .main-content::-webkit-scrollbar { width: 5px; }
  .main-content::-webkit-scrollbar-track { background: transparent; }
  .main-content::-webkit-scrollbar-thumb { background: #c5cfe0; border-radius: 10px; }

  /* ── Nav item states ── */
.nav-item.active {
    background: linear-gradient(135deg,#f5a524,#e0930f);
    box-shadow: 0 8px 20px rgba(245,165,36,.3);
  }
  .nav-item.active .nav-icon { background: rgba(255,255,255,.25); color: #1a0d00; }
  .nav-item.active .nav-text .main { color: #1a0d00; }
  .nav-item.active .nav-text .sub  { color: #3d2000; }

  .nav-item:not(.active):hover {
    background: rgba(245,165,36,.10) !important;
    transform: translateX(3px);
  }
  .nav-item:not(.active):hover .nav-icon {
    background: rgba(255,255,255,.22) !important;
    color: #f5a524 !important;
  }

  /* ── Overlays & profile menu ── */
  .overlay.show { opacity:1; visibility:visible; }
  .profile-menu.show { opacity:1; visibility:visible; transform:translateY(0); }

  .menu-toggle:active,
  .icon-btn:active {
    background:#b9c2d1 !important;
    color:#0f2242 !important;
  }

  /* ── Pulse animations ── */
  @keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(239,68,68,.7); }
    70%  { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
    100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
  }
  @keyframes pulse-ring-blue {
    0%   { box-shadow: 0 0 0 0 rgba(59,130,246,.7); }
    70%  { box-shadow: 0 0 0 5px rgba(59,130,246,0); }
    100% { box-shadow: 0 0 0 0 rgba(59,130,246,0); }
  }

  /* Section title */
  .nav-section-title {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.4rem 0.9rem 0.9rem;
    position: relative;
  }
  .nav-section-title .section-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #3b82f6;
    flex-shrink: 0;
    box-shadow: 0 0 6px rgba(59,130,246,.6);
  }
  .nav-section-title .section-label {
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.38);
    white-space: nowrap;
  }

  /* ── Mobile ── */
  @media (max-width:900px){
    .sidebar {
      position: fixed; top:0; left:0; height:100vh;
      transform: translateX(-100%);
      z-index: 1000;
      transition: transform .3s cubic-bezier(.4,0,.2,1);
    }
    .sidebar.show { transform: translateX(0); }
    .right-col { height: 100vh; }
    .topbar { padding: 0 1.2rem; }
    .main-content { padding: 1.5rem 1.2rem 3rem; }
  }
</style>
</head>
<body class="font-sans bg-bg text-textDark">

<div class="overlay fixed inset-0 bg-black/40 z-[999] opacity-0 invisible transition-all duration-300" id="overlay"></div>

<div class="layout-shell">

<!-- ═══════════════════════════════ SIDEBAR ═══════════════════════════════ -->
<aside class="sidebar bg-gradient-to-b from-navy900 to-navy800 flex flex-col shadow-sidebar transition-all duration-300" id="sidebar">

  <div class="flex items-center justify-center px-[1.4rem] pt-[2.8rem] pb-[1.4rem]">
    <svg class="w-full h-auto max-w-[232px]" viewBox="0 -45 320 205" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
      <defs>
        <linearGradient id="eightGrad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#f5a524"/>
          <stop offset="100%" stop-color="#e0930f"/>
        </linearGradient>
      </defs>
      <text x="-2" y="90" style="font-family:'Pinyon Script',cursive;font-size:135px;fill:white;">C</text>
      <text x="186" y="66" text-anchor="middle" style="font-family:'Fraunces',serif;font-weight:700;font-size:38px;letter-spacing:1px;">
        <tspan fill="white">RE</tspan><tspan fill="url(#eightGrad)">8</tspan><tspan fill="white">TED</tspan>
      </text>
      <text x="186" y="83" text-anchor="middle" style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;font-size:10px;fill:rgba(255,255,255,0.45);letter-spacing:4px;">TRAVEL &amp; TOURS</text>
    </svg>
  </div>

  <div style="width:100%; height:1px; background:rgba(255,255,255,0.10); margin-bottom:1rem; flex-shrink:0;"></div>

  <nav class="flex-1 px-[.9rem] py-2 pb-8" id="sidebarNav">

    <!-- Overview -->
    <div class="nav-section-title">
      <span class="w-[6px] h-[6px] rounded-full bg-blue-500 shrink-0" style="animation:pulse-ring-blue 2s infinite;"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Overview</span>
    </div>
    <a class="nav-item active group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Dashboard">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bxs-dashboard'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Dashboard</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">System Overview</span>
      </div>
    </a>

    <div style="margin: 1.2rem -0.9rem 1.6rem; height:1px; background:rgba(255,255,255,0.10);"></div>

    <!-- Core Accounting -->
    <div class="nav-section-title">
      <span class="w-[6px] h-[6px] rounded-full bg-blue-500 shrink-0" style="animation:pulse-ring-blue 2s infinite;"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Core Accounting</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="General Ledger">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-book-content'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">General Ledger</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Journal Entries &amp; Balances</span>
      </div>
    </a>

    <div style="margin: 1.2rem -0.9rem 1.6rem; height:1px; background:rgba(255,255,255,0.10);"></div>

    <!-- Financial Operations -->
    <div class="nav-section-title">
      <span class="w-[6px] h-[6px] rounded-full bg-blue-500 shrink-0" style="animation:pulse-ring-blue 2s infinite;"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Financial Operations</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Accounts Payable">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-credit-card'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Accounts Payable</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Vendor Bills &amp; Payments</span>
      </div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Accounts Receivable">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-receipt'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Accounts Receivable</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Invoices &amp; Incoming Payments</span>
      </div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Disbursement Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-transfer-alt'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Disbursement Management</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Outgoing Fund Transfers</span>
      </div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Collection Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-archive-in'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Collection Management</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Overdue Account Follow-ups</span>
      </div>
    </a>

    <div style="margin: 1.2rem -0.9rem 1.6rem; height:1px; background:rgba(255,255,255,0.10);"></div>

    <!-- Financial Control -->
    <div class="nav-section-title">
      <span class="w-[6px] h-[6px] rounded-full bg-blue-500 shrink-0" style="animation:pulse-ring-blue 2s infinite;"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Financial Control</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Budget Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-pie-chart-alt-2'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Budget Management</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Departmental Spending Plans</span>
      </div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Cash Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-money'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Cash Management</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Cash Flow &amp; Liquidity</span>
      </div>
    </a>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Tax Management">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-calculator'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Tax Management</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Compliance &amp; Filing</span>
      </div>
    </a>

    <div style="margin: 1.2rem -0.9rem 1.6rem; height:1px; background:rgba(255,255,255,0.10);"></div>

    <!-- Reporting -->
    <div class="nav-section-title">
      <span class="w-[6px] h-[6px] rounded-full bg-blue-500 shrink-0" style="animation:pulse-ring-blue 2s infinite;"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-[#c3cee4] uppercase whitespace-nowrap">Reporting</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative" data-page="Financial Reporting & Analytics">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-white/[.14] flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 text-orange"><i class='bx bx-line-chart'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]">
        <span class="main text-[.88rem] font-bold text-[#e7edf9]">Financial Reporting</span>
        <span class="sub text-[.68rem] font-medium text-[#7f93b8]">Reports &amp; Analytics</span>
      </div>
    </a>

  </nav>
</aside>

<!-- ═══════════════════════════════ RIGHT COL ═══════════════════════════════ -->
<div class="right-col" id="rightCol">

  <!-- TOPBAR -->
  <nav class="topbar sticky top-0 h-[65px] bg-card border-b border-line flex items-center justify-between px-5 z-[900] transition-all duration-300 shrink-0" id="topbar">
    <div class="flex items-center gap-4">
      <button class="menu-toggle w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-textMid cursor-pointer mr-1 transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="menuToggle" title="Toggle sidebar">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <line x1="5" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <line x1="5" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <line x1="5" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
      <h2 class="font-display text-[1.35rem] font-extrabold text-textDark" id="pageTitle"></h2>
    </div>
    <div class="flex items-center gap-0">
      <div class="flex items-center gap-0 text-[.95rem] text-textDark mr-[9px]">
        <div class="font-light text-[.9rem]" id="topbarDate">Loading date</div>
        <span class="font-light text-[.9rem]">,&nbsp;</span>
        <div class="font-light text-[.9rem]" id="topbarTime">--:--</div>
      </div>
      <button class="icon-btn relative w-10 h-10 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="notifBtn" title="Notifications">
        <i class='bx bx-bell'></i>
        <span class="absolute top-[9px] right-[10px] w-2 h-2 bg-red-500 rounded-full border-2 border-card" style="animation:pulse-ring 2s infinite;"></span>
      </button>
      <div class="relative ml-1">
        <button class="icon-btn profile-btn w-10 h-10 min-w-10 p-0 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="profileBtn" title="Profile">
          <i class='bx bx-user-circle'></i>
        </button>
        <div class="profile-menu absolute top-[calc(100%+0.5rem)] right-0 bg-white border border-line rounded-2xl shadow-menu min-w-[220px] overflow-hidden opacity-0 invisible -translate-y-2 transition-all duration-200 z-[10000]" id="profileMenu">
          <div class="flex items-center gap-3 px-4 pt-4 pb-3 border-b border-[rgba(14,27,60,.08)]">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-[#eaf1fb] text-textMid text-[1.25rem]"><i class='bx bx-user-circle'></i></div>
            <div>
              <div class="font-bold text-textDark">Avesxz</div>
              <div class="text-[.8rem] text-textSoft">Accountant</div>
            </div>
          </div>
          <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="profileViewBtn"><i class='bx bx-user text-[1.1rem]'></i>Profile</button>
          <button class="w-full px-4 py-[.95rem] bg-transparent border-none flex items-center gap-3 text-textDark text-[.95rem] cursor-pointer text-left hover:bg-[rgba(14,27,60,.05)]" id="logoutBtn"><i class='bx bx-log-out text-[1.1rem]'></i>Logout</button>
        </div>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="main-content flex-1 px-8 pt-[2.2rem] pb-12" id="mainContent">
    <!-- page content goes here -->
  </div>

</div><!-- /right-col -->
</div><!-- /layout-shell -->

<script>
document.addEventListener('DOMContentLoaded', function(){
  const sidebar    = document.getElementById('sidebar');
  const menuToggle = document.getElementById('menuToggle');
  const overlay    = document.getElementById('overlay');
  const rightCol   = document.getElementById('rightCol');

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
    const timeEl = document.getElementById('topbarTime');
    const dateEl = document.getElementById('topbarDate');
    if(timeEl) timeEl.textContent = now.toLocaleTimeString(undefined, timeOptions);
    if(dateEl) dateEl.textContent = now.toLocaleDateString(undefined, dateOptions);
  }
  updateTopbarTime();
  setInterval(updateTopbarTime, 1000);

  document.getElementById('notifBtn').addEventListener('click', () => alert('Notifications panel will open here.'));

  const profileBtn     = document.getElementById('profileBtn');
  const profileMenu    = document.getElementById('profileMenu');
  const profileViewBtn = document.getElementById('profileViewBtn');
  const logoutBtn      = document.getElementById('logoutBtn');

  profileBtn.addEventListener('click', () => profileMenu.classList.toggle('show'));
  profileViewBtn.addEventListener('click', () => { alert('Opening profile settings...'); profileMenu.classList.remove('show'); });
  logoutBtn.addEventListener('click', () => { alert('Logging out...'); profileMenu.classList.remove('show'); });
  document.addEventListener('click', e => {
    if(!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) profileMenu.classList.remove('show');
  });

  window.addEventListener('resize', () => {
    if(window.innerWidth > 900){
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
    }
  });
});
</script>
</body>
</html>