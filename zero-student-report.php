<?php include 'auth-check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU IT Department — Zero Student Report Management for classes where zero students reported as per timetable.">
    <title>Zero Student Log — GMIU IT Department</title>

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

    <!-- PDF.js CDN Library for client-side PDF reading -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    </script>

    <!-- Scoped Page Stylesheet -->
    <link rel="stylesheet" href="assets/css/zero-student-report.css">
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
                <a href="index" class="back-btn" id="backBtn">
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
                    <p class="rp-subtitle">Gyanmanjari Innovative University &nbsp;·&nbsp; Academic Timetable Logs</p>
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
                        <span>Import PDF</span>
                    </button>
                    <input type="file" id="pdf-import-input" accept=".pdf" style="display: none;">
                </div>

                <div class="zs-form-body">
                    <div class="form-group">
                        <label>Department / Branch Select</label>
                        <div class="zs-segment-control">
                            <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology">Information Technology</button>
                            <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="entry-date">Session Date <span class="req">*</span></label>
                        <input type="text" id="entry-date" required>
                    </div>

                    <div class="form-row-2">
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
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="entry-faculty">Faculty Initials <span class="req">*</span></label>
                            <div class="search-select-wrap">
                                <input type="text" id="entry-faculty" placeholder="e.g. SDG" autocomplete="off" required>
                                <div class="search-dropdown-list" id="facultyDropdownList"></div>
                            </div>
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
                    </div>

                    <div class="form-group">
                        <label for="entry-branch">Branch/Class <span class="req">*</span></label>
                        <div class="search-select-wrap">
                            <input type="text" id="entry-branch" placeholder="e.g. CLASS C B.TECH(IT)(ICT)" autocomplete="off" required>
                            <div class="search-dropdown-list" id="branchDropdownList"></div>
                        </div>
                    </div>

                    <div class="form-row-2 form-row-2-preserve">
                        <div class="form-group">
                            <label for="entry-timein">Time In <span class="req">*</span></label>
                            <input type="text" id="entry-timein" required>
                        </div>
                        <div class="form-group">
                            <label for="entry-timeout">Time Out <span class="req">*</span></label>
                            <input type="text" id="entry-timeout" required>
                        </div>
                    </div>

                    <div class="form-row-2 form-row-2-preserve">
                        <div class="form-group">
                            <label for="entry-remarks">Remarks</label>
                            <input type="text" id="entry-remarks" value="NO STUDENT">
                        </div>
                        <div class="form-group">
                            <label for="entry-students">No. of Students</label>
                            <input type="text" id="entry-students" value="---" placeholder="e.g. --- or 10 or 0">
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
        <footer class="zs-footer">
            <p>&copy; 2026 Department of Information Technology, GMIU &nbsp;·&nbsp; Designed with <span style="color:#f87171;">♥</span> by Dev Dholakiya</p>
        </footer>

    </div>

    <!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
    <div class="fab-nav" id="fabNav">
        <div class="fab-menu" id="fabMenu">
            <a href="index" class="fab-link" id="nav-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Home
            </a>
            <a href="faculty" class="fab-link" id="nav-faculty">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Faculty Team
            </a>
            <a href="report" class="fab-link" id="nav-report">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Report Request
            </a>
            <a href="ctlactivity" class="fab-link" id="nav-ctl">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                    <line x1="15" y1="3" x2="15" y2="21" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                </svg>
                CTL Activity
            </a>
            <a href="ctldrive" class="fab-link" id="nav-drive">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Drive Scanner
            </a>
            <a href="zero-student-report" class="fab-link active" id="nav-zero">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="17" y1="8" x2="23" y2="14" />
                    <line x1="23" y1="8" x2="17" y2="14" />
                </svg>
                Zero Student Report
            </a>
        </div>

        <button class="fab-btn" id="fabBtn" aria-label="Open Navigation">
            <svg class="fab-icon-menu" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <svg class="fab-icon-close" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24" style="display:none;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Faculty Member Data source -->
    <script src="assets/js/facultyData.js"></script>

    <!-- Sheets Configuration Proxy loaded via JS -->

    <!-- Page Logic Script -->
    <script src="assets/js/zero-student-report.js"></script>

    <!-- Clear session for password prompt on refresh -->
    <script>
        window.addEventListener('load', () => {
            fetch('verify-password?action=clear');
        });

        // ── FAB Menu Toggle ──
        const fabBtn = document.getElementById('fabBtn');
        const fabMenu = document.getElementById('fabMenu');
        const iconMenu = fabBtn.querySelector('.fab-icon-menu');
        const iconClose = fabBtn.querySelector('.fab-icon-close');

        fabBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = fabMenu.classList.toggle('open');
            fabBtn.classList.toggle('active', isOpen);
            iconMenu.style.display = isOpen ? 'none' : 'block';
            iconClose.style.display = isOpen ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('fabNav').contains(e.target)) {
                fabMenu.classList.remove('open');
                fabBtn.classList.remove('active');
                iconMenu.style.display = 'block';
                iconClose.style.display = 'none';
            }
        });
    </script>

    <!-- ── PDF Import Preview Modal ── -->
    <div class="zs-modal-overlay" id="pdfPreviewModal">
        <div class="zs-modal-card">
            <div class="zs-modal-header">
                <h3>Verify Parsed PDF Records</h3>
                <button class="zs-modal-close" id="pdfModalClose">&times;</button>
            </div>
            <div class="zs-modal-body">
                <p class="zs-modal-intro">The following records match our faculty team initials. Please review and confirm the import.</p>
                
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

                <div class="zs-table-container">
                    <table class="zs-modal-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-pdf-rows" checked></th>
                                <th>Date</th>
                                <th>Class/Lab</th>
                                <th>Subject</th>
                                <th>Faculty</th>
                                <th>Branch</th>
                                <th>Sem</th>
                                <th>Time</th>
                                <th>Students</th>
                                <th>Remarks</th>
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
