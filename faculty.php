<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU IT Department Faculty Directory — Meet our academic mentors, researchers, and creators shaping the future of IT.">
    <title>Faculty Directory — GMIU IT Department</title>
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
                <a href="index.php" class="back-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Portal
                </a>

                <div class="rp-header-center">
                    <div class="rp-dept-badge">
                        <span class="rp-badge-dot"></span>
                        Department of Information Technology
                    </div>
                    <h1 class="rp-title">Faculty Directory</h1>
                    <p class="rp-subtitle">Gyanmanjari Innovative University &nbsp;·&nbsp; Academic Mentors</p>
                </div>

                <span class="portal-badge">IT Faculty</span>
            </div>
        </header>

        <!-- ── Faculty Grid Container ── -->
        <main class="faculty-grid" id="facultyGrid">
            <!-- Dynamic cards rendered by JS -->
        </main>

        <!-- Footer -->
        <footer class="rp-footer text-center">
            <p>&copy; 2026 Department of Information Technology, GMIU &nbsp;·&nbsp; Designed with <span
                    style="color:#f87171;">♥</span> by Dev Dholakiya</p>
        </footer>

    </div><!-- /fac-page -->

    <!-- ░░ MODAL POPUPS CONTAINER (Bootstrap 5) ░░ -->
    <div id="modalContainer">
        <!-- Dynamic modals rendered by JS -->
    </div>

    <!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
    <div class="fab-nav" id="fabNav">
        <div class="fab-menu" id="fabMenu">
            <a href="index.php" class="fab-link" id="nav-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Home
            </a>
            <a href="faculty.php" class="fab-link active" id="nav-faculty">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Faculty Team
            </a>
            <a href="report.php" class="fab-link" id="nav-report">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Report Request
            </a>
            <a href="ctlactivity.php" class="fab-link" id="nav-ctl">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                    <line x1="15" y1="3" x2="15" y2="21" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                </svg>
                CTL Activity
            </a>
            <a href="ctldrive.php" class="fab-link" id="nav-drive">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Drive Scanner
            </a>
            <a href="zero-student-report.php" class="fab-link" id="nav-zero">
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

    <!-- Bootstrap 5 CDN JS Bundle -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Load Shared Faculty Data -->
    <script src="assets/js/facultyData.js"></script>

    <!-- Rendering Logic Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const grid = document.getElementById("facultyGrid");
            const modalContainer = document.getElementById("modalContainer");

            let cardsHtml = "";
            let modalsHtml = "";

            facultyData.forEach(member => {
                // Generate Card HTML
                cardsHtml += `
                <div class="faculty-card">
                    <div class="avatar-wrapper">
                        <div class="avatar-glow"></div>
                        <div class="avatar-image-placeholder ${member.avatarClass}">${member.initials}</div>
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
                                    <div class="modal-avatar-gradient ${member.avatarClass}">${member.initials}</div>
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

            grid.innerHTML = cardsHtml;
            modalContainer.innerHTML = modalsHtml;

            // ── FAB Nav Toggle ──
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
        });
    </script>
</body>

</html>
