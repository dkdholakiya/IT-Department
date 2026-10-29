<?php
$active_page = 'workload-summary';
$excelFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/uploads/timetable/timetable.xlsx';
if (!file_exists($excelFile)) {
    $glob = glob((defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/uploads/timetable/*.xlsx');
    if (!empty($glob)) $excelFile = $glob[0];
}
$excelExists = file_exists($excelFile);
$jsDataFile = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 3)) . '/assets/js/timetableData.js';
$jsDataExists = file_exists($jsDataFile);
$page_title = 'Faculty Workload Summary — CE & IT Department';
$page_description = 'Faculty Workload & Timetable Audit Summary — Department of CE & IT';
$page_css = [
    'assets/css/portal.css',
    'assets/css/faculty.css',
    'assets/css/timetable.css',
    'assets/css/workload.css',
];
include 'head.php';
?>
    <!-- Background Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>

    <!-- Header -->
    <header class="rp-header">
        <div class="rp-header-inner container">
            <a href="<?php echo base_url('timetable'); ?>" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Timetable
            </a>

            <div class="rp-header-center">
                <div class="rp-dept-badge">
                    <span class="rp-badge-dot"></span>
                    <span>Department of CE & IT — Faculty Workload Audit</span>
                </div>
                <h1 class="rp-title">Faculty Workload Audit & Total Load Summary</h1>
            </div>

            <span class="portal-badge">Workload Audit</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="summary-container container">

        <?php if (!$excelExists || !$jsDataExists): ?>
            <div class="controls-card" style="text-align: center; padding: 60px 24px;">
                <div style="font-size: 54px;">⚠️</div>
                <h2 style="color: #fbbf24; font-size: 22px; margin-top: 15px;">Database Sync Needed</h2>
                <p style="color: #94a3b8; font-size: 14.5px; margin: 10px 0 20px 0;">Please run the update script to compile the latest faculty timetable data.</p>
                <a href="<?php echo base_url('update-timetable'); ?>" class="back-btn" style="text-decoration: none;">Run Timetable Sync</a>
            </div>
        <?php else: ?>

            <!-- Printable Header (Visible only when printing) -->
            <div class="print-only-header">
                <h1>Department of CE & IT — Faculty Workload Summary</h1>
                <p>Official Weekly Teaching Load & Schedule Breakdown Report | Printed: <?php echo date('d M Y'); ?></p>
            </div>

            <!-- Stats Overview Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalFacultyCount">0</div>
                        <div class="stat-label">Faculty Members Loaded</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalTeachingHours">0 Hrs</div>
                        <div class="stat-label">Total Department Workload</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <div class="stat-value" id="avgWorkload">0 Hrs</div>
                        <div class="stat-label">Average Load / Faculty</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="maxWorkload">0 Hrs</div>
                        <div class="stat-label">Peak Workload Recorded</div>
                    </div>
                </div>
            </div>

            <!-- Controls & Filters Bar -->
            <div class="summary-controls">
                <div class="summary-search">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="summarySearchInput" placeholder="Search by faculty name or initials (e.g. APG, Remona)..." autocomplete="off">
                </div>

                <div class="summary-filters">
                    <button type="button" class="filter-btn active" data-filter="ALL">All Departments</button>
                    <button type="button" class="filter-btn" data-filter="Information Technology">IT Dept</button>
                    <button type="button" class="filter-btn" data-filter="Computer Engineering">CE Dept</button>

                    <select id="sortSelect" class="sort-select">
                        <option value="load-desc">Sort by Load (High to Low)</option>
                        <option value="load-asc">Sort by Load (Low to High)</option>
                        <option value="name-asc">Sort by Name (A to Z)</option>
                        <option value="initials-asc">Sort by Initials</option>
                    </select>

                    <button type="button" class="print-btn" onclick="window.print()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                            <path d="M6 14h12v8H6z" />
                        </svg>
                        Print Summary
                    </button>
                </div>
            </div>

            <!-- Main Workload Table Card -->
            <div class="summary-table-card">
                <div class="summary-table-header">
                    <div class="summary-table-title">
                        <span>📅</span> Faculty Teaching Load & Schedule Verification
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted, #94a3b8);" id="showingCount">
                        Showing 0 members
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">#</th>
                                <th>Faculty Member</th>
                                <th>Department</th>
                                <th>Daily Schedule Breakdown</th>
                                <th style="text-align: center;">Total Load</th>
                                <th style="text-align: center;">TT Match</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </main>

    <!-- Include Data Files -->
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>
    <script src="<?php echo v_asset('assets/js/timetableData.js'); ?>"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof timetableData === "undefined" || typeof facultyData === "undefined") return;

            let allRows = [];
            let currentFilter = "ALL";
            let currentSearch = "";
            let currentSort = "load-desc";

            // Process data for faculty members in timetableData that exist in facultyData.js
            Object.keys(timetableData).forEach(initials => {
                const info = timetableData[initials];
                let facultyObj = facultyData.find(f => f.initials && f.initials.toUpperCase() === initials.toUpperCase());
                if (!facultyObj) return;

                let name = facultyObj.name;
                let dept = facultyObj.department;
                let designation = facultyObj.designation || "Lecturer";
                let avatarClass = facultyObj.avatarClass || `av-${initials.toLowerCase()}`;

                let daily = { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 };
                
                if (info.schedule && Array.isArray(info.schedule)) {
                    info.schedule.forEach(slot => {
                        if (slot.isRecess) return;
                        ["MON", "TUE", "WED", "THU", "FRI", "SAT"].forEach(day => {
                            if (slot[day] && slot[day].occupied) {
                                daily[day]++;
                            }
                        });
                    });
                }

                let totalLoad = Object.values(daily).reduce((a, b) => a + b, 0);

                allRows.push({
                    initials: initials,
                    name: name,
                    department: dept,
                    designation: designation,
                    avatarClass: avatarClass,
                    daily: daily,
                    totalLoad: totalLoad
                });
            });

            // Add any faculty from facultyData that might not have a timetable tab yet
            facultyData.forEach(f => {
                // Exclude non-teaching administrative/developer roles (Developer, Admin)
                if (f.designation && (
                    f.designation.toLowerCase().includes("developer") ||
                    f.designation.toLowerCase().includes("admin")
                )) return;

                if (!allRows.some(r => r.initials.toUpperCase() === f.initials.toUpperCase())) {
                    allRows.push({
                        initials: f.initials,
                        name: f.name,
                        department: f.department,
                        designation: f.designation,
                        avatarClass: f.avatarClass,
                        daily: { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 },
                        totalLoad: 0
                    });
                }
            });

            // Update stats
            function updateStats(rows) {
                const totalFac = rows.length;
                const totalHours = rows.reduce((sum, r) => sum + r.totalLoad, 0);
                const avgHours = totalFac > 0 ? (totalHours / totalFac).toFixed(1) : "0.0";
                const maxHours = rows.length > 0 ? Math.max(...rows.map(r => r.totalLoad)) : 0;

                document.getElementById("totalFacultyCount").textContent = totalFac;
                document.getElementById("totalTeachingHours").textContent = `${totalHours} Hrs`;
                document.getElementById("avgWorkload").textContent = `${avgHours} Hrs`;
                document.getElementById("maxWorkload").textContent = `${maxHours} Hrs`;
            }

            // Render table
            function renderTable() {
                let filtered = allRows.filter(r => {
                    // Exclude non-teaching administrative & developer entries (Developer, Admin)
                    if (r.designation && (
                        r.designation.toLowerCase().includes("developer") ||
                        r.designation.toLowerCase().includes("admin")
                    )) return false;

                    let matchesFilter = (currentFilter === "ALL") || (r.department === currentFilter) || (r.department === "Both");
                    let query = currentSearch.toLowerCase().trim();
                    let matchesSearch = !query || 
                        r.name.toLowerCase().includes(query) || 
                        r.initials.toLowerCase().includes(query) || 
                        r.department.toLowerCase().includes(query) ||
                        r.designation.toLowerCase().includes(query);
                    return matchesFilter && matchesSearch;
                });

                // Sorting
                filtered.sort((a, b) => {
                    if (currentSort === "load-desc") return b.totalLoad - a.totalLoad;
                    if (currentSort === "load-asc") return a.totalLoad - b.totalLoad;
                    if (currentSort === "name-asc") return a.name.localeCompare(b.name);
                    if (currentSort === "initials-asc") return a.initials.localeCompare(b.initials);
                    return 0;
                });

                updateStats(filtered);

                const tbody = document.getElementById("summaryTableBody");
                document.getElementById("showingCount").textContent = `Showing ${filtered.length} of ${allRows.length} members`;

                if (filtered.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted, #94a3b8);">
                                🔍 No faculty members found matching your filter criteria.
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = "";
                filtered.forEach((r, idx) => {
                    let deptClass = "it";
                    if (r.department === "Computer Engineering") deptClass = "ce";
                    else if (r.department === "Both") deptClass = "both";

                    let loadBadgeClass = "normal";
                    if (r.totalLoad >= 20) loadBadgeClass = "high";
                    else if (r.totalLoad <= 10) loadBadgeClass = "low";

                    let daysHtml = ["MON", "TUE", "WED", "THU", "FRI", "SAT"].map(day => {
                        let count = r.daily[day];
                        let activeClass = count > 0 ? "active" : "";
                        return `<span class="day-pill ${activeClass}" title="${day}: ${count} Hrs">${count}</span>`;
                    }).join(" ");

                    html += `
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-muted, #94a3b8);">${idx + 1}</td>
                            <td>
                                <div class="fac-cell">
                                    <div class="fac-avatar-sm">${r.initials}</div>
                                    <div>
                                        <div class="fac-name-text">${r.name}</div>
                                        <div class="fac-sub-text">${r.designation} (${r.initials})</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="dept-tag ${deptClass}">${r.department}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    ${daysHtml}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="load-badge-main ${loadBadgeClass}">
                                    ${r.totalLoad} Hrs
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; ${r.totalLoad > 0 ? 'background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);' : 'background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4);'}">
                                    ${r.totalLoad > 0 ? '✓ Matched' : '⚠️ No Schedule'}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?php echo base_url('timetable'); ?>?initials=${encodeURIComponent(r.initials)}" class="view-btn">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Grid
                                </a>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            }

            // Event Listeners
            document.querySelectorAll(".filter-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
                    this.classList.add("active");
                    currentFilter = this.getAttribute("data-filter");
                    renderTable();
                });
            });

            document.getElementById("summarySearchInput").addEventListener("input", function() {
                currentSearch = this.value;
                renderTable();
            });

            document.getElementById("sortSelect").addEventListener("change", function() {
                currentSort = this.value;
                renderTable();
            });

            // Initial Render
            renderTable();
        });
    </script>
    <?php include 'fab-nav.php'; ?>
</body>

</html>
