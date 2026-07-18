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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CDN CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme and Faculty CSS -->
    <link rel="stylesheet" href="assets/css/portal.css">
    <link rel="stylesheet" href="assets/css/faculty.css">
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

                <span class="portal-badge" id="portalBadge">IT Faculty</span>
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
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Load Shared Faculty Data -->
    <script src="assets/js/facultyData.js"></script>

    <!-- Rendering Logic Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const grid = document.getElementById("facultyGrid");
            const modalContainer = document.getElementById("modalContainer");

            let currentFilterDept = "all";
            let currentSearchQuery = "";

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

                facultyData.forEach(member => {
                    const dept = member.department || "Information Technology";
                    if (filterDept !== "all" && dept !== filterDept && dept !== "Both") {
                        return; // skip if it doesn't match the active filter
                    }

                    // Check search query against name and initials
                    if (query !== "") {
                        const nameMatches = (member.name || "").toLowerCase().includes(query);
                        const initialsMatches = (member.initials || "").toLowerCase().includes(query);
                        if (!nameMatches && !initialsMatches) {
                            return; // skip if it doesn't match name or initials
                        }
                    }

                    const avatarClass = getAvatarClass(member);

                    // Generate Card HTML
                    cardsHtml += `
                    <div class="faculty-card">
                        <div class="avatar-wrapper">
                            <div class="avatar-glow"></div>
                            <div class="avatar-image-placeholder ${avatarClass}">${member.initials}</div>
                        </div>
                        <h3 class="faculty-name">${member.name}</h3>
                        <div class="faculty-desg">${member.designation}</div>
                        <div class="faculty-dept">${member.department || "Information Technology"}</div>
                        <p class="faculty-focus">Employee ID: ${member.empId}<br>Contact: ${member.email}</p>
                        <button type="button" class="details-btn" data-bs-toggle="modal" data-bs-target="#modal-${member.id}">
                            <span>View More Details</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>`;

                    // Generate Modal HTML
                    const phoneDigits = member.phone.replace(/\s+/g, '');
                    modalsHtml += `
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
                                    
                                    <div class="modal-fac-cabin mb-3">
                                        <span>🆔</span>
                                        <span>Employee ID: ${member.empId}</span>
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

            // Default to all departments on initial page load
            const allBtn = document.getElementById("filter-all-btn");
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
