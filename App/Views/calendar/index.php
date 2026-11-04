<?php
$page_title = 'Academic Calendar — CE & IT Department';
$page_css = ['assets/css/calendar.css'];
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard Layout Exactly (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-16 sm:pt-20 lg:pt-3.5 pb-8 sm:pb-10 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col gap-3 lg:gap-3 overflow-x-hidden lg:overflow-hidden">

    <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
    <?php
    $nav_title = 'Academic Calendar';
    $nav_subtitle = 'CE & IT Department • Academic Year 2026';
    $nav_search_placeholder = 'Search holidays, events, schedules...';
    include dirname(__DIR__) . '/partials/navbar.php';
    ?>

    <!-- ================= MAIN WORKSPACE (SAMSUNG SHORTCUT SIDEBAR + CALENDAR DOCK) ================= -->
    <div class="flex-1 min-h-0 flex items-stretch gap-3.5 lg:gap-4 relative">

      <!-- Shared Samsung Edge Shortcut Sidebar -->
      <?php
      $active_page = 'calendar';
      $sidebar_mode = 'nav';
      $sidebar_active_label = 'Academic Calendar';
      include dirname(__DIR__) . '/partials/sidebar.php';
      ?>

      <!-- Main Responsive Grid: Calendar Matrix (Left 8 cols) & Event Inspector (Right 4 cols) -->
      <main class="cal-main-grid flex-1 min-h-0 min-w-0 grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 lg:gap-4 transition-all duration-300">

        <!-- ================= 1. CALENDAR MATRIX CARD (8 Columns on Desktop) ================= -->
        <section class="lg:col-span-8 glass-card p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative">

          <!-- Top Header Row: Navigation Controls + Department Switcher & Filter Pills -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3 pb-2.5 sm:pb-3 border-b border-black/5 dark:border-white/10 shrink-0">
            <!-- Left: Month/Year Nav + Title + Today Button -->
            <div class="flex items-center justify-between sm:justify-start w-full sm:w-auto gap-2 sm:gap-3">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" class="cal-nav-btn" id="prev-month-btn" aria-label="Previous Month" title="Previous Month">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                <button type="button" class="cal-nav-btn" id="next-month-btn" aria-label="Next Month" title="Next Month">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
              <h2 class="text-lg sm:text-xl md:text-2xl font-bold font-serif tracking-tight text-[var(--text-primary)] leading-none truncate" id="monthYearDisplay">
                <?php echo date('F Y'); ?>
              </h2>
              <button
                type="button"
                id="todayJumpBtn"
                class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-full glass-pill text-[11px] sm:text-xs font-bold text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1 shrink-0 ml-auto sm:ml-1"
                title="Jump to current date"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="9"/>
                  <path d="m9 12 2 2 4-4"/>
                </svg>
                Today
              </button>
            </div>

            <!-- Right: Department Toggle + Category Filter Pills -->
            <div class="flex items-center flex-wrap gap-1.5 sm:gap-2 w-full sm:w-auto justify-start sm:justify-end">
              <!-- Department Selector Segment -->
              <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0">
                <button
                  type="button"
                  id="dept-it-btn"
                  data-dept="Information Technology"
                  class="px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 bg-[var(--color-primary)] text-white shadow-sm"
                >
                  IT
                </button>
                <button
                  type="button"
                  id="dept-ce-btn"
                  data-dept="Computer Engineering"
                  class="px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 text-[var(--text-muted)] hover:text-[var(--text-primary)]"
                >
                  CE
                </button>
              </div>

              <!-- Filter Toggles -->
              <label class="cursor-pointer select-none inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full glass-pill text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition border border-white/70 shrink-0">
                <input type="checkbox" id="filter-public" checked class="sr-only peer">
                <span class="w-2 h-2 rounded-full bg-[#E11D48] transition-transform peer-checked:scale-125 peer-not-checked:opacity-30"></span>
                <span class="peer-not-checked:line-through peer-not-checked:opacity-50">Public</span>
              </label>

              <label class="cursor-pointer select-none inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full glass-pill text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition border border-white/70 shrink-0">
                <input type="checkbox" id="filter-reserved" checked class="sr-only peer">
                <span class="w-2 h-2 rounded-full bg-[#F59E0B] transition-transform peer-checked:scale-125 peer-not-checked:opacity-30"></span>
                <span class="peer-not-checked:line-through peer-not-checked:opacity-50">Reserved</span>
              </label>

              <label class="cursor-pointer select-none inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full glass-pill text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition border border-white/70 shrink-0">
                <input type="checkbox" id="filter-custom" checked class="sr-only peer">
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] transition-transform peer-checked:scale-125 peer-not-checked:opacity-30"></span>
                <span class="peer-not-checked:line-through peer-not-checked:opacity-50">Academic</span>
              </label>
            </div>
          </div>

          <!-- Calendar Matrix: Day Headers (Sun-Sat) & 7-Column Cell Grid -->
          <div class="flex-1 min-h-0 flex flex-col justify-between py-1 sm:py-2 px-1.5 overflow-y-auto overflow-x-hidden">
            <!-- 7-Day Header Row -->
            <div class="cal-grid-header">
              <div class="text-[#E11D48]">Sun</div>
              <div>Mon</div>
              <div>Tue</div>
              <div>Wed</div>
              <div>Thu</div>
              <div>Fri</div>
              <div class="text-[#E11D48]">Sat</div>
            </div>

            <!-- Dynamic 7x5/6 Date Cells -->
            <div class="cal-days-grid flex-1 items-stretch mb-2 sm:mb-2.5" id="calendarDaysGrid">
              <!-- Dynamic day cells rendered via JavaScript -->
            </div>
          </div>

          <!-- Bottom Legend Strip -->
          <div class="pt-2 sm:pt-2.5 border-t border-black/5 dark:border-white/10 flex flex-wrap items-center justify-between gap-2 sm:gap-3 text-xs shrink-0">
            <div class="flex flex-wrap items-center gap-x-3 sm:gap-x-4 gap-y-1 text-[10px] sm:text-[11px] text-[var(--text-muted)]">
              <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-2 h-2 rounded-full bg-[#E11D48]"></span> Public Holiday
              </span>
              <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span> Reserved Holiday
              </span>
              <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span> Department Event
              </span>
            </div>

            <span class="text-[10px] sm:text-[11px] text-[var(--text-muted)] font-medium hidden md:inline-block">
              Click any date to inspect scheduled items
            </span>
          </div>

        </section>

        <!-- ================= 2. EVENT DETAILS INSPECTOR CARD (4 Columns on Desktop) ================= -->
        <section id="dayOverviewSection" class="lg:col-span-4 glass-card p-3.5 sm:p-5 flex flex-col justify-between overflow-hidden relative min-h-[280px] sm:min-h-[320px] lg:min-h-0">

          <!-- Selected Date Header -->
          <div class="pb-2.5 sm:pb-3 border-b border-black/5 dark:border-white/10 shrink-0">
            <div class="flex items-center justify-between gap-2 mb-1">
              <span class="text-[10px] font-extrabold uppercase tracking-widest text-[var(--color-primary)]">Day Overview</span>
              <span id="selectedDateBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/5 dark:bg-white/10 text-[var(--text-muted)]">Selected</span>
            </div>
            <h3 class="text-base sm:text-lg lg:text-xl font-bold font-serif text-[var(--text-primary)] leading-tight truncate" id="selectedDateTitle">
              Select a Date
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-0.5" id="selectedDateSubtitle">
              0 Scheduled Items
            </p>
          </div>

          <!-- Scrollable Event Items List -->
          <div class="flex-1 min-h-0 my-2 pt-2.5 sm:pt-3 pb-3 px-1.5 overflow-y-auto overflow-x-hidden space-y-2.5 sm:space-y-3 max-h-[300px] sm:max-h-[360px] lg:max-h-none" id="selectedEventsList">
            <!-- Dynamic Event Cards rendered via JavaScript -->
          </div>

          <!-- Bottom Quick Actions inside Inspector -->
          <div class="pt-3 border-t border-black/5 dark:border-white/10 flex items-center justify-between gap-2 shrink-0">
            <button
              type="button"
              id="printCalendarBtn"
              class="flex-1 py-2 px-3 rounded-xl glass-pill font-bold text-xs text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center justify-center gap-1.5 shadow-sm"
              title="Print or save current month calendar"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 14h12v8H6z"/>
              </svg>
              Print Month
            </button>

            <button
              type="button"
              id="copyDateInfoBtn"
              class="py-2 px-3 rounded-xl glass-pill font-bold text-xs text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center justify-center gap-1 shadow-sm"
              title="Copy date information"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
              </svg>
            </button>
          </div>

        </section>

      </main>

    </div><!-- /Workspace -->

  </div><!-- /Main Wrapper -->

  <!-- ================= POPUP MODAL OVERLAY (Settings / Profile / AI Assistant) ================= -->
  <div id="modalOverlay" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-lg p-5 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80">
      <div class="flex items-center justify-between pb-3.5 border-b border-black/10">
        <div>
          <h3 id="modalTitle" class="text-base sm:text-lg font-bold text-[var(--text-primary)]">Settings</h3>
          <p id="modalSubtitle" class="text-xs text-[var(--text-muted)]">Configure portal options</p>
        </div>
        <button id="closeModalBtn" type="button" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div id="modalBody" class="py-4 text-xs">
        <!-- Injected dynamically -->
      </div>
    </div>
  </div>

  <!-- ================= APPLICATION JAVASCRIPT: Modular Architecture ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/calendarData.js') : 'assets/js/calendarData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/calendar.js') : 'assets/js/calendar.js'; ?>"></script>
</body>
</html>
