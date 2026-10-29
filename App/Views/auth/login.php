<?php
$page_title = 'Authentication Required — CE & IT Portal';
$page_css = [
    'assets/css/portal.css',
    'assets/css/auth.css',
];
include 'head.php';
?>
    <div class="auth-overlay">
        <div class="auth-card" id="authCard">
            <div class="auth-icon-container">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            
            <h1 class="auth-title">CE & IT</h1>
            <p class="auth-subtitle">Authentication required to access <span class="auth-target-page"><?php echo htmlspecialchars($page_label); ?></span>.</p>
            
            <form id="authForm" onsubmit="return false;">
                <div class="auth-form-group">
                    <label for="authPassword" class="auth-label">Enter Password</label>
                    <div class="auth-input-wrapper">
                        <input type="password" id="authPassword" class="auth-input" placeholder="Type password here..." required autofocus autocomplete="current-password">
                        <button type="button" class="auth-toggle-pwd" id="authTogglePwd" title="Show/Hide Password">
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
            
            <a href="./" class="auth-back-link">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Portal Home
            </a>
        </div>
    </div>

    <script>
        const authForm = document.getElementById("authForm");
        const authCard = document.getElementById("authCard");
        const authPassword = document.getElementById("authPassword");
        const authTogglePwd = document.getElementById("authTogglePwd");
        const eyeIcon = document.getElementById("eyeIcon");
        const authSubmitBtn = document.getElementById("authSubmitBtn");
        const authErrorMsg = document.getElementById("authErrorMsg");
        const authErrorText = document.getElementById("authErrorText");

        let passwordVisible = false;
        authTogglePwd.addEventListener("click", function () {
            passwordVisible = !passwordVisible;
            if (passwordVisible) {
                authPassword.type = "text";
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                authPassword.type = "password";
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        });

        authForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const pwd = authPassword.value;
            if (!pwd) return;

            authPassword.classList.remove("input-error");
            authErrorMsg.style.display = "none";
            authCard.classList.remove("auth-shake");
            
            authSubmitBtn.disabled = true;
            const originalBtnHtml = authSubmitBtn.innerHTML;
            authSubmitBtn.innerHTML = `
                <svg class="auth-spinner" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right: 4px;">
                    <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.2)"></circle>
                    <path d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" fill="currentColor"></path>
                </svg>
                <span>Verifying...</span>
            `;

            try {
                const response = await fetch("verify-password.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ password: pwd })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                        return;
                    }
                    authErrorText.innerText = data.error || "Invalid password.";
                } else {
                    authErrorText.innerText = "Server error occurred.";
                }
            } catch (err) {
                authErrorText.innerText = "Network error occurred.";
            }

            authSubmitBtn.disabled = false;
            authSubmitBtn.innerHTML = originalBtnHtml;
            authPassword.classList.add("input-error");
            authErrorMsg.style.display = "flex";
            
            authCard.classList.remove("auth-shake");
            void authCard.offsetWidth; // Trigger reflow
            authCard.classList.add("auth-shake");
            authPassword.value = "";
            authPassword.focus();
        });
    </script>
    <?php include_once 'theme-toggle.php'; ?>
</body>
</html>
