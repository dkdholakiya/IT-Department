<?php
$active_page = isset($active_page) ? $active_page : 'home';
?>
<!-- Light Theme Stylesheet -->
<link rel="stylesheet" href="assets/css/theme-light.css">

<!-- Theme Toggle Widget (Bottom Left Corner) -->
<?php include_once 'theme-toggle.php'; ?>

<!-- FAB Background Blur Overlay -->
<div class="fab-overlay" id="fabOverlay"></div>

<!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
<div class="fab-nav" id="fabNav" data-active-page="<?php echo $active_page; ?>">
    <div class="fab-menu" id="fabMenu">
        <a href="./" class="fab-link <?php echo ($active_page === 'home') ? 'active' : ''; ?>" id="nav-home">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Home
        </a>
        <a href="faculty" class="fab-link <?php echo ($active_page === 'faculty') ? 'active' : ''; ?>" id="nav-faculty">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Faculty Team
        </a>
        <a href="report" class="fab-link <?php echo ($active_page === 'report') ? 'active' : ''; ?>" id="nav-report">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
            Report Request
        </a>
        <a href="syllabus" class="fab-link <?php echo ($active_page === 'syllabus' || $active_page === 'website') ? 'active' : ''; ?>" id="nav-syllabus">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                <path d="M6 6h10"></path>
                <path d="M6 10h10"></path>
            </svg>
            Syllabus Check
        </a>
    
        <a href="timetable" class="fab-link <?php echo ($active_page === 'timetable') ? 'active' : ''; ?>" id="nav-timetable">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            Personal Timetable
        </a>
        <a href="student-timetable" class="fab-link <?php echo ($active_page === 'student-timetable') ? 'active' : ''; ?>" id="nav-student-timetable">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Student Timetable
        </a>
        <a href="calendar" class="fab-link <?php echo ($active_page === 'calendar') ? 'active' : ''; ?>" id="nav-calendar">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
            </svg>
            Calendar
        </a>
            <a href="ctlactivity" class="fab-link <?php echo ($active_page === 'ctlactivity') ? 'active' : ''; ?>" id="nav-ctl">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="9" y1="3" x2="9" y2="21" />
                <line x1="15" y1="3" x2="15" y2="21" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="3" y1="15" x2="21" y2="15" />
            </svg>
            CTL Activity
        </a>
        <a href="ctldrive" class="fab-link <?php echo ($active_page === 'ctldrive') ? 'active' : ''; ?>" id="nav-drive">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
            </svg>
            Drive Scanner
        </a>
        <a href="zero-student-report" class="fab-link <?php echo ($active_page === 'zero-student-report') ? 'active' : ''; ?>" id="nav-zero">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize global department theme on load
        const fabNav = document.getElementById('fabNav');
        const activePage = fabNav ? fabNav.getAttribute('data-active-page') : 'home';
        
        // Define which pages are inherently common pages
        const isCommonPage = (activePage === 'report' || activePage === 'ctldrive' || activePage === 'ctlactivity');

        if (isCommonPage) {
            document.body.classList.remove('ce-active');
            document.body.classList.add('common-active');
        } else {
            // Always default to IT theme (red) on load for non-common pages
            document.body.classList.remove('ce-active');
            document.body.classList.remove('common-active');
        }

        const fabBtn = document.getElementById('fabBtn');
        const fabMenu = document.getElementById('fabMenu');
        const fabOverlay = document.getElementById('fabOverlay');
        const iconMenu = fabBtn.querySelector('.fab-icon-menu');
        const iconClose = fabBtn.querySelector('.fab-icon-close');

        if (fabBtn && fabMenu) {
            fabBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = fabMenu.classList.toggle('open');
                fabBtn.classList.toggle('active', isOpen);
                if (fabOverlay) fabOverlay.classList.toggle('open', isOpen);
                iconMenu.style.display = isOpen ? 'none' : 'block';
                iconClose.style.display = isOpen ? 'block' : 'none';
            });

            document.addEventListener('click', (e) => {
                const fabNav = document.getElementById('fabNav');
                if (fabNav && !fabNav.contains(e.target)) {
                    fabMenu.classList.remove('open');
                    fabBtn.classList.remove('active');
                    if (fabOverlay) fabOverlay.classList.remove('open');
                    iconMenu.style.display = 'block';
                    iconClose.style.display = 'none';
                }
            });

            if (fabOverlay) {
                fabOverlay.addEventListener('click', () => {
                    fabMenu.classList.remove('open');
                    fabBtn.classList.remove('active');
                    fabOverlay.classList.remove('open');
                    iconMenu.style.display = 'block';
                    iconClose.style.display = 'none';
                });
            }
        }
    });
</script>
