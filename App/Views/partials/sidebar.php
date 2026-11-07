<?php
/**
 * Shared Samsung Edge Floating Shortcut Sidebar Partial
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Supported Variables:
 * @var string|null $active_page          Current active page ('dashboard', 'calendar', 'timetable', 'report', 'ctldrive', 'faculty')
 * @var string|null $sidebar_active_label Optional custom label for the active page header
 */

$baseUrl = function_exists('base_url') ? base_url('/') : './';
$activePage = $active_page ?? 'dashboard';

$pageLabels = [
  'dashboard'   => 'Smart Dashboard',
  'calendar'    => 'Academic Calendar',
  'timetable'   => 'Timetable Schedules',
  'report'      => 'Workload Reports',
  'ctlactivity' => 'CTL Activity Dashboard',
  'ctldrive'    => 'CTL Activity Drive',
  'faculty'     => 'Faculty List',
];
$activeLabel = $sidebar_active_label ?? ($pageLabels[$activePage] ?? 'Portal Navigation');
?>

<!-- MOBILE / TABLET SAMSUNG EDGE PULL-HANDLE TAB (Tap left edge to smoothly open Samsung Shortcut Menu!) -->
<button
  id="samsungEdgeHandle"
  type="button"
  class="fixed left-0 top-[36%] z-30 lg:hidden w-2.5 h-20 rounded-r-full bg-[var(--color-primary)]/75 backdrop-blur-md border border-l-0 border-white/70 shadow-lg flex items-center justify-center transition-all duration-300 hover:w-4 active:scale-95 cursor-pointer"
  title="Open Side Menu"
  aria-label="Open Side Menu Handle"
>
  <span class="w-0.5 h-8 rounded-full bg-white/90"></span>
</button>

<!-- MOBILE / TABLET SIDEBAR BACKDROP OVERLAY (Covers entire screen behind Sidebar when open!) -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-black/45 backdrop-blur-md z-40 hidden lg:hidden transition-opacity duration-300 opacity-0 cursor-pointer" aria-hidden="true"></div>

<!-- =========================================================================
     LEFT SIDE MENU: FLOATING SAMSUNG EDGE SHORTCUT CAPSULE (<1024px) & DESKTOP DOCK
     - Default Mobile: Clean Icon Capsule (76px wide, matching Image 1)
     - Expand Button (>>): Expands to Full Sidebar (284px) showing full Side Menu Names
     ========================================================================= -->
<aside
  id="mainSidebar"
  aria-label="Side Menu"
  class="samsung-menu-closed samsung-icon-only fixed lg:static inset-y-0 my-auto lg:my-0 h-fit max-h-[calc(100dvh-20px)] lg:max-h-none lg:h-full left-[10px] z-50 w-[76px] lg:w-[56px] p-2.5 lg:py-0 lg:px-0 flex flex-col justify-between items-center gap-2.5 lg:gap-3 shrink-0 overflow-y-auto overflow-x-hidden select-none"
>
  <!-- 0. TOP HOME ICON CAPSULE (Desktop & Mobile Brand Home Button) -->
  <div id="sidebarHomeCapsule" class="samsung-shortcut-item glass-pill rounded-full w-12 h-12 flex items-center justify-center shrink-0" style="--stagger: 0;">
    <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" id="brandHomeBtn" class="w-full h-full rounded-full flex items-center justify-center text-[var(--color-primary)] hover:scale-110 group transition shrink-0" title="Return to Main Dashboard">
      <div class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--color-primary)] group-hover:bg-[var(--color-primary)]/10 transition shrink-0">
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
    </a>
  </div>
  <!-- 1. UPPER CONTROL HEADER PILL (Mobile Header: Brand Icon, Menu Title & Sleek Expand Button) -->
  <div id="upperSideMenuHeader" class="samsung-shortcut-item side-menu-glass flex lg:hidden items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-full w-full" style="--stagger: 0;">
    <!-- Left Brand & Active Menu Title (Visible when Expanded with Side Menu Names) -->
    <div class="expanded-only expanded-flex items-center gap-2 min-w-0 flex-1">
      <div class="w-7 h-7 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center shadow-sm shrink-0">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/>
          <rect x="14" y="14" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/>
        </svg>
      </div>
      <div class="min-w-0 flex-1">
        <span class="sidebar-header-kicker text-[9px] font-extrabold uppercase tracking-wider block leading-none">Side Menu</span>
        <p id="upperSideMenuActiveLabel" class="font-bold text-[11.5px] text-[var(--text-primary)] leading-tight truncate mt-0.5"><?php echo htmlspecialchars($activeLabel); ?></p>
      </div>
    </div>

    <!-- Single Expand / Collapse Full Menu Button in Responsive Top Pill (No crowded close button!) -->
    <div class="flex items-center justify-center shrink-0">
      <button
        id="expandSidebarNamesBtn"
        type="button"
        class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center hover:brightness-105 active:scale-95 transition shadow-sm shrink-0 cursor-pointer"
        aria-label="Toggle Full Sidebar / Compact Mode"
        title="Open Full Menu with Text / Collapse to Icons"
      >
        <svg id="expandSidebarNamesIcon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>

  <!-- Upper Desktop Header (Visible when desktop sidebar is expanded to show names) -->
  <div id="upperDesktopMenuHeader" class="side-menu-glass hidden items-center justify-between px-3 py-2 w-full rounded-2xl">
    <div class="min-w-0">
      <span class="sidebar-header-kicker text-[9px] font-extrabold uppercase tracking-wider block">Side Menu</span>
      <span id="desktopUpperActiveName" class="text-xs font-bold text-[var(--text-primary)] truncate block"><?php echo htmlspecialchars($activeLabel); ?></span>
    </div>
    <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
  </div>

  <!-- 2. MAIN VERTICAL SIDE MENU PILL (Unified Department Portal Navigation Across All Pages) -->
  <div class="my-auto flex flex-col items-center w-full">
    <nav id="sideMenuNavPill" class="desktop-pill-nav side-menu-glass rounded-full p-1.5 flex flex-col items-center gap-1.5 transition-all duration-300">

      <!-- Nav 1: Smart Dashboard -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" style="--stagger: 1;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'dashboard') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Smart Dashboard">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'dashboard') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Smart Dashboard</span>
        </span>
      </a>

      <!-- Nav 2: Academic Calendar -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>calendar" style="--stagger: 2;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'calendar') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Academic Calendar">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'calendar') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Academic Calendar</span>
        </span>
      </a>

      <!-- Nav 3: Timetable Management -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>timetable" style="--stagger: 3;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'timetable') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Timetable Schedules">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'timetable') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Timetables</span>
        </span>
      </a>

      <!-- Nav 4: Workload & CTL Report Submission -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>report" style="--stagger: 4;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'report') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Daily Workload Report">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'report') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Workload Reports</span>
        </span>
      </a>

      <!-- Nav 5: CTL Activity Dashboard -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>ctlactivity" style="--stagger: 5;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'ctlactivity') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="CTL Activity Dashboard">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'ctlactivity') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="9" y1="3" x2="9" y2="21"/>
            <line x1="15" y1="3" x2="15" y2="21"/>
            <line x1="3" y1="9" x2="21" y2="9"/>
            <line x1="3" y1="15" x2="21" y2="15"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">CTL Activity</span>
        </span>
      </a>

      <!-- Nav 6: CTL Activity Drive -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>ctldrive" style="--stagger: 6;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'ctldrive') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="CTL Activity Drive">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'ctldrive') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">CTL Drive</span>
        </span>
      </a>

      <!-- Nav 7: Faculty List -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>faculty" style="--stagger: 7;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'faculty') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Faculty List">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'faculty') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Faculty List</span>
        </span>
      </a>

    </nav>
  </div>

  <!-- 3. BOTTOM VERTICAL PILL SET (Settings & User Profile) -->
  <div id="bottomCapsuleDock" class="samsung-shortcut-item side-menu-glass rounded-full p-1.5 flex flex-col items-center justify-center gap-2 transition-all duration-300" style="--stagger: 7;">
    <!-- 1. Setting Icon -->
    <button
      id="openSettingsBtn"
      type="button"
      class="sidebar-bottom-btn w-10 h-10 rounded-full text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 flex items-center justify-center gap-2.5 transition shrink-0 cursor-pointer"
      title="Theme Color & Portal Settings"
      aria-label="Settings"
    >
      <span class="samsung-squircle w-9 h-9 rounded-full side-menu-icon-glass flex items-center justify-center shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0-2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
      </span>
      <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2 text-left">
        <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap text-[var(--text-primary)]">Settings & Theme</span>
      </span>
    </button>

    <!-- 2. User Profile Icon -->
    <button
      id="openProfileBtn"
      type="button"
      class="sidebar-bottom-btn w-10 h-10 rounded-full text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 flex items-center justify-center gap-2.5 focus:outline-none group transition shrink-0 cursor-pointer"
      title="User Profile: Imran Jakir"
      aria-label="User Profile"
    >
      <span class="samsung-squircle w-9 h-9 rounded-full bg-white text-[#0A0909] border border-black/10 shadow-sm flex items-center justify-center overflow-hidden shrink-0 relative">
        <img
          src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA=="
          alt="User Profile"
          class="w-full h-full object-cover rounded-full"
        />
      </span>
      <div class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2 text-left">
        <p class="sidebar-nav-label font-bold text-[12.5px] text-[var(--text-primary)] leading-tight whitespace-nowrap">Imran Jakir</p>
        <p class="sidebar-user-role text-[11px] font-bold whitespace-nowrap mt-0.5">Lead Agronomist</p>
      </div>
    </button>
  </div>
</aside>
