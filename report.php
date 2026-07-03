<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="GMIU IT Department — Departmental Report Management System for academic events, Expert talks, and achievement documentation.">
    <title>IT Report System — GMIU IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/vendor/bootstrap-icons/bootstrap-icons.min.css">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Custom Dark Glassmorphic & Print Styling imported from style.css -->
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
        <header class="rp-header container">
            <div class="rp-header-inner">
                <a href="index" class="back-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Portal
                </a>

                <div class="rp-header-center">
                    <div class="rp-dept-badge">
                        <span class="rp-badge-dot"></span>
                        Department of Information Technology
                    </div>
                    <h1 class="rp-title">Report Management System</h1>
                    <p class="rp-subtitle">Gyanmanjari Innovative University &nbsp;·&nbsp; Admin Portal</p>
                </div>

                <span class="portal-badge">IT Admin</span>
            </div>
        </header>

        <!-- Stepper Component -->
        <div class="container stepper-wrapper">
            <div class="stepper">
                <div class="stepper-progress-bar" id="stepperProgressBar"></div>
                <div class="step-node active" data-step="1" onclick="jumpToSection(1)">
                    <div class="step-circle">1</div>
                    <div class="step-title">Requested By</div>
                </div>
                <div class="step-node disabled-step" data-step="2" onclick="jumpToSection(2)">
                    <div class="step-circle">2</div>
                    <div class="step-title">Basic Info</div>
                </div>
                <div class="step-node disabled-step" data-step="3" onclick="jumpToSection(3)">
                    <div class="step-circle">3</div>
                    <div class="step-title">Specific Info</div>
                </div>
                <div class="step-node disabled-step" data-step="4" onclick="jumpToSection(4)">
                    <div class="step-circle">4</div>
                    <div class="step-title">Submit Report</div>
                </div>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="container mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0 rp-breadcrumb-list">
                    <li class="breadcrumb-item"><a href="index" class="text-decoration-none text-muted">Home</a>
                    </li>
                    <li class="breadcrumb-item active text-danger" aria-current="page">Report Management System</li>
                </ol>
            </nav>
        </div>

        <!-- ── Main Form Accordion Card ── -->
        <main class="rp-main container">
            <form id="reportForm" novalidate>
                <div class="accordion report-accordion" id="reportAccordion">

                    <!-- Section 1: Faculty Profile (Requested By) -->
                    <div class="accordion-item" id="secItemOne">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="bi bi-person-fill-gear me-2"></i> Section 1: Faculty Profile (Requested By)
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#reportAccordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="facultySearch">Faculty Name <span
                                                    class="text-danger">*</span></label>
                                            <div class="search-select-wrap">
                                                <input type="text" class="form-control" id="facultySearch"
                                                    placeholder="Type to search faculty name (e.g., Prof. Dhaval Chandarana)..."
                                                    autocomplete="off" required>
                                                <input type="hidden" id="facultyId" name="facultyId">
                                                <div class="search-dropdown-list" id="facultyDropdownList"></div>
                                            </div>
                                            <div class="invalid-feedback">Please select a valid faculty member.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="facultyDesignation">Designation</label>
                                            <input type="text" class="form-control" id="facultyDesignation"
                                                placeholder="Auto-filled..." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="facultyEmpId">Employee ID</label>
                                            <input type="text" class="form-control" id="facultyEmpId"
                                                placeholder="Auto-filled..." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="facultyEmail">Email Address</label>
                                            <input type="email" class="form-control" id="facultyEmail"
                                                placeholder="Auto-filled..." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="facultyPhone">Mobile Number</label>
                                            <input type="text" class="form-control" id="facultyPhone"
                                                placeholder="Auto-filled..." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="facultyDept">Department</label>
                                            <input type="text" class="form-control" id="facultyDept"
                                                value="Information Technology" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-end mt-4">
                                        <button type="button" class="btn btn-danger px-4"
                                            onclick="goToNextSection(1)">Next Section <i
                                                class="bi bi-arrow-right ms-2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Basic Report Information -->
                    <div class="accordion-item d-none" id="secItemTwo">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="bi bi-file-earmark-text-fill me-2"></i> Section 2: Basic Report Information
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#reportAccordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="academicYear">Academic Year <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="academicYear" required>
                                            <option value="2026-27" selected>2026-27</option>
                                        </select>
                                        <div class="invalid-feedback">Academic Year is required.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="reportType">Report Type <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="reportType" onchange="toggleReportTypeFields()"
                                            required>
                                            <option value="" disabled selected>Select Report Type...</option>
                                            <option value="training_placement">Training & Placement Activity</option>
                                            <option value="departmental">Departmental Activity</option>
                                            <option value="startup">Startup Activity</option>
                                            <option value="research">Research Activity</option>
                                            <option value="international_relational">International Relational Activity</option>
                                            <option value="central">Central Activity</option>
                                        </select>
                                        <div class="mt-2 d-none" id="customReportTypeWrap">
                                            <label class="form-label text-warning small" for="customReportType">Custom
                                                Report Type Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customReportType"
                                                placeholder="Enter custom report type..." oninput="syncEmailPreview()">
                                            <div class="invalid-feedback">Please specify the custom report type.</div>
                                        </div>
                                        <div class="invalid-feedback">Report Type is required.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="reportTitle">Report Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="reportTitle"
                                            placeholder="Enter report title..." oninput="syncEmailPreview()" required>
                                        <div class="invalid-feedback">Report Title is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="activityDate">Activity Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control form-control-dark-input"
                                            id="activityDate" required>
                                        <div class="invalid-feedback">Activity Date is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="startTime">Start Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control form-control-dark-input" id="startTime"
                                            required>
                                        <div class="invalid-feedback">Start Time is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="endTime">End Time <span
                                                class="text-danger">*</span></label>
                                        <input type="time" class="form-control form-control-dark-input" id="endTime"
                                            required>
                                        <div class="invalid-feedback">End Time is required.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="venue">Venue <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="venue"
                                            placeholder="e.g., FF-11, Auditorium..." required>
                                        <div class="invalid-feedback">Venue is required.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Programme <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="Diploma" id="progDiploma">
                                                <label class="form-check-label text-light"
                                                    for="progDiploma">Diploma</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="B.Tech" id="progBTech">
                                                <label class="form-check-label text-light"
                                                    for="progBTech">B.Tech</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="M.Tech" id="progMTech">
                                                <label class="form-check-label text-light"
                                                    for="progMTech">M.Tech</label>
                                            </div>
                                        </div>
                                        <div class="text-danger d-none rp-error-text" id="progError">Select at least one
                                            programme.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="semester">Semester <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="semester"
                                            placeholder="e.g., 4, 6..." 
                                            oninput="this.value = this.value.replace(/[^0-9,\s-]/g, '')" required>
                                        <div class="invalid-feedback">Semester is required (numbers only).</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="divisionClass">Division/Class <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="divisionClass"
                                            placeholder="e.g., C & G, Div A..." required>
                                        <div class="invalid-feedback">Division is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="participantsCount">Number of Participants <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="participantsCount" min="1"
                                            placeholder="e.g., 60" required>
                                        <div class="invalid-feedback">Participants count must be greater than 0.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="batch">Batch <span class="text-danger">*</span></label>
                                        <select class="form-select" id="batch" required>
                                            <option value="" disabled selected>Select Batch...</option>
                                            <option value="Batch 2023">Batch 2023</option>
                                            <option value="Batch 2024">Batch 2024</option>
                                            <option value="Batch 2025">Batch 2025</option>
                                            <option value="Batch 2026">Batch 2026</option>
                                        </select>
                                        <div class="invalid-feedback">Batch is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="studentCoordinator">Student Coordinator</label>
                                        <input type="text" class="form-control" id="studentCoordinator" placeholder="Enter student coordinator name...">
                                    </div>
                                    <div class="col-md-4">
                                        <!-- Spacer grid to keep layout balanced -->
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-light">Report is published on GMIU Website? <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="publishWebsite" id="publishYes" value="Yes" required>
                                                <label class="form-check-label text-light" for="publishYes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="publishWebsite" id="publishNo" value="No" required>
                                                <label class="form-check-label text-light" for="publishNo">No</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback text-danger">Please choose an option.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-light">Press Note Required? <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pressNote" id="pressYes" value="Yes" required>
                                                <label class="form-check-label text-light" for="pressYes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pressNote" id="pressNo" value="No" checked required>
                                                <label class="form-check-label text-light" for="pressNo">No</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback text-danger">Please choose an option.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="coordinators">Faculty Coordinator(s) <span
                                                class="text-danger">*</span></label>
                                        <div class="custom-multiselect-wrap">
                                            <input type="hidden" id="coordinators" name="coordinators" required>
                                            
                                            <!-- Custom UI representing the multi-select -->
                                            <div class="multiselect-trigger form-control" id="coordEmailsTrigger">
                                                <span class="placeholder-text" id="coordPlaceholder">Select Faculty Coordinator(s)...</span>
                                                <div class="selected-badges-container d-none" id="coordSelectedBadges"></div>
                                                <i class="bi bi-chevron-down multiselect-arrow-icon"></i>
                                            </div>
                                            
                                            <div class="multiselect-dropdown" id="coordDropdown">
                                                <div class="multiselect-search-wrap">
                                                    <i class="bi bi-search search-icon"></i>
                                                    <input type="text" class="form-control form-control-sm" id="coordSearchInput" placeholder="Search faculty member..." autocomplete="off">
                                                </div>
                                                <div class="multiselect-options-list" id="coordOptionsList">
                                                    <!-- Javascript populated -->
                                                </div>
                                            </div>
                                            <div class="invalid-feedback">Faculty Coordinator is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="briefObjective">Brief Objective <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="briefObjective" rows="3"
                                            placeholder="Define the primary objective of this activity..."
                                            required></textarea>
                                        <div class="invalid-feedback">Brief Objective is required.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="activityPhotos">Upload Activity Photos (ZIP
                                            format only) <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="activityPhotos" accept=".zip">
                                        <div class="selected-files-list" id="activityPhotosList"></div>
                                        <div class="invalid-feedback">Please upload a ZIP file containing the photos, or
                                            enter a Google Drive link below.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="driveLink">Or Google Drive Link <span
                                                class="text-danger">*</span></label>
                                        <input type="url" class="form-control" id="driveLink"
                                            placeholder="https://drive.google.com/..." oninput="syncEmailPreview()">
                                        <div class="form-text text-muted small mt-1">
                                            Please upload a ZIP file of the photos OR provide a Google Drive link.
                                        </div>
                                        <div class="invalid-feedback">Please enter a valid Google Drive link, or upload
                                            a ZIP file above.</div>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-light px-4"
                                            onclick="goToPrevSection(2)"><i class="bi bi-arrow-left me-2"></i> Previous
                                            Section</button>
                                        <button type="button" class="btn btn-danger px-4"
                                            onclick="goToNextSection(2)">Next Section <i
                                                class="bi bi-arrow-right ms-2"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Dynamic Report-Specific Information -->
                    <div class="accordion-item d-none" id="secItemThree">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="bi bi-grid-3x3-gap-fill me-2"></i> Section 3: Dynamic Report-Specific
                                Information
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#reportAccordion">
                            <div class="accordion-body">
                                <div id="dynamicPlaceholder" class="text-center py-4 text-muted rp-dynamic-placeholder">
                                    <i class="bi bi-exclamation-triangle-fill fs-3 text-warning d-block mb-2"></i>
                                    PLEASE SELECT A REPORT TYPE IN SECTION 2 TO DISPLAY SPECIFIC FIELDS.
                                </div>
                                                     <!-- Dynamic Training & Placement Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-training_placement">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label text-light">Placement Activity Type <span class="text-danger">*</span></label>
                                            <div class="d-flex gap-3 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input dynamic-field" type="radio" name="placementActType" id="typeRLM" value="RLM" onchange="togglePlacementActType()">
                                                    <label class="form-check-label text-light" for="typeRLM">RLM</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input dynamic-field" type="radio" name="placementActType" id="typePLM" value="PLM" onchange="togglePlacementActType()">
                                                    <label class="form-check-label text-light" for="typePLM">PLM</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- RLM Activity Dropdown -->
                                        <div class="col-md-12 d-none" id="rlmDropdownWrap">
                                            <label class="form-label" for="rlmActivity">RLM Activity <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="rlmActivity" onchange="toggleRlmCustomActivity()">
                                                <option value="" disabled selected>Select RLM Activity...</option>
                                                <option value="1. Domain Selection Process">1. Domain Selection Process</option>
                                                <option value="2. Industrial Visit">2. Industrial Visit</option>
                                                <option value="3. Expert Talk">3. Expert Talk</option>
                                                <option value="4. Workshop">4. Workshop</option>
                                                
                                                <optgroup label="Projects">
                                                    <option value="5.1 Projects - Social Impact Projects">5.1 Social Impact Projects</option>
                                                    <option value="5.2 Projects - Project Based Learning">5.2 Project Based Learning</option>
                                                    <option value="5.3 Projects - Micro">5.3 Micro</option>
                                                    <option value="5.4 Projects - Minor">5.4 Minor</option>
                                                    <option value="5.5 Projects - Major">5.5 Major</option>
                                                </optgroup>

                                                <optgroup label="Exposure to Cutting Edge Technology">
                                                    <option value="6.1 Exposure to Cutting Edge Technology - In - House (For Genius)">6.1 In - House (For Genius)</option>
                                                    <option value="6.2 Exposure to Cutting Edge Technology - Online">6.2 Online</option>
                                                </optgroup>

                                                <optgroup label="Technical Event / Competition Participation">
                                                    <option value="7.1 Technical Event / Competition Participation - In - House">7.1 In - House</option>
                                                    <option value="7.2 Technical Event / Competition Participation - State Level (Colleges)">7.2 State Level (Colleges)</option>
                                                    <option value="7.3 Technical Event / Competition Participation - National Level">7.3 National Level</option>
                                                </optgroup>

                                                <option value="8. Communication Skill Enhancement Activity">8. Communication Skill Enhancement Activity</option>
                                                <option value="9. Managerial / Leadership Skill Enhancement Activity">9. Managerial / Leadership Skill Enhancement Activity</option>
                                                <option value="10. Association (Student Chapter) Activities">10. Association (Student Chapter) Activities</option>

                                                <optgroup label="Pre - Placement Activity">
                                                    <option value="11.1 Pre - Placement Activity - Resume Building">11.1 Resume Building</option>
                                                    <option value="11.2 Pre - Placement Activity - Mock Interview">11.2 Mock Interview</option>
                                                    <option value="11.3 Pre - Placement Activity - Aptitude Test">11.3 Aptitude Test</option>
                                                    <option value="11.4 Pre - Placement Activity - Logical Reasoning Test">11.4 Logical Reasoning Test</option>
                                                    <option value="11.5 Pre - Placement Activity - Group Discussion">11.5 Group Discussion</option>
                                                    <option value="11.6 Pre - Placement Activity - Personality Grooming">11.6 Personality Grooming</option>
                                                    <option value="11.7 Pre - Placement Activity - Pre Placement Talk">11.7 Pre Placement Talk</option>
                                                </optgroup>

                                                <option value="12. Special Event (Technical)">12. Special Event (Technical)</option>

                                                <optgroup label="GEPS Activity">
                                                    <option value="13.1 GEPS Activity - GEPS Card">13.1 GEPS Card</option>
                                                    <option value="13.2 GEPS Activity - IQ / EQ Test">13.2 IQ / EQ Test</option>
                                                    <option value="13.3 GEPS Activity - Practical Skill Proficiency">13.3 Practical Skill Proficiency</option>
                                                    <option value="13.4 GEPS Activity - Software Skill Proficiency">13.4 Software Skill Proficiency</option>
                                                    <option value="13.5 GEPS Activity - Linkedin Profile">13.5 Linkedin Profile</option>
                                                    <option value="13.6 GEPS Activity - Free Lancer Profile">13.6 Free Lancer Profile</option>
                                                </optgroup>
                                                <option value="14. Internship / Training">14. Internship / Training</option>
                                                <option value="15. Field Visit">15. Field Visit</option>
                                                <option value="16. Alumni Interaction (Meet the Mastermind)">16. Alumni Interaction (Meet the Mastermind)</option>
                                                <option value="17. Prominent Speaker (Celebrity) Talk">17. Prominent Speaker (Celebrity) Talk</option>
                                                <option value="18. Startup & Incubation">18. Startup & Incubation</option>
                                                <option value="19. International Exposure">19. International Exposure</option>
                                                <option value="20. Research Activity">20. Research Activity</option>
                                                <option value="21. Guidance for Higher & Overseas Study">21. Guidance for Higher & Overseas Study</option>
                                                <option value="22. Entrepreneurship Development Program">22. Entrepreneurship Development Program</option>
                                                <option value="23. Earn while Learn">23. Earn while Learn</option>
                                                <option value="24. Professional Guidance & Development">24. Professional Guidance & Development</option>
                                                <option value="25. Stress Relief Activity (Ex. Yoga / Meditation etc.)">25. Stress Relief Activity (Ex. Yoga / Meditation etc.)</option>
                                                <option value="26. Innovative Teaching - Learning (Ex. Flipped / Blended Mode etc.)">26. Innovative Teaching - Learning (Ex. Flipped / Blended Mode etc.)</option>
                                                <option value="other">Other</option>
                                            </select>
                                            
                                            <!-- Custom RLM Activity input -->
                                            <div class="mt-2 d-none" id="customRlmActivityWrap">
                                                <label class="form-label text-warning small" for="customRlmActivity">Custom RLM Activity Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customRlmActivity" placeholder="Enter custom activity name...">
                                                <div class="invalid-feedback">Please specify the custom RLM activity name.</div>
                                            </div>
                                        </div>

                                        <!-- PLM Activity Dropdown -->
                                        <div class="col-md-12 d-none" id="plmDropdownWrap">
                                            <label class="form-label" for="plmActivity">PLM Activity <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="plmActivity" onchange="togglePlmCustomActivity()">
                                                <option value="" disabled selected>Select PLM Activity...</option>
                                                <optgroup label="Industrial (Real World Exposure) - Experiential Learning">
                                                    <option value="1. Expert Talk / Workshop (Industrial Expert)">1. Expert Talk / Workshop (Industrial Expert)</option>
                                                    <option value="2. Internship (Domestic / International)">2. Internship (Domestic / International)</option>
                                                    <option value="3. Real World Problem Solving (with industrial Tie Ups) Live Project (At least Once)">3. Real World Problem Solving (with industrial Tie Ups) Live Project (At least Once)</option>
                                                    <option value="4. Vocational Training (Optional)">4. Vocational Training (Optional)</option>
                                                </optgroup>
                                                <optgroup label="Soft Skill Development">
                                                    <option value="5. Participation in Debate">5. Participation in Debate</option>
                                                    <option value="6. GD">6. GD</option>
                                                    <option value="7. Public Speaking Experience">7. Public Speaking Experience</option>
                                                    <option value="8. Logical Reasoning & Quantitative Aptitude Test">8. Logical Reasoning & Quantitative Aptitude Test</option>
                                                </optgroup>
                                                <optgroup label="Pre-Placement Activities">
                                                    <option value="9. Resume Building">9. Resume Building</option>
                                                    <option value="10. Updating Resume">10. Updating Resume</option>
                                                    <option value="11. Mock Interview">11. Mock Interview</option>
                                                    <option value="12. Free Lancer Profile">12. Free Lancer Profile</option>
                                                </optgroup>
                                                <optgroup label="Problem Solving Skill Development">
                                                    <option value="13. Projects (Different Level) Micro, Mini, Major">13. Projects (Different Level) Micro, Mini, Major</option>
                                                    <option value="14. Event Participation (Hackathon etc.) (At least Once)">14. Event Participation (Hackathon etc.) (At least Once)</option>
                                                    <option value="15. Join / Start Innovation Clubs">15. Join / Start Innovation Clubs</option>
                                                    <option value="16. Case Study Analysis (At least Once)">16. Case Study Analysis (At least Once)</option>
                                                </optgroup>
                                                <optgroup label="Cutting Edge Technology">
                                                    <option value="17. External Certification Program (At least Once)">17. External Certification Program (At least Once)</option>
                                                    <option value="18. Online Course / MOOC">18. Online Course / MOOC</option>
                                                    <option value="19. Skill Development Program (At least Two) Online / In House">19. Skill Development Program (At least Two) Online / In House</option>
                                                </optgroup>
                                                <optgroup label="Networking & Community Engagement">
                                                    <option value="20. Membership of Professional Society">20. Membership of Professional Society</option>
                                                    <option value="21. Alumni Connect">21. Alumni Connect</option>
                                                    <option value="22. LinkedIn Profile">22. LinkedIn Profile</option>
                                                    <option value="23. Enrich LinkedIn Network">23. Enrich LinkedIn Network</option>
                                                </optgroup>
                                                <optgroup label="Placement">
                                                    <option value="24. Preparing Company Profile (Targeted Company)">24. Preparing Company Profile (Targeted Company)</option>
                                                    <option value="25. Placement Examination (eg TCS NQT etc) (At least once)">25. Placement Examination (eg TCS NQT etc) (At least once)</option>
                                                    <option value="26. Placement Drive">26. Placement Drive</option>
                                                </optgroup>
                                                <optgroup label="Research & Development">
                                                    <option value="27. Research Area Selection">27. Research Area Selection</option>
                                                    <option value="28. Preparing Research Plan">28. Preparing Research Plan</option>
                                                    <option value="29. Literature Review">29. Literature Review</option>
                                                    <option value="30. Poster Presentation">30. Poster Presentation</option>
                                                    <option value="31. Review Paper">31. Review Paper</option>
                                                    <option value="32. Major Research Project">32. Major Research Project</option>
                                                    <option value="33. IPR Awareness Program">33. IPR Awareness Program</option>
                                                    <option value="34. Research Paper Publication / Patent">34. Research Paper Publication / Patent</option>
                                                    <option value="35. Attending Conference">35. Attending Conference</option>
                                                </optgroup>
                                                <optgroup label="Startup & Entrepreneurship (Innovation)">
                                                    <option value="36. Participation (Ideathon)">36. Participation (Ideathon)</option>
                                                    <option value="37. Idea Generation (Innovative / Unique)">37. Idea Generation (Innovative / Unique)</option>
                                                    <option value="38. Project-based Entrepreneurial Learning">38. Project-based Entrepreneurial Learning</option>
                                                    <option value="39. Product Development (Concept to Product)">39. Product Development (Concept to Product)</option>
                                                    <option value="40. Usage of Collaborative Platform (Like GitHub)">40. Usage of Collaborative Platform (Like GitHub)</option>
                                                    <option value="41. Develop a Business Plan">41. Develop a Business Plan</option>
                                                    <option value="42. Join / Visit Incubators / Accelerators">42. Join / Visit Incubators / Accelerators</option>
                                                    <option value="43. Conduct Market Analysis">43. Conduct Market Analysis</option>
                                                    <option value="44. Build a minimum viable product">44. Build a minimum viable product</option>
                                                    <option value="45. Industrial Mentoring (Expert)">45. Industrial Mentoring (Expert)</option>
                                                    <option value="46. Establish A Startup">46. Establish A Startup</option>
                                                    <option value="47. Attend startup pitch competitions">47. Attend startup pitch competitions</option>
                                                    <option value="48. Seek for Funding">48. Seek for Funding</option>
                                                </optgroup>
                                                <optgroup label="Global Exposure">
                                                    <option value="49. International Summer Camp (Participation)">49. International Summer Camp (Participation)</option>
                                                    <option value="50. Participation in Exchange Program">50. Participation in Exchange Program</option>
                                                    <option value="51. Participate in Cultural / Immersion Program">51. Participate in Cultural / Immersion Program</option>
                                                    <option value="52. International Internship (Physical / Remote)">52. International Internship (Physical / Remote)</option>
                                                </optgroup>
                                                <option value="other">Other</option>
                                            </select>
                                            
                                            <!-- Custom PLM Activity input -->
                                            <div class="mt-2 d-none" id="customPlmActivityWrap">
                                                <label class="form-label text-warning small" for="customPlmActivity">Custom PLM Activity Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customPlmActivity" placeholder="Enter custom activity name...">
                                                <div class="invalid-feedback">Please specify the custom PLM activity name.</div>
                                            </div>
                                        </div>

                                        <!-- Placement Activity Sub-Type Dropdown -->
                                        <div class="col-md-6">
                                            <label class="form-label" for="placementSubType">Activity Sub-Type</label>
                                            <select class="form-select dynamic-field" id="placementSubType" onchange="togglePlacementCustomSubType()">
                                                <option value="" selected>Select Sub-Type...</option>
                                                <option value="Expert Talk">Expert Talk</option>
                                                <option value="Workshop">Workshop</option>
                                                <option value="Industrial Visit">Industrial Visit</option>
                                                <option value="Seminar">Seminar</option>
                                                <option value="Flip Class">Flip Class</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            
                                            <!-- Custom Placement Activity Sub-Type input -->
                                            <div class="mt-2 d-none" id="customPlacementSubTypeWrap">
                                                <label class="form-label text-warning small" for="customPlacementSubType">Custom Sub-Type Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customPlacementSubType" placeholder="Enter custom sub-type...">
                                                <div class="invalid-feedback">Please specify the custom sub-type.</div>
                                            </div>
                                        </div>

                                        <!-- Placement Speaker Details -->
                                        <div class="col-md-6">
                                            <label class="form-label" for="placementSpeaker">Speaker / Resource Person Details</label>
                                            <input type="text" class="form-control dynamic-field" id="placementSpeaker" placeholder="Enter speaker name & details...">
                                        </div>

                                        <!-- Placement Key Takeaways & Outcomes -->
                                        <div class="col-md-12">
                                            <label class="form-label" for="placementOutcomes">Key Takeaways & Outcomes</label>
                                            <textarea class="form-control dynamic-field" id="placementOutcomes" rows="3" placeholder="Enter session outcomes..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Departmental Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-departmental">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="departmentalSubType">Activity Sub-Type <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="departmentalSubType" onchange="toggleDepartmentalCustomSubType()">
                                                <option value="" disabled selected>Select Sub-Type...</option>
                                                <option value="Expert Talk">Expert Talk</option>
                                                <option value="Workshop">Workshop</option>
                                                <option value="Industrial Visit">Industrial Visit</option>
                                                <option value="Seminar">Seminar</option>
                                                <option value="Flip Class">Flip Class</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            
                                            <!-- Custom Departmental Activity Sub-Type input -->
                                            <div class="mt-2 d-none" id="customDepartmentalSubTypeWrap">
                                                <label class="form-label text-warning small" for="customDepartmentalSubType">Custom Sub-Type Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customDepartmentalSubType" placeholder="Enter custom sub-type...">
                                                <div class="invalid-feedback">Please specify the custom sub-type.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="departmentalSpeaker">Speaker / Resource Person Details</label>
                                            <input type="text" class="form-control dynamic-field" id="departmentalSpeaker" placeholder="Enter speaker name & details...">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="departmentalOutcomes">Key Takeaways & Outcomes <span class="text-danger">*</span></label>
                                            <textarea class="form-control dynamic-field" id="departmentalOutcomes" rows="3" placeholder="Enter session outcomes..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Startup Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-startup">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="startupName">Startup Name / Project Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control dynamic-field" id="startupName" placeholder="Enter title...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="startupStage">Current Stage <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="startupStage">
                                                <option value="" disabled selected>Select Stage...</option>
                                                <option value="Ideation">Ideation</option>
                                                <option value="Prototype">Prototype</option>
                                                <option value="MVP">MVP</option>
                                                <option value="Registered Startup">Registered Startup</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="startupTeam">Team Members</label>
                                            <input type="text" class="form-control dynamic-field" id="startupTeam" placeholder="Enter team member names...">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="startupProblem">Problem Statement / Idea Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control dynamic-field" id="startupProblem" rows="3" placeholder="Enter problem statement..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Research Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-research">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="researchPaperTitle">Research Paper Title / Topic <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control dynamic-field" id="researchPaperTitle" placeholder="Enter title...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="researchAuthors">Authors / Researchers <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control dynamic-field" id="researchAuthors" placeholder="Enter author names...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="researchJournal">Journal / Conference Name</label>
                                            <input type="text" class="form-control dynamic-field" id="researchJournal" placeholder="Enter journal/conference name...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="researchPubDate">Publication Date / Status</label>
                                            <input type="text" class="form-control dynamic-field" id="researchPubDate" placeholder="e.g., Published May 2026, Submitted...">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic International Relational Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-international_relational">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="intCollaboratingOrg">Collaborating Organization / Country <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control dynamic-field" id="intCollaboratingOrg" placeholder="e.g., University of California, USA...">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="intCollaborationType">Type of Collaboration <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="intCollaborationType">
                                                <option value="" disabled selected>Select Collaboration Type...</option>
                                                <option value="Student Exchange">Student Exchange</option>
                                                <option value="Faculty Exchange">Faculty Exchange</option>
                                                <option value="Joint Research">Joint Research</option>
                                                <option value="Webinar/Seminar">Webinar/Seminar</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="intDescription">Brief Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control dynamic-field" id="intDescription" rows="3" placeholder="Enter collaboration details..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Central Activity Section -->
                                <div class="dynamic-report-section d-none" id="sec-central">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="centralCategory">Event Category <span class="text-danger">*</span></label>
                                            <select class="form-select dynamic-field" id="centralCategory">
                                                <option value="" disabled selected>Select Category...</option>
                                                <option value="Cultural">Cultural</option>
                                                <option value="Sports">Sports</option>
                                                <option value="Social Welfare">Social Welfare</option>
                                                <option value="Tech-Fest">Tech-Fest</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- empty cell for alignment -->
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" for="centralHighlights">Brief Description & Highlights <span class="text-danger">*</span></label>
                                            <textarea class="form-control dynamic-field" id="centralHighlights" rows="3" placeholder="Enter event details and highlights..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-light px-4"
                                        onclick="goToPrevSection(3)"><i class="bi bi-arrow-left me-2"></i> Previous
                                        Section</button>
                                    <button type="button" class="btn btn-danger px-4" onclick="goToNextSection(3)">Next
                                        Section <i class="bi bi-arrow-right ms-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Email Notification -->
                    <div class="accordion-item d-none" id="secItemFive">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <i class="bi bi-envelope-at-fill me-2"></i> Section 4: Email Notification
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                            data-bs-parent="#reportAccordion">
                            <div class="accordion-body">
                                <div class="row g-4">
                                    <!-- A. Reference Faculty Email -->
                                    <div class="col-md-12">
                                        <h5 class="text-danger mb-3 rp-serif-heading"><i
                                                class="bi bi-envelope-paper me-2"></i> Request Review Email</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="refName">Faculty Name</label>
                                                <input type="text" class="form-control" id="refName"
                                                    placeholder="Auto-filled from Section 1..." readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="refEmail">Email Address</label>
                                                <input type="email" class="form-control" id="refEmail"
                                                    placeholder="Auto-filled from Section 1..." readonly>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="ccEmails">CC Emails (Multi-select)</label>
                                                <div class="custom-multiselect-wrap">
                                                    <!-- Under original ID and name so the form/draft handlers work perfectly, but visually hidden -->
                                                    <select class="form-select d-none" id="ccEmails" name="ccEmails[]" multiple>
                                                        <!-- Dynamically populated options -->
                                                    </select>
                                                    
                                                    <!-- Custom UI representing the multi-select -->
                                                    <div class="multiselect-trigger form-control" id="ccEmailsTrigger">
                                                        <span class="placeholder-text" id="ccPlaceholder">Select Faculty Members...</span>
                                                        <div class="selected-badges-container d-none" id="ccSelectedBadges"></div>
                                                        <i class="bi bi-chevron-down multiselect-arrow-icon"></i>
                                                    </div>
                                                    
                                                    <div class="multiselect-dropdown" id="ccDropdown">
                                                        <div class="multiselect-search-wrap">
                                                            <i class="bi bi-search search-icon"></i>
                                                            <input type="text" class="form-control form-control-sm" id="ccSearchInput" placeholder="Search faculty member..." autocomplete="off">
                                                        </div>
                                                        <div class="multiselect-options-list" id="ccOptionsList">
                                                            <!-- JavaScript will render option rows with checkboxes here -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-text text-muted small mt-1">Select one or more faculty members to CC in the email copy.</div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="refSubject">Subject</label>
                                                <input type="text" class="form-control" id="refSubject"
                                                    value="Request Review: Departmental Activity Report" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" for="refMessage">Message Body (Compiles
                                                    details in table format)</label>
                                                <textarea class="form-control" id="refMessage" rows="12" required
                                                    style="font-family: monospace;"></textarea>
                                            </div>
                                            <div class="col-12 text-end d-flex flex-wrap gap-2 justify-content-end">
                                                <button type="button" class="btn btn-gmiu-secondary btn-sm"
                                                    onclick="saveDraft()">
                                                    <i class="bi bi-save2"></i> Save Draft
                                                </button>
                                                <button type="button" class="btn btn-gmiu-secondary btn-sm"
                                                    onclick="openPreviewModal()">
                                                    <i class="bi bi-eye"></i> Preview Report
                                                </button>
                                                <button type="button" class="btn btn-gmiu-secondary btn-sm"
                                                    onclick="triggerPrint()">
                                                    <i class="bi bi-printer"></i> Print / PDF
                                                </button>
                                                <button type="button" class="btn btn-gmiu-secondary btn-sm text-danger"
                                                    onclick="resetReportForm()">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Form
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-light px-4"
                                        onclick="goToPrevSection(4)"><i class="bi bi-arrow-left me-2"></i> Previous
                                        Section</button>
                                    <button type="submit" class="btn btn-gmiu-primary px-4" id="submitBtn"><i
                                            class="bi bi-cloud-arrow-up"></i> Submit & Send</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /Accordion -->
            </form>
        </main>

        <!-- Footer -->
        <footer class="rp-footer text-center container">
            <p>&copy; 2026 Department of Information Technology, GMIU &nbsp;·&nbsp; Designed with <span
                    class="heart-red">♥</span> by Dev Dholakiya</p>
        </footer>

    </div><!-- /rp-page -->

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title rp-serif-heading" id="previewModalLabel"><i
                            class="bi bi-eye-fill text-danger me-2"></i> Report Preview Documentation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light text-dark p-4" id="modalPreviewBody">
                    <!-- Dynamic Preview Content Loaded here by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                    <button type="button" class="btn btn-danger" onclick="triggerPrint()"><i
                            class="bi bi-printer-fill me-1"></i> Print / PDF Export</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulated Email Loader Modal -->
    <div class="modal fade" id="emailStatusModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-sm-custom">
            <div class="modal-content modal-content-dark-custom">
                <div class="modal-body py-4">
                    <div id="emailSendingUI" class="text-center">
                        <div class="spinner-border text-danger mb-3 spinner-lg" role="status"></div>
                        <h5 class="text-light rp-serif-heading">Sending Email Notification...</h5>
                        <p class="text-muted small">Transmitting report parameters via department mail hub</p>
                    </div>
                    <div id="emailSuccessUI" class="success-checkmark-modal d-none">
                        <div class="icon-circle">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h5 class="text-light rp-serif-heading">Email Sent Successfully!</h5>
                        <p class="text-muted small mb-3">The report details were dispatched to the recipient mailbox.
                        </p>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Floating Toast Notification -->
    <div class="gmiu-toast" id="gmiuToast">
        <i class="bi bi-check-circle-fill text-success fs-5"></i>
        <span id="toastMessage">Draft successfully saved.</span>
    </div>

    <!-- ░░ FLOATING NAV BUTTON (Bottom Right) ░░ -->
    <div class="fab-nav" id="fabNav">
        <div class="fab-menu" id="fabMenu">
            <a href="index" class="fab-link" id="nav-home">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Home
            </a>
            <a href="faculty" class="fab-link" id="nav-faculty">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
                Faculty Team
            </a>
            <a href="report" class="fab-link active" id="nav-report">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
                Report Request
            </a>
            <a href="ctlactivity" class="fab-link" id="nav-ctl">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="9" y1="3" x2="9" y2="21" />
                    <line x1="15" y1="3" x2="15" y2="21" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="3" y1="15" x2="21" y2="15" />
                </svg>
                CTL Activity
            </a>
            <a href="ctldrive" class="fab-link" id="nav-drive">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg>
                Drive Scanner
            </a>
            <a href="zero-student-report" class="fab-link" id="nav-zero">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="17" y1="8" x2="23" y2="14" />
                    <line x1="23" y1="8" x2="17" y2="14" />
                </svg>
                Zero Student Report
            </a>
        </div>

        <button class="fab-btn" id="fabBtn" aria-label="Open Navigation">
            <svg class="fab-icon-menu" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <svg class="fab-icon-close" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24" style="display:none;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- ── Print Report Preview Area (Targeted by CSS @media print) ── -->
    <div id="printReportArea">
        <div class="print-header">
            <h4 style="margin: 0; font-family: 'Playfair Display', serif; font-weight: 800; letter-spacing: 0.5px;">
                GYANMANJARI INNOVATIVE UNIVERSITY</h4>
            <div class="print-subtitle">DEPARTMENT OF INFORMATION TECHNOLOGY</div>
            <div class="print-title" id="printHeaderTitle">Activity Report Documentation</div>
        </div>

        <div class="print-section-title">1. Faculty Profile (Requested By)</div>
        <table class="print-meta-table">
            <tr>
                <th>Faculty Name</th>
                <td id="pFacultyName">-</td>
                <th>Employee ID</th>
                <td id="pFacultyEmpId">-</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td id="pFacultyDesignation">-</td>
                <th>Email Address</th>
                <td id="pFacultyEmail">-</td>
            </tr>
            <tr>
                <th>Mobile Number</th>
                <td id="pFacultyPhone">-</td>
                <th>Department</th>
                <td id="pFacultyDept">-</td>
            </tr>
        </table>

        <div class="print-section-title">2. Basic Report Information</div>
        <table class="print-meta-table">
            <tr>
                <th>Academic Year</th>
                <td id="pAcademicYear">-</td>
                <th>Report Type</th>
                <td id="pReportType">-</td>
            </tr>
            <tr>
                <th>Report Title</th>
                <td colspan="3" id="pReportTitle">-</td>
            </tr>
            <tr>
                <th>Activity Date</th>
                <td id="pActivityDate">-</td>
                <th>Duration / Time</th>
                <td id="pActivityTime">-</td>
            </tr>
            <tr>
                <th>Venue</th>
                <td id="pVenue">-</td>
                <th>Programme(s)</th>
                <td id="pProgramme">-</td>
            </tr>
            <tr>
                <th>Semester / Class</th>
                <td id="pSemesterClass">-</td>
                <th>Participants Count</th>
                <td id="pParticipantsCount">-</td>
            </tr>
            <tr>
                <th>Batch</th>
                <td id="pBatch">-</td>
                <th>Student Coordinator</th>
                <td id="pStudentCoordinator">-</td>
            </tr>
            <tr>
                <th>Published on Website</th>
                <td id="pPublishWebsite">-</td>
                <th>Press Note Required</th>
                <td id="pPressNote">-</td>
            </tr>
            <tr>
                <th>Faculty Coordinator(s)</th>
                <td colspan="3" id="pCoordinators">-</td>
            </tr>
            <tr>
                <th>Brief Objective</th>
                <td colspan="3" id="pBriefObjective">-</td>
            </tr>
        </table>

        <div class="print-section-title" id="pDynamicSecHeader">3. Activity-Specific Details</div>
        <table class="print-meta-table" id="pDynamicMetaTable">
            <!-- Dynamically populated rows based on selected report type -->
        </table>

        <div class="print-signatures">
            <div class="sig-block">
                <div class="sig-line" id="sigRequestedBy">Requested By (Faculty Name)</div>
            </div>
            <div class="sig-block">
                <div class="sig-line">Head of Department (IT)</div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Shared Faculty Database -->
    <!-- Google Sheets configuration loaded securely from backend proxy -->
    <script src="assets/js/facultyData.js" defer></script>

    <!-- Department Report System logic -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Wait for facultyData to be available
            if (typeof facultyData === 'undefined') {
                console.error("Shared facultyData.js is missing or failed to load.");
            }

            // Initialize searchable autocomplete drop downs
            initAutocompleteSearch("facultySearch", "facultyId", "facultyDropdownList", fillFacultyDetails);
            // Populate CC emails multi-select dropdown
            populateCcEmails();

            // Populate Faculty Coordinators multi-select dropdown
            populateFacultyCoordinators();
            // Dynamic validation of Photos ZIP and Drive Link
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            const clearPhotoDriveValidation = () => {
                if (photosInput.files.length > 0 || driveInput.value.trim() !== "") {
                    photosInput.classList.remove("is-invalid");
                    driveInput.classList.remove("is-invalid");
                }
            };
            photosInput.addEventListener("change", clearPhotoDriveValidation);
            driveInput.addEventListener("input", clearPhotoDriveValidation);

            // Initialize Character Counters
            initCharacterCounters();

            // Initialize File Upload Displays
            initFileUploadDisplays();

            // Auto load draft if exists
            loadSavedDraft();

            // Track Stepper nodes states when accordions collapse/expand
            setupAccordionStateTracker();

            // Auto sync email preview table on any form input/change
            const rForm = document.getElementById("reportForm");
            rForm.addEventListener("input", syncEmailPreview);
            rForm.addEventListener("change", syncEmailPreview);

            // Submit event listener
            rForm.addEventListener("submit", function (e) {
                e.preventDefault();
                if (validateFullForm()) {
                    simulateFormSubmission();
                }
            });
        });

        // ── Autocomplete Search Feature ──
        function initAutocompleteSearch(inputId, hiddenId, dropdownId, selectCallback) {
            const input = document.getElementById(inputId);
            const hidden = document.getElementById(hiddenId);
            const dropdown = document.getElementById(dropdownId);

            if (!input || !dropdown) return;

            input.addEventListener("focus", function () {
                if (typeof facultyData !== 'undefined') {
                    filterFacultyList(input.value);
                }
            });

            input.addEventListener("input", function () {
                filterFacultyList(input.value);
            });

            document.addEventListener("click", function (e) {
                if (!e.target.closest("#" + inputId) && !e.target.closest("#" + dropdownId)) {
                    dropdown.classList.remove("show");
                }
            });

            function filterFacultyList(query) {
                dropdown.innerHTML = "";
                dropdown.classList.add("show");

                const cleanQuery = query.toLowerCase().replace("prof.", "").replace("mr.", "").trim();
                const filtered = facultyData.filter(member =>
                    member.name.toLowerCase().includes(cleanQuery) ||
                    member.empId.toLowerCase().includes(cleanQuery)
                );

                if (filtered.length === 0) {
                    dropdown.innerHTML = '<div class="no-results-item">No faculty members found</div>';
                    return;
                }

                filtered.forEach(member => {
                    const item = document.createElement("div");
                    item.className = "dropdown-item";
                    item.innerHTML = `
                        <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.name}</div>
                            <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
                        </div>
                    `;
                    item.addEventListener("click", function () {
                        input.value = member.name;
                        hidden.value = member.id;
                        dropdown.classList.remove("show");
                        selectCallback(member);
                    });
                    dropdown.appendChild(item);
                });
            }
        }

        // Section 1 autofill callback
        function fillFacultyDetails(member) {
            document.getElementById("facultyDesignation").value = member.designation;
            document.getElementById("facultyEmpId").value = member.empId;
            document.getElementById("facultyEmail").value = member.email;
            document.getElementById("facultyPhone").value = "+91 " + member.phone;
            document.getElementById("facultyDept").value = member.department || "Information Technology";

            // Sync to Section 7 Block A (Reference Faculty Email details)
            document.getElementById("refName").value = member.name;
            document.getElementById("refEmail").value = member.email;

            // Sync signature line
            document.getElementById("sigRequestedBy").innerText = `Requested By (${member.name})`;

            syncEmailPreview();
            updateStepState(1, true);
            unlockSection(2);

            // Automatically collapse Section 1 and expand Section 2
            setTimeout(() => {
                collapseAndExpand(1, 2);
            }, 600);
        }

        // Populate CC emails multi-select list dynamically and initialize custom multiselect
        function populateCcEmails() {
            const ccSelect = document.getElementById("ccEmails");
            const optionsList = document.getElementById("ccOptionsList");
            const trigger = document.getElementById("ccEmailsTrigger");
            const dropdown = document.getElementById("ccDropdown");
            const searchInput = document.getElementById("ccSearchInput");
            const placeholder = document.getElementById("ccPlaceholder");
            const badgesContainer = document.getElementById("ccSelectedBadges");

            if (!ccSelect || !optionsList || !trigger || !dropdown || !searchInput || !placeholder || !badgesContainer || typeof facultyData === 'undefined') return;

            // 1. Populate native hidden select
            ccSelect.innerHTML = "";
            facultyData.forEach(member => {
                const option = document.createElement("option");
                option.value = member.email;
                option.textContent = `${member.name} (${member.email})`;
                ccSelect.appendChild(option);
            });

            // 2. Populate custom list options in the dropdown
            optionsList.innerHTML = "";
            facultyData.forEach(member => {
                const optionItem = document.createElement("div");
                optionItem.className = "multiselect-option-item";
                optionItem.setAttribute("data-email", member.email);
                optionItem.setAttribute("data-name", member.name.toLowerCase());

                optionItem.innerHTML = `
                    <div class="multiselect-checkbox-wrap">
                        <input type="checkbox" value="${member.email}">
                    </div>
                    <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                    <div class="item-info">
                        <div class="item-name">${member.name}</div>
                        <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
                    </div>
                `;

                // Handle clicking anywhere on the option item row
                optionItem.addEventListener("click", function(e) {
                    const checkbox = optionItem.querySelector('input[type="checkbox"]');
                    if (e.target !== checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                    toggleSelection(member.email, checkbox.checked);
                });

                optionsList.appendChild(optionItem);
            });

            // 3. Dropdown Toggle Click Event
            trigger.addEventListener("click", function(e) {
                e.stopPropagation();
                const isOpen = dropdown.classList.toggle("show");
                trigger.classList.toggle("active", isOpen);
                if (isOpen) {
                    searchInput.value = "";
                    // Reset filter on open
                    const items = optionsList.querySelectorAll(".multiselect-option-item");
                    items.forEach(item => item.classList.remove("d-none"));
                    searchInput.focus();
                }
            });

            // 4. Click outside to close dropdown
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".custom-multiselect-wrap")) {
                    dropdown.classList.remove("show");
                    trigger.classList.remove("active");
                }
            });

            // Prevent dropdown closure when clicking inside it
            dropdown.addEventListener("click", function(e) {
                e.stopPropagation();
            });

            // 5. Search Filter Input Event
            searchInput.addEventListener("input", function() {
                const query = searchInput.value.toLowerCase().trim();
                const items = optionsList.querySelectorAll(".multiselect-option-item");
                
                items.forEach(item => {
                    const name = item.getAttribute("data-name");
                    const email = item.getAttribute("data-email").toLowerCase();
                    if (name.includes(query) || email.includes(query)) {
                        item.classList.remove("d-none");
                    } else {
                        item.classList.add("d-none");
                    }
                });
            });

            // Toggle Selection logic helper
            function toggleSelection(email, isSelected) {
                // Update native select
                Array.from(ccSelect.options).forEach(option => {
                    if (option.value === email) {
                        option.selected = isSelected;
                    }
                });

                // Update custom UI badges
                updateSelectedBadges();

                // Trigger form/select change events to sync ASCII previews etc
                const event = new Event('change', { bubbles: true });
                ccSelect.dispatchEvent(event);
            }

            // Sync badges rendering
            function updateSelectedBadges() {
                badgesContainer.innerHTML = "";
                const selectedOptions = Array.from(ccSelect.selectedOptions);

                if (selectedOptions.length === 0) {
                    placeholder.classList.remove("d-none");
                    badgesContainer.classList.add("d-none");
                } else {
                    placeholder.classList.add("d-none");
                    badgesContainer.classList.remove("d-none");

                    selectedOptions.forEach(opt => {
                        const email = opt.value;
                        // Find matching faculty member to get name
                        const member = facultyData.find(m => m.email === email);
                        const name = member ? member.name : email;

                        const badge = document.createElement("span");
                        badge.className = "multiselect-badge";
                        badge.innerHTML = `
                            <span>${name}</span>
                            <span class="multiselect-badge-remove" data-email="${email}">
                                <i class="bi bi-x-circle-fill"></i>
                            </span>
                        `;

                        // Badge remove click event
                        badge.querySelector(".multiselect-badge-remove").addEventListener("click", function(e) {
                            e.stopPropagation();
                            // Uncheck dropdown checkbox
                            const checkbox = optionsList.querySelector(`input[value="${email}"]`);
                            if (checkbox) checkbox.checked = false;
                            
                            toggleSelection(email, false);
                        });

                        badgesContainer.appendChild(badge);
                    });
                }
            }

            // Make updates globally accessible so loadDraft, reset etc. can trigger synchronization
            window.syncCcEmailsUi = function() {
                // Update checkboxes based on native select state
                const selectedEmails = Array.from(ccSelect.selectedOptions).map(opt => opt.value);
                
                const checkboxes = optionsList.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectedEmails.includes(checkbox.value);
                });

                // Update badges
                updateSelectedBadges();
            };
        }

        // ── Faculty Coordinators Multiselect Dropdown ──
        function populateFacultyCoordinators() {
            const coordInput = document.getElementById("coordinators");
            const optionsList = document.getElementById("coordOptionsList");
            const trigger = document.getElementById("coordEmailsTrigger");
            const dropdown = document.getElementById("coordDropdown");
            const searchInput = document.getElementById("coordSearchInput");
            const placeholder = document.getElementById("coordPlaceholder");
            const badgesContainer = document.getElementById("coordSelectedBadges");

            if (!coordInput || !optionsList || !trigger || !dropdown || !searchInput || !placeholder || !badgesContainer || typeof facultyData === 'undefined') return;

            let selectedCoordinators = [];

            // Helper to toggle selection
            function toggleCoordSelection(name, checked) {
                if (checked) {
                    if (!selectedCoordinators.includes(name)) {
                        selectedCoordinators.push(name);
                    }
                } else {
                    selectedCoordinators = selectedCoordinators.filter(item => item !== name);
                }
                
                // Update hidden input value (comma-separated names)
                coordInput.value = selectedCoordinators.join(', ');
                
                // Update badges
                updateCoordBadges();
                
                // Trigger form sync
                syncEmailPreview();
            }

            function updateCoordBadges() {
                badgesContainer.innerHTML = "";
                if (selectedCoordinators.length === 0) {
                    placeholder.classList.remove("d-none");
                    badgesContainer.classList.add("d-none");
                    trigger.classList.remove("has-badges");
                    coordInput.value = "";
                } else {
                    placeholder.classList.add("d-none");
                    badgesContainer.classList.remove("d-none");
                    trigger.classList.add("has-badges");

                    selectedCoordinators.forEach(name => {
                        const badge = document.createElement("span");
                        badge.className = "multiselect-badge";
                        badge.innerHTML = `
                            <span>${name}</span>
                            <span class="multiselect-badge-remove" data-name="${name}">
                                <i class="bi bi-x-circle-fill"></i>
                            </span>
                        `;
                        badge.querySelector(".multiselect-badge-remove").addEventListener("click", function(e) {
                            e.stopPropagation();
                            toggleCoordSelection(name, false);
                            // Uncheck in dropdown list
                            const checkbox = optionsList.querySelector(`input[value="${name}"]`);
                            if (checkbox) checkbox.checked = false;
                        });
                        badgesContainer.appendChild(badge);
                    });
                }
                // Dispatch event so validation classes clear correctly
                coordInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Populate custom list options in the dropdown
            optionsList.innerHTML = "";
            facultyData.forEach(member => {
                const optionItem = document.createElement("div");
                optionItem.className = "multiselect-option-item";
                optionItem.setAttribute("data-name", member.name.toLowerCase());

                optionItem.innerHTML = `
                    <div class="multiselect-checkbox-wrap">
                        <input type="checkbox" value="${member.name}">
                    </div>
                    <div class="item-avatar ${member.avatarClass}">${member.initials}</div>
                    <div class="item-info">
                        <div class="item-name">${member.name}</div>
                        <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
                    </div>
                `;

                optionItem.addEventListener("click", function(e) {
                    const checkbox = optionItem.querySelector('input[type="checkbox"]');
                    if (e.target !== checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                    toggleCoordSelection(member.name, checkbox.checked);
                });

                optionsList.appendChild(optionItem);
            });

            // Toggle dropdown open/close
            trigger.addEventListener("click", function (e) {
                e.stopPropagation();
                const isOpen = dropdown.classList.toggle("show");
                trigger.classList.toggle("active", isOpen);
                if (isOpen) {
                    searchInput.value = "";
                    const items = optionsList.querySelectorAll(".multiselect-option-item");
                    items.forEach(item => item.classList.remove("d-none"));
                    searchInput.focus();
                }
            });

            // Filter search input
            searchInput.addEventListener("input", function () {
                const q = searchInput.value.toLowerCase().replace("prof.", "").replace("mr.", "").trim();
                const items = optionsList.querySelectorAll(".multiselect-option-item");
                items.forEach(item => {
                    const name = item.getAttribute("data-name");
                    if (name.includes(q)) {
                        item.classList.remove("d-none");
                    } else {
                        item.classList.add("d-none");
                    }
                });
            });

            // Close on click outside
            document.addEventListener("click", function (e) {
                if (!e.target.closest(".custom-multiselect-wrap")) {
                    dropdown.classList.remove("show");
                    trigger.classList.remove("active");
                }
            });

            dropdown.addEventListener("click", function(e) {
                e.stopPropagation();
            });

            // Expose a function to set coordinators programmatically (for loadSavedDraft)
            window.syncCoordinatorsUi = function(value) {
                selectedCoordinators = value ? value.split(', ').map(s => s.trim()).filter(Boolean) : [];
                coordInput.value = value;
                updateCoordBadges();
                
                // Update dropdown checkboxes
                const checkboxes = optionsList.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectedCoordinators.includes(checkbox.value);
                });
            };
        }

        function toggleRlmCustomActivity() {
            const rlmSelect = document.getElementById("rlmActivity");
            const customWrap = document.getElementById("customRlmActivityWrap");
            const customInput = document.getElementById("customRlmActivity");

            if (rlmSelect && rlmSelect.value === "other") {
                if (customWrap) customWrap.classList.remove("d-none");
                if (customInput) customInput.setAttribute("required", "true");
            } else {
                if (customWrap) customWrap.classList.add("d-none");
                if (customInput) {
                    customInput.removeAttribute("required");
                    customInput.value = "";
                }
            }
            syncEmailPreview();
        }

        function togglePlmCustomActivity() {
            const plmSelect = document.getElementById("plmActivity");
            const customWrap = document.getElementById("customPlmActivityWrap");
            const customInput = document.getElementById("customPlmActivity");

            if (plmSelect && plmSelect.value === "other") {
                if (customWrap) customWrap.classList.remove("d-none");
                if (customInput) customInput.setAttribute("required", "true");
            } else {
                if (customWrap) customWrap.classList.add("d-none");
                if (customInput) {
                    customInput.removeAttribute("required");
                    customInput.value = "";
                }
            }
            syncEmailPreview();
        }

        function toggleDepartmentalCustomSubType() {
            const select = document.getElementById("departmentalSubType");
            const customWrap = document.getElementById("customDepartmentalSubTypeWrap");
            const customInput = document.getElementById("customDepartmentalSubType");

            if (select && select.value === "Other") {
                if (customWrap) customWrap.classList.remove("d-none");
                if (customInput) customInput.setAttribute("required", "true");
            } else {
                if (customWrap) customWrap.classList.add("d-none");
                if (customInput) {
                    customInput.removeAttribute("required");
                    customInput.value = "";
                }
            }
            syncEmailPreview();
        }

        function togglePlacementCustomSubType() {
            const select = document.getElementById("placementSubType");
            const customWrap = document.getElementById("customPlacementSubTypeWrap");
            const customInput = document.getElementById("customPlacementSubType");

            if (select && select.value === "Other") {
                if (customWrap) customWrap.classList.remove("d-none");
                if (customInput) customInput.setAttribute("required", "true");
            } else {
                if (customWrap) customWrap.classList.add("d-none");
                if (customInput) {
                    customInput.removeAttribute("required");
                    customInput.value = "";
                }
            }
            syncEmailPreview();
        }

        function togglePlacementActType() {
            const rlmRadio = document.getElementById("typeRLM");
            const plmRadio = document.getElementById("typePLM");
            const rlmWrap = document.getElementById("rlmDropdownWrap");
            const plmWrap = document.getElementById("plmDropdownWrap");
            const rlmSelect = document.getElementById("rlmActivity");
            const plmSelect = document.getElementById("plmActivity");

            // Custom RLM wrappers
            const rlmCustomWrap = document.getElementById("customRlmActivityWrap");
            const rlmCustomInput = document.getElementById("customRlmActivity");

            // Custom PLM wrappers
            const plmCustomWrap = document.getElementById("customPlmActivityWrap");
            const plmCustomInput = document.getElementById("customPlmActivity");

            if (rlmRadio && rlmRadio.checked) {
                rlmWrap.classList.remove("d-none");
                rlmSelect.setAttribute("required", "true");
                
                plmWrap.classList.add("d-none");
                plmSelect.removeAttribute("required");
                plmSelect.value = "";

                if (plmCustomWrap) plmCustomWrap.classList.add("d-none");
                if (plmCustomInput) {
                    plmCustomInput.removeAttribute("required");
                    plmCustomInput.value = "";
                }

                toggleRlmCustomActivity();
            } else if (plmRadio && plmRadio.checked) {
                plmWrap.classList.remove("d-none");
                plmSelect.setAttribute("required", "true");
                
                rlmWrap.classList.add("d-none");
                rlmSelect.removeAttribute("required");
                rlmSelect.value = "";

                if (rlmCustomWrap) rlmCustomWrap.classList.add("d-none");
                if (rlmCustomInput) {
                    rlmCustomInput.removeAttribute("required");
                    rlmCustomInput.value = "";
                }

                togglePlmCustomActivity();
            } else {
                if (rlmWrap) rlmWrap.classList.add("d-none");
                if (plmWrap) plmWrap.classList.add("d-none");
                if (rlmSelect) rlmSelect.removeAttribute("required");
                if (plmSelect) plmSelect.removeAttribute("required");

                if (rlmCustomWrap) rlmCustomWrap.classList.add("d-none");
                if (rlmCustomInput) {
                    rlmCustomInput.removeAttribute("required");
                    rlmCustomInput.value = "";
                }

                if (plmCustomWrap) plmCustomWrap.classList.add("d-none");
                if (plmCustomInput) {
                    plmCustomInput.removeAttribute("required");
                    plmCustomInput.value = "";
                }
            }
            syncEmailPreview();
        }

        // ── Toggle Specific Report Specific Fields (Section 3) ──
        function toggleReportTypeFields() {
            const reportType = document.getElementById("reportType").value;
            const placeholder = document.getElementById("dynamicPlaceholder");
            const customTypeWrap = document.getElementById("customReportTypeWrap");
            const customTypeInput = document.getElementById("customReportType");

            // Handle custom report type input visibility
            if (reportType === "other") {
                customTypeWrap.classList.remove("d-none");
                customTypeInput.setAttribute("required", "true");
            } else {
                customTypeWrap.classList.add("d-none");
                customTypeInput.removeAttribute("required");
                customTypeInput.value = "";
            }

            // Hide placeholder
            placeholder.classList.add("d-none");

            // Hide all sub sections
            const sections = document.querySelectorAll(".dynamic-report-section");
            sections.forEach(sec => sec.classList.add("d-none"));

            // Remove required attribute from all dynamic fields first
            const allDynamicFields = document.querySelectorAll(".dynamic-report-section input, .dynamic-report-section select, .dynamic-report-section textarea");
            allDynamicFields.forEach(field => {
                field.removeAttribute("required");
            });

            // Show relevant sub section and make its appropriate fields required
            const targetSection = document.getElementById("sec-" + reportType);
            if (targetSection) {
                targetSection.classList.remove("d-none");
                
                // Add required to selects, inputs, and textareas inside targetSection
                // (Except for optional fields and dynamically toggled custom inputs)
                const inputsToRequire = targetSection.querySelectorAll("input:not(#departmentalSpeaker):not(#placementSpeaker):not(#startupTeam):not(#researchJournal):not(#researchPubDate):not(#customRlmActivity):not(#customPlmActivity):not(#customDepartmentalSubType):not(#customPlacementSubType), select:not(#rlmActivity):not(#plmActivity):not(#placementSubType), textarea:not(#placementOutcomes)");
                inputsToRequire.forEach(input => {
                    if (input.type === "radio") {
                        const name = input.name;
                        targetSection.querySelectorAll(`input[name="${name}"]`).forEach(r => r.setAttribute("required", "true"));
                    } else {
                        input.setAttribute("required", "true");
                    }
                });
            } else {
                placeholder.classList.remove("d-none");
            }

            // Specific toggle logic for Training & Placement (clear sub dropdowns if changed)
            if (reportType !== "training_placement") {
                const typeRLM = document.getElementById("typeRLM");
                const typePLM = document.getElementById("typePLM");
                if (typeRLM) typeRLM.checked = false;
                if (typePLM) typePLM.checked = false;
                
                const placementSubTypeSelect = document.getElementById("placementSubType");
                if (placementSubTypeSelect) placementSubTypeSelect.value = "";

                const placementSpeaker = document.getElementById("placementSpeaker");
                if (placementSpeaker) placementSpeaker.value = "";

                const placementOutcomes = document.getElementById("placementOutcomes");
                if (placementOutcomes) placementOutcomes.value = "";
            }
            if (reportType !== "departmental") {
                const departmentalSubTypeSelect = document.getElementById("departmentalSubType");
                if (departmentalSubTypeSelect) departmentalSubTypeSelect.value = "";
            }
            
            togglePlacementActType();
            toggleDepartmentalCustomSubType();
            togglePlacementCustomSubType();

            syncEmailPreview();
            updateStepState(2, true);
        }

        // ── Character Counters (Section 4) ──
        function initCharacterCounters() {
            const textareas = document.querySelectorAll(".word-counter");
            textareas.forEach(textarea => {
                const countSpan = document.getElementById(textarea.id + "Count");
                if (countSpan) {
                    textarea.addEventListener("input", function () {
                        countSpan.innerText = this.value.length;
                    });
                }
            });
        }

        // ── Activity Photo file name display list ──
        function initFileUploadDisplays() {
            const input = document.getElementById("activityPhotos");
            const listContainer = document.getElementById("activityPhotosList");
            if (input && listContainer) {
                input.addEventListener("change", function () {
                    listContainer.innerHTML = "";
                    if (this.files.length === 0) return;

                    Array.from(this.files).forEach((file) => {
                        const badge = document.createElement("span");
                        badge.className = "file-badge";
                        badge.innerHTML = `
                            <i class="bi bi-file-earmark-check text-danger"></i>
                            <span>${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
                        `;
                        listContainer.appendChild(badge);
                    });
                });
            }
        }

        // ── Progress Stepper Track Logic ──
        function setupAccordionStateTracker() {
            const collapseElements = document.querySelectorAll('.accordion-collapse');
            collapseElements.forEach(collapse => {
                collapse.addEventListener('show.bs.collapse', function () {
                    const stepNumber = getStepNumberFromCollapseId(this.id);
                    setActiveStepNode(stepNumber);
                });
            });
        }

        function getStepNumberFromCollapseId(id) {
            switch (id) {
                case "collapseOne": return 1;
                case "collapseTwo": return 2;
                case "collapseThree": return 3;
                case "collapseFive": return 4;
                default: return 1;
            }
        }

        function setActiveStepNode(stepNum) {
            const stepNodes = document.querySelectorAll(".step-node");
            stepNodes.forEach(node => {
                const nStep = parseInt(node.getAttribute("data-step"));
                if (nStep === stepNum) {
                    node.classList.add("active");
                } else {
                    node.classList.remove("active");
                }
            });

            // Update progress bar
            const progressBar = document.getElementById("stepperProgressBar");
            const percentage = ((stepNum - 1) / (stepNodes.length - 1)) * 100;
            progressBar.style.width = `calc(${percentage}% - ${(percentage / 100) * 90}px)`;
        }

        function updateStepState(stepNum, isCompleted) {
            const stepNode = document.querySelector(`.step-node[data-step="${stepNum}"]`);
            if (stepNode) {
                if (isCompleted) {
                    stepNode.classList.add("completed");
                } else {
                    stepNode.classList.remove("completed");
                }
            }
        }

        function jumpToSection(stepNum) {
            const stepNode = document.querySelector(`.step-node[data-step="${stepNum}"]`);
            if (stepNode && stepNode.classList.contains("disabled-step")) {
                return; // Locked section, ignore jump click
            }

            let collapseId = "collapseOne";
            switch (stepNum) {
                case 1: collapseId = "collapseOne"; break;
                case 2: collapseId = "collapseTwo"; break;
                case 3: collapseId = "collapseThree"; break;
                case 4: collapseId = "collapseFive"; break;
            }

            const element = document.getElementById(collapseId);
            if (element) {
                const accordion = bootstrap.Collapse.getOrCreateInstance(element);

                // Scroll to header when the show animation starts
                const onShow = () => {
                    element.removeEventListener('show.bs.collapse', onShow);
                    setTimeout(() => {
                        const header = element.previousElementSibling;
                        if (header) {
                            header.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 50);
                };
                element.addEventListener('show.bs.collapse', onShow);

                accordion.show();
            }
        }

        // ── Wizard Sequential Navigation Handlers ──
        function unlockSection(stepNum) {
            const itemIds = ["", "secItemOne", "secItemTwo", "secItemThree", "secItemFive"];
            const id = itemIds[stepNum];
            if (!id) return;

            const el = document.getElementById(id);
            if (el) {
                el.classList.remove("d-none");
            }

            const stepNode = document.querySelector(`.step-node[data-step="${stepNum}"]`);
            if (stepNode) {
                stepNode.classList.remove("disabled-step");
            }
        }

        function collapseAndExpand(fromStep, toStep) {
            const collapseIds = ["", "collapseOne", "collapseTwo", "collapseThree", "collapseFive"];
            const fromEl = document.getElementById(collapseIds[fromStep]);
            const toEl = document.getElementById(collapseIds[toStep]);

            if (fromEl && toEl) {
                const bsFrom = bootstrap.Collapse.getOrCreateInstance(fromEl);
                const bsTo = bootstrap.Collapse.getOrCreateInstance(toEl);

                // Collapse the current section first
                bsFrom.hide();

                // Listen to when the hide transition finishes to expand the next one sequentially
                const onHidden = () => {
                    fromEl.removeEventListener('hidden.bs.collapse', onHidden);

                    bsTo.show();

                    const onShow = () => {
                        toEl.removeEventListener('show.bs.collapse', onShow);
                        // Scroll to the next section header smoothly while it expands
                        setTimeout(() => {
                            const header = toEl.previousElementSibling;
                            if (header) {
                                header.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }, 50);
                    };
                    toEl.addEventListener('show.bs.collapse', onShow);
                };

                fromEl.addEventListener('hidden.bs.collapse', onHidden);
            }
        }

        function goToNextSection(currentStep) {
            if (currentStep === 1) {
                if (!document.getElementById("facultyId").value) {
                    document.getElementById("facultySearch").classList.add("is-invalid");
                    return;
                }
                updateStepState(1, true);
                unlockSection(2);
                collapseAndExpand(1, 2);
            } else if (currentStep === 2) {
                const container = document.getElementById("collapseTwo");

                // Validate programme checkboxes
                const progChecked = container.querySelectorAll(".prog-checkbox:checked");
                const progError = document.getElementById("progError");
                if (progChecked.length === 0) {
                    progError.classList.remove("d-none");
                    progError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                } else {
                    progError.classList.add("d-none");
                }

                // Check ZIP or Drive validation
                const photosInput = document.getElementById("activityPhotos");
                const driveInput = document.getElementById("driveLink");
                if (photosInput.files.length === 0 && !driveInput.value.trim()) {
                    photosInput.classList.add("is-invalid");
                    driveInput.classList.add("is-invalid");
                    photosInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Standard validation on other fields
                const fields = container.querySelectorAll("input, select, textarea");
                let isValid = true;
                fields.forEach(field => {
                    if (field.id === "activityPhotos" || field.id === "driveLink") return; // checked manually
                    if (field.id === "customReportType" && document.getElementById("reportType").value !== "other") return;
                    if (!field.checkValidity()) {
                        field.classList.add("is-invalid");
                        isValid = false;
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });

                if (!isValid) {
                    container.classList.add("was-validated");
                    const firstInvalid = container.querySelector(":invalid");
                    if (firstInvalid) firstInvalid.focus();
                    return;
                }

                updateStepState(2, true);
                unlockSection(3);
                collapseAndExpand(2, 3);
            } else if (currentStep === 3) {
                const container = document.getElementById("collapseThree");
                const fields = container.querySelectorAll("input, select, textarea");
                let isValid = true;
                fields.forEach(field => {
                    // Check if field is inside a hidden container (i.e. d-none)
                    let isHidden = false;
                    let p = field;
                    while (p && p !== container) {
                        if (p.classList.contains("d-none")) {
                            isHidden = true;
                            break;
                        }
                        p = p.parentElement;
                    }
                    if (isHidden) return; // skip hidden fields

                    if (!field.checkValidity()) {
                        field.classList.add("is-invalid");
                        isValid = false;
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });

                if (!isValid) {
                    container.classList.add("was-validated");
                    // Scroll to the first invalid field
                    const firstInvalid = container.querySelector(".is-invalid, :invalid");
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                    return;
                }

                updateStepState(3, true);
                unlockSection(4);
                collapseAndExpand(3, 4);
            }
        }

        function goToPrevSection(currentStep) {
            if (currentStep > 1) {
                collapseAndExpand(currentStep, currentStep - 1);
            }
        }

        // ── ASCII Plain-Text Table Generator for Email/Textarea Preview ──
        function generateReportTextTable() {
            const facultyName = (document.getElementById("facultySearch").value || "-").toUpperCase();
            const designation = (document.getElementById("facultyDesignation").value || "-").toUpperCase();
            const empId = (document.getElementById("facultyEmpId").value || "-").toUpperCase();
            const email = (document.getElementById("facultyEmail").value || "-").toUpperCase();
            const phone = (document.getElementById("facultyPhone").value || "-").toUpperCase();
            const dept = (document.getElementById("facultyDept").value || "Information Technology").toUpperCase();

            const year = (document.getElementById("academicYear").value || "-").toUpperCase();
            const reportSelect = document.getElementById("reportType");
            let reportType = (reportSelect.options[reportSelect.selectedIndex]?.text || "-").toUpperCase();
            if (reportSelect.value === "other") {
                const customType = document.getElementById("customReportType").value.trim();
                if (customType) {
                    reportType = `OTHER (${customType.toUpperCase()})`;
                }
            }
            const title = (document.getElementById("reportTitle").value || "-").toUpperCase();
            const date = (document.getElementById("activityDate").value || "-").toUpperCase();
            const start = (document.getElementById("startTime").value || "-").toUpperCase();
            const end = (document.getElementById("endTime").value || "-").toUpperCase();
            const venue = (document.getElementById("venue").value || "-").toUpperCase();

            const progs = [];
            document.querySelectorAll(".prog-checkbox:checked").forEach(cb => progs.push(cb.value.toUpperCase()));
            const programmes = progs.length > 0 ? progs.join(", ") : "-";

            const semester = (document.getElementById("semester").value || "-").toUpperCase();
            const division = (document.getElementById("divisionClass").value || "-").toUpperCase();
            const participants = (document.getElementById("participantsCount").value || "-").toUpperCase();
            const coordinators = (document.getElementById("coordinators").value || "-").toUpperCase();
            const objective = (document.getElementById("briefObjective").value || "-").toUpperCase();

            const zipInput = document.getElementById("activityPhotos");
            const zipName = (zipInput.files.length > 0 ? zipInput.files[0].name : "NOT UPLOADED").toUpperCase();
            const driveLink = (document.getElementById("driveLink").value || "NOT PROVIDED").toUpperCase();

            // New core fields
            const batch = (document.getElementById("batch").value || "-").toUpperCase();
            const studentCoordinator = (document.getElementById("studentCoordinator").value || "-").toUpperCase();
            const publishWebsite = (document.querySelector('input[name="publishWebsite"]:checked')?.value || "-").toUpperCase();
            const pressNote = (document.querySelector('input[name="pressNote"]:checked')?.value || "-").toUpperCase();

            const specificFields = [];
            const activeSec = document.querySelector(`.dynamic-report-section:not(.d-none)`);
            if (activeSec) {
                const inputs = activeSec.querySelectorAll("input, select, textarea");
                inputs.forEach(input => {
                    let isHidden = false;
                    let p = input;
                    while (p && p !== activeSec) {
                        if (p.classList.contains("d-none")) {
                            isHidden = true;
                            break;
                        }
                        p = p.parentElement;
                    }
                    if (isHidden) return;

                    if (input.type === "radio") {
                        if (input.checked) {
                            const parentLabel = input.closest(".col-md-12")?.querySelector(".form-label")?.innerText || "Activity Type";
                            const label = parentLabel.replace("*", "").trim().toUpperCase();
                            const val = input.value.toUpperCase();
                            specificFields.push({ label, val });
                        }
                    } else {
                        const label = (input.previousElementSibling ? input.previousElementSibling.innerText : "Field").replace("*", "").trim().toUpperCase();
                        const val = (input.value || "-").toUpperCase();
                        specificFields.push({ label, val });
                    }
                });
            }

            let table = "";
            const width = 75;
            const hr = "+" + "-".repeat(width - 2) + "+\n";

            const centerAlign = (text) => {
                const spaces = Math.max(0, width - 2 - text.length);
                const left = Math.floor(spaces / 2);
                const right = spaces - left;
                return "|" + " ".repeat(left) + text + " ".repeat(right) + "|\n";
            };

            const formatRow = (label, val) => {
                const labelWidth = 22;
                const valWidth = width - labelWidth - 5;

                const wrapText = (text, maxW) => {
                    const words = text.split(" ");
                    const lines = [];
                    let currentLine = "";
                    words.forEach(word => {
                        if ((currentLine + " " + word).trim().length <= maxW) {
                            currentLine = (currentLine + " " + word).trim();
                        } else {
                            if (currentLine) lines.push(currentLine);
                            currentLine = word;
                        }
                    });
                    if (currentLine) lines.push(currentLine);
                    return lines;
                };

                const valLines = wrapText(String(val), valWidth);
                if (valLines.length === 0) valLines.push("");

                let rowStr = "";
                for (let i = 0; i < valLines.length; i++) {
                    const lbl = (i === 0) ? label.padEnd(labelWidth) : " ".repeat(labelWidth);
                    const vl = valLines[i].padEnd(valWidth);
                    rowStr += `| ${lbl} | ${vl} |\n`;
                }
                return rowStr;
            };

            table += hr;
            table += centerAlign("GYANMANJARI INNOVATIVE UNIVERSITY");
            table += centerAlign("DEPARTMENT OF INFORMATION TECHNOLOGY");
            table += centerAlign(`${reportType.toUpperCase()} ACTIVITY REPORT`);
            table += hr;
            table += centerAlign("1. FACULTY REQUEST PROFILE");
            table += hr;
            table += formatRow("Faculty Name", facultyName);
            table += formatRow("Designation", designation);
            table += formatRow("Employee ID", empId);
            table += formatRow("Email Address", email);
            table += formatRow("Mobile Number", phone);
            table += formatRow("Department", dept);
            table += hr;
            table += centerAlign("2. BASIC ACTIVITY DETAILS");
            table += hr;
            table += formatRow("Academic Year", year);
            table += formatRow("Report Title", title);
            table += formatRow("Activity Date", date);
            table += formatRow("Duration", `${start} to ${end}`);
            table += formatRow("Venue", venue);
            table += formatRow("Programme(s)", programmes);
            table += formatRow("Sem / Division", `Sem ${semester} (${division})`);
            table += formatRow("Participants Count", participants);
            table += formatRow("Batch", batch);
            table += formatRow("Student Coordinator", studentCoordinator);
            table += formatRow("Publish on Website", publishWebsite);
            table += formatRow("Press Note Required", pressNote);
            table += formatRow("Coordinator(s)", coordinators);
            table += formatRow("Brief Objective", objective);
            table += formatRow("ZIP Photo File", zipName);
            table += formatRow("Google Drive Link", driveLink);

            if (specificFields.length > 0) {
                table += hr;
                table += centerAlign("3. DYNAMIC CATEGORY DETAILS");
                table += hr;
                specificFields.forEach(f => {
                    table += formatRow(f.label, f.val);
                });
            }
            table += hr;
            return table;
        }

        // ── Responsive HTML Table Generator for Emails ──
        function generateReportHtml(isFacultyCopy = false) {
            const facultyName = (document.getElementById("facultySearch").value || "-").toUpperCase();
            const designation = (document.getElementById("facultyDesignation").value || "-").toUpperCase();
            const empId = (document.getElementById("facultyEmpId").value || "-").toUpperCase();
            const email = (document.getElementById("facultyEmail").value || "-").toUpperCase();
            const phone = (document.getElementById("facultyPhone").value || "-").toUpperCase();
            const dept = (document.getElementById("facultyDept").value || "Information Technology").toUpperCase();

            const year = (document.getElementById("academicYear").value || "-").toUpperCase();
            const reportSelect = document.getElementById("reportType");
            let reportType = (reportSelect.options[reportSelect.selectedIndex]?.text || "-").toUpperCase();
            if (reportSelect.value === "other") {
                const customType = document.getElementById("customReportType").value.trim();
                if (customType) {
                    reportType = `OTHER (${customType.toUpperCase()})`;
                }
            }
            const title = (document.getElementById("reportTitle").value || "-").toUpperCase();
            const date = (document.getElementById("activityDate").value || "-").toUpperCase();
            const start = (document.getElementById("startTime").value || "-").toUpperCase();
            const end = (document.getElementById("endTime").value || "-").toUpperCase();
            const venue = (document.getElementById("venue").value || "-").toUpperCase();

            const progs = [];
            document.querySelectorAll(".prog-checkbox:checked").forEach(cb => progs.push(cb.value.toUpperCase()));
            const programmes = progs.length > 0 ? progs.join(", ") : "-";

            const semester = (document.getElementById("semester").value || "-").toUpperCase();
            const division = (document.getElementById("divisionClass").value || "-").toUpperCase();
            const participants = (document.getElementById("participantsCount").value || "-").toUpperCase();
            const coordinators = (document.getElementById("coordinators").value || "-").toUpperCase();
            const objective = (document.getElementById("briefObjective").value || "-").toUpperCase();

            const zipInput = document.getElementById("activityPhotos");
            const zipName = (zipInput.files.length > 0 ? zipInput.files[0].name : "NOT UPLOADED").toUpperCase();
            const rawDriveLink = document.getElementById("driveLink").value || "Not Provided";
            const driveLink = rawDriveLink.toUpperCase();

            // New core fields
            const batch = (document.getElementById("batch").value || "-").toUpperCase();
            const studentCoordinator = (document.getElementById("studentCoordinator").value || "-").toUpperCase();
            const publishWebsite = (document.querySelector('input[name="publishWebsite"]:checked')?.value || "-").toUpperCase();
            const pressNote = (document.querySelector('input[name="pressNote"]:checked')?.value || "-").toUpperCase();

            let specificHtml = "";
            const activeSec = document.querySelector(`.dynamic-report-section:not(.d-none)`);
            if (activeSec) {
                const inputs = activeSec.querySelectorAll("input, select, textarea");
                inputs.forEach(input => {
                    let isHidden = false;
                    let p = input;
                    while (p && p !== activeSec) {
                        if (p.classList.contains("d-none")) {
                            isHidden = true;
                            break;
                        }
                        p = p.parentElement;
                    }
                    if (isHidden) return;

                    if (input.type === "radio") {
                        if (input.checked) {
                            const parentLabel = input.closest(".col-md-12")?.querySelector(".form-label")?.innerText || "Activity Type";
                            const label = parentLabel.replace("*", "").trim().toUpperCase();
                            const val = input.value.toUpperCase();
                            specificHtml += `
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Playfair Display', serif;">${label}</td>
                                    <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${val}</td>
                                </tr>
                            `;
                        }
                    } else {
                        const label = (input.previousElementSibling ? input.previousElementSibling.innerText : "Field").replace("*", "").trim().toUpperCase();
                        const val = (input.value || "-").toUpperCase();
                        specificHtml += `
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Playfair Display', serif;">${label}</td>
                                <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${val}</td>
                            </tr>
                        `;
                    }
                });
            }

            return `
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap" rel="stylesheet">
            <div style="font-family: 'Playfair Display', serif; line-height: 1.6; color: #333; max-width: 700px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div style="background-color: #8c1d1d; color: white; padding: 25px; text-align: center; font-family: 'Playfair Display', serif;">
                    <h2 style="margin: 0; font-size: 22px; font-weight: bold; letter-spacing: 0.5px; font-family: 'Playfair Display', serif;">GYANMANJARI INNOVATIVE UNIVERSITY</h2>
                    <div style="font-size: 14px; opacity: 0.9; margin-top: 5px; text-transform: uppercase; font-family: 'Playfair Display', serif;">Department of Information Technology</div>
                    <div style="margin-top: 15px; display: inline-block; background-color: rgba(255, 255, 255, 0.2); padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: bold; font-family: 'Playfair Display', serif;">
                        ${reportType} ACTIVITY DOCUMENTATION
                    </div>
                </div>
                
                <div style="padding: 25px; background-color: #ffffff; font-family: 'Playfair Display', serif;">
                    <p style="margin-top: 0; font-size: 15px; font-family: 'Playfair Display', serif;">
                        ${isFacultyCopy ? `<strong>DEAR ${facultyName}</strong>,` : `<strong>DEAR IT ADMINISTRATOR</strong>,`}
                    </p>
                    <p style="font-size: 14px; color: #555; font-family: 'Playfair Display', serif;">
    KINDLY <strong>REVIEW</strong> THE <strong>DETAILS</strong> PROVIDED BELOW.
</p>
                    ${isFacultyCopy ? `
                    <p style="font-size: 14px; color: #555; font-family: 'Playfair Display', serif; margin-top: 10px;">
                        THIS INFORMATION IS FOR YOUR <strong>REVIEW</strong>. PLEASE CHECK IF THERE IS ANY <strong>MISSING OR INCORRECT DETAIL</strong>. IF ANY <strong>CHANGES OR UPDATES</strong> ARE REQUIRED, PLEASE <strong>REPORT AND SUBMIT</strong> THEM TO <strong>Mr. DEV DHOLAKIYA</strong>.
                    </p>
                    ` : ''}
                    
                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', serif;">1. FACULTY REQUEST PROFILE</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Playfair Display', serif;">
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Playfair Display', serif;">FACULTY NAME</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${facultyName}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">DESIGNATION</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${designation}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">EMPLOYEE ID</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${empId}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">EMAIL ADDRESS</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;"><a href="mailto:${email.toLowerCase()}" style="color: #8c1d1d; text-decoration: underline; font-family: 'Playfair Display', serif;">${email}</a></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">MOBILE NUMBER</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${phone}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">DEPARTMENT</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${dept}</td>
                        </tr>
                    </table>

                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', serif;">2. BASIC ACTIVITY DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Playfair Display', serif;">
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Playfair Display', serif;">REPORT TITLE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #111; font-family: 'Playfair Display', serif;">${title}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">ACADEMIC YEAR</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${year}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">REPORT TYPE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${reportType}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">ACTIVITY DATE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${date}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">DURATION / TIME</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${start} TO ${end}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">VENUE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${venue}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">PROGRAMME(S)</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${programmes}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">SEMESTER & DIVISION</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">SEM ${semester} (${division})</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">PARTICIPANTS COUNT</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${participants}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">BATCH</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${batch}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">STUDENT COORDINATOR</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${studentCoordinator}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">PUBLISHED ON WEBSITE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${publishWebsite}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">PRESS NOTE REQUIRED</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${pressNote}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">FACULTY COORDINATOR(S)</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${coordinators}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">BRIEF OBJECTIVE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">${objective}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">PHOTOS ZIP FILE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: monospace;">${zipName}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Playfair Display', serif;">GOOGLE DRIVE LINK</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Playfair Display', serif;">
                                ${rawDriveLink.toLowerCase() !== "not provided" ? `<a href="${rawDriveLink}" target="_blank" style="color: #8c1d1d; text-decoration: underline; font-family: 'Playfair Display', serif;">OPEN GOOGLE DRIVE FOLDER</a>` : "NOT PROVIDED"}
                            </td>
                        </tr>
                    </table>

                    ${specificHtml ? `
                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', serif;">3. ACTIVITY-SPECIFIC DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Playfair Display', serif;">
                        ${specificHtml}
                    </table>
                    ` : ''}
                </div>
                <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Playfair Display', serif;">
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Playfair Display', serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="${window.location.href}" style="color: #c0392b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;">IT DEPARTMENT</a>.</p>
                    <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Playfair Display', serif;">&copy; 2026 <a href="${window.location.href}" style="color: #64748b; text-decoration: none; font-weight: 600; font-family: 'Playfair Display', serif;"></a>ALL RIGHTS RESERVED.</p>
                </div>
            </div>
            `;
        }

        // ── Sync Email Previews live ──
        function syncEmailPreview() {
            const reportTitle = document.getElementById("reportTitle").value || "[Report Title]";
            const facultyName = document.getElementById("facultySearch").value || "[Faculty Name]";
            const reportSelect = document.getElementById("reportType");
            let reportTypeLabel = reportSelect.options[reportSelect.selectedIndex]?.text || "[Report Type]";
            if (reportSelect.value === "other") {
                const customType = document.getElementById("customReportType").value.trim();
                if (customType) {
                    reportTypeLabel = `Other (${customType})`;
                }
            }

            // Sync Block A (Reference Review Email)
            document.getElementById("refSubject").value = `Review Request: ${reportTypeLabel} — ${reportTitle}`;

            const asciiTable = generateReportTextTable();
            document.getElementById("refMessage").value = `Dear ${facultyName},

Please review the draft details for the Departmental Report titled "${reportTitle}" prepared under your requested activity.

${asciiTable}

Regards,
Department of Information Technology, GMIU`;
        }

        // ── Validation logic ──
        function validateFullForm() {
            const form = document.getElementById("reportForm");
            let isValid = true;

            // Remove invalid class on searches
            document.getElementById("progError").classList.add("d-none");
            document.getElementById("facultySearch").classList.remove("is-invalid");

            // Check Section 1 Autofill Validation
            if (!document.getElementById("facultyId").value) {
                document.getElementById("facultySearch").classList.add("is-invalid");
                isValid = false;
                jumpToSection(1);
                return false;
            } else {
                updateStepState(1, true);
            }

            // Check Programme Checkboxes
            const progChecked = document.querySelectorAll(".prog-checkbox:checked");
            if (progChecked.length === 0) {
                document.getElementById("progError").classList.remove("d-none");
                isValid = false;
                jumpToSection(2);
                return false;
            }
            // Check ZIP or Drive validation
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            if (photosInput.files.length === 0 && !driveInput.value.trim()) {
                photosInput.classList.add("is-invalid");
                driveInput.classList.add("is-invalid");
                isValid = false;
                jumpToSection(2);
                return false;
            }

            // Check coordinators multiselect validation
            const coordTrigger = document.getElementById("coordEmailsTrigger");
            if (coordTrigger) {
                coordTrigger.classList.remove("is-invalid");
                if (!document.getElementById("coordinators").value.trim()) {
                    coordTrigger.classList.add("is-invalid");
                    isValid = false;
                    jumpToSection(2);
                    return false;
                }
            }

            // Trigger HTML5 validations
            if (!form.checkValidity()) {
                form.classList.add("was-validated");
                isValid = false;

                // Find the first invalid element and jump to its section
                const invalidField = form.querySelector(":invalid");
                if (invalidField) {
                    const collapseParent = invalidField.closest(".accordion-collapse");
                    if (collapseParent) {
                        const stepNum = getStepNumberFromCollapseId(collapseParent.id);
                        jumpToSection(stepNum);
                    }
                }
            } else {
                form.classList.add("was-validated");
                    }

                    return isValid;
                }

                // ── Local Storage Draft Saving ──
                function saveDraft() {
                    const ccSelect = document.getElementById("ccEmails");
                    const selectedCc = ccSelect ? Array.from(ccSelect.selectedOptions).map(opt => opt.value) : [];

                    const draft = {
                        facultySearch: document.getElementById("facultySearch").value,
                        facultyId: document.getElementById("facultyId").value,
                        facultyDesignation: document.getElementById("facultyDesignation").value,
                        facultyEmpId: document.getElementById("facultyEmpId").value,
                        facultyEmail: document.getElementById("facultyEmail").value,
                        facultyPhone: document.getElementById("facultyPhone").value,

                        academicYear: document.getElementById("academicYear").value,
                        reportType: document.getElementById("reportType").value,
                        customReportType: document.getElementById("customReportType").value,
                        reportTitle: document.getElementById("reportTitle").value,
                        activityDate: document.getElementById("activityDate").value,
                        startTime: document.getElementById("startTime").value,
                        endTime: document.getElementById("endTime").value,
                        venue: document.getElementById("venue").value,
                        semester: document.getElementById("semester").value,
                        divisionClass: document.getElementById("divisionClass").value,
                        participantsCount: document.getElementById("participantsCount").value,
                        coordinators: document.getElementById("coordinators").value,
                        briefObjective: document.getElementById("briefObjective").value,
                        driveLink: document.getElementById("driveLink").value,

                        // New fields
                        batch: document.getElementById("batch").value,
                        studentCoordinator: document.getElementById("studentCoordinator").value,
                        publishWebsite: document.querySelector('input[name="publishWebsite"]:checked')?.value || "",
                        pressNote: document.querySelector('input[name="pressNote"]:checked')?.value || "",
                        placementActType: document.querySelector('input[name="placementActType"]:checked')?.value || "",
                        rlmActivity: document.getElementById("rlmActivity").value,
                        customRlmActivity: document.getElementById("customRlmActivity").value,
                        plmActivity: document.getElementById("plmActivity").value,
                        customPlmActivity: document.getElementById("customPlmActivity").value,

                        // Other dynamic activity fields
                        departmentalSubType: document.getElementById("departmentalSubType").value,
                        customDepartmentalSubType: document.getElementById("customDepartmentalSubType").value,
                        departmentalSpeaker: document.getElementById("departmentalSpeaker").value,
                        departmentalOutcomes: document.getElementById("departmentalOutcomes").value,
                        placementSubType: document.getElementById("placementSubType").value,
                        customPlacementSubType: document.getElementById("customPlacementSubType").value,
                        placementSpeaker: document.getElementById("placementSpeaker").value,
                        placementOutcomes: document.getElementById("placementOutcomes").value,
                        startupName: document.getElementById("startupName").value,
                        startupStage: document.getElementById("startupStage").value,
                        startupTeam: document.getElementById("startupTeam").value,
                        startupProblem: document.getElementById("startupProblem").value,
                        researchPaperTitle: document.getElementById("researchPaperTitle").value,
                        researchAuthors: document.getElementById("researchAuthors").value,
                        researchJournal: document.getElementById("researchJournal").value,
                        researchPubDate: document.getElementById("researchPubDate").value,
                        intCollaboratingOrg: document.getElementById("intCollaboratingOrg").value,
                        intCollaborationType: document.getElementById("intCollaborationType").value,
                        intDescription: document.getElementById("intDescription").value,
                        centralCategory: document.getElementById("centralCategory").value,
                        centralHighlights: document.getElementById("centralHighlights").value,

                        // Email fields
                        refSubject: document.getElementById("refSubject").value,
                        refMessage: document.getElementById("refMessage").value,
                        ccEmails: selectedCc
                    };

                    localStorage.setItem("gmiu_it_report_draft", JSON.stringify(draft));
                    showToast("Draft successfully saved locally!");
                }

                // ── Local Storage Draft Loading ──
                function loadSavedDraft() {
                    const rawDraft = localStorage.getItem("gmiu_it_report_draft");
                    if (!rawDraft) return;

                    try {
                        const draft = JSON.parse(rawDraft);

                        // Populate Section 1
                        document.getElementById("facultySearch").value = draft.facultySearch || "";
                        document.getElementById("facultyId").value = draft.facultyId || "";
                        document.getElementById("facultyDesignation").value = draft.facultyDesignation || "";
                        document.getElementById("facultyEmpId").value = draft.facultyEmpId || "";
                        document.getElementById("facultyEmail").value = draft.facultyEmail || "";
                        document.getElementById("facultyPhone").value = draft.facultyPhone || "";
                        if (draft.facultyId) {
                            updateStepState(1, true);
                            unlockSection(2);
                        }

                        // Populate Section 2
                        document.getElementById("academicYear").value = draft.academicYear || "2026-27";
                        let draftReportType = draft.reportType || "";
                        if (draftReportType === "training") {
                            draftReportType = "training_placement";
                        }
                        document.getElementById("reportType").value = draftReportType;
                        document.getElementById("customReportType").value = draft.customReportType || "";
                        document.getElementById("reportTitle").value = draft.reportTitle || "";
                        document.getElementById("activityDate").value = draft.activityDate || "";
                        document.getElementById("startTime").value = draft.startTime || "";
                        document.getElementById("endTime").value = draft.endTime || "";
                        document.getElementById("venue").value = draft.venue || "";
                        document.getElementById("semester").value = draft.semester || "";
                        document.getElementById("divisionClass").value = draft.divisionClass || "";
                        document.getElementById("participantsCount").value = draft.participantsCount || "";
                        document.getElementById("briefObjective").value = draft.briefObjective || "";
                        document.getElementById("driveLink").value = draft.driveLink || "";

                        // Populate new basic fields
                        document.getElementById("batch").value = draft.batch || "";
                        document.getElementById("studentCoordinator").value = draft.studentCoordinator || "";
                        
                        if (draft.publishWebsite) {
                            const rb = document.querySelector(`input[name="publishWebsite"][value="${draft.publishWebsite}"]`);
                            if (rb) rb.checked = true;
                        }
                        if (draft.pressNote) {
                            const rb = document.querySelector(`input[name="pressNote"][value="${draft.pressNote}"]`);
                            if (rb) rb.checked = true;
                        }
                        if (draft.coordinators && typeof window.syncCoordinatorsUi === "function") {
                            window.syncCoordinatorsUi(draft.coordinators);
                        }
                        if (draftReportType) {
                            toggleReportTypeFields();
                            updateStepState(2, true);
                            unlockSection(3);
                            unlockSection(4);
                            unlockSection(5);
                        }

                        // Restore Placement Activity Category (RLM vs PLM) and sub-dropdowns
                        if (draft.placementActType) {
                            const rb = document.querySelector(`input[name="placementActType"][value="${draft.placementActType}"]`);
                            if (rb) {
                                rb.checked = true;
                                togglePlacementActType();
                            }
                        }
                        if (draft.rlmActivity) {
                            document.getElementById("rlmActivity").value = draft.rlmActivity;
                            toggleRlmCustomActivity();
                        }
                        if (draft.customRlmActivity) {
                            document.getElementById("customRlmActivity").value = draft.customRlmActivity;
                        }
                        if (draft.plmActivity) {
                            document.getElementById("plmActivity").value = draft.plmActivity;
                            togglePlmCustomActivity();
                        }
                        if (draft.customPlmActivity) {
                            document.getElementById("customPlmActivity").value = draft.customPlmActivity;
                        }

                        // Restore other dynamic fields
                        const dynFields = [
                            "departmentalSubType", "customDepartmentalSubType", "departmentalSpeaker", "departmentalOutcomes",
                            "placementSubType", "customPlacementSubType", "placementSpeaker", "placementOutcomes",
                            "startupName", "startupStage", "startupTeam", "startupProblem",
                            "researchPaperTitle", "researchAuthors", "researchJournal", "researchPubDate",
                            "intCollaboratingOrg", "intCollaborationType", "intDescription",
                            "centralCategory", "centralHighlights"
                        ];
                        dynFields.forEach(id => {
                            const el = document.getElementById(id);
                            if (el && draft[id] !== undefined) el.value = draft[id];
                        });

                        toggleDepartmentalCustomSubType();
                        togglePlacementCustomSubType();

                        // Populate Emails
                        document.getElementById("refName").value = draft.facultySearch || "";
                        document.getElementById("refEmail").value = draft.facultyEmail || "";
                        document.getElementById("refSubject").value = draft.refSubject || "";
                        document.getElementById("refMessage").value = draft.refMessage || "";

                        if (draft.ccEmails) {
                            const ccSelect = document.getElementById("ccEmails");
                            if (ccSelect) {
                                Array.from(ccSelect.options).forEach(option => {
                                    option.selected = draft.ccEmails.includes(option.value);
                                });
                                if (typeof window.syncCcEmailsUi === "function") {
                                    window.syncCcEmailsUi();
                                }
                            }
                        }
                        showToast("Previous saved report draft restored.");
                    } catch (err) {
                        console.error("Failed to parse report draft", err);
                    }
                }

        // ── Actual Email Transmission using send-email.php backend ──
        function sendReferenceEmail() {
            const to = document.getElementById("refEmail").value;

            if (!to) {
                showToast("ERROR: Recipient email is missing.");
                return;
            }

            // Show loading UI
            const modalEl = document.getElementById("emailStatusModal");
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const sendingUI = document.getElementById("emailSendingUI");
            const successUI = document.getElementById("emailSuccessUI");

            sendingUI.classList.remove("d-none");
            successUI.classList.add("d-none");
            modal.show();

            const htmlMessage = generateReportHtml(true);

            const ccSelect = document.getElementById("ccEmails");
            const selectedCcEmails = ccSelect ? Array.from(ccSelect.selectedOptions).map(option => option.value) : [];

            fetch("send-email", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    to: to,
                    cc: selectedCcEmails,
                    subject: subject,
                    html: htmlMessage
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        sendingUI.classList.add("d-none");
                        successUI.classList.remove("d-none");
                        showToast("Email dispatched successfully to faculty review.");
                    } else {
                        modal.hide();
                        showToast("ERROR sending email: " + (data.error || "Unknown error"));
                    }
                })
                .catch(err => {
                    modal.hide();
                    console.error(err);
                    showToast("ERROR connecting to email backend.");
                });
        }

        // ── Dynamic Preview Render Logic (Section 5 Modal) ──
        function openPreviewModal() {
            if (!validateFullForm()) return;

            const modalBody = document.getElementById("modalPreviewBody");

            const reportTitle = document.getElementById("reportTitle").value;
            const academicYear = document.getElementById("academicYear").value;
            const reportSelect = document.getElementById("reportType");
            let reportTypeLabel = reportSelect.options[reportSelect.selectedIndex].text;
            if (reportSelect.value === "other") {
                const customType = document.getElementById("customReportType").value.trim();
                if (customType) {
                    reportTypeLabel = `Other (${customType})`;
                }
            }
            const activityDate = document.getElementById("activityDate").value;
            const startTime = document.getElementById("startTime").value;
            const endTime = document.getElementById("endTime").value;
            const venue = document.getElementById("venue").value;
            const semester = document.getElementById("semester").value;
            const divisionClass = document.getElementById("divisionClass").value;
            const participantsCount = document.getElementById("participantsCount").value;
            const coordinators = document.getElementById("coordinators").value;
            const briefObjective = document.getElementById("briefObjective").value;

            // New core fields
            const batch = document.getElementById("batch").value || "-";
            const studentCoordinator = document.getElementById("studentCoordinator").value || "-";
            const publishWebsite = document.querySelector('input[name="publishWebsite"]:checked')?.value || "-";
            const pressNote = document.querySelector('input[name="pressNote"]:checked')?.value || "-";

            // Checked programmes
            const progs = [];
            document.querySelectorAll(".prog-checkbox:checked").forEach(cb => progs.push(cb.value));

            // Get Specific Fields dynamically
            let specificHtml = "";
            const activeSec = document.querySelector(`.dynamic-report-section:not(.d-none)`);
            if (activeSec) {
                const inputs = activeSec.querySelectorAll("input, select, textarea");
                inputs.forEach(input => {
                    let isHidden = false;
                    let p = input;
                    while (p && p !== activeSec) {
                        if (p.classList.contains("d-none")) {
                            isHidden = true;
                            break;
                        }
                        p = p.parentElement;
                    }
                    if (isHidden) return;

                    if (input.type === "radio") {
                        if (input.checked) {
                            const parentLabel = input.closest(".col-md-12")?.querySelector(".form-label")?.innerText || "Activity Type";
                            const label = parentLabel.replace("*", "").trim();
                            const val = input.value;
                            specificHtml += `
                                <tr>
                                    <th>${label}</th>
                                    <td colspan="3">${val}</td>
                                </tr>
                            `;
                        }
                    } else {
                        const label = (input.previousElementSibling ? input.previousElementSibling.innerText : "Field").replace("*", "").trim();
                        const val = input.value || "-";
                        specificHtml += `
                            <tr>
                                <th>${label}</th>
                                <td colspan="3">${val}</td>
                            </tr>
                        `;
                    }
                });
            }

            // Sync details to Printable Area
            document.getElementById("printHeaderTitle").innerText = `${reportTypeLabel} Activity Documentation`;
            document.getElementById("pFacultyName").innerText = document.getElementById("facultySearch").value;
            document.getElementById("pFacultyEmpId").innerText = document.getElementById("facultyEmpId").value;
            document.getElementById("pFacultyDesignation").innerText = document.getElementById("facultyDesignation").value;
            document.getElementById("pFacultyEmail").innerText = document.getElementById("facultyEmail").value;
            document.getElementById("pFacultyPhone").innerText = document.getElementById("facultyPhone").value;
            document.getElementById("pFacultyDept").innerText = document.getElementById("facultyDept").value;

            document.getElementById("pAcademicYear").innerText = academicYear;
            document.getElementById("pReportType").innerText = reportTypeLabel;
            document.getElementById("pReportTitle").innerText = reportTitle;
            document.getElementById("pActivityDate").innerText = activityDate;
            document.getElementById("pActivityTime").innerText = `${startTime} to ${endTime}`;
            document.getElementById("pVenue").innerText = venue;
            document.getElementById("pProgramme").innerText = progs.join(", ");
            document.getElementById("pSemesterClass").innerText = `Sem ${semester} (${divisionClass})`;
            document.getElementById("pParticipantsCount").innerText = participantsCount;
            document.getElementById("pBatch").innerText = batch;
            document.getElementById("pStudentCoordinator").innerText = studentCoordinator;
            document.getElementById("pPublishWebsite").innerText = publishWebsite;
            document.getElementById("pPressNote").innerText = pressNote;
            document.getElementById("pCoordinators").innerText = coordinators;
            document.getElementById("pBriefObjective").innerText = briefObjective;

            // Sync dynamic specific fields to Print Area
            const printDynamicTable = document.getElementById("pDynamicMetaTable");
            printDynamicTable.innerHTML = specificHtml || "<tr><td colspan='4' class='text-center text-muted'>No dynamic fields configured.</td></tr>";

            let previewHtml = `
                <div class="text-center border-bottom pb-3 mb-4">
                    <h4 class="fw-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Gyanmanjari Innovative University</h4>
                    <span class="text-muted small text-uppercase fw-bold letter-spacing-1">Department of Information Technology</span>
                    <h5 class="mt-2 fw-semibold text-danger" style="font-family: 'Playfair Display', serif;">${reportTypeLabel} — Activity Documentation</h5>
                </div>
                
                <h6 class="text-danger fw-bold border-bottom pb-1 mb-2">1. Request Profile</h6>
                <table class="table table-bordered table-sm small mb-4 text-dark">
                    <tr>
                        <th class="table-light w-25">Faculty Name</th><td>${document.getElementById("facultySearch").value}</td>
                        <th class="table-light w-25">Employee ID</th><td>${document.getElementById("facultyEmpId").value}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Designation</th><td>${document.getElementById("facultyDesignation").value}</td>
                        <th class="table-light">Email Address</th><td>${document.getElementById("facultyEmail").value}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Department</th><td colspan="3">${document.getElementById("facultyDept").value}</td>
                    </tr>
                </table>

                <h6 class="text-danger fw-bold border-bottom pb-1 mb-2">2. Basic Activity Details</h6>
                <table class="table table-bordered table-sm small mb-4 text-dark">
                    <tr>
                        <th class="table-light w-25">Report Title</th><td colspan="3" class="fw-bold">${reportTitle}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Academic Year</th><td>${academicYear}</td>
                        <th class="table-light">Report Type</th><td>${reportTypeLabel}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Activity Date</th><td>${activityDate}</td>
                        <th class="table-light">Activity Duration</th><td>${startTime} to ${endTime}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Venue</th><td>${venue}</td>
                        <th class="table-light">Programme(s)</th><td>${progs.join(", ")}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Semester / Class</th><td>Sem ${semester} (${divisionClass})</td>
                        <th class="table-light">Participants Count</th><td>${participantsCount} student(s)</td>
                    </tr>
                    <tr>
                        <th class="table-light">Batch</th><td>${batch}</td>
                        <th class="table-light">Student Coordinator</th><td>${studentCoordinator}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Published on Website</th><td>${publishWebsite}</td>
                        <th class="table-light">Press Note Required</th><td>${pressNote}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Coordinators</th><td colspan="3">${coordinators}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Objective</th><td colspan="3" class="small">${briefObjective}</td>
                    </tr>
                </table>

                ${specificHtml ? `
                <h6 class="text-danger fw-bold border-bottom pb-1 mb-2">3. Dynamic Category Details</h6>
                <table class="table table-bordered table-sm small mb-4 text-dark">${specificHtml}</table>
                ` : ''}
            `;

            modalBody.innerHTML = previewHtml;

            const modal = new bootstrap.Modal(document.getElementById("previewModal"));
            modal.show();
        }

        // ── Print Report Trigger ──
        function triggerPrint() {
            const modalEl = document.getElementById("previewModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            window.print();
        }

        // ── Form Submission to send parallel emails with ZIP attachment ──
        function simulateFormSubmission() {
            if (!validateFullForm()) return;

            const submitBtn = document.getElementById("submitBtn");
            const facultyEmail = document.getElementById("facultyEmail").value;
            const reportTitle = document.getElementById("reportTitle").value || "New Report";

            if (!facultyEmail) {
                showToast("ERROR: Faculty Email is missing. Please re-fill Section 1.");
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>Reading attachment & Submitting...</span>
            `;

            const photosInput = document.getElementById("activityPhotos");
            const hasFile = photosInput.files.length > 0;

            const getAttachmentData = () => {
                if (!hasFile) {
                    return Promise.resolve({ attachment: "", filename: "" });
                }
                const file = photosInput.files[0];
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        resolve({
                            attachment: e.target.result,
                            filename: file.name
                        });
                    };
                    reader.onerror = function () {
                        resolve({ attachment: "", filename: "" });
                    };
                    reader.readAsDataURL(file);
                });
            };

            getAttachmentData().then(attachInfo => {
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Sending Emails...</span>
                `;

                const htmlMessageFaculty = generateReportHtml(true);
                const htmlMessageAdmin = generateReportHtml(false);
                const adminEmail = "dkdholakiya@gmiu.edu.in";

                const ccSelect = document.getElementById("ccEmails");
                const selectedCcEmails = ccSelect ? Array.from(ccSelect.selectedOptions).map(option => option.value) : [];

                return fetch("send-email", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        emails: [
                            {
                                to: facultyEmail,
                                cc: selectedCcEmails,
                                subject: `Submitted Report Copy: ${reportTitle}`,
                                html: htmlMessageFaculty
                            },
                            {
                                to: adminEmail,
                                cc: selectedCcEmails,
                                subject: `Make New Report : ${reportTitle} (Faculty Copy)`,
                                html: htmlMessageAdmin
                            }
                        ],
                        attachment: attachInfo.attachment,
                        filename: attachInfo.filename
                    })
                }).then(res => res.json());
            })
                .then(data => {
                    if (data.success) {
                        // 1. Notify user
                        showToast("SUCCESS: Report Submitted. Confirmation emails sending in background.");
                        // 2. Auto-fill Google Sheet in background
                        appendToGoogleSheet();
                        // 3. Clear draft & reset
                        localStorage.removeItem("gmiu_it_report_draft");
                        resetReportForm();
                    } else {
                        showToast("ERROR submitting report: " + (data.error || "Unknown error"));
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast("ERROR: Connection failed, could not send emails.");
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<i class="bi bi-cloud-arrow-up"></i> Submit & Send`;
                });
        }

        // ── Reset Form values ──
        function resetReportForm() {
            document.getElementById("reportForm").reset();
            document.getElementById("reportForm").classList.remove("was-validated");

            // Clear CC emails selection
            const ccSelect = document.getElementById("ccEmails");
            if (ccSelect) {
                Array.from(ccSelect.options).forEach(option => option.selected = false);
                if (typeof window.syncCcEmailsUi === "function") {
                    window.syncCcEmailsUi();
                }
            }

            // Reset Autocompletes hidden
            document.getElementById("facultyId").value = "";

            // Clear Faculty Coordinators selection
            const coordInput = document.getElementById("coordinators");
            if (coordInput) {
                coordInput.value = "";
                if (typeof window.syncCoordinatorsUi === "function") {
                    window.syncCoordinatorsUi("");
                }
            }

            // Clear custom PLM/RLM activity inputs
            const plmCustomInput = document.getElementById("customPlmActivity");
            if (plmCustomInput) {
                plmCustomInput.value = "";
                plmCustomInput.removeAttribute("required");
            }
            const plmCustomWrap = document.getElementById("customPlmActivityWrap");
            if (plmCustomWrap) plmCustomWrap.classList.add("d-none");

            const rlmCustomInput = document.getElementById("customRlmActivity");
            if (rlmCustomInput) {
                rlmCustomInput.value = "";
                rlmCustomInput.removeAttribute("required");
            }
            const rlmCustomWrap = document.getElementById("customRlmActivityWrap");
            if (rlmCustomWrap) rlmCustomWrap.classList.add("d-none");

            // Reset Sub-Type Selects and Custom inputs
            const customDeptSubInput = document.getElementById("customDepartmentalSubType");
            if (customDeptSubInput) {
                customDeptSubInput.value = "";
                customDeptSubInput.removeAttribute("required");
            }
            const customDeptSubWrap = document.getElementById("customDepartmentalSubTypeWrap");
            if (customDeptSubWrap) customDeptSubWrap.classList.add("d-none");

            const customPlaceSubInput = document.getElementById("customPlacementSubType");
            if (customPlaceSubInput) {
                customPlaceSubInput.value = "";
                customPlaceSubInput.removeAttribute("required");
            }
            const customPlaceSubWrap = document.getElementById("customPlacementSubTypeWrap");
            if (customPlaceSubWrap) customPlaceSubWrap.classList.add("d-none");

            const deptSubTypeSelect = document.getElementById("departmentalSubType");
            if (deptSubTypeSelect) deptSubTypeSelect.value = "";

            const placeSubTypeSelect = document.getElementById("placementSubType");
            if (placeSubTypeSelect) placeSubTypeSelect.value = "";

            const placementSpeaker = document.getElementById("placementSpeaker");
            if (placementSpeaker) placementSpeaker.value = "";

            const placementOutcomes = document.getElementById("placementOutcomes");
            if (placementOutcomes) placementOutcomes.value = "";

            // Reset dynamic placement activities visibility/required state
            togglePlacementActType();
            toggleDepartmentalCustomSubType();
            togglePlacementCustomSubType();

            // Hide dynamic section and show placeholder
            const sections = document.querySelectorAll(".dynamic-report-section");
            sections.forEach(sec => sec.classList.add("d-none"));
            document.getElementById("dynamicPlaceholder").classList.remove("d-none");

            // Hide custom type wrap
            document.getElementById("customReportTypeWrap").classList.add("d-none");
            document.getElementById("customReportType").removeAttribute("required");

            // Clear counters
            document.querySelectorAll(".word-counter").forEach(textarea => {
                const countSpan = document.getElementById(textarea.id + "Count");
                if (countSpan) countSpan.innerText = "0";
            });

            // Clear file badge lists
            document.querySelectorAll(".selected-files-list").forEach(list => list.innerHTML = "");

            // Reset ZIP and Drive validations to default required state
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            photosInput.classList.remove("is-invalid");
            driveInput.classList.remove("is-invalid");
            photosInput.setAttribute("required", "true");
            driveInput.setAttribute("required", "true");

            // Lock and hide sections 2, 3, and 4
            const secTwo = document.getElementById("secItemTwo");
            const secThree = document.getElementById("secItemThree");
            const secFive = document.getElementById("secItemFive");
            if (secTwo) secTwo.classList.add("d-none");
            if (secThree) secThree.classList.add("d-none");
            if (secFive) secFive.classList.add("d-none");

            // Collapse all accordions except collapseOne
            const collapses = document.querySelectorAll(".accordion-collapse");
            collapses.forEach((col, index) => {
                const bootstrapCollapse = bootstrap.Collapse.getOrCreateInstance(col);
                if (index === 0) {
                    bootstrapCollapse.show();
                } else {
                    bootstrapCollapse.hide();
                }
            });

            // Reset step node classes and re-enable step 1, disable others
            const stepNodes = document.querySelectorAll(".step-node");
            stepNodes.forEach((node, index) => {
                node.classList.remove("completed");
                if (index === 0) {
                    node.classList.add("active");
                    node.classList.remove("disabled-step");
                } else {
                    node.classList.remove("active");
                    node.classList.add("disabled-step");
                }
            });
            document.getElementById("stepperProgressBar").style.width = "0%";

            // Clear local storage draft
            localStorage.removeItem("gmiu_it_report_draft");

            showToast("Report parameters successfully reset.");
        }

        // ═══════════════════════════════════════════════════════════
        //  GOOGLE SHEETS INTEGRATION
        //  Fires silently after a successful form submission.
        //  Reads SHEETS_CONFIG from assets/js/sheetsConfig.js
        // ═══════════════════════════════════════════════════════════
        function appendToGoogleSheet() {

            // ── Collect form data ──
            const facultyName = document.getElementById('facultySearch').value || '';
            const empId = document.getElementById('facultyEmpId').value || '';
            const designation = document.getElementById('facultyDesignation').value || '';
            const facultyEmail = document.getElementById('facultyEmail').value || '';
            const academicYear = document.getElementById('academicYear').value || '';

            // Report type label
            const reportSelect = document.getElementById('reportType');
            let reportTypeLabel = reportSelect.options[reportSelect.selectedIndex]?.text || '';
            if (reportSelect.value === 'other') {
                const customType = document.getElementById('customReportType').value.trim();
                if (customType) reportTypeLabel = `Other (${customType})`;
            }

            const reportTitle = document.getElementById('reportTitle').value || '';
            const activityDate = document.getElementById('activityDate').value || '';
            const startTime = document.getElementById('startTime').value || '';
            const endTime = document.getElementById('endTime').value || '';
            const venue = document.getElementById('venue').value || '';

            // Programmes
            const progs = [];
            document.querySelectorAll('.prog-checkbox:checked').forEach(cb => progs.push(cb.value));

            const semester = document.getElementById('semester').value || '';
            const division = document.getElementById('divisionClass').value || '';
            const participants = document.getElementById('participantsCount').value || '';
            const coordinators = document.getElementById('coordinators').value || '';
            const objective = document.getElementById('briefObjective').value || '';
            const driveLink = document.getElementById('driveLink').value || '';

            // New fields for sheet
            const batch = document.getElementById('batch').value || '';
            const studentCoordinator = document.getElementById('studentCoordinator').value || '';
            const publishWebsiteRaw = document.querySelector('input[name="publishWebsite"]:checked')?.value || '';
            const publishWebsite = (publishWebsiteRaw.toLowerCase() === 'yes') ? '✅' : ((publishWebsiteRaw.toLowerCase() === 'no') ? '❌' : '-');
            const pressNoteRaw = document.querySelector('input[name="pressNote"]:checked')?.value || '';
            const pressNote = (pressNoteRaw.toLowerCase() === 'yes') ? '✅' : ((pressNoteRaw.toLowerCase() === 'no') ? '❌' : '-');
            const placementActType = document.querySelector('input[name="placementActType"]:checked')?.value || '-';

            // Compile dynamic activity-specific details
            let activityDetails = '';
            const activeSec = document.querySelector('.dynamic-report-section:not(.d-none)');
            if (activeSec) {
                if (reportSelect.value === 'training_placement') {
                    const rlmRadio = document.getElementById('typeRLM');
                    const plmRadio = document.getElementById('typePLM');
                    let actVal = '';
                    if (rlmRadio && rlmRadio.checked) {
                        const rlmVal = document.getElementById('rlmActivity').value || '';
                        if (rlmVal === 'other') {
                            actVal = document.getElementById('customRlmActivity').value || '';
                        } else {
                            actVal = rlmVal;
                        }
                    } else if (plmRadio && plmRadio.checked) {
                        const plmVal = document.getElementById('plmActivity').value || '';
                        if (plmVal === 'other') {
                            actVal = document.getElementById('customPlmActivity').value || '';
                        } else {
                            actVal = plmVal;
                        }
                    }
                    
                    const subTypeSelect = document.getElementById('placementSubType');
                    let subTypeVal = subTypeSelect ? subTypeSelect.value : '';
                    if (subTypeVal === 'Other') {
                        subTypeVal = document.getElementById('customPlacementSubType').value || '';
                    }
                    
                    const placementSpeaker = document.getElementById('placementSpeaker')?.value || '';
                    const placementOutcomes = document.getElementById('placementOutcomes')?.value || '';
                    activityDetails = `Activity: ${actVal}`;
                    if (subTypeVal) {
                        activityDetails += ` | Activity Sub-Type: ${subTypeVal}`;
                    }
                    if (placementSpeaker) {
                        activityDetails += ` | Speaker: ${placementSpeaker}`;
                    }
                    if (placementOutcomes) {
                        activityDetails += ` | Outcomes: ${placementOutcomes}`;
                    }
                } else {
                    const detailsArr = [];
                    const inputs = activeSec.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        let isHidden = false;
                        let p = input;
                        while (p && p !== activeSec) {
                            if (p.classList.contains('d-none')) {
                                isHidden = true;
                                break;
                            }
                            p = p.parentElement;
                        }
                        if (isHidden) return;

                        if (input.type === 'radio') {
                            if (input.checked) {
                                const parentLabel = input.closest('.col-md-12')?.querySelector('.form-label')?.innerText || 'Activity Type';
                                const label = parentLabel.replace('*', '').trim();
                                const val = input.value;
                                detailsArr.push(`${label}: ${val}`);
                            }
                        } else {
                            const label = (input.previousElementSibling ? input.previousElementSibling.innerText : 'Field').replace('*', '').trim();
                            const val = input.value || '-';
                            detailsArr.push(`${label}: ${val}`);
                        }
                    });
                    activityDetails = detailsArr.join(' | ');
                }
            }

            const payload = {
                facultyName,
                empId,
                designation,
                facultyEmail,
                academicYear,
                reportType: reportTypeLabel,
                reportTitle,
                activityDate,
                startTime,
                endTime,
                venue,
                programmes: progs.join(', '),
                semester,
                division,
                participants,
                coordinators,
                driveLink,
                batch,
                studentCoordinator,
                publishWebsite,
                pressNote,
                placementActType,
                activityDetails
            };

            // ── POST to Backend Sheets Proxy ──
            fetch('proxy-sheets?target=report', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network response not ok');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        console.info('[GMIU Sheets] Data logged to sheet successfully.');
                        showToast('✔ Report data logged to Department Sheet.');
                    } else {
                        console.warn('[GMIU Sheets] Failed to log:', data.error);
                    }
                })
                .catch(err => {
                    console.warn('[GMIU Sheets] Failed to write to Google Sheet:', err);
                });
        }

        // ── Custom Toast Helper ──
        function showToast(message) {
            const toast = document.getElementById("gmiuToast");
            document.getElementById("toastMessage").innerText = message;

            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        // ── FAB Nav Toggle ──
        const fabBtn = document.getElementById('fabBtn');
        const fabMenu = document.getElementById('fabMenu');
        const iconMenu = fabBtn.querySelector('.fab-icon-menu');
        const iconClose = fabBtn.querySelector('.fab-icon-close');

        fabBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = fabMenu.classList.toggle('open');
            fabBtn.classList.toggle('active', isOpen);
            iconMenu.style.display = isOpen ? 'none' : 'block';
            iconClose.style.display = isOpen ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('fabNav').contains(e.target)) {
                fabMenu.classList.remove('open');
                fabBtn.classList.remove('active');
                iconMenu.style.display = 'block';
                iconClose.style.display = 'none';
            }
        });
    </script>
</body>

</html>