<?php include 'auth-check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU IT Department — Google Drive Folder Scanner & Excel Reporter utility for academic event files.">
    <title>Drive Folder Scanner — GMIU IT Department</title>

    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Scoped Utility Stylesheet -->
    <link rel="stylesheet" href="assets/css/ctldrive.css">
</head>

<body>

    <!-- Background particles -->
    <div class="rp-particles" aria-hidden="true">
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
    </div>

    <!-- Glow orbs -->
    <div class="rp-orb rp-orb-1" aria-hidden="true"></div>
    <div class="rp-orb rp-orb-2" aria-hidden="true"></div>

    <div class="rp-page">

        <!-- ── Page Header ── -->
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
                    <h1 class="rp-title">Drive Folder Scanner</h1>
                    <p class="rp-subtitle">Gyanmanjari Innovative University &nbsp;·&nbsp; Excel Reporter</p>
                </div>

                <span class="portal-badge">IT Drive Tool</span>
            </div>
        </header>

        <!-- ── Main Form Card ── -->
        <main class="rp-main">
            <div class="rp-form">
                <div class="rp-form-header">
                    <h2>Google Drive Scanner</h2>
                    <p>Scan a parent folder, list all empty and non-empty subfolders along with their documents, and
                        export the analysis to an `.xlsx` Excel sheet.</p>
                </div>

                <!-- Flex Grid Layout -->
                <div class="scanner-grid">

                    <!-- Left Column: Controls -->
                    <div class="scanner-col-left">
                        <div style="margin-bottom: 20px;">
                            <label class="scanner-label">1. Connection Status</label>
                            <div class="controls">
                                <button id="auth-btn" onclick="handleAuthClick()">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M12 11c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z" />
                                        <path d="M18 11c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z" />
                                        <path d="M6 11c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z" />
                                    </svg>
                                    Authorize & Connect
                                </button>
                                <button id="signout-btn" onclick="handleSignoutClick()" style="display: none;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <polyline points="16 17 21 12 16 7" />
                                        <line x1="21" y1="12" x2="9" y2="12" />
                                    </svg>
                                    Sign Out
                                </button>
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="scanner-label">2. Target Directory</label>
                            <div id="scan-container" class="controls"
                                style="display: none; flex-direction: column; align-items: stretch; gap: 12px; width: 100%;">
                                <input type="text" id="folder-id-input" placeholder="Enter Parent Folder ID or Link..."
                                    value="1zbvK-y8MMVOfE6KIRikRstae2LN0iGFq">
                                <button id="scan-btn" onclick="startScan()" style="width: 100%;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" />
                                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    </svg>
                                    Scan Folders
                                </button>
                            </div>
                        </div>

                        <!-- 3. Faculty Info (Email Report) -->
                        <div id="email-section" style="margin-bottom: 20px; display: none;">
                            <label class="scanner-label">3. Email Excel Report</label>
                            <div class="controls"
                                style="flex-direction: column; align-items: stretch; gap: 12px; width: 100%; margin-bottom: 0;">
                                <div class="form-group" style="position: relative; width: 100%; margin-bottom: 14px;">
                                    <label for="facultySearch" style="margin-bottom: 4px;">Faculty Name <span
                                            class="req">*</span></label>
                                    <div class="search-select-wrap">
                                        <input type="text" id="facultySearch"
                                            placeholder="Type to search faculty name..." autocomplete="off">
                                        <input type="hidden" id="preparedBy" name="preparedBy" required>
                                        <div class="search-dropdown-list" id="facultyDropdownList"></div>
                                        <svg class="search-icon" width="16" height="16" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="8" />
                                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                        </svg>
                                    </div>
                                    <div id="facultyError" class="validation-error hidden">Please select a faculty
                                        member from the dropdown.</div>
                                </div>

                                <div class="form-row" style="width: 100%; margin-bottom: 14px; gap: 12px;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="facultyEmail" style="margin-bottom: 4px;">Email Address</label>
                                        <input type="text" id="facultyEmail" placeholder="Auto-filled..." readonly>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="facultyPhone" style="margin-bottom: 4px;">Mobile Number</label>
                                        <input type="text" id="facultyPhone" placeholder="Auto-filled..." readonly>
                                    </div>
                                </div>

                                <div class="form-group" style="position: relative; width: 100%; margin-bottom: 20px;">
                                    <label for="ccSearch" style="margin-bottom: 4px;">CC Emails (Multi-select)</label>
                                    <div class="cc-select-wrap">
                                        <div class="cc-tags-container" id="ccTagsContainer"></div>
                                        <input type="text" id="ccSearch"
                                            placeholder="Type or click to select CC emails..." autocomplete="off">
                                        <div class="search-dropdown-list" id="ccDropdownList"></div>
                                    </div>
                                </div>

                                <button type="button" class="submit-btn" id="sendBtn"
                                    style="width: 100%; gap: 8px; margin: 0;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path d="M22 2L11 13" />
                                        <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                                    </svg>
                                    <span>Send Report via Email</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="scanner-label">Console Logs</label>
                            <div id="status" class="status">Please authorize to begin.</div>
                        </div>
                    </div>

                    <!-- Right Column: Results -->
                    <div class="scanner-col-right">
                        <div class="section-title">
                            <h2>Scan Results</h2>
                            <button id="download-btn" onclick="downloadExcelReport()">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                                    viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Download Excel Report
                            </button>
                        </div>

                        <!-- Scan Summary Card -->
                        <div id="summary-card" class="summary-card" style="display: none;">
                            <div class="summary-stat-item">
                                <span class="summary-stat-label">Folders Scanned</span>
                                <span class="summary-stat-value" id="total-folders-val">0</span>
                            </div>
                            <div class="summary-stat-item">
                                <span class="summary-stat-label">Total Documents</span>
                                <span class="summary-stat-value" id="total-docs-val">0</span>
                            </div>
                            <div class="summary-stat-item">
                                <span class="summary-stat-label">Non-Empty Folders</span>
                                <span class="summary-stat-value" id="non-empty-folders-val">0</span>
                            </div>
                            <div class="summary-stat-item">
                                <span class="summary-stat-label">Empty Folders</span>
                                <span class="summary-stat-value" id="empty-folders-val">0</span>
                            </div>
                        </div>

                        <div class="results-scroll-container">
                            <h3 class="results-header empty-title">Empty Folders</h3>
                            <ul id="empty-folders" class="folder-list"></ul>

                            <h3 class="results-header non-empty-title">Non-Empty Folders</h3>
                            <ul id="non-empty-folders" class="folder-list"></ul>
                        </div>
                    </div>

                </div><!-- /scanner-grid -->
            </div>
        </main>

        <!-- Footer -->
        <footer class="rp-footer">
            <p>&copy; 2026 Department of Information Technology, GMIU &nbsp;·&nbsp; Designed with <span
                    style="color:#f87171;">♥</span> by Dev Dholakiya</p>
        </footer>

    </div><!-- /rp-page -->

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
            <a href="faculty.php" class="fab-link" id="nav-faculty">
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
            <a href="ctldrive.php" class="fab-link active" id="nav-drive">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Drive Scanner
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

    <script src="assets/vendor/xlsx/xlsx.full.min.js"></script>

    <!-- Faculty Data -->
    <script src="assets/js/facultyData.js"></script>

    <!-- Google APIs -->
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>

    <script>
        // Clear the session on load so that refresh triggers password re-prompt
        window.addEventListener('load', () => {
            fetch('verify-password.php?action=clear');
        });

        // -------------------------------------------------------------
        // ENTER YOUR CONFIGURATION HERE
        // -------------------------------------------------------------
        const CLIENT_ID = '790619253351-7811pmcbuii0eq52ugkb6o9109q86t48.apps.googleusercontent.com';
        const API_KEY = 'AIzaSyCAlvtUeBgCsj3KBxJx_6XQM00gwIG6HTE';
        // -------------------------------------------------------------

        const SCOPES = 'https://www.googleapis.com/auth/drive.readonly';
        const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest';

        let tokenClient;
        let gapiInited = false;
        let gisInited = false;

        let reportData = []; // Array to store report objects

        document.getElementById('auth-btn').style.display = 'none';

        function gapiLoaded() {
            gapi.load('client', initializeGapiClient);
        }

        async function initializeGapiClient() {
            await gapi.client.init({
                apiKey: API_KEY,
                discoveryDocs: [DISCOVERY_DOC],
            });
            gapiInited = true;
            maybeEnableButtons();
        }

        function gisLoaded() {
            tokenClient = google.accounts.oauth2.initTokenClient({
                client_id: CLIENT_ID,
                scope: SCOPES,
                callback: '',
            });
            gisInited = true;
            maybeEnableButtons();
        }

        function maybeEnableButtons() {
            if (gapiInited && gisInited) {
                document.getElementById('auth-btn').style.display = 'inline-block';
                document.getElementById('status').innerText = 'Ready to Authorize.';
            }
        }

        function handleAuthClick() {
            tokenClient.callback = async (resp) => {
                if (resp.error !== undefined) {
                    throw (resp);
                }
                document.getElementById('auth-btn').style.display = 'none';
                document.getElementById('signout-btn').style.display = 'inline-block';
                document.getElementById('scan-container').style.display = 'flex';
                document.getElementById('status').innerText = 'Authorization successful. Ready to scan.';
            };

            if (gapi.client.getToken() === null) {
                tokenClient.requestAccessToken({ prompt: 'consent' });
            } else {
                tokenClient.requestAccessToken({ prompt: '' });
            }
        }

        function handleSignoutClick() {
            const token = gapi.client.getToken();
            if (token !== null) {
                google.accounts.oauth2.revoke(token.access_token);
                gapi.client.setToken(null);
                document.getElementById('auth-btn').style.display = 'inline-block';
                document.getElementById('signout-btn').style.display = 'none';
                document.getElementById('scan-container').style.display = 'none';
                document.getElementById('download-btn').style.display = 'none';
                document.getElementById('status').innerText = 'Signed out.';
                clearResults();
            }
        }

        function clearResults() {
            document.getElementById('empty-folders').innerHTML = '';
            document.getElementById('non-empty-folders').innerHTML = '';
            document.getElementById('download-btn').style.display = 'none';
            document.getElementById('summary-card').style.display = 'none';
            document.getElementById('total-folders-val').innerText = '0';
            document.getElementById('total-docs-val').innerText = '0';
            document.getElementById('non-empty-folders-val').innerText = '0';
            document.getElementById('empty-folders-val').innerText = '0';

            // Hide email form and clear CC selections
            document.getElementById('email-section').style.display = 'none';
            selectedCCEmails = [];
            const ccTags = document.getElementById('ccTagsContainer');
            if (ccTags) ccTags.innerHTML = '';
            const ccInput = document.getElementById('ccSearch');
            if (ccInput) ccInput.value = '';

            reportData = [];
        }

        function extractFolderId(input) {
            const match = input.match(/folders\/([a-zA-Z0-9-_]+)/);
            return match ? match[1] : input.trim();
        }

        async function startScan() {
            clearResults();
            const inputVal = document.getElementById('folder-id-input').value;
            const parentFolderId = extractFolderId(inputVal);

            if (!parentFolderId) {
                alert('Please provide a valid Folder ID or URL');
                return;
            }

            document.getElementById('status').innerText = 'Fetching subfolders...';
            document.getElementById('scan-btn').disabled = true;

            try {
                const response = await gapi.client.drive.files.list({
                    q: `'${parentFolderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false`,
                    fields: 'files(id, name)',
                    pageSize: 100
                });

                const subfolders = response.result.files;

                if (!subfolders || subfolders.length === 0) {
                    document.getElementById('status').innerText = 'No subfolders found inside this directory.';
                    document.getElementById('scan-btn').disabled = false;
                    return;
                }

                document.getElementById('status').innerText = `Found ${subfolders.length} subfolders. Scanning contents...`;

                let totalDocs = 0;
                let emptyCount = 0;
                let nonEmptyCount = 0;

                for (let i = 0; i < subfolders.length; i++) {
                    const folder = subfolders[i];
                    document.getElementById('status').innerText = `Scanning details: (${i + 1}/${subfolders.length}) ${folder.name}`;

                    const fileResponse = await gapi.client.drive.files.list({
                        q: `'${folder.id}' in parents and trashed = false`,
                        fields: 'files(name)',
                        pageSize: 100
                    });

                    const filesInFolder = fileResponse.result.files || [];

                    renderFolderResult(folder.name, filesInFolder);

                    const fileCount = filesInFolder.length;
                    totalDocs += fileCount;
                    if (fileCount === 0) {
                        emptyCount++;
                    } else {
                        nonEmptyCount++;
                    }

                    // Add structured data for Excel
                    reportData.push({
                        "Folder Name": folder.name,
                        "Status": filesInFolder.length === 0 ? "Empty" : "Has Files",
                        "File Count": filesInFolder.length,
                        "File Names": filesInFolder.map(f => f.name).join(', ') // comma separated
                    });
                }

                // Show summary metrics in HTML UI
                document.getElementById('total-folders-val').innerText = subfolders.length;
                document.getElementById('total-docs-val').innerText = totalDocs;
                document.getElementById('non-empty-folders-val').innerText = nonEmptyCount;
                document.getElementById('empty-folders-val').innerText = emptyCount;
                document.getElementById('summary-card').style.display = 'grid';

                // Append summary data directly to the end of reportData for Excel sheet
                reportData.push({
                    "Folder Name": "",
                    "Status": "",
                    "File Count": "",
                    "File Names": ""
                });
                reportData.push({
                    "Folder Name": "SUMMARY REPORT",
                    "Status": "",
                    "File Count": "",
                    "File Names": ""
                });
                reportData.push({
                    "Folder Name": "Total Folders Scanned",
                    "Status": "",
                    "File Count": subfolders.length,
                    "File Names": ""
                });
                reportData.push({
                    "Folder Name": "Total Scanned Documents",
                    "Status": "",
                    "File Count": totalDocs,
                    "File Names": ""
                });
                reportData.push({
                    "Folder Name": "Non-Empty Folders",
                    "Status": "",
                    "File Count": nonEmptyCount,
                    "File Names": ""
                });
                reportData.push({
                    "Folder Name": "Empty Folders",
                    "Status": "",
                    "File Count": emptyCount,
                    "File Names": ""
                });

                // Show email section
                document.getElementById('email-section').style.display = 'block';

                document.getElementById('status').innerText = 'Scan Complete! You can now download the Excel report or send it via email.';
                document.getElementById('download-btn').style.display = 'inline-block';

            } catch (err) {
                console.error(err);
                document.getElementById('status').innerText = 'Error processing request. Check console log or API Permissions.';
            }

            document.getElementById('scan-btn').disabled = false;
        }

        function renderFolderResult(folderName, files) {
            const emptyContainer = document.getElementById('empty-folders');
            const nonEmptyContainer = document.getElementById('non-empty-folders');

            const li = document.createElement('li');

            if (files.length === 0) {
                li.className = 'folder-item empty';
                li.innerHTML = `<div class="folder-name">${folderName}</div><div class="file-count">0 items (Empty)</div>`;
                emptyContainer.appendChild(li);
            } else {
                li.className = 'folder-item';
                let fileListHtml = '<ul class="file-list">';
                files.forEach(file => { fileListHtml += `<li>${file.name}</li>`; });
                fileListHtml += '</ul>';

                li.innerHTML = `
                <div class="folder-name">${folderName}</div>
                <div class="file-count">${files.length} document(s) detected</div>
                ${fileListHtml}
            `;
                nonEmptyContainer.appendChild(li);
            }
        }

        // Generate and download actual .xlsx file using SheetJS
        function downloadExcelReport() {
            if (reportData.length === 0) {
                alert("No data to download. Please scan a folder first.");
                return;
            }

            // Convert the JSON array into an Excel Worksheet
            const worksheet = XLSX.utils.json_to_sheet(reportData);

            // Adjust column widths automatically based on headers
            const wscols = [
                { wch: 35 }, // Folder Name width
                { wch: 15 }, // Status width
                { wch: 12 }, // File count width
                { wch: 60 }  // File Names width
            ];
            worksheet['!cols'] = wscols;

            // Create a new Excel Workbook and append the Worksheet
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Scan Results");

            // Trigger the download of the .xlsx file
            XLSX.writeFile(workbook, "Drive_Folder_Scan_Report.xlsx");
        }

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
                const query = facultySearch.value.toLowerCase().replace("prof.", "").trim();
                const filtered = facultyData.filter(member =>
                    member.name.toLowerCase().includes(query) ||
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
                        <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.name}</div>
                            <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
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

                const match = facultyData.find(m => m.name.toLowerCase() === val.toLowerCase());
                if (match) {
                    selectFaculty(match);
                } else {
                    const partialMatches = facultyData.filter(m => m.name.toLowerCase().includes(val.toLowerCase()));
                    if (partialMatches.length === 1) {
                        selectFaculty(partialMatches[0]);
                    } else {
                        clearSelection();
                        facultySearch.value = "";
                    }
                }
            }
        }

        // ── CC Emails Multi-Select Dropdown Logic ──
        let selectedCCEmails = [];

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

            function filterCCEmails() {
                const query = ccSearch.value.toLowerCase().trim();
                // Filter out already selected emails
                const available = facultyData.filter(member => !selectedCCEmails.includes(member.email));

                // Match by email or name
                const filtered = available.filter(member =>
                    member.email.toLowerCase().includes(query) ||
                    member.name.toLowerCase().includes(query)
                );

                renderCCDropdown(filtered);
            }

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
                        <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.email}</div>
                            <div class="item-desg">${member.name}</div>
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

            function removeCCTag(email) {
                selectedCCEmails = selectedCCEmails.filter(e => e !== email);
                updateCCTagsUI();
                ccSearch.focus();
                filterCCEmails();
            }

            function updateCCTagsUI() {
                ccTagsContainer.innerHTML = "";
                selectedCCEmails.forEach(email => {
                    const tag = document.createElement("div");
                    tag.className = "cc-tag";
                    tag.innerHTML = `
                        <span>${email}</span>
                        <button type="button" class="cc-tag-remove">&times;</button>
                    `;
                    tag.querySelector(".cc-tag-remove").addEventListener("click", function (e) {
                        e.stopPropagation();
                        removeCCTag(email);
                    });
                    ccTagsContainer.appendChild(tag);
                });
            }
        }

        // ── Email Send Submission ──
        function initEmailSend() {
            const sendBtn = document.getElementById("sendBtn");
            if (!sendBtn) return;

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

                if (reportData.length === 0) {
                    alert("Please scan a folder first before sending the report.");
                    return;
                }

                sendBtn.style.pointerEvents = "none";
                sendBtn.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Sending...</span>
                `;

                // Build HTML table for email from reportData
                const scanItems = reportData.filter(item => item["Status"] === "Empty" || item["Status"] === "Has Files");

                let tableRowsHtml = "";
                scanItems.forEach((item, index) => {
                    const bg = index % 2 === 0 ? '#ffffff' : '#f8fafc';
                    const statusColor = item["Status"] === "Empty" ? "#ef4444" : "#10b981";
                    const statusBg = item["Status"] === "Empty" ? "rgba(239, 68, 68, 0.08)" : "rgba(16, 185, 129, 0.08)";

                    tableRowsHtml += `
                        <tr style="background-color: ${bg};">
                            <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 13px; color: #64748b; font-family: 'Playfair Display', serif;">${index + 1}</td>
                            <td style="padding: 12px 14px; border: 1px solid rgba(0,0,0,0.08); font-size: 13.5px; font-weight: 600; color: #0f172a; font-family: 'Playfair Display', serif;">${item["Folder Name"]}</td>
                            <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 12px; font-family: 'Playfair Display', serif;">
                                <span style="display: inline-block; padding: 4px 10px; font-weight: 700; border-radius: 50px; background-color: ${statusBg}; color: ${statusColor}; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', serif;">${item["Status"]}</span>
                            </td>
                            <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 13.5px; font-weight: bold; color: #1e293b; font-family: 'Playfair Display', serif;">${item["File Count"]}</td>
                            <td style="padding: 12px 14px; border: 1px solid rgba(0,0,0,0.08); font-size: 13px; color: #475569; word-break: break-all; font-family: 'Playfair Display', serif;">${item["File Names"] || '-'}</td>
                        </tr>
                    `;
                });

                const totalFolders = scanItems.length;
                const emptyFolders = scanItems.filter(item => item["Status"] === "Empty").length;
                const nonEmptyFolders = scanItems.filter(item => item["Status"] === "Has Files").length;

                let totalDocs = 0;
                scanItems.forEach(item => totalDocs += item["File Count"]);

                const recipientEmail = document.getElementById("facultyEmail").value;
                const emailSubject = `Google Drive Folder Scan Analysis Report`;

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
                                font-family: 'Playfair Display', serif !important;
                            }
                            body { background-color: #f8fafc; margin: 0; padding: 0; color: #334155; }
                            .email-container { max-width: 800px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
                            .header-banner { background: linear-gradient(135deg, #0b1530 0%, #0d1b38 100%); padding: 28px 24px; text-align: center; color: #ffffff; border-bottom: 4px solid #10b981; }
                            .header-banner h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
                            .content-body { padding: 32px 30px; }
                            .greeting { font-size: 19px; font-weight: 600; color: #0b1530; margin-bottom: 18px; }
                            .message { font-size: 16.5px; line-height: 1.7; color: #475569; margin-bottom: 28px; }
                            .metrics-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; width: 25%; }
                            .metrics-num { font-family: 'Share Tech', Courier, monospace !important; font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px; }
                            .metrics-label { font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
                            .results-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
                            .results-table th { background-color: #f1f5f9; color: #475569; padding: 10px 12px; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #cbd5e1; }
                            .footer-container { background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-size: 14px; color: #64748b; line-height: 1.5; }

                            /* Responsive Styles */
                            @media only screen and (max-width: 600px) {
                                .email-container {
                                    padding: 12px 6px !important;
                                    margin: 10px auto !important;
                                }
                                .content-body {
                                    padding: 20px 14px !important;
                                }
                                .header-banner {
                                    padding: 20px 14px !important;
                                }
                                .header-banner h1 {
                                    font-size: 18px !important;
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
                                .results-table {
                                    width: 100% !important;
                                    min-width: 650px !important;
                                }
                                .footer-container {
                                    padding: 16px 12px !important;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="header-banner">
                                <h1>Google Drive Folder Scan Report</h1>
                            </div>
                            <div class="content-body">
                                <div class="greeting">Dear ${preparedBy},</div>
                                <div class="message">
                                    Please find the details of the latest <strong>Google Drive Folder Scan Report</strong> below, prepared by <strong>Mr. Dev K Dholakiya</strong>.<br><br>
                                    Please upload the pending documents to the Google Drive as soon as possible.
                                </div>
                                
                                <h2 style="font-size: 16px; color: #0b1530; margin: 0 0 12px; border-left: 4px solid #10b981; padding-left: 8px; line-height: 1.2;">Scan Summary Metrics</h2>
                                <table class="metrics-table" style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 30px;">
                                    <tr>
                                        <td class="metrics-card" style="border-top: 4px solid #fbbf24;">
                                            <div class="metrics-label">Folders Scanned</div>
                                            <div class="metrics-num">${totalFolders}</div>
                                        </td>
                                        <td class="metrics-card" style="border-top: 4px solid #3b82f6;">
                                            <div class="metrics-label">Total Documents</div>
                                            <div class="metrics-num">${totalDocs}</div>
                                        </td>
                                        <td class="metrics-card" style="border-top: 4px solid #10b981;">
                                            <div class="metrics-label">Non-Empty</div>
                                            <div class="metrics-num">${nonEmptyFolders}</div>
                                        </td>
                                        <td class="metrics-card" style="border-top: 4px solid #ef4444;">
                                            <div class="metrics-label">Empty</div>
                                            <div class="metrics-num">${emptyFolders}</div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <h2 style="font-size: 16px; color: #0b1530; margin: 24px 0 12px; border-left: 4px solid #10b981; padding-left: 8px; line-height: 1.2;">Scanned Folders List</h2>
                                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                    <table class="results-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Folder Name</th>
                                                <th style="width: 100px;">Status</th>
                                                <th style="width: 80px;">Files</th>
                                                <th>File Names</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${tableRowsHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Footer -->
                            <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', serif;">
                                <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">This email was automatically generated by the <br><a href="${window.location.href}" style="color: #c0392b; text-decoration: none; font-weight: 600;">IT Department</a>.</p>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                fetch('send-email.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: recipientEmail,
                        cc: selectedCCEmails,
                        subject: emailSubject,
                        html: emailHtml
                    })
                })
                    .then(response => response.json().then(data => {
                        if (!response.ok) {
                            throw new Error(data.error || `HTTP error ${response.status}`);
                        }
                        return data;
                    }))
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
                                <path d="M22 2L11 13" />
                                <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                            </svg>
                            <span>Send Report via Email</span>
                        `;
                        }, 4000);
                    });
            });
        }

        // ── FAB Nav Toggle ──
        window.addEventListener('DOMContentLoaded', () => {
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

            // Initialize faculty search, CC search, and email send
            initFacultySearch();
            initCCEmailsSearch();
            initEmailSend();
        });
    </script>
</body>

</html>
