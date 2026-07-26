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
  .sidebar{ 
    scrollbar-width:thin; 
    scrollbar-color:rgba(255,255,255,.08) transparent; 
  }

  .sidebar.collapsed{ 
    width:0; 
    overflow:hidden; 
  }

  .overlay.show{ opacity:1; visibility:visible; }

 
  .nav-item.active,
  .nav-item.active:hover{
    background:#fffcf6;
    transform:none;
  }
  .nav-item.active .nav-icon,
  .nav-item.active:hover .nav-icon{ background: rgba(2, 2, 86, 0.934); }
  .nav-item.active .nav-text .main,
  .nav-item.active:hover .nav-text .main{ color:#0f2242; }
  .nav-item.active .nav-text .sub,
  .nav-item.active:hover .nav-text .sub{ color:#5b6b85; }

  .nav-item:not(.active):hover{
    background:rgba(255,255,255,.12);
    transform:translateX(3px);
  }
  .nav-item:not(.active):hover .nav-icon{ background: rgba(2, 2, 86, 0.934);}
  .nav-item:not(.active):hover .nav-text .main{ color:black; }
  .nav-item:not(.active):hover .nav-text .sub{ color:rgb(62, 59, 59); }

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

  @keyframes pulse-red {
  0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.7); }
  50%       { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
  }
  .dot-pulse { animation: pulse-red 1.6s ease-in-out infinite; }

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
<aside class="sidebar shrink-0 w-[290px] h-full overflow-y-auto bg-gradient-to-br from-orange to-orangeDark flex flex-col z-[1000] transition-all duration-300 shadow-sidebar" id="sidebar">

  <div class="sidebar-top flex items-center justify-center px-[1.9rem] pt-[0.5rem] pb-[0.1rem]">
    <div class="brand flex items-center justify-center">
  <img src="../assets/images/logo.png" alt="Brand logo" class="w-50 h-40 object-contain" />
    </div>
  </div>
  <nav class="sidebar-nav px-[.9rem] py-2 pb-8" id="sidebarNav">
    <div class="w-full h-px bg-white/10 mb-4"></div>
     <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white hover:translate-x-[3px]" data-page="Dashboard">
     <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-navy800 flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-grid-alt'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-black">Dashboard</span></div>
    </a>
      <div class="w-full h-px bg-white/10 mb-4"></div>
    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
   <span class="w-[6px] h-[6px] rounded-full bg-black shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-black uppercase whitespace-nowrap">Reporting</span>
    </div>
    <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white hover:translate-x-[3px]" data-page="Reports &amp; Analytics">
     <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-navy800 flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-line-chart'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-black">Reports &amp; Analytics</span><span class="sub text-[.68rem] font-medium text-black">Performance Insights</span></div>
    </a>

    <div class="-mx-[.9rem] mt-[1.2rem] mb-[1.6rem] h-px bg-white/10"></div>

    

    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-black shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-black uppercase whitespace-nowrap">Core Accounting</span>
    </div>
    <a class="nav-item active group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white hover:translate-x-[3px]" data-page="General Ledger">
      <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-navy800 flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-book-content'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-black">General Ledger</span><span class="sub text-[.68rem] font-medium text-black">Transaction Records</span></div>
    </a>

    <div class="-mx-[.9rem] mt-[1.2rem] mb-[1.6rem] h-px bg-white/10"></div>

    <div class="nav-section-title flex items-center gap-2 px-[.9rem] pt-[.4rem] pb-[.9rem]">
      <span class="w-[6px] h-[6px] rounded-full bg-black shrink-0"></span>
      <span class="text-[.66rem] font-semibold tracking-[2px] text-black uppercase whitespace-nowrap">Financial Operations</span>
    </div>
     <a class="nav-item group flex items-center gap-[.8rem] px-[.9rem] py-[.85rem] rounded-xl text-[#b7c6e3] no-underline cursor-pointer mb-[.35rem] transition-all duration-200 relative hover:bg-white hover:translate-x-[3px]" data-page="Reports &amp; Analytics">
     <div class="nav-icon w-[34px] h-[34px] rounded-[10px] bg-navy800 flex items-center justify-center text-[1.15rem] shrink-0 transition-all duration-200 group-hover:bg-white/[.12]"><i class='bx bx-line-chart'></i></div>
      <div class="nav-text flex flex-col leading-[1.25]"><span class="main text-[.88rem] font-bold text-black">Reports &amp; Analytics</span><span class="sub text-[.68rem] font-medium text-black">Performance Insights</span></div>
    </a>
   
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
  </div>
  <div class="topbar-right flex items-center gap-0">
    <div class="topbar-time flex items-center gap-0 text-[.95rem] text-textDark mr-[9px]">
      <div class="date font-light text-[.9rem]" id="topbarDate">Loading date</div>
      <span class="font-light text-[.9rem]">,&nbsp;</span>
      <div class="time font-light text-[.9rem]" id="topbarTime">--:--</div>
    </div>
    <button class="icon-btn relative w-10 h-10 rounded-full bg-transparent border-none flex items-center justify-center text-textMid text-[1.5rem] cursor-pointer transition-all duration-200 hover:bg-[#dde3ec] hover:text-textDark" id="notifBtn" title="Notifications">
      <i class='bx bx-bell'></i><span class="dot dot-pulse absolute top-[9px] right-[10px] w-2 h-2 bg-red-500 rounded-full border-2 border-card"></span>
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

  <!-- ===== Dashboard ===== -->
  <div class="gl-header bg-card rounded-card shadow-panel border border-line p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-xl bg-navy800 flex items-center justify-center text-orange text-[1.3rem] shrink-0"><i class='bx bx-grid-alt'></i></div>
      <h1 class="font-display text-[1.3rem] font-extrabold text-textDark">Dashboard</h1>
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


});
</script>
</div>
</div>
</body>
</html>