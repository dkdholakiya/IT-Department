<?php
require_once 'auto-cache-bust.php';
$active_page = 'syllabus';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Syllabus Check — Fetch, verify, and view official GMIU course subject codes and credit tables in real time.">
    <title>Syllabus Check — CE &amp; IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon"          href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="dns-prefetch"  href="//fonts.googleapis.com">
    <link rel="dns-prefetch"  href="//fonts.gstatic.com">
    <link rel="preconnect"    href="https://fonts.googleapis.com">
    <link rel="preconnect"    href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&family=Kameron:wght@400..700&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Outfit:wght@400;600;700&family=Playfair+Display:ital,wght@0,700..900;1,700..900&family=Share+Tech&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Portal Design System -->
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/syllabus.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">
</head>

<body>

<div class="syl-page">

    <!-- ░░ Particles ░░ -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
        <div class="particle"></div><div class="particle"></div>
    </div>

    <!-- Glowing Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>
    <div class="orb orb-3" aria-hidden="true"></div>

    <!-- ════════════════════════════════════════════
         PAGE HEADER  (identical structure to faculty.php)
         ════════════════════════════════════════════ -->
    <header class="rp-header">
        <div class="rp-header-inner">

            <!-- Left: Back Button -->
            <a href="./" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to Portal
            </a>

            <!-- Centre: Title -->
            <div class="rp-header-center">
                <div class="rp-dept-badge">
                    <span class="rp-badge-dot"></span>
                    <span>Department of Information Technology</span>
                </div>
                <h1 class="rp-title">Syllabus Check</h1>
            </div>

            <!-- Right: Export button + badge -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnExport" class="syl-header-btn hidden" title="Export syllabus data as CSV">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Export CSV</span>
                </button>
                <span class="portal-badge">Live Scraper</span>
            </div>

        </div>
    </header>

    <!-- ════════════════════════════════════════════
         DEPARTMENT FILTER BAR  (hidden — replaced by dropdown)
         ════════════════════════════════════════════ -->
    <div class="syl-filter-wrap" style="display:none;" aria-hidden="true">
        <div class="zs-segment-control" id="deptFilter" role="group" aria-label="Department filter"></div>
    </div>

    <!-- ════════════════════════════════════════════
         URL INPUT SECTION
         ════════════════════════════════════════════ -->
    <section class="syl-input-section">
        <div class="syl-input-card">

            <!-- Card Header: Title left · Dropdown right -->
            <div class="syl-card-head-row">
                <div class="syl-card-head-text">
                    <h2 class="syl-input-card-title">Check Course Syllabus Tables</h2>
                    <p class="syl-input-card-desc">Choose a department or paste any GMIU student-corner URL to fetch, verify, and parse official subject codes and credit tables in real time.</p>
                </div>

                <!-- Department Dropdown -->
                <div class="syl-dept-select-wrap">
                    <label for="deptSelect" class="syl-dept-select-label">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                        Department
                    </label>
                    <div class="syl-select-box">
                        <select id="deptSelect" class="syl-dept-select" aria-label="Select department">
                            <optgroup label="── Under-Graduation (UG) ──">
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-engineering/student-corner" selected>
                                    Computer Engineering (UG)
                                </option>
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-science-and-engineering/student-corner">
                                    Computer Science &amp; Engineering (UG)
                                </option>
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-information-technology/student-corner">
                                    Information Technology (UG)
                                </option>
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-information-technology/student-corner">
                                    Info &amp; Communication Engineering (UG)
                                </option>
                            </optgroup>
                            <optgroup label="── Diploma ──">
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-computer-engineering/student-corner">
                                    Computer Engineering (Diploma)
                                </option>
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-information-technology/student-corner">
                                    Information Technology (Diploma)
                                </option>
                                <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-computer-science-and-engineering/student-corner">
                                    Computer Science &amp; Engineering (Diploma)
                                </option>
                            </optgroup>
                        </select>
                        <svg class="syl-select-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            <form id="scrapeForm" class="syl-form-row">
                <div class="syl-url-wrap">
                    <svg class="syl-url-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                    <input
                        type="url"
                        id="urlInput"
                        class="syl-url-input"
                        placeholder="https://gmiu.edu.in/gmiu/website/faculty/..."
                        value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-engineering/student-corner"
                        required
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <button type="button" id="btnClear" class="syl-url-clear" title="Clear" aria-label="Clear input">&times;</button>
                </div>

                <button type="submit" id="btnSubmit" class="syl-submit-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                    </svg>
                    Fetch Syllabus
                </button>
            </form>
        </div>
    </section>

    <!-- ════════════════════════════════════════════
         LOADING STATE
         ════════════════════════════════════════════ -->
    <div id="loadingBox" class="syl-loading hidden" role="status" aria-live="polite">
        <div class="syl-spinner" aria-hidden="true"></div>
        <div class="syl-loading-title">Fetching &amp; Parsing Syllabus Tables…</div>
        <div class="syl-loading-desc">Connecting to GMIU, reading HTML DOM, extracting subject codes and credits.</div>
    </div>

    <!-- ════════════════════════════════════════════
         ERROR ALERT
         ════════════════════════════════════════════ -->
    <div id="alertBox" class="syl-alert hidden" role="alert">
        <div class="syl-alert-icon" aria-hidden="true">⚠️</div>
        <div>
            <div id="alertTitle" class="syl-alert-title">Scan Error</div>
            <p  id="alertMsg"   class="syl-alert-msg">Unable to process the target URL.</p>
        </div>
    </div>

    <!-- ════════════════════════════════════════════
         RESULTS — Stats + Semester Tabs + Table
         ════════════════════════════════════════════ -->
    <section id="resultsSection" class="hidden" style="width:100%; max-width:1320px; margin:0 auto;">

        <!-- Stats Grid -->
        <div class="syl-stats-section">
            <div class="syl-stats-grid">
                <div class="stat-card">
                    <div id="statSem"  class="stat-card-val">0</div>
                    <div class="stat-card-lbl">Semesters Found</div>
                </div>
                <div class="stat-card">
                    <div id="statSub"  class="stat-card-val">0</div>
                    <div class="stat-card-lbl">Total Subjects</div>
                </div>
                <div class="stat-card">
                    <div id="statCred" class="stat-card-val">0</div>
                    <div class="stat-card-lbl">Total Credits</div>
                </div>
                <div>
                    <button type="button" id="btnExportGrid" class="syl-export-btn" title="Download as CSV">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Semester Tabs -->
        <div class="syl-sem-section">
            <div class="syl-sem-tabs" id="semTabs" role="tablist"></div>
        </div>

        <!-- Data Table Card -->
        <div class="syl-table-section">
            <div class="syl-table-card">
                <div class="syl-table-card-head">
                    <h3 id="activeSemTitle" class="syl-table-card-title">Semester 1</h3>
                    <span id="activeCountBadge" class="syl-count-badge">0 Subjects</span>
                </div>
                <div class="syl-table-scroll">
                    <table class="syl-table" role="grid">
                        <thead>
                            <tr>
                                <th scope="col">Subject Code</th>
                                <th scope="col">Subject Title / Name</th>
                                <th scope="col">Short Name</th>
                                <th scope="col">L</th>
                                <th scope="col">T</th>
                                <th scope="col">P</th>
                                <th scope="col">Credit</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>

    <!-- Portal Footer -->
    <?php include 'footer.php'; ?>

</div><!-- /.syl-page -->

<!-- FAB Navigation -->
<?php include 'fab-nav.php'; ?>

<!-- Bootstrap 5 JS -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>

<script>
(function () {
    'use strict';

    // ─── DOM References ───
    const form         = document.getElementById('scrapeForm');
    const urlInput     = document.getElementById('urlInput');
    const btnClear     = document.getElementById('btnClear');
    const btnSubmit    = document.getElementById('btnSubmit');
    const loadingBox   = document.getElementById('loadingBox');
    const alertBox     = document.getElementById('alertBox');
    const alertTitle   = document.getElementById('alertTitle');
    const alertMsg     = document.getElementById('alertMsg');
    const resultsSection = document.getElementById('resultsSection');

    const statSem  = document.getElementById('statSem');
    const statSub  = document.getElementById('statSub');
    const statCred = document.getElementById('statCred');

    const semTabs         = document.getElementById('semTabs');
    const activeSemTitle  = document.getElementById('activeSemTitle');
    const activeCountBadge = document.getElementById('activeCountBadge');
    const tableBody       = document.getElementById('tableBody');

    const btnExport     = document.getElementById('btnExport');
    const btnExportGrid = document.getElementById('btnExportGrid');

    let parsedData = null;

    // ─── Clear Button ───
    btnClear.addEventListener('click', () => {
        urlInput.value = '';
        urlInput.focus();
    });

    // ─── Department Filter Buttons (legacy, now hidden) ───
    document.querySelectorAll('#deptFilter .segment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#deptFilter .segment-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            urlInput.value = btn.getAttribute('data-url');
            form.dispatchEvent(new Event('submit', { bubbles: true }));
        });
    });

    // ─── Department Dropdown ───
    const deptSelect = document.getElementById('deptSelect');
    if (deptSelect) {
        deptSelect.addEventListener('change', () => {
            const url = deptSelect.value;
            if (!url) return;
            urlInput.value = url;
            form.dispatchEvent(new Event('submit', { bubbles: true }));
        });
    }

    // ─── Form Submit ───
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const url = urlInput.value.trim();
        if (!url) return;

        // Reset UI
        alertBox.classList.add('hidden');
        resultsSection.classList.add('hidden');
        btnExport.classList.add('hidden');
        loadingBox.classList.remove('hidden');
        btnSubmit.disabled = true;

        try {
            const res    = await fetch('scrape.php?url=' + encodeURIComponent(url));
            const result = await res.json();

            loadingBox.classList.add('hidden');
            btnSubmit.disabled = false;

            if (!result.success) {
                showAlert('Syllabus Fetch Failed', result.error || 'Unknown server error occurred.');
                return;
            }

            if (!result.data || Object.keys(result.data).length === 0) {
                showAlert('No Syllabus Tables Found', 'No tables containing "SUBJECT CODE" were detected on the target page. Try a different URL.');
                return;
            }

            parsedData = result.data;
            renderResults(result);

        } catch (err) {
            loadingBox.classList.add('hidden');
            btnSubmit.disabled = false;
            showAlert('Connection Error', 'Failed to reach the scrape API: ' + err.message);
        }
    });

    // ─── Render Results ───
    function renderResults(result) {
        const sems = Object.keys(parsedData);

        // Stats
        statSem.textContent  = sems.length;
        statSub.textContent  = result.totalSubjects || 0;

        let totalCredits = 0;
        sems.forEach(s => parsedData[s].forEach(row => {
            const v = parseFloat(row.credit);
            if (!isNaN(v)) totalCredits += v;
        }));
        statCred.textContent = totalCredits;

        // Semester Tabs
        semTabs.innerHTML = '';
        sems.forEach((sem, i) => {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'syl-sem-tab' + (i === 0 ? ' active' : '');
            btn.textContent = sem;
            btn.setAttribute('role', 'tab');
            btn.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
            btn.addEventListener('click', () => {
                document.querySelectorAll('.syl-sem-tab').forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
                renderTable(sem);
            });
            semTabs.appendChild(btn);
        });

        if (sems.length) renderTable(sems[0]);

        resultsSection.classList.remove('hidden');
        btnExport.classList.remove('hidden');
    }

    // ─── Render Table ───
    function renderTable(semName) {
        activeSemTitle.textContent = semName;
        const rows = parsedData[semName] || [];
        activeCountBadge.textContent = rows.length + (rows.length === 1 ? ' Subject' : ' Subjects');

        tableBody.innerHTML = '';

        if (!rows.length) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="7" style="text-align:center; color:var(--text-muted); padding:32px;">No subject records found for this semester.</td>';
            tableBody.appendChild(tr);
            return;
        }

        rows.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td><span class="code-pill">' + esc(row.code) + '</span></td>' +
                '<td><span class="subj-name">'  + esc(row.name) + '</span></td>' +
                '<td><span class="subj-short">' + esc(row.short) + '</span></td>' +
                '<td>' + esc(row.l) + '</td>' +
                '<td>' + esc(row.t) + '</td>' +
                '<td>' + esc(row.p) + '</td>' +
                '<td><span class="credit-num">' + esc(row.credit) + '</span></td>';
            tableBody.appendChild(tr);
        });
    }

    // ─── Export CSV ───
    function exportCSV() {
        if (!parsedData) return;
        let csv = 'data:text/csv;charset=utf-8,Semester,Subject Code,Subject Name,Short Name,L,T,P,Credit\n';
        Object.keys(parsedData).forEach(sem => {
            parsedData[sem].forEach(row => {
                csv += [
                    '"' + sem + '"',
                    '"' + row.code + '"',
                    '"' + row.name.replace(/"/g, '""') + '"',
                    '"' + row.short + '"',
                    row.l, row.t, row.p, row.credit
                ].join(',') + '\n';
            });
        });
        const a = document.createElement('a');
        a.href = encodeURI(csv);
        a.download = 'GMIU_Syllabus.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    btnExport.addEventListener('click', exportCSV);
    btnExportGrid.addEventListener('click', exportCSV);

    // ─── Helpers ───
    function showAlert(title, msg) {
        alertTitle.textContent = title;
        alertMsg.textContent   = msg;
        alertBox.classList.remove('hidden');
    }

    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

})();
</script>

</body>
</html>
