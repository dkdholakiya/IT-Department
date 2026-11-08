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

        <!-- 1. TOP HEADER & CONTROLS TOOLBAR -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">
          <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="text-base sm:text-xl md:text-2xl font-bold font-serif tracking-tight text-[var(--text-primary)] leading-none">
                Zero Attendance Incident Directory
              </h2>
              <span id="totalLogsBadge" class="px-2.5 py-0.5 rounded-full glass-pill text-[10px] sm:text-[11px] font-bold text-[var(--color-primary)] border border-white/80 dark:border-white/10 shadow-sm shrink-0">
                Loading...
              </span>
            </div>
            <p class="text-[11px] sm:text-xs text-[var(--text-muted)] mt-1 hidden sm:block">
              Live registry of timetable sessions where zero students reported. All records stored in JSON format with instant filtering.
            </p>
          </div>

          <!-- Action Controls Group: Refresh, Export Excel, + Log Zero Class -->
          <div class="flex items-center gap-1.5 sm:gap-2 self-start sm:self-auto flex-wrap">
            <!-- Refresh Button -->
            <button
              type="button"
              id="refreshRecordsBtn"
              class="h-7 sm:h-8 px-2.5 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 shrink-0 cursor-pointer"
              title="Reload live records from server"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
              </svg>
              <span class="hidden md:inline">Refresh</span>
            </button>

            <!-- Export Excel Button -->
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

            <!-- + Log Zero Class (Directs to Create Page) -->
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
          </div>
        </div>

        <!-- 2. KPI SUMMARY STATS CARDS -->
        <div class="zs-kpi-grid shrink-0">
          <!-- Total Logs -->
          <div class="zs-kpi-card">
            <div class="zs-kpi-icon icon-blue">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <div class="zs-kpi-val" id="kpiTotal">0</div>
              <div class="zs-kpi-label">Total Incidents</div>
            </div>
          </div>

          <!-- IT Incidents -->
          <div class="zs-kpi-card">
            <div class="zs-kpi-icon icon-amber">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
              </svg>
            </div>
            <div class="min-w-0">
              <div class="zs-kpi-val" id="kpiIt">0</div>
              <div class="zs-kpi-label">IT Department</div>
            </div>
          </div>

          <!-- CE Incidents -->
          <div class="zs-kpi-card">
            <div class="zs-kpi-icon icon-indigo">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>
              </svg>
            </div>
            <div class="min-w-0">
              <div class="zs-kpi-val" id="kpiCe">0</div>
              <div class="zs-kpi-label">CE Department</div>
            </div>
          </div>

          <!-- Today's Incidents -->
          <div class="zs-kpi-card">
            <div class="zs-kpi-icon icon-rose">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <div class="zs-kpi-val" id="kpiToday">0</div>
              <div class="zs-kpi-label">Today's Logs</div>
            </div>
          </div>
        </div>

        <!-- 3. COMPREHENSIVE FILTER TOOLBAR -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2.5 shrink-0">
          <!-- Left: Filter Dropdowns & Segment -->
          <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5 flex-wrap sm:flex-nowrap">
            <!-- Department Segment Control -->
            <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0 gap-1">
              <button type="button" id="deptFilterAll" class="fac-seg-btn active">
                <span class="hidden md:inline">All Depts</span>
                <span class="md:hidden">All</span>
              </button>
              <button type="button" id="deptFilterIt" class="fac-seg-btn">
                <span class="hidden md:inline">IT</span>
                <span class="md:hidden">IT</span>
              </button>
              <button type="button" id="deptFilterCe" class="fac-seg-btn">
                <span class="hidden md:inline">CE</span>
                <span class="md:hidden">CE</span>
              </button>
            </div>

            <!-- Faculty Filter Dropdown -->
            <select id="facultyFilterSelect" class="zs-filter-select text-xs min-w-[150px] sm:min-w-[180px]">
              <option value="">All Faculty Members</option>
              <!-- Injected via JavaScript from facultyData.js -->
            </select>

            <!-- Semester Filter Dropdown -->
            <select id="semFilterSelect" class="zs-filter-select text-xs min-w-[110px]">
              <option value="">All Semesters</option>
              <option value="1">Semester 1</option>
              <option value="3">Semester 3</option>
              <option value="5">Semester 5</option>
              <option value="7">Semester 7</option>
            </select>

            <!-- Session Date Filter -->
            <input
              type="text"
              id="dateFilterInput"
              class="zs-filter-input text-xs w-[115px] sm:w-[130px]"
              placeholder="Filter by date..."
              autocomplete="off"
            />

            <!-- Reset Filters Button -->
            <button
              type="button"
              id="resetFiltersBtn"
              class="zs-filter-btn ghost text-xs shrink-0"
              title="Reset all filters"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              <span>Reset</span>
            </button>
          </div>

          <!-- Right: Search Input Bar -->
          <div class="w-full lg:w-72 shrink-0">
            <div class="h-8 sm:h-9 px-3 rounded-full glass-pill flex items-center gap-2 border border-white/80 dark:border-white/10 shadow-sm w-full">
              <svg class="w-3.5 h-3.5 text-[var(--text-muted)] shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <input
                type="text"
                id="recordSearchInput"
                class="fac-search-input w-full bg-transparent border-none outline-none text-xs text-[var(--text-primary)] placeholder-[var(--text-muted)]"
                placeholder="Search faculty, subject, class, room..."
                autocomplete="off"
              />
            </div>
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

  <!-- Injected JSON Records Data from PHP Backend -->
  <script>
    window.initialZeroRecords = <?php echo json_encode($records ?? [], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
  </script>

  <!-- ================= APPLICATION JAVASCRIPT: Modular Architecture ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/zero-student-index.js') : 'assets/js/zero-student-index.js'; ?>"></script>
</body>
</html>
