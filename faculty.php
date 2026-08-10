<?php require_once 'auto-cache-bust.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Faculty Directory — Meet our academic mentors, researchers, and creators shaping the future of IT.">
    <title>Faculty Directory — CE & IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link class="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts & Preconnect Optimization -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&family=Kameron:wght@400..700&family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,700..900;1,700..900&family=Share+Tech&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CDN CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo v_asset('assets/css/portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/faculty.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">
</head>

<body>

    <div class="fac-page">

        <!-- ░░ Particles ░░ -->
        <div class="particles" aria-hidden="true">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
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
        <div class="orb orb-3" aria-hidden="true"></div>

        <!-- ── Page Header (matches report.php structure) ── -->
        <header class="rp-header">
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
                        <span id="rpDeptBadgeText">Department of Information Technology</span>
                    </div>
                    <h1 class="rp-title">Faculty Directory</h1>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="export-excel-btn" id="exportExcelBtn" onclick="exportFacultyExcel()" title="Export Faculty Directory to Excel">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Export Excel</span>
                    </button>
                    <span class="portal-badge" id="portalBadge">IT Faculty</span>
                </div>
            </div>
        </header>

        <!-- ── Faculty Filter ── -->
        <div class="fac-filter-container">
            <div class="zs-segment-control">
                <button type="button" class="segment-btn active" id="filter-all-btn" data-dept="all">All Departments</button>
                <button type="button" class="segment-btn" id="filter-it-btn" data-dept="Information Technology">Information Technology</button>
                <button type="button" class="segment-btn" id="filter-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
            </div>
        </div>

        <!-- ── Faculty Search ── -->
        <div class="fac-search-container">
            <div class="search-input-wrap" id="searchWrapper">
                <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="faculty-search-input" placeholder="Search by name or initials" autocomplete="off">
            </div>
        </div>

        <!-- ── Faculty Grid Container ── -->
        <main class="faculty-grid" id="facultyGrid">
            <!-- Dynamic cards rendered by JS -->
        </main>

        <!-- Footer -->
        <?php 
        $footer_class = 'rp-footer text-center';
        include 'footer.php'; 
        ?>

    </div><!-- /fac-page -->

    <!-- ░░ MODAL POPUPS CONTAINER (Bootstrap 5) ░░ -->
    <div id="modalContainer">
        <!-- Dynamic modals rendered by JS -->
    </div>

    <?php 
    $active_page = 'faculty';
    include 'fab-nav.php'; 
    ?>

    <!-- Bootstrap 5 CDN JS Bundle -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>

    <!-- Load Shared Faculty Data -->
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>

    <!-- Rendering & Export Logic Script -->
    <script>
        let exportFacultyExcel = null;

        document.addEventListener("DOMContentLoaded", function () {
            const grid = document.getElementById("facultyGrid");
            const modalContainer = document.getElementById("modalContainer");

            let currentFilterDept = "all";
            let currentSearchQuery = "";

            // Helper to check if a faculty member's department matches the filter (supports Both, IT & CE, etc.)
            const isDeptMatch = (memberDept, filterDept) => {
                if (filterDept === "all") return true;
                if (!memberDept) return true;
                const d = memberDept.toLowerCase().trim();
                if (d === "both" || d.includes("both") || d.includes("it & ce") || d.includes("ce & it") || d.includes("it/ce") || d.includes("ce/it")) {
                    return true;
                }
                if (filterDept === "Information Technology") {
                    return d === "information technology" || d.includes("information technology") || d === "it";
                }
                if (filterDept === "Computer Engineering") {
                    return d === "computer engineering" || d.includes("computer engineering") || d === "ce";
                }
                return d === filterDept.toLowerCase();
            };

            // Calculate faculty counts dynamically
            const allCount = facultyData.length;
            const itCount = facultyData.filter(m => isDeptMatch(m.department, "Information Technology")).length;
            const ceCount = facultyData.filter(m => isDeptMatch(m.department, "Computer Engineering")).length;

            // Update button texts to show counts with styled badge
            const allBtn = document.getElementById("filter-all-btn");
            const itBtn = document.getElementById("filter-it-btn");
            const ceBtn = document.getElementById("filter-ce-btn");

            if (allBtn) allBtn.innerHTML = `All Departments <span class="dept-count">${allCount}</span>`;
            if (itBtn) itBtn.innerHTML = `Information Technology <span class="dept-count">${itCount}</span>`;
            if (ceBtn) ceBtn.innerHTML = `Computer Engineering <span class="dept-count">${ceCount}</span>`;

            // Render function with optional department and search filtering
            function renderFaculty(filterDept = "all", searchQuery = "") {
                let cardsHtml = "";
                let modalsHtml = "";
                const query = searchQuery.toLowerCase().trim();

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

                const generateCardHtml = (member, avatarClass) => {
                    return `
                    <div class="faculty-card">
                        <div class="avatar-wrapper">
                            <div class="avatar-glow"></div>
                            <div class="avatar-image-placeholder ${avatarClass}">${member.initials}</div>
                        </div>
                        <h3 class="faculty-name">${member.name}</h3>
                        <div class="faculty-desg">${member.designation}</div>
                        <div class="faculty-dept">${member.department || "Information Technology"}</div>
                        <p class="faculty-focus">Employee ID: ${member.empId}${member.setting ? ` | Setting: ${member.setting}` : ''}<br>Contact: ${member.email}</p>
                        <button type="button" class="details-btn" data-bs-toggle="modal" data-bs-target="#modal-${member.id}">
                            <span>View More Details</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>`;
                };

                const generateModalHtml = (member, avatarClass) => {
                    const phoneDigits = (member.phone || '').replace(/\s+/g, '');
                    return `
                    <div class="modal fade gmiu-modal" id="modal-${member.id}" tabindex="-1" aria-labelledby="modal-${member.id}-label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-${member.id}-label">Faculty Profile Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <div class="modal-avatar-wrapper mx-auto mb-3">
                                        <div class="modal-avatar-gradient ${avatarClass}">${member.initials}</div>
                                    </div>
                                    <h4 class="modal-fac-name">${member.name}</h4>
                                    <p class="modal-fac-desg">${member.designation}</p>
                                    <p class="modal-fac-dept">${member.department || "Information Technology"}</p>
                                    
                                    <div class="modal-fac-cabin mb-3 d-flex justify-content-center align-items-center gap-3 flex-wrap">
                                        <span>🆔 Employee ID: ${member.empId}</span>
                                        ${member.setting ? `<span>📍 Setting: <strong>${member.setting}</strong></span>` : ''}
                                    </div>
                                    
                                    <div class="modal-fac-contact d-flex flex-column gap-2">
                                        <a href="mailto:${member.email}" class="modal-contact-link" title="Click to email">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                <polyline points="22,6 12,13 2,6"/>
                                            </svg>
                                            ${member.email}
                                        </a>
                                        <a href="tel:${phoneDigits}" class="modal-contact-link" title="Click to call">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                            </svg>
                                            +91 ${member.phone}
                                        </a>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="modal-action-close" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                };

                // Fixed Top HOD IDs in exact order: [1st: Dhaval Chandarana (HOD), 2nd: Shwetaba Chauhan, 3rd: Ekta Unagar]
                const fixedHodIds = ["dc", "sw", "eu"];

                // 1. Separate fixed top HOD members that match active filter
                let topHodMembers = [];
                fixedHodIds.forEach(id => {
                    const member = facultyData.find(m => m.id === id);
                    if (member) {
                        const deptMatch = isDeptMatch(member.department, filterDept);
                        let searchMatch = true;
                        if (query !== "") {
                            const nameMatches = (member.name || "").toLowerCase().includes(query);
                            const initialsMatches = (member.initials || "").toLowerCase().includes(query);
                            const settingMatches = (member.setting || "").toLowerCase().includes(query);
                            searchMatch = nameMatches || initialsMatches || settingMatches;
                        }
                        if (deptMatch && searchMatch) {
                            topHodMembers.push(member);
                        }
                    }
                });

                // 2. Filter remaining faculty members (excluding fixed HODs)
                let remainingMembers = facultyData.filter(member => {
                    if (fixedHodIds.includes(member.id)) return false;

                    if (!isDeptMatch(member.department, filterDept)) {
                        return false;
                    }
                    if (query !== "") {
                        const nameMatches = (member.name || "").toLowerCase().includes(query);
                        const initialsMatches = (member.initials || "").toLowerCase().includes(query);
                        const settingMatches = (member.setting || "").toLowerCase().includes(query);
                        if (!nameMatches && !initialsMatches && !settingMatches) {
                            return false;
                        }
                    }
                    return true;
                });

                // 3. Shuffle remaining members using Fisher-Yates algorithm
                for (let i = remainingMembers.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [remainingMembers[i], remainingMembers[j]] = [remainingMembers[j], remainingMembers[i]];
                }

                // 4. Render HTML: Top HOD Row (Row 1: 3 cards) + Shuffled Grid (Row 2+: 4 cards per row)
                if (topHodMembers.length > 0) {
                    let topRowCardsHtml = "";
                    topHodMembers.forEach(member => {
                        const avatarClass = getAvatarClass(member);
                        topRowCardsHtml += generateCardHtml(member, avatarClass);
                        modalsHtml += generateModalHtml(member, avatarClass);
                    });
                    cardsHtml += `<div class="hod-top-row hod-cols-${topHodMembers.length}">${topRowCardsHtml}</div>`;
                }

                remainingMembers.forEach(member => {
                    const avatarClass = getAvatarClass(member);
                    cardsHtml += generateCardHtml(member, avatarClass);
                    modalsHtml += generateModalHtml(member, avatarClass);
                });

                grid.innerHTML = cardsHtml || `<div class="col-12 text-center text-muted py-5" style="grid-column: 1 / -1; font-size: 15px; font-family: 'Share Tech', monospace; color: var(--text-dim) !important;">No faculty members found in this department.</div>`;
                modalContainer.innerHTML = modalsHtml;
            }

            // Filter button event listeners
            const filterBtns = document.querySelectorAll(".fac-filter-container .segment-btn");
            const rpDeptBadgeText = document.getElementById("rpDeptBadgeText");
            const portalBadge = document.getElementById("portalBadge");

            filterBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    filterBtns.forEach(b => b.classList.remove("active"));
                    this.classList.add("active");
                    currentFilterDept = this.getAttribute("data-dept");
                    
                    // Toggle theme class and update header texts dynamically
                    if (currentFilterDept === "Computer Engineering") {
                        document.body.classList.add("ce-active");
                        document.body.classList.remove("common-active");
                        if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Computer Engineering";
                        if (portalBadge) portalBadge.textContent = "CE Faculty";
                        localStorage.setItem('portal_dept', 'CE');
                        document.title = "Faculty Directory — CE Department";
                    } else if (currentFilterDept === "Information Technology") {
                        document.body.classList.remove("ce-active");
                        document.body.classList.remove("common-active");
                        if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Information Technology";
                        if (portalBadge) portalBadge.textContent = "IT Faculty";
                        localStorage.setItem('portal_dept', 'IT');
                        document.title = "Faculty Directory — IT Department";
                    } else {
                        // "all"
                        document.body.classList.remove("ce-active");
                        document.body.classList.add("common-active");
                        if (rpDeptBadgeText) rpDeptBadgeText.textContent = "IT & CE Departments";
                        if (portalBadge) portalBadge.textContent = "All Faculty";
                        // Note: Don't overwrite local storage for 'all' tab click to preserve base page default
                        document.title = "Faculty Directory — CE & IT Department";
                    }
                    
                    renderFaculty(currentFilterDept, currentSearchQuery);
                });
            });

            // Search input keyup/input event listener
            const searchInput = document.getElementById("faculty-search-input");
            const searchWrapper = document.getElementById("searchWrapper");
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    currentSearchQuery = this.value;
                    renderFaculty(currentFilterDept, currentSearchQuery);
                });

                searchInput.addEventListener("focus", () => {
                    if (searchWrapper) searchWrapper.classList.add("focused");
                });

                searchInput.addEventListener("blur", () => {
                    if (searchWrapper) searchWrapper.classList.remove("focused");
                });
            }

            // Export Filtered Faculty Data to Excel (Lazy-loads SheetJS library for super fast initial page load)
            exportFacultyExcel = function() {
                const runExport = () => {
                    const query = currentSearchQuery.toLowerCase().trim();
                    const listToExport = facultyData.filter(member => {
                        if (!isDeptMatch(member.department, currentFilterDept)) {
                            return false;
                        }
                        if (query !== "") {
                            const nameMatches = (member.name || "").toLowerCase().includes(query);
                            const initialsMatches = (member.initials || "").toLowerCase().includes(query);
                            const settingMatches = (member.setting || "").toLowerCase().includes(query);
                            if (!nameMatches && !initialsMatches && !settingMatches) return false;
                        }
                        return true;
                    });

                    if (!listToExport || listToExport.length === 0) {
                        alert("No faculty records found for the current selection.");
                        return;
                    }

                    // Format row data for Excel export
                    const excelRows = listToExport.map((member, index) => ({
                        "Sr. No.": index + 1,
                        "Faculty Name": member.name || "",
                        "Designation": member.designation || "",
                        "Department": member.department || "Information Technology",
                        "Employee ID": member.empId ? member.empId.replace('#', '') : "",
                        "Email Address": member.email || "",
                        "Mobile Number": member.phone ? `+91 ${member.phone}` : "",
                        "Setting": member.setting || ""
                    }));

                    // Build worksheet and set column widths
                    const worksheet = XLSX.utils.json_to_sheet(excelRows);
                    worksheet['!cols'] = [
                        { wch: 8 },   // Sr. No.
                        { wch: 28 },  // Faculty Name
                        { wch: 24 },  // Designation
                        { wch: 26 },  // Department
                        { wch: 14 },  // Employee ID
                        { wch: 32 },  // Email Address
                        { wch: 18 },  // Mobile Number
                        { wch: 12 }   // Setting
                    ];

                    // Build workbook
                    const workbook = XLSX.utils.book_new();
                    let sheetLabel = "All Faculty";
                    if (currentFilterDept === "Information Technology") sheetLabel = "IT Faculty";
                    if (currentFilterDept === "Computer Engineering") sheetLabel = "CE Faculty";

                    XLSX.utils.book_append_sheet(workbook, worksheet, sheetLabel);

                    // Generate filename
                    const dateStr = new Date().toISOString().split('T')[0];
                    const fileName = `Faculty_Directory_${sheetLabel.replace(/\s+/g, '_')}_${dateStr}.xlsx`;

                    // Export file
                    XLSX.writeFile(workbook, fileName);
                };

                if (typeof XLSX === "undefined") {
                    const script = document.createElement("script");
                    script.src = "assets/vendor/xlsx/xlsx.full.min.js";
                    script.onload = runExport;
                    document.body.appendChild(script);
                } else {
                    runExport();
                }
            };

            // Default to all departments on initial page load
            if (allBtn) {
                allBtn.click();
            } else {
                renderFaculty("all", "");
            }

            // Redundant FAB toggle logic removed
        });
    </script>
</body>

</html>
