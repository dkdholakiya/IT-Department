<?php include 'auth-check.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Google Drive Folder Scanner & Excel Reporter utility for academic event files.">
    <title>Drive Folder Scanner — CE & IT Department</title>

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
                        Department of CE & IT
                    </div>
                    <h1 class="rp-title">Drive Folder Scanner</h1>
                </div>

                <span class="portal-badge">Drive Tool</span>
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
        <?php 
        $footer_class = 'rp-footer';
        include 'footer.php'; 
        ?>

    </div><!-- /rp-page -->

    <?php 
    $active_page = 'ctldrive';
    include 'fab-nav.php'; 
    ?>

    <script src="assets/vendor/xlsx/xlsx.full.min.js"></script>

    <!-- Faculty Data -->
    <script src="assets/js/facultyData.js"></script>

    <!-- Google APIs -->
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>

    <script>
        // Clear the session on load so that refresh triggers password re-prompt
        window.addEventListener('load', () => {
            fetch('verify-password?action=clear');
        });

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
        let scannedFoldersData = []; // Array to store detailed folders and files

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
            scannedFoldersData = [];
        }

        function extractFolderId(input) {
            const match = input.match(/folders\/([a-zA-Z0-9-_]+)/);
            return match ? match[1] : input.trim();
        }

        // Utility to format file sizes
        function formatBytes(bytes) {
            if (bytes === undefined || bytes === null) return 'N/A';
            const b = parseInt(bytes);
            if (isNaN(b)) return 'N/A';
            if (b === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(b) / Math.log(k));
            return parseFloat((b / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Utility to format dates
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return 'N/A';
            return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        // Utility to format MIME type into a readable form
        function formatMimetype(mimeType, name) {
            if (!mimeType) return 'Unknown File';
            if (mimeType.startsWith('application/vnd.google-apps.')) {
                // Google formats
                const type = mimeType.split('.').pop();
                return 'Google ' + type.charAt(0).toUpperCase() + type.slice(1);
            }
            // Use extension if present
            const dotIdx = name.lastIndexOf('.');
            if (dotIdx !== -1 && dotIdx < name.length - 1) {
                return name.substring(dotIdx + 1).toUpperCase() + ' File';
            }
            // Standard MIME mapping fallback
            if (mimeType === 'application/pdf') return 'PDF Document';
            if (mimeType.startsWith('image/')) return 'Image File';
            if (mimeType.startsWith('video/')) return 'Video File';
            if (mimeType.startsWith('audio/')) return 'Audio File';
            if (mimeType.startsWith('text/')) return 'Text Document';
            return mimeType;
        }

        // Utility to ensure unique, valid Excel sheet names (max 31 characters, no invalid chars)
        function getUniqueSheetName(name, existingNames) {
            // Excel sheet names cannot contain: \ / ? * [ ] :
            let sanitized = name.replace(/[\\\/\?\*\[\]\:]/g, ' ');
            // Truncate to 31 characters
            sanitized = sanitized.substring(0, 31).trim();
            if (!sanitized) {
                sanitized = "Folder Sheet";
            }
            let finalName = sanitized;
            let counter = 1;
            while (existingNames.has(finalName.toLowerCase())) {
                const suffix = ` (${counter})`;
                const maxLen = 31 - suffix.length;
                finalName = sanitized.substring(0, maxLen).trim() + suffix;
                counter++;
            }
            existingNames.add(finalName.toLowerCase());
            return finalName;
        }

        async function startScan() {
            clearResults();
            const inputVal = document.getElementById('folder-id-input').value;
            const parentFolderId = extractFolderId(inputVal);

            if (!parentFolderId) {
                alert('Please provide a valid Folder ID or URL');
                return;
            }

            document.getElementById('status').innerText = 'Initializing recursive scan...';
            document.getElementById('scan-btn').disabled = true;

            let totalDocs = 0;
            let emptyCount = 0;
            let nonEmptyCount = 0;
            let folderCount = 0;
            let classFolders = [];

            // 1. Determine Class Level target depth based on root parent folder name
            let targetClassDepth = 3; // Default for root -> course -> semester -> class
            let rootName = "Root";

            async function findClassFolders(folderId, currentPath, depth) {
                // Fetch subfolders of this folder
                let subfolders = [];
                let nextPageToken = null;
                do {
                    const response = await gapi.client.drive.files.list({
                        q: `'${folderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false`,
                        fields: 'nextPageToken, files(id, name)',
                        pageSize: 100,
                        pageToken: nextPageToken || undefined
                    });
                    const filesBatch = response.result.files || [];
                    subfolders = subfolders.concat(filesBatch);
                    nextPageToken = response.result.nextPageToken;
                } while (nextPageToken);

                // If we reached target depth or this is a leaf folder before target depth
                if (depth === targetClassDepth || subfolders.length === 0) {
                    classFolders.push({
                        id: folderId,
                        name: currentPath || rootName
                    });
                    return;
                }

                // Recurse into subfolders
                for (const subfolder of subfolders) {
                    const nextPath = currentPath ? `${currentPath} > ${subfolder.name}` : subfolder.name;
                    await findClassFolders(subfolder.id, nextPath, depth + 1);
                }
            }

            // Recursive function to scan subfolders and files inside a Class folder
            async function scanClassContents(folderId, relativePath, classData) {
                // Fetch subfolders of this directory
                let subfolders = [];
                let nextPageToken = null;
                do {
                    const response = await gapi.client.drive.files.list({
                        q: `'${folderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false`,
                        fields: 'nextPageToken, files(id, name)',
                        pageSize: 100,
                        pageToken: nextPageToken || undefined
                    });
                    const filesBatch = response.result.files || [];
                    subfolders = subfolders.concat(filesBatch);
                    nextPageToken = response.result.nextPageToken;
                } while (nextPageToken);

                // Fetch files of this directory
                let filesInFolder = [];
                let filePageToken = null;
                do {
                    const fileResponse = await gapi.client.drive.files.list({
                        q: `'${folderId}' in parents and mimeType != 'application/vnd.google-apps.folder' and trashed = false`,
                        fields: 'nextPageToken, files(id, name, mimeType, size, createdTime, webViewLink)',
                        pageSize: 100,
                        pageToken: filePageToken || undefined
                    });
                    const fileBatch = fileResponse.result.files || [];
                    filesInFolder = filesInFolder.concat(fileBatch);
                    filePageToken = fileResponse.result.nextPageToken;
                } while (filePageToken);

                // If this is a subfolder of the class (depth 4 or more)
                if (relativePath) {
                    classData.activities.push({
                        name: relativePath,
                        folderId: folderId,
                        folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                        files: filesInFolder
                    });
                } else {
                    // Direct files in the class folder itself
                    if (filesInFolder.length > 0) {
                        classData.activities.push({
                            name: "Direct Files",
                            folderId: folderId,
                            folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                            files: filesInFolder
                        });
                    }
                }

                // If this is the class folder itself and it has no subfolders and no files
                if (!relativePath && subfolders.length === 0 && filesInFolder.length === 0) {
                    classData.activities.push({
                        name: "Main Folder",
                        folderId: folderId,
                        folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                        files: []
                    });
                }

                // Recurse into subfolders
                for (const subfolder of subfolders) {
                    const nextPath = relativePath ? `${relativePath} > ${subfolder.name}` : subfolder.name;
                    await scanClassContents(subfolder.id, nextPath, classData);
                }
            }

            try {
                // Fetch root folder metadata
                const rootMeta = await gapi.client.drive.files.get({
                    fileId: parentFolderId,
                    fields: 'name'
                });
                rootName = rootMeta.result.name || "Root";

                // Dynamically adjust target class depth based on root folder name
                const rootNameLower = rootName.toLowerCase();
                if (rootNameLower.includes("semester") || rootNameLower.includes("sem ")) {
                    targetClassDepth = 1;
                } else if (rootNameLower === "b.tech" || rootNameLower === "diploma" || rootNameLower === "btech") {
                    targetClassDepth = 2;
                } else if (rootNameLower.includes("clss") || rootNameLower.includes("class")) {
                    targetClassDepth = 0;
                }

                // Find class folders
                document.getElementById('status').innerText = 'Locating class folders...';
                await findClassFolders(parentFolderId, "", 0);

                if (classFolders.length === 0) {
                    document.getElementById('status').innerText = 'No class folders found inside this directory.';
                    document.getElementById('scan-btn').disabled = false;
                    return;
                }

                document.getElementById('status').innerText = `Found ${classFolders.length} class folders. Scanning contents...`;

                for (let i = 0; i < classFolders.length; i++) {
                    const classFolder = classFolders[i];
                    document.getElementById('status').innerText = `Scanning class (${i + 1}/${classFolders.length}): ${classFolder.name}...`;

                    const classData = {
                        id: classFolder.id,
                        name: classFolder.name,
                        activities: []
                    };

                    await scanClassContents(classFolder.id, "", classData);

                    // Compute metrics
                    const totalAct = classData.activities.length;
                    const emptyAct = classData.activities.filter(a => a.files.length === 0).length;
                    const filledAct = totalAct - emptyAct;
                    const docsCount = classData.activities.reduce((acc, a) => acc + a.files.length, 0);

                    totalDocs += docsCount;
                    folderCount++;
                    if (filledAct === 0) {
                        emptyCount++;
                    } else {
                        nonEmptyCount++;
                    }

                    // Render in the HTML UI
                    renderClassResult(classFolder.name, classData.activities);

                    // Store class data for Excel generation
                    scannedFoldersData.push(classData);

                    // Build formatted activities summary for legacy Excel & email table
                    const activitiesSummary = classData.activities.map(a => 
                        `${a.name}: ${a.files.length === 0 ? "Empty" : a.files.length + " Files"}`
                    ).join(' | ') || 'No Activities';

                    reportData.push({
                        "Folder Name": classFolder.name,
                        "Status": filledAct === 0 ? "Empty" : (emptyAct === 0 ? "Has Files" : "Partial"),
                        "File Count": docsCount,
                        "File Names": activitiesSummary
                    });
                }

                // Show summary metrics in HTML UI
                document.getElementById('total-folders-val').innerText = folderCount;
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
                    "File Count": folderCount,
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

        function renderClassResult(className, activities) {
            const emptyContainer = document.getElementById('empty-folders');
            const nonEmptyContainer = document.getElementById('non-empty-folders');

            const li = document.createElement('li');
            li.className = 'folder-item';

            const total = activities.length;
            const emptyActCount = activities.filter(a => a.files.length === 0).length;
            const filledCount = total - emptyActCount;

            let activitiesHtml = '<ul class="file-list" style="list-style-type: none; padding-left: 0; margin-top: 10px;">';
            activities.forEach(activity => {
                const isActEmpty = activity.files.length === 0;
                const bullet = isActEmpty ? '🔴' : '🟢';
                const statusText = isActEmpty ? '(Empty)' : `(${activity.files.length} documents)`;
                
                activitiesHtml += `<li style="margin-bottom: 8px; color: #cbd5e1; font-family: 'Merriweather Sans', sans-serif;">`;
                activitiesHtml += `${bullet} <strong>${activity.name}</strong> ${statusText}`;
                
                if (activity.files.length > 0) {
                    activitiesHtml += '<ul style="list-style-type: circle; padding-left: 20px; margin-top: 4px; font-size: 12.5px; color: #b4c6ef;">';
                    activity.files.forEach(file => {
                        activitiesHtml += `<li>${file.name}</li>`;
                    });
                    activitiesHtml += '</ul>';
                }
                activitiesHtml += '</li>';
            });
            activitiesHtml += '</ul>';

            li.innerHTML = `
                <div class="folder-name">${className}</div>
                <div class="file-count" style="margin-top: 6px;">
                    Activities: ${filledCount} / ${total} Filled &nbsp;·&nbsp; ${emptyActCount} Empty
                </div>
                ${activitiesHtml}
            `;

            if (filledCount === 0) {
                li.className = 'folder-item empty';
                emptyContainer.appendChild(li);
            } else {
                nonEmptyContainer.appendChild(li);
            }
        }

        // Generate and download actual .xlsx file using SheetJS with folder-wise sheets
        function downloadExcelReport() {
            if (scannedFoldersData.length === 0) {
                alert("No data to download. Please scan a folder first.");
                return;
            }

            // Draw text-based progress bar chart
            function drawProgressBar(value, total) {
                if (total === 0) return "[░░░░░░░░░░] 0%";
                const percentage = Math.round((value / total) * 100);
                const filledBlocks = Math.round(percentage / 10);
                const emptyBlocks = 10 - filledBlocks;
                const bar = "█".repeat(filledBlocks) + "░".repeat(emptyBlocks);
                return `[${bar}] ${percentage}%`;
            }

            // Create a new Excel Workbook
            const workbook = XLSX.utils.book_new();

            // 1. Add Summary Sheet (Overall Analysis dashboard)
            const summaryRows = [];
            summaryRows.push(["CE & IT DEPARTMENT - GOOGLE DRIVE FOLDER SCAN REPORT"]);
            summaryRows.push(["Report Generated At:", new Date().toLocaleString()]);
            summaryRows.push([]); // spacer row

            // Extract metrics counts
            const totalFolders = scannedFoldersData.length;
            let totalDocs = 0;
            let emptyCount = 0;
            let nonEmptyCount = 0;
            scannedFoldersData.forEach(classFolder => {
                const totalAct = classFolder.activities.length;
                const emptyAct = classFolder.activities.filter(a => a.files.length === 0).length;
                const filledAct = totalAct - emptyAct;
                totalDocs += classFolder.activities.reduce((acc, a) => acc + a.files.length, 0);
                if (filledAct === 0) {
                    emptyCount++;
                } else {
                    nonEmptyCount++;
                }
            });

            summaryRows.push(["METRICS SUMMARY"]);
            summaryRows.push(["Total Classes Scanned", totalFolders]);
            summaryRows.push(["Total Scanned Documents", totalDocs]);
            summaryRows.push(["Filled Classes", nonEmptyCount]);
            summaryRows.push(["Empty Classes (Action Required)", emptyCount]);
            summaryRows.push(["Overall Completion Rate", drawProgressBar(nonEmptyCount, totalFolders)]);
            summaryRows.push([]); // spacer row

            summaryRows.push(["CLASS-WISE OVERALL STATUS LIST"]);
            summaryRows.push(["S.No.", "Class Folder Path", "Status", "Activities Progress", "Total Docs", "Activities Detail Summary"]);

            scannedFoldersData.forEach((classFolder, index) => {
                const totalAct = classFolder.activities.length;
                const emptyAct = classFolder.activities.filter(a => a.files.length === 0).length;
                const filledAct = totalAct - emptyAct;
                const docsCount = classFolder.activities.reduce((acc, a) => acc + a.files.length, 0);

                let statusLabel = "🟢 FILLED";
                if (filledAct === 0) statusLabel = "🔴 EMPTY";
                else if (emptyAct > 0) statusLabel = "🟡 PARTIAL";

                const detailSummary = classFolder.activities.map(a => 
                    `${a.name}: ${a.files.length === 0 ? "🔴 Empty" : "🟢 " + a.files.length + " Docs"}`
                ).join(' | ') || 'No Activities';

                summaryRows.push([
                    index + 1,
                    classFolder.name,
                    statusLabel,
                    `${filledAct} / ${totalAct} Filled`,
                    docsCount,
                    detailSummary
                ]);
            });

            const summarySheet = XLSX.utils.aoa_to_sheet(summaryRows);
            summarySheet['!cols'] = [
                { wch: 8 },  // S.No.
                { wch: 45 }, // Class Folder Path
                { wch: 15 }, // Status
                { wch: 20 }, // Activities Progress
                { wch: 12 }, // Total Docs
                { wch: 65 }  // Activities Detail Summary
            ];
            XLSX.utils.book_append_sheet(workbook, summarySheet, "Overall Analysis");

            // Track sheet names to ensure uniqueness (case-insensitive)
            const existingNames = new Set(["overall analysis"]);

            // 2. Add a detailed sheet for each Class Folder
            scannedFoldersData.forEach(classFolder => {
                const pathParts = classFolder.name.split(" > ");
                const baseName = pathParts[pathParts.length - 1] || classFolder.name;
                const sheetName = getUniqueSheetName(baseName, existingNames);
                
                const folderRows = [];

                const totalAct = classFolder.activities.length;
                const emptyAct = classFolder.activities.filter(a => a.files.length === 0).length;
                const filledAct = totalAct - emptyAct;
                const classDocsCount = classFolder.activities.reduce((acc, a) => acc + a.files.length, 0);

                let statusLabel = "🟢 FILLED";
                if (filledAct === 0) statusLabel = "🔴 EMPTY";
                else if (emptyAct > 0) statusLabel = "🟡 PARTIAL";

                // Metadata block
                folderRows.push(["FOLDER ANALYSIS REPORT"]);
                folderRows.push(["Class Folder Path:", classFolder.name]);
                folderRows.push(["Status:", statusLabel]);
                folderRows.push(["Activities Progress:", `${filledAct} / ${totalAct} Activities Filled`]);
                folderRows.push(["Completion Bar:", drawProgressBar(filledAct, totalAct)]);
                folderRows.push([]); // Empty spacer row

                // Table Header
                folderRows.push(["S.No.", "Activity / Subfolder Name", "Activity Status", "File Name", "MIME Type", "Size", "Uploaded Date", "Google Drive Link"]);

                const dataRows = []; // Keep track of rows to add links

                if (totalAct === 0) {
                    folderRows.push([1, "Main Folder", "🔴 Empty", "No Files Uploaded", "-", "-", "-", "Open Folder"]);
                    dataRows.push({
                        linkUrl: `https://drive.google.com/drive/folders/${classFolder.id}`,
                        linkText: "Open Folder"
                    });
                } else {
                    let serialNo = 1;
                    classFolder.activities.forEach(activity => {
                        if (activity.files.length === 0) {
                            folderRows.push([
                                serialNo++,
                                activity.name,
                                "🔴 Empty",
                                "No Files Uploaded",
                                "-",
                                "-",
                                "-",
                                "Open Folder"
                            ]);
                            dataRows.push({
                                linkUrl: activity.folderLink,
                                linkText: "Open Folder"
                            });
                        } else {
                            activity.files.forEach(file => {
                                folderRows.push([
                                    serialNo++,
                                    activity.name,
                                    "🟢 Has Files",
                                    file.name,
                                    formatMimetype(file.mimeType, file.name),
                                    formatBytes(file.size),
                                    formatDate(file.createdTime),
                                    "Open File"
                                ]);
                                dataRows.push({
                                    linkUrl: file.webViewLink,
                                    linkText: "Open File"
                                });
                            });
                        }
                    });
                }

                const ws = XLSX.utils.aoa_to_sheet(folderRows);

                // Adjust column widths
                ws['!cols'] = [
                    { wch: 8 },  // S.No.
                    { wch: 35 }, // Activity / Subfolder Name
                    { wch: 15 }, // Activity Status
                    { wch: 45 }, // File Name
                    { wch: 25 }, // MIME Type
                    { wch: 15 }, // Size
                    { wch: 22 }, // Uploaded Date
                    { wch: 15 }  // Link
                ];

                // Transform long URLs to clickable cell hyperlinks
                dataRows.forEach((row, index) => {
                    if (row.linkUrl) {
                        const rowIndex = 7 + index; // Header is row index 6 (0-indexed)
                        const cellRef = XLSX.utils.encode_cell({ r: rowIndex, c: 7 }); // Column H is index 7
                        ws[cellRef] = {
                            t: 's',
                            v: row.linkText,
                            l: { Target: row.linkUrl, Tooltip: row.linkText === 'Open Folder' ? 'Click to open folder' : 'Click to open file' }
                        };
                    }
                });

                XLSX.utils.book_append_sheet(workbook, ws, sheetName);
            });

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
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.name}</div>
                            <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.department || "Information Technology"} &nbsp;·&nbsp; ${member.empId}</div>
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

                // Dynamically update document title based on selected faculty department
                const isCe = (member.initials === "DRC" || member.name.includes("Dhaval Chandarana")) 
                    ? (localStorage.getItem("portal_dept") === "CE") 
                    : (member.department === "Computer Engineering");
                document.title = isCe ? "Drive Folder Scanner — CE Department" : "Drive Folder Scanner — IT Department";
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
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
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

                const isCe = (selectedFaculty && (selectedFaculty.initials === "DRC" || selectedFaculty.name.includes("Dhaval Chandarana")) ? (localStorage.getItem("portal_dept") === "CE") : (selectedFaculty && selectedFaculty.department === "Computer Engineering"));

                // Build HTML table for email showing one row per activity folder
                let tableRowsHtml = "";
                let index = 0;
                let totalFolders = 0;
                let emptyFolders = 0;
                let nonEmptyFolders = 0;
                let totalDocs = 0;

                scannedFoldersData.forEach(classFolder => {
                    const pathParts = classFolder.name.split(" > ");
                    const classBaseName = pathParts[pathParts.length - 1] || classFolder.name;

                    classFolder.activities.forEach(activity => {
                        index++;
                        totalFolders++;

                        const bg = index % 2 === 0 ? '#ffffff' : '#f8fafc';
                        const isActEmpty = activity.files.length === 0;
                        const statusColor = isActEmpty ? "#ef4444" : "#10b981";
                        const statusBg = isActEmpty ? "rgba(239, 68, 68, 0.08)" : "rgba(16, 185, 129, 0.08)";
                        const statusText = isActEmpty ? "EMPTY" : "HAS FILES";

                        if (isActEmpty) {
                            emptyFolders++;
                        } else {
                            nonEmptyFolders++;
                        }

                        const fileCount = activity.files.length;
                        totalDocs += fileCount;

                        const filesListStr = activity.files.map(f => f.name).join(', ') || '-';
                        const displayName = `${classBaseName} > ${activity.name}`;

                        tableRowsHtml += `
                            <tr style="background-color: ${bg};">
                                <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 13px; color: #64748b; font-family: 'Playfair Display', serif;">${index}</td>
                                <td style="padding: 12px 14px; border: 1px solid rgba(0,0,0,0.08); font-size: 13.5px; font-weight: 600; color: #0f172a; font-family: 'Playfair Display', serif;">${displayName}</td>
                                <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 12px; font-family: 'Playfair Display', serif;">
                                    <span style="display: inline-block; padding: 4px 10px; font-weight: 700; border-radius: 50px; background-color: ${statusBg}; color: ${statusColor}; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Playfair Display', serif;">${statusText}</span>
                                </td>
                                <td style="padding: 12px 14px; text-align: center; border: 1px solid rgba(0,0,0,0.08); font-size: 13.5px; font-weight: bold; color: #1e293b; font-family: 'Playfair Display', serif;">${fileCount}</td>
                                <td style="padding: 12px 14px; border: 1px solid rgba(0,0,0,0.08); font-size: 13px; color: #475569; word-break: break-all; font-family: 'Playfair Display', serif;">${filesListStr}</td>
                            </tr>
                        `;
                    });
                });

                const recipientEmail = document.getElementById("facultyEmail").value || (localStorage.getItem("portal_dept") === "CE" ? "admincecse@gmiu.edu.in" : "adminit@gmiu.edu.in");
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
                                <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">This email was automatically generated by the <br><a href="${window.location.href}" style="color: ${isCe ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600;">${isCe ? 'CE Department' : 'IT Department'}</a>.</p>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                fetch('send-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: recipientEmail,
                        cc: selectedCCEmails,
                        subject: emailSubject,
                        html: emailHtml,
                        dept: (selectedFaculty && (selectedFaculty.initials === "DRC" || selectedFaculty.name.includes("Dhaval Chandarana")) ? (localStorage.getItem("portal_dept") === "CE" ? "CE" : "IT") : ((selectedFaculty && selectedFaculty.department === "Computer Engineering") ? "CE" : "IT"))
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

        // ── Initialize searches and email sends ──
        window.addEventListener('DOMContentLoaded', () => {
            // Initialize faculty search, CC search, and email send
            initFacultySearch();
            initCCEmailsSearch();
            initEmailSend();
        });
    </script>
</body>

</html>
