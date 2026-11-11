/**
 * CTL Drive Scanner & Excel Reporter Controller — CE & IT Department
 * Smart Management Dashboard & Portal Integration
 *
 * Capabilities:
 * - Google Identity Services (GIS) & Google API (GAPI) OAuth 2.0 flow
 * - 24-hour token caching with auto session restoration across page reloads
 * - Recursive Google Drive folder inspection with depth-level heuristics
 * - Real-time auto-scrolling log console with live modal popup
 * - 4 KPI metrics counters live updates
 * - SheetJS (.xlsx) multi-sheet workbook generation with progress bars & hyperlinks
 * - Searchable faculty auto-fill & CC email tags selector
 * - HTML report generation & email dispatching
 * - Synchronized top navigation search with 'portal:search'
 */

// Global configuration
const CLIENT_ID = '790619253351-7811pmcbuii0eq52ugkb6o9109q86t48.apps.googleusercontent.com';
const API_KEY = 'AIzaSyCAlvtUeBgCsj3KBxJx_6XQM00gwIG6HTE';
const SCOPES = 'https://www.googleapis.com/auth/drive.readonly';
const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest';

let tokenClient = null;
let gapiInited = false;
let gisInited = false;

let reportData = []; // Array to store report objects for email & summary
let scannedFoldersData = []; // Array to store detailed folders and files
let scannedRootFolderName = ''; // Root folder name

// Helper to resolve faculty avatar classes
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
// Google Drive API Initialization
// -------------------------------------------------------------
function gapiLoaded() {
    if (typeof gapi !== 'undefined' && !gapiInited) {
        gapi.load('client', initializeGapiClient);
    }
}

async function initializeGapiClient() {
    try {
        await gapi.client.init({
            apiKey: API_KEY,
            discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
        maybeEnableButtons();
    } catch (err) {
        console.error("GAPI Client init error:", err);
    }
}

function gisLoaded() {
    if (typeof google !== 'undefined' && google.accounts && google.accounts.oauth2 && !gisInited) {
        try {
            tokenClient = google.accounts.oauth2.initTokenClient({
                client_id: CLIENT_ID,
                scope: SCOPES,
                callback: '',
            });
            gisInited = true;
            maybeEnableButtons();
        } catch (err) {
            console.error("GIS Client init error:", err);
        }
    }
}

// Hook early window listeners and handle any queued calls
window._initGapi = gapiLoaded;
window._initGis = gisLoaded;
window.gapiLoaded = gapiLoaded;
window.gisLoaded = gisLoaded;

if (window._gapiLoadedQueue || typeof gapi !== 'undefined') {
    gapiLoaded();
}
if (window._gisLoadedQueue || (typeof google !== 'undefined' && google.accounts && google.accounts.oauth2)) {
    gisLoaded();
}

// Active interval checker in case external CDNs load before or after
const checkGoogleApisInterval = setInterval(() => {
    if (!gapiInited && typeof gapi !== 'undefined') {
        gapiLoaded();
    }
    if (!gisInited && typeof google !== 'undefined' && google.accounts && google.accounts.oauth2) {
        gisLoaded();
    }
    if (gapiInited && gisInited) {
        clearInterval(checkGoogleApisInterval);
    }
}, 250);
setTimeout(() => clearInterval(checkGoogleApisInterval), 15000);

function updateConnectionBadge(isConnected, labelText) {
    const badge = document.getElementById('connection-status-badge');
    const dot = document.getElementById('connection-status-dot');
    const text = document.getElementById('connection-status-text');
    if (badge && dot && text) {
        if (isConnected) {
            dot.className = 'w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0';
            text.innerText = labelText || 'Connected (Active Session)';
            text.className = 'text-emerald-700 dark:text-emerald-300 font-bold';
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400';
        } else {
            dot.className = 'w-2 h-2 rounded-full bg-amber-500 shrink-0';
            text.innerText = labelText || 'Ready to Authorize';
            text.className = 'text-amber-700 dark:text-amber-300 font-semibold';
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-600 dark:text-amber-400';
        }
    }
}

function maybeEnableButtons() {
    if (gapiInited && gisInited) {
        const savedToken = localStorage.getItem('gmiu_drive_token');
        const expiresAt = parseInt(localStorage.getItem('gmiu_token_expires_at') || '0', 10);

        const authBtn = document.getElementById('auth-btn');
        const signoutBtn = document.getElementById('signout-btn');
        const scanContainer = document.getElementById('scan-container');
        const statusEl = document.getElementById('status');

        if (savedToken && Date.now() < expiresAt) {
            // Restore existing Google Drive session across page refreshes
            gapi.client.setToken({ access_token: savedToken });
            if (authBtn) authBtn.style.display = 'none';
            if (signoutBtn) signoutBtn.style.display = 'inline-flex';
            if (scanContainer) scanContainer.style.display = 'flex';
            renderDeptPresets(currentDeptPreset);
            updateConnectionBadge(true, 'Connected (Active Session)');
            if (statusEl) statusEl.innerHTML = '<div class="status-line info"><span class="timestamp">[' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ']</span> Connected to Google Drive (Active session). Ready to scan.</div>';
        } else {
            if (savedToken) {
                localStorage.removeItem('gmiu_drive_token');
                localStorage.removeItem('gmiu_token_expires_at');
            }
            if (authBtn) authBtn.style.display = 'inline-flex';
            if (signoutBtn) signoutBtn.style.display = 'none';
            if (scanContainer) scanContainer.style.display = 'none';
            updateConnectionBadge(false, 'Ready to Authorize');
            if (statusEl) statusEl.innerHTML = '<div class="status-line info"><span class="timestamp">[' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ']</span> Ready to Authorize. Click "Authorize & Connect" to begin.</div>';
        }
    }
}

function handleAuthClick() {
    if (!tokenClient) {
        if (typeof google !== 'undefined' && google.accounts && google.accounts.oauth2) {
            gisLoaded();
        }
    }
    if (!gapiInited && typeof gapi !== 'undefined') {
        gapiLoaded();
    }

    if (!tokenClient) {
        alert("Google Authentication Client is still connecting to Google services. Please wait a couple of seconds and click again.");
        return;
    }

    tokenClient.callback = async (resp) => {
        if (resp.error !== undefined) {
            console.error("OAuth error:", resp);
            throw (resp);
        }

        // Save token to localStorage with 24-hour automatic logout timer
        const hours24InMs = 24 * 60 * 60 * 1000;
        localStorage.setItem('gmiu_drive_token', resp.access_token);
        localStorage.setItem('gmiu_token_expires_at', (Date.now() + hours24InMs).toString());

        gapi.client.setToken({ access_token: resp.access_token });
        const authBtn = document.getElementById('auth-btn');
        const signoutBtn = document.getElementById('signout-btn');
        const scanContainer = document.getElementById('scan-container');
        if (authBtn) authBtn.style.display = 'none';
        if (signoutBtn) signoutBtn.style.display = 'inline-flex';
        if (scanContainer) scanContainer.style.display = 'flex';
        renderDeptPresets(currentDeptPreset);
        updateConnectionBadge(true, 'Connected (Session Active 24h)');
        logConsole('Authorization successful (Session active for 24h). Ready to scan.', 'success');
        if (window.showPortalToast) window.showPortalToast('Google Drive Connected Successfully', 'success');
    };

    if (gapi.client.getToken() === null) {
        tokenClient.requestAccessToken({ prompt: 'consent' });
    } else {
        tokenClient.requestAccessToken({ prompt: '' });
    }
}

function handleSignoutClick() {
    const token = (typeof gapi !== 'undefined' && gapi.client) ? gapi.client.getToken() : null;
    const savedToken = localStorage.getItem('gmiu_drive_token') || (token ? token.access_token : null);

    if (savedToken && typeof google !== 'undefined' && google.accounts && google.accounts.oauth2) {
        try {
            google.accounts.oauth2.revoke(savedToken);
        } catch (e) {
            console.warn("Token revoke error:", e);
        }
    }

    if (typeof gapi !== 'undefined' && gapi.client) {
        gapi.client.setToken(null);
    }
    localStorage.removeItem('gmiu_drive_token');
    localStorage.removeItem('gmiu_token_expires_at');

    const authBtn = document.getElementById('auth-btn');
    const signoutBtn = document.getElementById('signout-btn');
    const scanContainer = document.getElementById('scan-container');
    const downloadBtn = document.getElementById('download-btn');
    if (authBtn) authBtn.style.display = 'inline-flex';
    if (signoutBtn) signoutBtn.style.display = 'none';
    if (scanContainer) scanContainer.style.display = 'none';
    if (downloadBtn) downloadBtn.style.display = 'none';

    updateConnectionBadge(false, 'Disconnected');
    logConsole('Signed out successfully from Google Drive.', 'info');
    clearResults();
    if (window.showPortalToast) window.showPortalToast('Signed out of Google Drive', 'info');
}

function clearResults() {
    const emptyList = document.getElementById('empty-folders');
    const nonEmptyList = document.getElementById('non-empty-folders');
    const downloadBtn = document.getElementById('download-btn');
    const summaryCard = document.getElementById('summary-card');
    const emailSection = document.getElementById('email-section');
    const emptyState = document.getElementById('results-empty-state');

    if (emptyList) emptyList.innerHTML = '';
    if (nonEmptyList) nonEmptyList.innerHTML = '';
    if (downloadBtn) downloadBtn.style.display = 'none';
    if (summaryCard) summaryCard.style.display = 'none';
    if (emptyState) emptyState.style.display = 'flex';

    const tf = document.getElementById('total-folders-val');
    const td = document.getElementById('total-docs-val');
    const nef = document.getElementById('non-empty-folders-val');
    const ef = document.getElementById('empty-folders-val');
    if (tf) tf.innerText = '0';
    if (td) td.innerText = '0';
    if (nef) nef.innerText = '0';
    if (ef) ef.innerText = '0';

    if (emailSection) emailSection.style.display = 'none';
    if (typeof resetCCEmailsToDefault === 'function') {
        resetCCEmailsToDefault();
    }
    const ccInput = document.getElementById('ccSearch');
    if (ccInput) ccInput.value = '';

    reportData = [];
    scannedFoldersData = [];
    scannedRootFolderName = '';
}

// -------------------------------------------------------------
// Department Presets & 1-Click Handlers (facultyData.js)
// -------------------------------------------------------------
let currentDeptPreset = (localStorage.getItem("portal_dept") === "CE") ? "CE" : "IT";

function renderDeptPresets(dept) {
    currentDeptPreset = dept;
    const container = document.getElementById("dept-presets-container");
    if (!container) return;

    const tabIT = document.getElementById("tab-dept-it");
    const tabCE = document.getElementById("tab-dept-ce");
    if (tabIT) {
        tabIT.classList.toggle("active-dept-tab", dept === "IT");
        tabIT.classList.toggle("bg-[var(--color-primary)]", dept === "IT");
        tabIT.classList.toggle("text-white", dept === "IT");
    }
    if (tabCE) {
        tabCE.classList.toggle("active-dept-tab", dept === "CE");
        tabCE.classList.toggle("bg-[var(--color-primary)]", dept === "CE");
        tabCE.classList.toggle("text-white", dept === "CE");
    }

    if (typeof facultyData === "undefined") {
        container.innerHTML = `<div class="text-[var(--text-muted)] p-2.5 text-xs text-center">Loading faculty presets...</div>`;
        return;
    }

    // Filter faculty/admin items matching the department that have a NON-EMPTY driveLink property
    const deptItems = facultyData.filter(m => {
        const d = m.department || "";
        const driveLinkVal = (m.driveLink || "").trim();
        if (!driveLinkVal) return false;

        if (dept === "IT") {
            return (d === "Information Technology" || d.includes("IT") || m.id === "adminit" || m.id === "dc" || m.id === "sw");
        } else {
            return (d === "Computer Engineering" || d.includes("CE") || m.id === "admince" || m.id === "dc" || m.id === "eu");
        }
    });

    // Sort department master admin item to the top
    const targetAdminId = (dept === "CE") ? "admince" : "adminit";
    deptItems.sort((a, b) => {
        if (a.id === targetAdminId) return -1;
        if (b.id === targetAdminId) return 1;
        return 0;
    });

    const currentVal = document.getElementById("folder-id-input") ? document.getElementById("folder-id-input").value.trim() : "";

    if (deptItems.length === 0) {
        container.innerHTML = `
            <div class="p-3 text-center text-xs text-[var(--text-muted)] rounded-xl border border-dashed border-black/10 dark:border-white/10 bg-black/5 dark:bg-white/5">
                No Drive links configured in <code>facultyData.js</code> for ${dept} Department.<br>
                <span class="opacity-80 text-[11px]">Please enter a Folder ID or Link in the input box below.</span>
            </div>`;
        return;
    }

    let html = "";
    deptItems.forEach(m => {
        const linkVal = (m.driveLink || "").trim();
        const isSelected = (currentVal === linkVal || extractFolderId(currentVal) === extractFolderId(linkVal));
        const safeName = (m.name || "").replace(/'/g, "\\'");
        const initials = m.initials || "IT";
        const isMasterAdmin = (m.id === "adminit" || m.id === "admince");

        if (isMasterAdmin) {
            const isMasterCe = (m.id === "admince");
            const masterTitle = m.semClass || (isMasterCe ? "CE 2026-27 ODD Master Folder" : "I.T 2026-27 ODD Master Folder");
            html += `
                <div class="preset-chip-card master-preset-card ${isSelected ? 'selected' : ''}" 
                     onclick="selectAndScanPreset('${linkVal}', '${m.id}', '${safeName}')" 
                     id="preset-card-${m.id}" 
                     title="${m.name} - ${masterTitle}">
                    <div class="chip-avatar-master">⚡</div>
                    <div class="chip-main-info flex-1 min-w-0">
                        <div class="chip-title font-bold text-xs truncate">${masterTitle}</div>
                        <div class="chip-sub text-[10px] font-semibold text-[var(--color-primary)]">⚡ Scan Whole ${dept} Department Folder</div>
                    </div>
                    <button type="button" class="chip-scan-btn master-scan-btn" title="1-Click Scan Whole Department">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        <span>Master Scan</span>
                    </button>
                </div>
            `;
        } else {
            const subText = m.semClass ? m.semClass.replace("Diploma ", "").replace("Degree ", "") : (m.designation || "Faculty");
            html += `
                <div class="preset-chip-card ${isSelected ? 'selected' : ''}" 
                     onclick="selectAndScanPreset('${linkVal}', '${m.id}', '${safeName}')" 
                     id="preset-card-${m.id}" 
                     title="${m.name} (${initials}) - ${subText}">
                    <div class="chip-avatar ${getAvatarClass(m)}">${initials}</div>
                    <div class="chip-main-info flex-1 min-w-0">
                        <div class="chip-title font-bold text-xs truncate">${initials}</div>
                        <div class="chip-sub text-[10px] truncate">${subText}</div>
                    </div>
                    <button type="button" class="chip-scan-btn" title="1-Click Scan">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        <span>Scan</span>
                    </button>
                </div>
            `;
        }
    });

    container.innerHTML = html;
}

function switchDeptTab(dept) {
    currentDeptPreset = dept;
    renderDeptPresets(dept);

    if (typeof facultyData !== "undefined") {
        const adminMember = facultyData.find(m => m.id === (dept === "CE" ? "admince" : "adminit") && (m.driveLink || "").trim() !== "") 
            || facultyData.find(m => m.id === "dc" && (m.driveLink || "").trim() !== "");
        if (adminMember && (adminMember.driveLink || "").trim() !== "") {
            const inputEl = document.getElementById("folder-id-input");
            if (inputEl) inputEl.value = adminMember.driveLink.trim();
        }
    }
}

function selectAndScanPreset(link, memberId, facultyName) {
    const inputEl = document.getElementById("folder-id-input");
    if (inputEl) {
        inputEl.value = link;
    }

    document.querySelectorAll(".preset-chip-card").forEach(card => card.classList.remove("selected"));
    const activeCard = document.getElementById(`preset-card-${memberId}`);
    if (activeCard) {
        activeCard.classList.add("selected");
    }

    if (typeof facultyData !== "undefined" && facultyName) {
        const member = facultyData.find(m => m.name === facultyName);
        if (member && document.getElementById("facultySearch")) {
            document.getElementById("facultySearch").value = member.name;
            document.getElementById("preparedBy").value = member.name;
            if (document.getElementById("facultyEmail")) document.getElementById("facultyEmail").value = member.email;
            if (document.getElementById("facultyPhone")) document.getElementById("facultyPhone").value = "+91 " + member.phone;
        }
    }

    startScan();
}

function selectAndScanMaster(link, dept) {
    const inputEl = document.getElementById("folder-id-input");
    if (inputEl) {
        inputEl.value = link;
    }

    switchDeptTab(dept);

    if (typeof facultyData !== "undefined") {
        const adminMember = facultyData.find(m => m.id === (dept === "CE" ? "admince" : "adminit"));
        if (adminMember && document.getElementById("facultySearch")) {
            document.getElementById("facultySearch").value = adminMember.name;
            document.getElementById("preparedBy").value = adminMember.name;
            if (document.getElementById("facultyEmail")) document.getElementById("facultyEmail").value = adminMember.email;
            if (document.getElementById("facultyPhone")) document.getElementById("facultyPhone").value = "+91 " + adminMember.phone;
        }
    }

    startScan();
}

// -------------------------------------------------------------
// Drive Links Modal Handlers
// -------------------------------------------------------------
let modalDeptFilter = "ALL";

function openDriveLinksModal() {
    const modal = document.getElementById("driveLinksModal");
    if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        renderModalDriveLinks();
    }
}

function closeDriveLinksModal() {
    const modal = document.getElementById("driveLinksModal");
    if (modal) {
        modal.classList.remove("flex");
        modal.classList.add("hidden");
    }
}

function filterModalByDept(dept) {
    modalDeptFilter = dept;
    document.querySelectorAll(".modal-pill").forEach(p => {
        p.classList.remove("bg-[var(--color-primary)]", "text-white");
        p.classList.add("text-[var(--text-muted)]");
    });
    const activePill = document.getElementById(`modal-pill-${dept.toLowerCase()}`);
    if (activePill) {
        activePill.classList.add("bg-[var(--color-primary)]", "text-white");
        activePill.classList.remove("text-[var(--text-muted)]");
    }
    renderModalDriveLinks();
}

function filterModalDriveLinks() {
    renderModalDriveLinks();
}

function renderModalDriveLinks() {
    const container = document.getElementById("modalDriveLinksList");
    if (!container || typeof facultyData === "undefined") return;

    const searchInput = document.getElementById("modalDriveSearch");
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";

    const items = facultyData.filter(member => {
        const memberDept = member.department || "";
        const driveLinkVal = (member.driveLink || "").trim();
        if (!driveLinkVal) return false;

        if (modalDeptFilter === "IT" && memberDept !== "Information Technology" && memberDept !== " IT & CE " && memberDept !== "Both") {
            return false;
        }
        if (modalDeptFilter === "CE" && memberDept !== "Computer Engineering" && memberDept !== " IT & CE " && memberDept !== "Both") {
            return false;
        }

        if (query) {
            const matchName = (member.name || "").toLowerCase().includes(query);
            const matchInitials = (member.initials || "").toLowerCase().includes(query);
            const matchEmail = (member.email || "").toLowerCase().includes(query);
            const matchDesg = (member.designation || "").toLowerCase().includes(query);
            return matchName || matchInitials || matchEmail || matchDesg;
        }
        return true;
    });

    if (items.length === 0) {
        container.innerHTML = `<div class="p-6 text-center text-xs text-[var(--text-muted)]">No drive links found for current search/filter.</div>`;
        return;
    }

    let html = "";
    items.forEach(member => {
        const linkVal = (member.driveLink || "").trim();
        const isDeptAdmin = (member.designation || "").includes("Admin") || (member.designation || "").includes("HOD");
        const badgeText = isDeptAdmin ? ((member.department || "").includes("Computer") ? "CE Dept" : "IT Dept") : (member.semClass ? member.semClass : (member.designation || "Faculty"));

        const safeName = (member.name || "").replace(/'/g, "\\'");
        html += `
            <div class="modal-drive-item flex items-center justify-between gap-3 p-3 rounded-2xl glass-pill hover:bg-black/5 dark:hover:bg-white/10 cursor-pointer transition" onclick="selectModalDriveLink('${linkVal}', '${safeName}')">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="modal-avatar ${getAvatarClass(member)} w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0">${member.initials}</div>
                    <div class="modal-item-info min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-xs text-[var(--text-primary)] leading-none">${member.name}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[var(--color-primary)]/10 text-[var(--color-primary)]">${badgeText}</span>
                        </div>
                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5 truncate">${member.email} &nbsp;·&nbsp; ${member.designation}</div>
                        <div class="text-[10px] font-mono text-[var(--text-muted)] opacity-75 truncate mt-0.5">${linkVal}</div>
                    </div>
                </div>
                <button type="button" class="h-8 px-3 rounded-xl glass-pill text-xs font-bold text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span>Scan</span>
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
}

function selectModalDriveLink(linkVal, facultyName) {
    closeDriveLinksModal();
    const inputEl = document.getElementById("folder-id-input");
    if (inputEl) {
        inputEl.value = linkVal;
    }

    if (typeof facultyData !== "undefined") {
        const member = facultyData.find(m => m.name === facultyName);
        if (member && document.getElementById("facultySearch")) {
            document.getElementById("facultySearch").value = member.name;
            document.getElementById("preparedBy").value = member.name;
            if (document.getElementById("facultyEmail")) document.getElementById("facultyEmail").value = member.email;
            if (document.getElementById("facultyPhone")) document.getElementById("facultyPhone").value = "+91 " + member.phone;
        }
    }

    startScan();
}

function extractFolderId(input) {
    if (!input) return "";
    const match = input.match(/folders\/([a-zA-Z0-9-_]+)/);
    return match ? match[1] : input.trim();
}

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

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return 'N/A';
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatMimetype(mimeType, name) {
    if (!mimeType) return 'Unknown File';
    if (mimeType.startsWith('application/vnd.google-apps.')) {
        const type = mimeType.split('.').pop();
        return 'Google ' + type.charAt(0).toUpperCase() + type.slice(1);
    }
    const dotIdx = name.lastIndexOf('.');
    if (dotIdx !== -1 && dotIdx < name.length - 1) {
        return name.substring(dotIdx + 1).toUpperCase() + ' File';
    }
    if (mimeType === 'application/pdf') return 'PDF Document';
    if (mimeType.startsWith('image/')) return 'Image File';
    if (mimeType.startsWith('video/')) return 'Video File';
    if (mimeType.startsWith('audio/')) return 'Audio File';
    if (mimeType.startsWith('text/')) return 'Text Document';
    return mimeType;
}

function getUniqueSheetName(name, existingNames) {
    let sanitized = name.replace(/[\\\/\?\*\[\]\:]/g, ' ');
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

// -------------------------------------------------------------
// Live Console Logging & Modal Scanner
// -------------------------------------------------------------
function logConsole(message, type = 'info') {
    const statusEl = document.getElementById('status');
    if (!statusEl) return;
    const line = document.createElement('div');
    line.className = `status-line ${type}`;
    const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    line.innerHTML = `<span class="timestamp">[${timeStr}]</span> ${message}`;
    statusEl.appendChild(line);

    // Auto-scroll both #status container and modal body container to the bottom
    statusEl.scrollTop = statusEl.scrollHeight;
    const modalBody = document.getElementById('scan-console-modal-body');
    if (modalBody) {
        modalBody.scrollTop = modalBody.scrollHeight;
    }
    if (statusEl.parentElement) {
        statusEl.parentElement.scrollTop = statusEl.parentElement.scrollHeight;
    }

    try {
        line.scrollIntoView({ behavior: 'smooth', block: 'end' });
    } catch (e) {
        // Fallback
    }
}

function openScanConsoleModal() {
    const modal = document.getElementById('scanConsoleModal');
    const modalBody = document.getElementById('scan-console-modal-body');
    const statusEl = document.getElementById('status');
    if (modal && modalBody && statusEl) {
        modalBody.appendChild(statusEl);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-open');
        statusEl.scrollTop = statusEl.scrollHeight;
        modalBody.scrollTop = modalBody.scrollHeight;
    }
}

function closeScanConsoleModal() {
    const modal = document.getElementById('scanConsoleModal');
    const inlineContainer = document.getElementById('inline-console-wrapper');
    const statusEl = document.getElementById('status');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    document.body.classList.remove('modal-open');
    if (inlineContainer && statusEl && statusEl.parentElement !== inlineContainer) {
        inlineContainer.appendChild(statusEl);
        statusEl.scrollTop = statusEl.scrollHeight;
    }
}

function setScanButtonScanning(isScanning) {
    const scanBtn = document.getElementById('scan-btn');
    if (!scanBtn) return;
    scanBtn.disabled = isScanning;
    if (isScanning) {
        scanBtn.innerHTML = `
            <svg class="spin-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
            </svg>
            <span id="scan-btn-text">Scanning...</span>
        `;
        scanBtn.classList.add('opacity-80', 'cursor-not-allowed');
    } else {
        scanBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <span>Scan Folders</span>
        `;
        scanBtn.classList.remove('opacity-80', 'cursor-not-allowed');
    }
}

function updateScanButtonText(text) {
    const btnTextEl = document.getElementById('scan-btn-text');
    if (btnTextEl) {
        btnTextEl.innerText = text;
    }
}

// -------------------------------------------------------------
// Core Google Drive Recursive Scanner
// -------------------------------------------------------------
async function startScan() {
    clearResults();
    const inputVal = document.getElementById('folder-id-input').value;
    const parentFolderId = extractFolderId(inputVal);

    if (!parentFolderId) {
        alert('Please provide a valid Folder ID or URL');
        return;
    }

    const statusEl = document.getElementById('status');
    if (statusEl) statusEl.innerHTML = '';
    setScanButtonScanning(true);
    openScanConsoleModal();

    // Show summary card immediately so user sees live metric updates line-by-line
    const summaryCard = document.getElementById('summary-card');
    const emptyState = document.getElementById('results-empty-state');
    if (summaryCard) summaryCard.style.display = 'grid';
    if (emptyState) emptyState.style.display = 'none';

    logConsole('🚀 Initializing Google Drive recursive folder scanner...', 'info');

    let totalDocs = 0;
    let totalActivityFolders = 0;
    let totalNonEmptyActivityFolders = 0;
    let totalEmptyActivityFolders = 0;
    let classFolderCount = 0;
    let classFolders = [];

    // 1. Determine Class Level target depth based on root parent folder name
    let targetClassDepth = 3; // Default for root -> course -> semester -> class
    let rootName = "Root";

    async function findClassFolders(folderId, currentPath, depth) {
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

        if (depth === targetClassDepth || subfolders.length === 0) {
            const folderName = currentPath || rootName;
            classFolders.push({
                id: folderId,
                name: folderName
            });
            logConsole(`🎯 Target directory identified: "${folderName}"`, 'success');
            return;
        }

        for (const subfolder of subfolders) {
            const nextPath = currentPath ? `${currentPath} > ${subfolder.name}` : subfolder.name;
            logConsole(`📁 Discovering structure: "${nextPath}"`, 'folder');
            await findClassFolders(subfolder.id, nextPath, depth + 1);
        }
    }

    async function scanClassContents(folderId, relativePath, classData) {
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

        const subName = relativePath || "Main Folder";
        logConsole(`   ↳ Checked "${subName}" — Found ${filesInFolder.length} file(s)`, filesInFolder.length > 0 ? 'file' : 'warn');

        if (relativePath) {
            classData.activities.push({
                name: relativePath,
                folderId: folderId,
                folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                files: filesInFolder
            });
        } else {
            if (filesInFolder.length > 0) {
                classData.activities.push({
                    name: "Direct Files",
                    folderId: folderId,
                    folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                    files: filesInFolder
                });
            }
        }

        if (!relativePath && subfolders.length === 0 && filesInFolder.length === 0) {
            classData.activities.push({
                name: "Main Folder",
                folderId: folderId,
                folderLink: `https://drive.google.com/drive/folders/${folderId}`,
                files: []
            });
        }

        for (const subfolder of subfolders) {
            const nextPath = relativePath ? `${relativePath} > ${subfolder.name}` : subfolder.name;
            await scanClassContents(subfolder.id, nextPath, classData);
        }
    }

    try {
        logConsole(`📡 Connecting to parent Google Drive ID: ${parentFolderId}...`, 'info');
        const rootMeta = await gapi.client.drive.files.get({
            fileId: parentFolderId,
            fields: 'name'
        });
        rootName = rootMeta.result.name || "Root";
        scannedRootFolderName = rootName;

        logConsole(`🏷️ Scanned Root Directory Name: "${rootName}"`, 'info');

        const rootNameLower = rootName.toLowerCase();
        if (rootNameLower.includes("semester") || rootNameLower.includes("sem ")) {
            targetClassDepth = 1;
        } else if (rootNameLower === "b.tech" || rootNameLower === "diploma" || rootNameLower === "btech") {
            targetClassDepth = 2;
        } else if (rootNameLower.includes("clss") || rootNameLower.includes("class")) {
            targetClassDepth = 0;
        }

        logConsole(`🔍 Scanning folder structure (Target Depth level = ${targetClassDepth})...`, 'info');
        await findClassFolders(parentFolderId, "", 0);

        if (classFolders.length === 0) {
            logConsole(`⚠️ No class folders found inside this directory.`, 'warn');
            setScanButtonScanning(false);
            return;
        }

        logConsole(`📦 Found ${classFolders.length} class folder(s). Executing content inspection...`, 'info');

        for (let i = 0; i < classFolders.length; i++) {
            const classFolder = classFolders[i];
            updateScanButtonText(`Scanning (${i + 1}/${classFolders.length})...`);
            const hintEl = document.getElementById('scan-console-modal-hint');
            if (hintEl) hintEl.innerText = `Scanning (${i + 1}/${classFolders.length}): ${classFolder.name}...`;

            logConsole(`🔍 [${i + 1}/${classFolders.length}] Inspecting "${classFolder.name}"...`, 'info');

            const classData = {
                id: classFolder.id,
                name: classFolder.name,
                activities: []
            };

            await scanClassContents(classFolder.id, "", classData);

            const totalAct = classData.activities.length;
            const emptyAct = classData.activities.filter(a => a.files.length === 0).length;
            const filledAct = totalAct - emptyAct;
            const docsCount = classData.activities.reduce((acc, a) => acc + a.files.length, 0);

            totalDocs += docsCount;
            totalActivityFolders += totalAct;
            totalNonEmptyActivityFolders += filledAct;
            totalEmptyActivityFolders += emptyAct;
            classFolderCount++;

            // Live update summary counters
            const tf = document.getElementById('total-folders-val');
            const td = document.getElementById('total-docs-val');
            const nef = document.getElementById('non-empty-folders-val');
            const ef = document.getElementById('empty-folders-val');
            if (tf) tf.innerText = totalActivityFolders;
            if (td) td.innerText = totalDocs;
            if (nef) nef.innerText = totalNonEmptyActivityFolders;
            if (ef) ef.innerText = totalEmptyActivityFolders;

            // Render result card in UI
            renderClassResult(classFolder.name, classData.activities);

            logConsole(`✅ [${i + 1}/${classFolders.length}] Finished "${classFolder.name}" — ${docsCount} docs, ${filledAct}/${totalAct} subfolders filled`, 'success');

            scannedFoldersData.push(classData);

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

        // Summary rows for report
        reportData.push({ "Folder Name": "", "Status": "", "File Count": "", "File Names": "" });
        reportData.push({ "Folder Name": "SUMMARY REPORT", "Status": "", "File Count": "", "File Names": "" });
        reportData.push({ "Folder Name": "Total Classes Scanned", "Status": "", "File Count": classFolderCount, "File Names": "" });
        reportData.push({ "Folder Name": "Total Activity Subfolders Scanned", "Status": "", "File Count": totalActivityFolders, "File Names": "" });
        reportData.push({ "Folder Name": "Total Scanned Documents", "Status": "", "File Count": totalDocs, "File Names": "" });
        reportData.push({ "Folder Name": "Non-Empty Activity Subfolders", "Status": "", "File Count": totalNonEmptyActivityFolders, "File Names": "" });
        reportData.push({ "Folder Name": "Empty Activity Subfolders", "Status": "", "File Count": totalEmptyActivityFolders, "File Names": "" });

        // Reveal email section & render default CC tags
        const emailSection = document.getElementById('email-section');
        if (emailSection) emailSection.style.display = 'block';
        if (typeof resetCCEmailsToDefault === 'function') {
            resetCCEmailsToDefault();
        }

        logConsole('🎉 Scan Complete! You can now download the Excel report or send it via email.', 'success');
        const hintEl = document.getElementById('scan-console-modal-hint');
        if (hintEl) hintEl.innerText = '🎉 Scan Complete! Closing popup modal...';

        const downloadBtn = document.getElementById('download-btn');
        if (downloadBtn) downloadBtn.style.display = 'inline-flex';

        if (window.showPortalToast) window.showPortalToast('Scan Complete! ' + totalDocs + ' documents found.', 'success');

    } catch (err) {
        console.error("Scan error:", err);
        if (err.status === 401 || (err.result && err.result.error && err.result.error.code === 401)) {
            localStorage.removeItem('gmiu_drive_token');
            localStorage.removeItem('gmiu_token_expires_at');
            if (typeof gapi !== 'undefined' && gapi.client) gapi.client.setToken(null);
            const authBtn = document.getElementById('auth-btn');
            const signoutBtn = document.getElementById('signout-btn');
            const scanContainer = document.getElementById('scan-container');
            if (authBtn) authBtn.style.display = 'inline-flex';
            if (signoutBtn) signoutBtn.style.display = 'none';
            if (scanContainer) scanContainer.style.display = 'none';
            updateConnectionBadge(false, 'Session Expired');
            logConsole('❌ Session expired. Please click Authorize & Connect to reconnect.', 'warn');
            if (window.showPortalToast) window.showPortalToast('Session expired. Please reconnect Google Drive.', 'error');
        } else {
            logConsole('❌ Error processing request. Check console log or API Permissions.', 'warn');
            if (window.showPortalToast) window.showPortalToast('Error processing request. Check permissions.', 'error');
        }
    } finally {
        setScanButtonScanning(false);
        setTimeout(closeScanConsoleModal, 1200);
    }
}

function renderClassResult(className, activities) {
    const emptyContainer = document.getElementById('empty-folders');
    const nonEmptyContainer = document.getElementById('non-empty-folders');

    const card = document.createElement('div');
    card.className = 'folder-result-card p-3.5 rounded-2xl glass-card transition-all duration-200 border border-white/60 dark:border-white/10';

    const total = activities.length;
    const emptyActCount = activities.filter(a => a.files.length === 0).length;
    const filledCount = total - emptyActCount;
    const isTotallyEmpty = (filledCount === 0);

    let activitiesHtml = '<div class="activity-tree-list mt-3 space-y-2 border-t border-black/5 dark:border-white/10 pt-2.5">';
    activities.forEach(activity => {
        const isActEmpty = activity.files.length === 0;
        const statusBadge = isActEmpty 
            ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">Empty</span>' 
            : `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">${activity.files.length} doc(s)</span>`;
        
        activitiesHtml += `
            <div class="activity-item p-2 rounded-xl bg-black/5 dark:bg-white/5 text-xs">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="w-2 h-2 rounded-full ${isActEmpty ? 'bg-rose-500' : 'bg-emerald-500'} shrink-0"></span>
                        <a href="${activity.folderLink}" target="_blank" class="font-bold text-[var(--text-primary)] hover:text-[var(--color-primary)] truncate" title="Open Google Drive folder">
                            ${activity.name}
                        </a>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        ${statusBadge}
                        <a href="${activity.folderLink}" target="_blank" class="text-[10px] font-bold text-[var(--color-primary)] hover:underline shrink-0" title="Open in Drive">
                            Open ↗
                        </a>
                    </div>
                </div>
        `;
        
        if (activity.files.length > 0) {
            activitiesHtml += '<div class="activity-file-list mt-1.5 pl-3 border-l-2 border-emerald-500/30 space-y-1">';
            activity.files.forEach(file => {
                activitiesHtml += `
                    <div class="flex items-center justify-between gap-2 text-[11px] text-[var(--text-muted)]">
                        <a href="${file.webViewLink}" target="_blank" class="hover:text-[var(--color-primary)] truncate font-mono" title="${file.name}">
                            📄 ${file.name}
                        </a>
                        <span class="text-[10px] opacity-75 shrink-0">${formatBytes(file.size)}</span>
                    </div>
                `;
            });
            activitiesHtml += '</div>';
        }
        activitiesHtml += '</div>';
    });
    activitiesHtml += '</div>';

    card.innerHTML = `
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0">
                <h4 class="font-bold text-sm text-[var(--text-primary)] truncate">${className}</h4>
                <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
                    Activities: <strong class="text-[var(--text-primary)]">${filledCount} / ${total}</strong> filled &nbsp;·&nbsp; <strong class="${emptyActCount > 0 ? 'text-rose-500' : 'text-emerald-500'}">${emptyActCount}</strong> empty
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 ${isTotallyEmpty ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30' : (emptyActCount > 0 ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30')}">
                ${isTotallyEmpty ? '🔴 Empty' : (emptyActCount > 0 ? '🟡 Partial' : '🟢 Filled')}
            </span>
        </div>
        ${activitiesHtml}
    `;

    if (isTotallyEmpty) {
        if (emptyContainer) emptyContainer.appendChild(card);
    } else {
        if (nonEmptyContainer) nonEmptyContainer.appendChild(card);
    }
}

// -------------------------------------------------------------
// SheetJS Excel Generator with Hyperlinks & Detailed Sheets
// -------------------------------------------------------------
function downloadExcelReport() {
    if (scannedFoldersData.length === 0) {
        alert("No data to download. Please scan a folder first.");
        return;
    }

    if (typeof XLSX === 'undefined') {
        alert("SheetJS library is not loaded yet. Please try again.");
        return;
    }

    function drawProgressBar(value, total) {
        if (total === 0) return "[░░░░░░░░░░] 0%";
        const percentage = Math.round((value / total) * 100);
        const filledBlocks = Math.round(percentage / 10);
        const emptyBlocks = 10 - filledBlocks;
        const bar = "█".repeat(filledBlocks) + "░".repeat(emptyBlocks);
        return `[${bar}] ${percentage}%`;
    }

    const workbook = XLSX.utils.book_new();

    // 1. Summary Sheet
    const summaryRows = [];
    summaryRows.push(["CE & IT DEPARTMENT - GOOGLE DRIVE FOLDER SCAN REPORT"]);
    summaryRows.push(["Report Generated At:", new Date().toLocaleString()]);
    summaryRows.push([]);

    const totalClassFolders = scannedFoldersData.length;
    let totalActivityFolders = 0;
    let totalNonEmptyActivityFolders = 0;
    let totalEmptyActivityFolders = 0;
    let totalDocs = 0;

    scannedFoldersData.forEach(classFolder => {
        const totalAct = classFolder.activities.length;
        const emptyAct = classFolder.activities.filter(a => a.files.length === 0).length;
        const filledAct = totalAct - emptyAct;
        totalDocs += classFolder.activities.reduce((acc, a) => acc + a.files.length, 0);
        totalActivityFolders += totalAct;
        totalNonEmptyActivityFolders += filledAct;
        totalEmptyActivityFolders += emptyAct;
    });

    summaryRows.push(["METRICS SUMMARY"]);
    summaryRows.push(["Total Classes Scanned", totalClassFolders]);
    summaryRows.push(["Total Subfolders Scanned", totalActivityFolders]);
    summaryRows.push(["Total Scanned Documents", totalDocs]);
    summaryRows.push(["Non-Empty Subfolders", totalNonEmptyActivityFolders]);
    summaryRows.push(["Empty Subfolders (Action Required)", totalEmptyActivityFolders]);
    summaryRows.push(["Overall Completion Rate", drawProgressBar(totalNonEmptyActivityFolders, totalActivityFolders)]);
    summaryRows.push([]);

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

    const existingNames = new Set(["overall analysis"]);

    // 2. Individual Class Sheets
    scannedFoldersData.forEach(classFolder => {
        const pathParts = classFolder.name.split(" > ");
        const baseName = pathParts[pathParts.length - 1] || classFolder.name;
        const sheetName = getUniqueSheetName(baseName, existingNames);
        
        const folderRows = [];

        const totalAct = classFolder.activities.length;
        const emptyAct = classFolder.activities.filter(a => a.files.length === 0).length;
        const filledAct = totalAct - emptyAct;

        let statusLabel = "🟢 FILLED";
        if (filledAct === 0) statusLabel = "🔴 EMPTY";
        else if (emptyAct > 0) statusLabel = "🟡 PARTIAL";

        folderRows.push(["FOLDER ANALYSIS REPORT"]);
        folderRows.push(["Class Folder Path:", classFolder.name]);
        folderRows.push(["Status:", statusLabel]);
        folderRows.push(["Activities Progress:", `${filledAct} / ${totalAct} Activities Filled`]);
        folderRows.push(["Completion Bar:", drawProgressBar(filledAct, totalAct)]);
        folderRows.push([]);

        folderRows.push(["S.No.", "Activity / Subfolder Name", "Activity Status", "File Name", "MIME Type", "Size", "Uploaded Date", "Google Drive Link"]);

        const dataRows = [];

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

        dataRows.forEach((row, index) => {
            if (row.linkUrl) {
                const rowIndex = 7 + index;
                const cellRef = XLSX.utils.encode_cell({ r: rowIndex, c: 7 });
                ws[cellRef] = {
                    t: 's',
                    v: row.linkText,
                    l: { Target: row.linkUrl, Tooltip: row.linkText === 'Open Folder' ? 'Click to open folder' : 'Click to open file' }
                };
            }
        });

        XLSX.utils.book_append_sheet(workbook, ws, sheetName);
    });

    XLSX.writeFile(workbook, "Drive_Folder_Scan_Report.xlsx");
    if (window.showPortalToast) window.showPortalToast('Excel Report Downloaded', 'success');
}

// -------------------------------------------------------------
// Faculty Search & Auto-fill Logic
// -------------------------------------------------------------
let selectedFaculty = null;

function initFacultySearch() {
    const facultySearch = document.getElementById("facultySearch");
    const preparedBy = document.getElementById("preparedBy");
    const facultyDropdownList = document.getElementById("facultyDropdownList");
    const facultyError = document.getElementById("facultyError");
    const facultyEmail = document.getElementById("facultyEmail");
    const facultyPhone = document.getElementById("facultyPhone");

    if (!facultySearch || !facultyDropdownList || typeof facultyData === "undefined") return;

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
        const query = facultySearch.value.toLowerCase().replace("prof.", "").replace("mr.", "").replace("dr.", "").trim();
        const filtered = facultyData.filter(member =>
            (member.name || '').toLowerCase().includes(query) ||
            (member.initials || '').toLowerCase().includes(query) ||
            (member.email || '').toLowerCase().includes(query) ||
            (member.designation || '').toLowerCase().includes(query) ||
            (member.empId || '').toLowerCase().includes(query)
        );
        renderDropdown(filtered);
    }

    function renderDropdown(list) {
        facultyDropdownList.innerHTML = "";
        if (list.length === 0) {
            facultyDropdownList.innerHTML = `<div class="p-3 text-xs text-[var(--text-muted)] text-center">No faculty members found</div>`;
            return;
        }

        list.forEach(member => {
            const item = document.createElement("div");
            item.className = "dropdown-item flex items-center gap-2.5 p-2 rounded-xl hover:bg-black/5 dark:hover:bg-white/10 cursor-pointer transition";
            item.innerHTML = `
                <div class="item-avatar ${getAvatarClass(member)} w-7 h-7 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0">${member.initials}</div>
                <div class="item-info min-w-0 flex-1">
                    <div class="font-bold text-xs text-[var(--text-primary)] leading-tight truncate">${member.name} <span class="opacity-75 font-normal text-[11px]">(${member.initials})</span></div>
                    <div class="text-[10px] text-[var(--text-muted)] truncate mt-0.5">${member.email} &nbsp;·&nbsp; ${member.designation || 'Faculty'}</div>
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

        if (facultyEmail) facultyEmail.value = member.email;
        if (facultyPhone) facultyPhone.value = "+91 " + (member.phone || "");

        facultySearch.classList.remove("input-error", "border-rose-500");
        if (facultyError) facultyError.classList.add("hidden");

        facultyDropdownList.classList.remove("show");

        const isCe = (member.initials === "DRC" || member.name.includes("Dhaval Chandarana")) 
            ? (localStorage.getItem("portal_dept") === "CE") 
            : (member.department === "Computer Engineering");
        document.title = isCe ? "Drive Folder Scanner — CE Department" : "Drive Folder Scanner — IT Department";
    }

    function clearSelection() {
        preparedBy.value = "";
        selectedFaculty = null;
        if (facultyEmail) facultyEmail.value = "";
        if (facultyPhone) facultyPhone.value = "";
    }

    function validateSearchInput() {
        const val = facultySearch.value.trim();
        if (val === "") {
            clearSelection();
            return;
        }

        const valLower = val.toLowerCase();
        const match = facultyData.find(m => 
            (m.name || '').toLowerCase() === valLower || 
            (m.initials || '').toLowerCase() === valLower || 
            (m.email || '').toLowerCase() === valLower
        );
        if (match) {
            selectFaculty(match);
        } else {
            const partialMatches = facultyData.filter(m => 
                (m.name || '').toLowerCase().includes(valLower) || 
                (m.initials || '').toLowerCase().includes(valLower) || 
                (m.email || '').toLowerCase().includes(valLower)
            );
            if (partialMatches.length === 1) {
                selectFaculty(partialMatches[0]);
            } else {
                clearSelection();
                facultySearch.value = "";
            }
        }
    }
}

// -------------------------------------------------------------
// CC Emails Multi-Select Dropdown Logic
// -------------------------------------------------------------
const defaultCCEmails = [
    "drchandarana@gmiu.edu.in", // HOD (Prof. Dhaval Chandarana)
    "sbchauhan@gmiu.edu.in",    // Incharge HOD IT (Prof. Shwetaba Chauhan)
    "ehunagar@gmiu.edu.in"      // Incharge HOD CE (Prof. Ekta Unagar)
];
let selectedCCEmails = [...defaultCCEmails];

function getCompactDesignation(desg) {
    if (!desg) return "";
    const lower = desg.toLowerCase();
    if (lower === "hod" || lower.includes("both")) return "HOD";
    if (lower.includes("incharge hod it")) return "HOD IT";
    if (lower.includes("incharge hod ce")) return "HOD CE";
    if (lower.includes("sub incharge hod it")) return "Sub HOD IT";
    if (lower.includes("sub incharge hod ce")) return "Sub HOD CE";
    if (lower.includes("assistant professor")) return "Asst. Prof";
    if (lower.includes("associate professor")) return "Assoc. Prof";
    if (lower.includes("teaching assistant")) return "TA";
    if (lower.includes("lecturer")) return "Lecturer";
    return desg;
}

function updateCCTagsUI() {
    const ccTagsContainer = document.getElementById("ccTagsContainer");
    if (!ccTagsContainer) return;

    ccTagsContainer.innerHTML = "";
    selectedCCEmails.forEach(email => {
        const member = (typeof facultyData !== "undefined") ? facultyData.find(m => m.email.toLowerCase() === email.toLowerCase()) : null;
        let displayName = email;
        let tooltipText = email;
        if (member) {
            const shortDesg = getCompactDesignation(member.designation);
            displayName = shortDesg ? `${member.name} (${shortDesg})` : member.name;
            tooltipText = `${member.name} — ${member.designation || ''} (${email})`;
        }

        const tag = document.createElement("div");
        tag.className = "cc-tag inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-[var(--color-primary)]/15 text-[var(--text-primary)] border border-[var(--color-primary)]/30 shrink-0";
        tag.title = tooltipText;
        tag.innerHTML = `
            <span class="truncate max-w-[150px] sm:max-w-[200px]">${displayName}</span>
            <button type="button" class="cc-tag-remove w-4 h-4 rounded-full flex items-center justify-center text-rose-500 hover:bg-rose-500 hover:text-white transition cursor-pointer" title="Remove">&times;</button>
        `;
        tag.querySelector(".cc-tag-remove").addEventListener("click", function (e) {
            e.stopPropagation();
            removeCCTag(email);
        });
        ccTagsContainer.appendChild(tag);
    });
}

function removeCCTag(email) {
    selectedCCEmails = selectedCCEmails.filter(e => e !== email);
    updateCCTagsUI();
    const ccSearch = document.getElementById("ccSearch");
    if (ccSearch) {
        ccSearch.focus();
        if (typeof filterCCEmails === "function") filterCCEmails();
    }
}

function resetCCEmailsToDefault() {
    selectedCCEmails = [...defaultCCEmails];
    updateCCTagsUI();
}

let filterCCEmails = null;

function initCCEmailsSearch() {
    const ccSearch = document.getElementById("ccSearch");
    const ccDropdownList = document.getElementById("ccDropdownList");
    const ccTagsContainer = document.getElementById("ccTagsContainer");
    if (!ccSearch || !ccDropdownList) return;

    const ccSelectWrap = ccSearch.closest(".cc-select-wrap");

    if (ccSelectWrap) {
        ccSelectWrap.addEventListener("click", function (e) {
            if (e.target === ccSelectWrap || e.target === ccTagsContainer) {
                ccSearch.focus();
            }
        });
    }

    updateCCTagsUI();

    if (typeof facultyData !== "undefined") {
        renderCCDropdown(facultyData);
    }

    ccSearch.addEventListener("focus", function () {
        ccDropdownList.classList.add("show");
        if (filterCCEmails) filterCCEmails();
    });

    ccSearch.addEventListener("input", function () {
        ccDropdownList.classList.add("show");
        if (filterCCEmails) filterCCEmails();
    });

    ccSearch.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && ccSearch.value === "" && selectedCCEmails.length > 0) {
            removeCCTag(selectedCCEmails[selectedCCEmails.length - 1]);
        }
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".cc-select-wrap")) {
            ccDropdownList.classList.remove("show");
            ccSearch.value = "";
        }
    });

    filterCCEmails = function() {
        if (typeof facultyData === 'undefined') return;
        const query = ccSearch.value.toLowerCase().replace("prof.", "").replace("mr.", "").replace("dr.", "").trim();
        const available = facultyData.filter(member => !selectedCCEmails.includes(member.email));

        const filtered = available.filter(member =>
            (member.email || '').toLowerCase().includes(query) ||
            (member.name || '').toLowerCase().includes(query) ||
            (member.initials || '').toLowerCase().includes(query)
        );

        renderCCDropdown(filtered);
    };

    function renderCCDropdown(list) {
        ccDropdownList.innerHTML = "";
        if (list.length === 0) {
            ccDropdownList.innerHTML = `<div class="p-3 text-xs text-[var(--text-muted)] text-center">No matching emails found</div>`;
            return;
        }

        list.forEach(member => {
            const item = document.createElement("div");
            item.className = "dropdown-item flex items-center gap-2.5 p-2 rounded-xl hover:bg-black/5 dark:hover:bg-white/10 cursor-pointer transition";
            item.innerHTML = `
                <div class="item-avatar ${getAvatarClass(member)} w-7 h-7 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0">${member.initials}</div>
                <div class="item-info min-w-0 flex-1">
                    <div class="font-bold text-xs text-[var(--text-primary)] leading-tight truncate">${member.email}</div>
                    <div class="text-[10px] text-[var(--text-muted)] truncate mt-0.5">${member.name} <span class="opacity-75">(${member.initials})</span> &nbsp;·&nbsp; ${member.department || "IT Department"}</div>
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
        if (filterCCEmails) filterCCEmails();
    }
}

// -------------------------------------------------------------
// Email Dispatch Handlers
// -------------------------------------------------------------
function getMemberDepartment() {
    let member = selectedFaculty;
    if (!member && typeof facultyData !== 'undefined') {
        const facEmailVal = (document.getElementById("facultyEmail") ? document.getElementById("facultyEmail").value : "").toLowerCase().trim();
        const preparedByVal = (document.getElementById("preparedBy") ? document.getElementById("preparedBy").value : "").toLowerCase().trim();
        const facSearchVal = (document.getElementById("facultySearch") ? document.getElementById("facultySearch").value : "").toLowerCase().trim();

        member = facultyData.find(m => 
            (facEmailVal && (m.email || '').toLowerCase() === facEmailVal) ||
            (preparedByVal && (m.name || '').toLowerCase() === preparedByVal) ||
            (facSearchVal && (m.name || '').toLowerCase().includes(facSearchVal))
        );
    }

    if (member) {
        if (member.initials === "DRC" || (member.name || '').includes("Dhaval Chandarana") || member.department === "Both") {
            return (localStorage.getItem("portal_dept") === "CE") ? "CE" : "IT";
        }
        if (member.department === "Computer Engineering") {
            return "CE";
        }
        if (member.department === "Information Technology") {
            return "IT";
        }
    }

    return (localStorage.getItem("portal_dept") === "CE") ? "CE" : "IT";
}

function initEmailSend() {
    const sendBtn = document.getElementById("sendBtn");
    if (!sendBtn) return;

    sendBtn.addEventListener("click", function () {
        const preparedByEl = document.getElementById("preparedBy");
        const preparedBy = preparedByEl ? preparedByEl.value : "";
        if (!preparedBy) {
            const facultySearch = document.getElementById("facultySearch");
            const facultyError = document.getElementById("facultyError");
            if (facultySearch) {
                facultySearch.classList.add("input-error", "border-rose-500");
                facultySearch.focus();
            }
            if (facultyError) facultyError.classList.remove("hidden");
            return;
        }

        if (reportData.length === 0) {
            alert("Please scan a folder first before sending the report.");
            return;
        }

        sendBtn.style.pointerEvents = "none";
        sendBtn.innerHTML = `
            <svg class="w-4 h-4 spin-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
            <span>Sending Report...</span>
        `;

        const activeDept = getMemberDepartment();
        const isCe = (activeDept === "CE");

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

        const facultyEmailEl = document.getElementById("facultyEmail");
        const recipientEmail = (facultyEmailEl ? facultyEmailEl.value : "") || (isCe ? "admincecse@gmiu.edu.in" : "adminit@gmiu.edu.in");

        const rawGreeting = selectedFaculty ? selectedFaculty.name : preparedBy;
        let formattedGreeting = rawGreeting ? rawGreeting.trim() : "Faculty Member";
        if (!/^(Prof\.|Dr\.|Mr\.|Ms\.|Mrs\.)/i.test(formattedGreeting)) {
            formattedGreeting = "Prof. " + formattedGreeting;
        }

        const folderMentionText = scannedRootFolderName ? ` for <strong>${scannedRootFolderName}</strong>` : ``;
        const emailSubject = `Google Drive Folder Scan Report` + (scannedRootFolderName ? ` - ${scannedRootFolderName}` : ``);

        const emailHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <style>
                    body, table, td, th, div, span, p, a, h1, h2 { font-family: ${window.PORTAL_FONT_FAMILY || "'PT Serif', Georgia, serif"} !important; }
                    body { background-color: #f8fafc; margin: 0; padding: 0; color: #334155; }
                    .email-container { max-width: 800px; margin: 20px auto; background: #ffffff; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
                    .header-banner { background: linear-gradient(135deg, #0b1530 0%, #0d1b38 100%); padding: 28px 24px; text-align: center; color: #ffffff; border-bottom: 4px solid #10b981; }
                    .header-banner h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
                    .content-body { padding: 32px 30px; }
                    .greeting { font-size: 19px; font-weight: 600; color: #0b1530; margin-bottom: 18px; }
                    .message { font-size: 16.5px; line-height: 1.7; color: #475569; margin-bottom: 28px; }
                    .metrics-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; text-align: center; width: 25%; }
                    .metrics-num { font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 4px; }
                    .metrics-label { font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
                    .results-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
                    .results-table th { background-color: #f1f5f9; color: #475569; padding: 10px 12px; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid #cbd5e1; }
                    .footer-container { background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-size: 14px; color: #64748b; line-height: 1.5; }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="header-banner">
                        <h1>Google Drive Folder Scan Report</h1>
                        ${scannedRootFolderName ? `<div style="font-size: 14px; margin-top: 6px; opacity: 0.85; font-weight: 500;">Scanned Directory: ${scannedRootFolderName}</div>` : ''}
                    </div>
                    <div class="content-body">
                        <div class="greeting">Dear ${formattedGreeting},</div>
                        <div class="message">
                            Please find the details of the latest <strong>Google Drive Folder Scan Report</strong>${folderMentionText} below, prepared by <strong>Mr. Dev K Dholakiya</strong>.<br><br>
                            Please upload the pending documents to the Google Drive as soon as possible.
                        </div>
                        
                        <h2 style="font-size: 16px; color: #0b1530; margin: 0 0 12px; border-left: 4px solid #10b981; padding-left: 8px; line-height: 1.2;">Scan Summary Metrics</h2>
                        <table style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 30px;">
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
                        <div style="overflow-x: auto;">
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
                    <div class="footer-container">
                        <p style="margin: 0; font-size: 14px; color: #64748b;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="https://engineering.gt.tc/" target="_blank" style="color: #2563eb; font-weight: 700;">ENGINEERING DEPT CE & IT</a>.</p>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #94a3b8;">&copy; 2026 ALL RIGHTS RESERVED.</p>
                    </div>
                </div>
            </body>
            </html>
        `;

        const emailEndpoint = (window.APP_BASE_URL ? window.APP_BASE_URL.replace(/\/?$/, '/') : './') + 'send-email';

        fetch(emailEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                to: recipientEmail,
                cc: selectedCCEmails,
                subject: emailSubject,
                html: emailHtml,
                dept: activeDept
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
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Sent Successfully!</span>
                `;
                sendBtn.style.background = "linear-gradient(135deg, #10b981 0%, #059669 100%)";
                if (window.showPortalToast) window.showPortalToast('Report Emailed Successfully!', 'success');
            } else {
                throw new Error(data.error || 'Server reported error');
            }
        })
        .catch(err => {
            console.error('Email send failed:', err);
            if (window.showPortalToast) window.showPortalToast('Email dispatch failed: ' + err.message, 'error');
            sendBtn.innerHTML = `
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <span>Send Failed</span>
            `;
            sendBtn.style.background = "linear-gradient(135deg, #ef4444 0%, #991b1b 100%)";
        })
        .finally(() => {
            setTimeout(() => {
                sendBtn.style.pointerEvents = "auto";
                sendBtn.style.background = "";
                sendBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M22 2L11 13" />
                        <path d="M22 2L15 22l-4-9-9-4 20-7z" />
                    </svg>
                    <span>Send Report via Email</span>
                `;
            }, 4000);
        });
    });
}

// -------------------------------------------------------------
// Synchronized Top Navbar Search Listener
// -------------------------------------------------------------
window.addEventListener('portal:search', (e) => {
    const q = (e.detail?.query || '').toLowerCase().trim();
    if (!q) {
        document.querySelectorAll('.folder-result-card').forEach(c => c.style.display = '');
        return;
    }
    document.querySelectorAll('.folder-result-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
    });
});

// -------------------------------------------------------------
// Global Window Exports for Inline HTML Handlers
// -------------------------------------------------------------
window.gapiLoaded = gapiLoaded;
window.gisLoaded = gisLoaded;
window.handleAuthClick = handleAuthClick;
window.handleSignoutClick = handleSignoutClick;
window.startScan = startScan;
window.downloadExcelReport = downloadExcelReport;
window.switchDeptTab = switchDeptTab;
window.selectAndScanPreset = selectAndScanPreset;
window.selectAndScanMaster = selectAndScanMaster;
window.openDriveLinksModal = openDriveLinksModal;
window.closeDriveLinksModal = closeDriveLinksModal;
window.filterModalByDept = filterModalByDept;
window.filterModalDriveLinks = filterModalDriveLinks;
window.selectModalDriveLink = selectModalDriveLink;
window.openScanConsoleModal = openScanConsoleModal;
window.closeScanConsoleModal = closeScanConsoleModal;

// -------------------------------------------------------------
// DOM Ready Initialization
// -------------------------------------------------------------
window.addEventListener('DOMContentLoaded', () => {
    renderDeptPresets(currentDeptPreset);
    initFacultySearch();
    initCCEmailsSearch();
    initEmailSend();
    maybeEnableButtons();
});

