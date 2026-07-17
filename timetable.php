<?php
$excelFile = __DIR__ . '/assets/uploads/Personal Time Table CE_CSE _ IT _ ICT.xlsx';
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
    <link rel="stylesheet" href="assets/css/timetable.css?v=1">
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
                    The schedule Excel file is missing from the uploads folder. Please place the timetable Excel sheet inside the <code>assets/uploads/</code> directory.
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
                        <div class="faculty-semester-info" id="facSemesterInfo"></div>
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

            // Sync layout themes
            function setDepartmentTheme(dept) {
                currentActiveDept = dept;
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
                const keys = Object.keys(mappedTimetable);
                const match = keys.find(k => mappedTimetable[k].department === dept);
                if (match) {
                    loadTimetable(match);
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
                    // Show only faculty in active department selection
                    if (data.department !== currentActiveDept) return;

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
                facDeptBadge.innerText = `Department of ${faculty.department}`;
                
                const facDesignation = document.getElementById("facDesignation");
                if (facDesignation) {
                    facDesignation.innerText = faculty.designation || '';
                }
                const facSemesterInfo = document.getElementById("facSemesterInfo");
                if (facSemesterInfo) {
                    facSemesterInfo.innerText = faculty.semesterInfo || '';
                }

                // Update document title dynamically
                document.title = `Faculty Timetable (${initials}) — ${faculty.department} Department`;

                // Update highlight state in options list
                const options = optionsListContainer.querySelectorAll(".select-option");
                options.forEach(opt => {
                    if (opt.getAttribute("data-initials") === initials) {
                        opt.classList.add("selected");
                    } else {
                        opt.classList.remove("selected");
                    }
                });

                // Render Table rows
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

                // Helper to parse time string like "12:15" or "01:00 PM" into minutes from midnight
                function parseTimeToMinutes(timeStr) {
                    if (!timeStr) return null;
                    timeStr = timeStr.trim().toLowerCase();
                    
                    let isPM = timeStr.includes('pm');
                    let isAM = timeStr.includes('am');
                    
                    let clean = timeStr.replace(/[^0-9:]/g, '');
                    let parts = clean.split(':');
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

                // Helper to parse slot time range into start and end minutes
                function getSlotMinutes(timeStr) {
                    if (!timeStr) return null;
                    const parts = timeStr.toLowerCase().split(/(?:to|\s|-)+/);
                    const times = [];
                    parts.forEach(p => {
                        const clean = p.trim();
                        if (clean.match(/\d{1,2}:\d{2}/)) {
                            times.push(clean);
                        }
                    });
                    if (times.length >= 2) {
                        const startMin = parseTimeToMinutes(times[0]);
                        const endMin = parseTimeToMinutes(times[1]);
                        if (startMin !== null && endMin !== null) {
                            return { start: startMin, end: endMin };
                        }
                    }
                    return null;
                }

                // 2. Scan and merge identical consecutive slots for each day dynamically
                for (let d = 0; d < 6; d++) {
                    let mergeStartRow = -1;
                    let lastEndMin = -1;
                    
                    for (let r = 0; r < rowCount; r++) {
                        const slot = faculty.schedule[r];
                        if (slot.isRecess) {
                            // Recess breaks any consecutive merge
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
                            if (cell.skip) continue; // Skip rendering merged cell

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

            // Department Toggles
            itBtn.addEventListener("click", () => setDepartmentTheme("Information Technology"));
            ceBtn.addEventListener("click", () => setDepartmentTheme("Computer Engineering"));

            // Read starting state from local storage or default to IT
            const startingDept = localStorage.getItem("portal_dept") === "CE" ? "Computer Engineering" : "Information Technology";
            // Make sure the active initials matches the starting department
            if (startingDept === "Computer Engineering") {
                selectedInitials = "EHU"; // default CE
            } else {
                selectedInitials = "SBC"; // default IT
            }
            setDepartmentTheme(startingDept);
        });
    </script>

</body>

</html>
