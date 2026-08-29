<?php include 'auth-check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Zero Student Report Management for classes where zero students reported as per timetable.">
    <title>Zero Student Log — CE & IT Department</title>

    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/portal.css">

    <!-- Flatpickr Date/Time Picker CSS and JS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- SheetJS CDN Library for client-side Excel reading -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- Scoped Page Stylesheet -->
    <link rel="stylesheet" href="assets/css/zero-student-report.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/theme-light.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Background particles -->
    <div class="particles" aria-hidden="true">
        <div class="particle" style="width:4px; height:4px; left:8%;  background:rgba(192,57,43,0.5);  animation: rise 20s linear infinite;"></div>
        <div class="particle" style="width:3px; height:3px; left:22%; background:rgba(255,255,255,0.2); animation: rise 25s linear -6s infinite;"></div>
        <div class="particle" style="width:5px; height:5px; left:38%; background:rgba(37,99,235,0.5);  animation: rise 18s linear -3s infinite;"></div>
        <div class="particle" style="width:3px; height:3px; left:55%; background:rgba(255,255,255,0.15);animation: rise 22s linear -10s infinite;"></div>
        <div class="particle" style="width:4px; height:4px; left:68%; background:rgba(124,58,237,0.45);animation: rise 28s linear -1s infinite;"></div>
        <div class="particle" style="width:3px; height:3px; left:80%; background:rgba(255,255,255,0.18);animation: rise 24s linear -8s infinite;"></div>
        <div class="particle" style="width:5px; height:5px; left:90%; background:rgba(192,57,43,0.4);  animation: rise 19s linear -4s infinite;"></div>
    </div>

    <!-- Glow orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>
    <div class="orb orb-3" aria-hidden="true"></div>

    <!-- Grid overlay -->
    <div class="zs-grid-overlay" aria-hidden="true"></div>

    <div class="zs-page" id="zsPage">

        <!-- ── Page Header ── -->
        <header class="rp-header">
            <div class="rp-header-inner">
                <a href="./" class="back-btn" id="backBtn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Portal
                </a>

                <div class="rp-header-center">
                    <div class="rp-dept-badge" id="rp-dept-badge">
                        <span class="rp-badge-dot"></span>
                        <span id="rp-dept-badge-text">Department of Information Technology</span>
                    </div>
                    <h1 class="rp-title">Zero Student Report</h1>
                </div>

                <span class="portal-badge">Timetable Tool</span>
            </div>
        </header>

        <!-- ── Main Centered Card Layout ── -->
        <main class="zs-main">
            <div class="zs-form-card">
                <div class="zs-form-header" style="justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 14px; flex: 1;">
                        <div class="zs-form-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h2>Log Zero Class</h2>
                            <p>Record a class where no students reported.</p>
                        </div>
                    </div>
                    <button type="button" class="import-pdf-btn" id="import-pdf-btn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span>Import Excel</span>
                    </button>
                    <input type="file" id="pdf-import-input" accept=".xlsx, .xls" style="display: none;">
                </div>

                <div class="zs-form-body">
                    <div class="form-group">
                        <label>Department / Branch Select</label>
                        <div class="zs-segment-control">
                            <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology">Information Technology</button>
                            <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
                        </div>
                    </div>

                    <div class="form-row-custom-3">
                        <div class="form-group">
                            <label for="entry-date">Session Date <span class="req">*</span></label>
                            <input type="text" id="entry-date" required>
                        </div>
                        <div class="form-group">
                            <label for="entry-sem">Semester <span class="req">*</span></label>
                            <select id="entry-sem" required>
                                <option value="" disabled selected>Select...</option>
                                <option value="1">1</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="entry-branch">Branch/Class <span class="req">*</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-branch" placeholder="e.g. CLASS C B.TECH(IT)(ICT)" autocomplete="off" required>
                                <div class="search-dropdown-list" id="branchDropdownList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="entry-room">Classroom/Lab <span class="req">*</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-room" placeholder="e.g. FF-11" autocomplete="off" required>
                                <div class="search-dropdown-list" id="roomDropdownList"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="entry-subject">Subject Code/Name <span class="req">*</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-subject" placeholder="e.g. CV" autocomplete="off" required>
                                <div class="search-dropdown-list" id="subjectDropdownList"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="entry-faculty">Faculty Initials <span class="req">*</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-faculty" placeholder="e.g. SDG" autocomplete="off" required>
                                <div class="search-dropdown-list" id="facultyDropdownList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-3">
                        <div class="form-group">
                            <label for="entry-timein">Time In <span class="req">*</span></label>
                            <input type="text" id="entry-timein" required>
                        </div>
                        <div class="form-group">
                            <label for="entry-timeout">Time Out <span class="req">*</span></label>
                            <input type="text" id="entry-timeout" required>
                        </div>
                        <div class="form-group">
                            <label for="entry-students">No. of Students</label>
                            <input type="text" id="entry-students" value="---" placeholder="e.g. --- or 10 or 0">
                        </div>
                    </div>

                    <div class="form-row-2 form-row-remarks-cc">
                        <div class="form-group">
                            <label for="entry-remarks">Remarks</label>
                            <input type="text" id="entry-remarks" value="NO STUDENTS">
                        </div>
                        <div class="form-group">
                            <label for="entry-cc">CC Email Recipient(s) <span style="font-size: 10.5px; color: var(--text-dim); text-transform: none; font-weight: normal;">(Default: HOD & Incharge HOD)</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-cc" placeholder="e.g. drchandarana@gmiu.edu.in, sbchauhan@gmiu.edu.in" autocomplete="off">
                                <div class="search-dropdown-list" id="ccDropdownList"></div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="submit-btn" id="add-entry-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M22 2L11 13" />
                            <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                        </svg>
                        <span>Submit Zero Student Log</span>
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <?php 
        $footer_class = 'zs-footer';
        include 'footer.php'; 
        ?>

    </div>

    <?php 
    $active_page = 'zero-student-report';
    include 'fab-nav.php'; 
    ?>

    <!-- Faculty Member & Timetable Data source -->
    <script src="assets/js/facultyData.js"></script>
    <script src="assets/js/timetableData.js"></script>
    <?php
    $stCacheFile = __DIR__ . '/uploads/student_timetable/student_timetable_cache.json';
    $studentTtDataJson = file_exists($stCacheFile) ? file_get_contents($stCacheFile) : 'null';
    ?>
    <script>
    window.studentTimetableData = <?php echo $studentTtDataJson; ?>;
    </script>

    <!-- Sheets Configuration Proxy loaded via JS -->

    <!-- Page Logic Script -->
    <script src="assets/js/zero-student-report.js"></script>

    <!-- Clear session for password prompt on refresh -->
    <script>
        window.addEventListener('load', () => {
            fetch('verify-password?action=clear');
        });

        // Redundant FAB toggle logic removed
    </script>

    <!-- ── Excel Import Preview Modal ── -->
    <div class="zs-modal-overlay" id="pdfPreviewModal">
        <div class="zs-modal-card">
            <div class="zs-modal-header">
                <h3>Verify Parsed Excel Records</h3>
                <button class="zs-modal-close" id="pdfModalClose">&times;</button>
            </div>
            <div class="zs-modal-body">
                <p class="zs-modal-intro">The following records match our faculty team initials. Please review and confirm the import.</p>
                
                <!-- CC Email Input for Batch Import -->
                <div class="form-group" style="margin-bottom: 6px;">
                    <label for="import-cc" style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">CC Email Recipient(s) <span style="font-size: 11px; color: #64748b; text-transform: none; font-weight: normal;">(Optional, for batch email dispatch)</span></label>
                    <div class="search-select-wrap">
                        <input type="text" id="import-cc" placeholder="e.g. sbchauhan@gmiu.edu.in, drchandarana@gmiu.edu.in" autocomplete="off" style="width: 100%; padding: 8px 12px; font-size: 13px; background: rgba(15, 23, 42, 0.7); border: 1px solid var(--zs-border); border-radius: 8px; color: #f8fafc;">
                        <div class="search-dropdown-list" id="importCcDropdownList"></div>
                    </div>
                </div>
                
                <!-- Import Progress Section -->
                <div class="zs-import-progress-container" id="importProgressContainer" style="display: none;">
                    <div class="zs-progress-header">
                        <span id="importProgressText">Importing records: 0 / 0</span>
                        <span id="importProgressPercent">0%</span>
                    </div>
                    <div class="zs-progress-bar-bg">
                        <div class="zs-progress-bar-fill" id="importProgressBarFill" style="width: 0%;"></div>
                    </div>
                </div>

                <!-- Validation Summary Section -->
                <div class="zs-validation-summary-container" id="validationSummaryContainer" style="display: none; margin-top: 15px; padding: 12px; background: rgba(30, 41, 59, 0.5); border-radius: 8px; border: 1px solid #334155; font-size: 13px;">
                    <h4 style="margin: 0 0 8px 0; color: #f8fafc; font-size: 14px; font-weight: 600;">Validation Summary</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; color: #94a3b8; font-family: monospace;">
                        <div>Total rows detected in Excel: <span id="val-total" style="color: #f8fafc; font-weight: bold;">0</span></div>
                        <div>Eligible rows after filtering: <span id="val-eligible" style="color: #f8fafc; font-weight: bold;">0</span></div>
                        <div>Rows imported: <span id="val-imported" style="color: #34d399; font-weight: bold;">0</span></div>
                        <div>Missing rows: <span id="val-missing" style="color: #ef4444; font-weight: bold;">0</span></div>
                        <div>Duplicate rows: <span id="val-duplicate" style="color: #fb923c; font-weight: bold;">0</span></div>
                    </div>
                    <div id="val-status-box" style="margin-top: 12px; padding: 8px; border-radius: 6px; text-align: center; font-weight: bold; font-size: 14px;">
                        <!-- Status text here -->
                    </div>
                    <div id="val-mismatch-details" style="display: none; margin-top: 10px; max-height: 100px; overflow-y: auto; font-family: monospace; font-size: 11px; color: #ef4444; padding: 6px; background: rgba(0,0,0,0.2); border-radius: 4px;">
                        <!-- List of missing/duplicate records -->
                    </div>
                </div>

                <div class="zs-table-container">
                    <table class="zs-modal-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-pdf-rows" checked></th>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Date</th>
                                <th>Class/Lab</th>
                                <th>Subject</th>
                                <th>Faculty</th>
                                <th>Branch</th>
                                <th>Sem</th>
                                <th>Time</th>
                                <th>Students</th>
                                <th>Remarks</th>
                                <th style="text-align: center;">Dept</th>
                                <th style="text-align: center;">TT Match</th>
                            </tr>
                        </thead>
                        <tbody id="pdf-parsed-rows-body">
                            <!-- Rows will be injected here -->
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
</body>

</html>
