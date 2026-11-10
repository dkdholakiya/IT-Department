<?php
$page_title = 'CTL Activity Report — CE & IT Department';
$page_css = ['assets/css/ctlactivity.css'];
$extra_head = '<script src="' . (function_exists('v_asset') ? v_asset('assets/vendor/xlsx/xlsx.full.min.js') : 'assets/vendor/xlsx/xlsx.full.min.js') . '"></script>';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard & Faculty Layout Exactly (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'ctlactivity';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'CTL Activity Report';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + CTL ACTIVITY WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'CTL Activity';
      $nav_subtitle = 'CE & IT Department • Academic Tracking & Submission Analytics';
      $nav_search_placeholder = 'Search activities, faculty, statuses...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS CTL ACTIVITY DASHBOARD ================= -->
      <main class="ctl-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">

        <div id="mainDashboardContent" class="flex-1 min-h-0 min-w-0 flex flex-col gap-3 overflow-y-auto ctl-custom-scroll pr-1">

          <!-- Hidden badge hook for JS compatibility -->
          <span id="totalActivitiesBadge" class="hidden" aria-hidden="true"></span>

          <!-- 1. TOP BENTO GRID: UPLOAD MASTER SHEET & FACULTY COORDINATOR PROFILE -->
          <div class="ctl-bento-grid grid grid-cols-1 lg:grid-cols-12 gap-3 shrink-0 relative z-40">

            <!-- Card A: Upload Dropzone (5 Columns on Desktop) -->
            <div class="lg:col-span-5 glass-card p-3 sm:p-4 rounded-2xl flex flex-col justify-between border border-white/80 dark:border-white/10">
              <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)]">Upload Master Sheet (.xlsx)</span>
                <span class="text-[10px] font-bold text-[var(--color-primary)] font-mono">SheetJS v0.19</span>
              </div>

              <!-- Drag and Drop Dropzone -->
              <div id="uploadArea" class="ctl-dropzone p-4 sm:p-5 flex flex-col items-center justify-center text-center gap-2 flex-1 min-h-[140px]">
                <input type="file" id="excelFile" accept=".xlsx,.xls" class="hidden" />

                <!-- Initial Upload Prompt -->
                <div id="uploadPrompt" class="flex flex-col items-center gap-1.5">
                  <div class="w-10 h-10 rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                    </svg>
                  </div>
                  <div class="text-xs font-bold text-[var(--text-primary)]">Click or Drag &amp; Drop Master Sheet</div>
                  <div class="text-[10.5px] text-[var(--text-muted)]">Supports Microsoft Excel (.xlsx, .xls)</div>
                </div>

                <!-- File Loaded Badge (Hidden initially) -->
                <div id="fileLoadedBadge" class="hidden flex flex-col items-center gap-2">
                  <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                  </div>
                  <div>
                    <div id="loadedFileName" class="text-xs font-bold text-[var(--text-primary)] max-w-[220px] truncate">file.xlsx</div>
                    <div id="loadedFileSize" class="text-[10.5px] text-[var(--text-muted)] font-mono">0 KB</div>
                  </div>
                  <button type="button" id="replaceFileBtn" class="px-2.5 py-1 rounded-lg glass-pill text-[10px] font-bold text-[var(--color-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition cursor-pointer">
                    Replace Sheet
                  </button>
                </div>
              </div>

              <!-- Card A Action Controls: Direct ERP Links & Reset (Under Master Sheet Dropzone) -->
              <div class="flex items-center gap-2 pt-2.5 mt-2.5 border-t border-black/5 dark:border-white/10 shrink-0">
                <!-- Direct ERP Button -->
                <button
                  type="button"
                  id="directErpBtn"
                  class="flex-1 h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                  title="Direct ERP Links Quick Access"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                  </svg>
                  <span>Direct ERP Links</span>
                </button>

                <!-- Reset Filters Button -->
                <button
                  type="button"
                  id="resetFiltersBtn"
                  class="h-7 sm:h-8 px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center justify-center gap-1.5 cursor-pointer"
                  title="Reset table filters"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                  </svg>
                  <span>Reset</span>
                </button>
              </div>
            </div>

            <!-- Card B: Faculty Coordinator & Dispatch Center (7 Columns on Desktop) -->
            <div class="ctl-dispatch-card lg:col-span-7 glass-card p-3 sm:p-4 rounded-2xl flex flex-col justify-between border border-white/80 dark:border-white/10 gap-2.5 relative z-40">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--text-muted)]">Prepared By &amp; Dispatch Options</span>
                <button type="button" id="resetCCBtn" class="text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] transition cursor-pointer" title="Reset CC recipients to department defaults">
                  Reset CC Defaults
                </button>
              </div>

              <!-- Faculty Coordinator Autocomplete Input -->
              <div class="form-group search-select-wrap">
                <label for="facultySearch" class="block text-[10.5px] font-bold text-[var(--text-muted)] mb-1">
                  Faculty Coordinator <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    type="text"
                    id="facultySearch"
                    placeholder="Search faculty by name, initials, emp ID..."
                    autocomplete="off"
                    class="w-full h-8 px-3 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] border border-white/80 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                  />
                  <input type="hidden" id="preparedBy" name="preparedBy" required />
                  <div id="facultyDropdownList" class="search-dropdown-list ctl-custom-scroll"></div>
                </div>
                <div id="facultyError" class="hidden text-[10px] font-bold text-red-500 mt-1">Please select a faculty member from the list</div>
              </div>

              <!-- Auto-populated Readonly Info (Email + Phone) -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                  <label class="block text-[10px] font-bold text-[var(--text-muted)] mb-0.5">Faculty Email</label>
                  <input
                    type="text"
                    id="facultyEmail"
                    placeholder="Auto-filled email..."
                    readonly
                    class="w-full h-7 px-2.5 rounded-lg glass-pill text-[11px] font-mono text-[var(--text-secondary)] border border-white/60 dark:border-white/10 bg-transparent focus:outline-none"
                  />
                </div>
                <div>
                  <label class="block text-[10px] font-bold text-[var(--text-muted)] mb-0.5">Contact Phone</label>
                  <input
                    type="text"
                    id="facultyPhone"
                    placeholder="Auto-filled phone..."
                    readonly
                    class="w-full h-7 px-2.5 rounded-lg glass-pill text-[11px] font-mono text-[var(--text-secondary)] border border-white/60 dark:border-white/10 bg-transparent focus:outline-none"
                  />
                </div>
              </div>

              <!-- CC Emails Multi-Select Input + Send Action Row -->
              <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-2 pt-1 border-t border-black/5 dark:border-white/10">
                <div class="flex-1 min-w-0 form-group cc-select-wrap">
                  <label for="ccSearch" class="block text-[10px] font-bold text-[var(--text-muted)] mb-0.5">
                    CC Recipients <span class="font-normal opacity-70">(HOD &amp; Incharges)</span>
                  </label>
                  <div class="min-h-8 p-1 rounded-xl glass-pill border border-white/80 dark:border-white/10 flex flex-wrap items-center gap-1">
                    <div id="ccTagsContainer" class="flex flex-wrap items-center gap-1"></div>
                    <input
                      type="text"
                      id="ccSearch"
                      placeholder="Type name or email to add CC..."
                      autocomplete="off"
                      class="flex-1 min-w-[130px] h-6 px-1.5 text-xs text-[var(--text-primary)] bg-transparent placeholder-[var(--text-muted)] focus:outline-none"
                    />
                  </div>
                  <div id="ccDropdownList" class="search-dropdown-list ctl-custom-scroll"></div>
                </div>

                <!-- Send Action Button -->
                <button
                  type="button"
                  id="sendBtn"
                  class="h-8 px-4 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center justify-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
                  title="Dispatch HTML Report to Coordinator &amp; CCs"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M22 2L11 13"></path>
                    <path d="M22 2L15 22l-4-9-9-4 20-7z"></path>
                  </svg>
                  <span>Send Report</span>
                </button>
              </div>
            </div>

          </div>

          <!-- 3. KPI METRICS CARDS GRID (6 Cards) -->
          <div class="ctl-kpi-grid grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-2.5 shrink-0 relative z-10">
            <!-- Total Activities -->
            <div class="ctl-kpi-card ctl-kpi-total p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Total Activities</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-[var(--text-primary)] mt-0.5 leading-none" id="total">0</div>
            </div>

            <!-- Submitted -->
            <div class="ctl-kpi-card ctl-kpi-submitted p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">Submitted</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-blue-600 dark:text-blue-400 mt-0.5 leading-none" id="submittedCount">0</div>
            </div>

            <!-- Not Submitted -->
            <div class="ctl-kpi-card ctl-kpi-notsubmitted p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-red-600 dark:text-red-400 block">Not Submitted</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-red-600 dark:text-red-400 mt-0.5 leading-none" id="notSubmittedCount">0</div>
            </div>

            <!-- Approved -->
            <div class="ctl-kpi-card ctl-kpi-approved p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Approved</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-emerald-600 dark:text-emerald-400 mt-0.5 leading-none" id="approved">0</div>
            </div>

            <!-- Pending -->
            <div class="ctl-kpi-card ctl-kpi-pending p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Pending</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-amber-600 dark:text-amber-400 mt-0.5 leading-none" id="pending">0</div>
            </div>

            <!-- Reject -->
            <div class="ctl-kpi-card ctl-kpi-reject p-2.5 sm:p-3">
              <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-rose-700 dark:text-rose-400 block">Reject</span>
              <div class="text-xl sm:text-2xl font-bold font-serif text-rose-700 dark:text-rose-400 mt-0.5 leading-none" id="rejected">0</div>
            </div>
          </div>

          <!-- 4. ADVANCED FILTER TOOLBAR -->
          <div class="ctl-filter-toolbar flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 p-2 rounded-2xl glass-card border border-white/80 dark:border-white/10 shrink-0 relative z-[5]">
            <div class="flex items-center flex-wrap gap-2 flex-1 min-w-0">
              <!-- Approval Status Select -->
              <div class="flex items-center gap-1.5 min-w-[140px] flex-1 sm:flex-initial">
                <span class="text-[10.5px] font-bold text-[var(--text-muted)] whitespace-nowrap hidden sm:inline">Status:</span>
                <select id="statusFilter" class="w-full sm:w-auto h-7 px-2.5 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] border border-white/70 dark:border-white/10 focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer">
                  <option value="">All Statuses</option>
                  <option value="Approved">Approved</option>
                  <option value="Pending">Pending</option>
                  <option value="Rejected">Reject</option>
                </select>
              </div>

              <!-- Submission Condition Select -->
              <div class="flex items-center gap-1.5 min-w-[150px] flex-1 sm:flex-initial">
                <span class="text-[10.5px] font-bold text-[var(--text-muted)] whitespace-nowrap hidden sm:inline">Condition:</span>
                <select id="submissionFilter" class="w-full sm:w-auto h-7 px-2.5 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] border border-white/70 dark:border-white/10 focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer">
                  <option value="">All Conditions</option>
                  <option value="Submitted">Submitted (Any)</option>
                  <option value="Not Submitted">Not Submitted</option>
                  <option value="On Time">On Time</option>
                  <option value="Delayed">Delayed</option>
                  <option value="Over Grace Period">Over Grace Period</option>
                </select>
              </div>
            </div>

            <!-- Search Activity Input -->
            <div class="relative min-w-[180px] sm:w-64">
              <svg class="w-3.5 h-3.5 absolute left-2.5 top-2 text-[var(--text-muted)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input
                type="text"
                id="search"
                placeholder="Search activity name..."
                class="w-full h-7 pl-8 pr-3 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] border border-white/70 dark:border-white/10 focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)]"
              />
            </div>
          </div>

          <!-- 5. GLASS DATA TABLE WRAPPER -->
          <div class="ctl-table-container flex-1 min-h-[260px] rounded-2xl glass-card overflow-hidden border border-white/80 dark:border-white/10 flex flex-col relative z-0">
            <div class="flex-1 overflow-auto ctl-table-scroll">
              <table class="ctl-table">
                <thead>
                  <tr>
                    <th class="w-12 text-center">#</th>
                    <th class="min-w-[200px]">Activity Name</th>
                    <th class="text-center whitespace-nowrap">Plan Date</th>
                    <th class="text-center whitespace-nowrap">Actual Date</th>
                    <th class="text-center whitespace-nowrap">Modified Date</th>
                    <th class="text-center whitespace-nowrap">Marks</th>
                    <th class="text-center whitespace-nowrap">Flags</th>
                    <th class="text-center whitespace-nowrap">Submission Flags</th>
                    <th class="text-center whitespace-nowrap">Approval Status</th>
                  </tr>
                </thead>
                <tbody id="tbody">
                  <!-- Dynamically populated via JS -->
                </tbody>
              </table>
            </div>
          </div>

        </div><!-- /#mainDashboardContent -->

        <!-- 6. FULL PAGE 500 SERVER ERROR VIEW (Hidden by default) -->
        <div id="fullServerErrorContainer" class="hidden flex-1 items-center justify-center p-6 text-center">
          <div class="glass-card max-w-md p-6 sm:p-8 rounded-3xl border border-red-500/30 text-center shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-red-500/15 text-red-500 flex items-center justify-center mx-auto mb-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
              </svg>
            </div>
            <h3 class="text-base font-bold text-red-500">500 Internal Server Error</h3>
            <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
              Failed to process uploaded Excel sheet. Server encountered an unexpected error.
            </p>
            <button type="button" onclick="location.reload();" class="mt-4 px-4 py-2 rounded-xl bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition">
              Reload Page
            </button>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- ================= DIRECT ERP LINK POPUP MODAL ================= -->
  <div id="erpModalOverlay" class="fixed inset-0 z-[1050] bg-black/50 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-4" aria-hidden="true" role="dialog" aria-labelledby="erpModalTitle">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-2xl max-h-[85vh] rounded-3xl shadow-2xl border border-white/80 dark:border-white/10 flex flex-col overflow-hidden">
      
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-3.5 sm:p-4 border-b border-black/5 dark:border-white/10 shrink-0">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-sm sm:text-base font-bold text-[var(--text-primary)]" id="erpModalTitle">Direct ERP Links</h3>
            <p class="text-[10.5px] text-[var(--text-muted)]">Quick repository access for CE &amp; IT Faculty members</p>
          </div>
        </div>
        <button type="button" id="erpModalClose" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] text-base font-bold cursor-pointer" aria-label="Close modal">&times;</button>
      </div>

      <!-- Search & Filters -->
      <div class="p-3 sm:p-4 pb-2 border-b border-black/5 dark:border-white/10 flex flex-col gap-2.5 shrink-0">
        <!-- Search bar -->
        <div class="relative w-full">
          <svg class="w-4 h-4 absolute left-3 top-2.5 text-[var(--text-muted)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input
            type="text"
            id="erpSearchInput"
            placeholder="Search by faculty name, semester, or class..."
            autocomplete="off"
            class="w-full h-9 pl-9 pr-8 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] border border-white/80 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
          />
          <button type="button" id="erpSearchClear" class="hidden absolute right-2.5 top-2 text-[var(--text-muted)] hover:text-[var(--text-primary)] font-bold text-sm">&times;</button>
        </div>

        <!-- Filter Pills -->
        <div id="erpFilterPills" class="flex items-center gap-1.5 overflow-x-auto ctl-custom-scroll pb-1">
          <button type="button" class="erp-pill active px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold bg-[var(--color-primary)] text-white shrink-0 cursor-pointer" data-filter="all">All</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="ce">CE Dept</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="it">IT Dept</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="sem1">Sem 1</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="sem3">Sem 3</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="sem5">Sem 5</button>
          <button type="button" class="erp-pill px-3 py-1 rounded-full glass-pill text-[10.5px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] shrink-0 cursor-pointer" data-filter="sem7">Sem 7</button>
        </div>
      </div>

      <!-- Modal Body Cards Grid -->
      <div class="flex-1 overflow-y-auto ctl-custom-scroll p-3 sm:p-4">
        <div id="erpCardsGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <!-- Dynamically populated via JS -->
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="p-3 sm:p-4 border-t border-black/5 dark:border-white/10 flex items-center justify-between shrink-0">
        <span id="erpItemCount" class="text-xs font-mono font-bold text-[var(--text-muted)]">0 links available</span>
        <button type="button" id="erpCloseBtn" class="px-4 py-1.5 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Config variable for error handling if configured in PHP -->
  <script>
    window.APP_BASE_URL = '<?php echo function_exists("base_url") ? base_url("/") : "./"; ?>';
    const ctlExcelServerError = <?php echo !empty($config['ctl_excel_server_error']) ? 'true' : 'false'; ?>;
  </script>

  <!-- ================= APPLICATION SCRIPTS ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/ctlactivity.js') : 'assets/js/ctlactivity.js'; ?>"></script>
</body>
</html>
