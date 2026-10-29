<?php 
$baseDir = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3);
$studentUploadDir = $baseDir . '/uploads/student_timetable/';
if (!is_dir($studentUploadDir)) {
    @mkdir($studentUploadDir, 0777, true);
}

require_once 'student-timetable-parser.php';

// Handle file upload if POST request contains uploaded file
$uploadMessage = '';
$uploadSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['timetable_file'])) {
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
            parseExcelToTtCache($targetPath, $studentUploadDir . 'student_timetable_cache.json');
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
        parseExcelToTtCache($fullExcelPath, $cacheJsonPath);
    }
    if (file_exists($cacheJsonPath)) {
        $preloadedJsonData = file_get_contents($cacheJsonPath);
    }
}
$page_title = 'Student Timetable — CE & IT Department';
$page_description = 'Department of CE & IT — Student Class Timetable Viewer for weekly academic schedules.';
$page_css = [
    'assets/css/portal.css',
    'assets/css/faculty.css',
    'assets/css/timetable.css',
    'assets/css/student-timetable.css',
];
include 'head.php';
?>
    <?php include_once 'theme-toggle.php'; ?>

    <!-- ░░ Particles ░░ -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Glowing Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>

    <!-- Page Header -->
    <header class="rp-header">
        <div class="rp-header-inner container">
            <a href="<?php echo base_url('/'); ?>" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Portal
            </a>

            <div class="rp-header-center">
                <div class="rp-dept-badge">
                    <span class="rp-badge-dot"></span>
                    <span id="rpDeptBadgeText">Department of Information Technology</span>
                </div>
                <h1 class="rp-title" id="pageTitleMain">Student Class Timetable Module</h1>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <span class="portal-badge" id="portalBadge">Student Timetable</span>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="timetable-container container">

        <?php if (!$excelExists): ?>
            <!-- Clean Empty State when no Excel file is uploaded -->
            <div class="controls-card empty-state-card">
                <div class="empty-state-icon">📅</div>
                <h2 class="empty-state-title">No Student Timetable File Found</h2>
                <p class="empty-state-desc">
                    No Excel schedule file was found inside <code>uploads/student_timetable/</code>.<br>
                    Please place your student timetable Excel file into <code>uploads/student_timetable/</code> or upload it using the button below.
                </p>
                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <button type="button" onclick="quickSyncSheet(event, 'student')" class="print-btn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); display: inline-flex; align-items: center; gap: 8px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="quick-sync-icon">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="quick-sync-text">⚡ 1-Click Sync Google Sheet</span>
                    </button>
                    <input type="file" name="timetable_file" accept=".xlsx, .xls" required style="display: none;" id="excelFileInput" onchange="this.form.submit()">
                    <label for="excelFileInput" class="print-btn upload-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                        Upload Timetable Excel (.xlsx)
                    </label>
                </form>
            </div>

        <?php else: ?>

            <!-- Combined Controls Card (Sheet Selector + Print Button) -->
            <div class="controls-card">
                <div class="selector-group">
                    <span class="selector-label">Select Timetable Sheet:</span>
                    <div class="select-wrap student-select-wrap">
                        <select id="sheetSelect" class="custom-form-select">
                            <option value="">Loading Timetable Sheets...</option>
                        </select>
                        <span class="select-arrow">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="action-toolbar-right">
                    <button type="button" onclick="quickSyncSheet(event, 'student')" class="print-btn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="quick-sync-icon">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="quick-sync-text">Sync Live Sheet</span>
                    </button>
                    <button type="button" class="print-btn" onclick="window.print();">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Print / Download PDF
                    </button>
                </div>
            </div>

            <!-- Student Timetable Printable Card -->
            <div class="student-tt-card" id="printableTimetableCard">
                
                <!-- Desktop Timetable View -->
                <div class="desktop-timetable-view">
                    <table>
                        <!-- Header Rows -->
                        <tr>
                            <td colspan="8" class="title-header" id="stTitleHeader">Time Table</td>
                        </tr>
                        <tr class="header-info-row bold">
                            <td colspan="3" id="stSemesterVal">Semester : -</td>
                            <td colspan="2" id="stClassroomVal">Class Room : -</td>
                            <td colspan="3" id="stWefVal">W.E.F.: -</td>
                        </tr>

                        <!-- Days and Columns -->
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

                        <!-- Schedule Body -->
                        <tbody id="stTableBody">
                            <!-- Loaded dynamically from Excel sheet in uploads/student_timetable/ -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Timetable View -->
                <div class="mobile-timetable-view">
                    <!-- Mobile metadata display -->
                    <div class="mobile-meta-container">
                        <div class="meta-badge" id="mSemesterVal">Semester : -</div>
                        <div class="meta-badge" id="mClassroomVal">Class Room : -</div>
                        <div class="meta-badge" id="mWefVal">W.E.F.: -</div>
                    </div>
                    
                    <!-- Mobile Day Selector Tab Bar -->
                    <div class="mobile-tabs-container">
                        <button type="button" class="day-tab-btn" data-day="MON">Mon</button>
                        <button type="button" class="day-tab-btn" data-day="TUE">Tue</button>
                        <button type="button" class="day-tab-btn" data-day="WED">Wed</button>
                        <button type="button" class="day-tab-btn" data-day="THU">Thu</button>
                        <button type="button" class="day-tab-btn" data-day="FRI">Fri</button>
                        <button type="button" class="day-tab-btn" data-day="SAT">Sat</button>
                    </div>

                    <!-- Mobile dynamic slot list -->
                    <div class="mobile-slots-list" id="mSlotsList">
                        <div class="mobile-empty-state">Loading timetable slots...</div>
                    </div>
                </div>

                <!-- Subject and Faculty Details -->
                <div class="details-section" id="stDetailsSection">
                    <div class="details-column" id="stSubjectCol">
                        <strong>Subject</strong><br>-
                    </div>
                    <div class="details-column" id="stFacultyCol">
                        <strong>Faculty Details</strong><br>-
                    </div>
                    <div class="details-column" id="stLocationCol">
                        <strong>Location</strong><br>-
                    </div>
                </div>

            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Navigation Hub (Bottom Right) -->
    <?php 
    $active_page = 'student-timetable';
    include 'fab-nav.php'; 
    ?>

    <!-- Data & Excel Parsing Libraries -->
    <?php 
    if (!defined('SECURE_ACCESS')) define('SECURE_ACCESS', true);
    $config_file = __DIR__ . '/config.php';
    $config = file_exists($config_file) ? include $config_file : [];
    ?>
    <script>
        window.AUTO_SYNC_ENABLED = <?php echo json_encode(($appConfig['auto_sync_enabled'] ?? 1) == 1); ?>;
        window.AUTO_SYNC_INTERVAL = <?php echo json_encode(((int)($appConfig['auto_sync_interval'] ?? 300)) * 1000); ?>;
    </script>
    <script src="<?php echo v_asset('assets/js/timetableSync.js'); ?>"></script>
    
    <?php if ($excelExists): ?>
    <script>
        const preloadedWorkbook = <?php echo !empty($preloadedJsonData) ? $preloadedJsonData : 'null'; ?>;
    </script>
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>
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
            const stTableBody = document.getElementById("stTableBody");
            const stSubjectCol = document.getElementById("stSubjectCol");
            const stFacultyCol = document.getElementById("stFacultyCol");
            const stLocationCol = document.getElementById("stLocationCol");

            const itBtn = document.getElementById("dept-it-btn");
            const ceBtn = document.getElementById("dept-ce-btn");
            const rpDeptBadgeText = document.getElementById("rpDeptBadgeText");
            const portalBadge = document.getElementById("portalBadge");

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
            sheetSelect.addEventListener("change", (e) => {
                if (cachedWorkbook && e.target.value) {
                    try { localStorage.setItem("gmiu_student_tt_sheet", e.target.value); } catch(err) {}
                    renderSheet(e.target.value);
                }
            });

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

                // Format header values strictly from Excel sheet data 1:1
                stTitleHeader.textContent = title ? (String(title).toLowerCase().startsWith("time table") ? title : `Time Table: ${title}`) : `Time Table: ${sheetName}`;
                stSemesterVal.textContent = semester ? (String(semester).toLowerCase().startsWith("semester") ? semester : `Semester : ${semester}`) : "Semester : -";
                stClassroomVal.textContent = classroom ? (String(classroom).toLowerCase().startsWith("class room") || String(classroom).toLowerCase().startsWith("classroom") ? classroom : `Class Room : ${classroom}`) : "Class Room : -";
                stWefVal.textContent = wef ? (String(wef).toLowerCase().startsWith("w.e.f") ? wef : `W.E.F.: ${wef}`) : "W.E.F.: -";

                // Set mobile metadata as well
                const mSemesterVal = document.getElementById("mSemesterVal");
                const mClassroomVal = document.getElementById("mClassroomVal");
                const mWefVal = document.getElementById("mWefVal");
                if (mSemesterVal) mSemesterVal.textContent = semester ? (String(semester).toLowerCase().startsWith("semester") ? semester : `Semester : ${semester}`) : "Semester : -";
                if (mClassroomVal) mClassroomVal.textContent = classroom ? (String(classroom).toLowerCase().startsWith("class room") || String(classroom).toLowerCase().startsWith("classroom") ? classroom : `Class Room : ${classroom}`) : "Class Room : -";
                if (mWefVal) mWefVal.textContent = wef ? (String(wef).toLowerCase().startsWith("w.e.f") ? wef : `W.E.F.: ${wef}`) : "W.E.F.: -";

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

                // Map merged cells (rowspans) from sheetMerges or consecutive empty slot detection
                const mergeGrid = {}; // key: "rowIdx,colIdx" -> { rowspan, skip }

                if (sheetMerges) {
                    sheetMerges.forEach(m => {
                        const startR = m.s.r;
                        const endR = m.e.r;
                        const startC = m.s.c;
                        const endC = m.e.c;

                        if (endR > startR) {
                            mergeGrid[`${startR},${startC}`] = { rowspan: endR - startR + 1 };
                            for (let r = startR + 1; r <= endR; r++) {
                                mergeGrid[`${r},${startC}`] = { skip: true };
                            }
                        }
                    });
                } else {
                    // Fallback automatic 2-hour slot detection: if slot 1 has a lecture and slot 2 in same block is empty/dash
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
                            <td class="bold">${srNo++}</td>
                            <td class="bold">${timeCell}</td>
                    `;

                    // Day columns (columns 2 to 7 -> MON to SAT)
                    const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    for (let c = 2; c <= 7; c++) {
                        const dayName = dayNames[c - 2];
                        const cellKey = `${i},${c}`;
                        const mergeInfo = mergeGrid[cellKey] || {};

                        if (mergeInfo.skip) {
                            // Skip rendering cell because it is merged with the cell above
                            continue;
                        }

                        const rSpanAttr = mergeInfo.rowspan > 1 ? ` rowspan="${mergeInfo.rowspan}"` : '';
                        const cellVal = String(row[c] || "").trim();

                        if (cellVal) {
                            // Extract all faculty initials in parentheses for the legend set (e.g. PKG, JBS, SBC)
                            const facMatches = [...cellVal.matchAll(/\(([A-Z]{2,4})\)/gi)];
                            facMatches.forEach(m => facultyInitialsSet.add(m[1].toUpperCase()));

                            // Format multi-line cell content for display (preserve all lines & batches)
                            let formattedContent = cellVal
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/\r\n|\r|\n/g, "<br>");

                            // Highlight faculty initials in accent color
                            formattedContent = formattedContent.replace(/\(([A-Z]{2,4})\)/gi, '<span class="fac-init">($1)</span>');

                            // Highlight room codes in cyan/muted tag
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
                            rowHtml += `<td${rSpanAttr}>-</td>`;
                        }
                    }

                    rowHtml += `</tr>`;
                    bodyHtml += rowHtml;
                });

                stTableBody.innerHTML = bodyHtml || `<tr><td colspan="8">No schedule data found in sheet.</td></tr>`;

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

                // Locate Legend Section below the main schedule grid (rows containing "Subject" / "Location")
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
                            break; // Stop at signature row
                        }

                        // 1. Extract Subject entries (columns 0 & 1)
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

                        // 2. Extract Faculty entries (columns 2 to 6)
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
                stSubjectCol.innerHTML = subHtml || `<strong>Subject</strong><br>-`;

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
                stFacultyCol.innerHTML = facHtml || `<strong>Faculty Details</strong><br>-`;

                // Render Location Column
                const finalLocation = detectedLocation || classroom.replace(/Class Room\s*:\s*/i, "");
                stLocationCol.innerHTML = `<strong>Location</strong><br>${finalLocation}`;
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

                // Update active tab button style
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
                                    <span style="font-size: 10px; color: #64748b; font-weight: normal; margin-top: 2px; text-transform: lowercase;">to</span>
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

            // Wire up event listeners for mobile day tabs
            const dayTabs = document.querySelectorAll(".day-tab-btn");
            dayTabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    const day = tab.getAttribute("data-day");
                    renderMobileDay(day);
                });
            });
        });
    </script>
    <?php endif; ?>
</body>

</html>
