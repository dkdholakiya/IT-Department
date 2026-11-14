<?php
$page_title = 'Report Management Directory — CE & IT Department';
$page_description = 'Departmental registry of faculty activity reports, workshops, seminars, and workload logs.';
$page_css = [
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'assets/vendor/bootstrap-icons/bootstrap-icons.min.css',
    'assets/css/report.css',
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
    $active_page = 'report';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Workload Reports';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + DIRECTORY WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Report Management System';
      $nav_subtitle = 'CE & IT Department • Academic Events, Talks & Documentation';
      $nav_search_placeholder = 'Search title, faculty, category, venue...';
      $nav_back_url = function_exists('base_url') ? base_url('/dashboard') : './dashboard';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS DIRECTORY CONTAINER ================= -->
      <main class="rp-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-4 lg:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300 gap-3 glass-card">

        <!-- 1. TOP KPI SUMMARY METRICS & UPPER ACTION CONTROLS -->
        <div class="rp-header-toolbar flex flex-col 2xl:flex-row 2xl:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">

          <?php if ($canViewAll ?? false): ?>
          <!-- 4 KPI Summary Cards for Super Admin, HOD, and Sub Admin -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 flex-1 min-w-0">

            <!-- KPI 1: Total Reports -->
            <div class="zs-kpi-card zs-kpi-primary p-2.5 sm:p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block truncate">Total Reports</span>
                <div class="text-base sm:text-xl font-bold font-serif text-[var(--text-primary)] mt-0.5 leading-none" id="kpiTotal">0</div>
                <span class="text-[9.5px] text-[var(--color-primary)] font-semibold mt-1 block truncate">Master Registry</span>
              </div>
              <div class="w-8 h-8 rounded-xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center shrink-0">
                <i class="bi bi-file-earmark-text text-sm"></i>
              </div>
            </div>

            <!-- KPI 2: IT Department Reports -->
            <div class="zs-kpi-card zs-kpi-blue p-2.5 sm:p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 block truncate">IT Department</span>
                <div class="text-base sm:text-xl font-bold font-serif text-blue-600 dark:text-blue-400 mt-0.5 leading-none" id="kpiIt">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Information Technology</span>
              </div>
              <div class="w-8 h-8 rounded-xl bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <i class="bi bi-laptop text-sm"></i>
              </div>
            </div>

            <!-- KPI 3: CE Department Reports -->
            <div class="zs-kpi-card zs-kpi-amber p-2.5 sm:p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block truncate">CE Department</span>
                <div class="text-base sm:text-xl font-bold font-serif text-amber-600 dark:text-amber-400 mt-0.5 leading-none" id="kpiCe">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Computer Engineering</span>
              </div>
              <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <i class="bi bi-cpu text-sm"></i>
              </div>
            </div>

            <!-- KPI 4: My Reports -->
            <div class="zs-kpi-card zs-kpi-rose p-2.5 sm:p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400 block truncate">My Submissions</span>
                <div class="text-base sm:text-xl font-bold font-serif text-rose-600 dark:text-rose-400 mt-0.5 leading-none" id="kpiMy">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Authored By Me</span>
              </div>
              <div class="w-8 h-8 rounded-xl bg-rose-500/15 text-rose-500 flex items-center justify-center shrink-0">
                <i class="bi bi-person-check text-sm"></i>
              </div>
            </div>

          </div>
          <?php else: ?>
          <!-- Regular Faculty View: Only My Submissions KPI Card -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-1 min-w-0 max-w-sm">

            <!-- KPI: My Submissions -->
            <div class="zs-kpi-card zs-kpi-rose p-2.5 sm:p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
              <div class="min-w-0 flex-1">
                <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400 block truncate">My Submissions</span>
                <div class="text-base sm:text-xl font-bold font-serif text-rose-600 dark:text-rose-400 mt-0.5 leading-none" id="kpiMy">0</div>
                <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Authored By Me</span>
              </div>
              <div class="w-8 h-8 rounded-xl bg-rose-500/15 text-rose-500 flex items-center justify-center shrink-0">
                <i class="bi bi-person-check text-sm"></i>
              </div>
            </div>

          </div>
          <?php endif; ?>

          <!-- Action Controls Group: Search, Filter (Left of Refresh), Refresh, Export Excel, + Create Report -->
          <div class="flex items-center gap-1.5 sm:gap-2 self-start 2xl:self-center flex-wrap shrink-0">

            <!-- Upper Side Search Bar -->
            <div class="relative flex items-center h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill border border-white/80 dark:border-white/10 shadow-sm w-40 sm:w-48 lg:w-56 shrink-0 transition focus-within:ring-1 focus-within:ring-[var(--color-primary)]">
              <i class="bi bi-search text-xs text-[var(--text-muted)] shrink-0 mr-1.5"></i>
              <input
                type="text"
                id="reportSearchInput"
                class="w-full bg-transparent text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none"
                placeholder="Search reports..."
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
              title="Filter and Refine Reports"
            >
              <i class="bi bi-funnel text-xs"></i>
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
              <i class="bi bi-arrow-clockwise text-xs"></i>
              <span class="hidden md:inline">Refresh</span>
            </button>

            <!-- Export to Excel Button -->
            <?php if ($canExportReport ?? true): ?>
            <button
              type="button"
              id="exportExcelBtn"
              class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              title="Export report registry as Excel"
            >
              <i class="bi bi-file-earmark-spreadsheet text-xs"></i>
              <span class="hidden xs:inline">Export</span><span class="hidden md:inline">&nbsp;Excel</span>
            </button>
            <?php endif; ?>

            <!-- + Create New Report Button -->
            <?php if ($canCreateReport ?? true): ?>
            <a
              href="<?php echo function_exists('base_url') ? base_url('/report-create') : './report-create'; ?>"
              class="h-7 sm:h-8 px-3 sm:px-3.5 rounded-xl bg-[var(--color-primary)] text-white text-[10.5px] sm:text-[11px] font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer text-decoration-none"
              title="Open Report Creation Wizard"
            >
              <i class="bi bi-plus-lg text-xs"></i>
              <span>Create Report</span>
            </a>
            <?php endif; ?>

          </div>

        </div>

        <!-- 2. MASTER REPORT DATA TABLE -->
        <div class="flex-1 min-h-0 min-w-0 overflow-y-auto overflow-x-auto rp-custom-scroll rounded-2xl border border-black/5 dark:border-white/10 bg-white/40 dark:bg-black/20">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 z-10 bg-black/5 dark:bg-[#1a2332] backdrop-blur-md border-b border-black/5 dark:border-white/10">
              <tr class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)]">
                <th class="py-2.5 px-3 w-12 text-center">#</th>
                <th class="py-2.5 px-3">ID</th>
                <th class="py-2.5 px-3">Activity Title & Objective</th>
                <th class="py-2.5 px-3">Category & Dept</th>
                <th class="py-2.5 px-3">Faculty In-Charge</th>
                <th class="py-2.5 px-3">Date & Time</th>
                <th class="py-2.5 px-3">Status</th>
                <th class="py-2.5 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="reportTableBody">
              <!-- Dynamically rendered via assets/js/report-index.js -->
            </tbody>
          </table>
        </div>

        <!-- 3. BOTTOM PAGINATION & METRICS FOOTER -->
        <div class="pt-2 border-t border-black/5 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
          <!-- Summary info text & rows-per-page -->
          <div class="flex items-center gap-3 text-xs text-[var(--text-muted)]">
            <span id="tableInfoText" class="font-semibold select-none">
              Loading report records...
            </span>

            <span id="totalLogsBadge" class="hidden sm:inline-block px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-[var(--color-primary)]/15 text-[var(--color-primary)]"></span>

            <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-black/10 dark:border-white/10">
              <label for="pageSizeSelect" class="font-medium text-[11px] select-none">Rows:</label>
              <select
                id="pageSizeSelect"
                class="px-2 py-0.5 rounded-lg glass-pill font-bold text-xs text-[var(--text-primary)] border border-white/80 dark:border-white/10 focus:outline-none cursor-pointer"
              >
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">All</option>
              </select>
            </div>
          </div>

          <!-- Page Navigation Buttons -->
          <div id="paginationControls" class="flex items-center gap-1 shrink-0">
            <!-- Rendered dynamically via assets/js/report-index.js -->
          </div>
        </div>

      </main>

    </div>
  </div>

  <!-- ================= BOOTSTRAP 5 OFFCANVAS RIGHT: REPORT FILTER & REFINE DRAWER ================= -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <!-- Drawer Header -->
    <div class="offcanvas-header px-5 py-4 border-b border-black/5 dark:border-white/10 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[var(--color-primary)]/20 to-[var(--color-primary)]/5 border border-[var(--color-primary)]/30 text-[var(--color-primary)] flex items-center justify-center shadow-sm shrink-0">
          <i class="bi bi-funnel text-lg"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h5 class="text-sm font-bold font-serif text-[var(--text-primary)] leading-tight" id="offcanvasRightLabel">
              Filter & Sort Reports
            </h5>
            <span id="offcanvasActiveBadge" class="hidden px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-[var(--color-primary)]/15 text-[var(--color-primary)] border border-[var(--color-primary)]/30">
              Active
            </span>
          </div>
          <p class="text-[11px] text-[var(--text-muted)] mt-0.5 leading-tight">Refine workload and activity report logs</p>
        </div>
      </div>
      <button type="button" class="w-8 h-8 rounded-full glass-pill hover:bg-black/10 dark:hover:bg-white/10 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition active:scale-95 cursor-pointer" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="bi bi-x-lg text-xs"></i>
      </button>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-5 space-y-5 flex flex-col justify-between overflow-y-auto">
      <div class="space-y-5">

        <!-- 1. Department Filter Cards -->
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
                  <i class="bi bi-grid-fill text-sm"></i>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">All Departments</div>
                  <div class="text-[10px] text-[var(--text-muted)]">Combined IT & CE reports</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>

            <!-- IT Dept -->
            <button type="button" id="deptFilterIt" class="wl-drawer-card" data-dept="Information Technology">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                  <i class="bi bi-laptop text-sm"></i>
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
                  <i class="bi bi-cpu text-sm"></i>
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

        <!-- 2. Activity Category Filter Dropdown -->
        <div>
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
            Activity Category
          </label>
          <div class="relative">
            <select
              id="categoryFilterSelect"
              class="w-full h-10 px-3 pr-8 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-xs font-semibold text-[var(--text-primary)] focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer"
            >
              <option value="">All Categories</option>
              <option value="academic">Academic Event / Guest Lecture</option>
              <option value="expert_talk">Expert Talk / Seminar</option>
              <option value="workshop">Workshop / Hands-on</option>
              <option value="industrial_visit">Industrial Visit</option>
              <option value="training_placement">Placement (RLM / PLM)</option>
              <option value="startup">Startup / SSIP / IIC</option>
              <option value="research">Research / Publication</option>
              <option value="international">International Collab</option>
              <option value="central">Central / University Event</option>
              <option value="other">Other Activity</option>
            </select>
          </div>
        </div>

        <!-- 3. Faculty Filter Dropdown -->
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

        <!-- 4. Activity Session Date Filter -->
        <div>
          <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block mb-2">
            Activity Session Date
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
          <i class="bi bi-arrow-counterclockwise text-xs"></i>
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

  <!-- ================= POPUP MODAL: REPORT DETAIL INSPECTOR ================= -->
  <div id="viewReportModal" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-5">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-4xl max-h-[90vh] rounded-3xl relative shadow-2xl border border-white/80 dark:border-white/10 flex flex-col overflow-hidden">

      <!-- Modal Header -->
      <div class="px-5 py-3.5 border-b border-black/5 dark:border-white/10 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center">
            <i class="bi bi-file-earmark-text text-sm"></i>
          </div>
          <div>
            <h3 class="text-sm font-bold text-[var(--text-primary)]">Workload & Activity Report</h3>
            <span class="text-[10px] text-[var(--text-muted)] font-mono" id="modalReportId">#rep_000</span>
          </div>
        </div>
        <button type="button" id="closeDetailModalBtn" class="w-8 h-8 rounded-xl hover:bg-black/5 dark:hover:bg-white/10 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition cursor-pointer">
          <i class="bi bi-x-lg text-xs"></i>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-5 overflow-y-auto rp-custom-scroll flex-1 min-h-0" id="detailModalBody">
        <!-- Dynamically populated via assets/js/report-index.js -->
      </div>

      <!-- Modal Footer -->
      <div class="px-5 py-3 border-t border-black/5 dark:border-white/10 flex items-center justify-between shrink-0 bg-black/5 dark:bg-white/5">
        <span class="text-[10.5px] text-[var(--text-muted)]">CE & IT Department Academic Documentation</span>
        <button type="button" id="closeDetailModalBottomBtn" class="h-8 px-4 rounded-xl glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-black/10 dark:hover:bg-white/10 transition cursor-pointer">
          Close Window
        </button>
      </div>

    </div>
  </div>

  <!-- ================= POPUP MODAL: DELETE CONFIRMATION ================= -->
  <div id="deleteReportModal" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-sm p-5 rounded-3xl relative shadow-2xl border border-white/80 dark:border-white/10 text-center">
      <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-rose-500 mx-auto flex items-center justify-center mb-3">
        <i class="bi bi-trash3 text-xl"></i>
      </div>
      <h3 class="text-base font-bold text-[var(--text-primary)]">Delete Report Record?</h3>
      <p class="text-xs text-[var(--text-muted)] mt-1 mb-2">
        Are you sure you want to permanently delete this report from the master registry?
      </p>
      <div class="text-xs font-bold text-rose-500 mb-4 p-2 rounded-xl bg-rose-500/10 truncate" id="deleteTargetTitle">
        #rep_000
      </div>
      <div class="flex items-center justify-center gap-2">
        <button type="button" id="cancelDeleteBtn" class="h-9 px-4 rounded-xl glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-black/10 dark:hover:bg-white/10 transition cursor-pointer">
          Cancel
        </button>
        <button type="button" id="confirmDeleteBtn" class="h-9 px-4 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition shadow-sm cursor-pointer">
          Yes, Delete Report
        </button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') : 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'; ?>"></script>

  <!-- Shared Layout Scripts (Samsung Edge Sidebar & Top Navbar) -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>

  <!-- Shared Faculty Database -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>

  <!-- Server-Injected State & Permissions -->
  <script>
    window.initialReportRecords = <?php echo json_encode($records ?? []); ?>;
    window.canViewAllFaculty = <?php echo ($canViewAll ?? false) ? 'true' : 'false'; ?>;
    window.canCreateReport = <?php echo ($canCreateReport ?? false) ? 'true' : 'false'; ?>;
    window.canEditReport = <?php echo ($canEditReport ?? false) ? 'true' : 'false'; ?>;
    window.canDeleteReport = <?php echo ($canDeleteReport ?? false) ? 'true' : 'false'; ?>;
    window.canExportReport = <?php echo ($canExportReport ?? false) ? 'true' : 'false'; ?>;
    window.baseUrl = <?php echo json_encode(function_exists('base_url') ? base_url('/') : '/'); ?>;
    window.loggedInUser = <?php echo json_encode([
        'id'          => $currentUser['id'] ?? '',
        'name'        => $currentUser['name'] ?? '',
        'initials'    => $currentUser['initials'] ?? '',
        'email'       => $currentUser['email'] ?? '',
        'department'  => $currentUser['department'] ?? '',
        'designation' => $currentUser['designation'] ?? 'Faculty Member',
        'empId'       => $currentUser['empId'] ?? ''
    ]); ?>;
  </script>

  <!-- Report Directory Controller Script -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/report-index.js') : 'assets/js/report-index.js'; ?>"></script>
</body>
</html>
