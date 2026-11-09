<?php 
$baseDir = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);
$studentUploadDir = $baseDir . '/uploads/student_timetable/';
if (!is_dir($studentUploadDir)) {
    @mkdir($studentUploadDir, 0777, true);
}

$parserFile = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2)) . '/Services/student-timetable-parser.php';
if (file_exists($parserFile)) {
    require_once $parserFile;
} elseif (file_exists(__DIR__ . '/student-timetable-parser.php')) {
    require_once __DIR__ . '/student-timetable-parser.php';
}

// Handle file upload if POST request contains uploaded file
$uploadMessage = '';
$uploadSuccess = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_FILES['timetable_file'])) {
    $file = $_FILES['timetable_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, ['xlsx', 'xls'])) {
        // Clean old files in student_timetable directory
        $oldFiles = glob($studentUploadDir . '*.{xlsx,xls}', GLOB_BRACE);
        foreach ($oldFiles as $oldFile) {
            @unlink($oldFile);
        }
        @unlink($studentUploadDir . 'student_timetable_cache.json');
        
        $targetPath = $studentUploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $uploadSuccess = true;
            $uploadMessage = "File uploaded successfully!";
            if (function_exists('parseExcelToTtCache')) {
                parseExcelToTtCache($targetPath, $studentUploadDir . 'student_timetable_cache.json');
            }
        } else {
            $uploadMessage = "Error saving uploaded file.";
        }
    } else {
        $uploadMessage = "Invalid file format. Please upload an Excel (.xlsx or .xls) file.";
    }
}

// Check for existing files in uploads/student_timetable/
$excelFiles = glob($studentUploadDir . '*.{xlsx,xls}', GLOB_BRACE);
$excelExists = !empty($excelFiles);
$activeExcelFile = $excelExists ? basename($excelFiles[0]) : '';
$activeExcelPath = $excelExists ? 'uploads/student_timetable/' . basename($excelFiles[0]) : '';
$excelMTime = ($excelExists && file_exists($studentUploadDir . $activeExcelFile)) ? filemtime($studentUploadDir . $activeExcelFile) : 0;

$cacheJsonPath = $studentUploadDir . 'student_timetable_cache.json';
$preloadedJsonData = null;

if ($excelExists) {
    $fullExcelPath = $studentUploadDir . $activeExcelFile;
    if (!file_exists($cacheJsonPath) || filemtime($cacheJsonPath) < filemtime($fullExcelPath)) {
        if (function_exists('parseExcelToTtCache')) {
            parseExcelToTtCache($fullExcelPath, $cacheJsonPath);
        }
    }
    if (file_exists($cacheJsonPath)) {
        $preloadedJsonData = file_get_contents($cacheJsonPath);
    }
}

$page_title = 'Student Timetable — CE & IT Department';
$page_description = 'Department of CE & IT — Student Class Timetable Viewer for weekly academic schedules.';
$page_css = [
    'assets/css/timetable.css',
    'assets/css/student-timetable.css',
];
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Calendar, Timetable, CTL Activity & Faculty (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'student-timetable';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Student Timetable';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + STUDENT TIMETABLE WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Student Timetable';
      $nav_subtitle = 'CE & IT Department • Class Schedules & Academic Slots';
      $nav_search_placeholder = 'Search timetable sheet, slot, subject...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS STUDENT TIMETABLE DASHBOARD ================= -->
      <main class="tt-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">

        <div id="mainStudentTtContent" class="flex-1 min-h-0 min-w-0 flex flex-col gap-3 overflow-y-auto tt-custom-scroll pr-1">

          <!-- Hidden Compatibility Hooks for Existing JavaScript -->
          <span id="pageTitleMain" class="hidden" aria-hidden="true">Student Class Timetable Module</span>
          <span id="portalBadge" class="hidden" aria-hidden="true">Student Timetable</span>
          <span id="rpDeptBadgeText" class="hidden" aria-hidden="true">Department of Information Technology</span>
          <button id="dept-it-btn" type="button" class="hidden" aria-hidden="true"></button>
          <button id="dept-ce-btn" type="button" class="hidden" aria-hidden="true"></button>

          <?php if (!$excelExists): ?>
            <!-- Clean Empty State when no Excel file is uploaded -->
            <div class="glass-card p-6 sm:p-10 rounded-2xl flex flex-col items-center justify-center text-center gap-3 border border-white/80 dark:border-white/10 my-auto">
              <div class="w-14 h-14 rounded-2xl bg-[var(--color-primary)]/10 text-[var(--color-primary)] flex items-center justify-center text-2xl shadow-sm">
                📅
              </div>
              <h2 class="text-base sm:text-lg font-bold font-serif text-[var(--text-primary)] m-0">No Student Timetable File Found</h2>
              <p class="text-xs text-[var(--text-muted)] max-w-md leading-relaxed m-0">
                No Excel schedule file was found inside <code>uploads/student_timetable/</code>.<br>
                Please place your student timetable Excel file into the uploads directory or sync directly from Google Sheets.
              </p>
              <div class="flex items-center gap-2.5 flex-wrap justify-center mt-2">
                <button type="button" onclick="quickSyncSheet(event, 'student')" class="h-8 px-4 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                  <svg class="w-3.5 h-3.5 quick-sync-icon" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <span class="quick-sync-text">⚡ 1-Click Sync Google Sheet</span>
                </button>
                <form method="POST" enctype="multipart/form-data" class="inline-flex m-0 p-0">
                  <input type="file" name="timetable_file" accept=".xlsx,.xls" required class="hidden" id="excelFileInputEmpty" onchange="this.form.submit()">
                  <label for="excelFileInputEmpty" class="h-8 px-4 rounded-xl glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <span>Upload Timetable Excel</span>
                  </label>
                </form>
              </div>
            </div>

          <?php else: ?>

            <!-- 1. TOP HEADER TOOLBAR: SHEET SELECTOR + METADATA PILLS + ACTION BUTTONS -->
            <div class="glass-card p-2.5 sm:p-3 rounded-2xl flex flex-col xl:flex-row xl:items-center justify-between gap-2.5 border border-white/80 dark:border-white/10 shrink-0">
              
              <!-- Left Group: Sheet Selector Dropdown + Metadata Badges -->
              <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap flex-1 min-w-0">
                <div class="flex items-center gap-1.5 w-full sm:w-auto shrink-0">
                  <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] whitespace-nowrap">Sheet:</span>
                  <div class="relative flex-1 sm:flex-initial min-w-[170px] sm:min-w-[210px] w-full sm:w-auto">
                    <select id="sheetSelect" class="w-full h-8 pl-3 pr-8 rounded-xl glass-pill text-xs font-semibold text-[var(--text-primary)] border border-white/70 dark:border-white/10 focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer appearance-none bg-transparent">
                      <option value="">Loading Timetable Sheets...</option>
                    </select>
                    <svg class="w-3.5 h-3.5 text-[var(--text-muted)] absolute right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>

                <!-- Metadata Badges (Semester, Class Room, W.E.F.) -->
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span id="stSemesterVal" class="px-2.5 py-1 rounded-xl glass-pill text-[10px] sm:text-[10.5px] font-bold text-[var(--color-primary)] border border-white/60 dark:border-white/10 shadow-sm shrink-0">Semester : -</span>
                  <span id="stClassroomVal" class="px-2.5 py-1 rounded-xl glass-pill text-[10px] sm:text-[10.5px] font-semibold text-[var(--text-secondary)] border border-white/60 dark:border-white/10 shrink-0">Class Room : -</span>
                  <span id="stWefVal" class="px-2.5 py-1 rounded-xl glass-pill text-[10px] sm:text-[10.5px] font-mono text-[var(--text-muted)] border border-white/60 dark:border-white/10 shrink-0">W.E.F.: -</span>
                </div>
              </div>

              <!-- Right Group: Responsive View Switcher (Grid/Day) + Action Controls -->
              <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 self-start xl:self-auto flex-wrap">
                <!-- Responsive View Switcher Pills -->
                <div class="flex items-center p-0.5 rounded-xl glass-pill border border-white/60 dark:border-white/10 shrink-0" role="tablist">
                  <button type="button" id="viewModeGridBtn" class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition-all cursor-pointer text-white bg-[var(--color-primary)] shadow-sm" title="Show complete timetable grid">
                    📊 Table Grid
                  </button>
                  <button type="button" id="viewModeDayBtn" class="px-2 sm:px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition-all cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]" title="Show single day cards">
                    📅 Day Cards
                  </button>
                </div>

                <!-- Sync Live Sheet -->
                <button type="button" onclick="quickSyncSheet(event, 'student')" class="h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[11px] sm:text-xs font-bold text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm cursor-pointer" title="Sync from master Google Sheet">
                  <svg class="w-3.5 h-3.5 quick-sync-icon" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  <span class="quick-sync-text">Sync Live Sheet</span>
                </button>

                <!-- Print / Download PDF -->
                <button type="button" onclick="window.print();" class="h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[11px] sm:text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 cursor-pointer" title="Print or save as PDF">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <rect x="6" y="14" width="12" height="8" />
                  </svg>
                  <span>Print / PDF</span>
                </button>

                <!-- Upload File Button -->
                <form method="POST" enctype="multipart/form-data" class="inline-flex m-0 p-0">
                  <input type="file" name="timetable_file" accept=".xlsx,.xls" required class="hidden" id="excelFileInput" onchange="this.form.submit()">
                  <label for="excelFileInput" class="h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[11px] sm:text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 cursor-pointer" title="Upload replacement student timetable sheet">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <span>Upload Excel</span>
                  </label>
                </form>
              </div>

            </div>

            <!-- 2. STUDENT TIMETABLE PRINTABLE CARD -->
            <div class="student-tt-card glass-card p-3 sm:p-5 rounded-2xl border border-white/80 dark:border-white/10 flex-1 flex flex-col justify-between" id="printableTimetableCard">
              
              <!-- Desktop Timetable View -->
              <div class="desktop-timetable-view overflow-x-auto ctl-custom-scroll flex-1">
                <table>
                  <tr>
                    <td colspan="8" class="title-header" id="stTitleHeader">Time Table</td>
                  </tr>
                  <tr class="header-info-row bold">
                    <td colspan="3" id="stTableSemesterVal">Semester : -</td>
                    <td colspan="2" id="stTableClassroomVal">Class Room : -</td>
                    <td colspan="3" id="stTableWefVal">W.E.F.: -</td>
                  </tr>
                  <thead id="stTableHead">
                    <tr class="days-header-row bold">
                      <td width="5%">SR. NO.</td>
                      <td width="15%">TIME</td>
                      <td width="13%">MON</td>
                      <td width="13%">TUE</td>
                      <td width="13%">WED</td>
                      <td width="13%">THU</td>
                      <td width="13%">FRI</td>
                      <td width="15%">SAT</td>
                    </tr>
                  </thead>
                  <tbody id="stTableBody">
                    <!-- Dynamically populated via JavaScript -->
                  </tbody>
                </table>
              </div>

              <!-- Mobile Timetable View -->
              <div class="mobile-timetable-view">
                <div class="mobile-meta-container">
                  <div class="meta-badge" id="mSemesterVal">Semester : -</div>
                  <div class="meta-badge" id="mClassroomVal">Class Room : -</div>
                  <div class="meta-badge" id="mWefVal">W.E.F.: -</div>
                </div>
                <div class="mobile-tabs-container">
                  <button type="button" class="day-tab-btn" data-day="MON">Mon</button>
                  <button type="button" class="day-tab-btn" data-day="TUE">Tue</button>
                  <button type="button" class="day-tab-btn" data-day="WED">Wed</button>
                  <button type="button" class="day-tab-btn" data-day="THU">Thu</button>
                  <button type="button" class="day-tab-btn" data-day="FRI">Fri</button>
                  <button type="button" class="day-tab-btn" data-day="SAT">Sat</button>
                </div>
                <div class="mobile-slots-list" id="mSlotsList">
                  <div class="mobile-empty-state">Loading timetable slots...</div>
                </div>
              </div>

              <!-- Downside Legend Controls & Toggle Button -->
              <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-black/5 dark:border-white/10 shrink-0" id="stLegendToggleBar">
                <button
                  type="button"
                  id="toggleDetailsBtn"
                  class="h-8 px-3.5 rounded-xl glass-pill text-xs font-bold text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm cursor-pointer"
                  aria-expanded="false"
                  aria-controls="stDetailsSection"
                >
                  <svg id="toggleDetailsIcon" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                  </svg>
                  <span id="toggleDetailsText">Show Subject &amp; Faculty Details</span>
                </button>
                
                <div class="flex items-center gap-2 text-[11px] text-[var(--text-muted)]">
                  <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-primary)]"></span>
                  <span>Subject, Faculty &amp; Location Legend</span>
                </div>
              </div>

              <!-- Subject and Faculty Details Section (3-Column Grid) - Hidden by default -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 sm:gap-3 mt-2.5 hidden transition-all duration-300" id="stDetailsSection">
                <div class="details-column ctl-custom-scroll" id="stSubjectCol">
                  <strong>Subject</strong><br>-
                </div>
                <div class="details-column ctl-custom-scroll" id="stFacultyCol">
                  <strong>Faculty Details</strong><br>-
                </div>
                <div class="details-column ctl-custom-scroll" id="stLocationCol">
                  <strong>Location</strong><br>-
                </div>
              </div>

            </div>

          <?php endif; ?>

        </div>

      </main>

    </div>
  </div>

  <!-- APPLICATION DATA & SCRIPTS -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  
  <?php 
  if (!defined('SECURE_ACCESS')) define('SECURE_ACCESS', true);
  $configFile = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2)) . '/config.php';
  $appConfig = file_exists($configFile) ? include $configFile : [];
  ?>
  <script>
    window.AUTO_SYNC_ENABLED = <?php echo json_encode(($appConfig['auto_sync_enabled'] ?? 1) == 1); ?>;
    window.AUTO_SYNC_INTERVAL = <?php echo json_encode(((int)($appConfig['auto_sync_interval'] ?? 300)) * 1000); ?>;
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/timetableSync.js') : 'assets/js/timetableSync.js'; ?>"></script>
  
  <?php if ($excelExists): ?>
  <script>
    const preloadedWorkbook = <?php echo !empty($preloadedJsonData) ? $preloadedJsonData : 'null'; ?>;
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const excelPath = "<?php echo $activeExcelPath; ?>";
      const excelMTime = "<?php echo $excelMTime; ?>";
      const cacheKey = "st_tt_data_v3_" + excelMTime;

      const sheetSelect = document.getElementById("sheetSelect");

      const stTitleHeader = document.getElementById("stTitleHeader");
      const stSemesterVal = document.getElementById("stSemesterVal");
      const stClassroomVal = document.getElementById("stClassroomVal");
      const stWefVal = document.getElementById("stWefVal");
      const stTableSemesterVal = document.getElementById("stTableSemesterVal");
      const stTableClassroomVal = document.getElementById("stTableClassroomVal");
      const stTableWefVal = document.getElementById("stTableWefVal");

      const stTableBody = document.getElementById("stTableBody");
      const stSubjectCol = document.getElementById("stSubjectCol");
      const stFacultyCol = document.getElementById("stFacultyCol");
      const stLocationCol = document.getElementById("stLocationCol");

      let cachedWorkbook = preloadedWorkbook;

      // Fast instant load from preloaded object or sessionStorage fallback
      if (!cachedWorkbook) {
        try {
          const stored = sessionStorage.getItem(cacheKey);
          if (stored) {
            cachedWorkbook = JSON.parse(stored);
          }
        } catch(e) {}
      }

      if (cachedWorkbook && cachedWorkbook.SheetNames && cachedWorkbook.SheetNames.length > 0) {
        initTimetable(cachedWorkbook);
      } else {
        // Dynamic fallback if preloaded data is missing
        const script = document.createElement('script');
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js";
        script.onload = () => {
          fetch(excelPath)
            .then(res => res.arrayBuffer())
            .then(buffer => {
              const data = new Uint8Array(buffer);
              const wb = XLSX.read(data, { type: 'array' });
              cachedWorkbook = {
                SheetNames: wb.SheetNames,
                SheetsData: {},
                MergesData: {}
              };
              wb.SheetNames.forEach(sName => {
                const sheet = wb.Sheets[sName];
                cachedWorkbook.SheetsData[sName] = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: "" });
                cachedWorkbook.MergesData[sName] = sheet['!merges'] || null;
              });
              try {
                sessionStorage.setItem(cacheKey, JSON.stringify(cachedWorkbook));
              } catch(e) {}
              initTimetable(cachedWorkbook);
            })
            .catch(err => {
              console.error("Error loading student timetable Excel:", err);
            });
        };
        document.head.appendChild(script);
      }

      function initTimetable(wbData) {
        const allSheets = wbData.SheetNames;
        if (!sheetSelect) return;
        sheetSelect.innerHTML = "";

        if (!allSheets || allSheets.length === 0) {
          sheetSelect.innerHTML = `<option value="">No sheets found in Excel file</option>`;
          return;
        }

        const savedSheet = localStorage.getItem("gmiu_student_tt_sheet");
        let defaultSheet = allSheets[0];
        if (savedSheet && allSheets.includes(savedSheet)) {
          defaultSheet = savedSheet;
        }

        allSheets.forEach((sName, index) => {
          const option = document.createElement("option");
          option.value = sName;
          option.textContent = `${index + 1}. ${sName}`;
          if (sName === defaultSheet) option.selected = true;
          sheetSelect.appendChild(option);
        });

        renderSheet(defaultSheet);
      }

      // Sheet Selection Change Listener
      if (sheetSelect) {
        sheetSelect.addEventListener("change", (e) => {
          if (cachedWorkbook && e.target.value) {
            try { localStorage.setItem("gmiu_student_tt_sheet", e.target.value); } catch(err) {}
            renderSheet(e.target.value);
          }
        });
      }

      // Render selected sheet contents from Excel file
      function renderSheet(sheetName) {
        if (!cachedWorkbook || !cachedWorkbook.SheetsData[sheetName]) return;

        const rows = cachedWorkbook.SheetsData[sheetName];
        const sheetMerges = cachedWorkbook.MergesData[sheetName];

        if (!rows || rows.length === 0) return;

        // Extract Metadata dynamically from top 8 rows of Excel sheet
        let title = "";
        let semester = "";
        let classroom = "";
        let wef = "";

        // Scan top 8 rows of sheet for metadata
        for (let i = 0; i < Math.min(8, rows.length); i++) {
          const row = rows[i];
          if (!row) continue;

          for (let c = 0; c < row.length; c++) {
            const cellVal = String(row[c] || "").trim();
            if (!cellVal) continue;
            const lower = cellVal.toLowerCase();

            // Title match
            if ((lower.includes("time table") || lower.includes("timetable")) && !title) {
              title = cellVal;
            }
            // Semester match
            if (lower.includes("semester") && !semester) {
              semester = cellVal;
            }
            // Classroom match
            if ((lower.includes("class room") || lower.includes("classroom") || lower.includes("room :") || lower.includes("room:")) && !classroom && !lower.includes("b11") && !lower.includes("x11") && !lower.includes("x22")) {
              classroom = cellVal;
            }
            // W.E.F. match
            if (lower.includes("w.e.f") && !wef) {
              wef = cellVal;
            }
          }
        }

        // Format header values
        const formattedTitle = title ? (String(title).toLowerCase().startsWith("time table") ? title : `Time Table: ${title}`) : `Time Table: ${sheetName}`;
        const formattedSemester = semester ? (String(semester).toLowerCase().startsWith("semester") ? semester : `Semester : ${semester}`) : "Semester : -";
        const formattedClassroom = classroom ? (String(classroom).toLowerCase().startsWith("class room") || String(classroom).toLowerCase().startsWith("classroom") ? classroom : `Class Room : ${classroom}`) : "Class Room : -";
        const formattedWef = wef ? (String(wef).toLowerCase().startsWith("w.e.f") ? wef : `W.E.F.: ${wef}`) : "W.E.F.: -";

        if (stTitleHeader) stTitleHeader.textContent = formattedTitle;
        if (stSemesterVal) stSemesterVal.textContent = formattedSemester;
        if (stClassroomVal) stClassroomVal.textContent = formattedClassroom;
        if (stWefVal) stWefVal.textContent = formattedWef;

        if (stTableSemesterVal) stTableSemesterVal.textContent = formattedSemester;
        if (stTableClassroomVal) stTableClassroomVal.textContent = formattedClassroom;
        if (stTableWefVal) stTableWefVal.textContent = formattedWef;

        // Set mobile metadata
        const mSemesterVal = document.getElementById("mSemesterVal");
        const mClassroomVal = document.getElementById("mClassroomVal");
        const mWefVal = document.getElementById("mWefVal");
        if (mSemesterVal) mSemesterVal.textContent = formattedSemester;
        if (mClassroomVal) mClassroomVal.textContent = formattedClassroom;
        if (mWefVal) mWefVal.textContent = formattedWef;

        // Locate header row (SR. NO. or TIME or MON)
        let headerIdx = -1;
        for (let i = 0; i < Math.min(10, rows.length); i++) {
          if (!rows[i]) continue;
          const rStr = rows[i].join(" ").toLowerCase();
          if (rStr.includes("sr. no.") || rStr.includes("time") || rStr.includes("mon")) {
            headerIdx = i;
            break;
          }
        }
        if (headerIdx === -1) headerIdx = 4; // Fallback

        // Build index of schedule row candidates
        const scheduleRowIndices = [];
        for (let i = headerIdx + 1; i < rows.length; i++) {
          const row = rows[i];
          if (!row || row.length === 0) continue;
          const rowStr = row.join(" ").trim();
          if (!rowStr) continue;
          const lowerRowStr = rowStr.toLowerCase();
          if (lowerRowStr.includes("total load") || 
            lowerRowStr.includes("co-ordinator") || 
            lowerRowStr.includes("subject") || 
            lowerRowStr.includes("faculty") || 
            lowerRowStr.includes("location") ||
            lowerRowStr.includes("load distribution")) {
            break;
          }
          scheduleRowIndices.push(i);
        }

        // Map merged cells (rowspans) from sheetMerges
        const mergeGrid = {};

        if (sheetMerges) {
          sheetMerges.forEach(m => {
            const startR = m.s.r;
            const endR = m.e.r;
            const startC = m.s.c;
            if (endR > startR) {
              mergeGrid[`${startR},${startC}`] = { rowspan: endR - startR + 1 };
              for (let r = startR + 1; r <= endR; r++) {
                mergeGrid[`${r},${startC}`] = { skip: true };
              }
            }
          });
        } else {
          // Automatic 2-hour slot detection fallback
          for (let idx = 0; idx < scheduleRowIndices.length - 1; idx++) {
            const currR = scheduleRowIndices[idx];
            const nextR = scheduleRowIndices[idx + 1];

            const currRow = rows[currR];
            const nextRow = rows[nextR];

            const isCurrRecess = currRow.join(" ").toLowerCase().includes("recess") || currRow.join(" ").toLowerCase().includes("break");
            const isNextRecess = nextRow.join(" ").toLowerCase().includes("recess") || nextRow.join(" ").toLowerCase().includes("break");

            if (!isCurrRecess && !isNextRecess) {
              for (let c = 2; c <= 7; c++) {
                const currVal = String(currRow[c] || "").trim();
                const nextVal = String(nextRow[c] || "").trim();

                if (currVal && (!nextVal || nextVal === "-")) {
                  mergeGrid[`${currR},${c}`] = { rowspan: 2 };
                  mergeGrid[`${nextR},${c}`] = { skip: true };
                }
              }
            }
          }
        }

        // Initialize mobile days data dictionary
        const daysData = {
          'MON': [],
          'TUE': [],
          'WED': [],
          'THU': [],
          'FRI': [],
          'SAT': []
        };

        // Render timetable schedule rows
        let bodyHtml = "";
        let srNo = 1;
        const subjectsSet = new Set();
        const facultyInitialsSet = new Set();

        scheduleRowIndices.forEach(i => {
          const row = rows[i];
          const rowStr = row.join(" ").trim();
          const isRecess = rowStr.toLowerCase().includes("recess") || rowStr.toLowerCase().includes("break");

          if (isRecess) {
            const recessLabel = row.find(c => String(c).toLowerCase().includes("recess") || String(c).toLowerCase().includes("break")) || "RECESS";
            bodyHtml += `
              <tr class="recess-row">
                <td colspan="8">${recessLabel}</td>
              </tr>
            `;

            // Add recess to all mobile days
            const timeCell = row[1] || row[0] || "RECESS";
            const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
            dayNames.forEach(day => {
              daysData[day].push({
                isRecess: true,
                label: recessLabel,
                time: timeCell
              });
            });
            return;
          }

          // Extract Time slot and Day columns
          const timeCell = row[1] || row[0] || "";
          if (!timeCell || (!String(timeCell).includes(":") && !String(timeCell).match(/\d{1,2}/))) return;

          let rowHtml = `
            <tr>
              <td class="bold font-mono text-xs">${srNo++}</td>
              <td class="bold font-mono text-xs text-[var(--text-primary)]">${timeCell}</td>
          `;

          // Day columns (columns 2 to 7 -> MON to SAT)
          const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
          for (let c = 2; c <= 7; c++) {
            const dayName = dayNames[c - 2];
            const cellKey = `${i},${c}`;
            const mergeInfo = mergeGrid[cellKey] || {};

            if (mergeInfo.skip) {
              continue;
            }

            const rSpanAttr = mergeInfo.rowspan > 1 ? ` rowspan="${mergeInfo.rowspan}"` : '';
            const cellVal = String(row[c] || "").trim();

            if (cellVal) {
              // Extract faculty initials in parentheses
              const facMatches = [...cellVal.matchAll(/\(([A-Z]{2,4})\)/gi)];
              facMatches.forEach(m => facultyInitialsSet.add(m[1].toUpperCase()));

              // Format multi-line cell content for display
              let formattedContent = cellVal
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\r\n|\r|\n/g, "<br>");

              // Highlight faculty initials in accent color
              formattedContent = formattedContent.replace(/\(([A-Z]{2,4})\)/gi, '<span class="fac-init">($1)</span>');

              // Highlight room codes
              formattedContent = formattedContent.replace(/\(([A-Z]{1,2}-\d{1,2})\)/gi, '<span class="room-tag">($1)</span>');

              rowHtml += `<td${rSpanAttr}><div class="slot-pill">${formattedContent}</div></td>`;

              // Mobile schedule data structure population
              let timeString = timeCell;
              if (mergeInfo.rowspan > 1) {
                const currentIdxInIndices = scheduleRowIndices.indexOf(i);
                const endRowIdx = scheduleRowIndices[currentIdxInIndices + mergeInfo.rowspan - 1];
                const endRow = rows[endRowIdx];
                if (endRow) {
                  const endTimeCell = endRow[1] || endRow[0] || "";
                  timeString = mergeTimes(timeCell, endTimeCell);
                }
              }

              let subjectName = cellVal;
              let facultyInitials = "";
              let roomTag = "";

              const facMatch = cellVal.match(/\(([A-Z]{2,4})\)/i);
              if (facMatch) {
                facultyInitials = facMatch[1].toUpperCase();
                subjectName = subjectName.replace(facMatch[0], "").trim();
              }

              const roomMatch = cellVal.match(/\(([A-Z]{1,2}-\d{1,2})\)/i);
              if (roomMatch) {
                roomTag = roomMatch[1].toUpperCase();
                subjectName = subjectName.replace(roomMatch[0], "").trim();
              }

              subjectName = subjectName.replace(/\r\n|\r|\n/g, " / ").replace(/\s+/g, " ").trim();

              daysData[dayName].push({
                isRecess: false,
                time: timeString,
                subject: subjectName,
                faculty: facultyInitials,
                room: roomTag
              });
            } else {
              rowHtml += `<td${rSpanAttr} class="text-[var(--text-muted)] opacity-60">-</td>`;
            }
          }

          rowHtml += `</tr>`;
          bodyHtml += rowHtml;
        });

        if (stTableBody) {
          stTableBody.innerHTML = bodyHtml || `<tr><td colspan="8" class="p-6 text-xs text-[var(--text-muted)] text-center">No schedule data found in sheet.</td></tr>`;
        }

        // Store mobile data in global scope
        window.mobileDaysData = daysData;

        // Determine which tab should be loaded
        const weekdayNames = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
        const currentDayIndex = new Date().getDay();
        let dayToSelect = weekdayNames[currentDayIndex];
        if (dayToSelect === "SUN" || !daysData[dayToSelect]) {
          dayToSelect = "MON";
        }

        if (window.currentActiveDay && daysData[window.currentActiveDay]) {
          dayToSelect = window.currentActiveDay;
        }

        renderMobileDay(dayToSelect);

        // Locate Legend Section below the main schedule grid
        let legendStartIdx = -1;
        for (let i = headerIdx + 1; i < rows.length; i++) {
          const rStr = (rows[i] || []).join(" ").toLowerCase();
          if (rStr.includes("subject") || rStr.includes("location") || rStr.includes("faculty")) {
            legendStartIdx = i;
            break;
          }
        }

        const excelSubjectList = [];
        const excelFacultyList = [];
        let detectedLocation = "";

        if (legendStartIdx !== -1) {
          for (let i = legendStartIdx; i < rows.length; i++) {
            const row = rows[i];
            if (!row || row.length === 0) continue;

            const rowStr = row.join(" ").trim();
            if (rowStr.toLowerCase().includes("co-ordinator") || rowStr.toLowerCase().includes("head of department")) {
              break;
            }

            // 1. Extract Subject entries
            const c0 = String(row[0] || "").trim();
            const c1 = String(row[1] || "").trim();
            if (c0 && c0.toLowerCase() !== "subject") {
              if (c0.includes(":") && c1 && !c1.toLowerCase().includes("prof")) {
                excelSubjectList.push(`${c0} ${c1}`);
              } else if (c0.includes(":") && c0.length > 5) {
                excelSubjectList.push(c0);
              } else if (c0 && c1 && !c1.toLowerCase().includes("prof") && !c1.toLowerCase().includes("location")) {
                excelSubjectList.push(`${c0} : ${c1}`);
              }
            }

            // 2. Extract Faculty entries
            for (let c = 2; c < row.length; c++) {
              const cellVal = String(row[c] || "").trim();
              if (!cellVal || cellVal.toLowerCase() === "location") continue;

              if (cellVal.includes(":") && (cellVal.toLowerCase().includes("prof") || cellVal.toLowerCase().includes("dr.") || cellVal.toLowerCase().includes("mr.") || cellVal.toLowerCase().includes("ms."))) {
                excelFacultyList.push(cellVal);
              } else if (cellVal.includes(":") && c < row.length - 1) {
                const nextCell = String(row[c+1] || "").trim();
                if (nextCell.toLowerCase().includes("prof") || nextCell.toLowerCase().includes("dr") || nextCell.toLowerCase().includes("mr") || nextCell.toLowerCase().includes("ms")) {
                  excelFacultyList.push(`${cellVal} ${nextCell}`);
                }
              }
            }

            // 3. Extract Location cell
            for (let c = 0; c < row.length; c++) {
              const cellVal = String(row[c] || "").trim();
              if (cellVal && (cellVal.toLowerCase().includes("gf-") || cellVal.toLowerCase().includes("tf-") || cellVal.toLowerCase().includes("ff-") || cellVal.toLowerCase().includes("sf-"))) {
                detectedLocation = cellVal;
              }
            }
          }
        }

        // Render Subject Column
        let subHtml = `<strong>Subject</strong><br>`;
        if (excelSubjectList.length > 0) {
          excelSubjectList.forEach(line => {
            subHtml += `${line}<br>`;
          });
        } else {
          subjectsSet.forEach(sub => {
            subHtml += `${sub}<br>`;
          });
        }
        if (stSubjectCol) stSubjectCol.innerHTML = subHtml || `<strong>Subject</strong><br>-`;

        // Render Faculty Column
        let facHtml = `<strong>Faculty Details</strong><br>`;
        if (excelFacultyList.length > 0) {
          excelFacultyList.forEach(line => {
            facHtml += `${line}<br>`;
          });
        } else {
          facultyInitialsSet.forEach(init => {
            facHtml += `${init} : Prof. ${init}<br>`;
          });
        }
        if (stFacultyCol) stFacultyCol.innerHTML = facHtml || `<strong>Faculty Details</strong><br>-`;

        // Render Location Column
        const finalLocation = detectedLocation || classroom.replace(/Class Room\s*:\s*/i, "");
        if (stLocationCol) stLocationCol.innerHTML = `<strong>Location</strong><br>${finalLocation || '-'}`;
      }

      // Time merging helper
      function mergeTimes(t1, t2) {
        if (!t1) return t2 || "";
        if (!t2) return t1 || "";
        const parts1 = String(t1).split('-');
        const parts2 = String(t2).split('-');
        if (parts1.length === 2 && parts2.length === 2) {
          return parts1[0].trim() + ' - ' + parts2[1].trim();
        }
        return t1 + ' - ' + t2;
      }

      // Mobile day-by-day card list renderer
      function renderMobileDay(dayName) {
        window.currentActiveDay = dayName;

        const dayTabs = document.querySelectorAll(".day-tab-btn");
        dayTabs.forEach(tab => {
          if (tab.getAttribute("data-day") === dayName) {
            tab.classList.add("active");
          } else {
            tab.classList.remove("active");
          }
        });

        const mSlotsList = document.getElementById("mSlotsList");
        if (!mSlotsList) return;

        const slots = (window.mobileDaysData && window.mobileDaysData[dayName]) || [];
        if (slots.length === 0) {
          mSlotsList.innerHTML = `<div class="mobile-empty-state">No lectures or breaks scheduled for ${dayName}.</div>`;
          return;
        }

        let slotsHtml = "";
        slots.forEach(slot => {
          if (slot.isRecess) {
            slotsHtml += `
              <div class="mobile-recess-divider">
                <span>${slot.label} (${slot.time})</span>
              </div>
            `;
          } else {
            const facultyText = slot.faculty ? `<span class="mobile-slot-faculty">Prof. ${slot.faculty}</span>` : "";
            const roomText = slot.room ? `<span class="mobile-slot-room">Room: ${slot.room}</span>` : "";
            
            const timeParts = slot.time.split('-');
            const startTime = timeParts[0] ? timeParts[0].trim() : "";
            const endTime = timeParts[1] ? timeParts[1].trim() : "";

            slotsHtml += `
              <div class="mobile-slot-card">
                <div class="mobile-slot-time">
                  <span>${startTime}</span>
                  <span class="text-[9.5px] text-[var(--text-muted)] font-normal lowercase">to</span>
                  <span>${endTime}</span>
                </div>
                <div class="mobile-slot-details">
                  <div class="mobile-slot-subject">${slot.subject}</div>
                  ${(facultyText || roomText) ? `
                  <div class="mobile-slot-meta">
                    ${facultyText}
                    ${roomText}
                  </div>
                  ` : ''}
                </div>
              </div>
            `;
          }
        });

        mSlotsList.innerHTML = slotsHtml;
      }

      // Event listeners for mobile day tabs
      const dayTabs = document.querySelectorAll(".day-tab-btn");
      dayTabs.forEach(tab => {
        tab.addEventListener("click", () => {
          const day = tab.getAttribute("data-day");
          renderMobileDay(day);
        });
      });

      // Toggle Subject, Faculty & Location Details Cards
      const toggleDetailsBtn = document.getElementById("toggleDetailsBtn");
      const stDetailsSection = document.getElementById("stDetailsSection");
      const toggleDetailsText = document.getElementById("toggleDetailsText");
      const toggleDetailsIcon = document.getElementById("toggleDetailsIcon");

      if (toggleDetailsBtn && stDetailsSection) {
        toggleDetailsBtn.addEventListener("click", () => {
          const isHidden = stDetailsSection.classList.contains("hidden");
          if (isHidden) {
            stDetailsSection.classList.remove("hidden");
            toggleDetailsBtn.setAttribute("aria-expanded", "true");
            if (toggleDetailsText) toggleDetailsText.textContent = "Hide Subject & Faculty Details";
            if (toggleDetailsIcon) toggleDetailsIcon.classList.add("rotate-180");
          } else {
            stDetailsSection.classList.add("hidden");
            toggleDetailsBtn.setAttribute("aria-expanded", "false");
            if (toggleDetailsText) toggleDetailsText.textContent = "Show Subject & Faculty Details";
            if (toggleDetailsIcon) toggleDetailsIcon.classList.remove("rotate-180");
          }
        });
      }

      // Responsive View Switcher (Table Grid vs Day Cards)
      const viewModeGridBtn = document.getElementById("viewModeGridBtn");
      const viewModeDayBtn  = document.getElementById("viewModeDayBtn");
      const printableCard   = document.getElementById("printableTimetableCard");

      function setViewMode(mode) {
        if (!printableCard) return;
        if (mode === 'grid') {
          printableCard.classList.add('show-grid');
          printableCard.classList.remove('show-day');
          if (viewModeGridBtn) {
            viewModeGridBtn.classList.add('text-white', 'bg-[var(--color-primary)]', 'shadow-sm');
            viewModeGridBtn.classList.remove('text-[var(--text-muted)]');
          }
          if (viewModeDayBtn) {
            viewModeDayBtn.classList.remove('text-white', 'bg-[var(--color-primary)]', 'shadow-sm');
            viewModeDayBtn.classList.add('text-[var(--text-muted)]');
          }
        } else {
          printableCard.classList.add('show-day');
          printableCard.classList.remove('show-grid');
          if (viewModeDayBtn) {
            viewModeDayBtn.classList.add('text-white', 'bg-[var(--color-primary)]', 'shadow-sm');
            viewModeDayBtn.classList.remove('text-[var(--text-muted)]');
          }
          if (viewModeGridBtn) {
            viewModeGridBtn.classList.remove('text-white', 'bg-[var(--color-primary)]', 'shadow-sm');
            viewModeGridBtn.classList.add('text-[var(--text-muted)]');
          }
        }
      }

      if (viewModeGridBtn) viewModeGridBtn.addEventListener("click", () => setViewMode('grid'));
      if (viewModeDayBtn)  viewModeDayBtn.addEventListener("click", () => setViewMode('day'));

      // Automatically sync view buttons on load according to screen width
      if (window.innerWidth <= 900) {
        setViewMode('day');
      } else {
        setViewMode('grid');
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>
