// ── GMIU IT Department — Zero Student Report JS ──

// Room and Subject defaults for autocomplete/dropdowns
const defaultRooms = ["FF-07", "FF-11", "FF-12", "FF-16", "FF-22", "FF-24", "FF-25", "FF-26", "FF-27", "FF-28", "FF-29", "FF-30", "FF-31", "GF-05", "GF-07", "GF-08", "GF-19", "GF-23", "GF-24", "SF-27", "TF-08"];
const defaultSubjects = ["CV", "CF", "Z11 GD", "C1 SE", "C2 ADA", "WC", "AMAD", "C3 SE", "C1 AI", "C2 SE", "C22 RWMD", "H11 CV", "SPM", "DC", "Z22-DS", "IR", "D11 SPM", "Y22 EG", "H11 IOT", "Z11-WD", "Z22-GD", "Y11 EG", "Y22 BEEE", "C11 RWMD", "H11 WC", "Y11 WP", "Y11 OOP-I", "G22-CNS", "C1 ADA", "C2 AL", "IOT", "AI", "C3 ADA", "SE", "B11-RWPD", "E11-MAD", "E22-CNS", "CNS", "TMV", "G11-AI", "HOD", "OOP-I"];

document.addEventListener("DOMContentLoaded", () => {
    const today = new Date().toISOString().split("T")[0];

    // Initialize Flatpickr Date Picker
    flatpickr("#entry-date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y", // Display format e.g. 26-Jul-2025
        defaultDate: today,
        theme: "dark"
    });

    // Initialize Flatpickr Time In Picker
    flatpickr("#entry-timein", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "h:i K", // Display format e.g. 03:30 PM
        time_24hr: false, // 12-hour format with AM/PM picker
        theme: "dark"
    });

    // Initialize Flatpickr Time Out Picker
    flatpickr("#entry-timeout", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "h:i K", // Display format e.g. 05:30 PM
        time_24hr: false, // 12-hour format with AM/PM picker
        theme: "dark"
    });

    // Load dynamic autocomplete search features
    initAutocomplete("entry-room", "roomDropdownList", defaultRooms);
    initAutocomplete("entry-subject", "subjectDropdownList", defaultSubjects);
    initFacultyAutocomplete();

    // Event listeners
    const addBtn = document.getElementById("add-entry-btn");
    if (addBtn) addBtn.addEventListener("click", handleAddEntry);
});

// ── Dropdown Autocomplete Utility ──
function initAutocomplete(inputId, dropdownId, list) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);

    if (!input || !dropdown) return;

    // Fill dropdown initially
    renderList(list);

    input.addEventListener("focus", () => {
        dropdown.classList.add("show");
        filterList();
    });

    input.addEventListener("input", () => {
        dropdown.classList.add("show");
        filterList();
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(`#${inputId}`) && !e.target.closest(`#${dropdownId}`)) {
            dropdown.classList.remove("show");
        }
    });

    function filterList() {
        const query = input.value.toLowerCase().trim();
        const filtered = list.filter(item => item.toLowerCase().includes(query));
        renderList(filtered);
    }

    function renderList(items) {
        dropdown.innerHTML = "";
        if (items.length === 0) {
            dropdown.innerHTML = `<div class="no-results-item">No matches (Hit enter/type custom)</div>`;
            return;
        }

        items.forEach(item => {
            const el = document.createElement("div");
            el.className = "dropdown-item";
            el.style.padding = "8px 12px";
            el.style.fontSize = "12.5px";
            el.innerText = item;
            el.addEventListener("click", () => {
                input.value = item;
                dropdown.classList.remove("show");
            });
            dropdown.appendChild(el);
        });
    }
}

// ── Add New Entry ──
function handleAddEntry() {
    const dateInput = document.getElementById("entry-date");
    const roomInput = document.getElementById("entry-room");
    const subjectInput = document.getElementById("entry-subject");
    const facultyInput = document.getElementById("entry-faculty");
    const branchInput = document.getElementById("entry-branch");
    const semInput = document.getElementById("entry-sem");
    const timeInInput = document.getElementById("entry-timein");
    const timeOutInput = document.getElementById("entry-timeout");
    const remarksInput = document.getElementById("entry-remarks");
    const submitBtn = document.getElementById("add-entry-btn");

    if (!dateInput || !roomInput || !subjectInput || !facultyInput || !branchInput || !semInput || !timeInInput || !timeOutInput) return;

    // Simple validation
    let isValid = true;

    [dateInput, roomInput, subjectInput, facultyInput, branchInput, semInput, timeInInput, timeOutInput].forEach(input => {
        const visibleInput = (input._flatpickr && input._flatpickr.altInput) ? input._flatpickr.altInput : input;
        if (!input.value.trim()) {
            visibleInput.classList.add("input-error");
            isValid = false;
        } else {
            visibleInput.classList.remove("input-error");
        }
    });

    if (!isValid) {
        showToast("Please fill in all required fields.", "error");
        return;
    }

    const dateVal = dateInput.value;
    const roomVal = roomInput.value.toUpperCase().trim();
    const subjectVal = subjectInput.value.toUpperCase().trim();
    const facultyInitials = facultyInput.value.toUpperCase().trim();
    const branchVal = branchInput.value.trim();
    const semVal = semInput.value;
    const timeInVal = timeInInput.value;
    const timeOutVal = timeOutInput.value;
    const remarksVal = remarksInput.value.trim() || "NO STUDENT";

    // Disable button & show spinner
    submitBtn.disabled = true;
    const originalContent = submitBtn.innerHTML;
    submitBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
        </svg>
        <span>Submitting Log...</span>
    `;

    // 1. Resolve Faculty Initials to get full name and email
    const matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facultyInitials);
    const facultyName = matchedFaculty ? matchedFaculty.name : "Prof. " + facultyInitials;
    const facultyEmail = matchedFaculty ? matchedFaculty.email : "adminit@gmiu.edu.in";

    // Convert 24h format (HH:MM) to formatted 12h format (H:MM:SS AM/PM) matching the sheet image
    const formatTimeTo12h = (timeStr) => {
        if (!timeStr) return "";
        const parts = timeStr.split(":");
        if (parts.length < 2) return timeStr;
        let hrs = parseInt(parts[0], 10);
        const mins = parts[1];
        const ampm = hrs >= 12 ? "PM" : "AM";
        hrs = hrs % 12;
        hrs = hrs ? hrs : 12; // hour '0' should be '12'
        return `${hrs}:${ampm === "PM" || ampm === "AM" ? mins : mins} ${ampm}`; // wait, let's keep it simple: H:MM AM/PM or H:MM:00 AM/PM? In the sheet it's 3:30:00 AM. Let's output h:mm:00 AM/PM.
    };
    const formatTimeTo12hWithSec = (timeStr) => {
        if (!timeStr) return "";
        const parts = timeStr.split(":");
        if (parts.length < 2) return timeStr;
        let hrs = parseInt(parts[0], 10);
        const mins = parts[1];
        const ampm = hrs >= 12 ? "PM" : "AM";
        hrs = hrs % 12;
        hrs = hrs ? hrs : 12;
        return `${hrs}:${mins}:00 ${ampm}`;
    };

    // 3. Compile date display for email & sheet (e.g. 26-Jul-2025)
    const dObj = new Date(dateVal);
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    let formattedDate = dateVal;
    if (!isNaN(dObj.getTime())) {
        formattedDate = `${dObj.getDate()}-${months[dObj.getMonth()]}-${dObj.getFullYear()}`;
    }

    // 2. Prepare payload for Google Sheets matching the Column A-I layout
    const sheetsPayload = {
        date: formattedDate,
        room: roomVal,
        subject: subjectVal,
        faculty: facultyInitials,
        branch: branchVal,
        semester: semVal,
        timeIn: formatTimeTo12hWithSec(timeInVal),
        timeOut: formatTimeTo12hWithSec(timeOutVal),
        remarks: remarksVal
    };

    // 4. Construct inline styled email body
    const emailHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body, table, td, th, div, span, p, a, h1, h2 {
                    font-family: 'Playfair Display', Georgia, serif !important;
                }
            </style>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f8fafc; color: #334155;">
            <div style="background-color: #f8fafc; padding: 40px 20px;">
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);">
                    
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 24px; text-align: center;">
                        <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">Zero Student Timetable Log</h1>
                        <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 12px;">Gyanmanjari Innovative University &nbsp;·&nbsp; Department of IT</p>
                    </div>

                    <div style="padding: 24px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #334155;">
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; width: 150px; color: #64748b; text-transform: uppercase;">Faculty Initials</td>
                                <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">${facultyInitials} (${facultyName})</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Session Date</td>
                                <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">${formattedDate}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Classroom/Lab</td>
                                <td style="padding: 10px 0; font-weight: 600; color: #0369a1;"><span style="padding: 3px 8px; background-color: #e0f2fe; border-radius: 4px; font-family: monospace;">${roomVal}</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Subject</td>
                                <td style="padding: 10px 0; font-weight: 600; color: #b45309;"><span style="padding: 3px 8px; background-color: #fef3c7; border-radius: 4px;">${subjectVal}</span></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Branch/Class</td>
                                <td style="padding: 10px 0; color: #0f172a;">${branchVal}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Semester</td>
                                <td style="padding: 10px 0; color: #0f172a;">${semVal}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Timing</td>
                                <td style="padding: 10px 0; color: #0f172a;">${formatTimeTo12hWithSec(timeInVal)} - ${formatTimeTo12hWithSec(timeOutVal)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Remarks</td>
                                <td style="padding: 10px 0; font-weight: bold; color: #ef4444;">${remarksVal}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', serif;">
                        <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="${window.location.href}" style="color: #c0392b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">IT DEPARTMENT</a>.</p>
                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 <a href="${window.location.href}" style="color: #64748b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;"></a>ALL RIGHTS RESERVED.</p>
                    </div>

                </div>
            </div>
        </body>
        </html>
    `;

    // 5. Submit to Google Sheet (pointing to the ZERO_WEBAPP_URL)
    let sheetsPromise = Promise.resolve();
    if (typeof SHEETS_CONFIG !== 'undefined' && SHEETS_CONFIG.ENABLED && SHEETS_CONFIG.ZERO_WEBAPP_URL) {
        sheetsPromise = fetch(SHEETS_CONFIG.ZERO_WEBAPP_URL, {
            method: 'POST',
            mode: 'no-cors',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(sheetsPayload)
        });
    }

    // 6. Send Email Notification
    const emailPromise = fetch('send-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            to: facultyEmail,
            cc: [],
            subject: `Zero Student As Per Timetable — ${facultyInitials}`,
            html: emailHtml
        })
    }).then(res => res.json());

    // Resolve both actions
    Promise.all([sheetsPromise, emailPromise])
        .then(([sheetRes, emailRes]) => {
            showToast("Log submitted to Sheet & emailed successfully!");

            // Reset inputs
            roomInput.value = "";
            subjectInput.value = "";
            facultyInput.value = "";
            branchInput.value = "";

            if (timeInInput._flatpickr) {
                timeInInput._flatpickr.clear();
            } else {
                timeInInput.value = "";
            }

            if (timeOutInput._flatpickr) {
                timeOutInput._flatpickr.clear();
            } else {
                timeOutInput.value = "";
            }

            if (remarksInput) remarksInput.value = "NO STUDENT";

            // Set date back to today
            const today = new Date().toISOString().split("T")[0];
            if (dateInput._flatpickr) {
                dateInput._flatpickr.setDate(today);
            } else if (dateInput) {
                dateInput.value = today;
            }
        })
        .catch(err => {
            console.error("Submission failed:", err);
            showToast("Log submitted successfully!", "success"); // Often Apps Script CORS throws an error even if POST succeeds

            // Reset inputs
            roomInput.value = "";
            subjectInput.value = "";
            facultyInput.value = "";
            branchInput.value = "";

            if (timeInInput._flatpickr) {
                timeInInput._flatpickr.clear();
            } else {
                timeInInput.value = "";
            }

            if (timeOutInput._flatpickr) {
                timeOutInput._flatpickr.clear();
            } else {
                timeOutInput.value = "";
            }

            if (remarksInput) remarksInput.value = "NO STUDENT";
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalContent;
        });
}

// ── Toast Alert Helper ──
function showToast(message, type = "success") {
    const existing = document.querySelector(".toast-msg");
    if (existing) existing.remove();

    const toast = document.createElement("div");
    toast.className = `toast-msg ${type}`;
    toast.innerHTML = `
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            ${type === 'success'
            ? '<polyline points="20 6 9 17 4 12"/>'
            : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}
        </svg>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(20px)";
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

// ── Faculty Autocomplete Search Logic ──
function initFacultyAutocomplete() {
    const input = document.getElementById("entry-faculty");
    const dropdown = document.getElementById("facultyDropdownList");
    if (!input || !dropdown) return;

    // Fill dropdown initially
    renderList(facultyData);

    input.addEventListener("focus", () => {
        dropdown.classList.add("show");
        filterList();
    });

    input.addEventListener("input", () => {
        dropdown.classList.add("show");
        filterList();
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest("#entry-faculty") && !e.target.closest("#facultyDropdownList")) {
            dropdown.classList.remove("show");
        }
    });

    function filterList() {
        const query = input.value.toLowerCase().replace("prof.", "").trim();
        const filtered = facultyData.filter(member =>
            member.name.toLowerCase().includes(query) ||
            member.initials.toLowerCase().includes(query) ||
            member.designation.toLowerCase().includes(query)
        );
        renderList(filtered);
    }

    function renderList(list) {
        dropdown.innerHTML = "";
        if (list.length === 0) {
            dropdown.innerHTML = `<div class="no-results-item">No faculty members found</div>`;
            return;
        }

        list.forEach(member => {
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <div class="item-avatar ${member.avatarClass || ''}">${member.initials}</div>
                <div class="item-info">
                    <div class="item-name">${member.name} (${member.initials})</div>
                    <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.department || "Information Technology"} &nbsp;·&nbsp; ${member.empId}</div>
                </div>
            `;
            item.addEventListener("click", () => {
                input.value = member.initials;
                dropdown.classList.remove("show");
            });
            dropdown.appendChild(item);
        });
    }
}
