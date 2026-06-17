/**
 * GMIU IT Department — Portal Security Module
 * Securely restricts access to protected pages (ctlactivity.html, ctldrive.html)
 */

(function () {
    // 1. Instant body hiding to prevent FOUC (Flash of Unauthenticated Content)
    // We append this style to the documentElement immediately (before body is parsed)
    const style = document.createElement('style');
    style.id = 'auth-hide-body-style';
    style.innerHTML = `
        body {
            overflow: hidden !important;
        }
        body > *:not(.auth-overlay):not(script):not(style):not(link) {
            display: none !important;
        }
    `;
    document.documentElement.appendChild(style);
})();

document.addEventListener("DOMContentLoaded", function () {

    // Determine current page name dynamically for display
    let rawTitle = document.title || "";
    let pageLabel = "this protected resource";
    if (rawTitle.toLowerCase().includes("ctl")) {
        pageLabel = "CTL Activity Dashboard";
    } else if (rawTitle.toLowerCase().includes("drive") || rawTitle.toLowerCase().includes("scanner")) {
        pageLabel = "Drive Folder Scanner";
    }

    // Inject CSS for the authentication UI
    const authStyles = document.createElement('style');
    authStyles.innerHTML = `
        .auth-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999999;
            background: radial-gradient(circle at 30% 30%, rgba(11, 21, 48, 0.98) 0%, rgba(6, 13, 31, 0.99) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Merriweather Sans', 'Segoe UI', sans-serif;
            padding: 20px;
            overflow-y: auto;
        }
        
        /* Glassmorphic card design */
        .auth-card {
            background: rgba(11, 21, 48, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.5), 
                0 0 0 1px rgba(255, 255, 255, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            text-align: center;
            position: relative;
            transform: scale(1);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s;
        }

        .auth-card.success-animation {
            transform: scale(0.92);
            opacity: 0;
        }

        /* Lock Icon Header */
        .auth-icon-container {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(192, 57, 43, 0.1);
            border: 1px solid rgba(192, 57, 43, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #f87171;
            box-shadow: 0 0 20px rgba(192, 57, 43, 0.2);
            position: relative;
        }

        .auth-icon-container::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1px solid rgba(192, 57, 43, 0.15);
            animation: auth-pulse 2s ease-in-out infinite;
        }

        @keyframes auth-pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0; }
        }

        .auth-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .auth-target-page {
            color: #f87171;
            font-weight: 600;
        }

        /* Styled inputs */
        .auth-form-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .auth-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: block;
        }

        .auth-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .auth-input {
            width: 100%;
            background: rgba(6, 13, 31, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            color: #ffffff;
            padding: 13px 44px 13px 16px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .auth-input:focus {
            border-color: #ef4444;
            box-shadow: 
                0 0 0 3px rgba(192, 57, 43, 0.25),
                inset 0 2px 4px rgba(0, 0, 0, 0.2);
            background: rgba(6, 13, 31, 0.8);
        }

        .auth-input.input-error {
            border-color: #ef4444;
            background: rgba(192, 57, 43, 0.05);
            box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.2);
        }

        /* Password show/hide toggle */
        .auth-toggle-pwd {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .auth-toggle-pwd:hover {
            color: #94a3b8;
        }

        /* Button design matching GMIU IT portal */
        .auth-btn {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 60%, #7f1d1d 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 20px rgba(192, 57, 43, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(192, 57, 43, 0.5);
        }

        .auth-btn:active {
            transform: translateY(1px);
        }

        .auth-btn:disabled {
            background: linear-gradient(135deg, #334155, #1e293b);
            color: #64748b;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Spin animation */
        @keyframes auth-spin {
            to { transform: rotate(360deg); }
        }
        .auth-spinner {
            animation: auth-spin 1s linear infinite;
        }

        /* Error text alert */
        .auth-error-msg {
            background: rgba(192, 57, 43, 0.1);
            border: 1px solid rgba(192, 57, 43, 0.25);
            border-radius: 6px;
            color: #fca5a5;
            font-size: 12.5px;
            padding: 10px 14px;
            margin-top: 18px;
            text-align: left;
            display: none;
            align-items: center;
            gap: 8px;
        }

        /* Shake animation on wrong password */
        @keyframes auth-shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .auth-shake {
            animation: auth-shake 0.4s ease-in-out;
        }

        /* Back to portal button */
        .auth-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-back-link:hover {
            color: #ffffff;
        }
    `;
    document.head.appendChild(authStyles);

    // Build the Auth Overlay DOM structure
    const overlay = document.createElement("div");
    overlay.className = "auth-overlay";
    overlay.innerHTML = `
        <div class="auth-card" id="authCard">
            <div class="auth-icon-container">
                <!-- Lock Icon -->
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            
            <h1 class="auth-title">GMIU IT Faculty Portal</h1>
            <p class="auth-subtitle">Authentication required to access the <span class="auth-target-page">${pageLabel}</span>.</p>
            
            <form id="authForm" onsubmit="return false;">
                <div class="auth-form-group">
                    <label for="authPassword" class="auth-label">Enter Password</label>
                    <div class="auth-input-wrapper">
                        <input type="password" id="authPassword" class="auth-input" placeholder="Type password here..." required autofocus autocomplete="current-password">
                        <button type="button" class="auth-toggle-pwd" id="authTogglePwd" title="Show/Hide Password">
                            <!-- Eye Icon -->
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="eyeIcon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn" id="authSubmitBtn">
                    <span>Unlock Page</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </button>
                
                <div class="auth-error-msg" id="authErrorMsg">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="authErrorText">Invalid password. Please try again.</span>
                </div>
            </form>
            
            <a href="index.html" class="auth-back-link">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Portal Home
            </a>
        </div>
    `;

    document.body.appendChild(overlay);

    const authForm = document.getElementById("authForm");
    const authCard = document.getElementById("authCard");
    const authPassword = document.getElementById("authPassword");
    const authTogglePwd = document.getElementById("authTogglePwd");
    const eyeIcon = document.getElementById("eyeIcon");
    const authSubmitBtn = document.getElementById("authSubmitBtn");
    const authErrorMsg = document.getElementById("authErrorMsg");
    const authErrorText = document.getElementById("authErrorText");

    // Focus input field instantly
    setTimeout(() => authPassword.focus(), 100);

    // Password Visibility Toggle Logic
    let passwordVisible = false;
    authTogglePwd.addEventListener("click", function () {
        passwordVisible = !passwordVisible;
        if (passwordVisible) {
            authPassword.type = "text";
            eyeIcon.innerHTML = `
                <!-- Crossed Eye Icon -->
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
        } else {
            authPassword.type = "password";
            eyeIcon.innerHTML = `
                <!-- Eye Icon -->
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `;
        }
    });

    // Configure the correct password for client-side fallback (normal text use)
    const correctPassword = "gmiu@it";

    // Handle Authentication Submission
    authForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        
        const pwd = authPassword.value;
        if (!pwd) return;

        // Reset error state
        authPassword.classList.remove("input-error");
        authErrorMsg.style.display = "none";
        authCard.classList.remove("auth-shake");
        
        // Show loading spinner in the submit button
        authSubmitBtn.disabled = true;
        const originalBtnHtml = authSubmitBtn.innerHTML;
        authSubmitBtn.innerHTML = `
            <svg class="auth-spinner" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right: 4px;">
                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.2)"></circle>
                <path d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" fill="currentColor"></path>
            </svg>
            <span>Verifying...</span>
        `;

        let isAuthSuccessful = false;
        let errorMessage = "Invalid password. Please try again.";

        try {
            // Plan A: Query server-side verify-password.php endpoint
            // Try fetching from the relative location (useful for XAMPP deployment)
            const response = await fetch("verify-password.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ password: pwd })
            });

            if (response.ok) {
                const data = await response.json();
                isAuthSuccessful = !!data.success;
                if (data.error) errorMessage = data.error;
            } else {
                // If endpoint exists but returned a HTTP error code, throw to enter fallback
                throw new Error("HTTP error: " + response.status);
            }

        } catch (err) {
            console.warn("verify-password.php endpoint unavailable or error occurred. Running client-side password validation fallback.");
            
            // Plan B: Client-Side Validation Fallback (Normal string comparison)
            if (pwd === correctPassword) {
                isAuthSuccessful = true;
            }
        }

        if (isAuthSuccessful) {
            // ── Authentication Success ──
            
            // Animate card scale/opacity fade out
            authCard.classList.add("success-animation");
            
            setTimeout(() => {
                // Remove the custom hiding styles to restore body content visibility
                const hideStyleEl = document.getElementById("auth-hide-body-style");
                if (hideStyleEl) hideStyleEl.remove();
                
                // Remove overlay modal from DOM
                overlay.remove();
                
                // Dispatch window resize event to let dashboard graphs, layout containers recalibrate
                window.dispatchEvent(new Event('resize'));
            }, 350);

        } else {
            // ── Authentication Failure ──
            authSubmitBtn.disabled = false;
            authSubmitBtn.innerHTML = originalBtnHtml;
            
            // Add shake animation and error formatting
            authPassword.classList.add("input-error");
            authErrorText.innerText = errorMessage;
            authErrorMsg.style.display = "flex";
            
            // Force redraw of card element to re-trigger CSS animations
            authCard.classList.remove("auth-shake");
            void authCard.offsetWidth; // Trigger reflow
            authCard.classList.add("auth-shake");
            
            authPassword.value = "";
            authPassword.focus();
        }
    });
});
