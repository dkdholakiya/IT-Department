<?php
$page_title = 'Log Zero Student Class — CE & IT Department';
$page_description = 'Record an incident of zero student attendance for a timetable session.';
$page_css = [
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'assets/css/zero-student-report.css',
];
$extra_head = '<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>' .
              '<script src="' . (function_exists('v_asset') ? v_asset('assets/vendor/xlsx/xlsx.full.min.js') : 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js') . '"></script>';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Calendar, CTL Activity, Timetable & Faculty (1600px Max-Width, 100vh on Desktop) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'zero-student-report';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Zero Student Log';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + CREATE WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Log Zero Class';
      $nav_subtitle = 'CE & IT Department • Record Attendance Incident';
      $nav_search_placeholder = 'Search timetable, faculty, subjects, rooms...';
      $nav_back_url = function_exists('base_url') ? base_url('/zero-student-report') : './zero-student-report';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: FULL WIDTH & FULL HEIGHT (MATCHING INDEX PAGE) ================= -->
      <main class="zs-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-4 lg:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300 gap-3">

        <!-- Scrollable Interior Workspace -->
        <div id="zsPage" class="flex-1 min-h-0 min-w-0 flex flex-col justify-between gap-3 sm:gap-4 overflow-y-auto zs-custom-scroll pr-0 sm:pr-1">

          <!-- 1. TOP CONTROLS & NAVIGATION TOOLBAR (Replaces Redundant In-Page Title) -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">
            <!-- Left: Back Link to Incident Directory -->
            <div class="flex items-center gap-2">
              <a
                href="<?php echo function_exists('base_url') ? base_url('/zero-student-report') : './zero-student-report'; ?>"
                class="h-8 px-3 rounded-xl glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
                title="Back to Incident Records Directory"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
                <span>Incident Directory</span>
              </a>
              <!-- Hidden element to maintain JS compatibility without error -->
              <span id="rp-dept-badge-text" class="hidden" aria-hidden="true"></span>
            </div>

            <!-- Action Controls Group: Segment Switcher + Excel Import Button -->
            <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto shrink-0 flex-wrap sm:flex-nowrap">
              <!-- Department Switcher Segment -->
              <div class="zs-segment-control flex-1 sm:flex-initial sm:min-w-[270px]">
                <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology" title="Switch to Information Technology">
                  <span class="hidden sm:inline">Information Technology</span>
                  <span class="sm:hidden">Info Tech</span>
                </button>
                <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering" title="Switch to Computer Engineering">
                  <span class="hidden sm:inline">Computer Engineering</span>
                  <span class="sm:hidden">Comp Eng</span>
                </button>
              </div>

              <!-- Import Excel Button -->
              <button type="button" class="import-pdf-btn shrink-0" id="import-pdf-btn" title="Batch Import from Excel Timetable File">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                </svg>
                <span>Import Excel</span>
              </button>
              <input type="file" id="pdf-import-input" accept=".xlsx, .xls" style="display: none;">
            </div>
          </div>

          <!-- 2. MAIN LOG FORM BODY -->
          <div class="zs-form-body flex-1 flex flex-col justify-between gap-3 sm:gap-4 py-1">

            <!-- Inputs Rows Container -->
            <div class="flex flex-col gap-3 sm:gap-4">
              <!-- Form Row 1: Session Date, Semester, Branch/Class -->
              <div class="form-row-custom-3">
                <div class="form-group">
                  <label for="entry-date">Session Date <span class="req">*</span></label>
                  <div class="relative">
                    <input type="text" id="entry-date" placeholder="Select date..." required autocomplete="off">
                  </div>
                </div>

                <div class="form-group">
                  <label for="entry-sem">Semester <span class="req">*</span></label>
                  <select id="entry-sem" required>
                    <option value="" disabled selected>Select semester...</option>
                    <option value="1">Semester 1</option>
                    <option value="3">Semester 3</option>
                    <option value="5">Semester 5</option>
                    <option value="7">Semester 7</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="entry-branch">Branch / Class <span class="req">*</span></label>
                  <div class="search-select-wrap">
                    <input type="text" id="entry-branch" placeholder="e.g. CLASS C B.TECH(IT)(ICT)" autocomplete="off" required>
                    <div class="search-dropdown-list" id="branchDropdownList"></div>
                  </div>
                </div>
              </div>

              <!-- Form Row 2: Classroom/Lab, Subject Code/Name, Faculty Initials -->
              <div class="form-row-3">
                <div class="form-group">
                  <label for="entry-room">Classroom / Lab <span class="req">*</span></label>
                  <div class="search-select-wrap">
                    <input type="text" id="entry-room" placeholder="e.g. FF-11" autocomplete="off" required>
                    <div class="search-dropdown-list" id="roomDropdownList"></div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entry-subject">Subject Code / Name <span class="req">*</span></label>
                  <div class="search-select-wrap">
                    <input type="text" id="entry-subject" placeholder="e.g. CV or ADA" autocomplete="off" required>
                    <div class="search-dropdown-list" id="subjectDropdownList"></div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entry-faculty">Faculty Initials <span class="req">*</span></label>
                  <div class="search-select-wrap">
                    <input type="text" id="entry-faculty" placeholder="e.g. SDG or IMI" autocomplete="off" required>
                    <div class="search-dropdown-list" id="facultyDropdownList"></div>
                  </div>
                </div>
              </div>

              <!-- Form Row 3: Time In, Time Out, No. of Students -->
              <div class="form-row-3">
                <div class="form-group">
                  <label for="entry-timein">Time In <span class="req">*</span></label>
                  <input type="text" id="entry-timein" placeholder="e.g. 09:30 AM" required autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="entry-timeout">Time Out <span class="req">*</span></label>
                  <input type="text" id="entry-timeout" placeholder="e.g. 11:30 AM" required autocomplete="off">
                </div>

                <div class="form-group">
                  <label for="entry-students">No. of Students</label>
                  <input type="text" id="entry-students" value="---" placeholder="e.g. --- or 0">
                </div>
              </div>

              <!-- Form Row 4: Remarks & CC Email Recipients (Balanced 1fr : 1.6fr on Desktop) -->
              <div class="form-row-remarks-cc">
                <div class="form-group">
                  <label for="entry-remarks">Remarks</label>
                  <input type="text" id="entry-remarks" value="NO STUDENTS" placeholder="Reason or remarks...">
                </div>

                <div class="form-group">
                  <label for="entry-cc">
                    CC Email Recipient(s)
                    <span class="text-[10px] text-[var(--text-muted)] font-normal tracking-normal lowercase">(default: HOD & incharge HOD)</span>
                  </label>
                  <div class="search-select-wrap">
                    <input type="text" id="entry-cc" placeholder="e.g. drchandarana@gmiu.edu.in, sbchauhan@gmiu.edu.in" autocomplete="off">
                    <div class="search-dropdown-list" id="ccDropdownList"></div>
                  </div>
                </div>
              </div>

              <!-- Informative Guide Cards: Gives aesthetic balance & visual dignity -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 pt-1">
                <div class="p-2.5 rounded-xl glass-pill border border-white/80 dark:border-white/10 flex items-center gap-2.5 shadow-sm">
                  <div class="w-7 h-7 rounded-lg bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 text-xs font-bold">
                    ⏱
                  </div>
                  <div class="min-w-0">
                    <div class="text-[11px] font-bold text-[var(--text-primary)]">Timetable Verified</div>
                    <div class="text-[10px] text-[var(--text-muted)] truncate">Auto-matches scheduled slot</div>
                  </div>
                </div>

                <div class="p-2.5 rounded-xl glass-pill border border-white/80 dark:border-white/10 flex items-center gap-2.5 shadow-sm">
                  <div class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xs font-bold">
                    📊
                  </div>
                  <div class="min-w-0">
                    <div class="text-[11px] font-bold text-[var(--text-primary)]">Google Sheet Sync</div>
                    <div class="text-[10px] text-[var(--text-muted)] truncate">Instantly writes to master log</div>
                  </div>
                </div>

                <div class="p-2.5 rounded-xl glass-pill border border-white/80 dark:border-white/10 flex items-center gap-2.5 shadow-sm">
                  <div class="w-7 h-7 rounded-lg bg-purple-500/15 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 text-xs font-bold">
                    ✉️
                  </div>
                  <div class="min-w-0">
                    <div class="text-[11px] font-bold text-[var(--text-primary)]">Email Dispatch</div>
                    <div class="text-[10px] text-[var(--text-muted)] truncate">Notifies faculty & department HOD</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Action Button: Flush At Card Bottom -->
            <div class="pt-2 shrink-0">
              <button type="button" class="submit-btn" id="add-entry-btn">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
                <span>Submit Zero Student Log</span>
              </button>
            </div>

          </div>

        </div><!-- /Interior Scroll Workspace -->

      </main>

    </div><!-- /Right Column -->

  </div><!-- /Main Wrapper -->

  <!-- ================= EXCEL IMPORT PREVIEW MODAL ================= -->
  <div class="zs-modal-overlay" id="pdfPreviewModal">
    <div class="zs-modal-card">
      <div class="zs-modal-header">
        <div>
          <h3>Verify Parsed Excel Records</h3>
          <p class="zs-modal-intro">Review matched faculty classes from uploaded timetable before importing.</p>
        </div>
        <button class="zs-modal-close" id="pdfModalClose" type="button" title="Close Modal">&times;</button>
      </div>

      <div class="zs-modal-body">
        <!-- Progress Bar & Batch CC Input -->
        <div class="zs-import-progress-container" id="importProgressContainer">
          <div class="flex items-center justify-between text-xs font-bold mb-1">
            <span id="importProgressText">Preparing import...</span>
            <span id="importProgressPercent">0%</span>
          </div>
          <div class="zs-import-progress-bar">
            <div class="zs-import-progress-fill" id="importProgressBarFill" style="width: 0%;"></div>
          </div>
        </div>

        <div class="zs-modal-toolbar">
          <div class="zs-import-cc-group">
            <label for="import-cc">Batch Notification CC:</label>
            <div class="search-select-wrap flex-1">
              <input type="text" id="import-cc" placeholder="Enter emails separated by comma..." autocomplete="off">
              <div class="search-dropdown-list" id="importCcDropdownList"></div>
            </div>
          </div>
        </div>

        <!-- Validation Summary Dashboard (KPI Row & Status Box) -->
        <div id="validationSummaryContainer" class="validation-summary-container" style="display: none;">
          <div class="val-kpi-row">
            <div>Total rows: <span id="val-total" class="val-num">0</span></div>
            <div>Eligible rows: <span id="val-eligible" class="val-num">0</span></div>
            <div>Imported rows: <span id="val-imported" class="val-num val-success-num">0</span></div>
            <div>Missing rows: <span id="val-missing" class="val-num val-danger-num">0</span></div>
            <div>Duplicates: <span id="val-duplicate" class="val-num val-warning-num">0</span></div>
          </div>
          <div id="val-status-box" class="val-status-box"></div>
          <div id="val-mismatch-details" class="val-mismatch-details" style="display: none;"></div>
        </div>

        <!-- Parsed Rows Table -->
        <div class="zs-table-container">
          <table class="zs-modal-table">
            <thead>
              <tr>
                <th style="width: 36px; text-align: center;"><input type="checkbox" id="select-all-pdf-rows" checked></th>
                <th style="width: 36px; text-align: center;">#</th>
                <th>Date</th>
                <th>Class/Lab</th>
                <th>Subject</th>
                <th>Faculty</th>
                <th>Branch</th>
                <th>Sem</th>
                <th>Time</th>
                <th>Students</th>
                <th>Remarks</th>
                <th>Faculty Claim</th>
                <th style="text-align: center;">Dept</th>
                <th style="text-align: center;">TT Match</th>
              </tr>
            </thead>
            <tbody id="pdf-parsed-rows-body">
              <!-- Injected dynamically via JS -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="zs-modal-footer">
        <button type="button" class="zs-modal-btn secondary" id="pdfModalCancel">Cancel</button>
        <button type="button" class="zs-modal-btn primary" id="pdfModalImportBtn">Import Selected (0)</button>
      </div>
    </div>
  </div>

  <!-- ================= POPUP MODAL OVERLAY (Settings / Profile / AI Assistant) ================= -->
  <div id="modalOverlay" class="fixed inset-0 z-50 bg-black/45 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-lg p-5 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 dark:border-white/10">
      <div class="flex items-center justify-between pb-3.5 border-b border-black/10 dark:border-white/10">
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
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/timetableData.js') : 'assets/js/timetableData.js'; ?>"></script>
  <?php
  $stCacheFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/uploads/student_timetable/student_timetable_cache.json';
  if (!file_exists($stCacheFile)) {
      $stCacheFile = dirname(__DIR__, 2) . '/uploads/student_timetable/student_timetable_cache.json';
  }
  $studentTtDataJson = file_exists($stCacheFile) ? file_get_contents($stCacheFile) : 'null';
  ?>
  <script>
    window.studentTimetableData = <?php echo $studentTtDataJson ?: 'null'; ?>;
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/zero-student-report.js') : 'assets/js/zero-student-report.js'; ?>"></script>
</body>
</html>
