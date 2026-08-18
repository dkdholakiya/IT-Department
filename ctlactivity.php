<?php include 'auth-check.php'; ?>
<?php require_once 'auto-cache-bust.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="CTL Activity Dashboard for academic tracking and expert session analytics.">
    <title>CTL Activity Dashboard — CE & IT Department</title>

    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <script src="assets/vendor/xlsx/xlsx.full.min.js"></script>

    <!-- Theme Stylesheets -->
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/ctlactivity.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">
    <style>
        html, body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            height: auto !important;
            width: 100% !important;
        }
    </style>
</head>

<body>

    <div class="ctl-page">

        <!-- Background particles -->
        <div class="particles" aria-hidden="true">
            <div class="particle"
                style="width:4px; height:4px; left:8%;  background:rgba(192,57,43,0.5);  animation: rise 20s linear infinite;">
            </div>
            <div class="particle"
                style="width:3px; height:3px; left:22%; background:rgba(255,255,255,0.2); animation: rise 25s linear -6s infinite;">
            </div>
            <div class="particle"
                style="width:5px; height:5px; left:38%; background:rgba(37,99,235,0.5);  animation: rise 18s linear -3s infinite;">
            </div>
            <div class="particle"
                style="width:3px; height:3px; left:55%; background:rgba(255,255,255,0.15);animation: rise 22s linear -10s infinite;">
            </div>
            <div class="particle"
                style="width:4px; height:4px; left:68%; background:rgba(124,58,237,0.45);animation: rise 28s linear -1s infinite;">
            </div>
            <div class="particle"
                style="width:3px; height:3px; left:80%; background:rgba(255,255,255,0.18);animation: rise 24s linear -8s infinite;">
            </div>
            <div class="particle"
                style="width:5px; height:5px; left:90%; background:rgba(192,57,43,0.4);  animation: rise 19s linear -4s infinite;">
            </div>
        </div>

        <!-- Glowing Orbs -->
        <div class="orb orb-1" aria-hidden="true"></div>
        <div class="orb orb-2" aria-hidden="true"></div>
        <div class="orb orb-3" aria-hidden="true"></div>

        <!-- ── Page Header ── -->
        <header class="rp-header container">
            <div class="rp-header-inner">
                <a href="./" class="back-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Portal
                </a>

                <div class="rp-header-center">
                    <div class="rp-dept-badge">
                        <span class="rp-badge-dot"></span>
                        Department of CE & IT
                    </div>
                    <h1 class="rp-title">CTL Activity Dashboard</h1>
                </div>

                <div class="rp-header-right">
                    <span class="portal-badge">CTL Activity</span>
                    <button type="button" class="direct-erp-btn" id="directErpBtn" title="Direct ERP Links Quick Access">
                        <svg class="erp-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Direct ERP Link</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="container">

            <div id="mainDashboardContent">
                <!-- Top Layout: Upload on left, Faculty Profile on right -->
                <div class="top-layout">
                    <!-- Left: Upload Master Sheet Dropzone -->
                    <div class="top-panel left-panel">
                        <label
                            style="display:block; margin-bottom:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.8px; font-size:12px;">Upload
                            Master Sheet (.xlsx)</label>
                        <div class="upload-area" id="uploadArea">
                            <svg class="upload-icon" width="36" height="36" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2v-4M17 8l-5-5-5 5M12 3v12" />
                            </svg>
                            <div class="upload-text" id="uploadText">Click or Drag &amp; Drop to Upload File</div>
                            <div class="upload-subtext">Excel Master Sheet (.xlsx, .xls)</div>
                        </div>
                        <input type="file" id="excelFile" accept=".xlsx,.xls">
                    </div>

                    <!-- Right: Faculty Info Profile Form -->
                    <div class="top-panel right-panel" style="padding: 0; overflow: visible;">
                        <!-- Form Stepper Header -->
                        <div class="form-stepper" style="padding: 18px 24px;">
                            <div class="step-indicator active">
                                <span class="step-num">1</span>
                                <span class="step-label">Faculty Info</span>
                            </div>
                        </div>

                        <div style="padding: 24px;">
                            <div class="form-group">
                                <label for="facultySearch">Prepared By (Faculty Name) <span class="req">*</span></label>
                                <div class="search-select-wrap">
                                    <input type="text" id="facultySearch"
                                        placeholder="Type to search faculty name (e.g. Prof. Dhaval Chandarana)..."
                                        autocomplete="off" required>
                                    <input type="hidden" id="preparedBy" name="preparedBy" required>
                                    <div class="search-dropdown-list" id="facultyDropdownList"></div>
                                    <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" />
                                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    </svg>
                                </div>
                                <div id="facultyError" class="validation-error hidden">Please select a faculty member from
                                    the dropdown.</div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="facultyEmail">Email Address</label>
                                    <input type="text" id="facultyEmail" placeholder="Auto-filled..." readonly>
                                </div>
                                <div class="form-group">
                                    <label for="facultyPhone">Mobile Number</label>
                                    <input type="text" id="facultyPhone" placeholder="Auto-filled..." readonly>
                                </div>
                            </div>

                            <div class="form-group" style="position: relative; margin-top: 16px;">
                                <label for="ccSearch">CC Emails (Multi-select)</label>
                                <div class="cc-select-wrap">
                                    <div class="cc-tags-container" id="ccTagsContainer"></div>
                                    <input type="text" id="ccSearch" placeholder="Type or click to select CC emails..."
                                        autocomplete="off">
                                    <div class="search-dropdown-list" id="ccDropdownList"></div>
                                </div>
                            </div>

                            <button type="button" class="submit-btn" id="sendBtn"
                                style="width: 100%; margin-top: 24px; gap: 8px;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path d="M22 2L11 13" />
                                    <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                                </svg>
                                <span>Send</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards Grid -->
                <div class="cards">
                    <div class="card">
                        <h2 id="total">0</h2>
                        <p>Total Activities</p>
                    </div>
                    <div class="card submitted-card">
                        <h2 id="submittedCount">0</h2>
                        <p>Submitted</p>
                    </div>
                    <div class="card not-submitted-card">
                        <h2 id="notSubmittedCount">0</h2>
                        <p>Not Submitted</p>
                    </div>
                    <div class="card approved-card">
                        <h2 id="approved">0</h2>
                        <p>Approved</p>
                    </div>
                    <div class="card pending-card">
                        <h2 id="pending">0</h2>
                        <p>Pending</p>
                    </div>
                    <div class="card missing-card">
                        <h2 id="rejected">0</h2>
                        <p>Reject</p>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Approval Status</label>
                        <select id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Reject</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Submission Condition</label>
                        <select id="submissionFilter">
                            <option value="">All Conditions</option>
                            <option value="Submitted">Submitted (Any)</option>
                            <option value="Not Submitted">Not Submitted</option>
                            <option value="On Time">On Time</option>
                            <option value="Delayed">Delayed</option>
                            <option value="Over Grace Period">Over Grace Period</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Search Activity</label>
                        <input type="text" id="search" placeholder="Type activity name...">
                    </div>
                </div>

                <!-- Table List -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-sr">#</th>
                                <th class="col-name">Name</th>
                                <th class="col-plan">Plan Date</th>
                                <th class="col-actual">Actual Date</th>
                                <th class="col-modified">Modified Date</th>
                                <th class="col-marks">Marks</th>
                                <th class="col-flags">Flags</th>
                                <th class="col-sub">Submission Flags</th>
                                <th class="col-status">Approval Status</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <tr>
                                <td colspan="9"
                                    style="text-align: center; color: var(--text-dim); padding: 50px; font-weight: 500; font-family: 'Share Tech', monospace; font-size: 16px; letter-spacing: 0.5px;">
                                    AWAITING EXCEL MASTER SHEET UPLOAD...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- /#mainDashboardContent -->

            <!-- Full Page Centered Server Error Container -->
            <div id="fullServerErrorContainer" style="display: none; width: 100%; min-height: 60vh; align-items: center; justify-content: center; text-align: center; padding: 40px 15px;">
                <div style="background: rgba(192, 57, 43, 0.04); border: 1px solid rgba(192, 57, 43, 0.25); border-radius: 20px; padding: 60px 30px; max-width: 680px; width: 100%; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                    <div style="width: 80px; height: 80px; margin: 0 auto 24px auto; background: rgba(192, 57, 43, 0.12); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1.5px solid rgba(192, 57, 43, 0.4); box-shadow: 0 0 25px rgba(192, 57, 43, 0.2);">
                        <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #ef4444;">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 style="color: #fca5a5; font-family: 'Share Tech', monospace; font-size: 28px; font-weight: 800; letter-spacing: 1.5px; margin: 0 0 14px 0; text-transform: uppercase;">500 Internal Server Error</h2>
                    <p style="color: var(--text-muted); font-size: 15px; font-family: 'Merriweather Sans', sans-serif; margin: 0 0 32px 0; line-height: 1.6; max-width: 520px; margin-left: auto; margin-right: auto;">
                        Failed to process uploaded Excel sheet. Server encountered an unexpected internal error.
                    </p>
                    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
                        <button type="button" onclick="location.reload();" style="display: inline-flex; align-items: center; gap: 8px; padding: 13px 28px; background: linear-gradient(135deg, #c0392b 0%, #962d22 100%); color: #ffffff; border: none; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 6px 20px rgba(192, 57, 43, 0.4); font-family: 'Merriweather Sans', sans-serif;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M23 4v6h-6M1 20v-6h6"/>
                                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                            </svg>
                            <span>Try Again</span>
                        </button>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <?php 
        $footer_class = 'rp-footer text-center';
        include 'footer.php'; 
        ?>

    </div><!-- /ctl-page -->

    <!-- ── Direct ERP Link Popup Modal ── -->
    <div class="erp-modal-overlay" id="erpModalOverlay" aria-hidden="true" role="dialog" aria-labelledby="erpModalTitle">
        <div class="erp-modal-container">
            <!-- Modal Header -->
            <div class="erp-modal-header">
                <div class="erp-header-title-wrap">
                    <div class="erp-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
                        <span>QUICK ACCESS</span>
                    </div>
                    <h2 class="erp-modal-title" id="erpModalTitle">Direct ERP LINK</h2>
                </div>
                <button type="button" class="erp-modal-close" id="erpModalClose" aria-label="Close modal">&times;</button>
            </div>

            <!-- Modal Search & Filters Bar -->
            <div class="erp-modal-controls">
                <div class="erp-search-box">
                    <svg class="erp-search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="erpSearchInput" placeholder="Search by faculty name, semester, or class..." autocomplete="off">
                    <button type="button" class="erp-search-clear hidden" id="erpSearchClear">&times;</button>
                </div>
                <div class="erp-filter-pills" id="erpFilterPills">
                    <button type="button" class="erp-pill active" data-filter="all">All</button>
                    <button type="button" class="erp-pill" data-filter="ce">CE Dept</button>
                    <button type="button" class="erp-pill" data-filter="it">IT Dept</button>
                    <button type="button" class="erp-pill" data-filter="sem1">Sem 1</button>
                    <button type="button" class="erp-pill" data-filter="sem3">Sem 3</button>
                    <button type="button" class="erp-pill" data-filter="sem5">Sem 5</button>
                    <button type="button" class="erp-pill" data-filter="sem7">Sem 7</button>
                </div>
            </div>

            <!-- Modal Cards Body Grid -->
            <div class="erp-modal-body">
                <div class="erp-cards-grid" id="erpCardsGrid">
                    <!-- Dynamically populated via JS -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="erp-modal-footer">
                <div class="erp-footer-info">
                    <span id="erpItemCount">0 links available</span>
                </div>
                <button type="button" class="erp-close-btn" id="erpCloseBtn">Close</button>
            </div>
        </div>
    </div>

    <?php 
    $active_page = 'ctlactivity';
    include 'fab-nav.php'; 
    ?>

    <!-- Script Logic -->
    <script>
        const ctlExcelServerError = <?php echo !empty($config['ctl_excel_server_error']) ? 'true' : 'false'; ?>;
    </script>
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>
    <script>
        // Clear the session on load so that refresh triggers password re-prompt
        window.addEventListener('load', () => {
            fetch('verify-password?action=clear');
        });

        // ── Direct ERP Link Modal Popup Logic ──
        const directErpData = (typeof facultyData !== 'undefined' ? facultyData : [])
            .filter(member => member.link && member.link.trim() !== '')
            .map(member => ({
                name: member.name,
                semClass: member.semClass || '',
                link: member.link,
                dept: member.dept || (member.department && member.department.toLowerCase().includes('computer') ? 'CE' : 'IT'),
                sem: member.sem || ''
            }));

        let activeErpFilter = "all";
        let erpSearchQuery = "";

        function initDirectErpModal() {
            const btn = document.getElementById("directErpBtn");
            const overlay = document.getElementById("erpModalOverlay");
            const closeBtn = document.getElementById("erpModalClose");
            const footerClose = document.getElementById("erpCloseBtn");
            const searchInput = document.getElementById("erpSearchInput");
            const searchClear = document.getElementById("erpSearchClear");
            const filterPills = document.querySelectorAll("#erpFilterPills .erp-pill");

            if (!btn || !overlay) return;

            function openModal() {
                overlay.classList.add("active");
                document.body.style.overflow = "hidden";
                if (searchInput) searchInput.focus();
                renderErpCards();
            }

            function closeModal() {
                overlay.classList.remove("active");
                document.body.style.overflow = "";
            }

            btn.addEventListener("click", openModal);
            if (closeBtn) closeBtn.addEventListener("click", closeModal);
            if (footerClose) footerClose.addEventListener("click", closeModal);

            overlay.addEventListener("click", function(e) {
                if (e.target === overlay) closeModal();
            });

            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape" && overlay.classList.contains("active")) {
                    closeModal();
                }
            });

            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    erpSearchQuery = this.value.trim().toLowerCase();
                    if (searchClear) {
                        searchClear.classList.toggle("hidden", erpSearchQuery.length === 0);
                    }
                    renderErpCards();
                });
            }

            if (searchClear) {
                searchClear.addEventListener("click", function() {
                    searchInput.value = "";
                    erpSearchQuery = "";
                    searchClear.classList.add("hidden");
                    searchInput.focus();
                    renderErpCards();
                });
            }

            filterPills.forEach(pill => {
                pill.addEventListener("click", function() {
                    filterPills.forEach(p => p.classList.remove("active"));
                    this.classList.add("active");
                    activeErpFilter = this.getAttribute("data-filter");
                    renderErpCards();
                });
            });
        }

        function renderErpCards() {
            const grid = document.getElementById("erpCardsGrid");
            const itemCount = document.getElementById("erpItemCount");
            if (!grid) return;

            const filtered = directErpData.filter(item => {
                let matchesFilter = true;
                if (activeErpFilter === "ce") matchesFilter = item.dept.toUpperCase() === "CE";
                else if (activeErpFilter === "it") matchesFilter = item.dept.toUpperCase() === "IT";
                else if (activeErpFilter.startsWith("sem")) matchesFilter = item.sem.toLowerCase() === activeErpFilter.toLowerCase();

                let matchesSearch = true;
                if (erpSearchQuery) {
                    matchesSearch = item.name.toLowerCase().includes(erpSearchQuery) ||
                                    item.semClass.toLowerCase().includes(erpSearchQuery) ||
                                    item.dept.toLowerCase().includes(erpSearchQuery);
                }

                return matchesFilter && matchesSearch;
            });

            if (itemCount) {
                itemCount.textContent = `${filtered.length} link${filtered.length === 1 ? '' : 's'} available`;
            }

            if (filtered.length === 0) {
                grid.innerHTML = `
                    <div class="erp-no-data">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <p>No ERP links found matching your criteria.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = filtered.map(item => `
                <div class="erp-card">
                    <div class="erp-card-name">${item.name}</div>
                    <div class="erp-card-sem">${item.semClass}</div>
                    <a href="${item.link}" target="_blank" rel="noopener noreferrer" class="erp-card-link-btn">
                        <span>Button Link</span>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                </div>
            `).join('');
        }

        document.addEventListener("DOMContentLoaded", initDirectErpModal);

        const getAvatarClass = (member) => {
            const legacyClasses = ["av-dc", "av-sw", "av-eu", "av-tv"];
            if (member.avatarClass && legacyClasses.includes(member.avatarClass)) {
                return member.avatarClass;
            }
            const colors = [
                'av-theme-red', 'av-theme-blue', 'av-theme-purple', 
                'av-theme-teal', 'av-theme-green', 'av-theme-orange', 
                'av-theme-indigo', 'av-theme-cyan', 'av-theme-pink'
            ];
            let hash = 0;
            const name = member.name || "";
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        };

        let allData = [];
        let activeData = [];

        // ── Faculty Search Dropdown Logic ──
        let selectedFaculty = null;

        function initFacultySearch() {
            const facultySearch = document.getElementById("facultySearch");
            const preparedBy = document.getElementById("preparedBy");
            const facultyDropdownList = document.getElementById("facultyDropdownList");
            const facultyError = document.getElementById("facultyError");

            const facultyEmail = document.getElementById("facultyEmail");
            const facultyPhone = document.getElementById("facultyPhone");

            // Initialize dropdown list
            renderDropdown(facultyData);

            facultySearch.addEventListener("focus", function () {
                facultyDropdownList.classList.add("show");
                filterFaculty();
            });

            facultySearch.addEventListener("input", function () {
                facultyDropdownList.classList.add("show");
                filterFaculty();

                if (this.value.trim() === "") {
                    clearSelection();
                }
            });

            document.addEventListener("click", function (e) {
                if (!e.target.closest(".search-select-wrap")) {
                    facultyDropdownList.classList.remove("show");
                    validateSearchInput();
                }
            });

            function filterFaculty() {
                const query = facultySearch.value.toLowerCase().replace("prof.", "").replace("mr.", "").replace("dr.", "").trim();
                const filtered = facultyData.filter(member =>
                    member.name.toLowerCase().includes(query) ||
                    member.initials.toLowerCase().includes(query) ||
                    member.email.toLowerCase().includes(query) ||
                    member.designation.toLowerCase().includes(query) ||
                    member.empId.toLowerCase().includes(query)
                );
                renderDropdown(filtered);
            }

            function renderDropdown(list) {
                facultyDropdownList.innerHTML = "";
                if (list.length === 0) {
                    facultyDropdownList.innerHTML = `<div class="no-results-item">No faculty members found</div>`;
                    return;
                }

                list.forEach(member => {
                    const item = document.createElement("div");
                    item.className = "dropdown-item";
                    item.innerHTML = `
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.name} <span style="opacity: 0.75; font-weight: normal; font-size: 0.9em;">(${member.initials})</span></div>
                            <div class="item-desg">${member.email} &nbsp;·&nbsp; ${member.designation} &nbsp;·&nbsp; ${member.department || "Information Technology"}</div>
                        </div>
                    `;
                    item.addEventListener("click", function () {
                        selectFaculty(member);
                    });
                    facultyDropdownList.appendChild(item);
                });
            }

            function selectFaculty(member) {
                facultySearch.value = member.name;
                preparedBy.value = member.name;
                selectedFaculty = member;

                facultyEmail.value = member.email;
                facultyPhone.value = "+91 " + member.phone;

                facultySearch.classList.remove("input-error");
                facultyError.classList.add("hidden");

                facultyDropdownList.classList.remove("show");

                // Dynamically update document title based on selected faculty department
                const isCe = (member.initials === "DRC" || member.name.includes("Dhaval Chandarana")) 
                    ? (localStorage.getItem("portal_dept") === "CE") 
                    : (member.department === "Computer Engineering");
                document.title = isCe ? "CTL Activity — CE Department" : "CTL Activity — IT Department";
            }

            function clearSelection() {
                preparedBy.value = "";
                selectedFaculty = null;
                facultyEmail.value = "";
                facultyPhone.value = "";
            }

            function validateSearchInput() {
                const val = facultySearch.value.trim();
                if (val === "") {
                    clearSelection();
                    return;
                }

                const valLower = val.toLowerCase();
                const match = facultyData.find(m => 
                    m.name.toLowerCase() === valLower || 
                    m.initials.toLowerCase() === valLower || 
                    m.email.toLowerCase() === valLower
                );
                if (match) {
                    selectFaculty(match);
                } else {
                    const partialMatches = facultyData.filter(m => 
                        m.name.toLowerCase().includes(valLower) || 
                        m.initials.toLowerCase().includes(valLower) || 
                        m.email.toLowerCase().includes(valLower)
                    );
                    if (partialMatches.length === 1) {
                        selectFaculty(partialMatches[0]);
                    } else {
                        clearSelection();
                        facultySearch.value = "";
                    }
                }
            }
        }

        // Initialize Faculty Search Search logic
        initFacultySearch();

        // ── CC Emails Multi-Select Dropdown Logic ──
        const defaultCCEmails = [
            "drchandarana@gmiu.edu.in", // HOD (Prof. Dhaval Chandarana)
            "sbchauhan@gmiu.edu.in",    // Incharge HOD IT (Prof. Shwetaba Chauhan)
            "ehunagar@gmiu.edu.in",     // Incharge HOD CE (Prof. Ekta Unagar)
            "tmvyas@gmiu.edu.in",       // Sub Incharge HOD IT (Prof. Tarjanee Vyas)
            "phkaneijya@gmiu.edu.in"    // Sub Incharge HOD CE (Prof. Pragnesh Kanejiya)
        ];
        let selectedCCEmails = [...defaultCCEmails];

        function getCompactDesignation(desg) {
            if (!desg) return "";
            const lower = desg.toLowerCase();
            if (lower === "hod" || lower.includes("both")) return "HOD";
            if (lower.includes("incharge hod it")) return "HOD IT";
            if (lower.includes("incharge hod ce")) return "HOD CE";
            if (lower.includes("sub incharge hod it")) return "Sub HOD IT";
            if (lower.includes("sub incharge hod ce")) return "Sub HOD CE";
            if (lower.includes("assistant professor")) return "Asst. Prof";
            if (lower.includes("associate professor")) return "Assoc. Prof";
            if (lower.includes("teaching assistant")) return "TA";
            if (lower.includes("lecturer")) return "Lecturer";
            return desg;
        }

        function updateCCTagsUI() {
            const ccTagsContainer = document.getElementById("ccTagsContainer");
            if (!ccTagsContainer) return;

            ccTagsContainer.innerHTML = "";
            selectedCCEmails.forEach(email => {
                const member = (typeof facultyData !== "undefined") ? facultyData.find(m => m.email.toLowerCase() === email.toLowerCase()) : null;
                let displayName = email;
                let tooltipText = email;
                if (member) {
                    const shortDesg = getCompactDesignation(member.designation);
                    displayName = shortDesg ? `${member.name} (${shortDesg})` : member.name;
                    tooltipText = `${member.name} — ${member.designation || ''} (${email})`;
                }

                const tag = document.createElement("div");
                tag.className = "cc-tag";
                tag.title = tooltipText;
                tag.innerHTML = `
                    <span>${displayName}</span>
                    <button type="button" class="cc-tag-remove" title="Remove">&times;</button>
                `;
                tag.querySelector(".cc-tag-remove").addEventListener("click", function (e) {
                    e.stopPropagation();
                    removeCCTag(email);
                });
                ccTagsContainer.appendChild(tag);
            });
        }

        function removeCCTag(email) {
            selectedCCEmails = selectedCCEmails.filter(e => e !== email);
            updateCCTagsUI();
            const ccSearch = document.getElementById("ccSearch");
            if (ccSearch) {
                ccSearch.focus();
                if (typeof filterCCEmails === "function") filterCCEmails();
            }
        }

        function resetCCEmailsToDefault() {
            selectedCCEmails = [...defaultCCEmails];
            updateCCTagsUI();
        }

        let filterCCEmails = null;

        function initCCEmailsSearch() {
            const ccSearch = document.getElementById("ccSearch");
            const ccDropdownList = document.getElementById("ccDropdownList");
            const ccTagsContainer = document.getElementById("ccTagsContainer");
            const ccSelectWrap = ccSearch.closest(".cc-select-wrap");

            // Focus search input when clicking the wrapper container
            ccSelectWrap.addEventListener("click", function (e) {
                if (e.target === ccSelectWrap || e.target === ccTagsContainer) {
                    ccSearch.focus();
                }
            });

            // Set initial default CC tags UI
            updateCCTagsUI();

            // Populate all emails initially
            renderCCDropdown(facultyData);

            ccSearch.addEventListener("focus", function () {
                ccDropdownList.classList.add("show");
                filterCCEmails();
            });

            ccSearch.addEventListener("input", function () {
                ccDropdownList.classList.add("show");
                filterCCEmails();
            });

            // Backspace to remove last tag if input is empty
            ccSearch.addEventListener("keydown", function (e) {
                if (e.key === "Backspace" && ccSearch.value === "" && selectedCCEmails.length > 0) {
                    removeCCTag(selectedCCEmails[selectedCCEmails.length - 1]);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function (e) {
                if (!e.target.closest(".cc-select-wrap")) {
                    ccDropdownList.classList.remove("show");
                    ccSearch.value = "";
                }
            });

            filterCCEmails = function() {
                const query = ccSearch.value.toLowerCase().replace("prof.", "").replace("mr.", "").replace("dr.", "").trim();
                // Filter out already selected emails
                const available = facultyData.filter(member => !selectedCCEmails.includes(member.email));

                // Match by email, name, or initials
                const filtered = available.filter(member =>
                    member.email.toLowerCase().includes(query) ||
                    member.name.toLowerCase().includes(query) ||
                    member.initials.toLowerCase().includes(query)
                );

                renderCCDropdown(filtered);
            };

            function renderCCDropdown(list) {
                ccDropdownList.innerHTML = "";
                if (list.length === 0) {
                    ccDropdownList.innerHTML = `<div class="no-results-item">No matching emails found</div>`;
                    return;
                }

                list.forEach(member => {
                    const item = document.createElement("div");
                    item.className = "dropdown-item";
                    item.innerHTML = `
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.email}</div>
                            <div class="item-desg">${member.name} <span style="opacity: 0.75;">(${member.initials})</span> &nbsp;·&nbsp; ${member.department || "Information Technology"}</div>
                        </div>
                    `;
                    item.addEventListener("click", function (e) {
                        e.stopPropagation();
                        addCCTag(member.email);
                    });
                    ccDropdownList.appendChild(item);
                });
            }

            function addCCTag(email) {
                if (!selectedCCEmails.includes(email)) {
                    selectedCCEmails.push(email);
                    updateCCTagsUI();
                }
                ccSearch.value = "";
                ccSearch.focus();
                filterCCEmails();
            }
        }

        // Initialize CC Emails Search logic
        initCCEmailsSearch();

        // ── Send Button Submit Action ──
        const sendBtn = document.getElementById("sendBtn");
        if (sendBtn) {
            sendBtn.addEventListener("click", function () {
                const preparedBy = document.getElementById("preparedBy").value;
                if (!preparedBy) {
                    const facultySearch = document.getElementById("facultySearch");
                    const facultyError = document.getElementById("facultyError");
                    facultySearch.classList.add("input-error");
                    facultyError.classList.remove("hidden");
                    facultySearch.focus();
                    return;
                }

                sendBtn.style.pointerEvents = "none";
                sendBtn.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Sending...</span>
                `;

                const isCe = (selectedFaculty && (selectedFaculty.initials === "DRC" || selectedFaculty.name.includes("Dhaval Chandarana")) ? (localStorage.getItem("portal_dept") === "CE") : (selectedFaculty && selectedFaculty.department === "Computer Engineering"));

                // Helpers to generate inline-styled pills for emails
                function getApprovalPillHtmlForEmail(text) {
                    let cleanVal = (text || '').replace(/^[•\s]+/, '').trim();
                    const lower = cleanVal.toLowerCase();
                    let bg = '#fef3c7';
                    let fg = '#92400e';
                    let label = 'Pending';

                    if (lower.includes('approved')) {
                        bg = '#d1fae5'; fg = '#065f46'; label = 'Approved';
                    } else if (lower.includes('rejected') || lower.includes('reject')) {
                        bg = '#fee2e2'; fg = '#991b1b'; label = 'Reject';
                    }

                    return `<span style="display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 9999px; background-color: ${bg}; color: ${fg}; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">${label}</span>`;
                }

                function getSubmissionPillsHtmlForEmail(text) {
                    const parts = extractSubmissionTokens(text);

                    if (!parts || parts.length === 0) {
                        return `<span style="display: inline-block; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 9999px; background-color: #f1f5f9; color: #475569; font-family: 'Playfair Display', Georgia, serif;">-</span>`;
                    }

                    return `<div style="text-align: center; font-family: 'Playfair Display', Georgia, serif;">` + parts.map(part => {
                        const lower = part.toLowerCase();
                        let bg = '#f1f5f9';
                        let fg = '#475569';

                        if (lower.includes('not submitted') || lower.includes('not submited')) {
                            bg = '#fee2e2'; fg = '#991b1b';
                        } else if (lower.includes('over grace period')) {
                            bg = '#fca5a5'; fg = '#7f1d1d';
                        } else if (lower.includes('past planning date') || lower.includes('delayed')) {
                            bg = '#ffedd5'; fg = '#9a3412';
                        } else if (lower.includes('on time') || lower.includes('submitted') || lower.includes('submited')) {
                            bg = '#dbeafe'; fg = '#1e40af';
                        }

                        return `<div style="margin: 4px 0;"><span style="display: inline-block; padding: 4px 8px; font-size: 9px; font-weight: 700; border-radius: 9999px; background-color: ${bg}; color: ${fg}; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap; font-family: 'Playfair Display', Georgia, serif;">${part}</span></div>`;
                    }).join('') + `</div>`;
                }

                // Compute overall metrics dynamically from allData (overall stats, unaffected by filters)
                const activeTotal = allData.length;
                const activeSubmitted = allData.filter(x => {
                    const sub = (x.submissionFlags || '').toLowerCase();
                    return (sub.includes('submitted') || sub.includes('submited') || sub.includes('on time') || sub.includes('delayed')) && !sub.includes('not submitted') && !sub.includes('not submited');
                }).length;
                const activeNotSubmitted = allData.filter(x => {
                    const sub = (x.submissionFlags || '').toLowerCase();
                    return sub.includes('not submitted') || sub.includes('not submited');
                }).length;
                const activeApproved = allData.filter(x => (x.approvalStatus || '').toLowerCase().includes('approved')).length;
                const activePending = allData.filter(x => {
                    const app = (x.approvalStatus || '').toLowerCase();
                    return app.includes('pending') || (!app && !app.includes('approved') && !app.includes('reject') && !app.includes('rejected'));
                }).length;
                const activeRejected = allData.filter(x => (x.approvalStatus || '').toLowerCase().includes('rejected') || (x.approvalStatus || '').toLowerCase().includes('reject')).length;

                // Build HTML table for the email report (using activeData)
                let tableRowsHtml = "";
                if (activeData.length > 0) {
                    activeData.forEach((item, index) => {
                        const bg = index % 2 === 0 ? '#ffffff' : '#f8fafc';
                        tableRowsHtml += `
                            <tr style="background-color: ${bg};">
                                <td style="padding: 14px 16px; text-align: center; font-weight: 500; color: #64748b; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${index + 1}</td>
                                <td style="padding: 14px 16px; font-weight: 600; color: #0f172a; word-break: break-word; word-wrap: break-word; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.name}</td>
                                <td style="padding: 14px 16px; text-align: center; color: #334155; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.planDate || '-'}</td>
                                <td style="padding: 14px 16px; text-align: center; color: #334155; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${item.actualDate || '-'}</td>
                                <td style="padding: 14px 16px; text-align: center; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${getSubmissionPillsHtmlForEmail(item.submissionFlags)}</td>
                                <td style="padding: 14px 16px; text-align: center; border: 1px solid #cbd5e1; vertical-align: middle; background-color: ${bg}; font-family: 'Playfair Display', Georgia, serif;">${getApprovalPillHtmlForEmail(item.approvalStatus)}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableRowsHtml = '<tr><td colspan="6" style="text-align: center; padding: 32px; color: #64748b; font-weight: 500; border: 1px solid #cbd5e1; vertical-align: middle; font-family: \'Playfair Display\', Georgia, serif;">No matching activities found.</td></tr>';
                }

                const emailHtml = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link rel="preconnect" href="https://fonts.googleapis.com">
                        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                        <!--[if !mso]><!-->
                        <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap" rel="stylesheet">
                        <!--<![endif]-->
                        <style>
                            @import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap');
                            
                            body, table, td, th, div, span, p, a, h1, h2 {
                                font-family: 'Playfair Display', Georgia, serif !important;
                            }
                            
                            /* Responsive Styles */
                            @media only screen and (max-width: 600px) {
                                .email-container {
                                    padding: 12px 6px !important;
                                }
                                .card-wrapper {
                                    border-radius: 12px !important;
                                }
                                .header-banner {
                                    padding: 24px 16px !important;
                                }
                                .header-banner h1 {
                                    font-size: 20px !important;
                                }
                                .faculty-card {
                                    margin: 16px 12px 0 12px !important;
                                    padding: 12px !important;
                                }
                                .faculty-table, .faculty-table tr {
                                    display: block !important;
                                    width: 100% !important;
                                }
                                .faculty-table td.faculty-avatar-cell {
                                    display: inline-block !important;
                                    width: auto !important;
                                    vertical-align: middle !important;
                                }
                                .faculty-table td.faculty-info-cell {
                                    display: inline-block !important;
                                    width: auto !important;
                                    padding-left: 10px !important;
                                    vertical-align: middle !important;
                                }
                                .faculty-table td.faculty-date-cell {
                                    display: block !important;
                                    width: 100% !important;
                                    margin-top: 12px !important;
                                    text-align: left !important;
                                    padding-left: 0 !important;
                                    border-top: 1px dashed #cbd5e1 !important;
                                    padding-top: 12px !important;
                                }
                                .section-container {
                                    margin: 20px 12px 0 12px !important;
                                }
                                .section-container-details {
                                    margin: 20px 12px 20px 12px !important;
                                }
                                .metrics-table {
                                    border-spacing: 0 !important;
                                    margin: 0 !important;
                                }
                                .metrics-table tr, .metrics-table td {
                                    display: block !important;
                                    width: 100% !important;
                                }
                                .metrics-card {
                                    width: auto !important;
                                    margin-bottom: 12px !important;
                                    box-sizing: border-box !important;
                                }
                                .table-responsive {
                                    display: block !important;
                                    width: 100% !important;
                                    overflow-x: auto !important;
                                    -webkit-overflow-scrolling: touch !important;
                                }
                                .footer-container {
                                    padding: 16px 12px !important;
                                }
                            }
                        </style>
                    </head>
                    <body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Playfair Display', Georgia, serif; color: #334155; -webkit-font-smoothing: antialiased;">
                        <div class="email-container" style="background-color: #f8fafc; padding: 40px 20px;">
                            <div class="card-wrapper" style="max-width: 1050px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);">
                                
                                <!-- Header Banner -->
                                <div class="header-banner" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 24px; position: relative; text-align: center;">
                                    <h1 style="margin: 0; font-family: 'Playfair Display', Georgia, serif; font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; text-align: center;">CTL Activity Report Dashboard</h1>
                                </div>

                                <!-- Faculty Card -->
                                <div class="faculty-card" style="margin: 32px 24px 0 24px; padding: 20px; background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                                    <table class="faculty-table" style="width: 100%; border-collapse: collapse; font-family: 'Playfair Display', Georgia, serif;">
                                        <tr>
                                            <td class="faculty-avatar-cell" style="vertical-align: middle; width: 48px;">
                                                <div style="width: 40px; height: 40px; border-radius: 20px; background-color: #0f172a; color: #e5c185; text-align: center; line-height: 40px; font-weight: bold; font-size: 16px; font-family: 'Playfair Display', Georgia, serif;">
                                                    ${(selectedFaculty ? selectedFaculty.initials : preparedBy.charAt(0)).toUpperCase()}
                                                </div>
                                            </td>
                                            <td class="faculty-info-cell" style="vertical-align: middle; padding-left: 12px; font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Prepared By </div>
                                                <div style="font-size: 15px; font-weight: 600; color: #0f172a; margin-top: 2px; font-family: 'Playfair Display', Georgia, serif;">${selectedFaculty ? selectedFaculty.name : preparedBy}</div>
                                            </td>
                                            <td class="faculty-date-cell" style="vertical-align: middle; text-align: right; font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Date</div>
                                                <div style="font-size: 14px; font-weight: 600; color: #0f172a; margin-top: 2px; font-family: 'Playfair Display', Georgia, serif;">${new Date().toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Section: Summary Metrics -->
                                <div class="section-container" style="margin: 32px 24px 0 24px;">
                                    <h2 style="margin: 0 0 16px 0; font-family: 'Playfair Display', Georgia, serif; font-size: 20px; font-weight: 700; color: #0f172a; border-left: 4px solid #c29a5b; padding-left: 10px; line-height: 1.2;">Summary Metrics</h2>
                                    
                                    <table class="metrics-table" style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin: 0 -8px; font-family: 'Playfair Display', Georgia, serif;">
                                        <tr>
                                            <!-- Card 1: Total -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #64748b; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Total</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 4px;">${activeTotal}</div>
                                            </td>
                                            <!-- Card 2: Submitted -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 9px; color: #3b82f6; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Submitted</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #1e40af; margin-top: 4px;">${activeSubmitted}</div>
                                            </td>
                                            <!-- Card 3: Not Submitted -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #ef4444; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 9px; color: #ef4444; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Not Submitted</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #991b1b; margin-top: 4px;">${activeNotSubmitted}</div>
                                            </td>
                                            <!-- Card 4: Approved -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #10b981; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 8px; color: #10b981; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Approved</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #065f46; margin-top: 4px;">${activeApproved}</div>
                                            </td>
                                            <!-- Card 5: Pending -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #fbbf24; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 8px; color: #fbbf24; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Pending</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #92400e; margin-top: 4px;">${activePending}</div>
                                            </td>
                                            <!-- Card 6: Rejected -->
                                            <td class="metrics-card" style="width: 16.66%; background-color: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #991b1b; border-radius: 8px; padding: 12px 6px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.02); font-family: 'Playfair Display', Georgia, serif;">
                                                <div style="font-size: 8px; color: #991b1b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif;">Reject</div>
                                                <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #991b1b; margin-top: 4px;">${activeRejected}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Section: Activity Details -->
                                <div class="section-container-details" style="margin: 36px 24px 40px 24px;">
                                    <h2 style="margin: 0 0 16px 0; font-family: 'Playfair Display', Georgia, serif; font-size: 20px; font-weight: 700; color: #0f172a; border-left: 4px solid #c29a5b; padding-left: 10px; line-height: 1.2;">Activity Details</h2>
                                    
                                    <div class="table-responsive" style="border: 1px solid #cbd5e1; border-radius: 12px; overflow-x: auto; -webkit-overflow-scrolling: touch; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                                        <table style="width: 100%; min-width: 950px; border-collapse: collapse; text-align: left; font-size: 13px; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1;">
                                            <thead>
                                                <tr style="background-color: #f8fafc;">
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 5%; text-align: center; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">#</th>
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 35%; text-align: left; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">Activity Name</th>
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 12%; text-align: center; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">Plan Date</th>
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 12%; text-align: center; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">Actual Date</th>
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 22%; text-align: center; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">Submission Flags</th>
                                                    <th style="padding: 14px 16px; font-weight: 700; color: #334155; width: 14%; text-align: center; font-family: 'Playfair Display', Georgia, serif; border: 1px solid #cbd5e1; background-color: #f1f5f9;">Approval Status</th>
                                                </tr>
                                            </thead>
                                            <tbody style="color: #334155; font-family: 'Playfair Display', Georgia, serif;">
                                                ${tableRowsHtml}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', Georgia, serif;">
                                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', Georgia, serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="https://engineering.gt.tc/" target="_blank" style="color: ${isCe ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600;">${isCe ? 'CE DEPARTMENT' : 'IT DEPARTMENT'}</a>.</p>
                                    <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', Georgia, serif;">&copy; 2026 ALL RIGHTS RESERVED.</p>
                                    <p style="margin: 6px 0 0 0; font-size: 11px; color: #64748b; font-family: 'Playfair Display', Georgia, serif;"><a href="https://engineering.gt.tc/" target="_blank" style="color: ${isCe ? '#2563eb' : '#8c1d1d'}; text-decoration: underline; font-weight: 600; font-family: 'Playfair Display', Georgia, serif;">https://engineering.gt.tc/</a></p>
                                </div>

                            </div>
                        </div>
                    </body>
                    </html>
                `;

                const recipientEmail = document.getElementById("facultyEmail").value || (localStorage.getItem("portal_dept") === "CE" ? "admincecse@gmiu.edu.in" : "adminit@gmiu.edu.in");

                let emailSubject = "CTL Activity Report In Website ERP Upload Documents";

                // Trigger POST request to the local PHP email service
                fetch('send-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: recipientEmail,
                        cc: selectedCCEmails,
                        subject: emailSubject,
                        html: emailHtml,
                        dept: isCe ? "CE" : "IT"
                    })
                })
                    .then(response => {
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw new Error(data.error || `HTTP error ${response.status}`);
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            sendBtn.innerHTML = `
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <span>Sent Successfully!</span>
                        `;
                            sendBtn.style.background = "linear-gradient(135deg, #10b981 0%, #059669 100%)";
                            sendBtn.style.boxShadow = "0 6px 28px rgba(16, 185, 129, 0.4)";
                        } else {
                            throw new Error(data.error || 'Server reported error');
                        }
                    })
                    .catch(err => {
                        console.error('Email send failed:', err);
                        alert("Email service is offline or returned an error! \n\nDetails: " + err.message);

                        sendBtn.innerHTML = `
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        <span>Send Failed</span>
                    `;
                        sendBtn.style.background = "linear-gradient(135deg, #ef4444 0%, #991b1b 100%)";
                        sendBtn.style.boxShadow = "0 6px 28px rgba(239, 68, 68, 0.4)";
                    })
                    .finally(() => {
                        setTimeout(() => {
                            sendBtn.style.pointerEvents = "auto";
                            sendBtn.style.background = "";
                            sendBtn.style.boxShadow = "";
                            sendBtn.innerHTML = `
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/>
                            </svg>
                            <span>Send</span>
                        `;
                        }, 4000);
                    });
            });
        }

        // Upload dropzone triggers original input field
        const uploadArea = document.getElementById('uploadArea');
        const excelFile = document.getElementById('excelFile');
        const uploadText = document.getElementById('uploadText');

        uploadArea.addEventListener('click', () => {
            excelFile.click();
        });

        // Drag and drop event listeners
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = "var(--red-bright)";
            uploadArea.style.background = "rgba(192, 57, 43, 0.08)";
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = "rgba(255, 255, 255, 0.15)";
            uploadArea.style.background = "rgba(6, 10, 28, 0.4)";
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = "rgba(255, 255, 255, 0.15)";
            uploadArea.style.background = "rgba(6, 10, 28, 0.4)";

            if (e.dataTransfer.files.length > 0) {
                excelFile.files = e.dataTransfer.files;
                handleFileLoad(excelFile.files[0]);
            }
        });

        excelFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileLoad(e.target.files[0]);
            }
        });

        document.getElementById('statusFilter').addEventListener('change', filterData);
        document.getElementById('submissionFilter').addEventListener('change', filterData);
        document.getElementById('search').addEventListener('keyup', filterData);

        // Token extraction helper for Submission Flags (handles bullets, commas, and concatenated space strings like "Submitted Delayed Over Grace Period 30 Days")
        function extractSubmissionTokens(text) {
            if (!text) return [];
            let str = String(text).trim();
            if (!str) return [];

            let parts = str.split(/[•,\n\r|;]+/).map(p => p.replace(/^[•\s]+/, '').trim()).filter(p => p.length > 0);

            let result = [];
            parts.forEach(part => {
                const lower = part.toLowerCase();
                if (
                    (lower.includes('submitted') || lower.includes('submited') || lower.includes('grace period') || lower.includes('past planning')) &&
                    !part.includes('•') && !part.includes(',') && !part.includes(';')
                ) {
                    const tokenRegex = /(not submited|not submitted|submitted|submited|on time|over grace period|past planning date \d+ days?|past planning date|\d+ days?|delayed)/gi;
                    const matches = part.match(tokenRegex);
                    if (matches && matches.length > 0) {
                        let seen = new Set();
                        matches.forEach(m => {
                            let cleanM = m.trim();
                            let key = cleanM.toLowerCase().replace('submited', 'submitted');
                            if (!seen.has(key)) {
                                seen.add(key);
                                if (cleanM.toLowerCase() === 'not submited') cleanM = 'Not Submitted';
                                if (cleanM.toLowerCase() === 'submited') cleanM = 'Submitted';
                                result.push(cleanM);
                            }
                        });
                        return;
                    }
                }
                result.push(part);
            });

            return result;
        }

        // Cleaner function to clean newlines and trim text
        function cleanText(str) {
            if (str === undefined || str === null || str === "") return "";
            return String(str).replace(/\n/g, ', ').trim();
        }

        // Helper function to format Excel date values properly (handles JS Dates, Excel Serial Numbers, and clean strings)
        function formatExcelDate(cell) {
            if (cell === undefined || cell === null || cell === "") return "";

            if (cell instanceof Date) {
                // SheetJS parses date values as UTC by default. Using UTC getters avoids timezone shift.
                const d = String(cell.getUTCDate()).padStart(2, '0');
                const m = String(cell.getUTCMonth() + 1).padStart(2, '0');
                const y = cell.getUTCFullYear();
                return `${d}-${m}-${y}`;
            }

            if (typeof cell === 'number' && cell > 30000 && cell < 60000) {
                // Convert Excel serial number to date
                const date = new Date(Math.round((cell - 25569) * 86400) * 1000);
                const userTimezoneOffset = date.getTimezoneOffset() * 60000;
                const adjustedDate = new Date(date.getTime() + userTimezoneOffset);
                const d = String(adjustedDate.getDate()).padStart(2, '0');
                const m = String(adjustedDate.getMonth() + 1).padStart(2, '0');
                const y = adjustedDate.getFullYear();
                return `${d}-${m}-${y}`;
            }

            return cleanText(cell);
        }

        function handleFileLoad(file) {
            if (!file) return;

            const mainContent = document.getElementById('mainDashboardContent');
            const fullError = document.getElementById('fullServerErrorContainer');

            if (typeof ctlExcelServerError !== 'undefined' && ctlExcelServerError) {
                // Hide main dashboard content (top layout, faculty info, cards, filters, table)
                if (mainContent) mainContent.style.display = 'none';
                if (fullError) fullError.style.display = 'flex';
                return;
            }

            // Normal state: ensure main dashboard is visible and full error container is hidden
            if (mainContent) mainContent.style.display = 'block';
            if (fullError) fullError.style.display = 'none';

            // Reset dropzone styling to normal if previously in error state
            uploadArea.style.borderColor = "";
            uploadArea.style.background = "";

            // Show filename visual update
            uploadText.innerText = "Loaded: " + file.name;
            uploadText.style.color = "var(--green)";

            const reader = new FileReader();

            reader.onload = function (evt) {
                const data = new Uint8Array(evt.target.result);
                const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                const sheet = workbook.Sheets[workbook.SheetNames[0]];

                const rows = XLSX.utils.sheet_to_json(sheet, {
                    header: 1,
                    defval: ""
                });

                allData = [];

                let headerRowIndex = 1; // Default fallback index
                let colIndices = {
                    srNo: 0,
                    name: 1,
                    planDate: 2,
                    actualDate: 3,
                    modifiedDate: 4,
                    marks: 5,
                    flags: 6,
                    submissionFlags: 7,
                    approvalStatus: 8
                };

                // Scan rows to find the header row
                for (let i = 0; i < Math.min(10, rows.length); i++) {
                    const row = rows[i];
                    if (!row || row.length === 0) continue;

                    let hasName = false;
                    let hasSub = false;
                    row.forEach(cell => {
                        const str = String(cell).trim().toLowerCase();
                        if (str === 'name') hasName = true;
                        if (str.includes('submission flag') || str.includes('submission flags') || str.includes('submission')) hasSub = true;
                    });

                    if (hasName && hasSub) {
                        headerRowIndex = i;
                        // Reset indices to -1 so we map strictly what is found in the matched header row
                        colIndices = {
                            srNo: -1,
                            name: -1,
                            planDate: -1,
                            actualDate: -1,
                            modifiedDate: -1,
                            marks: -1,
                            flags: -1,
                            submissionFlags: -1,
                            approvalStatus: -1
                        };
                        // Map indices dynamically
                        row.forEach((cell, idx) => {
                            const str = String(cell).trim().toLowerCase();
                            if (str === '#') colIndices.srNo = idx;
                            else if (str === 'name') colIndices.name = idx;
                            else if (str === 'plan date') colIndices.planDate = idx;
                            else if (str === 'actual date') colIndices.actualDate = idx;
                            else if (str === 'modified date') colIndices.modifiedDate = idx;
                            else if (str === 'marks') colIndices.marks = idx;
                            else if (str === 'flags') colIndices.flags = idx;
                            else if (str.includes('submission flag') || str.includes('submission flags') || str.includes('submission')) colIndices.submissionFlags = idx;
                            else if (str.includes('approval status') || str === 'approval' || str.includes('approval')) colIndices.approvalStatus = idx;
                        });
                        break;
                    }
                }

                // Parse data rows starting after headerRowIndex
                let autoSrNo = 1;
                for (let i = headerRowIndex + 1; i < rows.length; i++) {
                    const row = rows[i];

                    // Skip empty rows or rows missing Name
                    if (!row || colIndices.name === -1 || !row[colIndices.name]) continue;

                    let rawSr = colIndices.srNo !== -1 && row[colIndices.srNo] !== undefined ? cleanText(row[colIndices.srNo]) : "";
                    let finalSr = rawSr ? rawSr : autoSrNo;
                    autoSrNo++;

                    allData.push({
                        srNo: finalSr,
                        name: cleanText(row[colIndices.name]),
                        planDate: colIndices.planDate !== -1 && row[colIndices.planDate] !== undefined ? formatExcelDate(row[colIndices.planDate]) : "",
                        actualDate: colIndices.actualDate !== -1 && row[colIndices.actualDate] !== undefined ? formatExcelDate(row[colIndices.actualDate]) : "",
                        modifiedDate: colIndices.modifiedDate !== -1 && row[colIndices.modifiedDate] !== undefined ? formatExcelDate(row[colIndices.modifiedDate]) : "",
                        marks: colIndices.marks !== -1 && row[colIndices.marks] !== undefined ? cleanText(row[colIndices.marks]) : "",
                        flags: colIndices.flags !== -1 && row[colIndices.flags] !== undefined ? cleanText(row[colIndices.flags]) : "",
                        submissionFlags: colIndices.submissionFlags !== -1 && row[colIndices.submissionFlags] !== undefined ? cleanText(row[colIndices.submissionFlags]) : "",
                        approvalStatus: colIndices.approvalStatus !== -1 && row[colIndices.approvalStatus] !== undefined ? cleanText(row[colIndices.approvalStatus]) : ""
                    });
                }

                // Dynamically populate submissionFilter dropdown with any unique tags found in Column H
                const subFilterSelect = document.getElementById('submissionFilter');
                if (subFilterSelect) {
                    const defaultOptions = ['Submitted', 'Not Submitted', 'On Time', 'Delayed', 'Over Grace Period'];
                    let foundTags = new Set();
                    allData.forEach(item => {
                        if (item.submissionFlags) {
                            const parts = item.submissionFlags.split(/[•,\n\r|;]+/).map(p => p.replace(/^[•\s]+/, '').trim()).filter(p => p.length > 0);
                            parts.forEach(p => foundTags.add(p));
                        }
                    });

                    let optionsHtml = `<option value="">All Conditions</option>
                        <option value="Submitted">Submitted (Any)</option>
                        <option value="Not Submitted">Not Submitted</option>
                        <option value="On Time">On Time</option>
                        <option value="Delayed">Delayed</option>
                        <option value="Over Grace Period">Over Grace Period</option>`;

                    foundTags.forEach(tag => {
                        const exists = defaultOptions.some(opt => opt.toLowerCase() === tag.toLowerCase());
                        if (!exists) {
                            optionsHtml += `<option value="${tag}">${tag}</option>`;
                        }
                    });
                    subFilterSelect.innerHTML = optionsHtml;
                }

                updateCards(allData);
                renderTable(allData);
                activeData = [...allData];
            };

            reader.readAsArrayBuffer(file);
        }

        function updateCards(data) {
            document.getElementById('total').innerText = data.length;

            const submittedCount = data.filter(x => {
                const sub = (x.submissionFlags || '').toLowerCase();
                return (sub.includes('submitted') || sub.includes('submited') || sub.includes('on time') || sub.includes('delayed')) && !sub.includes('not submitted') && !sub.includes('not submited');
            }).length;

            const notSubmittedCount = data.filter(x => {
                const sub = (x.submissionFlags || '').toLowerCase();
                return sub.includes('not submitted') || sub.includes('not submited');
            }).length;

            const approvedCount = data.filter(x => (x.approvalStatus || '').toLowerCase().includes('approved')).length;
            const pendingCount = data.filter(x => {
                const app = (x.approvalStatus || '').toLowerCase();
                return app.includes('pending') || (!app && !app.includes('approved') && !app.includes('reject') && !app.includes('rejected'));
            }).length;
            const rejectedCount = data.filter(x => (x.approvalStatus || '').toLowerCase().includes('rejected') || (x.approvalStatus || '').toLowerCase().includes('reject')).length;

            if (document.getElementById('submittedCount')) document.getElementById('submittedCount').innerText = submittedCount;
            if (document.getElementById('notSubmittedCount')) document.getElementById('notSubmittedCount').innerText = notSubmittedCount;
            if (document.getElementById('approved')) document.getElementById('approved').innerText = approvedCount;
            if (document.getElementById('pending')) document.getElementById('pending').innerText = pendingCount;
            if (document.getElementById('rejected')) document.getElementById('rejected').innerText = rejectedCount;
        }

        // Helper to apply beautiful CSS pills based on keywords in Submission Flags (Column H)
        function getSubmissionPills(text) {
            const parts = extractSubmissionTokens(text);

            if (!parts || parts.length === 0) {
                return `<span class="status-pill pill-default">-</span>`;
            }

            return `<div class="sub-flags-container">` + parts.map(part => {
                const lower = part.toLowerCase();
                let className = 'pill-default';

                if (lower.includes('not submitted') || lower.includes('not submited')) {
                    className = 'pill-missing';
                } else if (lower.includes('over grace period')) {
                    className = 'pill-rejected';
                } else if (lower.includes('past planning date') || lower.includes('delayed')) {
                    className = 'pill-delayed';
                } else if (lower.includes('on time') || lower.includes('submitted') || lower.includes('submited')) {
                    className = 'pill-submitted';
                }

                return `<span class="status-pill ${className}">${part}</span>`;
            }).join('') + `</div>`;
        }

        function getApprovalPill(text) {
            let cleanVal = (text || '').replace(/^[•\s]+/, '').trim();
            const lower = cleanVal.toLowerCase();
            
            if (lower.includes('approved')) return `<span class="status-pill pill-approved">Approved</span>`;
            if (lower.includes('rejected') || lower.includes('reject')) return `<span class="status-pill pill-rejected">Reject</span>`;
            
            return `<span class="status-pill pill-pending">Pending</span>`;
        }

        function renderTable(data) {
            const tbody = document.getElementById('tbody');
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px; font-weight: 500;">No matching activities found.</td></tr>`;
                return;
            }

            data.forEach(item => {
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td class="col-sr" style="text-align: center;">${item.srNo}</td>
                    <td class="col-name" style="font-weight: 500;">${item.name}</td>
                    <td class="col-plan">${item.planDate}</td>
                    <td class="col-actual">${item.actualDate}</td>
                    <td class="col-modified">${item.modifiedDate || 'N/A'}</td>
                    <td class="col-marks" style="text-align: center;">${item.marks}</td>
                    <td class="col-flags">${item.flags}</td>
                    <td class="col-sub">${getSubmissionPills(item.submissionFlags)}</td>
                    <td class="col-status">${getApprovalPill(item.approvalStatus)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function filterData() {
            const status = document.getElementById('statusFilter').value.toLowerCase().trim();
            const submission = document.getElementById('submissionFilter').value.toLowerCase().trim();
            const search = document.getElementById('search').value.toLowerCase().trim();

            const filtered = allData.filter(item => {
                const itemApproval = (item.approvalStatus || '').toLowerCase();
                const itemSubRaw = (item.submissionFlags || '').toLowerCase();
                const itemAct = (item.name || '').toLowerCase();
                const tokens = extractSubmissionTokens(item.submissionFlags).map(t => t.toLowerCase());

                const isNotSubmitted = itemSubRaw.includes('not submitted') || itemSubRaw.includes('not submited') || tokens.some(t => t.includes('not submitted') || t.includes('not submited'));

                // Status Match Logic (Approval Status)
                let statusMatch = true;
                if (status) {
                    if (status.includes('reject')) {
                        statusMatch = itemApproval.includes('reject') || itemApproval.includes('rejected');
                    } else if (status.includes('approved')) {
                        statusMatch = itemApproval.includes('approved');
                    } else if (status.includes('pending')) {
                        statusMatch = itemApproval.includes('pending') || (!itemApproval.includes('approved') && !(itemApproval.includes('reject') || itemApproval.includes('rejected')));
                    } else {
                        statusMatch = itemApproval.includes(status);
                    }
                }

                // Submission Match Logic (Submission Condition Column H)
                let submissionMatch = true;
                if (submission) {
                    if (submission === 'not submitted' || submission === 'not submited') {
                        submissionMatch = isNotSubmitted;
                    } else if (submission === 'submitted' || submission === 'submited') {
                        submissionMatch = !isNotSubmitted && (itemSubRaw.includes('submitted') || itemSubRaw.includes('submited') || itemSubRaw.includes('on time') || itemSubRaw.includes('delayed') || tokens.some(t => t.includes('submitted') || t.includes('on time') || t.includes('delayed')));
                    } else if (submission === 'on time') {
                        submissionMatch = itemSubRaw.includes('on time') || itemSubRaw.includes('ontime') || itemSubRaw.includes('on-time') || tokens.some(t => t.includes('on time') || t.includes('ontime'));
                    } else {
                        submissionMatch = itemSubRaw.includes(submission) || tokens.some(t => t.includes(submission) || submission.includes(t));
                    }
                }

                const searchMatch = !search || itemAct.includes(search);

                return statusMatch && submissionMatch && searchMatch;
            });

            renderTable(filtered);
            activeData = filtered;
        }

        // Redundant FAB toggle logic removed
    </script>

</body>

</html>
