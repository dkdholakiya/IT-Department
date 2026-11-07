/**
 * GMIU IT Department — 6-Digit Email OTP Verification Frontend Module
 * Responsive, Adaptive Light/Dark Theme OTP Verification Modal with Instant Pop-Up & Compact Digit Inputs
 */

(function () {
    'use strict';

    // Inject Theme-Adaptive CSS styles into head
    function injectOtpStyles() {
        if (document.getElementById('gmiuOtpStyles')) return;

        const style = document.createElement('style');
        style.id = 'gmiuOtpStyles';
        style.textContent = `
            /* OTP Modal Backdrop & Responsive Dialog */
            #gmiuOtpModal .modal-dialog {
                max-width: 440px !important;
                width: 92% !important;
                margin: 2rem auto !important;
            }

            /* Unified Glassmorphism Theme OTP Modal Card */
            .otp-modal-content {
                background: var(--glass-surface-strong, rgba(255, 255, 255, 0.95)) !important;
                backdrop-filter: blur(28px) saturate(180%) !important;
                -webkit-backdrop-filter: blur(28px) saturate(180%) !important;
                border: 1.5px solid var(--glass-border, rgba(255, 255, 255, 0.8)) !important;
                border-radius: 1.75rem !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.22), 0 0 25px rgba(var(--color-primary-rgb, 16, 185, 129), 0.12) !important;
                color: var(--text-primary, #0f172a) !important;
                padding: 1.75rem 1.5rem !important;
            }

            body.theme-dark .otp-modal-content {
                background: rgba(15, 23, 42, 0.92) !important;
                border-color: rgba(var(--color-primary-rgb, 16, 185, 129), 0.25) !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 30px rgba(var(--color-primary-rgb, 16, 185, 129), 0.2) !important;
                color: #f1f5f9 !important;
            }

            .otp-modal-header {
                border-bottom: none !important;
            }

            .otp-icon-badge {
                width: 56px;
                height: 56px;
                background: rgba(var(--color-primary-rgb, 16, 185, 129), 0.12) !important;
                border: 2px solid var(--color-primary-light, #34d399) !important;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: var(--color-primary, #10b981) !important;
                box-shadow: 0 4px 16px rgba(var(--color-primary-rgb, 16, 185, 129), 0.25) !important;
                margin: 0 auto 12px auto;
            }

            .otp-modal-title {
                color: var(--text-primary, #0f172a) !important;
                font-weight: 800 !important;
                font-family: 'PT Serif', serif, sans-serif !important;
                letter-spacing: -0.02em;
            }

            body.theme-dark .otp-modal-title {
                color: #ffffff !important;
            }

            .otp-subtext {
                color: var(--text-muted, #64748b) !important;
                font-size: 12.5px !important;
            }

            .otp-info-email {
                display: inline-block;
                padding: 3px 12px;
                border-radius: 9999px;
                background: rgba(var(--color-primary-rgb, 16, 185, 129), 0.1) !important;
                border: 1px solid rgba(var(--color-primary-rgb, 16, 185, 129), 0.28) !important;
                color: var(--color-primary, #10b981) !important;
                font-weight: 700 !important;
                font-size: 12px !important;
                margin-top: 5px;
                word-break: break-all;
            }

            /* Compact 6-Digit Input Container */
            .otp-inputs-wrapper {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin: 18px 0 12px;
            }

            .otp-digit-box {
                width: 44px;
                height: 50px;
                font-size: 22px;
                font-weight: 800;
                text-align: center;
                border-radius: 12px;
                border: 1.5px solid rgba(0, 0, 0, 0.15) !important;
                background: #ffffff !important;
                color: var(--text-primary, #0f172a) !important;
                outline: none;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            }

            body.theme-dark .otp-digit-box {
                border-color: rgba(255, 255, 255, 0.15) !important;
                background: rgba(255, 255, 255, 0.06) !important;
                color: #ffffff !important;
            }

            .otp-digit-box:focus {
                border-color: var(--color-primary, #10b981) !important;
                background: #ffffff !important;
                box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb, 16, 185, 129), 0.25) !important;
                color: var(--text-primary, #0f172a) !important;
                transform: translateY(-2px);
            }

            body.theme-dark .otp-digit-box:focus {
                background: rgba(255, 255, 255, 0.1) !important;
                color: #ffffff !important;
            }

            .otp-digit-box.is-invalid {
                border-color: #ef4444 !important;
                color: #ef4444 !important;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
            }

            /* Mobile Responsiveness (< 420px) */
            @media (max-width: 420px) {
                #gmiuOtpModal .modal-dialog {
                    margin: 0.75rem auto !important;
                    width: 95% !important;
                }

                .otp-modal-content {
                    padding: 1.25rem 1rem !important;
                }

                .otp-inputs-wrapper {
                    gap: 5px;
                    margin: 12px 0;
                }

                .otp-digit-box {
                    width: 36px;
                    height: 44px;
                    font-size: 18px;
                    border-radius: 9px;
                }

                .otp-icon-badge {
                    width: 48px;
                    height: 48px;
                    font-size: 20px;
                    margin-bottom: 8px;
                }
            }

            /* Shake Animation for Incorrect OTP */
            @keyframes otpShake {
                0%, 100% { transform: translateX(0); }
                20%, 60% { transform: translateX(-6px); }
                40%, 80% { transform: translateX(6px); }
            }

            .otp-shake {
                animation: otpShake 0.35s ease-in-out;
            }

            /* Timer & Resend Link */
            .otp-resend-wrap {
                font-size: 12px;
                color: var(--text-muted, #64748b) !important;
                margin-top: 10px;
            }

            .otp-resend-btn {
                color: var(--color-primary, #10b981) !important;
                text-decoration: none;
                font-weight: 700;
                cursor: pointer;
                background: none;
                border: none;
                padding: 0;
            }

            .otp-resend-btn:hover {
                text-decoration: underline;
            }

            .otp-resend-btn:disabled {
                color: var(--text-muted, #94a3b8) !important;
                cursor: not-allowed;
                text-decoration: none;
                opacity: 0.6;
            }

            /* Status & Error Text */
            .otp-error-msg {
                font-size: 12.5px;
                font-weight: 600;
                min-height: 20px;
                margin-top: 6px;
            }

            .otp-btn-cancel {
                background: var(--glass-surface, rgba(255, 255, 255, 0.8)) !important;
                border: 1.5px solid var(--glass-border-light, rgba(0, 0, 0, 0.1)) !important;
                color: var(--text-primary, #0f172a) !important;
                border-radius: 9999px !important;
                font-weight: 600 !important;
                padding: 0.5rem 1.4rem !important;
                transition: all 0.2s ease !important;
            }

            .otp-btn-cancel:hover {
                background: rgba(0, 0, 0, 0.06) !important;
            }

            body.theme-dark .otp-btn-cancel {
                background: rgba(255, 255, 255, 0.08) !important;
                border-color: rgba(255, 255, 255, 0.15) !important;
                color: #f1f5f9 !important;
            }

            .otp-btn-submit {
                background: var(--color-primary, #10b981) !important;
                color: #ffffff !important;
                border: none !important;
                border-radius: 9999px !important;
                font-weight: 700 !important;
                padding: 0.5rem 1.6rem !important;
                box-shadow: 0 4px 14px rgba(var(--color-primary-rgb, 16, 185, 129), 0.35) !important;
                transition: all 0.2s ease !important;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
            }

            .otp-btn-submit:hover {
                filter: brightness(1.08);
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(var(--color-primary-rgb, 16, 185, 129), 0.45) !important;
            }
        `;
        document.head.appendChild(style);
    }

    // Build OTP Modal HTML element
    function createOtpModalElement() {
        let modalEl = document.getElementById('gmiuOtpModal');
        if (modalEl) return modalEl;

        modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = 'gmiuOtpModal';
        modalEl.setAttribute('tabindex', '-1');
        modalEl.setAttribute('data-bs-backdrop', 'static');
        modalEl.setAttribute('data-bs-keyboard', 'false');
        modalEl.setAttribute('aria-hidden', 'true');

        modalEl.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content otp-modal-content">
                    <div class="modal-header otp-modal-header border-0 pb-0 justify-content-center flex-column text-center">
                        <div class="otp-icon-badge">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h4 class="modal-title otp-modal-title fw-bold mb-1 fs-5">Email Security Verification</h4>
                        <p class="text-muted small mb-0 otp-subtext">6-Digit Authorization Code Sent To</p>
                        <p class="otp-info-email mb-0 small" id="otpModalEmailDisplay">faculty@gmiu.edu.in</p>
                    </div>

                    <div class="modal-body text-center pt-2 pb-1">
                        <p class="small text-muted mb-2 otp-subtext" style="font-size: 12px;">Enter the 6-digit code sent to your faculty email to authorize submission.</p>
                        
                        <!-- 6 Digit Input Fields -->
                        <div class="otp-inputs-wrapper" id="otpInputsWrapper">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="0" id="otpBox0">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="1" id="otpBox1">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="2" id="otpBox2">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="3" id="otpBox3">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="4" id="otpBox4">
                            <input type="text" class="otp-digit-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" data-index="5" id="otpBox5">
                        </div>

                        <!-- Feedback Message -->
                        <div class="otp-error-msg" id="otpErrorMsg"></div>

                        <!-- Resend OTP Timer -->
                        <div class="otp-resend-wrap">
                            Didn't receive code? 
                            <button type="button" class="otp-resend-btn" id="otpResendBtn" disabled>Resend OTP (<span id="otpTimerCount">60</span>s)</button>
                        </div>
                    </div>

                    <div class="modal-footer border-0 justify-content-center gap-2 pt-2">
                        <button type="button" class="btn btn-sm otp-btn-cancel" id="otpCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-sm otp-btn-submit" id="otpVerifyBtn">
                            <i class="bi bi-patch-check-fill me-1"></i> Verify & Submit
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modalEl);
        return modalEl;
    }

    let countdownInterval = null;

    // Start 60-second countdown timer for Resend button
    function startResendTimer(seconds = 60) {
        const resendBtn = document.getElementById('otpResendBtn');
        const timerCountSpan = document.getElementById('otpTimerCount');
        if (!resendBtn || !timerCountSpan) return;

        clearInterval(countdownInterval);
        resendBtn.disabled = true;
        let remaining = seconds;
        timerCountSpan.textContent = remaining;

        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Resend OTP';
            } else {
                timerCountSpan.textContent = remaining;
            }
        }, 1000);
    }

    // Trigger OTP Verification Flow with INSTANT Modal Pop-up
    window.triggerOtpVerification = function (facultyEmail, facultyName) {
        return new Promise((resolve, reject) => {
            injectOtpStyles();
            const modalEl = createOtpModalElement();
            const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

            const emailDisplay = document.getElementById('otpModalEmailDisplay');
            const errorMsg = document.getElementById('otpErrorMsg');
            const verifyBtn = document.getElementById('otpVerifyBtn');
            const cancelBtn = document.getElementById('otpCancelBtn');
            const resendBtn = document.getElementById('otpResendBtn');
            const inputsWrapper = document.getElementById('otpInputsWrapper');
            const boxes = Array.from(modalEl.querySelectorAll('.otp-digit-box'));

            emailDisplay.textContent = facultyEmail || 'faculty@gmiu.edu.in';
            errorMsg.innerHTML = '<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span> Dispatching OTP code to your email...</span>';

            // Reset digit boxes
            boxes.forEach(b => {
                b.value = '';
                b.classList.remove('is-invalid');
                b.disabled = false;
            });

            // ⚡ Show Modal INSTANTLY (<10ms UI execution)
            bsModal.show();
            setTimeout(() => boxes[0].focus(), 250);

            // Handle Input & Keyboard Navigation
            boxes.forEach((box, idx) => {
                box.oninput = function (e) {
                    const val = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = val;
                    if (val.length === 1) {
                        box.classList.remove('is-invalid');
                        errorMsg.textContent = '';
                        if (idx < 5) {
                            boxes[idx + 1].focus();
                        } else {
                            // Automatically attempt verification when 6th digit entered
                            attemptVerification();
                        }
                    }
                };

                box.onkeydown = function (e) {
                    if (e.key === 'Backspace' && !box.value && idx > 0) {
                        boxes[idx - 1].focus();
                        boxes[idx - 1].value = '';
                    } else if (e.key === 'ArrowLeft' && idx > 0) {
                        boxes[idx - 1].focus();
                    } else if (e.key === 'ArrowRight' && idx < 5) {
                        boxes[idx + 1].focus();
                    }
                };

                box.onpaste = function (e) {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                    const digits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

                    if (digits) {
                        for (let i = 0; i < 6; i++) {
                            boxes[i].value = digits[i] || '';
                            boxes[i].classList.remove('is-invalid');
                        }
                        if (digits.length === 6) {
                            boxes[5].focus();
                            attemptVerification();
                        } else {
                            boxes[Math.min(digits.length, 5)].focus();
                        }
                    }
                };
            });

            // Parallel Async OTP Email Dispatch
            sendOtpApi(facultyEmail, facultyName)
                .then(res => {
                    if (res.bypass) {
                        // OTP feature disabled in backend config -> hide modal & bypass
                        bsModal.hide();
                        resolve(true);
                        return;
                    }
                    errorMsg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> OTP code dispatched to email!</span>';
                    startResendTimer(60);
                })
                .catch(err => {
                    errorMsg.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${err.error || err.message || 'Failed to send OTP code.'}</span>`;
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Resend OTP';
                });

            // Resend OTP Click Event
            resendBtn.onclick = function () {
                resendBtn.disabled = true;
                errorMsg.innerHTML = '<span class="text-primary"><span class="spinner-border spinner-border-sm me-1"></span> Sending new OTP...</span>';
                
                sendOtpApi(facultyEmail, facultyName)
                    .then(res => {
                        errorMsg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> New OTP code dispatched to email.</span>';
                        startResendTimer(60);
                        boxes.forEach(b => { b.value = ''; b.classList.remove('is-invalid'); });
                        boxes[0].focus();
                    })
                    .catch(err => {
                        errorMsg.innerHTML = `<span class="text-danger">${err.error || 'Failed to resend OTP.'}</span>`;
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Resend OTP';
                    });
            };

            // Cancel Button Event
            cancelBtn.onclick = function () {
                clearInterval(countdownInterval);
                bsModal.hide();
                reject(new Error("OTP verification cancelled by user."));
            };

            // Verification Action
            function attemptVerification() {
                const code = boxes.map(b => b.value).join('');
                if (code.length < 6) {
                    errorMsg.innerHTML = '<span class="text-danger">Please enter all 6 digits of the OTP code.</span>';
                    boxes.forEach(b => { if (!b.value) b.classList.add('is-invalid'); });
                    return;
                }

                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Verifying...';
                boxes.forEach(b => b.disabled = true);

                fetch('otp-service?action=verify_otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'verify_otp',
                        email: facultyEmail,
                        otp: code
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        errorMsg.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> OTP Verified! Processing report...</span>';
                        setTimeout(() => {
                            clearInterval(countdownInterval);
                            bsModal.hide();
                            resolve(true);
                        }, 400);
                    } else {
                        throw data;
                    }
                })
                .catch(err => {
                    inputsWrapper.classList.add('otp-shake');
                    setTimeout(() => inputsWrapper.classList.remove('otp-shake'), 400);

                    errorMsg.innerHTML = `<span class="text-danger">${err.error || 'Invalid 6-digit OTP code. Please try again.'}</span>`;
                    boxes.forEach(b => {
                        b.classList.add('is-invalid');
                        b.disabled = false;
                    });
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> Verify & Submit';
                    boxes[0].focus();
                });
            }

            verifyBtn.onclick = attemptVerification;
        });
    };

    // Helper: Send OTP via AJAX API
    function sendOtpApi(email, facultyName) {
        const deptVal = document.getElementById("facultyDept")?.value || "";
        return fetch('otp-service?action=send_otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'send_otp',
                email: email,
                faculty_name: facultyName,
                dept: deptVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw data;
            return data;
        });
    }

})();
