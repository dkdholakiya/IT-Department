// ── GMIU IT Department — Zero Student Report JS ──

// Room and Subject defaults for autocomplete/dropdowns
const defaultRooms = ["FF-07", "FF-11", "FF-12", "FF-16", "FF-22", "FF-24", "FF-25", "FF-26", "FF-27", "FF-28", "FF-29", "FF-30", "FF-31", "GF-05", "GF-07", "GF-08", "GF-19", "GF-23", "GF-24", "SF-27", "TF-08"];
const defaultSubjects = ["CV", "CF", "Z11 GD", "C1 SE", "C2 ADA", "WC", "AMAD", "C3 SE", "C1 AI", "C2 SE", "C22 RWMD", "H11 CV", "SPM", "DC", "Z22-DS", "IR", "D11 SPM", "Y22 EG", "H11 IOT", "Z11-WD", "Z22-GD", "Y11 EG", "Y22 BEEE", "C11 RWMD", "H11 WC", "Y11 WP", "Y11 OOP-I", "G22-CNS", "C1 ADA", "C2 AL", "IOT", "AI", "C3 ADA", "SE", "B11-RWPD", "E11-MAD", "E22-CNS", "CNS", "TMV", "G11-AI", "HOD", "OOP-I"];

// Branch/Class defaults for IT and CE autocomplete
const defaultBranchesIT = ["CLASS A B.TECH(IT)", "CLASS B B.TECH(IT)", "CLASS C B.TECH(IT)(ICT)", "B.TECH(IT)", "DIPLOMA(IT)"];
const defaultBranchesCE = ["CLASS A B.TECH(CE)", "CLASS B B.TECH(CE)", "CLASS C B.TECH(CE)", "B.TECH(CE)", "DIPLOMA(CE)"];

// Keep track of active department selection
let currentDepartment = "Information Technology";

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
    initCcAutocomplete("entry-cc", "ccDropdownList");
    initCcAutocomplete("import-cc", "importCcDropdownList");

    // Always default to Information Technology on initial load
    setDepartment("Information Technology");

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
    let facInitNormalized = facultyInitials.toUpperCase().trim();
    if (facInitNormalized === "PHC") facInitNormalized = "PHK";
    if (facInitNormalized === "PMM") facInitNormalized = "PMB";

    let matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facInitNormalized);
    if (!matchedFaculty) {
        const parenMatch = facultyInitials.match(/\(([^)]+)\)/);
        if (parenMatch) {
            let extractedInitials = parenMatch[1].toUpperCase().trim();
            if (extractedInitials === "PHC") extractedInitials = "PHK";
            if (extractedInitials === "PMM") extractedInitials = "PMB";
            matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === extractedInitials);
        }
    }
    if (!matchedFaculty) {
        matchedFaculty = facultyData.find(f => {
            const cleanName = f.name.replace(/Prof\.\s*/i, "").toUpperCase().trim();
            return facInitNormalized.includes(cleanName) || facInitNormalized.includes(f.initials.toUpperCase());
        });
    }
    const deptName = matchedFaculty && matchedFaculty.department !== "Both" ? (matchedFaculty.department || currentDepartment) : currentDepartment;
    const deptAbbr = (deptName === "Computer Engineering") ? "CE" : "IT";
    const facultyName = matchedFaculty ? matchedFaculty.name : "Prof. " + facultyInitials;
    const facultyEmail = matchedFaculty ? matchedFaculty.email : (deptAbbr === "CE" ? "admincecse@gmiu.edu.in" : "adminit@gmiu.edu.in");

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
        srNo: "---",
        room: roomVal,
        subject: subjectVal,
        faculty: facInitNormalized,
        alteration: "---",
        pt: "---",
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
                        <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 12px;">Department of ${deptAbbr}</p>
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
                        <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="https://engineering.gt.tc/" target="_blank" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">${deptAbbr} DEPARTMENT</a>.</p>
                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 ALL RIGHTS RESERVED.</p>
                        <p style="margin: 6px 0 0 0; font-size: 11px; color: #64748b; font-family: 'Playfair Display', serif;"><a href="https://engineering.gt.tc/" target="_blank" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#8c1d1d'}; text-decoration: underline; font-weight: 600; font-family: 'Playfair Display', serif;">https://engineering.gt.tc/</a></p>
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
        const ccInput = document.getElementById("entry-cc");
        if (ccInput) {
            ccInput.value = "";
            updateCcFieldForDept(currentDepartment);
        }

        // Set date back to today
        const today = new Date().toISOString().split("T")[0];
        if (dateInput._flatpickr) {
            dateInput._flatpickr.setDate(today);
        } else if (dateInput) {
            dateInput.value = today;
        }
    };

    // Extract CC email recipients
    const ccEmails = getCcEmails("entry-cc");

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
                cc: ccEmails,
                subject: `Zero Student As Per Timetable — ${facInitNormalized} (${deptAbbr})`,
                html: emailHtml,
                module: 'zero',
                dept: deptAbbr
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
        if (document.activeElement === input) {
            dropdown.classList.add("show");
        }
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
            filtered = facultyData.filter(member => member.department === currentDepartment || member.department === "Both");
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
                <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                <div class="item-info">
                    <div class="item-name">${member.name} (${member.initials})</div>
                    <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.department || "Information Technology"} &nbsp;·&nbsp; ${member.empId}</div>
                </div>
            `;
            item.addEventListener("click", () => {
                input.value = member.initials;
                dropdown.classList.remove("show");
                if (member.department && member.department !== "Both") {
                    setDepartment(member.department);
                }
            });
            dropdown.appendChild(item);
        });
    }
}

// ── PDF Import and Parsing Logic ──
let pdfParsedData = [];
let pdfTotalRowsDetected = 0;
let pdfEligibleRowsCount = 0;

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

        showToast("Reading Excel file...", "success");
        try {
            const data = await parseExcelFile(file);
            processParsedExcelRows(data);
            
            if (pdfParsedData.length === 0) {
                showToast("No matching records found in Excel for our faculty initials.", "error");
                return;
            }
            renderPreviewRows();
            openModal();
        } catch (error) {
            console.error("Excel Parse error: ", error);
            showToast("Failed to parse Excel: " + error.message, "error");
        }
    });

    // Modal control
    const openModal = () => {
        modal.classList.add("show");
        // Sync main form CC value to import CC input if empty
        const entryCc = document.getElementById("entry-cc");
        const importCc = document.getElementById("import-cc");
        if (entryCc && importCc && !importCc.value.trim()) {
            importCc.value = entryCc.value;
        }
        // Reset progress bar UI
        document.getElementById("importProgressContainer").style.display = "none";
        document.getElementById("importProgressBarFill").style.width = "0%";
        document.getElementById("validationSummaryContainer").style.display = "none";
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

    if (pdfParsedData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="12" style="text-align: center; color: var(--text-muted); padding: 24px;">No records with remarks found in this Excel file.</td></tr>`;
        importConfirmBtn.innerText = `Import Selected (0)`;
        importConfirmBtn.disabled = true;
        return;
    }

    importConfirmBtn.disabled = false;

    pdfParsedData.forEach((row, index) => {
        const tr = document.createElement("tr");
        tr.id = `pdf-row-${index}`;
        
        // Checked by default only if auto-detected dept matches currently selected portal tab AND has a valid remark
        const isCurrentDept = (row.selectedDept === currentDepartment);
        const isCheckedStr = (isCurrentDept && row.hasValidRemark) ? "checked" : "";

        // Build interactive dropdown select for Department
        const deptSelectHtml = `
            <select class="zs-row-dept-select" data-index="${index}" style="background: #0f172a; color: #f8fafc; border: 1px solid #334155; border-radius: 4px; padding: 4px; font-size: 11px; font-weight: 600; cursor: pointer;">
                <option value="Information Technology" ${row.selectedDept === "Information Technology" ? "selected" : ""}>IT</option>
                <option value="Computer Engineering" ${row.selectedDept === "Computer Engineering" ? "selected" : ""}>CE</option>
                <option value="" ${!row.selectedDept ? "selected" : ""}>Skip</option>
            </select>
        `;

        tr.innerHTML = `
            <td style="text-align: center;"><input type="checkbox" class="pdf-row-checkbox" data-index="${index}" ${isCheckedStr}></td>
            <td style="text-align: center; color: var(--text-muted); font-size: 11px;">${index + 1}</td>
            <td>${row.formattedDate || row.date}</td>
            <td><strong>${row.room}</strong></td>
            <td>${row.subject}</td>
            <td>${row.facultyLabel}</td>
            <td>${row.branch}</td>
            <td style="text-align: center;">${row.semester}</td>
            <td>${row.timeInStr} - ${row.timeOutStr}</td>
            <td style="text-align: center;">${row.noOfStudents}</td>
            <td style="font-size: 11px;">${row.remarks || "NO STUDENT"}</td>
            <td style="text-align: center;">${deptSelectHtml}</td>
        `;

        // Update selectedDept and check state on change
        const selectEl = tr.querySelector(".zs-row-dept-select");
        selectEl.addEventListener("change", (e) => {
            row.selectedDept = e.target.value;
            const cb = tr.querySelector(".pdf-row-checkbox");
            if (cb) {
                // Auto-check if department is selected, uncheck if set to Skip
                cb.checked = (e.target.value !== "");
            }
            importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
        });

        // Update select count on checkbox change
        const checkbox = tr.querySelector(".pdf-row-checkbox");
        checkbox.addEventListener("change", () => {
            importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
        });

        tbody.appendChild(tr);
    });

    importConfirmBtn.innerText = `Import Selected (${getSelectedCount()})`;
}

async function parseExcelFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const sheetName = workbook.SheetNames[0];
                const sheet = workbook.Sheets[sheetName];
                
                const rows = XLSX.utils.sheet_to_json(sheet, { header: 1, raw: false });
                if (rows.length === 0) {
                    resolve([]);
                    return;
                }

                // Find header row dynamically
                let headerIdx = -1;
                for (let r = 0; r < rows.length; r++) {
                    const row = rows[r];
                    if (row && row.some(cell => typeof cell === 'string' && cell.toUpperCase().includes("DATE")) &&
                        row.some(cell => typeof cell === 'string' && cell.toUpperCase().includes("SR NO"))) {
                        headerIdx = r;
                        break;
                    }
                }

                if (headerIdx === -1) {
                    throw new Error("Could not find table headers in Excel. Please verify Excel format.");
                }

                const headerRow = rows[headerIdx];
                let colMapping = {
                    date: -1, srNo: -1, room: -1, subject: -1, faculty: -1, alteration: -1, pt: -1,
                    branch: -1, semester: -1, timeIn: -1, timeOut: -1, remarks: -1, students: -1
                };

                headerRow.forEach((cell, idx) => {
                    if (!cell) return;
                    const hText = cell.toString().toUpperCase().trim();
                    if (hText.includes("DATE")) colMapping.date = idx;
                    else if (hText.includes("SR") || hText.includes("NO.")) colMapping.srNo = idx;
                    else if (hText.includes("CLASS") || hText.includes("LAB")) colMapping.room = idx;
                    else if (hText.includes("SUBJECT")) colMapping.subject = idx;
                    else if (hText.includes("FACULTY")) colMapping.faculty = idx;
                    else if (hText.includes("ALTERATION")) colMapping.alteration = idx;
                    else if (hText.includes("P/T")) colMapping.pt = idx;
                    else if (hText.includes("BRANCH")) colMapping.branch = idx;
                    else if (hText.includes("SEM")) colMapping.semester = idx;
                    else if (hText.includes("TIME IN")) colMapping.timeIn = idx;
                    else if (hText.includes("TIME OUT")) colMapping.timeOut = idx;
                    else if (hText.includes("REMARKS")) colMapping.remarks = idx;
                    else if (hText.includes("STUDENT")) colMapping.students = idx;
                });

                let parsedRows = [];
                let currentDate = "";
                let currentFormattedDate = "";
                
                const cleanStr = (val) => {
                    if (val === undefined || val === null) return "";
                    return val.toString().replace(/[\r\n]+/g, " ").replace(/\s+/g, " ").trim();
                };

                let totalRows = 0;

                for (let r = headerIdx + 1; r < rows.length; r++) {
                    const row = rows[r];
                    if (!row) continue;

                    const srNoVal = colMapping.srNo !== -1 ? cleanStr(row[colMapping.srNo]) : "";
                    const roomVal = colMapping.room !== -1 ? cleanStr(row[colMapping.room]) : "";
                    const subjectVal = colMapping.subject !== -1 ? cleanStr(row[colMapping.subject]) : "";
                    const rawDate = colMapping.date !== -1 ? cleanStr(row[colMapping.date]) : "";
                    const facultyInitials = colMapping.faculty !== -1 ? cleanStr(row[colMapping.faculty]) : "";
                    const alterationVal = colMapping.alteration !== -1 ? cleanStr(row[colMapping.alteration]) : "";
                    const ptVal = colMapping.pt !== -1 ? cleanStr(row[colMapping.pt]) : "";
                    const branchVal = colMapping.branch !== -1 ? cleanStr(row[colMapping.branch]) : "";
                    const semVal = colMapping.semester !== -1 ? cleanStr(row[colMapping.semester]) : "";
                    const timeInVal = colMapping.timeIn !== -1 ? cleanStr(row[colMapping.timeIn]) : "";
                    const timeOutVal = colMapping.timeOut !== -1 ? cleanStr(row[colMapping.timeOut]) : "";
                    const remarksVal = colMapping.remarks !== -1 ? cleanStr(row[colMapping.remarks]) : "";
                    const studentsVal = colMapping.students !== -1 ? cleanStr(row[colMapping.students]) : "";

                    if (!srNoVal && !roomVal && !subjectVal) continue;

                    totalRows++;

                    let parsedDateVal = "";
                    let formattedDate = "";
                    
                    if (rawDate) {
                        const res = formatPDFDate(rawDate);
                        currentDate = res.parsed;
                        currentFormattedDate = res.formatted;
                    }
                    parsedDateVal = currentDate;
                    formattedDate = currentFormattedDate;

                    const timeIn24 = parseTimeTo24h(timeInVal);
                    const timeOut24 = parseTimeTo24h(timeOutVal);

                    parsedRows.push({
                        srNo: srNoVal,
                        date: parsedDateVal,
                        formattedDate: formattedDate || rawDate,
                        room: roomVal.toUpperCase(),
                        subject: subjectVal.toUpperCase(),
                        faculty: facultyInitials.toUpperCase(),
                        alteration: alterationVal,
                        pt: ptVal.toUpperCase(),
                        branch: branchVal,
                        semester: semVal,
                        timeIn: timeIn24,
                        timeOut: timeOut24,
                        timeInStr: timeInVal,
                        timeOutStr: timeOutVal,
                        remarks: remarksVal,
                        noOfStudents: studentsVal || "---"
                    });
                }

                pdfTotalRowsDetected = totalRows;
                resolve(parsedRows);
            } catch (err) {
                reject(err);
            }
        };
        reader.onerror = () => reject(new Error("FileReader read error."));
        reader.readAsArrayBuffer(file);
    });
}

function processParsedExcelRows(allRows) {
    const normalizeBranch = (str) => {
        if (!str) return "";
        return str.toUpperCase().replace(/[\.\s\-\&]/g, "").replace(/[\(\)]/g, "");
    };

    let filtered = [];
    let seenKeys = new Set();

    allRows.forEach(row => {
        const cleanRemarks = row.remarks ? row.remarks.trim() : "";
        const hasValidRemark = (cleanRemarks !== "" && cleanRemarks !== "---");
        row.hasValidRemark = hasValidRemark;

        const clean = normalizeBranch(row.branch);

        // 2. Resolve faculty initials with mappings for variations
        let facInit = row.faculty.toUpperCase().trim();
        if (facInit === "PHC") facInit = "PHK";
        if (facInit === "PMM") facInit = "PMB";

        let matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === facInit);
        if (!matchedFaculty) {
            const parenMatch = row.faculty.match(/\(([^)]+)\)/);
            if (parenMatch) {
                let extractedInitials = parenMatch[1].toUpperCase().trim();
                if (extractedInitials === "PHC") extractedInitials = "PHK";
                if (extractedInitials === "PMM") extractedInitials = "PMB";
                matchedFaculty = facultyData.find(f => f.initials.toUpperCase() === extractedInitials);
            }
        }
        if (!matchedFaculty) {
            matchedFaculty = facultyData.find(f => {
                const cleanName = f.name.replace(/Prof\.\s*/i, "").toUpperCase().trim();
                return facInit.includes(cleanName) || facInit.includes(f.initials.toUpperCase());
            });
        }

        const facultyLabel = matchedFaculty ? `${matchedFaculty.name} (${matchedFaculty.initials})` : `Prof. ${facInit}`;
        const facultyDept = matchedFaculty ? matchedFaculty.department : "";

        // 3. Auto-detect department (CE/CSE vs IT vs Skip)
        let autoDept = "";
        if (clean) {
            const cleanLower = clean.toLowerCase();
            // Strict CE and CSE branch detection (excludes CS)
            const isCeOrCse = cleanLower.includes("computerengineering") || cleanLower.includes("cse") || (cleanLower.includes("computerscience") && cleanLower.includes("engineering"));
            const isItBranch = cleanLower.includes("it") || cleanLower.includes("info") || cleanLower.includes("ict");

            if (isCeOrCse) {
                autoDept = "Computer Engineering";
            } else if (isItBranch) {
                autoDept = "Information Technology";
            } else {
                // If branch is ambiguous, check the faculty's home department
                if (facultyDept === "Computer Engineering" || facultyDept === "Information Technology") {
                    autoDept = facultyDept;
                }
            }
        }

        const facultyEmail = matchedFaculty ? matchedFaculty.email : (autoDept === "Computer Engineering" ? "admincecse@gmiu.edu.in" : "adminit@gmiu.edu.in");

        row.resolvedFaculty = matchedFaculty ? matchedFaculty.initials : facInit;
        row.facultyLabel = facultyLabel;
        row.facultyEmail = facultyEmail;
        row.facultyDept = facultyDept;
        row.autoDept = autoDept;
        row.selectedDept = autoDept; // Default to auto-detected department

        const uniqueKey = `${row.date}|${row.srNo}|${row.room}|${row.subject}|${row.resolvedFaculty}|${row.branch}|${row.semester}|${row.timeInStr}-${row.timeOutStr}`;
        if (seenKeys.has(uniqueKey)) return;
        seenKeys.add(uniqueKey);

        filtered.push(row);
    });

    pdfParsedData = filtered;
    pdfEligibleRowsCount = filtered.length;
}

function formatPDFDate(dateStr) {
    if (!dateStr) return { parsed: "", formatted: "" };
    const cleanDate = dateStr.replace(/\s+/g, "");
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
    const monthRegex = new RegExp(`^(\\d{1,2})[\\/\\-]?(${monthNames.join("|")})[\\/\\-]?(\\d{4})$`, "i");
    const mMatch = cleanDate.match(monthRegex);
    if (mMatch) {
        const day = mMatch[1].padStart(2, '0');
        const monthName = mMatch[2];
        const year = mMatch[3];
        const monthIndex = monthNames.findIndex(m => m.toLowerCase() === monthName.toLowerCase());
        const monthNum = (monthIndex + 1).toString().padStart(2, '0');
        return {
            parsed: `${year}-${monthNum}-${day}`,
            formatted: `${parseInt(day, 10)}-${monthNames[monthIndex]}-${year}`
        };
    }

    const match = cleanDate.match(/^(\d{1,2})[\/\-]?(\d{1,2})[\/\-]?(\d{4})$/);
    if (match) {
        const day = match[1].padStart(2, '0');
        const month = match[2].padStart(2, '0');
        const year = match[3];
        const mIdx = parseInt(month, 10) - 1;
        if (mIdx >= 0 && mIdx < 12) {
            return {
                parsed: `${year}-${month}-${day}`,
                formatted: `${parseInt(day, 10)}-${monthNames[mIdx]}-${year}`
            };
        }
    }
    
    const dateParts = cleanDate.split(/[\/\-]/);
    if (dateParts.length === 3) {
        let day = dateParts[0].padStart(2, '0');
        let month = dateParts[1].padStart(2, '0');
        let year = dateParts[2];
        if (year.length === 2) year = "20" + year;
        const mIdx = parseInt(month, 10) - 1;
        if (mIdx >= 0 && mIdx < 12) {
            return {
                parsed: `${year}-${month}-${day}`,
                formatted: `${parseInt(day, 10)}-${monthNames[mIdx]}-${year}`
            };
        }
    }
    
    return { parsed: "", formatted: "" };
}

function parseTimeTo24h(timeStr) {
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

    const importCcVal = getCcEmails("import-cc");
    const mainCcVal = getCcEmails("entry-cc");
    const extraUserCc = importCcVal.length > 0 ? importCcVal : mainCcVal;

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
            // Get selected department from the dropdown of the current row
            let targetDept = row.selectedDept;
            if (tr) {
                const selectEl = tr.querySelector(".zs-row-dept-select");
                if (selectEl) {
                    targetDept = selectEl.value;
                }
            }

            if (!targetDept) {
                // If set to Skip, bypass this row
                continue;
            }

            const deptAbbr = (targetDept === "Computer Engineering") ? "CE" : "IT";

            const sheetsPayload = {
                date: row.formattedDate,
                srNo: row.srNo || "---",
                room: row.room,
                subject: row.subject,
                faculty: row.resolvedFaculty,
                alteration: row.alteration || "---",
                pt: row.pt || "---",
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
                                <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 12px;">Department of ${deptAbbr}</p>
                            </div>
                            <div style="padding: 24px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #334155;">
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 10px 0; font-weight: bold; width: 150px; color: #64748b; text-transform: uppercase;">Faculty Initials</td>
                                        <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">${row.resolvedFaculty} (${row.facultyName})</td>
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
                                <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="https://engineering.gt.tc/" target="_blank" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">${deptAbbr} DEPARTMENT</a>.</p>
                                <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 ALL RIGHTS RESERVED.</p>
                                <p style="margin: 6px 0 0 0; font-size: 11px; color: #64748b; font-family: 'Playfair Display', serif;"><a href="https://engineering.gt.tc/" target="_blank" style="color: ${deptAbbr === 'CE' ? '#2563eb' : '#8c1d1d'}; text-decoration: underline; font-weight: 600; font-family: 'Playfair Display', serif;">https://engineering.gt.tc/</a></p>
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

            // Build row-specific CC list: Dhaval Sir + target department Incharge HOD + user CCs
            const rowDefaultCc = getDefaultCcForDept(targetDept);
            const itHodEmail = "sbchauhan@gmiu.edu.in";
            const ceHodEmail = "ehunagar@gmiu.edu.in";
            let combinedCc = [...rowDefaultCc, ...extraUserCc];
            if (targetDept === "Computer Engineering") {
                combinedCc = combinedCc.filter(e => e !== itHodEmail);
            } else {
                combinedCc = combinedCc.filter(e => e !== ceHodEmail);
            }
            const rowCcEmails = Array.from(new Set(combinedCc));

            // Send email (catch any connection reset/close errors locally so sheet write success is kept)
            try {
                await fetch('send-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        to: row.facultyEmail,
                        cc: rowCcEmails,
                        subject: `Zero Student As Per Timetable — ${row.resolvedFaculty} (${deptAbbr})`,
                        html: emailHtml,
                        module: 'zero',
                        dept: deptAbbr
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

    // Show and populate Validation Summary
    const valSummary = document.getElementById("validationSummaryContainer");
    if (valSummary) {
        valSummary.style.display = "block";
        document.getElementById("val-total").innerText = pdfTotalRowsDetected;
        document.getElementById("val-eligible").innerText = pdfEligibleRowsCount;
        document.getElementById("val-imported").innerText = successCount;
        document.getElementById("val-missing").innerText = failCount;
        document.getElementById("val-duplicate").innerText = duplicateCount;
        
        const statusBox = document.getElementById("val-status-box");
        const detailsBox = document.getElementById("val-mismatch-details");
        
        if (failCount === 0) {
            statusBox.style.background = "rgba(16, 185, 129, 0.2)";
            statusBox.style.color = "#10b981";
            statusBox.style.border = "1px solid #10b981";
            statusBox.innerText = "✓ Perfect Match";
            detailsBox.style.display = "none";
        } else {
            statusBox.style.background = "rgba(239, 68, 68, 0.2)";
            statusBox.style.color = "#ef4444";
            statusBox.style.border = "1px solid #ef4444";
            statusBox.innerText = "✗ Mismatch";
            
            detailsBox.style.display = "block";
            detailsBox.innerHTML = `<strong>Error Details:</strong><br>` + errorDetails.join("<br>");
        }
    }

    let finalMsg = `Excel Import complete: ${successCount} success`;
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
        document.body.classList.add("ce-active");
        if (badgeText) badgeText.innerText = "Department of Computer Engineering";
        localStorage.setItem('portal_dept', 'CE');
        document.title = "Zero Student Log — CE Department";
    } else {
        if (ceBtn) ceBtn.classList.remove("active");
        if (itBtn) itBtn.classList.add("active");
        if (pageContainer) pageContainer.classList.remove("ce-active");
        document.body.classList.remove("ce-active");
        if (badgeText) badgeText.innerText = "Department of Information Technology";
        localStorage.setItem('portal_dept', 'IT');
        document.title = "Zero Student Log — IT Department";
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

    // Update default CC field based on selected department (Dhaval Sir + Dept Incharge HOD)
    updateCcFieldForDept(dept);
}

function getActiveBranchList() {
    return currentDepartment === "Computer Engineering" ? defaultBranchesCE : defaultBranchesIT;
}

// ── CC Email Helper & Autocomplete ──
function getDefaultCcForDept(dept) {
    const dhavalEmail = "drchandarana@gmiu.edu.in";
    const itHodEmail = "sbchauhan@gmiu.edu.in";
    const ceHodEmail = "ehunagar@gmiu.edu.in";

    if (dept === "Computer Engineering" || dept === "CE") {
        return [dhavalEmail, ceHodEmail];
    } else {
        return [dhavalEmail, itHodEmail];
    }
}

function updateCcFieldForDept(dept) {
    const ccInput = document.getElementById("entry-cc");
    if (!ccInput) return;

    const dhavalEmail = "drchandarana@gmiu.edu.in";
    const itHodEmail = "sbchauhan@gmiu.edu.in";
    const ceHodEmail = "ehunagar@gmiu.edu.in";

    let currentVal = ccInput.value.trim();
    if (!currentVal) {
        const defaults = getDefaultCcForDept(dept);
        ccInput.value = defaults.join(", ") + ", ";
        return;
    }

    let parts = currentVal.split(/[\s,;]+/).map(e => e.trim()).filter(Boolean);

    // Always ensure Dhaval Sir is included
    if (!parts.includes(dhavalEmail)) {
        parts.unshift(dhavalEmail);
    }

    if (dept === "Computer Engineering" || dept === "CE") {
        parts = parts.filter(e => e !== itHodEmail);
        if (!parts.includes(ceHodEmail)) {
            const idx = parts.indexOf(dhavalEmail);
            parts.splice(idx >= 0 ? idx + 1 : 0, 0, ceHodEmail);
        }
    } else {
        parts = parts.filter(e => e !== ceHodEmail);
        if (!parts.includes(itHodEmail)) {
            const idx = parts.indexOf(dhavalEmail);
            parts.splice(idx >= 0 ? idx + 1 : 0, 0, itHodEmail);
        }
    }

    ccInput.value = Array.from(new Set(parts)).join(", ") + ", ";
}

function getCcEmails(inputId = "entry-cc") {
    const input = document.getElementById(inputId);
    if (!input || !input.value.trim()) return [];
    const parts = input.value.split(/[\s,;]+/);
    const emails = [];
    parts.forEach(p => {
        const clean = p.trim();
        if (clean) {
            emails.push(clean);
        }
    });
    return emails;
}

function initCcAutocomplete(inputId, dropdownId) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    if (!input || !dropdown) return;

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
        const fullVal = input.value;
        const currentSegment = fullVal.split(/[\s,;]+/).pop().toLowerCase().trim();
        let filtered = facultyData;
        if (currentSegment) {
            filtered = facultyData.filter(m =>
                m.name.toLowerCase().includes(currentSegment) ||
                m.initials.toLowerCase().includes(currentSegment) ||
                (m.email && m.email.toLowerCase().includes(currentSegment))
            );
        }
        renderList(filtered);
    }

    function renderList(list) {
        dropdown.innerHTML = "";
        if (list.length === 0) {
            dropdown.innerHTML = `<div class="no-results-item">No matching faculty emails</div>`;
            return;
        }

        list.slice(0, 10).forEach(member => {
            if (!member.email) return;
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                <div class="item-info">
                    <div class="item-name">${member.name} (${member.initials})</div>
                    <div class="item-desg" style="color: #60a5fa; font-weight: 500;">${member.email}</div>
                </div>
            `;
            item.addEventListener("click", () => {
                const parts = input.value.split(/,\s*/);
                parts.pop();
                parts.push(member.email);
                input.value = parts.filter(Boolean).join(", ") + ", ";
                dropdown.classList.remove("show");
                input.focus();
            });
            dropdown.appendChild(item);
        });
    }
}

