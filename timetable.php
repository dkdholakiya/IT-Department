<?php
$excelFile = __DIR__ . '/uploads/timetable/Personal Time Table CE_CSE _ IT _ ICT.xlsx';
$excelExists = file_exists($excelFile);
$jsDataFile = __DIR__ . '/assets/js/timetableData.js';
$jsDataExists = file_exists($jsDataFile);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Department of CE & IT — Faculty Timetable Viewer for weekly lecture schedules.">
    <title>Faculty Timetable — CE & IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Outfit:wght@300;400;600;700;800&family=Share+Tech&display=swap"
        rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/portal.css?v=3">
    <link rel="stylesheet" href="assets/css/faculty.css?v=3">
    <link rel="stylesheet" href="assets/css/timetable.css?v=8">
</head>

<body>

    <!-- ░░ Particles (Matching Home Page) ░░ -->
    <div class="particles" aria-hidden="true">
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

    <!-- Page Header -->
    <header class="rp-header">
        <div class="rp-header-inner container">
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
                <h1 class="rp-title" id="pageTitleMain">Faculty Timetable Viewer</h1>
            </div>

            <span class="portal-badge" id="portalBadge">IT Timetable</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="timetable-container container">

        <?php if (!$excelExists): ?>
            <!-- Error message if Excel doesn't exist -->
            <div class="controls-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 24px; gap: 20px; text-align: center;">
                <div style="font-size: 54px;">📅</div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: #f87171; margin: 0;">No Timetable Loaded</h2>
                <p style="color: #94a3b8; font-size: 14.5px; max-width: 500px; line-height: 1.6; margin: 0;">
                    The schedule Excel file is missing from the uploads folder. Please place the timetable Excel sheet inside the <code>uploads/timetable/</code> directory.
                </p>
                <div style="margin-top: 10px;">
                    <a href="update-timetable" class="back-btn" style="text-decoration: none;">
                        Upload & Sync Database
                    </a>
                </div>
            </div>

        <?php elseif (!$jsDataExists): ?>
            <!-- Error message if JS database doesn't exist -->
            <div class="controls-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 24px; gap: 20px; text-align: center;">
                <div style="font-size: 54px;">🔄</div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: #fbbf24; margin: 0;">Sync Required</h2>
                <p style="color: #94a3b8; font-size: 14.5px; max-width: 500px; line-height: 1.6; margin: 0;">
                    The schedule Excel file is uploaded, but the database has not been compiled yet. Please run the sync utility to compile the timetable details.
                </p>
                <div style="margin-top: 10px;">
                    <a href="update-timetable" class="back-btn" style="text-decoration: none;">
                        Sync Database
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Controls Card (Search and Selection) -->
            <div class="controls-card">
                <div class="selector-group">
                    <span class="selector-label">Select Faculty:</span>
                    <div class="custom-select-wrapper">
                        <div class="custom-select-trigger" id="selectTrigger">
                            <span id="selectedFacultyText">Select a member...</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div class="custom-select-options" id="selectOptions">
                            <div class="select-search-box">
                                <input type="text" class="select-search-input" id="selectSearchInput" placeholder="Search by name or initials..." autocomplete="off">
                            </div>
                            <div id="optionsListContainer">
                                <!-- Populated via Javascript -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="zs-segment-control" style="margin-top: 0;">
                    <button type="button" class="segment-btn active" id="dept-it-btn" data-dept="Information Technology">Information Technology</button>
                    <button type="button" class="segment-btn" id="dept-ce-btn" data-dept="Computer Engineering">Computer Engineering</button>
                </div>
            </div>

            <!-- Faculty Details Badge -->
            <div class="faculty-info-card" id="facultyInfoCard">
                <div class="faculty-avatar" id="facAvatar">SBC</div>
                <div class="faculty-meta-wrapper">
                    <div class="faculty-meta-left">
                        <h2 class="faculty-name-title" id="facName">Prof. Shwetaba B. Chauhan</h2>
                        <span class="faculty-designation-badge" id="facDesignation"></span>
                    </div>
                    <div class="faculty-meta-right">
                        <span class="faculty-dept-badge" id="facDeptBadge">Department of Information Technology</span>
                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 6px; flex-wrap: wrap; justify-content: flex-end;">
                            <div class="faculty-semester-info" id="facSemesterInfo"></div>
                            <button type="button" class="leave-toggle-btn" id="leaveToggleBtn">Alteration</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timetable Table Card -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="timetable-table">
                        <thead>
                            <tr>
                                <th class="time-col-header">Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                            </tr>
                        </thead>
                        <tbody id="timetableBody">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Leave Arrangement Card -->
            <div class="leave-card" id="leaveCardContainer" style="display: none;">
                <div class="leave-card-header">
                    <div>
                        <h3 class="leave-card-title">Proxy Assignment Finder</h3>
                        <p class="leave-card-subtitle">Find available faculty members to substitute for scheduled lectures when this member has an alteration.</p>
                    </div>
                    <div class="load-badge" id="selectedFacultyLoadBadge">
                        <span class="load-badge-dot" aria-hidden="true"></span>
                        <div class="load-badge-content">
                            <span class="load-badge-label" id="loadBadgeDayLabel">Monday Workload</span>
                            <div class="load-badge-value">
                                <span id="loadBadgeNum">0.0</span>
                                <span class="load-badge-unit">Hrs</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Day, Leave Type, and Time Selectors -->
                <div class="leave-controls-row">
                    <div class="leave-form-group">
                        <label for="leaveDaySelect">1. Select Day:</label>
                        <div class="select-wrap">
                            <select id="leaveDaySelect" class="custom-form-select">
                                <option value="">Select Day...</option>
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THU">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                            </select>
                            <span class="select-arrow">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="leave-form-group">
                        <label for="leaveTypeSelect">2. Alteration Type:</label>
                        <div class="select-wrap">
                            <select id="leaveTypeSelect" class="custom-form-select" disabled>
                                <option value="FULL">Full Day</option>
                                <option value="FIRST">First Half</option>
                                <option value="SECOND">Second Half</option>
                            </select>
                            <span class="select-arrow">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="leave-form-group">
                        <label for="leaveTimeSelect">3. Select Time Slot:</label>
                        <div class="select-wrap">
                            <select id="leaveTimeSelect" class="custom-form-select" disabled>
                                <option value="ALL">All Time Slots</option>
                            </select>
                            <span class="select-arrow">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="leave-results-container" id="leaveResultsContainer">
                    <div class="leave-instructions">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-bottom: 10px; opacity: 0.6;">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Please select a day and time slot to search available proxy candidates.
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Navigation Hub (Bottom Right) -->
    <?php 
    $active_page = 'timetable';
    include 'fab-nav.php'; 
    ?>

    <!-- Timetable Data and Logic -->
    <script src="assets/js/facultyData.js"></script>
    <script src="assets/js/timetableData.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const selectTrigger = document.getElementById("selectTrigger");
            if (!selectTrigger) return;
            const selectOptions = document.getElementById("selectOptions");
            const selectSearchInput = document.getElementById("selectSearchInput");
            const optionsListContainer = document.getElementById("optionsListContainer");
            const selectedFacultyText = document.getElementById("selectedFacultyText");
            
            const itBtn = document.getElementById("dept-it-btn");
            const ceBtn = document.getElementById("dept-ce-btn");
            const rpDeptBadgeText = document.getElementById("rpDeptBadgeText");
            const portalBadge = document.getElementById("portalBadge");
            
            const facAvatar = document.getElementById("facAvatar");
            const facName = document.getElementById("facName");
            const facDeptBadge = document.getElementById("facDeptBadge");
            const timetableBody = document.getElementById("timetableBody");

            let currentActiveDept = "Information Technology";
            let selectedInitials = "SBC";
            let currentTab = "timetable"; // "timetable" or "leave"

            // Map Excel timetableData with official facultyData.js file to filter, name-match, and group by department
            const mappedTimetable = {};
            if (typeof facultyData !== 'undefined' && typeof timetableData !== 'undefined') {
                facultyData.forEach(member => {
                    const initials = member.initials.toUpperCase().trim();
                    const excelKey = Object.keys(timetableData).find(
                        key => key.toUpperCase().trim() === initials
                    );
                    if (excelKey) {
                        mappedTimetable[initials] = {
                            name: member.name,
                            initials: initials,
                            department: member.department,
                            designation: member.designation || '',
                            semesterInfo: timetableData[excelKey].semesterInfo || '',
                            schedule: timetableData[excelKey].schedule
                        };
                    }
                });
            }

            // Helper to parse time string like "12:15" or "01:00 PM" into minutes from midnight
            function parseTimeToMinutes(timeStr) {
                if (!timeStr) return null;
                timeStr = timeStr.trim().toLowerCase();
                
                let isPM = timeStr.includes('pm');
                let isAM = timeStr.includes('am');
                
                let clean = timeStr.replace(/[^0-9:.]/g, '');
                let parts = clean.split(/[:.]/);
                if (parts.length < 2) return null;
                
                let hours = parseInt(parts[0], 10);
                let minutes = parseInt(parts[1], 10);
                
                if (isPM && hours < 12) {
                    hours += 12;
                }
                if (isAM && hours === 12) {
                    hours = 0;
                }
                
                // Infer afternoon/evening PM for times between 1:00 and 6:59 when AM/PM is omitted
                if (!isPM && !isAM) {
                    if (hours >= 1 && hours < 7) {
                        hours += 12;
                    }
                }
                
                return hours * 60 + minutes;
            }

            // Extract start and end time strings from a range like "12:00 to 01:00 PM" or "02:45 03:45"
            function parseTimeRangeStrings(timeStr) {
                if (!timeStr) return null;
                const matches = timeStr.match(/\d{1,2}\s*[:.]\s*\d{2}(?:\s*(?:am|pm))?/gi);
                if (matches && matches.length >= 2) {
                    return {
                        startStr: matches[0].trim(),
                        endStr: matches[matches.length - 1].trim()
                    };
                }
                return null;
            }

            // Helper to parse slot time range into start and end minutes
            function getSlotMinutes(timeStr) {
                const range = parseTimeRangeStrings(timeStr);
                if (range) {
                    const startMin = parseTimeToMinutes(range.startStr);
                    const endMin = parseTimeToMinutes(range.endStr);
                    if (startMin !== null && endMin !== null) {
                        return { start: startMin, end: endMin };
                    }
                }
                return null;
            }

            // Determine faculty shift based on their schedule on the given day (or across the week if day is free)
            function getFacultyShift(faculty, day) {
                let occupiedSlots = faculty.schedule.filter(slot => {
                    return !slot.isRecess && slot[day] && slot[day].occupied === true;
                });
                
                // Fallback to checking all days of the week if they have no occupied lectures on this day
                if (occupiedSlots.length === 0) {
                    const days = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    days.forEach(d => {
                        const slots = faculty.schedule.filter(slot => {
                            return !slot.isRecess && slot[d] && slot[d].occupied === true;
                        });
                        occupiedSlots = occupiedSlots.concat(slots);
                    });
                }
                
                let minStart = 1440; // 24 hours in minutes
                let maxEnd = 0;
                occupiedSlots.forEach(slot => {
                    const times = getSlotMinutes(slot.time);
                    if (times) {
                        if (times.start < minStart) {
                            minStart = times.start;
                        }
                        if (times.end > maxEnd) {
                            maxEnd = times.end;
                        }
                    }
                });
                
                // Shift boundaries in minutes from midnight:
                // Shift-1: 7:30 AM to 2:45 PM (450 to 885 minutes)
                // Shift-2: 9:30 AM to 5:15 PM (570 to 1035 minutes)
                // Shift-3: 10:30 AM to 6:00 PM (630 to 1080 minutes)
                if (maxEnd === 0) {
                    return {
                        id: 1,
                        name: "Shift-1",
                        start: 450, // 7:30 AM
                        end: 885,   // 2:45 PM
                        startTimeStr: "7:30 AM",
                        endTimeStr: "2:45 PM"
                    };
                }

                if (maxEnd > 1035 || minStart >= 660) { // First lecture at/after 11:00 AM OR last lecture ends after 5:15 PM
                    return {
                        id: 3,
                        name: "Shift-3",
                        start: 630, // 10:30 AM
                        end: 1080,  // 6:00 PM
                        startTimeStr: "10:30 AM",
                        endTimeStr: "6:00 PM"
                    };
                } else if (maxEnd > 885 || minStart >= 600) { // First lecture at 10:00 AM OR last lecture ends after 2:45 PM
                    return {
                        id: 2,
                        name: "Shift-2",
                        start: 570, // 9:30 AM
                        end: 1035,  // 5:15 PM
                        startTimeStr: "9:30 AM",
                        endTimeStr: "5:15 PM"
                    };
                } else {
                    return {
                        id: 1,
                        name: "Shift-1",
                        start: 450, // 7:30 AM
                        end: 885,   // 2:45 PM
                        startTimeStr: "7:30 AM",
                        endTimeStr: "2:45 PM"
                    };
                }
            }

            // Calculate total faculty load in hours for a given day
            function getFacultyLoad(faculty, day) {
                let loadMinutes = 0;
                faculty.schedule.forEach(slot => {
                    if (!slot.isRecess && slot[day] && slot[day].occupied === true) {
                        const times = getSlotMinutes(slot.time);
                        if (times) {
                            loadMinutes += (times.end - times.start);
                        }
                    }
                });
                return loadMinutes / 60;
            }

            // Update the badge displaying the selected faculty's total workload for the chosen day
            function updateSelectedFacultyLoadBadge() {
                const daySelect = document.getElementById("leaveDaySelect");
                const badge = document.getElementById("selectedFacultyLoadBadge");
                if (!daySelect || !badge) return;

                const day = daySelect.value;
                const faculty = mappedTimetable[selectedInitials];

                if (!day || !faculty) {
                    badge.style.display = "none";
                    return;
                }

                const load = getFacultyLoad(faculty, day);
                const dayNames = {
                    'MON': 'Monday',
                    'TUE': 'Tuesday',
                    'WED': 'Wednesday',
                    'THU': 'Thursday',
                    'FRI': 'Friday',
                    'SAT': 'Saturday'
                };
                const dayName = dayNames[day] || '';

                const dayLabel = document.getElementById("loadBadgeDayLabel");
                const numSpan = document.getElementById("loadBadgeNum");

                if (dayLabel) dayLabel.textContent = `${dayName} Workload`;
                if (numSpan) numSpan.textContent = load.toFixed(1);

                // Toggle overloaded state (>= 4 hours)
                badge.classList.toggle("overloaded", load >= 4);
                badge.style.display = "flex";

                // Re-trigger the fade-in animation
                badge.style.animation = "none";
                badge.offsetHeight; // force reflow
                badge.style.animation = "";
            }

            // Sync layout themes
            function setDepartmentTheme(dept) {
                currentActiveDept = dept;
                toggleLeaveMode("timetable");

                if (dept === "Computer Engineering") {
                    document.body.classList.add("ce-active");
                    if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Computer Engineering";
                    if (portalBadge) portalBadge.textContent = "CE Timetable";
                    itBtn.classList.remove("active");
                    ceBtn.classList.add("active");
                } else {
                    document.body.classList.remove("ce-active");
                    if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Information Technology";
                    if (portalBadge) portalBadge.textContent = "IT Timetable";
                    ceBtn.classList.remove("active");
                    itBtn.classList.add("active");
                }
                populateDropdown();
                
                // Select first faculty in selected department
                const faculty = mappedTimetable[selectedInitials];
                if (!faculty || (faculty.department !== dept && faculty.department !== "Both")) {
                    const keys = Object.keys(mappedTimetable);
                    const match = keys.find(k => mappedTimetable[k].department === dept || mappedTimetable[k].department === "Both");
                    if (match) {
                        loadTimetable(match);
                    }
                } else {
                    loadTimetable(selectedInitials);
                }
            }

            // Toggle view mode between Timetable grid and Leave proxy finder
            function toggleLeaveMode(forceTab) {
                const leaveCard = document.getElementById("leaveCardContainer");
                const tableCard = document.querySelector(".table-card");
                const leaveToggleBtn = document.getElementById("leaveToggleBtn");

                if (forceTab !== undefined) {
                    currentTab = forceTab;
                } else {
                    currentTab = (currentTab === "leave") ? "timetable" : "leave";
                }

                if (currentTab === "leave") {
                    if (leaveCard) leaveCard.style.display = "flex";
                    if (tableCard) tableCard.style.display = "none";
                    if (leaveToggleBtn) {
                        leaveToggleBtn.textContent = "Show Timetable";
                        leaveToggleBtn.classList.add("active");
                    }
                    if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Faculty Alteration Arrangement Finder";
                    if (portalBadge) portalBadge.textContent = "Alteration Tracker";
                    renderLeaveArrangements(selectedInitials);
                    updateSelectedFacultyLoadBadge();
                } else {
                    if (leaveCard) leaveCard.style.display = "none";
                    if (tableCard) tableCard.style.display = "block";
                    const badge = document.getElementById("selectedFacultyLoadBadge");
                    if (badge) { badge.style.display = "none"; badge.classList.remove("overloaded"); }
                    if (leaveToggleBtn) {
                        leaveToggleBtn.textContent = "Alteration";
                        leaveToggleBtn.classList.remove("active");
                    }
                    if (currentActiveDept === "Computer Engineering") {
                        if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Computer Engineering";
                        if (portalBadge) portalBadge.textContent = "CE Timetable";
                    } else {
                        if (rpDeptBadgeText) rpDeptBadgeText.textContent = "Department of Information Technology";
                        if (portalBadge) portalBadge.textContent = "IT Timetable";
                    }
                    const faculty = mappedTimetable[selectedInitials];
                    if (faculty) {
                        renderTimetableGrid(faculty);
                    }
                }
            }

            // Dropdown controls
            selectTrigger.addEventListener("click", (e) => {
                e.stopPropagation();
                selectOptions.classList.toggle("show");
                selectTrigger.classList.toggle("active");
                if (selectOptions.classList.contains("show")) {
                    selectSearchInput.value = "";
                    filterOptions("");
                    selectSearchInput.focus();
                }
            });

            document.addEventListener("click", () => {
                selectOptions.classList.remove("show");
                selectTrigger.classList.remove("active");
            });

            selectSearchInput.addEventListener("input", (e) => {
                filterOptions(e.target.value);
            });

            selectSearchInput.addEventListener("click", (e) => {
                e.stopPropagation(); // prevent dropdown close
            });

            // Populate custom select options
            function populateDropdown() {
                optionsListContainer.innerHTML = "";
                const sortedKeys = Object.keys(mappedTimetable).sort();
                
                sortedKeys.forEach(initials => {
                    const data = mappedTimetable[initials];
                    // If in timetable tab, show only active department
                    if (currentTab !== "leave" && data.department !== currentActiveDept && data.department !== "Both") return;

                    const option = document.createElement("div");
                    option.className = `select-option ${initials === selectedInitials ? 'selected' : ''}`;
                    option.setAttribute("data-initials", initials);
                    
                    option.innerHTML = `
                        <span>${data.name} (${initials})</span>
                        <span class="option-initials">${initials}</span>
                    `;

                    option.addEventListener("click", () => {
                        loadTimetable(initials);
                    });

                    optionsListContainer.appendChild(option);
                });
            }

            // Filter options dynamically
            function filterOptions(query) {
                const q = query.toLowerCase().trim();
                const options = optionsListContainer.querySelectorAll(".select-option");
                options.forEach(opt => {
                    const text = opt.innerText.toLowerCase();
                    if (text.includes(q)) {
                        opt.style.display = "flex";
                    } else {
                        opt.style.display = "none";
                    }
                });
            }

            // Load and render timetable
            function loadTimetable(initials) {
                selectedInitials = initials;
                const faculty = mappedTimetable[initials];
                if (!faculty) return;

                // Update UI text triggers
                selectedFacultyText.innerText = `${faculty.name} (${initials})`;
                facAvatar.innerText = initials;
                facName.innerText = faculty.name;
                facDeptBadge.innerText = faculty.department === "Both" ? "Department of CE & IT" : `Department of ${faculty.department}`;
                
                const facDesignation = document.getElementById("facDesignation");
                if (facDesignation) {
                    facDesignation.innerText = faculty.designation || '';
                }
                const facSemesterInfo = document.getElementById("facSemesterInfo");
                if (facSemesterInfo) {
                    facSemesterInfo.innerText = faculty.semesterInfo || '';
                }

                // Update document title dynamically
                if (currentTab === "leave") {
                    document.title = `Faculty Alteration Proxy (${initials}) — GMIU`;
                } else {
                    document.title = `Faculty Timetable (${initials}) — ${faculty.department === "Both" ? "CE & IT" : faculty.department} Department`;
                }

                // Update highlight state in options list
                const options = optionsListContainer.querySelectorAll(".select-option");
                options.forEach(opt => {
                    if (opt.getAttribute("data-initials") === initials) {
                        opt.classList.add("selected");
                    } else {
                        opt.classList.remove("selected");
                    }
                });

                if (currentTab === "leave") {
                    renderLeaveArrangements(initials);
                    updateSelectedFacultyLoadBadge();
                } else {
                    renderTimetableGrid(faculty);
                }
            }

            // Render standard timetable grid
            function renderTimetableGrid(faculty) {
                timetableBody.innerHTML = "";
                const days = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                const rowCount = faculty.schedule.length;

                // 1. Initialize grid of cell objects
                const grid = [];
                for (let r = 0; r < rowCount; r++) {
                    grid[r] = [];
                    const slot = faculty.schedule[r];
                    if (slot.isRecess) continue;

                    days.forEach((day, dIdx) => {
                        const daySlot = slot[day];
                        grid[r][dIdx] = {
                            occupied: daySlot ? daySlot.occupied : false,
                            class: daySlot ? daySlot.class : '',
                            room: daySlot ? daySlot.room : '',
                            rowspan: 1,
                            skip: false
                        };
                    });
                }

                // 2. Scan and merge identical consecutive slots for each day dynamically
                for (let d = 0; d < 6; d++) {
                    let mergeStartRow = -1;
                    let lastEndMin = -1;
                    
                    for (let r = 0; r < rowCount; r++) {
                        const slot = faculty.schedule[r];
                        if (slot.isRecess) {
                            mergeStartRow = -1;
                            lastEndMin = -1;
                            continue;
                        }
                        
                        const cell = grid[r][d];
                        const times = getSlotMinutes(slot.time);
                        
                        if (!cell || !cell.occupied || !times) {
                            mergeStartRow = -1;
                            lastEndMin = -1;
                            continue;
                        }
                        
                        if (mergeStartRow !== -1) {
                            const startCell = grid[mergeStartRow][d];
                            if (startCell.class === cell.class && startCell.room === cell.room && lastEndMin === times.start) {
                                startCell.rowspan += 1;
                                cell.skip = true;
                                lastEndMin = times.end;
                                continue;
                            }
                        }
                        
                        mergeStartRow = r;
                        lastEndMin = times.end;
                    }
                }

                // 3. Render rows
                for (let r = 0; r < rowCount; r++) {
                    const slot = faculty.schedule[r];
                    const tr = document.createElement("tr");

                    if (slot.isRecess) {
                        tr.className = "recess-row";
                        tr.innerHTML = `
                            <td class="time-cell">${slot.time}</td>
                            <td colspan="6" class="recess-cell">${slot.label}</td>
                        `;
                    } else {
                        let slotsHtml = `<td class="time-cell">${slot.time}</td>`;

                        for (let d = 0; d < 6; d++) {
                            const cell = grid[r][d];
                            if (cell.skip) continue;

                            const rowspanAttr = cell.rowspan > 1 ? ` rowspan="${cell.rowspan}"` : '';

                            if (cell.occupied) {
                                slotsHtml += `
                                    <td class="slot-occupied"${rowspanAttr}>
                                        <div class="occupied-wrapper">
                                            <span class="class-badge">${cell.class}</span>
                                            <span class="room-badge">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
                                                </svg>
                                                ${cell.room}
                                            </span>
                                        </div>
                                    </td>
                                `;
                            } else {
                                slotsHtml += `
                                    <td class="slot-free"${rowspanAttr}>-</td>
                                `;
                            }
                        }
                        tr.innerHTML = slotsHtml;
                    }
                    timetableBody.appendChild(tr);
                }
            }

            // Render day-wise leave arrangements and find free substitutes
            function renderLeaveArrangements(absentInitials) {
                const leaveDaySelect = document.getElementById("leaveDaySelect");
                const leaveTypeSelect = document.getElementById("leaveTypeSelect");
                const leaveTimeSelect = document.getElementById("leaveTimeSelect");
                const leaveResultsContainer = document.getElementById("leaveResultsContainer");

                if (leaveDaySelect) leaveDaySelect.value = "";
                if (leaveTypeSelect) {
                    leaveTypeSelect.value = "FULL";
                    leaveTypeSelect.disabled = true;
                }
                if (leaveTimeSelect) {
                    leaveTimeSelect.innerHTML = '<option value="ALL">All Time Slots</option>';
                    leaveTimeSelect.disabled = true;
                }
                if (leaveResultsContainer) {
                    leaveResultsContainer.innerHTML = `
                        <div class="leave-instructions">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-bottom: 10px; opacity: 0.6;">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Please select a day and time slot to search available proxy candidates.
                        </div>
                    `;
                }
            }

            const leaveDaySelect = document.getElementById("leaveDaySelect");
            const leaveTypeSelect = document.getElementById("leaveTypeSelect");
            const leaveTimeSelect = document.getElementById("leaveTimeSelect");
            const leaveResultsContainer = document.getElementById("leaveResultsContainer");

            let currentFilteredSlots = [];

            if (leaveDaySelect && leaveTypeSelect && leaveTimeSelect) {
                
                // Helper to render proxy results list
                function renderProxyResult(day, busySlots, faculty) {
                    if (busySlots.length === 0) {
                        leaveResultsContainer.innerHTML = `
                            <div class="leave-day-empty" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                🍵 No busy lectures scheduled for this period. No proxy required!
                            </div>
                        `;
                        return;
                    }

                    leaveResultsContainer.innerHTML = "";

                    const resultsStack = document.createElement("div");
                    resultsStack.style.display = "flex";
                    resultsStack.style.flexDirection = "column";
                    resultsStack.style.gap = "24px";

                    busySlots.forEach(slot => {
                        const times = getSlotMinutes(slot.time);
                        if (!times) return;

                        const sameDeptProxies = [];
                        const otherDeptProxies = [];

                        Object.keys(mappedTimetable).forEach(candInitials => {
                            // Exclude the absent faculty member themselves
                            if (candInitials === selectedInitials) return;

                            const candidate = mappedTimetable[candInitials];
                            let isFree = true;

                            // Check if candidate has an overlapping occupied lecture
                            candidate.schedule.forEach(candSlot => {
                                if (candSlot.isRecess) return;
                                if (candSlot[day] && candSlot[day].occupied === true) {
                                    const candTimes = getSlotMinutes(candSlot.time);
                                    if (candTimes) {
                                        if (candTimes.start < times.end && candTimes.end > times.start) {
                                            isFree = false;
                                        }
                                    }
                                }
                            });

                            // Determine candidate's shift and load
                            const candShift = getFacultyShift(candidate, day);
                            const candLoad = getFacultyLoad(candidate, day);

                            // Determine if candidate has a free day (0 occupied lectures on this day)
                            const candidateDayLectures = candidate.schedule.filter(s => !s.isRecess && s[day] && s[day].occupied === true).length;
                            const isFreeDay = (candidateDayLectures === 0);

                            // Check if candidate's shift covers the slot (always true if candidate has a Free Day)
                            // Allow Shift-2 candidates (id === 2) to cover slots up to 6:00 PM (1080 minutes)
                            const allowedShiftEnd = (candShift.id === 2) ? 1080 : candShift.end;
                            const inShift = isFreeDay || (times.start >= candShift.start && times.end <= allowedShiftEnd);

                            if (isFree && inShift) {
                                if (candidate.department === faculty.department || candidate.department === "Both" || faculty.department === "Both") {
                                    sameDeptProxies.push({ member: candidate, isFreeDay, load: candLoad, shift: candShift });
                                } else {
                                    otherDeptProxies.push({ member: candidate, isFreeDay, load: candLoad, shift: candShift });
                                }
                            }
                        });

                        // Sort proxies: non-overloaded (load < 4) first, then overloaded (load >= 4), then by initials
                        const sortProxies = (a, b) => {
                            const aOverload = a.load >= 4;
                            const bOverload = b.load >= 4;
                            if (aOverload !== bOverload) {
                                return aOverload ? 1 : -1;
                            }
                            return a.member.initials.localeCompare(b.member.initials);
                        };
                        sameDeptProxies.sort(sortProxies);
                        otherDeptProxies.sort(sortProxies);

                        let proxiesHtml = "";
                        if (sameDeptProxies.length === 0 && otherDeptProxies.length === 0) {
                            proxiesHtml = `<div class="leave-no-proxies" style="text-align: center; padding: 20px;">⚠️ No free faculty members found at this time.</div>`;
                        } else {
                            if (sameDeptProxies.length > 0) {
                                proxiesHtml += `
                                    <div class="proxy-group">
                                        <span class="proxy-group-label dept-same-label">${faculty.department === "Both" ? currentActiveDept : faculty.department} Department Candidates (${sameDeptProxies.length}):</span>
                                        <div class="proxy-badges">
                                            ${sameDeptProxies.map(p => {
                                                const isOverloaded = p.load >= 4;
                                                const overloadClass = isOverloaded ? "overload-warning" : "";
                                                const overloadBadge = isOverloaded ? ` <span class="badge-overload">⚠️ Overload (${p.load.toFixed(1)}h)</span>` : ` (${p.load.toFixed(1)}h)`;
                                                const shiftInfo = ` <span class="badge-shift">[${p.shift.name}]</span>`;
                                                return `
                                                    <span class="proxy-badge dept-same ${overloadClass}" title="${p.member.name} (${p.shift.name}, Load: ${p.load.toFixed(1)}h)">
                                                        <b>${p.member.initials}</b> ${p.member.name}${overloadBadge}${shiftInfo}${p.isFreeDay ? " 🌟 (Free Day)" : ""}
                                                    </span>
                                                `;
                                            }).join("")}
                                        </div>
                                    </div>
                                `;
                            }
                            if (otherDeptProxies.length > 0) {
                                const otherDeptName = (faculty.department === "Both" ? currentActiveDept : faculty.department) === "Information Technology" ? "Computer Engineering" : "Information Technology";
                                proxiesHtml += `
                                    <div class="proxy-group" style="margin-top: 14px;">
                                        <span class="proxy-group-label dept-other-label">${otherDeptName} Department Candidates (${otherDeptProxies.length}):</span>
                                        <div class="proxy-badges">
                                            ${otherDeptProxies.map(p => {
                                                const isOverloaded = p.load >= 4;
                                                const overloadClass = isOverloaded ? "overload-warning" : "";
                                                const overloadBadge = isOverloaded ? ` <span class="badge-overload">⚠️ Overload (${p.load.toFixed(1)}h)</span>` : ` (${p.load.toFixed(1)}h)`;
                                                const shiftInfo = ` <span class="badge-shift">[${p.shift.name}]</span>`;
                                                return `
                                                    <span class="proxy-badge dept-other ${overloadClass}" title="${p.member.name} (${p.shift.name}, Load: ${p.load.toFixed(1)}h)">
                                                        <b>${p.member.initials}</b> ${p.member.name}${overloadBadge}${shiftInfo}${p.isFreeDay ? " 🌟 (Free Day)" : ""}
                                                    </span>
                                                `;
                                            }).join("")}
                                        </div>
                                    </div>
                                `;
                            }
                        }

                        const slotRow = document.createElement("div");
                        slotRow.className = "leave-slot-row";
                        slotRow.style.borderBottom = "1px solid rgba(255, 255, 255, 0.08)";
                        slotRow.style.paddingBottom = "24px";

                        slotRow.innerHTML = `
                            <div class="leave-slot-info" style="flex-direction: row; justify-content: space-between; align-items: center; margin-bottom: 20px; background: rgba(var(--theme-color-rgb), 0.08); border-color: rgba(var(--theme-color-rgb), 0.2);">
                                <div>
                                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-family: 'Share Tech', monospace; letter-spacing: 1px;">Selected Lecture</div>
                                    <div class="leave-slot-subject">${slot[day].class}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="leave-slot-time">${slot.time}</div>
                                    <div class="leave-slot-room" style="font-weight: bold; color: var(--text-primary);">Room: ${slot[day].room || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="leave-slot-proxies">
                                ${proxiesHtml}
                            </div>
                        `;
                        resultsStack.appendChild(slotRow);
                    });

                    if (resultsStack.lastChild) {
                        resultsStack.lastChild.style.borderBottom = "none";
                        resultsStack.lastChild.style.paddingBottom = "0";
                    }

                    leaveResultsContainer.appendChild(resultsStack);
                }

                // Populate busy time slots based on Day and Leave Type
                function populateLeaveTimeSlots() {
                    const day = leaveDaySelect.value;
                    const leaveType = leaveTypeSelect.value;
                    const faculty = mappedTimetable[selectedInitials];

                    // Update selected faculty load badge
                    updateSelectedFacultyLoadBadge();

                    // Reset time select
                    leaveTimeSelect.innerHTML = '<option value="ALL">All Time Slots</option>';
                    leaveTimeSelect.disabled = true;

                    // Reset results container to default instructions
                    leaveResultsContainer.innerHTML = `
                        <div class="leave-instructions">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-bottom: 10px; opacity: 0.6;">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Please select a time slot to search available proxy candidates.
                        </div>
                    `;

                    if (!day || !faculty) {
                        leaveTypeSelect.disabled = true;
                        currentFilteredSlots = [];
                        return;
                    }

                    leaveTypeSelect.disabled = false;

                    // Find occupied lecture slots for this day
                    const rawBusySlots = faculty.schedule.filter(slot => {
                        return !slot.isRecess && slot[day] && slot[day].occupied === true;
                    });

                    // Merge consecutive identical slots (same class and room, with no break)
                    let busySlots = [];
                    for (let i = 0; i < rawBusySlots.length; i++) {
                        let currentSlot = JSON.parse(JSON.stringify(rawBusySlots[i]));
                        let currentTimes = getSlotMinutes(currentSlot.time);
                        if (!currentTimes) continue;

                        while (i + 1 < rawBusySlots.length) {
                            let nextSlot = rawBusySlots[i + 1];
                            let nextTimes = getSlotMinutes(nextSlot.time);
                            if (nextTimes && 
                                nextTimes.start === currentTimes.end && 
                                nextSlot[day].class === currentSlot[day].class && 
                                nextSlot[day].room === currentSlot[day].room) {
                                
                                const currentRange = parseTimeRangeStrings(currentSlot.time);
                                const nextRange = parseTimeRangeStrings(nextSlot.time);
                                if (currentRange && nextRange) {
                                    currentSlot.time = `${currentRange.startStr} to ${nextRange.endStr}`;
                                }
                                
                                currentTimes.end = nextTimes.end;
                                i++;
                            } else {
                                break;
                            }
                        }
                        busySlots.push(currentSlot);
                    }

                    // Filter based on Leave Type (threshold 1:00 PM = 780 minutes)
                    if (leaveType === "FIRST") {
                        busySlots = busySlots.filter(slot => {
                            const times = getSlotMinutes(slot.time);
                            return times && times.start < 780;
                        });
                    } else if (leaveType === "SECOND") {
                        busySlots = busySlots.filter(slot => {
                            const times = getSlotMinutes(slot.time);
                            return times && times.start >= 780;
                        });
                    }

                    currentFilteredSlots = busySlots;

                    if (busySlots.length === 0) {
                        const leaveTypeLabel = leaveType === "FIRST" ? "First Half" : (leaveType === "SECOND" ? "Second Half" : "Day");
                        leaveResultsContainer.innerHTML = `
                            <div class="leave-day-empty" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                🍵 No busy lectures scheduled for ${faculty.name} on this ${leaveTypeLabel} (Free). No proxy required!
                            </div>
                        `;
                    } else {
                        // Populate time slots dropdown
                        busySlots.forEach((slot) => {
                            const option = document.createElement("option");
                            option.value = JSON.stringify(slot);
                            option.textContent = `${slot.time} (${slot[day].class})`;
                            leaveTimeSelect.appendChild(option);
                        });
                        leaveTimeSelect.disabled = false;

                        // Automatically render proxies for ALL slots by default!
                        renderProxyResult(day, busySlots, faculty);
                    }
                }

                // Day selector change listener
                leaveDaySelect.addEventListener("change", populateLeaveTimeSlots);

                // Leave type change listener
                leaveTypeSelect.addEventListener("change", populateLeaveTimeSlots);

                // Time selector change listener
                leaveTimeSelect.addEventListener("change", () => {
                    const timeVal = leaveTimeSelect.value;
                    const day = leaveDaySelect.value;
                    const faculty = mappedTimetable[selectedInitials];

                    if (!day || !faculty) return;

                    if (timeVal === "ALL") {
                        renderProxyResult(day, currentFilteredSlots, faculty);
                    } else {
                        const slot = JSON.parse(timeVal);
                        renderProxyResult(day, [slot], faculty);
                    }
                });
            }

            // Department Toggles
            itBtn.addEventListener("click", () => setDepartmentTheme("Information Technology"));
            ceBtn.addEventListener("click", () => setDepartmentTheme("Computer Engineering"));

            // Leave Toggle button inside the faculty details card
            const leaveToggleBtn = document.getElementById("leaveToggleBtn");
            if (leaveToggleBtn) {
                leaveToggleBtn.addEventListener("click", () => toggleLeaveMode());
            }

            // By default, always show Information Technology department on initial load
            const startingDept = "Information Technology";
            selectedInitials = "SBC"; // default IT
            setDepartmentTheme(startingDept);
        });
    </script>

</body>

</html>
