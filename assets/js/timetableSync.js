/**
 * GMIU IT Department — Client-Side Timetable 1-Click Sync Handler
 * Supports password authentication prompt with automatic Light & Dark Theme matching.
 */

function promptSyncPasswordModal(onSuccessCallback) {
    let existingModal = document.getElementById('syncAuthOverlay');
    if (existingModal) existingModal.remove();

    const overlay = document.createElement('div');
    overlay.id = 'syncAuthOverlay';
    overlay.className = 'sync-auth-overlay-container';

    overlay.innerHTML = `
        <div class="sync-auth-card" id="syncAuthCard">
            <button type="button" id="closeSyncModalBtn" class="sync-auth-close-btn" title="Close">&times;</button>
            
            <div class="sync-auth-icon-wrap">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            
            <h2 class="sync-auth-title">CE & IT Portal</h2>
            <p class="sync-auth-subtitle">Authentication required to <span style="color:#ef4444; font-weight:700;">Sync Google Sheets</span>.</p>
            
            <form id="syncAuthForm">
                <div style="margin-bottom:20px; text-align:left;">
                    <label for="syncPasswordInput" class="sync-auth-label">Enter Password</label>
                    <div class="sync-auth-input-group">
                        <input type="password" id="syncPasswordInput" class="sync-auth-input" placeholder="Type password here..." required autofocus autocomplete="current-password">
                        <button type="button" id="syncTogglePwdBtn" class="sync-auth-toggle-pwd" title="Show/Hide Password">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="syncEyeIcon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" id="syncAuthSubmitBtn" class="sync-auth-submit-btn">
                    <span>Unlock & Sync</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </button>
                
                <div id="syncAuthErrorMsg" class="sync-auth-error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="syncAuthErrorText">Invalid password. Please try again.</span>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(overlay);

    const form = document.getElementById('syncAuthForm');
    const pwdInput = document.getElementById('syncPasswordInput');
    const toggleBtn = document.getElementById('syncTogglePwdBtn');
    const eyeIcon = document.getElementById('syncEyeIcon');
    const submitBtn = document.getElementById('syncAuthSubmitBtn');
    const errorBox = document.getElementById('syncAuthErrorMsg');
    const errorText = document.getElementById('syncAuthErrorText');
    const card = document.getElementById('syncAuthCard');
    const closeBtn = document.getElementById('closeSyncModalBtn');

    let pwdVisible = false;
    toggleBtn.addEventListener('click', function () {
        pwdVisible = !pwdVisible;
        pwdInput.type = pwdVisible ? 'text' : 'password';
        eyeIcon.innerHTML = pwdVisible ? `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        ` : `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        `;
    });

    closeBtn.addEventListener('click', function () {
        overlay.remove();
    });

    form.addEventListener('submit', function (evt) {
        evt.preventDefault();
        const pwd = pwdInput.value.trim();
        if (!pwd) return;

        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';

        fetch('verify-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password: pwd })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                overlay.remove();
                if (typeof onSuccessCallback === 'function') {
                    onSuccessCallback(pwd);
                }
            } else {
                errorText.textContent = data.error || 'Invalid password.';
                errorBox.style.display = 'flex';
                card.style.transform = 'scale(0.98)';
                setTimeout(() => card.style.transform = 'scale(1)', 150);
                pwdInput.value = '';
                pwdInput.focus();
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        })
        .catch(err => {
            errorText.textContent = 'Authentication request failed.';
            errorBox.style.display = 'flex';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        });
    });

    pwdInput.focus();
}

function fetchDirectFromAppsScript(webappUrl, targetType, providedPassword, callback) {
    const btnTexts = document.querySelectorAll('.quick-sync-text');
    const icons = document.querySelectorAll('.quick-sync-icon');
    
    btnTexts.forEach(el => el.textContent = 'Browser Downloading Sheet...');
    
    const url = webappUrl + (webappUrl.includes('?') ? '&' : '?') + 'target=' + encodeURIComponent(targetType) + '&_t=' + Date.now();
    
    fetch(url, { cache: 'no-store' })
    .then(res => {
        if (!res.ok) throw new Error('Apps Script Web App HTTP ' + res.status);
        return res.json();
    })
    .then(json => {
        if (json.success && json.base64) {
            btnTexts.forEach(el => el.textContent = 'Saving Sheet...');
            
            let postBody = 'action=save_base64&target=' + encodeURIComponent(targetType) +
                           '&password=' + encodeURIComponent(providedPassword || '') +
                           '&base64=' + encodeURIComponent(json.base64);
                           
            return fetch('sync-timetables?action=save_base64', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: postBody
            })
            .then(res => res.json())
            .then(saveRes => {
                icons.forEach(el => el.style.animation = 'none');
                if (saveRes.success) {
                    btnTexts.forEach(el => el.textContent = '✓ Updated!');
                    if (callback) callback(saveRes);
                    else setTimeout(() => location.reload(), 500);
                } else {
                    btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');
                    alert("⚠️ Save Error: " + (saveRes.message || 'Failed to save base64 binary.'));
                }
            });
        } else {
            icons.forEach(el => el.style.animation = 'none');
            btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');
            alert("⚠️ Google Apps Script Error: " + (json.error || 'Failed to fetch spreadsheet.'));
        }
    })
    .catch(err => {
        icons.forEach(el => el.style.animation = 'none');
        btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');
        alert("⚠️ Direct Sync Error: " + err.message);
    });
}

function quickSyncSheet(e, targetType, providedPassword) {
    if (e) e.preventDefault();

    if (!providedPassword) {
        promptSyncPasswordModal((validPassword) => {
            quickSyncSheet(null, targetType, validPassword);
        });
        return;
    }

    const action = targetType === 'student' ? 'sync_student' : (targetType === 'faculty' ? 'sync_faculty' : 'sync_all');
    
    const btnTexts = document.querySelectorAll('.quick-sync-text');
    const icons = document.querySelectorAll('.quick-sync-icon');
    
    let postBody = 'action=' + encodeURIComponent(action) + '&password=' + encodeURIComponent(providedPassword);
    
    btnTexts.forEach(el => el.textContent = 'Downloading...');
    icons.forEach(el => el.style.animation = 'spin 0.8s linear infinite');
    
    fetch('sync-timetables?action=' + encodeURIComponent(action), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: postBody
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Server HTTP Status ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        if (data.auth_required) {
            icons.forEach(el => el.style.animation = 'none');
            btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');
            promptSyncPasswordModal((validPassword) => {
                quickSyncSheet(null, targetType, validPassword);
            });
            return;
        }

        // Check if server cURL failed and requested client direct fallback
        if (data.results) {
            const key = targetType === 'student' ? 'student' : (data.results.faculty ? 'faculty' : 'student');
            const resObj = data.results[key];
            if (resObj && resObj.client_fallback && resObj.webapp_url) {
                fetchDirectFromAppsScript(resObj.webapp_url, resObj.target, providedPassword);
                return;
            }
        }

        icons.forEach(el => el.style.animation = 'none');
        btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');

        if (data.success) {
            btnTexts.forEach(el => el.textContent = '✓ Updated!');
            setTimeout(() => location.reload(), 500);
        } else {
            let errMsg = 'Failed to sync Google Sheet.';
            if (data.results) {
                const key = targetType === 'student' ? 'student' : (data.results.faculty ? 'faculty' : 'student');
                if (data.results[key]) errMsg = data.results[key].message;
            }
            alert("⚠️ " + errMsg);
        }
    })
    .catch(err => {
        icons.forEach(el => el.style.animation = 'none');
        btnTexts.forEach(el => el.textContent = 'Sync Live Sheet');
        alert("⚠️ Error connecting to server: " + err.message);
    });
}

// ── Auto-Sync Background Scheduler (60 Seconds) ──
(function initAutoSyncTimer() {
    const autoSyncIntervalMs = 60000; // 60 seconds interval
    
    setInterval(function() {
        fetch('sync-timetables?action=sync_all&auto=1', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: 'action=sync_all&auto=1'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('[AutoSync] Background Google Sheets live sync completed cleanly at ' + new Date().toLocaleTimeString());
            }
        })
        .catch(err => {
            console.warn('[AutoSync] Background sync check skipped:', err);
        });
    }, autoSyncIntervalMs);
})();
