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
                max-width: 420px !important;
                width: 92% !important;
                margin: 1.75rem auto !important;
            }

            /* Default Dark Theme Modal Card */
            .otp-modal-content {
                background: #0f172a !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                border-radius: 18px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 20px rgba(59, 130, 246, 0.15) !important;
                color: #f8fafc !important;
                padding: 1.5rem !important;
            }

            .otp-modal-header {
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            }

            .otp-modal-title {
                color: #ffffff !important;
                font-weight: 700 !important;
            }

            .otp-subtext {
                color: #cbd5e1 !important;
            }

            .otp-icon-badge {
                width: 52px;
                height: 52px;
                background: rgba(37, 99, 235, 0.2);
                border: 2px solid rgba(59, 130, 246, 0.4);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                color: #60a5fa;
                margin: 0 auto 10px auto;
            }

            /* Light Theme Adaptive Overrides */
            [data-theme="light"] .otp-modal-content {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18), 0 0 15px rgba(37, 99, 235, 0.1) !important;
                color: #0f172a !important;
            }

            [data-theme="light"] .otp-modal-header {
                border-bottom: 1px solid #e2e8f0 !important;
            }

            [data-theme="light"] .otp-modal-title {
                color: #0f172a !important;
            }

            [data-theme="light"] .otp-subtext {
                color: #475569 !important;
            }

            [data-theme="light"] .otp-icon-badge {
                background: rgba(37, 99, 235, 0.1);
                border-color: rgba(37, 99, 235, 0.25);
                color: #2563eb;
            }

            /* Compact 6-Digit Input Container */
            .otp-inputs-wrapper {
                display: flex;
                gap: 6px;
                justify-content: center;
                margin: 16px 0;
            }

            .otp-digit-box {
                width: 40px;
                height: 48px;
                font-size: 20px;
                font-weight: 700;
                text-align: center;
                border-radius: 10px;
                border: 2px solid #475569;
                background: #1e293b;
                color: #ffffff;
                outline: none;
                transition: all 0.18s ease-in-out;
            }

            .otp-digit-box:focus {
                border-color: #38bdf8;
                background: #0f172a;
                box-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
                color: #ffffff;
                transform: translateY(-2px);
            }

            /* Light Mode Digit Boxes */
            [data-theme="light"] .otp-digit-box {
                border: 2px solid #cbd5e1;
                background: #f8fafc;
                color: #0f172a;
            }

            [data-theme="light"] .otp-digit-box:focus {
                border-color: #2563eb;
                background: #ffffff;
                box-shadow: 0 0 10px rgba(37, 99, 235, 0.3);
            }

            .otp-digit-box.is-invalid {
                border-color: #ef4444 !important;
                color: #ef4444 !important;
                box-shadow: 0 0 8px rgba(239, 68, 68, 0.3) !important;
            }

            /* Mobile Responsiveness (< 420px) */
            @media (max-width: 420px) {
                #gmiuOtpModal .modal-dialog {
                    margin: 0.75rem auto !important;
                    width: 95% !important;
                }

                .otp-modal-content {
                    padding: 1.1rem !important;
                }

                .otp-inputs-wrapper {
                    gap: 4px;
                    margin: 12px 0;
                }

                .otp-digit-box {
                    width: 34px;
                    height: 42px;
                    font-size: 17px;
                    border-radius: 8px;
                }

                .otp-icon-badge {
                    width: 44px;
                    height: 44px;
                    font-size: 18px;
                    margin-bottom: 6px;
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
                font-size: 12.5px;
                color: #94a3b8;
                margin-top: 12px;
            }

            [data-theme="light"] .otp-resend-wrap {
                color: #64748b;
            }

            .otp-resend-btn {
                color: #38bdf8;
                text-decoration: none;
                font-weight: 600;
                cursor: pointer;
                background: none;
                border: none;
                padding: 0;
            }

            [data-theme="light"] .otp-resend-btn {
                color: #2563eb;
            }

            .otp-resend-btn:hover {
                text-decoration: underline;
            }

            .otp-resend-btn:disabled {
                color: #64748b;
                cursor: not-allowed;
                text-decoration: none;
            }

            /* Status & Error Text */
            .otp-error-msg {
                font-size: 12.5px;
                font-weight: 500;
                min-height: 20px;
                margin-top: 6px;
            }

            .otp-info-email {
                color: #38bdf8;
                font-weight: 600;
                word-break: break-all;
            }

            [data-theme="light"] .otp-info-email {
                color: #2563eb;
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
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 rounded-pill" id="otpCancelBtn">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-4 rounded-pill fw-bold" id="otpVerifyBtn">
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
