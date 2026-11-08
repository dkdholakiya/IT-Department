<?php
$compilerFile = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2)) . '/Services/timetable-compiler.php';
if (file_exists($compilerFile)) {
    require_once $compilerFile;
} elseif (file_exists('timetable-compiler.php')) {
    require_once 'timetable-compiler.php';
}

$excelFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/uploads/timetable/timetable.xlsx';
if (!file_exists($excelFile)) {
    $glob = glob((defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/uploads/timetable/*.xlsx');
    if (!empty($glob)) $excelFile = $glob[0];
}
$excelExists = file_exists($excelFile);
$jsDataFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/assets/js/timetableData.js';

// Auto-compile JS database whenever Excel file is uploaded or modified (with cooldown lock)
if ($excelExists && (!file_exists($jsDataFile) || filemtime($excelFile) > filemtime($jsDataFile))) {
    $lockFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/scratch/compile.lock';
    if (!file_exists($lockFile) || (time() - filemtime($lockFile) > 60)) {
        @touch($lockFile);
        if (function_exists('compileTimetableData')) {
            $res = compileTimetableData();
            if (!($res['success'] ?? false) && file_exists($jsDataFile)) {
                @touch($jsDataFile, filemtime($excelFile) + 1);
            }
        }
        @unlink($lockFile);
    }
}
$jsDataExists = file_exists($jsDataFile);

$page_title = 'Faculty Workload Audit & Summary — CE & IT Department';
$page_description = 'Official Faculty Workload & Timetable Audit Summary — Department of CE & IT';
$page_css = ['assets/css/workload.css'];
$extra_head = '<script src="' . (function_exists('v_asset') ? v_asset('assets/vendor/xlsx/xlsx.full.min.js') : 'assets/vendor/xlsx/xlsx.full.min.js') . '"></script>';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Timetable, Faculty & CTL Activity (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'timetable';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Faculty Timetable';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + WORKLOAD WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Workload Audit';
      $nav_subtitle = 'CE & IT Department • Teaching Load Analytics & Audit';
      $nav_search_placeholder = 'Search faculty, initials, department...';
      $nav_back_url = function_exists('base_url') ? base_url('timetable') : './timetable';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS BENTO WORKLOAD DASHBOARD ================= -->
      <main class="wl-main-workspace flex-1 min-h-0 min-w-0 glass-card p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">

        <!-- Interior Scroll Area (Fits 100% inside 100vh on Desktop, Smooth Scroll on Mobile) -->
        <div id="mainWorkloadContent" class="flex-1 min-h-0 min-w-0 flex flex-col gap-3 sm:gap-3.5 overflow-y-auto wl-custom-scroll pr-1 sm:pr-1.5">

          <?php if (!$excelExists || !$jsDataExists): ?>
            <!-- Database Sync Alert State -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-2xl glass-card border border-amber-500/30 my-auto">
              <div class="w-16 h-16 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center text-3xl mb-3 shadow-sm">
                ⚠️
              </div>
              <h2 class="text-xl sm:text-2xl font-bold font-serif text-[var(--text-primary)]">Database Sync Needed</h2>
              <p class="text-xs sm:text-sm text-[var(--text-muted)] max-w-md mt-1.5 mb-5">
                The compiled faculty timetable database is missing or requires synchronization with the latest master Excel sheet.
              </p>
              <a
                href="<?php echo function_exists('base_url') ? base_url('update-timetable') : './update-timetable'; ?>"
                class="h-9 px-5 rounded-full bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-2 shadow-md cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>Run Timetable Sync</span>
              </a>
            </div>
          <?php else: ?>

            <!-- Printable Official Header (Visible ONLY when printing) -->
            <div class="print-only-header">
              <h1>Department of CE & IT — Faculty Workload Summary</h1>
              <p>Official Weekly Teaching Load & Schedule Breakdown Report | Printed: <?php echo date('d M Y'); ?></p>
            </div>

            <!-- 1. TOP KPI SUMMARY METRICS & ACTION TOOLBAR -->
            <div class="wl-header-toolbar flex flex-col 2xl:flex-row 2xl:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">

              <!-- 4 KPI Summary Cards (In Place of Redundant Page Title) -->
              <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 flex-1 min-w-0">

                <!-- KPI 1: Total Faculty Count -->
                <div class="wl-kpi-card wl-kpi-primary">
                  <div class="min-w-0 flex-1">
                    <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block truncate">Faculty Members</span>
                    <div class="text-base sm:text-xl font-bold font-serif text-[var(--text-primary)] mt-0.5 leading-none" id="totalFacultyCount">0</div>
                    <span class="text-[9.5px] text-[var(--color-primary)] font-semibold mt-1 block truncate">Verified in Roster</span>
                  </div>
                  <div class="wl-kpi-icon-wrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                  </div>
                </div>

                <!-- KPI 2: Total Department Workload Hours -->
                <div class="wl-kpi-card wl-kpi-blue">
                  <div class="min-w-0 flex-1">
                    <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 block truncate">Total Teaching Load</span>
                    <div class="text-base sm:text-xl font-bold font-serif text-blue-600 dark:text-blue-400 mt-0.5 leading-none" id="totalTeachingHours">0 Hrs</div>
                    <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Weekly Dept Hours</span>
                  </div>
                  <div class="wl-kpi-icon-wrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10"/>
                      <polyline points="12 6 12 12 16 14"/>
                    </svg>
                  </div>
                </div>

                <!-- KPI 3: Average Load / Faculty -->
                <div class="wl-kpi-card wl-kpi-amber">
                  <div class="min-w-0 flex-1">
                    <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block truncate">Average Load / Staff</span>
                    <div class="text-base sm:text-xl font-bold font-serif text-amber-600 dark:text-amber-400 mt-0.5 leading-none" id="avgWorkload">0.0 Hrs</div>
                    <span class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Norm: 16–20 Hrs/Wk</span>
                  </div>
                  <div class="wl-kpi-icon-wrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <line x1="18" y1="20" x2="18" y2="10"/>
                      <line x1="12" y1="20" x2="12" y2="4"/>
                      <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                  </div>
                </div>

                <!-- KPI 4: Peak Workload Recorded -->
                <div class="wl-kpi-card wl-kpi-rose">
                  <div class="min-w-0 flex-1">
                    <span class="text-[9px] sm:text-[9.5px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400 block truncate">Peak Workload Recorded</span>
                    <div class="text-base sm:text-xl font-bold font-serif text-rose-600 dark:text-rose-400 mt-0.5 leading-none" id="maxWorkload">0 Hrs</div>
                    <span id="peakFacultyLabel" class="text-[9.5px] text-[var(--text-muted)] font-medium mt-1 block truncate">Highest Load</span>
                  </div>
                  <div class="wl-kpi-icon-wrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z"/>
                    </svg>
                  </div>
                </div>

              </div>

              <!-- Action Buttons (Right) -->
              <div class="flex items-center gap-1.5 sm:gap-2 self-start 2xl:self-center flex-wrap shrink-0">
                <!-- Search Bar (Faculty Search) -->
                <div class="relative flex items-center h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill border border-white/80 dark:border-white/10 shadow-sm w-36 sm:w-44 lg:w-48 shrink-0 transition focus-within:ring-1 focus-within:ring-[var(--color-primary)]">
                  <svg class="w-3.5 h-3.5 text-[var(--text-muted)] shrink-0 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                  </svg>
                  <input
                    type="text"
                    id="summarySearchInput"
                    class="w-full bg-transparent text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none"
                    placeholder="Search faculty..."
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

                <!-- Filter Button (Left side of Matrix button, triggers Offcanvas Right) -->
                <button
                  type="button"
                  id="filterOffcanvasBtn"
                  class="relative h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
                  data-bs-toggle="offcanvas"
                  data-bs-target="#offcanvasRight"
                  aria-controls="offcanvasRight"
                  title="Filter and Sort Workload Records"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.539.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.378-1.007.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                  </svg>
                  <span>Filter</span>
                  <span id="activeFilterBadge" class="hidden w-2 h-2 rounded-full bg-[var(--color-primary)] ring-2 ring-white dark:ring-gray-900"></span>
                </button>

                <!-- Return to Timetable Matrix -->
                <a
                  href="<?php echo function_exists('base_url') ? base_url('timetable') : './timetable'; ?>"
                  class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
                  title="Return to Weekly Timetable Matrix"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" x2="16" y1="2" y2="6"/>
                    <line x1="8" x2="8" y1="2" y2="6"/>
                    <line x1="3" x2="21" y1="10" y2="10"/>
                  </svg>
                  <span class="hidden xs:inline">Timetable</span> Matrix
                </a>

                <!-- Actions Dropdown (Export Excel + Print Summary combined in one button) -->
                <div class="relative inline-block text-left" id="actionsDropdownWrapper">
                  <button
                    type="button"
                    id="actionsDropdownBtn"
                    class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl bg-[var(--color-primary)] text-white text-[10.5px] sm:text-[11px] font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
                    title="Export & Print Options"
                    aria-expanded="false"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <span>Actions</span>
                    <svg class="w-3 h-3 ml-0.5 transition-transform duration-200" id="actionsDropdownChevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  <!-- Actions Dropdown Menu -->
                  <div
                    id="actionsDropdownMenu"
                    class="hidden absolute right-0 mt-1.5 w-48 rounded-2xl glass-card border border-white/80 dark:border-white/10 shadow-2xl py-1.5 z-50 transition-all duration-150 backdrop-blur-2xl bg-white/95 dark:bg-[#1a1528]/95 overflow-hidden"
                    role="menu"
                  >
                    <!-- Export to Excel Button -->
                    <button
                      type="button"
                      id="exportExcelBtn"
                      class="w-full text-left px-3 py-2 text-xs font-semibold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-2.5 cursor-pointer group"
                      role="menuitem"
                    >
                      <div class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0 group-hover:bg-white/20 group-hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                          <polyline points="14 2 14 8 20 8"></polyline>
                          <line x1="16" y1="13" x2="8" y2="13"></line>
                          <line x1="16" y1="17" x2="8" y2="17"></line>
                          <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="font-bold leading-tight">Export Excel</div>
                        <div class="text-[9.5px] opacity-70 leading-tight">Download XLSX sheet</div>
                      </div>
                    </button>

                    <div class="my-1 border-t border-black/5 dark:border-white/10"></div>

                    <!-- Print Summary Button -->
                    <button
                      type="button"
                      id="printSummaryBtn"
                      onclick="window.print()"
                      class="w-full text-left px-3 py-2 text-xs font-semibold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-2.5 cursor-pointer group"
                      role="menuitem"
                    >
                      <div class="w-6 h-6 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0 group-hover:bg-white/20 group-hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12 0h12v4H6v-4z" />
                        </svg>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="font-bold leading-tight">Print Summary</div>
                        <div class="text-[9.5px] opacity-70 leading-tight">Department audit print</div>
                      </div>
                    </button>
                  </div>
                </div>

              </div>

            </div>

            <!-- 4. MAIN WORKLOAD SUMMARY GLASS TABLE -->
            <div class="wl-table-card flex-1 min-h-0 rounded-2xl border border-black/5 dark:border-white/10 glass-card">
              <div class="wl-table-wrapper rounded-2xl">
                <table class="wl-table">
                  <thead>
                    <tr>
                      <th class="w-10 sm:w-12 text-center">#</th>
                      <th>Faculty Member</th>
                      <th>Department</th>
                      <th class="hidden sm:table-cell text-center">Daily Schedule Breakdown (Hrs)</th>
                      <th class="text-center">Total Load</th>
                      <th class="text-center hidden md:table-cell">TT Match</th>
                      <th class="wl-action-col text-center pr-3 sm:pr-4">Action</th>
                    </tr>
                  </thead>
                  <tbody id="summaryTableBody">
                    <!-- Populated dynamically via JavaScript below -->
                  </tbody>
                </table>
              </div>
            </div>

            <!-- 5. BOTTOM PAGINATION & METRICS FOOTER (Same as Faculty Table) -->
            <div class="wl-footer-bar pt-2.5 sm:pt-3 border-t border-black/5 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2.5 sm:gap-3 shrink-0">
              <!-- Summary Text & Page Size Selector -->
              <div class="flex items-center gap-3 text-xs text-[var(--text-muted)]">
                <span id="showingCount" class="font-semibold select-none">
                  Loading faculty records...
                </span>

                <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-black/10 dark:border-white/10">
                  <label for="pageSizeSelect" class="font-medium text-[11px] select-none">Rows per page:</label>
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

              <!-- Page Navigation Buttons (Circular matching Faculty view) -->
              <div id="paginationControls" class="flex items-center gap-1 shrink-0">
                <!-- Dynamically populated via JavaScript -->
              </div>
            </div>

          <?php endif; ?>

        </div>

      </main>

    </div>

  </div>

  <!-- Bootstrap 5 Offcanvas Right: Workload Filter & Sort Drawer (Ultra Professional Floating Glass Sheet) -->
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
              Filter & Sort Records
            </h5>
            <span id="offcanvasActiveBadge" class="hidden px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-[var(--color-primary)]/15 text-[var(--color-primary)] border border-[var(--color-primary)]/30">
              Active
            </span>
          </div>
          <p class="text-[11px] text-[var(--text-muted)] mt-0.5 leading-tight">Interactive faculty dataset refinements</p>
        </div>
      </div>
      <button type="button" class="w-8 h-8 rounded-full glass-pill hover:bg-black/10 dark:hover:bg-white/10 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition active:scale-95 cursor-pointer" data-bs-dismiss="offcanvas" aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Drawer Body -->
    <div class="offcanvas-body p-5 space-y-6 flex flex-col justify-between overflow-y-auto">
      <div class="space-y-6">

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
            <button type="button" class="wl-drawer-card dept-btn active" data-dept="ALL">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">All Departments</div>
                  <div class="text-[10px] text-[var(--text-muted)]">Combined faculty summary roster</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>

            <!-- IT Dept -->
            <button type="button" class="wl-drawer-card dept-btn" data-dept="Information Technology">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">Information Technology</div>
                  <div class="text-[10px] text-[var(--text-muted)]">IT faculty members & laboratory staff</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>

            <!-- CE Dept -->
            <button type="button" class="wl-drawer-card dept-btn" data-dept="Computer Engineering">
              <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                  </svg>
                </div>
                <div class="min-w-0">
                  <div class="font-bold text-xs text-[var(--text-primary)]">Computer Engineering</div>
                  <div class="text-[10px] text-[var(--text-muted)]">CE faculty members & technical mentors</div>
                </div>
              </div>
              <div class="wl-drawer-radio"></div>
            </button>
          </div>
        </div>

        <!-- 2. Teaching Load Range Tier -->
        <div>
          <div class="flex items-center justify-between mb-2.5">
            <label class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)]">
              Teaching Load Range
            </label>
            <span class="text-[11px] font-bold text-[var(--color-primary)]" id="activeTierText">All Load</span>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <!-- All Load -->
            <button type="button" class="wl-tier-tile load-tier-btn active" data-tier="ALL">
              <div class="flex items-center justify-between mb-1.5">
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-[var(--text-muted)]">Entire Staff</span>
              </div>
              <div class="font-bold text-sm text-[var(--text-primary)] leading-tight">All Load</div>
              <div class="text-[10px] text-[var(--text-muted)] mt-0.5">Every workload tier</div>
            </button>

            <!-- High Load -->
            <button type="button" class="wl-tier-tile load-tier-btn" data-tier="HIGH">
              <div class="flex items-center justify-between mb-1.5">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-rose-500">Peak Load</span>
              </div>
              <div class="font-bold text-sm text-rose-600 dark:text-rose-400 leading-tight">High (20+)</div>
              <div class="text-[10px] text-[var(--text-muted)] mt-0.5">≥ 20 Hours / Week</div>
            </button>

            <!-- Balanced -->
            <button type="button" class="wl-tier-tile load-tier-btn" data-tier="BALANCED">
              <div class="flex items-center justify-between mb-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-500">Standard</span>
              </div>
              <div class="font-bold text-sm text-emerald-600 dark:text-emerald-400 leading-tight">Balanced</div>
              <div class="text-[10px] text-[var(--text-muted)] mt-0.5">11 – 19 Hours / Wk</div>
            </button>

            <!-- Low Load -->
            <button type="button" class="wl-tier-tile load-tier-btn" data-tier="LOW">
              <div class="flex items-center justify-between mb-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="text-[9px] font-extrabold uppercase tracking-wider text-amber-500">Light</span>
              </div>
              <div class="font-bold text-sm text-amber-600 dark:text-amber-400 leading-tight">Low (≤10)</div>
              <div class="text-[10px] text-[var(--text-muted)] mt-0.5">≤ 10 Hours / Week</div>
            </button>
          </div>
        </div>

        <!-- 3. Sort Order -->
        <div>
          <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2">
            Sort Order
          </label>
          <div class="relative">
            <select
              id="sortSelect"
              class="w-full h-10 px-3.5 pr-8 rounded-xl glass-card text-xs font-bold text-[var(--text-primary)] border border-black/10 dark:border-white/15 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] cursor-pointer appearance-none bg-transparent transition shadow-sm"
            >
              <option value="load-desc">Sort: Load (Highest to Lowest)</option>
              <option value="load-asc">Sort: Load (Lowest to Highest)</option>
              <option value="name-asc">Sort: Faculty Name (A to Z)</option>
              <option value="initials-asc">Sort: Initials (A to Z)</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[var(--text-muted)]">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>
        </div>

      </div>

      <!-- Drawer Footer -->
      <div class="pt-4 border-t border-black/5 dark:border-white/10 flex items-center gap-2.5 mt-auto">
        <button
          type="button"
          id="resetFiltersBtn"
          class="flex-1 h-10 px-3 rounded-xl glass-pill text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center justify-center gap-1.5 border border-black/10 dark:border-white/10 cursor-pointer active:scale-95"
        >
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Reset Filters</span>
        </button>

        <button
          type="button"
          class="flex-1 h-10 px-3 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center justify-center gap-1.5 shadow-md cursor-pointer"
          data-bs-dismiss="offcanvas"
        >
          <span>Apply Filters</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Include Portal Data Dependencies -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/timetableData.js') : 'assets/js/timetableData.js'; ?>"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (typeof timetableData === "undefined" || typeof facultyData === "undefined") {
        console.warn("Timetable or faculty data not loaded.");
        return;
      }

      let allRows = [];
      let currentDept = "ALL";
      let currentTier = "ALL";
      let currentSearch = "";
      let currentSort = "load-desc";
      let currentPage = 1;
      let pageSize = 20;

      const timetableUrl = "<?php echo function_exists('base_url') ? base_url('timetable') : './timetable'; ?>";

      // Process raw data for faculty members from timetableData matching facultyData
      Object.keys(timetableData).forEach(initials => {
        const info = timetableData[initials];
        let facultyObj = facultyData.find(f => f.initials && f.initials.toUpperCase() === initials.toUpperCase());
        if (!facultyObj) return;

        let name = facultyObj.name;
        let dept = (facultyObj.department || "Information Technology").trim();
        let designation = facultyObj.designation || "Lecturer";
        let avatarClass = facultyObj.avatarClass || `av-${initials.toLowerCase()}`;

        let daily = { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 };

        if (info.schedule && Array.isArray(info.schedule)) {
          info.schedule.forEach(slot => {
            if (slot.isRecess) return;
            ["MON", "TUE", "WED", "THU", "FRI", "SAT"].forEach(day => {
              if (slot[day] && slot[day].occupied) {
                daily[day]++;
              }
            });
          });
        }

        let totalLoad = Object.values(daily).reduce((a, b) => a + b, 0);

        allRows.push({
          initials: initials,
          name: name,
          department: dept,
          designation: designation,
          avatarClass: avatarClass,
          daily: daily,
          totalLoad: totalLoad,
          hasTimetable: true
        });
      });

      // Add any teaching faculty from facultyData that don't have timetable tabs yet
      facultyData.forEach(f => {
        if (!f.initials) return;
        if (f.designation && (
          f.designation.toLowerCase().includes("developer") ||
          f.designation.toLowerCase().includes("admin")
        )) return;

        if (!allRows.some(r => r.initials.toUpperCase() === f.initials.toUpperCase())) {
          allRows.push({
            initials: f.initials,
            name: f.name,
            department: (f.department || "Information Technology").trim(),
            designation: f.designation || "Faculty",
            avatarClass: f.avatarClass,
            daily: { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 },
            totalLoad: 0,
            hasTimetable: false
          });
        }
      });

      // Update KPI Statistics
      function updateStats(rows) {
        const totalFac = rows.length;
        const totalHours = rows.reduce((sum, r) => sum + r.totalLoad, 0);
        const avgHours = totalFac > 0 ? (totalHours / totalFac).toFixed(1) : "0.0";
        const maxHours = rows.length > 0 ? Math.max(...rows.map(r => r.totalLoad)) : 0;

        // Find faculty member with peak workload
        let peakFac = rows.find(r => r.totalLoad === maxHours && maxHours > 0);
        const peakLabel = peakFac ? `${peakFac.initials} (${peakFac.name.split(' ').slice(0, 2).join(' ')})` : "Top Faculty Load";

        const countEl = document.getElementById("totalFacultyCount");
        const hoursEl = document.getElementById("totalTeachingHours");
        const avgEl = document.getElementById("avgWorkload");
        const maxEl = document.getElementById("maxWorkload");
        const peakLabelEl = document.getElementById("peakFacultyLabel");
        const totalBadgeEl = document.getElementById("totalBadge");

        if (countEl) countEl.textContent = totalFac;
        if (hoursEl) hoursEl.textContent = `${totalHours} Hrs`;
        if (avgEl) avgEl.textContent = `${avgHours} Hrs`;
        if (maxEl) maxEl.textContent = `${maxHours} Hrs`;
        if (peakLabelEl) peakLabelEl.textContent = peakLabel;
        if (totalBadgeEl) totalBadgeEl.textContent = `${totalFac} Faculty Active`;
      }

      // Filter and Sort Data
      function getFilteredRows() {
        let filtered = allRows.filter(r => {
          // Exclude non-teaching administrative entries
          if (r.designation && (
            r.designation.toLowerCase().includes("developer") ||
            r.designation.toLowerCase().includes("admin")
          )) return false;

          // Department filter
          let matchesDept = (currentDept === "ALL") ||
            (r.department.toLowerCase().includes("it") && currentDept.toLowerCase().includes("it")) ||
            (r.department.toLowerCase().includes("computer") && currentDept.toLowerCase().includes("computer")) ||
            (r.department.toLowerCase().includes("both"));

          // Load Tier filter
          let matchesTier = true;
          if (currentTier === "HIGH") matchesTier = r.totalLoad >= 20;
          else if (currentTier === "BALANCED") matchesTier = (r.totalLoad >= 11 && r.totalLoad < 20);
          else if (currentTier === "LOW") matchesTier = (r.totalLoad > 0 && r.totalLoad <= 10);
          else if (currentTier === "ZERO") matchesTier = (r.totalLoad === 0);

          // Search query
          let query = currentSearch.toLowerCase().trim();
          let matchesSearch = !query ||
            r.name.toLowerCase().includes(query) ||
            r.initials.toLowerCase().includes(query) ||
            r.department.toLowerCase().includes(query) ||
            r.designation.toLowerCase().includes(query);

          return matchesDept && matchesTier && matchesSearch;
        });

        // Sorting
        filtered.sort((a, b) => {
          if (currentSort === "load-desc") return b.totalLoad - a.totalLoad;
          if (currentSort === "load-asc") return a.totalLoad - b.totalLoad;
          if (currentSort === "name-asc") return a.name.localeCompare(b.name);
          if (currentSort === "initials-asc") return a.initials.localeCompare(b.initials);
          return 0;
        });

        return filtered;
      }

      // Render Table with Pagination (Matching Faculty Management Table)
      function renderTable() {
        const filtered = getFilteredRows();
        updateStats(filtered);

        const tbody = document.getElementById("summaryTableBody");
        const countText = document.getElementById("showingCount");
        const totalRecords = filtered.length;

        // Handle Page Bounds
        const maxPage = pageSize === "all" ? 1 : Math.max(1, Math.ceil(totalRecords / pageSize));
        if (currentPage > maxPage) currentPage = maxPage;

        // Slice for pagination
        let startIdx = 0;
        let endIdx = totalRecords;
        let paginated = filtered;

        if (pageSize !== "all") {
          startIdx = (currentPage - 1) * pageSize;
          endIdx = Math.min(startIdx + pageSize, totalRecords);
          paginated = filtered.slice(startIdx, endIdx);
        }

        if (countText) {
          countText.textContent = totalRecords === 0
            ? "Showing 0 faculty records"
            : `Showing ${startIdx + 1}–${endIdx} of ${totalRecords} faculty members`;
        }

        renderPagination(totalRecords, maxPage);

        if (!tbody) return;

        if (totalRecords === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center py-12 px-4 text-[var(--text-muted)]">
                <div class="flex flex-col items-center justify-center gap-2">
                  <div class="w-12 h-12 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-center text-xl">
                    🔍
                  </div>
                  <p class="font-bold text-sm text-[var(--text-primary)]">No matching faculty records found</p>
                  <p class="text-xs">Try adjusting your department filter, load tier, or search keywords.</p>
                </div>
              </td>
            </tr>
          `;
          return;
        }

        let html = "";
        paginated.forEach((r, idx) => {
          let deptClass = "wl-dept-it";
          let deptLabel = "IT";
          if (r.department.toLowerCase().includes("computer") || r.department.toLowerCase().includes("ce")) {
            deptClass = "wl-dept-ce";
            deptLabel = "CE";
          } else if (r.department.toLowerCase().includes("both") || r.department.toLowerCase().includes("&")) {
            deptClass = "wl-dept-both";
            deptLabel = "IT & CE";
          }

          let loadBadgeClass = "wl-load-balanced";
          if (r.totalLoad >= 20) loadBadgeClass = "wl-load-high";
          else if (r.totalLoad > 0 && r.totalLoad <= 10) loadBadgeClass = "wl-load-low";
          else if (r.totalLoad === 0) loadBadgeClass = "wl-load-zero";

          let daysHtml = ["MON", "TUE", "WED", "THU", "FRI", "SAT"].map(day => {
            let count = r.daily[day];
            let activeClass = count > 0 ? "active" : "";
            return `<span class="wl-day-pill ${activeClass}" title="${day}: ${count} Teaching Hours">${count > 0 ? count : 0}</span>`;
          }).join("");

          html += `
            <tr class="group">
              <td class="text-center font-bold text-xs text-[var(--text-muted)]">${startIdx + idx + 1}</td>
              <td>
                <div class="wl-fac-cell">
                  <div class="wl-fac-avatar">${r.initials}</div>
                  <div class="min-w-0">
                    <div class="wl-fac-name truncate">${r.name}</div>
                    <div class="wl-fac-sub truncate">${r.designation} • <span class="font-bold text-[var(--text-primary)]">${r.initials}</span></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="wl-dept-pill ${deptClass}">${deptLabel}</span>
              </td>
              <td class="hidden sm:table-cell text-center">
                <div class="wl-days-row justify-center">
                  ${daysHtml}
                </div>
              </td>
              <td class="text-center">
                <span class="wl-load-badge ${loadBadgeClass}">
                  ${r.totalLoad >= 20 ? '🔥 ' : ''}${r.totalLoad} Hrs
                </span>
              </td>
              <td class="text-center hidden md:table-cell">
                <span class="wl-match-badge ${r.totalLoad > 0 ? 'wl-match-ok' : 'wl-match-warn'}">
                  ${r.totalLoad > 0 ? '✓ Matched' : '⚠️ No Schedule'}
                </span>
              </td>
              <td class="wl-action-cell text-center">
                <a href="${timetableUrl}?initials=${encodeURIComponent(r.initials)}" class="wl-action-btn" title="View Weekly Timetable for ${r.name}">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <span>View Grid</span>
                </a>
              </td>
            </tr>
          `;
        });

        tbody.innerHTML = html;
      }

      // Render Pagination Controls (Identical to Faculty Management Table)
      function renderPagination(totalRecords, maxPage) {
        const container = document.getElementById("paginationControls");
        if (!container) return;

        if (totalRecords === 0 || pageSize === "all" || maxPage <= 1) {
          container.innerHTML = "";
          return;
        }

        let pagesHtml = "";

        // Prev Button
        pagesHtml += `
          <button type="button" class="fac-page-btn" id="prevPageBtn" ${currentPage === 1 ? "disabled" : ""} aria-label="Previous Page" title="Previous Page">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/>
            </svg>
          </button>
        `;

        const windowSize = (typeof window !== "undefined" && window.innerWidth < 640) ? 1 : 2;
        const startPage = Math.max(1, currentPage - windowSize);
        const endPage = Math.min(maxPage, currentPage + windowSize);

        if (startPage > 1) {
          pagesHtml += `<button type="button" class="fac-page-btn" data-page="1">1</button>`;
          if (startPage > 2) pagesHtml += `<span class="px-0.5 text-[var(--text-muted)] text-[11px]">…</span>`;
        }

        for (let p = startPage; p <= endPage; p++) {
          pagesHtml += `
            <button type="button" class="fac-page-btn ${p === currentPage ? "active" : ""}" data-page="${p}">
              ${p}
            </button>
          `;
        }

        if (endPage < maxPage) {
          if (endPage < maxPage - 1) pagesHtml += `<span class="px-1 text-[var(--text-muted)]">…</span>`;
          pagesHtml += `<button type="button" class="fac-page-btn" data-page="${maxPage}">${maxPage}</button>`;
        }

        // Next Button
        pagesHtml += `
          <button type="button" class="fac-page-btn" id="nextPageBtn" ${currentPage === maxPage ? "disabled" : ""} aria-label="Next Page" title="Next Page">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
            </svg>
          </button>
        `;

        container.innerHTML = pagesHtml;

        // Events
        const prevBtn = document.getElementById("prevPageBtn");
        const nextBtn = document.getElementById("nextPageBtn");
        if (prevBtn) {
          prevBtn.addEventListener("click", () => {
            if (currentPage > 1) {
              currentPage--;
              renderTable();
            }
          });
        }
        if (nextBtn) {
          nextBtn.addEventListener("click", () => {
            if (currentPage < maxPage) {
              currentPage++;
              renderTable();
            }
          });
        }

        const pageBtns = container.querySelectorAll(".fac-page-btn[data-page]");
        pageBtns.forEach(btn => {
          btn.addEventListener("click", () => {
            currentPage = parseInt(btn.getAttribute("data-page"), 10);
            renderTable();
          });
        });
      }

      // Page Size Selector Listener
      const pageSizeSelect = document.getElementById("pageSizeSelect");
      if (pageSizeSelect) {
        pageSizeSelect.addEventListener("change", function() {
          pageSize = this.value === "all" ? "all" : parseInt(this.value, 10);
          currentPage = 1;
          renderTable();
        });
      }

      // Active Filter Badge & Status Label Updates
      const activeFilterBadge = document.getElementById("activeFilterBadge");
      const offcanvasActiveBadge = document.getElementById("offcanvasActiveBadge");

      function updateActiveFilterBadge() {
        const hasFilters = (currentDept !== "ALL" || currentTier !== "ALL");
        if (activeFilterBadge) {
          activeFilterBadge.classList.toggle("hidden", !hasFilters);
        }
        if (offcanvasActiveBadge) {
          offcanvasActiveBadge.classList.toggle("hidden", !hasFilters);
        }
        const activeDeptText = document.getElementById("activeDeptText");
        const activeTierText = document.getElementById("activeTierText");
        if (activeDeptText) {
          activeDeptText.textContent = currentDept === "ALL" ? "All Departments" : (currentDept.includes("Info") ? "IT Dept" : "CE Dept");
        }
        if (activeTierText) {
          activeTierText.textContent = currentTier === "ALL" ? "All Load" : (currentTier === "HIGH" ? "High (20+)" : (currentTier === "BALANCED" ? "Balanced" : "Low (≤10)"));
        }
      }

      // Department Filter Buttons
      document.querySelectorAll(".dept-btn").forEach(btn => {
        btn.addEventListener("click", function() {
          document.querySelectorAll(".dept-btn").forEach(b => b.classList.remove("active"));
          this.classList.add("active");
          currentDept = this.getAttribute("data-dept");
          currentPage = 1;
          updateActiveFilterBadge();
          renderTable();
        });
      });

      // Load Tier Filter Buttons
      document.querySelectorAll(".load-tier-btn").forEach(btn => {
        btn.addEventListener("click", function() {
          document.querySelectorAll(".load-tier-btn").forEach(b => b.classList.remove("active"));
          this.classList.add("active");
          currentTier = this.getAttribute("data-tier");
          currentPage = 1;
          updateActiveFilterBadge();
          renderTable();
        });
      });

      // Reset Filters Button
      const resetBtn = document.getElementById("resetFiltersBtn");
      if (resetBtn) {
        resetBtn.addEventListener("click", function() {
          currentDept = "ALL";
          currentTier = "ALL";
          currentPage = 1;
          document.querySelectorAll(".dept-btn").forEach(b => {
            b.classList.toggle("active", b.getAttribute("data-dept") === "ALL");
          });
          document.querySelectorAll(".load-tier-btn").forEach(b => {
            b.classList.toggle("active", b.getAttribute("data-tier") === "ALL");
          });
          updateActiveFilterBadge();
          renderTable();
        });
      }

      // Search Inputs (In-page and Global Navbar)
      const searchInput = document.getElementById("summarySearchInput");
      const clearBtn = document.getElementById("clearSearchBtn");

      if (searchInput) {
        searchInput.addEventListener("input", function() {
          currentSearch = this.value;
          currentPage = 1;
          if (clearBtn) {
            clearBtn.classList.toggle("hidden", !this.value);
          }
          renderTable();
        });
      }

      if (clearBtn && searchInput) {
        clearBtn.addEventListener("click", function() {
          searchInput.value = "";
          currentSearch = "";
          currentPage = 1;
          clearBtn.classList.add("hidden");
          renderTable();
          searchInput.focus();
        });
      }

      // Sort Select
      const sortSelect = document.getElementById("sortSelect");
      if (sortSelect) {
        sortSelect.addEventListener("change", function() {
          currentSort = this.value;
          currentPage = 1;
          renderTable();
        });
      }

      // Actions Dropdown Logic (Export Excel & Print Summary)
      const actionsDropdownBtn = document.getElementById("actionsDropdownBtn");
      const actionsDropdownMenu = document.getElementById("actionsDropdownMenu");
      const actionsDropdownChevron = document.getElementById("actionsDropdownChevron");

      if (actionsDropdownBtn && actionsDropdownMenu) {
        actionsDropdownBtn.addEventListener("click", function(e) {
          e.stopPropagation();
          const isHidden = actionsDropdownMenu.classList.toggle("hidden");
          actionsDropdownBtn.setAttribute("aria-expanded", !isHidden);
          if (actionsDropdownChevron) {
            actionsDropdownChevron.classList.toggle("rotate-180", !isHidden);
          }
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", function(e) {
          if (!actionsDropdownMenu.contains(e.target) && !actionsDropdownBtn.contains(e.target)) {
            actionsDropdownMenu.classList.add("hidden");
            actionsDropdownBtn.setAttribute("aria-expanded", "false");
            if (actionsDropdownChevron) actionsDropdownChevron.classList.remove("rotate-180");
          }
        });

        // Close dropdown on Escape
        document.addEventListener("keydown", function(e) {
          if (e.key === "Escape") {
            actionsDropdownMenu.classList.add("hidden");
            actionsDropdownBtn.setAttribute("aria-expanded", "false");
            if (actionsDropdownChevron) actionsDropdownChevron.classList.remove("rotate-180");
          }
        });
      }

      // Offcanvas Standalone Fallback Controller
      const offcanvasEl = document.getElementById("offcanvasRight");
      const filterBtn = document.getElementById("filterOffcanvasBtn");

      function toggleCustomOffcanvas(show) {
        if (!offcanvasEl) return;
        let backdrop = document.getElementById("offcanvasBackdrop");
        if (show) {
          offcanvasEl.classList.add("show");
          if (!backdrop) {
            backdrop = document.createElement("div");
            backdrop.id = "offcanvasBackdrop";
            backdrop.className = "offcanvas-backdrop show";
            document.body.appendChild(backdrop);
            backdrop.addEventListener("click", () => toggleCustomOffcanvas(false));
          }
          document.body.style.overflow = "hidden";
        } else {
          offcanvasEl.classList.remove("show");
          if (backdrop) backdrop.remove();
          document.body.style.overflow = "";
        }
      }

      // Attach fallback listeners if bootstrap.Offcanvas is not present
      if (typeof bootstrap === "undefined" || !bootstrap.Offcanvas) {
        if (filterBtn) {
          filterBtn.addEventListener("click", (e) => {
            e.preventDefault();
            toggleCustomOffcanvas(true);
          });
        }
        document.querySelectorAll('[data-bs-dismiss="offcanvas"]').forEach(btn => {
          btn.addEventListener("click", (e) => {
            e.preventDefault();
            toggleCustomOffcanvas(false);
          });
        });
      }

      // Export to Excel using SheetJS
      const exportBtn = document.getElementById("exportExcelBtn");
      if (exportBtn) {
        exportBtn.addEventListener("click", function() {
          if (actionsDropdownMenu) {
            actionsDropdownMenu.classList.add("hidden");
            actionsDropdownBtn.setAttribute("aria-expanded", "false");
            if (actionsDropdownChevron) actionsDropdownChevron.classList.remove("rotate-180");
          }

          const filtered = getFilteredRows();
          if (!filtered || filtered.length === 0) {
            alert("No faculty records match your current filter to export.");
            return;
          }

          if (typeof XLSX === "undefined") {
            alert("SheetJS export library is still loading. Please try again in a moment.");
            return;
          }

          const exportData = filtered.map((r, idx) => ({
            "Sr. No.": idx + 1,
            "Faculty Name": r.name,
            "Initials": r.initials,
            "Department": r.department,
            "Designation": r.designation,
            "Monday (Hrs)": r.daily.MON,
            "Tuesday (Hrs)": r.daily.TUE,
            "Wednesday (Hrs)": r.daily.WED,
            "Thursday (Hrs)": r.daily.THU,
            "Friday (Hrs)": r.daily.FRI,
            "Saturday (Hrs)": r.daily.SAT,
            "Total Teaching Load (Hrs)": r.totalLoad,
            "Timetable Match": r.totalLoad > 0 ? "Matched" : "No Schedule"
          }));

          const worksheet = XLSX.utils.json_to_sheet(exportData);
          worksheet["!cols"] = [
            { wch: 8 },
            { wch: 30 },
            { wch: 10 },
            { wch: 25 },
            { wch: 22 },
            { wch: 12 },
            { wch: 12 },
            { wch: 12 },
            { wch: 12 },
            { wch: 12 },
            { wch: 12 },
            { wch: 24 },
            { wch: 16 }
          ];

          const workbook = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(workbook, worksheet, "Workload Summary");
          const dateStr = new Date().toISOString().split("T")[0];
          XLSX.writeFile(workbook, `CE_IT_Faculty_Workload_Summary_${dateStr}.xlsx`);
        });
      }

      // Initial Render
      updateActiveFilterBadge();
      renderTable();
    });
  </script>

  <!-- Bootstrap 5 Bundle JS (Supports Offcanvas, Dropdowns, and Modals) -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') : 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'; ?>"></script>

  <!-- Layout Control Scripts (Shared Across All Portal Views) -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
</body>

</html>
