<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Department of CE & IT — Academic Calendar showing Public Holidays, Reserved Holidays, and Departmental Events.">
    <title>Academic Calendar — CE & IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&family=Kameron:wght@400..700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CDN CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme and Custom CSS -->
    <link rel="stylesheet" href="assets/css/portal.css?v=3">
    <link rel="stylesheet" href="assets/css/faculty.css?v=3">
    <link rel="stylesheet" href="assets/css/calendar.css">
</head>

<body>

    <div class="fac-page">

        <!-- ░░ Particles (Matching Timetable & Faculty Page) ░░ -->
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
        </div>

        <!-- Glowing Background Orbs -->
        <div class="orb orb-1" aria-hidden="true"></div>
        <div class="orb orb-2" aria-hidden="true"></div>
        <div class="orb orb-3" aria-hidden="true"></div>

        <!-- ── Page Header ── -->
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
                    <h1 class="rp-title">Academic Calendar</h1>
                </div>

                <span class="portal-badge" id="portalBadge">IT Calendar</span>
            </div>
        </header>

        <!-- Main Content Layout -->
        <main class="container py-4">

            <!-- Filters Dashboard Panel -->
            <section class="filters-card">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    
                    <!-- Filter Checkboxes -->
                    <div class="filter-row">
                        <span class="filter-title">Filters:</span>
                        <div class="filter-checkboxes-group">
                            <label class="filter-checkbox-label check-public">
                                <input type="checkbox" id="filter-public" checked>
                                <span class="filter-indicator-dot"></span>
                                <span>Public Holidays</span>
                            </label>

                            <label class="filter-checkbox-label check-reserved">
                                <input type="checkbox" id="filter-reserved" checked>
                                <span class="filter-indicator-dot"></span>
                                <span>Reserved Holidays</span>
                            </label>

                            <label class="filter-checkbox-label check-custom">
                                <input type="checkbox" id="filter-custom" checked>
                                <span class="filter-indicator-dot"></span>
                                <span>Academic Events</span>
                            </label>
                        </div>
                    </div>

                    <!-- Department Selection Segment Controls -->
                    <div class="zs-segment-control m-0">
                        <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology">Information Technology</button>
                        <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
                    </div>

                </div>
            </section>

            <!-- Calendar Layout (Split view: Grid left, Sidebar right) -->
            <section class="calendar-layout">
                
                <!-- Left: Calendar Matrix -->
                <div class="calendar-card">
                    
                    <!-- Month/Year Toggle Row -->
                    <div class="calendar-header-row">
                        <button type="button" class="calendar-nav-btn" id="prev-month-btn" aria-label="Previous Month">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        <h2 class="month-year-title" id="monthYearDisplay">July 2026</h2>
                        
                        <button type="button" class="calendar-nav-btn" id="next-month-btn" aria-label="Next Month">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Days Header and Date Grid Wrapper -->
                    <div class="calendar-scroll-wrapper">
                        <div class="calendar-grid-min-width">
                            <div class="calendar-grid">
                                <div class="grid-day-header">Sun</div>
                                <div class="grid-day-header">Mon</div>
                                <div class="grid-day-header">Tue</div>
                                <div class="grid-day-header">Wed</div>
                                <div class="grid-day-header">Thu</div>
                                <div class="grid-day-header">Fri</div>
                                <div class="grid-day-header">Sat</div>
                            </div>
                            <div class="calendar-grid" id="calendarDaysGrid">
                                <!-- Dynamic cells populated via JS -->
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right: Detailed Info Sidebar -->
                <div class="details-card">
                    
                    <!-- Selected Date Header -->
                    <div class="details-header">
                        <h3 class="details-date-title" id="selectedDateTitle">Select a Date</h3>
                        <div class="details-subtitle" id="selectedDateSubtitle">Day Overview</div>
                    </div>

                    <!-- List of events on selected date -->
                    <div class="event-details-list" id="selectedEventsList">
                        <!-- Dynamic list populated via JS -->
                    </div>

                </div>

            </section>

        </main>

        <!-- Footer -->
        <?php 
        $footer_class = 'rp-footer text-center';
        include 'footer.php'; 
        ?>

    </div><!-- /fac-page -->

    <!-- Navigation Menu (FAB Bottom Right) -->
    <?php 
    $active_page = 'calendar';
    include 'fab-nav.php'; 
    ?>

    <!-- Bootstrap 5 CDN JS Bundle -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Load Shared Data -->
    <script src="assets/js/calendarData.js"></script>

    <!-- Calendar Rendering Logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // State variables
            const today = new Date();
            let currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            let selectedDateStr = `${today.getFullYear()}-${mm}-${dd}`;
            let currentFilterPublic = true;
            let currentFilterReserved = true;
            let currentFilterCustom = true;
            let currentDept = "Information Technology"; // Department state tracker

            // DOM Elements
            const calendarDaysGrid = document.getElementById("calendarDaysGrid");
            const monthYearDisplay = document.getElementById("monthYearDisplay");
            const prevMonthBtn = document.getElementById("prev-month-btn");
            const nextMonthBtn = document.getElementById("next-month-btn");
            const selectedDateTitle = document.getElementById("selectedDateTitle");
            const selectedDateSubtitle = document.getElementById("selectedDateSubtitle");
            const selectedEventsList = document.getElementById("selectedEventsList");

            // Filter Toggles
            const filterPublicCheckbox = document.getElementById("filter-public");
            const filterReservedCheckbox = document.getElementById("filter-reserved");
            const filterCustomCheckbox = document.getElementById("filter-custom");

            // Department Toggle Elements
            const itBtn = document.getElementById("dept-it-btn");
            const ceBtn = document.getElementById("dept-ce-btn");
            const rpDeptBadgeText = document.getElementById("rpDeptBadgeText");
            const portalBadge = document.getElementById("portalBadge");

            // Helper: Format Date to YYYY-MM-DD in local time
            function formatDateStr(year, month, day) {
                const mm = String(month + 1).padStart(2, '0');
                const dd = String(day).padStart(2, '0');
                return `${year}-${mm}-${dd}`;
            }

            // Helper: Convert YYYY-MM-DD to human readable string (e.g. August 15, 2026)
            function getHumanDate(dateString) {
                const dateParts = dateString.split("-");
                if (dateParts.length !== 3) return dateString;
                const d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                return d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }

            // Sync Department Theme (IT / CE)
            function updateDepartmentTheme(isCe) {
                currentDept = isCe ? "Computer Engineering" : "Information Technology";
                if (isCe) {
                    document.body.classList.add("ce-active");
                    if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Computer Engineering";
                    if (portalBadge) portalBadge.textContent = "CE Calendar";
                    itBtn.classList.remove("active");
                    ceBtn.classList.add("active");
                    localStorage.setItem('portal_dept', 'CE');
                    document.title = "Academic Calendar — CE Department";
                } else {
                    document.body.classList.remove("ce-active");
                    if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Information Technology";
                    if (portalBadge) portalBadge.textContent = "IT Calendar";
                    ceBtn.classList.remove("active");
                    itBtn.classList.add("active");
                    localStorage.setItem('portal_dept', 'IT');
                    document.title = "Academic Calendar — IT Department";
                }

                // Trigger re-render if elements are already loaded
                if (calendarDaysGrid && calendarDaysGrid.children.length > 0) {
                    renderCalendar();
                    showEventDetails(selectedDateStr);
                }
            }

            // Department Click Handlers
            itBtn.addEventListener("click", () => updateDepartmentTheme(false));
            ceBtn.addEventListener("click", () => updateDepartmentTheme(true));

            // Always default to Information Technology on initial page load
            updateDepartmentTheme(false);

            // Find events for a specific date
            function getEventsForDate(dateStr) {
                const matches = [];
                
                // Helper to check if event department matches currentDept
                function matchesDept(dept) {
                    if (!dept || dept === "all" || dept === "both") return true;
                    if (Array.isArray(dept)) {
                        return dept.includes(currentDept) || dept.includes("all") || dept.includes("both");
                    }
                    return dept === currentDept;
                }

                if (currentFilterPublic && calendarData.publicHolidays) {
                    calendarData.publicHolidays.forEach(h => {
                        if (h.date === dateStr) {
                            if (matchesDept(h.department)) {
                                matches.push({ ...h, type: 'public', badgeName: 'Public Holiday' });
                            }
                        }
                    });
                }
                
                if (currentFilterReserved && calendarData.reservedHolidays) {
                    calendarData.reservedHolidays.forEach(h => {
                        if (h.date === dateStr) {
                            if (matchesDept(h.department)) {
                                matches.push({ ...h, type: 'reserved', badgeName: 'Reserved Holiday' });
                            }
                        }
                    });
                }
                
                if (currentFilterCustom) {
                    if (calendarData.customEvents) {
                        calendarData.customEvents.forEach(e => {
                            if (e.date === dateStr) {
                                if (matchesDept(e.department)) {
                                    matches.push({ ...e, type: 'custom', badgeName: e.category || 'Event' });
                                }
                            }
                        });
                    }

                    if (calendarData.recurringEvents) {
                        const parts = dateStr.split("-");
                        const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                        const dayOfWeek = dateObj.getDay();

                        calendarData.recurringEvents.forEach(r => {
                            if (dayOfWeek === r.dayOfWeek && dateStr >= r.startDate) {
                                if (matchesDept(r.department)) {
                                    matches.push({
                                        date: dateStr,
                                        name: r.name,
                                        type: 'custom',
                                        badgeName: r.category || 'Event',
                                        description: r.description,
                                        time: r.time,
                                        department: r.department
                                    });
                                }
                            }
                        });
                    }
                }
                
                return matches;
            }

            // Render calendar grid and update sidebar displays
            function renderCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();

                // Clear previous grid elements
                calendarDaysGrid.innerHTML = "";

                // Update Month/Year Header text
                const monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

                // Calculate first day and count of days in month
                const firstDayIndex = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();

                // Render blank filler cells for start spacing
                for (let i = 0; i < firstDayIndex; i++) {
                    const emptyCell = document.createElement("div");
                    emptyCell.className = "grid-cell empty-cell";
                    calendarDaysGrid.appendChild(emptyCell);
                }

                // Render day cells
                const today = new Date();
                const todayFormatted = formatDateStr(today.getFullYear(), today.getMonth(), today.getDate());

                for (let day = 1; day <= totalDays; day++) {
                    const cellDateStr = formatDateStr(year, month, day);
                    const cellEvents = getEventsForDate(cellDateStr);

                    const dayCell = document.createElement("div");
                    dayCell.className = "grid-cell";
                    dayCell.setAttribute("data-date", cellDateStr);

                    // Add dynamic class indicators
                    if (cellDateStr === todayFormatted) {
                        dayCell.classList.add("today-cell");
                    }
                    if (cellDateStr === selectedDateStr) {
                        dayCell.classList.add("selected-cell");
                    }

                    // Date Label
                    const dateLabel = document.createElement("span");
                    dateLabel.className = "date-number";
                    dateLabel.textContent = day;
                    dayCell.appendChild(dateLabel);

                    // Render event status dots inside cell
                    if (cellEvents.length > 0) {
                        const dotsContainer = document.createElement("div");
                        dotsContainer.className = "indicator-dots";

                        // Create deduped list of indicator dots
                        const types = new Set(cellEvents.map(e => e.type));
                        types.forEach(t => {
                            const dot = document.createElement("span");
                            dot.className = `dot dot-${t}`;
                            dotsContainer.appendChild(dot);
                        });

                        dayCell.appendChild(dotsContainer);
                    }

                    // Click handler to select date
                    dayCell.addEventListener("click", () => {
                        // Remove previously selected highlights
                        const activeSelected = calendarDaysGrid.querySelector(".selected-cell");
                        if (activeSelected) {
                            activeSelected.classList.remove("selected-cell");
                        }

                        // Add highlighting to current target
                        dayCell.classList.add("selected-cell");
                        selectedDateStr = cellDateStr;
                        showEventDetails(cellDateStr);
                    });

                    calendarDaysGrid.appendChild(dayCell);
                }
            }

            // Display list of events for the selected day in sidebar
            function showEventDetails(dateStr) {
                selectedDateTitle.textContent = getHumanDate(dateStr);
                
                const events = getEventsForDate(dateStr);
                selectedEventsList.innerHTML = "";

                if (events.length === 0) {
                    selectedEventsList.innerHTML = `
                        <div class="no-events-placeholder">
                            <span class="placeholder-icon">🍵</span>
                            <span>No holidays or events scheduled for this day.</span>
                        </div>
                    `;
                    selectedDateSubtitle.textContent = "Day Overview";
                    return;
                }

                // Subtitle count
                selectedDateSubtitle.textContent = `${events.length} Scheduled Item(s)`;

                // Loop and render details cards
                events.forEach(e => {
                    const node = document.createElement("div");
                    node.className = `event-node type-${e.type}`;

                    // Build meta elements html
                    let metaHtml = '';
                    if (e.time || e.department) {
                        metaHtml += `<div class="event-node-meta">`;
                        
                        if (e.time) {
                            metaHtml += `
                                <div class="event-node-time">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 6v6h6"/>
                                    </svg>
                                    <span>${e.time}</span>
                                </div>
                            `;
                        }

                        if (e.department) {
                            const isBoth = Array.isArray(e.department) ? e.department.length > 1 : (e.department === 'both' || e.department === 'all');
                            if (isBoth) {
                                metaHtml += `
                                    <div class="event-node-dept dept-both">
                                        <span>Common: CE & IT</span>
                                    </div>
                                `;
                            } else {
                                const deptName = Array.isArray(e.department) ? e.department[0] : e.department;
                                const shortDeptName = deptName === 'Computer Engineering' ? 'CE' : (deptName === 'Information Technology' ? 'IT' : deptName);
                                metaHtml += `
                                    <div class="event-node-dept">
                                        <span>Dept: ${shortDeptName}</span>
                                    </div>
                                `;
                            }
                        }

                        metaHtml += `</div>`;
                    }

                    node.innerHTML = `
                        <div class="event-node-header">
                            <h4 class="event-node-title">${e.name}</h4>
                            <span class="event-node-badge badge-${e.type}">${e.badgeName}</span>
                        </div>
                        ${metaHtml}
                        <p class="event-node-desc">${e.description || 'No description provided.'}</p>
                    `;
                    selectedEventsList.appendChild(node);
                });
            }

            // Month navigation events
            prevMonthBtn.addEventListener("click", () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
                // If the selected date is in the newly loaded month, view it, else update sidebar with instructions
                const parts = selectedDateStr.split("-");
                if (parseInt(parts[0]) === currentDate.getFullYear() && (parseInt(parts[1]) - 1) === currentDate.getMonth()) {
                    showEventDetails(selectedDateStr);
                } else {
                    selectedDateTitle.textContent = "Select a Date";
                    selectedDateSubtitle.textContent = "Day Overview";
                    selectedEventsList.innerHTML = `
                        <div class="no-events-placeholder">
                            <span class="placeholder-icon">📅</span>
                            <span>Click a cell on the calendar to view scheduled day events.</span>
                        </div>
                    `;
                }
            });

            nextMonthBtn.addEventListener("click", () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
                const parts = selectedDateStr.split("-");
                if (parseInt(parts[0]) === currentDate.getFullYear() && (parseInt(parts[1]) - 1) === currentDate.getMonth()) {
                    showEventDetails(selectedDateStr);
                } else {
                    selectedDateTitle.textContent = "Select a Date";
                    selectedDateSubtitle.textContent = "Day Overview";
                    selectedEventsList.innerHTML = `
                        <div class="no-events-placeholder">
                            <span class="placeholder-icon">📅</span>
                            <span>Click a cell on the calendar to view scheduled day events.</span>
                        </div>
                    `;
                }
            });

            // Filter Change Listeners
            filterPublicCheckbox.addEventListener("change", (e) => {
                currentFilterPublic = e.target.checked;
                renderCalendar();
                showEventDetails(selectedDateStr);
            });

            filterReservedCheckbox.addEventListener("change", (e) => {
                currentFilterReserved = e.target.checked;
                renderCalendar();
                showEventDetails(selectedDateStr);
            });

            filterCustomCheckbox.addEventListener("change", (e) => {
                currentFilterCustom = e.target.checked;
                renderCalendar();
                showEventDetails(selectedDateStr);
            });

            // Initial render call
            renderCalendar();
            showEventDetails(selectedDateStr);
        });
    </script>

</body>

</html>
