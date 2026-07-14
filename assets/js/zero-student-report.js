// ── GMIU IT Department — Zero Student Report JS ──

// Room and Subject defaults for autocomplete/dropdowns
const defaultRooms = ["FF-07", "FF-11", "FF-12", "FF-16", "FF-22", "FF-24", "FF-25", "FF-26", "FF-27", "FF-28", "FF-29", "FF-30", "FF-31", "GF-05", "GF-07", "GF-08", "GF-19", "GF-23", "GF-24", "SF-27", "TF-08"];
const defaultSubjects = ["CV", "CF", "Z11 GD", "C1 SE", "C2 ADA", "WC", "AMAD", "C3 SE", "C1 AI", "C2 SE", "C22 RWMD", "H11 CV", "SPM", "DC", "Z22-DS", "IR", "D11 SPM", "Y22 EG", "H11 IOT", "Z11-WD", "Z22-GD", "Y11 EG", "Y22 BEEE", "C11 RWMD", "H11 WC", "Y11 WP", "Y11 OOP-I", "G22-CNS", "C1 ADA", "C2 AL", "IOT", "AI", "C3 ADA", "SE", "B11-RWPD", "E11-MAD", "E22-CNS", "CNS", "TMV", "G11-AI", "HOD", "OOP-I"];

// Branch/Class defaults for IT and CE autocomplete
const defaultBranchesIT = ["CLASS A B.TECH(IT)", "CLASS B B.TECH(IT)", "CLASS C B.TECH(IT)(ICT)", "B.TECH(IT)", "DIPLOMA(IT)"];
const defaultBranchesCE = ["CLASS A B.TECH(CE)", "CLASS B B.TECH(CE)", "CLASS C B.TECH(CE)", "B.TECH(CE)", "DIPLOMA(CE)"];

// Keep track of active department selection
let currentDepartment = "Information Technology";

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
    initBranchAutocomplete();
    initDepartmentToggle();

    // Event listeners
    const addBtn = document.getElementById("add-entry-btn");
    if (addBtn) addBtn.addEventListener("click", handleAddEntry);

    // Initialize PDF Import feature
    initPdfImport();
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
    const studentsInput = document.getElementById("entry-students");
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
    const studentsVal = studentsInput ? studentsInput.value.trim() : "---";

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

    const deptName = matchedFaculty ? (matchedFaculty.department || currentDepartment) : currentDepartment;
    const deptAbbr = (deptName === "Computer Engineering") ? "CE" : "IT";

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

    // 2. Prepare payload for Google Sheets matching the Column A-J layout
    const sheetsPayload = {
        date: formattedDate,
        room: roomVal,
        subject: subjectVal,
        faculty: facultyInitials,
        branch: branchVal,
        semester: semVal,
        timeIn: formatTimeTo12hWithSec(timeInVal),
        timeOut: formatTimeTo12hWithSec(timeOutVal),
        remarks: remarksVal,
        noOfStudents: studentsVal,
        dept: deptAbbr
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
                        <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 12px;">Gyanmanjari Innovative University &nbsp;·&nbsp; Department of ${deptAbbr}</p>
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
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">No. of Students</td>
                                <td style="padding: 10px 0; color: #0f172a;">${studentsVal}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Remarks</td>
                                <td style="padding: 10px 0; font-weight: bold; color: #ef4444;">${remarksVal}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', serif;">
                        <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="${window.location.href}" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">${deptAbbr} DEPARTMENT</a>.</p>
                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 <a href="${window.location.href}" style="color: #64748b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;"></a>ALL RIGHTS RESERVED.</p>
                    </div>

                </div>
            </div>
        </body>
        </html>
    `;

    // Helper function to reset form inputs
    const resetInputs = () => {
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
        if (studentsInput) studentsInput.value = "---";

        // Set date back to today
        const today = new Date().toISOString().split("T")[0];
        if (dateInput._flatpickr) {
            dateInput._flatpickr.setDate(today);
        } else if (dateInput) {
            dateInput.value = today;
        }
    };

    // 5. Submit to Google Sheet via secure backend proxy
    fetch('proxy-sheets?target=zero', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(sheetsPayload)
    })
    .then(res => {
        if (!res.ok) throw new Error('Proxy response not ok');
        return res.json();
    })
    .then(sheetData => {
        if (!sheetData.success) throw new Error(sheetData.error || 'Logging failed');

        if (sheetData.duplicate) {
            showToast("Duplicate entry. Record already exists in Google Sheet.", "error");
            resetInputs();
            return;
        }

        // 6. Send Email Notification (only if not duplicate!)
        fetch('send-email', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                to: facultyEmail,
                cc: [],
                subject: `Zero Student As Per Timetable — ${facultyInitials} (${deptAbbr})`,
                html: emailHtml
            })
        })
        .then(res => res.json())
        .then(emailRes => {
            showToast("Log submitted to Sheet & emailed successfully!");
            resetInputs();
        })
        .catch(err => {
            console.error("Email failed:", err);
            showToast("Log submitted to Sheet, but email failed.", "error");
            resetInputs();
        });
    })
    .catch(err => {
        console.error("Submission failed:", err);
        showToast("Log submission failed.", "error");
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
        let filtered = [];
        if (!query) {
            filtered = facultyData.filter(member => member.department === currentDepartment);
        } else {
            filtered = facultyData.filter(member =>
                member.name.toLowerCase().includes(query) ||
                member.initials.toLowerCase().includes(query) ||
                member.designation.toLowerCase().includes(query)
            );
        }
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
                if (member.department) {
                    setDepartment(member.department);
                }
            });
            dropdown.appendChild(item);
        });
    }
}

// ── PDF Import and Parsing Logic ──
let pdfParsedData = []; // Store parsed records in global array

function initPdfImport() {
    const importBtn = document.getElementById("import-pdf-btn");
    const fileInput = document.getElementById("pdf-import-input");
    const modal = document.getElementById("pdfPreviewModal");
    const closeBtn = document.getElementById("pdfModalClose");
    const cancelBtn = document.getElementById("pdfModalCancel");
    const importConfirmBtn = document.getElementById("pdfModalImportBtn");
    const selectAllCheckbox = document.getElementById("select-all-pdf-rows");

    if (!importBtn || !fileInput || !modal) return;

    // Trigger file dialog
    importBtn.addEventListener("click", () => {
        fileInput.value = ""; // clear previous select
        fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        showToast("Reading PDF file...", "success");
        try {
            const data = await parsePDFFile(file);
            if (data.length === 0) {
                showToast("No matching records found in PDF for our faculty initials.", "error");
                return;
            }
            pdfParsedData = data;
            renderPreviewRows();
            openModal();
        } catch (error) {
            console.error("PDF Parse error: ", error);
            showToast("Failed to parse PDF: " + error.message, "error");
        }
    });

    // Modal control
    const openModal = () => {
        modal.classList.add("show");
        // Reset progress bar UI
        document.getElementById("importProgressContainer").style.display = "none";
        document.getElementById("importProgressBarFill").style.width = "0%";
        importConfirmBtn.disabled = false;
        importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
        selectAllCheckbox.checked = true;
    };

    const closeModal = () => {
        modal.classList.remove("show");
    };

    closeBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);

    // Select all handler
    selectAllCheckbox.addEventListener("change", (e) => {
        const checked = e.target.checked;
        const rowCheckboxes = document.querySelectorAll(".pdf-row-checkbox");
        rowCheckboxes.forEach(cb => cb.checked = checked);
        importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
    });

    // Import confirmation handler
    importConfirmBtn.addEventListener("click", handleBatchImport);
}

function getSelectedCount() {
    const rowCheckboxes = document.querySelectorAll(".pdf-row-checkbox:checked");
    return rowCheckboxes.length;
}

function renderPreviewRows() {
    const tbody = document.getElementById("pdf-parsed-rows-body");
    const importConfirmBtn = document.getElementById("pdfModalImportBtn");
    if (!tbody) return;

    tbody.innerHTML = "";
    
    // Filter rows by current selected department
    const filteredRows = [];
    pdfParsedData.forEach((row, originalIndex) => {
        const facInit = row.faculty.toUpperCase().trim();
        const matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facInit);
        if (matchedFaculty && matchedFaculty.department === currentDepartment) {
            filteredRows.push({ row, originalIndex });
        }
    });

    if (filteredRows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; color: var(--text-muted); padding: 24px;">No records found for the ${currentDepartment} department in this PDF.</td></tr>`;
        importConfirmBtn.innerText = `Import Selected (0)`;
        importConfirmBtn.disabled = true;
        return;
    }

    importConfirmBtn.disabled = false;

    filteredRows.forEach(({ row, originalIndex }) => {
        const tr = document.createElement("tr");
        tr.id = `pdf-row-${originalIndex}`;
        
        // Match faculty name
        const facultyInitials = row.faculty.toUpperCase();
        const matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facultyInitials);
        const facultyLabel = matchedFaculty ? `${matchedFaculty.name} (${facultyInitials})` : `Prof. ${facultyInitials}`;

        const isCe = currentDepartment === "Computer Engineering";
        const deptBadge = `<span class="zs-dept-badge-pill ${isCe ? 'ce' : 'it'}">${isCe ? 'CE' : 'IT'}</span>`;

        tr.innerHTML = `
            <td style="text-align: center;"><input type="checkbox" class="pdf-row-checkbox" data-index="${originalIndex}" checked></td>
            <td>${row.formattedDate || row.date}</td>
            <td><strong>${row.room}</strong></td>
            <td>${row.subject}</td>
            <td>${facultyLabel}${deptBadge}</td>
            <td>${row.branch}</td>
            <td style="text-align: center;">${row.semester}</td>
            <td>${row.timeInStr} - ${row.timeOutStr}</td>
            <td style="text-align: center;">${row.noOfStudents}</td>
            <td style="font-size: 11px;">${row.remarks || "NO STUDENT"}</td>
        `;

        // Update select count on checkbox change
        const checkbox = tr.querySelector(".pdf-row-checkbox");
        checkbox.addEventListener("change", () => {
            importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
        });

        tbody.appendChild(tr);
    });

    importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
}

function isBranchMatching(branchStr, department) {
    if (!branchStr) return false;
    
    // Strip dots, spaces, hyphens, and ampersands to normalize
    const cleanBranch = branchStr.toUpperCase().replace(/[\.\s\-\&]/g, "");
    
    // Whitelist generic or sub-class layout remnants from multi-line PDF text
    const genericKeywords = ["BTECH", "DIPLOMA", "MTECH", "BE", "B", "ENGINEERING", "SCIENCE", "BTECHCE", "BTECHIT"];
    if (genericKeywords.includes(cleanBranch) || cleanBranch.includes("CLASS")) {
        return true;
    }
    
    if (department === "Computer Engineering") {
        return cleanBranch.includes("CE") || 
               cleanBranch.includes("COMPUTER") || 
               cleanBranch.includes("COMP") || 
               cleanBranch.includes("CSE");
    } else {
        return cleanBranch.includes("IT") || 
               cleanBranch.includes("INFORMATION") || 
               cleanBranch.includes("INFO") || 
               cleanBranch.includes("ICT");
    }
}

async function parsePDFFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = async function() {
            try {
                const typedarray = new Uint8Array(this.result);
                const pdf = await pdfjsLib.getDocument({data: typedarray}).promise;
                let parsedRows = [];
                let currentDate = "";
                let globalHeaderColumns = [];

                // Extract text coordinates from all pages
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum);
                    const textContent = await page.getTextContent();
                    const items = textContent.items;

                    if (items.length === 0) continue;

                    // Group by Y-coordinate
                    let yGroups = {};
                    items.forEach(item => {
                        const str = item.str;
                        if (str === undefined || str === null) return;
                        const trimmed = str.trim();
                        if (!trimmed) return;
                        
                        const y = Math.round(item.transform[5] * 10) / 10;
                        const tolerance = 8.0;
                        let matchedY = null;
                        
                        for (const key of Object.keys(yGroups)) {
                            if (Math.abs(parseFloat(key) - y) <= tolerance) {
                                matchedY = key;
                                break;
                            }
                        }
                        if (matchedY !== null) {
                            yGroups[matchedY].push(item);
                        } else {
                            yGroups[y] = [item];
                        }
                    });

                    // Sort rows top-to-bottom (Y descending)
                    const sortedYs = Object.keys(yGroups).map(Number).sort((a, b) => b - a);
                    const rows = sortedYs.map(y => {
                        const rowItems = yGroups[y];
                        rowItems.sort((a, b) => a.transform[4] - b.transform[4]);
                        return rowItems;
                    });

                    // Merge adjacent horizontal elements in each row (like words inside a cell)
                    const mergedRows = rows.map(mergeCloseItems);

                    // Find headers on this page
                    let headerRow = null;
                    let headerRowIdx = -1;
                    for (let r = 0; r < mergedRows.length; r++) {
                        const row = mergedRows[r];
                        const joinedText = row.map(item => item.str.toUpperCase()).join(" ");
                        if (joinedText.includes("DATE") && (joinedText.includes("CLASS") || joinedText.includes("LAB")) && joinedText.includes("FACULTY")) {
                            headerRow = row;
                            headerRowIdx = r;
                            break;
                        }
                    }

                    if (!headerRow) {
                        if (pageNum === 1) {
                            throw new Error("Could not find table headers in PDF. Please verify PDF format.");
                        }
                    }

                    // Identify column mapping dynamically using centers of header columns
                    if (headerRow) {
                        let headerColumns = headerRow;

                        // Pre-process headerColumns to merge split headers like "TIME" + "IN" or "TIME" + "OUT"
                        let cleanHeaders = [];
                        for (let i = 0; i < headerColumns.length; i++) {
                            const current = headerColumns[i];
                            const currentStr = current.str.toUpperCase().trim();
                            
                            if (i < headerColumns.length - 1) {
                                const next = headerColumns[i + 1];
                                const nextStr = next.str.toUpperCase().trim();
                                
                                // Merge "TIME" + "IN"
                                if (currentStr === "TIME" && nextStr === "IN") {
                                    current.str = "TIME IN";
                                    current.width = (next.transform[4] + next.width) - current.transform[4];
                                    cleanHeaders.push(current);
                                    i++; // skip next
                                    continue;
                                }
                                // Merge "TIME" + "OUT"
                                if (currentStr === "TIME" && nextStr === "OUT") {
                                    current.str = "TIME OUT";
                                    current.width = (next.transform[4] + next.width) - current.transform[4];
                                    cleanHeaders.push(current);
                                    i++; // skip next
                                    continue;
                                }
                                // Merge "SR" + "NO" / "NO."
                                if (currentStr === "SR" && (nextStr === "NO" || nextStr === "NO.")) {
                                    current.str = "SR NO";
                                    current.width = (next.transform[4] + next.width) - current.transform[4];
                                    cleanHeaders.push(current);
                                    i++; // skip next
                                    continue;
                                }
                                // Merge "NO" + "OF" + "STUDENTS"
                                if ((currentStr === "NO OF" || currentStr === "NO") && nextStr.includes("STUDENT")) {
                                    current.str = "NO OF STUDENTS";
                                    current.width = (next.transform[4] + next.width) - current.transform[4];
                                    cleanHeaders.push(current);
                                    i++; // skip next
                                    continue;
                                }
                            }
                            cleanHeaders.push(current);
                        }
                        globalHeaderColumns = cleanHeaders;
                    }

                    // Parse data rows below the header
                    const dataRowsStartIdx = headerRow ? headerRowIdx + 1 : 0;
                    for (let r = dataRowsStartIdx; r < mergedRows.length; r++) {
                        const row = mergedRows[r];
                        
                        // Map each text item to its closest header column
                        let colValues = Array(globalHeaderColumns.length || 14).fill("");
                        row.forEach(item => {
                            const itemCenter = item.transform[4] + item.width / 2;
                            let closestHeaderIdx = 0;
                            let minDistance = Infinity;
                            
                            globalHeaderColumns.forEach((h, idx) => {
                                const hCenter = h.transform[4] + h.width / 2;
                                const dist = Math.abs(hCenter - itemCenter);
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    closestHeaderIdx = idx;
                                }
                            });
                            
                            if (colValues[closestHeaderIdx]) {
                                colValues[closestHeaderIdx] += " " + item.str;
                            } else {
                                colValues[closestHeaderIdx] = item.str;
                            }
                        });

                        // Clean columns: trim values
                        colValues = colValues.map(v => v ? v.trim() : "");

                        let dateVal = "";
                        let srNoVal = "";
                        let roomVal = "";
                        let subjectVal = "";
                        let facultyInitials = "";
                        let branchVal = "";
                        let semVal = "";
                        let timeInVal = "";
                        let timeOutVal = "";
                        let remarksVal = "";
                        let studentsVal = "";

                        // Map values based on header names
                        if (globalHeaderColumns.length > 0) {
                            globalHeaderColumns.forEach((colHeader, index) => {
                                const hText = colHeader.str.toUpperCase();
                                const val = colValues[index] || "";
                                if (hText.includes("DATE")) dateVal = val;
                                else if (hText.includes("SR") || hText.includes("NO.")) srNoVal = val;
                                else if (hText.includes("CLASS") || hText.includes("LAB")) roomVal = val;
                                else if (hText.includes("SUBJECT")) subjectVal = val;
                                else if (hText.includes("FACULTY")) facultyInitials = val;
                                else if (hText.includes("BRANCH")) branchVal = val;
                                else if (hText.includes("SEM")) semVal = val;
                                else if (hText.includes("TIME IN")) timeInVal = val;
                                else if (hText.includes("TIME OUT")) timeOutVal = val;
                                else if (hText.includes("REMARKS")) remarksVal = val;
                                else if (hText.includes("STUDENT")) studentsVal = val;
                            });
                        }

                        // Validate if this is a valid data row (SR NO must be a number)
                        if (!srNoVal || isNaN(parseInt(srNoVal, 10))) {
                            continue;
                        }

                        // Propagate date if empty
                        if (dateVal) {
                            currentDate = dateVal;
                        } else {
                            dateVal = currentDate;
                        }

                        // Skip if basic info is missing
                        if (!facultyInitials || !roomVal || !subjectVal) {
                            continue;
                        }

                        // Check if faculty initials match our department (facultyData)
                        const facInit = facultyInitials.toUpperCase().trim();
                        const matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facInit);
                        if (!matchedFaculty) {
                            continue; // Skip
                        }

                        // Verify if the parsed branch matches the faculty's department keywords
                        if (!isBranchMatching(branchVal, matchedFaculty.department)) {
                            continue; // Skip if branch does not match
                        }

                        // Format Date to sheets format: DD-MMM-YYYY
                        let parsedDateVal = "";
                        let formattedDate = "";
                        if (dateVal) {
                            const cleanDate = dateVal.replace(/\s+/g, "");
                            const match = cleanDate.match(/^(\d{1,2})[\/\-]?(\d{1,2})[\/\-]?(\d{4})$/);
                            if (match) {
                                let day = match[1].padStart(2, '0');
                                let month = match[2].padStart(2, '0');
                                let year = match[3];
                                parsedDateVal = `${year}-${month}-${day}`;
                                
                                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                                const mIdx = parseInt(month, 10) - 1;
                                if (mIdx >= 0 && mIdx < 12) {
                                    formattedDate = `${parseInt(day, 10)}-${months[mIdx]}-${year}`;
                                }
                            } else {
                                const dateParts = cleanDate.split(/[\/\-]/);
                                if (dateParts.length === 3) {
                                    let day = dateParts[0].padStart(2, '0');
                                    let month = dateParts[1].padStart(2, '0');
                                    let year = dateParts[2];
                                    if (year.length === 2) year = "20" + year;
                                    parsedDateVal = `${year}-${month}-${day}`;
                                    
                                    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                                    const mIdx = parseInt(month, 10) - 1;
                                    if (mIdx >= 0 && mIdx < 12) {
                                        formattedDate = `${parseInt(day, 10)}-${months[mIdx]}-${year}`;
                                    }
                                }
                            }
                        }
                        if (!parsedDateVal) {
                            parsedDateVal = new Date().toISOString().split("T")[0];
                        }

                        // Parse times to 24h
                        const parseTimeTo24h = (timeStr) => {
                            if (!timeStr) return "";
                            const clean = timeStr.replace(/\s*[AP]M\s*/gi, "").trim();
                            const parts = clean.split(":");
                            if (parts.length < 2) return timeStr;
                            let hrs = parseInt(parts[0], 10);
                            const mins = parts[1];
                            
                            let isPm = timeStr.toLowerCase().includes("pm");
                            if (!isPm && !timeStr.toLowerCase().includes("am")) {
                                if (hrs === 12 || (hrs >= 1 && hrs <= 7)) {
                                    isPm = true;
                                }
                            }

                            if (isPm && hrs < 12) hrs += 12;
                            if (!isPm && hrs === 12) hrs = 0;
                            return `${hrs.toString().padStart(2, '0')}:${mins}`;
                        };

                        const timeIn24 = parseTimeTo24h(timeInVal);
                        const timeOut24 = parseTimeTo24h(timeOutVal);

                        // Differentiate zero-student classes from regular conducted classes
                        const hasStudents = (studentsVal && !isNaN(parseInt(studentsVal, 10)) && parseInt(studentsVal, 10) > 0);
                        const cleanRemarks = remarksVal.toUpperCase();
                        
                        // If it has students and remarks do not state 'NO STUDENT', skip it from the zero-student report list
                        if (hasStudents && !cleanRemarks.includes("NO STUDENT") && !cleanRemarks.includes("NO STUDENTS")) {
                            continue; 
                        }

                        // Determine remarks value: fallback to "NO STUDENT" only if student count matches zero class
                        let finalRemarks = remarksVal;
                        if (!finalRemarks) {
                            finalRemarks = (studentsVal === "0" || studentsVal === "---" || !studentsVal) ? "NO STUDENT" : "-";
                        }

                        parsedRows.push({
                            date: parsedDateVal,
                            formattedDate: formattedDate || dateVal,
                            room: roomVal.toUpperCase(),
                            subject: subjectVal.toUpperCase(),
                            faculty: facInit,
                            branch: branchVal || "B TECH CIVIL",
                            semester: semVal || "3",
                            timeIn: timeIn24,
                            timeOut: timeOut24,
                            timeInStr: timeInVal,
                            timeOutStr: timeOutVal,
                            remarks: finalRemarks,
                            noOfStudents: studentsVal || "---"
                        });
                    }
                }

                resolve(parsedRows);
            } catch (err) {
                reject(err);
            }
        };
        reader.onerror = () => reject(new Error("FileReader read error."));
        reader.readAsArrayBuffer(file);
    });
}

function mergeCloseItems(items) {
    if (items.length === 0) return [];
    let merged = [];
    let current = { ...items[0] };
    
    for (let i = 1; i < items.length; i++) {
        const next = items[i];
        const distance = next.transform[4] - (current.transform[4] + current.width);
        if (distance < 12) {
            current.str += " " + next.str;
            current.width = (next.transform[4] + next.width) - current.transform[4];
        } else {
            merged.push(current);
            current = { ...next };
        }
    }
    merged.push(current);
    return merged;
}

async function handleBatchImport() {
    const rowCheckboxes = document.querySelectorAll(".pdf-row-checkbox:checked");
    if (rowCheckboxes.length === 0) {
        showToast("No records selected for import.", "error");
        return;
    }

    const indicesToImport = Array.from(rowCheckboxes).map(cb => parseInt(cb.getAttribute("data-index"), 10));
    const total = indicesToImport.length;

    const progressContainer = document.getElementById("importProgressContainer");
    const progressText = document.getElementById("importProgressText");
    const progressBarFill = document.getElementById("importProgressBarFill");
    const progressPercent = document.getElementById("importProgressPercent");
    const importBtn = document.getElementById("pdfModalImportBtn");
    const cancelBtn = document.getElementById("pdfModalCancel");
    const closeBtn = document.getElementById("pdfModalClose");

    importBtn.disabled = true;
    cancelBtn.disabled = true;
    closeBtn.style.visibility = "hidden";
    progressContainer.style.display = "block";

    let successCount = 0;
    let failCount = 0;
    let duplicateCount = 0;
    let errorDetails = [];

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

    for (let i = 0; i < total; i++) {
        const index = indicesToImport[i];
        const row = pdfParsedData[index];
        const tr = document.getElementById(`pdf-row-${index}`);

        const progress = Math.round((i / total) * 100);
        progressBarFill.style.width = `${progress}%`;
        progressPercent.innerText = `${progress}%`;
        progressText.innerText = `Importing: ${i + 1} of ${total} (${row.subject} by ${row.faculty})`;

        if (tr) {
            const cb = tr.querySelector(".pdf-row-checkbox");
            if (cb) cb.disabled = true;
        }

        try {
            const matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === row.faculty.toUpperCase());
            const facultyName = matchedFaculty ? matchedFaculty.name : "Prof. " + row.faculty;
            const facultyEmail = matchedFaculty ? matchedFaculty.email : "adminit@gmiu.edu.in";

            const deptName = matchedFaculty ? (matchedFaculty.department || currentDepartment) : currentDepartment;
            const deptAbbr = (deptName === "Computer Engineering") ? "CE" : "IT";

            const sheetsPayload = {
                date: row.formattedDate,
                room: row.room,
                subject: row.subject,
                faculty: row.faculty,
                branch: row.branch,
                semester: row.semester,
                timeIn: formatTimeTo12hWithSec(row.timeIn),
                timeOut: formatTimeTo12hWithSec(row.timeOut),
                remarks: row.remarks,
                noOfStudents: row.noOfStudents,
                dept: deptAbbr
            };

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
                                <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 12px;">Gyanmanjari Innovative University &nbsp;·&nbsp; Department of ${deptAbbr}</p>
                            </div>
                            <div style="padding: 24px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #334155;">
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; width: 150px; color: #64748b; text-transform: uppercase;">Faculty Initials</td>
                                        <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">${row.faculty} (${facultyName})</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Session Date</td>
                                        <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">${row.formattedDate}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Classroom/Lab</td>
                                        <td style="padding: 10px 0; font-weight: 600; color: #0369a1;"><span style="padding: 3px 8px; background-color: #e0f2fe; border-radius: 4px; font-family: monospace;">${row.room}</span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Subject</td>
                                        <td style="padding: 10px 0; font-weight: 600; color: #b45309;"><span style="padding: 3px 8px; background-color: #fef3c7; border-radius: 4px;">${row.subject}</span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Branch/Class</td>
                                        <td style="padding: 10px 0; color: #0f172a;">${row.branch}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Semester</td>
                                        <td style="padding: 10px 0; color: #0f172a;">${row.semester}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Timing</td>
                                        <td style="padding: 10px 0; color: #0f172a;">${formatTimeTo12hWithSec(row.timeIn)} - ${formatTimeTo12hWithSec(row.timeOut)}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">No. of Students</td>
                                        <td style="padding: 10px 0; color: #0f172a;">${row.noOfStudents}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; font-weight: bold; color: #64748b; text-transform: uppercase;">Remarks</td>
                                        <td style="padding: 10px 0; font-weight: bold; color: #ef4444;">${row.remarks}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', serif;">
                                <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="${window.location.href}" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">${deptAbbr} DEPARTMENT</a>.</p>
                                <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 <a href="${window.location.href}" style="color: #64748b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;"></a>ALL RIGHTS RESERVED.</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;

            // Submit sheet
            const sheetRes = await fetch('proxy-sheets?target=zero', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(sheetsPayload)
            });
            
            if (!sheetRes.ok) {
                let errMsg = 'Google sheet proxy write failed';
                try {
                    const errData = await sheetRes.json();
                    if (errData && errData.error) {
                        errMsg = errData.error + (errData.details ? " (" + errData.details + ")" : "");
                    }
                } catch (e) {}
                throw new Error(errMsg);
            }

            const sheetData = await sheetRes.json();
            if (sheetData.duplicate) {
                duplicateCount++;
                if (tr) {
                    tr.className = "unmatched-row";
                    const cb = tr.querySelector(".pdf-row-checkbox");
                    if (cb) {
                        cb.checked = false;
                        cb.disabled = true;
                    }
                }
                continue;
            }

            // Send email (catch any connection reset/close errors locally so sheet write success is kept)
            try {
                await fetch('send-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: facultyEmail,
                        cc: [],
                        subject: `Zero Student As Per Timetable — ${row.faculty} (${deptAbbr})`,
                        html: emailHtml
                    })
                });
            } catch (emailErr) {
                console.warn("Email dispatch connection warning:", emailErr);
            }

            successCount++;
            if (tr) {
                tr.className = "imported-row";
                const cb = tr.querySelector(".pdf-row-checkbox");
                if (cb) cb.checked = false;
            }
        } catch (error) {
            console.error(`Import failed for index ${index}: `, error);
            errorDetails.push(`Row ${index + 1} (${row.subject} by ${row.faculty}): ${error.message}`);
            failCount++;
            if (tr) {
                tr.className = "error-row";
            }
        }
    }

    progressBarFill.style.width = "100%";
    progressPercent.innerText = "100%";
    
    let summaryText = `Completed: ${successCount} imported successfully`;
    if (duplicateCount > 0) summaryText += `, ${duplicateCount} duplicate(s) skipped`;
    if (failCount > 0) summaryText += `, ${failCount} failed`;
    progressText.innerText = summaryText;

    cancelBtn.disabled = false;
    cancelBtn.innerText = "Close";
    closeBtn.style.visibility = "visible";
    importBtn.disabled = false;
    importBtn.innerText = "Import Selected (0)";

    let finalMsg = `PDF Import complete: ${successCount} success`;
    if (duplicateCount > 0) finalMsg += `, ${duplicateCount} skipped duplicates`;
    if (failCount > 0) finalMsg += `, ${failCount} failed`;
    
    if (failCount > 0) {
        alert(finalMsg + "\n\nError details:\n" + errorDetails.join("\n"));
    } else {
        showToast(finalMsg, "success");
    }
}

// ── Branch Autocomplete ──
function initBranchAutocomplete() {
    const input = document.getElementById("entry-branch");
    const dropdown = document.getElementById("branchDropdownList");

    if (!input || !dropdown) return;

    // Fill dropdown initially
    renderList(getActiveBranchList());

    input.addEventListener("focus", () => {
        dropdown.classList.add("show");
        filterList();
    });

    input.addEventListener("input", () => {
        dropdown.classList.add("show");
        filterList();
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest("#entry-branch") && !e.target.closest("#branchDropdownList")) {
            dropdown.classList.remove("show");
        }
    });

    function filterList() {
        const query = input.value.toLowerCase().trim();
        const activeList = getActiveBranchList();
        const filtered = activeList.filter(item => item.toLowerCase().includes(query));
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

// ── Department Toggle ──
function initDepartmentToggle() {
    const itBtn = document.getElementById("dept-it-btn");
    const ceBtn = document.getElementById("dept-ce-btn");

    if (!itBtn || !ceBtn) return;

    itBtn.addEventListener("click", () => {
        setDepartment("Information Technology");
    });

    ceBtn.addEventListener("click", () => {
        setDepartment("Computer Engineering");
    });
}

function setDepartment(dept) {
    const itBtn = document.getElementById("dept-it-btn");
    const ceBtn = document.getElementById("dept-ce-btn");
    const pageContainer = document.getElementById("zsPage");
    const badgeText = document.getElementById("rp-dept-badge-text");

    currentDepartment = dept;

    if (dept === "Computer Engineering") {
        if (itBtn) itBtn.classList.remove("active");
        if (ceBtn) ceBtn.classList.add("active");
        if (pageContainer) pageContainer.classList.add("ce-active");
        if (badgeText) badgeText.innerText = "Department of Computer Engineering";
    } else {
        if (ceBtn) ceBtn.classList.remove("active");
        if (itBtn) itBtn.classList.add("active");
        if (pageContainer) pageContainer.classList.remove("ce-active");
        if (badgeText) badgeText.innerText = "Department of Information Technology";
    }

    // Refresh Branch suggestions
    const branchDropdown = document.getElementById("branchDropdownList");
    if (branchDropdown) {
        const activeList = getActiveBranchList();
        branchDropdown.innerHTML = "";
        activeList.forEach(item => {
            const el = document.createElement("div");
            el.className = "dropdown-item";
            el.style.padding = "8px 12px";
            el.style.fontSize = "12.5px";
            el.innerText = item;
            el.addEventListener("click", () => {
                const input = document.getElementById("entry-branch");
                if (input) input.value = item;
                branchDropdown.classList.remove("show");
            });
            branchDropdown.appendChild(el);
        });
    }

    // Refresh faculty list
    const facultyInput = document.getElementById("entry-faculty");
    if (facultyInput) {
        facultyInput.dispatchEvent(new Event("input"));
    }

    // Refresh PDF preview list if a PDF has been parsed
    if (typeof pdfParsedData !== 'undefined' && pdfParsedData.length > 0) {
        renderPreviewRows();
    }
}

function getActiveBranchList() {
    return currentDepartment === "Computer Engineering" ? defaultBranchesCE : defaultBranchesIT;
}
