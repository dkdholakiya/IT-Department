<?php
$page_title = 'Zero Student Log Directory — CE & IT Department';
$page_description = 'Incident directory of timetable sessions where zero students were present as per academic timetable.';
$page_css = [
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'assets/css/zero-student-report.css',
];
$extra_head = '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>' .
              '<script src="' . (function_exists('v_asset') ? v_asset('assets/vendor/xlsx/xlsx.full.min.js') : 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js') . '"></script>';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Calendar, CTL Activity, Timetable & Faculty (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'zero-student-report';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Zero Student Log';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + DIRECTORY WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Zero Student Reports';
      $nav_subtitle = 'CE & IT Department • Incident Logs & Registry';
      $nav_search_placeholder = 'Search faculty, subjects, rooms, class...';
      $nav_back_url = function_exists('base_url') ? base_url('/dashboard') : './dashboard';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS DIRECTORY CONTAINER ================= -->
      <main class="zs-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-4 lg:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300 gap-3">

        <!-- 1. TOP KPI SUMMARY METRICS & ACTION TOOLBAR (Replaces Redundant Page Title) -->
        <div class="zs-header-toolbar flex flex-col 2xl:flex-row 2xl:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">

          <!-- 4 KPI Summary Cards (In Place of Redundant Page Title) -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 flex-1 min-w-0">

            <!-- KPI 1: Total Incidents -->
            <div class="zs-kpi-card zs-kpi-primary">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block truncate">Total Incidents</span>
                <div class="text-base sm:text-xl font-bold font-serif text-[var(--text-primary)] mt-0.5 leading-none" id="kpiTotal">0</div>
                <span class="text-[9.5px] text-[var(--color-primary)] font-semibold mt-1 block truncate">Master Registry</span>
              </div>
              <div class="zs-kpi-icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                </svg>
              </div>
            </div>

            <!-- KPI 2: IT Department Incidents -->
            <div class="zs-kpi-card zs-kpi-blue">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 block truncate">IT Department</span>
                <div class="text-base sm:text-xl font-bold font-serif text-blue-600 dark:text-blue-400 mt-0.5 leading-none" id="kpiIt">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Information Technology</span>
              </div>
              <div class="zs-kpi-icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                </svg>
              </div>
            </div>

            <!-- KPI 3: CE Department Incidents -->
            <div class="zs-kpi-card zs-kpi-amber">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block truncate">CE Department</span>
                <div class="text-base sm:text-xl font-bold font-serif text-amber-600 dark:text-amber-400 mt-0.5 leading-none" id="kpiCe">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Computer Engineering</span>
              </div>
              <div class="zs-kpi-icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>
                </svg>
              </div>
            </div>

            <!-- KPI 4: Today's Incidents -->
            <div class="zs-kpi-card zs-kpi-rose">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400 block truncate">Today's Logs</span>
                <div class="text-base sm:text-xl font-bold font-serif text-rose-600 dark:text-rose-400 mt-0.5 leading-none" id="kpiToday">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Today's Sessions</span>
              </div>
              <div class="zs-kpi-icon-wrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
              </div>
            </div>

          </div>

          <!-- Hidden badge to prevent JS null errors -->
          <span id="totalLogsBadge" class="hidden" aria-hidden="true"></span>

          <!-- Action Controls Group: Search, Filter (Left of Refresh), Refresh, Export Excel, + Log Zero Class -->
          <div class="flex items-center gap-1.5 sm:gap-2 self-start 2xl:self-center flex-wrap shrink-0">

            <!-- Search Bar -->
            <div class="relative flex items-center h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill border border-white/80 dark:border-white/10 shadow-sm w-36 sm:w-44 lg:w-48 shrink-0 transition focus-within:ring-1 focus-within:ring-[var(--color-primary)]">
              <svg class="w-3.5 h-3.5 text-[var(--text-muted)] shrink-0 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <input
                type="text"
                id="recordSearchInput"
                class="w-full bg-transparent text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none"
                placeholder="Search incidents..."
                autocomplete="off"
              />
              <button
                type="button"
                id="clearSearchBtn"
                class="hidden ml-1 text-[var(--text-muted)] hover:text-[var(--text-primary)] text-xs font-bold cursor-pointer"
                title="Clear search"
              >
                ✕
              </button>
            </div>

            <!-- Filter Button (Left side of Refresh button, triggers Offcanvas Right) -->
            <button
              type="button"
              id="filterOffcanvasBtn"
              class="relative h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasRight"
              aria-controls="offcanvasRight"
              title="Filter and Refine Incidents"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.539.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.378-1.007.917-1.096A48.32 48.32 0 0 1 12 3Z" />
              </svg>
              <span>Filter</span>
              <span id="activeFilterBadge" class="hidden w-2 h-2 rounded-full bg-[var(--color-primary)] ring-2 ring-white dark:ring-gray-900"></span>
            </button>

            <!-- Refresh Button -->
            <button
              type="button"
              id="refreshRecordsBtn"
              class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 shrink-0 cursor-pointer"
              title="Reload live records from server"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
              </svg>
              <span class="hidden md:inline">Refresh</span>
            </button>

            <!-- Export Excel Button -->
            <?php if (!class_exists('Core\Auth') || \Core\Auth::can('zero_student.export')): ?>
            <button
              type="button"
              id="exportExcelBtn"
              class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              title="Export filtered records to Excel"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
              <span class="hidden xs:inline">Export</span><span class="hidden md:inline">&nbsp;Excel</span>
            </button>
            <?php endif; ?>

            <!-- + Log Zero Class (Directs to Create Page) -->
            <?php if (!class_exists('Core\Auth') || \Core\Auth::can('zero_student.create')): ?>
            <a
              href="<?php echo function_exists('base_url') ? base_url('/zero-student-create') : './zero-student-create'; ?>"
              class="h-7 sm:h-8 px-3 sm:px-3.5 rounded-xl bg-[var(--color-primary)] text-white text-[10.5px] sm:text-[11px] font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer decoration-transparent"
              title="Log a new Zero Student incident session"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
              <span>Create Zero Log</span>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- 4. MAIN SCROLLABLE INCIDENTS TABLE -->
        <div class="zs-dir-table-wrapper flex-1 min-h-0">
          <table class="zs-dir-table">
            <thead>
              <tr>
                <th class="w-10 text-center">#</th>
                <th>Date & Time</th>
                <th>Department</th>
                <th>Class / Sem</th>
                <th>Room</th>
                <th>Subject</th>
                <th>Faculty</th>
                <th class="text-center">Students</th>
                <th>Remarks</th>
                <th class="text-right pr-4">Action</th>
              </tr>
            </thead>
            <tbody id="recordsTableBody">
              <!-- Dynamically populated via assets/js/zero-student-index.js -->
            </tbody>
          </table>
        </div>

        <!-- 5. BOTTOM PAGINATION & METRICS FOOTER -->
        <div class="pt-2 border-t border-black/5 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
          <!-- Summary info text & rows-per-page -->
          <div class="flex items-center gap-3 text-xs text-[var(--text-muted)]">
            <span id="tableInfoText" class="font-semibold select-none">
              Loading incident records...
            </span>

            <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-black/10 dark:border-white/10">
              <label for="pageSizeSelect" class="font-medium text-[11px] select-none">Rows:</label>
              <select
                id="pageSizeSelect"
                class="px-2 py-0.5 rounded-lg glass-pill font-bold text-xs text-[var(--text-primary)] border border-white/80 dark:border-white/10 focus:outline-none cursor-pointer"
              >
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="all">All</option>
              </select>
            </div>
          </div>

          <!-- Page Navigation Buttons -->
          <div id="paginationControls" class="flex items-center gap-1 shrink-0">
            <!-- Rendered dynamically -->
          </div>
        </div>

      </main>

    </div><!-- /Right Column -->

  </div><!-- /Main Wrapper -->

  <!-- ================= POPUP MODAL: INCIDENT DETAIL VIEW ================= -->
  <div id="viewRecordModal" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-lg p-5 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 dark:border-white/10">
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-black/10 dark:border-white/10">
        <div>
          <h3 class="text-base sm:text-lg font-bold text-[var(--text-primary)]">Zero Attendance Incident Record</h3>
          <p class="text-xs text-[var(--text-muted)]" id="modalRecordId">Record ID: #---</p>
        </div>
        <button type="button" id="closeDetailModalBtn" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div id="detailModalBody" class="space-y-3 text-xs">
        <!-- Injected dynamically via JS -->
      </div>

      <div class="pt-4 mt-4 border-t border-black/10 dark:border-white/10 flex items-center justify-end gap-2">
        <button type="button" id="closeDetailModalBottomBtn" class="h-8 px-4 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- ================= POPUP MODAL: DELETE CONFIRMATION ================= -->
  <div id="deleteRecordModal" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-sm p-5 rounded-3xl relative shadow-2xl border border-white/80 dark:border-white/10 text-center">
      <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-rose-500 mx-auto flex items-center justify-center mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
        </svg>
      </div>
      <h3 class="text-base font-bold text-[var(--text-primary)]">Delete Incident Log?</h3>
      <p class="text-xs text-[var(--text-muted)] mt-1 mb-4">
        Are you sure you want to delete this incident record from the master JSON registry? This action cannot be undone.
      </p>
      <div class="flex items-center justify-center gap-2">
        <button type="button" id="cancelDeleteBtn" class="h-8 px-4 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          Cancel
        </button>
        <button type="button" id="confirmDeleteBtn" class="h-8 px-4 rounded-xl bg-rose-500 text-white font-bold text-xs hover:bg-rose-600 transition shadow-sm cursor-pointer">
          Delete Record
        </button>
      </div>
    </div>
  </div>

  <!-- ================= BOOTSTRAP 5 OFFCANVAS RIGHT: INCIDENT FILTER & REFINE DRAWER ================= -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <!-- Drawer Header -->
    <div class="offcanvas-header px-5 py-4 border-b border-black/5 dark:border-white/10 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[var(--color-primary)]/20 to-[var(--color-primary)]/5 border border-[var(--color-primary)]/30 text-[var(--color-primary)] flex items-center justify-center shadow-sm shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.539.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.378-1.007.917-1.096A48.32 48.32 0 0 1 12 3Z" />
          </svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h5 class="text-sm font-bold font-serif text-[var(--text-primary)] leading-tight" id="offcanvasRightLabel">
              Filter & Sort Incidents
            </h5>
            <span id="offcanvasActiveBadge" class="hidden px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-[var(--color-primary)]/15 text-[var(--color-primary)] border border-[var(--color-primary)]/30">
              Active
            </span>
          </div>
          <p class="text-[11px] text-[var(--text-muted)] mt-0.5 leading-tight">Refine zero attendance incident logs</p>
        </div>
      </div>
      <button type="button" class="w-8 h-8 rounded-full glass-pill hover:bg-black/10 dark:hover:bg-white/10 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition active:scale-95 cursor-pointer" data-bs-dismiss="offcanvas" aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-5 space-y-5 flex flex-col justify-between overflow-y-auto">
      <div class="space-y-5">

        <!-- 1. Department Filter -->
        <div>
          <div class="flex items-center justify-between mb-2.5">
            <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] flex items-center gap-1.5">
              <span>Department</span>
            </label>
            <span class="text-[11px] font-bold text-[var(--color-primary)]" id="activeDeptText">All Departments</span>
          </div>

          <div class="flex flex-col gap-2">
            <!-- All Departments -->
            <button type="button" id="deptFilterAll" class="wl-drawer-card active" data-dept="all">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">All Departments</div>
                  <div class="text-[10px] text-[var(--text-muted)]">Combined IT & CE incidents</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>

            <!-- IT Dept -->
            <button type="button" id="deptFilterIt" class="wl-drawer-card" data-dept="Information Technology">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">Information Technology</div>
                  <div class="text-[10px] text-[var(--text-muted)]">IT faculty members & sessions</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>

            <!-- CE Dept -->
            <button type="button" id="deptFilterCe" class="wl-drawer-card" data-dept="Computer Engineering">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">Computer Engineering</div>
                  <div class="text-[10px] text-[var(--text-muted)]">CE faculty members & sessions</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>
          </div>
        </div>

        <!-- 2. Faculty Filter Dropdown -->
        <div>
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
            Faculty Member
          </label>
          <div class="relative">
            <select
              id="facultyFilterSelect"
              class="w-full h-10 px-3 pr-8 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs font-semibold text-[var(--text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer"
            >
              <option value="">All Faculty Members</option>
              <!-- Dynamically populated via JS -->
            </select>
          </div>
        </div>

        <!-- 3. Semester Filter Dropdown -->
        <div>
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
            Academic Semester
          </label>
          <div class="relative">
            <select
              id="semFilterSelect"
              class="w-full h-10 px-3 pr-8 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs font-semibold text-[var(--text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer"
            >
              <option value="">All Semesters</option>
              <option value="1">Semester 1</option>
              <option value="3">Semester 3</option>
              <option value="5">Semester 5</option>
              <option value="7">Semester 7</option>
            </select>
          </div>
        </div>

        <!-- 4. Session Date Filter -->
        <div>
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
            Incident Session Date
          </label>
          <div class="relative">
            <input
              type="text"
              id="dateFilterInput"
              class="w-full h-10 px-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer"
              placeholder="Select session date..."
              autocomplete="off"
            />
          </div>
        </div>

      </div>

      <!-- Drawer Footer Actions -->
      <div class="pt-4 border-t border-black/5 dark:border-white/10 flex items-center gap-2.5">
        <button
          type="button"
          id="resetFiltersBtn"
          class="flex-1 h-9 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center justify-center gap-1.5 cursor-pointer"
          title="Reset all filters"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
          </svg>
          <span>Reset</span>
        </button>
        <button
          type="button"
          id="applyFiltersBtn"
          class="flex-1 h-9 rounded-xl bg-[var(--color-primary)] text-white font-bold text-xs hover:brightness-105 active:scale-95 transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
          data-bs-dismiss="offcanvas"
          title="Apply filter criteria and close drawer"
        >
          <span>Apply Filters</span>
        </button>
      </div>
    </div>
  </div>

  <?php
  $user = class_exists('Core\Auth') ? \Core\Auth::user() : null;
  $canViewAll = class_exists('Core\Auth') ? \Core\Auth::canViewAllFaculty() : true;
  $userInitials = strtoupper(trim($user['initials'] ?? ''));
  $userDept = $user['department'] ?? 'Information Technology';

  if ($canViewAll) {
      $usersFile = defined('BASE_PATH') ? BASE_PATH . '/config/users.json' : dirname(__DIR__, 3) . '/config/users.json';
      $rawUsers = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
      $facultyDataOutput = [];
      if (is_array($rawUsers)) {
          foreach ($rawUsers as $u) {
              $facultyDataOutput[] = [
                  'name'        => $u['name'] ?? '',
                  'initials'    => $u['initials'] ?? '',
                  'department'  => $u['department'] ?? '',
                  'designation' => $u['designation'] ?? ''
              ];
          }
      }
  } else {
      $facultyDataOutput = [
          [
              'name'        => $user['name'] ?? '',
              'initials'    => $userInitials,
              'department'  => $userDept,
              'designation' => $user['designation'] ?? 'Faculty Member'
          ]
      ];
  }
  ?>

  <!-- Injected JSON Records Data from PHP Backend -->
  <script>
    window.initialZeroRecords = <?php echo json_encode($records ?? [], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    window.canDeleteZeroStudent = <?php echo (class_exists('Core\Auth') && \Core\Auth::can('zero_student.delete')) ? 'true' : 'false'; ?>;
    window.canExportZeroStudent = <?php echo (class_exists('Core\Auth') && \Core\Auth::can('zero_student.export')) ? 'true' : 'false'; ?>;
    window.canViewAllFaculty = <?php echo $canViewAll ? 'true' : 'false'; ?>;
    window.loggedInUser = <?php echo json_encode([
        'name'        => $user['name'] ?? '',
        'initials'    => $userInitials,
        'email'       => $user['email'] ?? '',
        'department'  => $userDept,
        'designation' => $user['designation'] ?? 'Faculty Member'
    ], JSON_UNESCAPED_UNICODE); ?>;
    window.facultyData = <?php echo json_encode($facultyDataOutput, JSON_UNESCAPED_UNICODE); ?>;
    const facultyData = window.facultyData;
  </script>

  <!-- ================= APPLICATION JAVASCRIPT: Modular Architecture ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') : 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/zero-student-index.js') : 'assets/js/zero-student-index.js'; ?>"></script>
</body>
</html>
