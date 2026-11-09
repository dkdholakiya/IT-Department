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
  'dashboard'           => 'Smart Dashboard',
  'calendar'            => 'Academic Calendar',
  'timetable'           => 'Timetable Schedules',
  'workload-summary'    => 'Faculty Workload Audit',
  'report'              => 'Workload Reports',
  'ctlactivity'         => 'CTL Activity Dashboard',
  'ctldrive'            => 'Drive Folder Scanner',
  'faculty'             => 'Faculty List',
  'zero-student-report' => 'Zero Student Log',
  'student-timetable'   => 'Student Timetable',
  'syllabus-papers'     => 'Syllabus & Exam Papers',
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
  <!-- Instant Desktop Sidebar Mode Evaluator (Synchronous DOM setup before child rendering) -->
  <script>
    (function() {
      try {
        var m = localStorage.getItem('portal_sidebar_desktop_mode');
        if (window.innerWidth >= 1024) {
          var sidebar = document.getElementById('mainSidebar');
          if (sidebar) {
            if (m === 'expanded') {
              sidebar.classList.add('desktop-expanded');
            } else if (m === 'hidden') {
              sidebar.classList.add('lg:hidden');
            }
          }
        }
      } catch (e) {}
    })();
  </script>
  <!-- 0. TOP AI BRAND ICON CAPSULE (Desktop & Mobile AI Model Logo with Sidebar Glass Effect) -->
  <div id="sidebarHomeCapsule" class="samsung-shortcut-item side-menu-glass rounded-full w-12 h-12 flex items-center justify-center self-center mx-auto shrink-0 cursor-pointer overflow-hidden transition-all duration-300" style="--stagger: 0;">
    <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" id="brandHomeBtn" class="w-full h-full rounded-full flex items-center justify-center group shrink-0" title="Return to Main Dashboard">
      <div class="sidebar-ai-float relative w-8 h-6 flex items-center justify-center shrink-0">
        <svg viewBox="0 0 46 32" class="sidebar-ai-robot w-7 h-5 overflow-visible" fill="none">
          <defs>
            <linearGradient id="sidebarAiLogoRimGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="var(--color-primary-light, #9b3dff)" />
              <stop offset="100%" stop-color="var(--color-primary, #2ca9ff)" />
            </linearGradient>
          </defs>
          <!-- Outer white/pearl capsule shell -->
          <rect x="1" y="1" width="44" height="30" rx="15" fill="#ffffff" stroke="var(--glass-border-strong, rgba(16, 185, 129, 0.45))" stroke-width="1.2" />
          <!-- Dynamic colored rim matching theme primary -->
          <rect x="3.8" y="3.8" width="38.4" height="24.4" rx="12.2" fill="url(#sidebarAiLogoRimGrad)" />
          <!-- Deep black glossy visor -->
          <rect x="6.6" y="6.6" width="32.8" height="18.8" rx="9.4" fill="#08080d" />
          <!-- Two white capsule eyes with lifelike blink -->
          <rect class="sidebar-ai-eye" x="14.2" y="10.8" width="5.0" height="10.4" rx="2.5" fill="#ffffff" />
          <rect class="sidebar-ai-eye" x="26.8" y="10.8" width="5.0" height="10.4" rx="2.5" fill="#ffffff" />
        </svg>
      </div>
    </a>
  </div>
  <!-- 1. UPPER CONTROL HEADER PILL (Mobile Header: Brand Icon, Menu Title & Sleek Expand Button) -->
  <div id="upperSideMenuHeader" class="samsung-shortcut-item side-menu-glass flex lg:hidden items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-full w-full" style="--stagger: 0;">
    <!-- Left Brand & Active Menu Title (Visible when Expanded with Side Menu Names) -->
    <div class="expanded-only expanded-flex items-center gap-2 min-w-0 flex-1">
      <div class="w-7 h-7 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center shadow-sm shrink-0">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/>
          <rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>
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

  <!-- 2. MAIN VERTICAL SIDE MENU PILL (Unified Department Portal Navigation Across All Pages) -->
  <div class="my-auto flex flex-col items-center w-full">
    <nav id="sideMenuNavPill" class="desktop-pill-nav side-menu-glass rounded-full p-1.5 flex flex-col items-center gap-1.5 transition-all duration-300">

      <!-- Nav 1: Smart Dashboard (Bento Space Dashboard) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" style="--stagger: 1;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'dashboard') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Smart Dashboard">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'dashboard') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="9" rx="1.5"/>
            <rect x="14" y="3" width="7" height="5" rx="1.5"/>
            <rect x="14" y="12" width="7" height="9" rx="1.5"/>
            <rect x="3" y="16" width="7" height="5" rx="1.5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Smart Dashboard</span>
        </span>
      </a>

      <!-- Nav 2: Academic Calendar (Calendar with Binder Pins & Date Dots) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>calendar" style="--stagger: 2;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'calendar') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Academic Calendar">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'calendar') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2.5"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke-width="2.5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Academic Calendar</span>
        </span>
      </a>

      <!-- Nav 3: Timetable Management (Calendar Schedule with Live Clock) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>timetable" style="--stagger: 3;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'timetable') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Timetable Schedules">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'timetable') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/>
            <path d="M16 2v4M8 2v4M3 10h18"/>
            <circle cx="16.5" cy="16.5" r="5"/>
            <polyline points="16.5 14.5 16.5 16.5 18 17.5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Timetables</span>
        </span>
      </a>

      <!-- Nav 4: Workload & CTL Report Submission (Document with Workload Analytics Bars) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>report" style="--stagger: 4;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'report') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Daily Workload Report">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'report') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <path d="M8 17v-2M12 17v-4M16 17v-6"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Workload Reports</span>
        </span>
      </a>

      <!-- Nav 5: CTL Activity Dashboard (Live Activity Pulse Monitor) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>ctlactivity" style="--stagger: 5;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'ctlactivity') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="CTL Activity Dashboard">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'ctlactivity') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">CTL Activity</span>
        </span>
      </a>

      <!-- Nav 6: Drive Folder Scanner (Folder with Integrated Scanner Lens) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>ctldrive" style="--stagger: 6;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'ctldrive') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Drive Folder Scanner">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'ctldrive') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/>
            <circle cx="12" cy="13" r="2.5"/>
            <path d="m14 15 2.5 2.5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Drive Scanner</span>
        </span>
      </a>

      <!-- Nav 7: Faculty List (Academic Professors & Faculty Group) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>faculty" style="--stagger: 7;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'faculty') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Faculty List">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'faculty') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Faculty List</span>
        </span>
      </a>

      <!-- Nav 8: Zero Student Log (Student Attendance Incident / Absentee Log) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>zero-student-report" style="--stagger: 8;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'zero-student-report') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Zero Student Log">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'zero-student-report') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="17" y1="8" x2="22" y2="13"/>
            <line x1="22" y1="8" x2="17" y2="13"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Zero Student Log</span>
        </span>
      </a>

      <!-- Nav 9: Student Timetable (Academic Graduation Cap) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>student-timetable" style="--stagger: 9;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'student-timetable') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Student Timetables">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'student-timetable') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Student Timetable</span>
        </span>
      </a>

      <!-- Nav 10: Syllabus & Exam Papers (Dual-Page Open Curriculum Book) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>syllabus-papers" style="--stagger: 10;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition <?php echo ($activePage === 'syllabus-papers') ? 'bg-[var(--sidebar-active-bg)] text-white shadow-sm' : 'text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10'; ?>" title="Syllabus & Exam Papers">
        <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center <?php echo ($activePage === 'syllabus-papers') ? 'text-white' : 'text-current'; ?> shrink-0">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
          </svg>
        </span>
        <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
          <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Syllabus & Papers</span>
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
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0-2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
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
