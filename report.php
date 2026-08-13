<?php require_once 'auto-cache-bust.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Departmental Report Management System for academic events, Expert talks, and achievement documentation.">
    <title>Report System — CE & IT Department</title>
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
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">
    <link rel="stylesheet" href="assets/css/theme-light.css?v=<?php echo time(); ?>">

    <!-- Custom Dark Glassmorphic & Print Styling imported from style.css -->
    <!-- html2pdf Library for Client-side Automatic Email PDF Attachment -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                <a href="./" class="back-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Portal
                </a>

                <div class="rp-header-center">
                    <div class="rp-dept-badge">
                        <span class="rp-badge-dot"></span>
                        Department of CE & IT
                    </div>
                    <h1 class="rp-title">Report Management System</h1>
                </div>

                <span class="portal-badge">Report System</span>
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
                    <li class="breadcrumb-item"><a href="./" class="text-decoration-none text-muted">Home</a>
                    </li>
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
                                            <option value="other">Other</option>
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
                                        <div class="input-group date-input-group">
                                            <input type="text" class="form-control form-control-dark-input date-picker-input" id="activityDate"
                                                placeholder="YYYY-MM-DD" onclick="openDatePicker('activityDate')" readonly required>
                                            <button class="btn date-picker-btn" type="button" onclick="openDatePicker('activityDate')" title="Select Date">
                                                <i class="bi bi-calendar-event-fill"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Activity Date is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="startTime">Start Time <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group clock-input-group">
                                            <input type="text" class="form-control form-control-dark-input clock-picker-input" id="startTime"
                                                placeholder="11:05 PM" onclick="openClockPicker('startTime')" readonly required>
                                            <button class="btn clock-picker-btn" type="button" onclick="openClockPicker('startTime')" title="Select Start Time">
                                                <i class="bi bi-clock-fill"></i>
                                            </button>
                                        </div>
                                        <div class="invalid-feedback">Start Time is required.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="endTime">End Time <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group clock-input-group">
                                            <input type="text" class="form-control form-control-dark-input clock-picker-input" id="endTime"
                                                placeholder="12:05 PM" onclick="openClockPicker('endTime')" readonly required>
                                            <button class="btn clock-picker-btn" type="button" onclick="openClockPicker('endTime')" title="Select End Time">
                                                <i class="bi bi-clock-fill"></i>
                                            </button>
                                        </div>
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
                                        <div class="d-flex flex-wrap gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="Diploma" id="progDiploma">
                                                <label class="form-check-label text-light"
                                                    for="progDiploma">Diploma</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="Diploma Premium" id="progDiplomaPremium">
                                                <label class="form-check-label text-light"
                                                    for="progDiplomaPremium">Diploma Premium</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="Diploma PLM" id="progDiplomaPLM">
                                                <label class="form-check-label text-light"
                                                    for="progDiplomaPLM">Diploma PLM</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="B.Tech" id="progBTech">
                                                <label class="form-check-label text-light"
                                                    for="progBTech">B.Tech</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="B.Tech Premium" id="progBTechPremium">
                                                <label class="form-check-label text-light"
                                                    for="progBTechPremium">B.Tech Premium</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="B.Tech PLM" id="progBTechPLM">
                                                <label class="form-check-label text-light"
                                                    for="progBTechPLM">B.Tech PLM</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="M.Tech" id="progMTech">
                                                <label class="form-check-label text-light"
                                                    for="progMTech">M.Tech</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="M.Tech Premium" id="progMTechPremium">
                                                <label class="form-check-label text-light"
                                                    for="progMTechPremium">M.Tech Premium</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input prog-checkbox" type="checkbox"
                                                    value="DLM" id="progDLM">
                                                <label class="form-check-label text-light"
                                                    for="progDLM">DLM</label>
                                            </div>
                                        </div>
                                        <div class="text-danger d-none rp-error-text" id="progError">Select at least one
                                            programme.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="semester">Semester <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="semester" required>
                                            <option value="" disabled selected>Select Semester...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a semester.</div>
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
                                        <label class="form-label text-light">Report is published on Website? <span class="text-danger">*</span></label>
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
                                        <label class="form-label" for="coordSearch">Faculty Coordinator(s) <span
                                                class="text-danger"></span></label>
                                        <div class="cc-select-wrap">
                                            <input type="hidden" id="coordinators" name="coordinators" required>
                                            <div class="cc-tags-container" id="coordTagsContainer"></div>
                                            <input type="text" id="coordSearch" placeholder="Type or click to select coordinators..." autocomplete="off">
                                            <div class="search-dropdown-list" id="coordDropdownList"></div>
                                            <div class="invalid-feedback">Faculty Coordinator is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="briefObjective">Brief Objective <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="briefObjective" rows="4"
                                            placeholder="Define the primary objective of this activity (Minimum 51 words required)..."
                                            required></textarea>
                                        <div class="invalid-feedback" id="briefObjectiveFeedback">Brief Objective is required and must contain at least 51 words.</div>
                                        <div class="form-text mt-1 d-flex justify-content-between align-items-center">
                                            <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Minimum 51 words required to proceed to next section.</span>
                                            <span class="badge bg-dark text-warning border border-warning" id="briefObjectiveWordCount">0 / 51 words</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-light">Photo Submission Method <span class="text-danger">*</span></label>
                                        <div class="d-flex flex-wrap gap-3 mt-1 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="photoMethod" id="photoMethodDrive" value="drive" checked required>
                                                <label class="form-check-label text-light" for="photoMethodDrive">Google Drive Link</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="photoMethod" id="photoMethodZip" value="zip" required>
                                                <label class="form-check-label text-light" for="photoMethodZip">Upload ZIP File (Max 5 MB)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="photoMethod" id="photoMethodEmail" value="email" required>
                                                <label class="form-check-label text-light" for="photoMethodEmail">ZIP > 5 MB (Attach via Email Reply)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 d-none" id="activityPhotosWrap">
                                        <label class="form-label" for="activityPhotos">Upload Activity Photos (ZIP format only) <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" id="activityPhotos" accept=".zip">
                                        <div class="form-text zip-warning-notice mt-2 small d-flex align-items-start gap-2">
                                            <i class="bi bi-info-circle-fill fs-6 mt-1 flex-shrink-0"></i>
                                            <span>Do not upload ZIP files larger than 5 MB in the form. Submit the report first and attach the ZIP file in the email reply you receive.</span>
                                        </div>
                                        <div class="selected-files-list" id="activityPhotosList"></div>
                                        <div class="invalid-feedback">Please upload a ZIP file containing the photos.</div>
                                    </div>
                                    <div class="col-md-12" id="driveLinkWrap">
                                        <label class="form-label" for="driveLink">Google Drive Link <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control" id="driveLink" placeholder="https://drive.google.com/..." oninput="syncEmailPreview()">
                                        <div class="invalid-feedback">Please enter a valid Google Drive link.</div>
                                    </div>
                                    <div class="col-md-12 d-none" id="emailReplyNoticeWrap">
                                        <div class="form-check p-3 rounded" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" id="largeZipCheckbox">
                                            <label class="form-check-label text-light fw-bold ms-1" for="largeZipCheckbox">
                                                My ZIP file is larger than 5 MB. I will submit the report first and attach the ZIP file in the email reply I receive. <span class="text-danger">*</span>
                                            </label>
                                            <div class="invalid-feedback text-danger mt-1">Please check this box to confirm you will send the ZIP file via email reply.</div>
                                        </div>
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
                                            <select class="form-select dynamic-field" id="intCollaborationType" onchange="toggleIntCollaborationCustomType()">
                                                <option value="" disabled selected>Select Collaboration Type...</option>
                                                <option value="Student Exchange">Student Exchange</option>
                                                <option value="Faculty Exchange">Faculty Exchange</option>
                                                <option value="Joint Research">Joint Research</option>
                                                <option value="Webinar/Seminar">Webinar/Seminar</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            
                                            <!-- Custom International Collaboration Type input -->
                                            <div class="mt-2 d-none" id="customIntCollaborationTypeWrap">
                                                <label class="form-label text-warning small" for="customIntCollaborationType">Custom Collaboration Type Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customIntCollaborationType" placeholder="Enter custom collaboration type...">
                                                <div class="invalid-feedback">Please specify the custom collaboration type.</div>
                                            </div>
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
                                            <select class="form-select dynamic-field" id="centralCategory" onchange="toggleCentralCustomCategory()">
                                                <option value="" disabled selected>Select Category...</option>
                                                <option value="Cultural">Cultural</option>
                                                <option value="Sports">Sports</option>
                                                <option value="Social Welfare">Social Welfare</option>
                                                <option value="Tech-Fest">Tech-Fest</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            
                                            <!-- Custom Central Category input -->
                                            <div class="mt-2 d-none" id="customCentralCategoryWrap">
                                                <label class="form-label text-warning small" for="customCentralCategory">Custom Event Category Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control dynamic-field" id="customCentralCategory" placeholder="Enter custom event category...">
                                                <div class="invalid-feedback">Please specify the custom event category.</div>
                                            </div>
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
                                                <label class="form-label" for="ccSearch">CC Emails (Multi-select)</label>
                                                <div class="cc-select-wrap">
                                                    <!-- Under original ID and name so the form/draft handlers work perfectly, but visually hidden -->
                                                    <select class="form-select d-none" id="ccEmails" name="ccEmails[]" multiple>
                                                        <!-- Dynamically populated options -->
                                                    </select>
                                                    <div class="cc-tags-container" id="ccTagsContainer"></div>
                                                    <input type="text" id="ccSearch" placeholder="Type or click to select CC emails..." autocomplete="off">
                                                    <div class="search-dropdown-list" id="ccDropdownList"></div>
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
                                            <div class="col-12 mt-3 mb-2">
                                                <div class="form-check d-flex align-items-center gap-2 flex-wrap">
                                                    <input class="form-check-input" type="checkbox" id="enableDeadline">
                                                    <label class="form-check-label text-warning fw-bold" for="enableDeadline" style="cursor: pointer; font-size: 15px; text-shadow: 0 0 10px rgba(245, 158, 11, 0.2);">
                                                        Set Submission Deadline <span class="text-danger">*</span>
                                                    </label>
                                                    <span class="text-light small ms-1">(Specify a target deadline for report review)</span>
                                                    <span id="deadlineDisplay" class="badge bg-danger ms-2 d-none" style="font-size: 13px; font-weight: 700;"></span>
                                                </div>
                                                <div class="text-danger d-none small mt-1 fw-bold" id="deadlineFormError">Submission deadline is required to submit the report.</div>
                                                <div class="mt-2 d-none" id="deadlinePickerWrap" style="max-width: 520px;">
                                                    <label class="form-label text-warning small mb-2">Select Deadline Date & Time <span class="text-danger">*</span></label>
                                                    <div class="row g-2">
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-muted small me-1 mb-1">Deadline Date</label>
                                                            <div class="input-group date-input-group">
                                                                <input type="text" class="form-control form-control-dark-input date-picker-input" id="deadlineDate"
                                                                    placeholder="YYYY-MM-DD" onclick="openDatePicker('deadlineDate')" readonly>
                                                                <button class="btn date-picker-btn" type="button" onclick="openDatePicker('deadlineDate')" title="Select Deadline Date">
                                                                    <i class="bi bi-calendar-event-fill"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label text-muted small me-1 mb-1">Deadline Time</label>
                                                            <div class="input-group clock-input-group">
                                                                <input type="text" class="form-control form-control-dark-input clock-picker-input" id="deadlineTime"
                                                                    placeholder="06:00 PM" onclick="openClockPicker('deadlineTime')" readonly>
                                                                <button class="btn clock-picker-btn" type="button" onclick="openClockPicker('deadlineTime')" title="Select Deadline Time">
                                                                    <i class="bi bi-clock-fill"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="deadlineVal" name="deadlineVal">
                                                </div>
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
        <?php 
        $footer_class = 'rp-footer text-center container';
        include 'footer.php'; 
        ?>

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

    <!-- ZIP File Size Warning Modal -->
    <div class="modal fade" id="zipSizeModal" tabindex="-1" aria-labelledby="zipSizeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content zip-size-modal-content p-4">
                <div class="modal-header border-0 pb-0 justify-content-center flex-column text-center">
                    <div class="zip-modal-icon mb-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="modal-title zip-modal-title" id="zipSizeModalLabel">ZIP File Size Exceeds 5 MB</h5>
                </div>
                <div class="modal-body text-center pt-3 pb-3">
                    <p class="zip-modal-text mb-0">
                        Your ZIP file size is larger than 5 MB. Please do not upload the ZIP file here. Submit the report first. You will receive an email on your registered email ID. Reply to that email and attach the ZIP file with your photos.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pt-2">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill text-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn zip-modal-btn px-4 rounded-pill" data-bs-dismiss="modal" onclick="switchToEmailReplyMethod()">Switch to Email Reply Method</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Floating Toast Notification -->
    <div class="gmiu-toast" id="gmiuToast">
        <i class="bi bi-check-circle-fill text-success fs-5"></i>
        <span id="toastMessage">Draft successfully saved.</span>
    </div>

    <?php 
    $active_page = 'report';
    include 'fab-nav.php'; 
    ?>

    <!-- ── Print Report Preview Area (Targeted by CSS @media print) ── -->
    <div id="printReportArea">
        <div class="print-header">
            <h4 id="printDeptHeader" style="margin: 0; font-family: 'Playfair Display', serif; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">
                DEPARTMENT OF INFORMATION TECHNOLOGY</h4>
            <div class="print-title" id="printHeaderTitle">Activity Documentation</div>
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
            <tr>
                <th>Photo Method</th>
                <td id="pPhotoMethod">-</td>
                <th>Photos ZIP File</th>
                <td id="pPhotoZip">-</td>
            </tr>
            <tr>
                <th>Google Drive Link</th>
                <td colspan="3" id="pDriveLink">-</td>
            </tr>
            <tr>
                <th>Submission Deadline</th>
                <td colspan="3" id="pDeadline">-</td>
            </tr>
        </table>

        <div class="print-section-title" id="pDynamicSecHeader">3. Activity-Specific Details</div>
        <table class="print-meta-table" id="pDynamicMetaTable">
            <!-- Dynamically populated rows based on selected report type -->
        </table>

        <div class="print-signatures-container">
            <div class="print-signatures-row">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-name" id="sigDevName">Mr. Dev K Dholakiya</div>
                    <div class="sig-title" id="sigDevTitle">Department of IT / ICT / CE / CSE</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-name" id="sigRequestedByName">Faculty Name</div>
                    <div class="sig-title" id="sigRequestedByTitle">Requested By (Faculty)</div>
                </div>
            </div>
            <div class="print-signatures-center">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-name" id="sigHodName">Prof. Dhaval Chandarana</div>
                    <div class="sig-title" id="sigHodTitle">Head of Department (CE & IT)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Shared Faculty Database -->
    <!-- Google Sheets configuration loaded securely from backend proxy -->
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>" defer></script>

    <!-- 6-Digit Email OTP Verification Component -->
    <script src="<?php echo v_asset('assets/js/otp-verify.js'); ?>"></script>

    <!-- Department Report System logic -->
    <script>
        // ── Helper Functions for Dynamic Department Title & Clean Activity Documentation Header ──
        function getDepartmentTitle(deptInput) {
            const dept = (deptInput || "Information Technology").toUpperCase().trim();
            if (dept.includes("BOTH") || (dept.includes("CE") && dept.includes("IT")) || (dept.includes("COMPUTER") && dept.includes("INFORMATION"))) {
                return "DEPARTMENT OF CE & IT";
            } else if (dept.includes("COMPUTER") || dept.includes("CE")) {
                return "DEPARTMENT OF COMPUTER ENGINEERING";
            } else if (dept.includes("INFORMATION") || dept.includes("IT")) {
                return "DEPARTMENT OF INFORMATION TECHNOLOGY";
            } else {
                return `DEPARTMENT OF ${dept}`;
            }
        }

        function getCleanActivityTitle(reportTypeLabel) {
            let cleanLabel = reportTypeLabel || "Activity";
            cleanLabel = cleanLabel.replace(/\s*activity\s*/gi, " ").replace(/\s*report\s*/gi, " ").trim();
            return `${cleanLabel} Activity Documentation`;
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Wait for facultyData to be available
            if (typeof facultyData === 'undefined') {
                console.error("Shared facultyData.js is missing or failed to load.");
            }

            // Helper to get color theme for avatar
            window.getAvatarClass = (member) => {
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

            // Initialize searchable autocomplete drop downs
            initAutocompleteSearch("facultySearch", "facultyId", "facultyDropdownList", fillFacultyDetails);
            // Populate CC emails multi-select dropdown
            populateCcEmails();

            // Populate Faculty Coordinators multi-select dropdown
            populateFacultyCoordinators();
            // Dynamic validation of Photos ZIP and Drive Link
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            const largeZipChk = document.getElementById("largeZipCheckbox");
            const clearPhotoDriveValidation = () => {
                if (photosInput.files.length > 0 || driveInput.value.trim() !== "") {
                    photosInput.classList.remove("is-invalid");
                    driveInput.classList.remove("is-invalid");
                }
            };
            photosInput.addEventListener("change", clearPhotoDriveValidation);
            driveInput.addEventListener("input", clearPhotoDriveValidation);
            if (largeZipChk) {
                largeZipChk.addEventListener("change", () => {
                    if (largeZipChk.checked) largeZipChk.classList.remove("is-invalid");
                });
            }

            // Toggle submission method listeners
            document.querySelectorAll('input[name="photoMethod"]').forEach(radio => {
                radio.addEventListener("change", togglePhotoMethod);
            });
            togglePhotoMethod();

            // Toggle deadline listener
            const enableDeadline = document.getElementById("enableDeadline");
            const deadlineVal = document.getElementById("deadlineVal");
            const deadlineDate = document.getElementById("deadlineDate");
            const deadlineTime = document.getElementById("deadlineTime");
            const deadlinePickerWrap = document.getElementById("deadlinePickerWrap");
            const deadlineDisplay = document.getElementById("deadlineDisplay");

            function syncDeadlineValue() {
                if (!deadlineDate || !deadlineTime || !deadlineVal) return;
                const dStr = deadlineDate.value.trim();
                const tStr = deadlineTime.value.trim();

                if (dStr && tStr) {
                    const match = tStr.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                    if (match) {
                        let h = parseInt(match[1], 10);
                        const m = match[2];
                        const ampm = match[3].toUpperCase();
                        if (ampm === 'PM' && h < 12) h += 12;
                        if (ampm === 'AM' && h === 12) h = 0;
                        const hh = String(h).padStart(2, '0');
                        deadlineVal.value = `${dStr}T${hh}:${m}`;
                    } else {
                        deadlineVal.value = `${dStr}T18:00`;
                    }
                    deadlineDate.classList.remove("is-invalid");
                    deadlineTime.classList.remove("is-invalid");
                    deadlineVal.classList.remove("is-invalid");
                    const deadlineFormError = document.getElementById("deadlineFormError");
                    if (deadlineFormError) deadlineFormError.classList.add("d-none");
                } else {
                    deadlineVal.value = "";
                }

                updateDeadlineDisplay();
                syncEmailPreview();
            }

            if (enableDeadline && deadlineVal && deadlinePickerWrap && deadlineDisplay) {
                enableDeadline.addEventListener("change", function () {
                    if (this.checked) {
                        deadlinePickerWrap.classList.remove("d-none");
                        deadlineVal.setAttribute("required", "true");

                        if (deadlineDate && !deadlineDate.value) {
                            deadlineDate.value = (typeof getFutureDateString === "function") ? getFutureDateString(2) : getTodayDateString();
                        }
                        if (deadlineTime && !deadlineTime.value) {
                            deadlineTime.value = "06:00 PM";
                        }
                        syncDeadlineValue();
                    } else {
                        deadlinePickerWrap.classList.add("d-none");
                        deadlineVal.removeAttribute("required");
                        deadlineVal.value = "";
                        if (deadlineDate) deadlineDate.value = "";
                        if (deadlineTime) deadlineTime.value = "";
                        deadlineVal.classList.remove("is-invalid");
                        if (deadlineDate) deadlineDate.classList.remove("is-invalid");
                        if (deadlineTime) deadlineTime.classList.remove("is-invalid");
                        deadlineDisplay.classList.add("d-none");
                        deadlineDisplay.innerText = "";
                    }
                    syncEmailPreview();
                });

                if (deadlineDate) {
                    deadlineDate.addEventListener("input", syncDeadlineValue);
                    deadlineDate.addEventListener("change", syncDeadlineValue);
                }
                if (deadlineTime) {
                    deadlineTime.addEventListener("input", syncDeadlineValue);
                    deadlineTime.addEventListener("change", syncDeadlineValue);
                }
                deadlineVal.addEventListener("input", function () {
                    updateDeadlineDisplay();
                    syncEmailPreview();
                });

                function updateDeadlineDisplay() {
                    const val = deadlineVal.value;
                    if (val) {
                        const dateObj = new Date(val);
                        if (!isNaN(dateObj)) {
                            const dd = String(dateObj.getDate()).padStart(2, '0');
                            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const yyyy = dateObj.getFullYear();
                            let hours = dateObj.getHours();
                            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                            const ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12;
                            hours = hours ? hours : 12;
                            const hh = String(hours).padStart(2, '0');
                            
                            const formatted = `Deadline: ${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}`;
                            deadlineDisplay.innerText = formatted;
                            deadlineDisplay.classList.remove("d-none");
                            return;
                        }
                    }
                    deadlineDisplay.classList.add("d-none");
                    deadlineDisplay.innerText = "";
                }
                
                // Expose helper to script
                window.syncDeadlineUi = updateDeadlineDisplay;
            }

            // Initialize Character Counters
            initCharacterCounters();

            // Initialize File Upload Displays
            initFileUploadDisplays();

            // Initialize Brief Objective Word Counter
            initBriefObjectiveWordCount();

            // Auto load draft if exists
            loadSavedDraft();

            // Set default today's date for activityDate if not already set (by draft or otherwise)
            const activityDateInput = document.getElementById("activityDate");
            if (activityDateInput && !activityDateInput.value) {
                activityDateInput.value = getTodayDateString();
                if (typeof syncEmailPreview === "function") {
                    syncEmailPreview();
                }
            }

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

        // ── Visual Toggle for Photo Submission Method ──
        function togglePhotoMethod() {
            const method = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
            const zipWrap = document.getElementById("activityPhotosWrap");
            const driveWrap = document.getElementById("driveLinkWrap");
            const emailWrap = document.getElementById("emailReplyNoticeWrap");

            if (zipWrap && driveWrap) {
                if (method === "zip") {
                    zipWrap.classList.remove("d-none");
                    driveWrap.classList.add("d-none");
                    if (emailWrap) emailWrap.classList.add("d-none");
                } else if (method === "drive") {
                    zipWrap.classList.add("d-none");
                    driveWrap.classList.remove("d-none");
                    if (emailWrap) emailWrap.classList.add("d-none");
                } else if (method === "email") {
                    zipWrap.classList.add("d-none");
                    driveWrap.classList.add("d-none");
                    if (emailWrap) emailWrap.classList.remove("d-none");
                }
            }
        }

        function switchToEmailReplyMethod() {
            const emailRadio = document.getElementById("photoMethodEmail");
            if (emailRadio) {
                emailRadio.checked = true;
                togglePhotoMethod();
            }
            const largeZipChk = document.getElementById("largeZipCheckbox");
            if (largeZipChk) {
                largeZipChk.checked = true;
                largeZipChk.classList.remove("is-invalid");
            }
        }

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
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
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
            let deptVal = member.department || "Information Technology";
            if (member.initials === "DRC" || member.name.includes("Dhaval Chandarana")) {
                const activeDept = localStorage.getItem("portal_dept");
                if (activeDept === "CE") {
                    deptVal = "Computer Engineering";
                } else if (activeDept === "IT") {
                    deptVal = "Information Technology";
                }
            }
            document.getElementById("facultyDept").value = deptVal;

            // Dynamically update document title based on resolved department
            if (deptVal === "Computer Engineering") {
                document.title = "Report System — CE Department";
            } else {
                document.title = "Report System — IT Department";
            }

            // Sync default CC emails based on department
            if (typeof window.setDefaultCcEmails === "function") {
                window.setDefaultCcEmails(deptVal);
            }

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
            const ccSearch = document.getElementById("ccSearch");
            const ccDropdownList = document.getElementById("ccDropdownList");
            const ccTagsContainer = document.getElementById("ccTagsContainer");
            const ccSelect = document.getElementById("ccEmails");

            if (!ccSearch || !ccDropdownList || !ccTagsContainer || !ccSelect || typeof facultyData === 'undefined') return;

            let selectedCCEmails = [];

            function getRequestedFacultyEmail() {
                const fEmail = document.getElementById("facultyEmail")?.value || document.getElementById("refEmail")?.value || "";
                return fEmail.toLowerCase().trim();
            }

            function getDefaultCcEmails(dept) {
                const currentDept = dept || document.getElementById("facultyDept")?.value || "Information Technology";
                const isCe = currentDept.toUpperCase().includes("COMPUTER") || currentDept.toUpperCase().includes("CE");
                const reqEmail = getRequestedFacultyEmail();

                // 1. HOD Email (Prof. Dhaval Chandarana)
                const hodMember = facultyData.find(m => m.designation === "HOD" || (m.designation && m.designation.toLowerCase() === "hod"));
                const hodEmail = hodMember ? hodMember.email : "drchandarana@gmiu.edu.in";

                // 2. Department Incharge HOD Email (Prof. Shwetaba Chauhan for IT / Prof. Ekta Unagar for CE)
                let inchargeMember;
                if (isCe) {
                    inchargeMember = facultyData.find(m => m.designation && m.designation.toLowerCase().includes("incharge hod ce"));
                } else {
                    inchargeMember = facultyData.find(m => m.designation && m.designation.toLowerCase().includes("incharge hod it"));
                }
                const inchargeEmail = inchargeMember ? inchargeMember.email : (isCe ? "ehunagar@gmiu.edu.in" : "sbchauhan@gmiu.edu.in");

                const defaults = [];
                if (hodEmail && hodEmail.toLowerCase() !== reqEmail) {
                    defaults.push(hodEmail);
                }
                if (inchargeEmail && inchargeEmail.toLowerCase() !== reqEmail && !defaults.includes(inchargeEmail)) {
                    defaults.push(inchargeEmail);
                }
                return defaults;
            }

            window.setDefaultCcEmails = function(dept) {
                const currentDept = dept || document.getElementById("facultyDept")?.value || "Information Technology";
                const isCe = currentDept.toUpperCase().includes("COMPUTER") || currentDept.toUpperCase().includes("CE");
                const oppositeInchargeEmail = isCe ? "sbchauhan@gmiu.edu.in" : "ehunagar@gmiu.edu.in";
                const reqEmail = getRequestedFacultyEmail();

                // Remove opposing department's incharge email AND requested faculty email if present
                selectedCCEmails = selectedCCEmails.filter(e => 
                    e.toLowerCase() !== oppositeInchargeEmail.toLowerCase() &&
                    e.toLowerCase() !== reqEmail
                );

                const defaults = getDefaultCcEmails(currentDept);
                defaults.forEach(email => {
                    if (!selectedCCEmails.includes(email)) {
                        selectedCCEmails.push(email);
                    }
                });
                updateCCTagsUI();
            };

            const ccSelectWrap = ccSearch.closest(".cc-select-wrap");

            // Focus search input when clicking the wrapper container
            ccSelectWrap.addEventListener("click", function (e) {
                if (e.target === ccSelectWrap || e.target === ccTagsContainer) {
                    ccSearch.focus();
                }
            });

            // Set initial defaults (HOD + Department Incharge HOD)
            selectedCCEmails = getDefaultCcEmails();

            ccSearch.addEventListener("focus", function () {
                ccDropdownList.classList.add("show");
                filterCCEmails();
            });

            ccSearch.addEventListener("input", function () {
                ccDropdownList.classList.add("show");
                filterCCEmails();
            });

            // Backspace to remove last tag if input is empty
            ccSearch.addEventListener("keydown", function (e) {
                if (e.key === "Backspace" && ccSearch.value === "" && selectedCCEmails.length > 0) {
                    removeCCTag(selectedCCEmails[selectedCCEmails.length - 1]);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function (e) {
                if (!e.target.closest(".cc-select-wrap")) {
                    ccDropdownList.classList.remove("show");
                    ccSearch.value = "";
                }
            });

            function filterCCEmails() {
                const query = ccSearch.value.toLowerCase().trim();
                const reqEmail = getRequestedFacultyEmail();

                // Filter out already selected emails AND requested faculty member's email
                const available = facultyData.filter(member => 
                    !selectedCCEmails.includes(member.email) &&
                    member.email.toLowerCase() !== reqEmail
                );

                // Match by email or name
                const filtered = available.filter(member =>
                    member.email.toLowerCase().includes(query) ||
                    member.name.toLowerCase().includes(query)
                );

                renderCCDropdown(filtered);
            }

            function renderCCDropdown(list) {
                ccDropdownList.innerHTML = "";
                if (list.length === 0) {
                    ccDropdownList.innerHTML = `<div class="no-results-item">No matching emails found</div>`;
                    return;
                }

                list.forEach(member => {
                    const item = document.createElement("div");
                    item.className = "dropdown-item";
                    item.innerHTML = `
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.email}</div>
                            <div class="item-desg">${member.name}</div>
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
                filterCCEmails();
            }

            function isRequestedByDev() {
                const reqEmail = getRequestedFacultyEmail();
                const reqSearch = (document.getElementById("facultySearch")?.value || "").toLowerCase();
                return reqEmail.includes("dkdholakiya") || reqSearch.includes("dev");
            }

            function removeCCTag(email) {
                if (!isRequestedByDev()) {
                    const defaultEmails = getDefaultCcEmails().map(e => e.toLowerCase());
                    if (defaultEmails.includes(email.toLowerCase())) {
                        showToast("Default HOD / Incharge HOD CC email cannot be removed.");
                        return;
                    }
                }
                selectedCCEmails = selectedCCEmails.filter(e => e !== email);
                updateCCTagsUI();
                ccSearch.focus();
                filterCCEmails();
            }

            function updateCCTagsUI() {
                ccTagsContainer.innerHTML = "";
                
                // Clear and update the hidden select options
                ccSelect.innerHTML = "";
                
                const defaultEmails = getDefaultCcEmails().map(e => e.toLowerCase());
                const isDev = isRequestedByDev();

                selectedCCEmails.forEach(email => {
                    const isDefault = defaultEmails.includes(email.toLowerCase());
                    const isLocked = isDefault && !isDev;
                    
                    const tag = document.createElement("div");
                    tag.className = "cc-tag" + (isLocked ? " cc-tag-default" : "");
                    
                    if (isLocked) {
                        tag.innerHTML = `
                            <span>${email}</span>
                            <span class="badge bg-secondary ms-1" style="font-size: 10px; opacity: 0.75; pointer-events: none;" title="Required Default CC"><i class="bi bi-lock-fill"></i></span>
                        `;
                    } else {
                        tag.innerHTML = `
                            <span>${email}</span>
                            <button type="button" class="cc-tag-remove">&times;</button>
                        `;
                        tag.querySelector(".cc-tag-remove").addEventListener("click", function (e) {
                            e.stopPropagation();
                            removeCCTag(email);
                        });
                    }
                    ccTagsContainer.appendChild(tag);
                    
                    const opt = document.createElement("option");
                    opt.value = email;
                    opt.selected = true;
                    ccSelect.appendChild(opt);
                });

                // Trigger change event to update previews or form state
                ccSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Render initial default CC tags in UI
            updateCCTagsUI();

            // Populate all emails initially in dropdown
            renderCCDropdown(facultyData);

            // Make updates globally accessible so loadDraft, reset etc. can trigger synchronization
            window.syncCcEmailsUi = function() {
                // Update selectedCCEmails based on native select state
                selectedCCEmails = Array.from(ccSelect.options)
                    .filter(opt => opt.selected)
                    .map(opt => opt.value);
                
                // Update UI tags
                updateCCTagsUI();
            };
        }

        // ── Faculty Coordinators Multiselect Dropdown ──
        function populateFacultyCoordinators() {
            const coordSearch = document.getElementById("coordSearch");
            const coordDropdownList = document.getElementById("coordDropdownList");
            const coordTagsContainer = document.getElementById("coordTagsContainer");
            const coordInput = document.getElementById("coordinators");

            if (!coordSearch || !coordDropdownList || !coordTagsContainer || !coordInput || typeof facultyData === 'undefined') return;

            let selectedCoordinators = [];

            const coordSelectWrap = coordSearch.closest(".cc-select-wrap");

            // Focus search input when clicking the wrapper container
            coordSelectWrap.addEventListener("click", function (e) {
                if (e.target === coordSelectWrap || e.target === coordTagsContainer) {
                    coordSearch.focus();
                }
            });

            // Populate all options initially
            renderCoordDropdown(facultyData);

            coordSearch.addEventListener("focus", function () {
                coordDropdownList.classList.add("show");
                filterCoordinators();
            });

            coordSearch.addEventListener("input", function () {
                coordDropdownList.classList.add("show");
                filterCoordinators();
            });

            // Backspace to remove last tag if input is empty
            coordSearch.addEventListener("keydown", function (e) {
                if (e.key === "Backspace" && coordSearch.value === "" && selectedCoordinators.length > 0) {
                    removeCoordTag(selectedCoordinators[selectedCoordinators.length - 1]);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function (e) {
                if (!e.target.closest(".cc-select-wrap")) {
                    coordDropdownList.classList.remove("show");
                    coordSearch.value = "";
                }
            });

            function filterCoordinators() {
                const query = coordSearch.value.toLowerCase().replace("prof.", "").replace("mr.", "").trim();
                // Filter out already selected coordinators
                const available = facultyData.filter(member => !selectedCoordinators.includes(member.name));

                // Match by name
                const filtered = available.filter(member =>
                    member.name.toLowerCase().includes(query)
                );

                renderCoordDropdown(filtered);
            }

            function renderCoordDropdown(list) {
                coordDropdownList.innerHTML = "";
                if (list.length === 0) {
                    coordDropdownList.innerHTML = `<div class="no-results-item">No faculty members found</div>`;
                    return;
                }

                list.forEach(member => {
                    const item = document.createElement("div");
                    item.className = "dropdown-item";
                    item.innerHTML = `
                        <div class="item-avatar ${getAvatarClass(member)}">${member.initials}</div>
                        <div class="item-info">
                            <div class="item-name">${member.name}</div>
                            <div class="item-desg">${member.designation} &nbsp;·&nbsp; ${member.empId}</div>
                        </div>
                    `;
                    item.addEventListener("click", function (e) {
                        e.stopPropagation();
                        addCoordTag(member.name);
                    });
                    coordDropdownList.appendChild(item);
                });
            }

            function addCoordTag(name) {
                if (!selectedCoordinators.includes(name)) {
                    selectedCoordinators.push(name);
                    updateCoordTagsUI();
                }
                coordSearch.value = "";
                coordSearch.focus();
                filterCoordinators();
            }

            function removeCoordTag(name) {
                selectedCoordinators = selectedCoordinators.filter(c => c !== name);
                updateCoordTagsUI();
                coordSearch.focus();
                filterCoordinators();
            }

            function updateCoordTagsUI() {
                coordTagsContainer.innerHTML = "";
                
                selectedCoordinators.forEach(name => {
                    const tag = document.createElement("div");
                    tag.className = "cc-tag";
                    tag.innerHTML = `
                        <span>${name}</span>
                        <button type="button" class="cc-tag-remove">&times;</button>
                    `;
                    tag.querySelector(".cc-tag-remove").addEventListener("click", function (e) {
                        e.stopPropagation();
                        removeCoordTag(name);
                    });
                    coordTagsContainer.appendChild(tag);
                });

                // Update hidden input value (comma-separated names)
                coordInput.value = selectedCoordinators.join(', ');

                // Dispatch input event for real-time validation checks
                coordInput.dispatchEvent(new Event('input', { bubbles: true }));

                // Sync email body preview
                syncEmailPreview();
            }

            // Expose a function to set coordinators programmatically (for loadSavedDraft)
            window.syncCoordinatorsUi = function(value) {
                selectedCoordinators = value ? value.split(', ').map(s => s.trim()).filter(Boolean) : [];
                updateCoordTagsUI();
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

        function toggleIntCollaborationCustomType() {
            const select = document.getElementById("intCollaborationType");
            const customWrap = document.getElementById("customIntCollaborationTypeWrap");
            const customInput = document.getElementById("customIntCollaborationType");

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

        function toggleCentralCustomCategory() {
            const select = document.getElementById("centralCategory");
            const customWrap = document.getElementById("customCentralCategoryWrap");
            const customInput = document.getElementById("customCentralCategory");

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
                const inputsToRequire = targetSection.querySelectorAll("input:not(#departmentalSpeaker):not(#placementSpeaker):not(#startupTeam):not(#researchJournal):not(#researchPubDate):not(#customRlmActivity):not(#customPlmActivity):not(#customDepartmentalSubType):not(#customPlacementSubType):not(#customIntCollaborationType):not(#customCentralCategory), select:not(#rlmActivity):not(#plmActivity):not(#placementSubType), textarea:not(#placementOutcomes)");
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
            if (reportType !== "international_relational") {
                const intCollaborationTypeSelect = document.getElementById("intCollaborationType");
                if (intCollaborationTypeSelect) intCollaborationTypeSelect.value = "";
            }
            if (reportType !== "central") {
                const centralCategorySelect = document.getElementById("centralCategory");
                if (centralCategorySelect) centralCategorySelect.value = "";
            }
            
            togglePlacementActType();
            toggleDepartmentalCustomSubType();
            togglePlacementCustomSubType();
            toggleIntCollaborationCustomType();
            toggleCentralCustomCategory();
            syncEmailPreview();
            updateStepState(2, true);
        }

        // ── Word Counter & Minimum 51 Words Validation for Brief Objective ──
        function getWordCount(str) {
            if (!str) return 0;
            const trimmed = str.trim();
            if (!trimmed) return 0;
            return trimmed.split(/\s+/).filter(Boolean).length;
        }

        function updateBriefObjectiveWordCount() {
            const el = document.getElementById("briefObjective");
            const countBadge = document.getElementById("briefObjectiveWordCount");
            const feedback = document.getElementById("briefObjectiveFeedback");
            if (!el || !countBadge) return;

            const words = getWordCount(el.value);
            countBadge.innerText = `${words} / 51 words`;

            if (words >= 51) {
                countBadge.className = "badge bg-success text-light border border-success";
                el.classList.remove("is-invalid");
                if (feedback) feedback.innerText = "Brief Objective is required.";
            } else {
                countBadge.className = "badge bg-dark text-warning border border-warning";
            }
        }

        function initBriefObjectiveWordCount() {
            const el = document.getElementById("briefObjective");
            if (el) {
                el.addEventListener("input", updateBriefObjectiveWordCount);
                updateBriefObjectiveWordCount();
            }
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

                    const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                    let oversized = false;

                    Array.from(this.files).forEach((file) => {
                        if (file.size > maxSizeBytes) {
                            oversized = true;
                        }
                    });

                    if (oversized) {
                        this.value = "";
                        listContainer.innerHTML = "";
                        const zipModalEl = document.getElementById("zipSizeModal");
                        if (zipModalEl) {
                            const zipModal = bootstrap.Modal.getOrCreateInstance(zipModalEl);
                            zipModal.show();
                        } else {
                            alert("Your ZIP file size is larger than 5 MB. Please do not upload the ZIP file here. Submit the report first. You will receive an email on your registered email ID. Reply to that email and attach the ZIP file.");
                        }
                        return;
                    }

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

                // Check ZIP, Drive or Email validation
                const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
                const photosInput = document.getElementById("activityPhotos");
                const driveInput = document.getElementById("driveLink");
                const largeZipChk = document.getElementById("largeZipCheckbox");

                if (photoMethod === "zip") {
                    if (photosInput.files.length === 0) {
                        photosInput.classList.add("is-invalid");
                        photosInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    } else if (photosInput.files[0].size > 5 * 1024 * 1024) {
                        photosInput.value = "";
                        const listContainer = document.getElementById("activityPhotosList");
                        if (listContainer) listContainer.innerHTML = "";
                        photosInput.classList.add("is-invalid");
                        const zipModalEl = document.getElementById("zipSizeModal");
                        if (zipModalEl) {
                            const zipModal = bootstrap.Modal.getOrCreateInstance(zipModalEl);
                            zipModal.show();
                        }
                        return;
                    }
                } else if (photoMethod === "drive") {
                    if (!driveInput.value.trim()) {
                        driveInput.classList.add("is-invalid");
                        driveInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }
                } else if (photoMethod === "email") {
                    if (largeZipChk && !largeZipChk.checked) {
                        largeZipChk.classList.add("is-invalid");
                        largeZipChk.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    } else if (largeZipChk) {
                        largeZipChk.classList.remove("is-invalid");
                    }
                }

                // Minimum 51 words validation for Brief Objective
                const briefObjInput = document.getElementById("briefObjective");
                const briefObjVal = briefObjInput ? briefObjInput.value : "";
                const briefObjWords = getWordCount(briefObjVal);
                const briefObjFeedback = document.getElementById("briefObjectiveFeedback");

                if (briefObjWords < 51) {
                    briefObjInput.classList.add("is-invalid");
                    if (briefObjFeedback) {
                        briefObjFeedback.innerText = `Brief Objective requires a minimum of 51 words (currently ${briefObjWords} word${briefObjWords === 1 ? '' : 's'}).`;
                    }
                    briefObjInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    briefObjInput.focus();
                    showToast(`ERROR: Brief Objective must contain at least 51 words. (Currently ${briefObjWords} word${briefObjWords === 1 ? '' : 's'})`);
                    return;
                } else {
                    briefObjInput.classList.remove("is-invalid");
                    if (briefObjFeedback) {
                        briefObjFeedback.innerText = "Brief Objective is required.";
                    }
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

            const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
            const zipInput = document.getElementById("activityPhotos");
            let zipName = "NOT UPLOADED";
            let driveLink = (document.getElementById("driveLink").value || "NOT PROVIDED").toUpperCase();
            if (photoMethod === "zip") {
                zipName = (zipInput.files.length > 0 ? zipInput.files[0].name : "NOT UPLOADED").toUpperCase();
                driveLink = "NOT PROVIDED";
            } else if (photoMethod === "drive") {
                zipName = "NOT UPLOADED (USING GOOGLE DRIVE LINK)";
            } else if (photoMethod === "email") {
                zipName = "MY ZIP FILE IS LARGER THAN 5 MB. I WILL SUBMIT THE REPORT FIRST AND ATTACH THE ZIP FILE IN THE EMAIL REPLY I RECEIVE.";
                driveLink = "ATTACH VIA EMAIL REPLY";
            }

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

            const deptTitle = getDepartmentTitle(dept);
            const docTitle = getCleanActivityTitle(reportType).toUpperCase();

            table += hr;
            table += centerAlign(deptTitle);
            table += centerAlign(docTitle);
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
            const isCe = (dept.includes("COMPUTER") || dept.includes("CE"));

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

            const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
            const zipInput = document.getElementById("activityPhotos");
            let zipName = "NOT UPLOADED";
            let rawDriveLink = document.getElementById("driveLink").value || "Not Provided";

            if (photoMethod === "zip") {
                zipName = (zipInput.files.length > 0 ? zipInput.files[0].name : "NOT UPLOADED").toUpperCase();
                rawDriveLink = "Not Provided";
            } else if (photoMethod === "drive") {
                zipName = "NOT UPLOADED (USING GOOGLE DRIVE LINK)";
            } else if (photoMethod === "email") {
                zipName = "MY ZIP FILE IS LARGER THAN 5 MB. I WILL SUBMIT THE REPORT FIRST AND ATTACH THE ZIP FILE IN THE EMAIL REPLY I RECEIVE.";
                rawDriveLink = "Not Provided (Attaching via Email Reply)";
            }
            const driveLink = rawDriveLink.toUpperCase();

            const batch = (document.getElementById("batch").value || "-").toUpperCase();
            const studentCoordinator = (document.getElementById("studentCoordinator").value || "-").toUpperCase();
            const publishWebsite = (document.querySelector('input[name="publishWebsite"]:checked')?.value || "-").toUpperCase();
            const pressNote = (document.querySelector('input[name="pressNote"]:checked')?.value || "-").toUpperCase();

            // Extract deadline
            const enableDeadline = document.getElementById("enableDeadline")?.checked;
            const deadlineVal = document.getElementById("deadlineVal")?.value;
            let deadline = "-";
            if (enableDeadline && deadlineVal) {
                const dateObj = new Date(deadlineVal);
                if (!isNaN(dateObj)) {
                    const dd = String(dateObj.getDate()).padStart(2, '0');
                    const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const yyyy = dateObj.getFullYear();
                    let hours = dateObj.getHours();
                    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    const hh = String(hours).padStart(2, '0');
                    deadline = `${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}`;
                }
            }

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
                                    <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Lora', Georgia, serif;">${label}</td>
                                    <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${val}</td>
                                </tr>
                            `;
                        }
                    } else {
                        const label = (input.previousElementSibling ? input.previousElementSibling.innerText : "Field").replace("*", "").trim().toUpperCase();
                        const val = (input.value || "-").toUpperCase();
                        specificHtml += `
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Lora', Georgia, serif;">${label}</td>
                                <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${val}</td>
                            </tr>
                        `;
                    }
                });
            }

            const deptTitle = getDepartmentTitle(dept);
            const docTitle = getCleanActivityTitle(reportType).toUpperCase();

            return `
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');
            </style>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
            <div style="font-family: 'Lora', Georgia, serif; line-height: 1.6; color: #333; max-width: 700px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div style="background-color: #8c1d1d; color: white; padding: 25px; text-align: center; font-family: 'Lora', Georgia, serif;">
                    <h2 style="margin: 0; font-size: 22px; font-weight: bold; letter-spacing: 0.5px; font-family: 'Playfair Display', Georgia, serif; text-transform: uppercase;">${deptTitle}</h2>
                    <div style="margin-top: 15px; display: inline-block; background-color: rgba(255, 255, 255, 0.2); padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: bold; font-family: 'Playfair Display', Georgia, serif;">
                        ${docTitle}
                    </div>
                </div>
                
                <div style="padding: 25px; background-color: #ffffff; font-family: 'Lora', Georgia, serif;">
                    <p style="margin-top: 0; font-size: 15px; font-family: 'Lora', Georgia, serif;">
                        ${isFacultyCopy ? `<strong>DEAR ${facultyName}</strong>,` : `<strong>DEAR ADMINISTRATOR</strong>,`}
                    </p>
                    <p style="font-size: 14px; color: #555; font-family: 'Lora', Georgia, serif;">
    KINDLY <strong>REVIEW</strong> THE <strong>DETAILS</strong> PROVIDED BELOW.
</p>
                    ${isFacultyCopy ? `
                    <p style="font-size: 14px; color: #555; font-family: 'Lora', Georgia, serif; margin-top: 10px;">
                        THIS INFORMATION IS FOR YOUR <strong>REVIEW</strong>. PLEASE CHECK IF THERE IS ANY <strong>MISSING OR INCORRECT DETAIL</strong>. IF ANY <strong>CHANGES OR UPDATES</strong> ARE REQUIRED, PLEASE <strong>REPORT AND SUBMIT</strong> THEM TO <strong>Mr. DEV DHOLAKIYA</strong>.
                    </p>
                    ` : ''}
                    <div style="margin-top: 15px; padding: 12px 15px; background-color: #fff8e6; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 13px; color: #854d0e; font-family: 'Lora', Georgia, serif;">
                        <strong>Please Note:</strong><br>
                        The report will be ready within a minimum of 48 hours.
                    </div>
                    
                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', Georgia, serif;">1. FACULTY REQUEST PROFILE</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Lora', Georgia, serif;">
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Lora', Georgia, serif;">FACULTY NAME</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${facultyName}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">DESIGNATION</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${designation}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">EMPLOYEE ID</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${empId}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">EMAIL ADDRESS</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;"><a href="mailto:${email.toLowerCase()}" style="color: #8c1d1d; text-decoration: underline; font-family: 'Lora', Georgia, serif;">${email}</a></td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">MOBILE NUMBER</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${phone}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">DEPARTMENT</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${dept}</td>
                        </tr>
                    </table>

                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', Georgia, serif;">2. BASIC ACTIVITY DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Lora', Georgia, serif;">
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; width: 30%; font-family: 'Lora', Georgia, serif;">REPORT TITLE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #111; font-family: 'Lora', Georgia, serif;">${title}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">ACADEMIC YEAR</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${year}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">REPORT TYPE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${reportType}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">ACTIVITY DATE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${date}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">DURATION / TIME</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${start} TO ${end}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">VENUE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${venue}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">PROGRAMME(S)</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${programmes}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">SEMESTER & DIVISION</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">SEM ${semester} (${division})</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">PARTICIPANTS COUNT</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${participants}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">BATCH</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${batch}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">STUDENT COORDINATOR</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${studentCoordinator}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">PUBLISHED ON WEBSITE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${publishWebsite}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">PRESS NOTE REQUIRED</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${pressNote}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">FACULTY COORDINATOR(S)</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${coordinators}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">BRIEF OBJECTIVE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">${objective}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">PHOTOS ZIP FILE</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: monospace;">${zipName}</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #fcfcfc; font-family: 'Lora', Georgia, serif;">GOOGLE DRIVE LINK</td>
                            <td style="padding: 10px; border: 1px solid #ddd; font-family: 'Lora', Georgia, serif;">
                                ${(rawDriveLink.toLowerCase() !== "not provided" && !rawDriveLink.toLowerCase().includes("not provided")) ? `<a href="${rawDriveLink}" target="_blank" style="color: #8c1d1d; text-decoration: underline; font-family: 'Lora', Georgia, serif;">OPEN GOOGLE DRIVE FOLDER</a>` : "NOT PROVIDED"}
                            </td>
                        </tr>
                        <tr style="background-color: #fff5f5;">
                            <td style="padding: 10px; border: 1px solid #feb2b2; font-weight: bold; color: #c53030; font-family: 'Lora', Georgia, serif;">SUBMISSION DEADLINE</td>
                            <td style="padding: 10px; border: 1px solid #feb2b2; font-family: 'Lora', Georgia, serif; font-weight: bold; color: #e53e3e; font-size: 14px; text-shadow: 0 0 8px rgba(229, 62, 62, 0.1);">${deadline}</td>
                        </tr>
                    </table>

                    ${specificHtml ? `
                    <h3 style="color: #8c1d1d; border-bottom: 2px solid #8c1d1d; padding-bottom: 5px; font-size: 16px; margin-top: 25px; font-family: 'Playfair Display', Georgia, serif;">3. ACTIVITY-SPECIFIC DETAILS</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; font-family: 'Lora', Georgia, serif;">
                        ${specificHtml}
                    </table>
                    ` : ''}
                </div>
                <div class="footer-container" style="background-color: #f8fafc; padding: 24px 24px; border-top: 1px solid #cbd5e1; text-align: center; font-family: 'Lora', Georgia, serif;">
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.5; font-family: 'Lora', Georgia, serif;">THIS EMAIL WAS AUTOMATICALLY GENERATED BY THE <br><a href="https://engineering.gt.tc/" target="_blank" style="color: ${isCe ? '#2563eb' : '#c0392b'}; text-decoration: none; font-weight: 600; font-family: 'Lora', Georgia, serif;">${isCe ? 'CE DEPARTMENT' : 'IT DEPARTMENT'}</a>.</p>
                    <p style="margin: 4px 0 0 0; font-size: 11px; color: #94a3b8; font-family: 'Lora', Georgia, serif;">&copy; 2026 ALL RIGHTS RESERVED.</p>
                    <p style="margin: 6px 0 0 0; font-size: 11px; color: #64748b; font-family: 'Lora', Georgia, serif;"><a href="https://engineering.gt.tc/" target="_blank" style="color: ${isCe ? '#2563eb' : '#c0392b'}; text-decoration: underline; font-weight: 600; font-family: 'Lora', Georgia, serif;">https://engineering.gt.tc/</a></p>
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

Please Note:
The report will be ready within a minimum of 48 hours.

Regards,
Department of CE & IT`;
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
            // Check ZIP, Drive or Email validation
            const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            const largeZipChk = document.getElementById("largeZipCheckbox");

            if (photoMethod === "zip") {
                if (photosInput.files.length === 0) {
                    photosInput.classList.add("is-invalid");
                    isValid = false;
                    jumpToSection(2);
                    return false;
                } else if (photosInput.files[0].size > 5 * 1024 * 1024) {
                    photosInput.value = "";
                    const listContainer = document.getElementById("activityPhotosList");
                    if (listContainer) listContainer.innerHTML = "";
                    photosInput.classList.add("is-invalid");
                    const zipModalEl = document.getElementById("zipSizeModal");
                    if (zipModalEl) {
                        const zipModal = bootstrap.Modal.getOrCreateInstance(zipModalEl);
                        zipModal.show();
                    }
                    isValid = false;
                    jumpToSection(2);
                    return false;
                }
            } else if (photoMethod === "drive") {
                if (!driveInput.value.trim()) {
                    driveInput.classList.add("is-invalid");
                    isValid = false;
                    jumpToSection(2);
                    return false;
                }
            } else if (photoMethod === "email") {
                if (largeZipChk && !largeZipChk.checked) {
                    largeZipChk.classList.add("is-invalid");
                    isValid = false;
                    jumpToSection(2);
                    return false;
                } else if (largeZipChk) {
                    largeZipChk.classList.remove("is-invalid");
                }
            }

            // Minimum 51 words validation for Brief Objective
            const briefObjInput = document.getElementById("briefObjective");
            const briefObjVal = briefObjInput ? briefObjInput.value : "";
            const briefObjWords = getWordCount(briefObjVal);
            const briefObjFeedback = document.getElementById("briefObjectiveFeedback");

            if (briefObjWords < 51) {
                briefObjInput.classList.add("is-invalid");
                if (briefObjFeedback) {
                    briefObjFeedback.innerText = `Brief Objective requires a minimum of 51 words (currently ${briefObjWords} word${briefObjWords === 1 ? '' : 's'}).`;
                }
                showToast(`ERROR: Brief Objective must contain at least 51 words. (Currently ${briefObjWords} word${briefObjWords === 1 ? '' : 's'})`);
                jumpToSection(2);
                briefObjInput.focus();
                return false;
            }

            // Check Deadline validation (MANDATORY)
            const enableDeadline = document.getElementById("enableDeadline");
            const deadlineVal = document.getElementById("deadlineVal");
            const deadlineDate = document.getElementById("deadlineDate");
            const deadlineTime = document.getElementById("deadlineTime");
            const deadlineFormError = document.getElementById("deadlineFormError");
            if (enableDeadline && deadlineVal) {
                if (!enableDeadline.checked || !deadlineVal.value) {
                    enableDeadline.classList.add("is-invalid");
                    deadlineVal.classList.add("is-invalid");
                    if (deadlineDate) deadlineDate.classList.add("is-invalid");
                    if (deadlineTime) deadlineTime.classList.add("is-invalid");
                    if (deadlineFormError) deadlineFormError.classList.remove("d-none");
                    isValid = false;
                    showToast("ERROR: Submission Deadline is mandatory. Please check Set Deadline and select a date/time.");
                    jumpToSection(4);
                    return false;
                } else {
                    enableDeadline.classList.remove("is-invalid");
                    deadlineVal.classList.remove("is-invalid");
                    if (deadlineDate) deadlineDate.classList.remove("is-invalid");
                    if (deadlineTime) deadlineTime.classList.remove("is-invalid");
                    if (deadlineFormError) deadlineFormError.classList.add("d-none");
                }
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
                        programmes: Array.from(document.querySelectorAll('.prog-checkbox:checked')).map(cb => cb.id),
                        semester: document.getElementById("semester").value,
                        divisionClass: document.getElementById("divisionClass").value,
                        participantsCount: document.getElementById("participantsCount").value,
                        coordinators: document.getElementById("coordinators").value,
                        briefObjective: document.getElementById("briefObjective").value,
                        driveLink: document.getElementById("driveLink").value,
                        photoMethod: document.querySelector('input[name="photoMethod"]:checked')?.value || "drive",
                        largeZipCheckbox: document.getElementById("largeZipCheckbox")?.checked,
                        enableDeadline: document.getElementById("enableDeadline")?.checked,
                        deadlineVal: document.getElementById("deadlineVal")?.value,

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
                        customIntCollaborationType: document.getElementById("customIntCollaborationType").value,
                        intDescription: document.getElementById("intDescription").value,
                        centralCategory: document.getElementById("centralCategory").value,
                        customCentralCategory: document.getElementById("customCentralCategory").value,
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
                        document.getElementById("activityDate").value = draft.activityDate || getTodayDateString();
                        document.getElementById("startTime").value = draft.startTime || "";
                        document.getElementById("endTime").value = draft.endTime || "";
                        document.getElementById("venue").value = draft.venue || "";
                        document.getElementById("semester").value = draft.semester || "";
                        document.getElementById("divisionClass").value = draft.divisionClass || "";
                        document.getElementById("participantsCount").value = draft.participantsCount || "";
                        document.getElementById("briefObjective").value = draft.briefObjective || "";
                        document.getElementById("driveLink").value = draft.driveLink || "";

                        if (draft.programmes) {
                            document.querySelectorAll(".prog-checkbox").forEach(cb => {
                                cb.checked = draft.programmes.includes(cb.id);
                            });
                        }

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

                        if (draft.photoMethod) {
                            const rb = document.querySelector(`input[name="photoMethod"][value="${draft.photoMethod}"]`);
                            if (rb) rb.checked = true;
                        }
                        if (draft.largeZipCheckbox) {
                            const chk = document.getElementById("largeZipCheckbox");
                            if (chk) chk.checked = true;
                        }
                        togglePhotoMethod();

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
                            "intCollaboratingOrg", "intCollaborationType", "customIntCollaborationType", "intDescription",
                            "centralCategory", "customCentralCategory", "centralHighlights"
                        ];
                        dynFields.forEach(id => {
                            const el = document.getElementById(id);
                            if (el && draft[id] !== undefined) el.value = draft[id];
                        });
 
                        toggleDepartmentalCustomSubType();
                        togglePlacementCustomSubType();
                        toggleIntCollaborationCustomType();
                        toggleCentralCustomCategory();

                        // Restore Deadline settings
                        if (draft.enableDeadline !== undefined) {
                            const chk = document.getElementById("enableDeadline");
                            if (chk) {
                                chk.checked = draft.enableDeadline;
                                const wrap = document.getElementById("deadlinePickerWrap");
                                const valInput = document.getElementById("deadlineVal");
                                if (chk.checked) {
                                    if (wrap) wrap.classList.remove("d-none");
                                    if (valInput) {
                                        valInput.setAttribute("required", "true");
                                        valInput.value = draft.deadlineVal || "";
                                    }
                                } else {
                                    if (wrap) wrap.classList.add("d-none");
                                    if (valInput) {
                                        valInput.removeAttribute("required");
                                        valInput.value = "";
                                    }
                                }
                                if (typeof window.syncDeadlineUi === "function") {
                                    window.syncDeadlineUi();
                                }
                            }
                        }

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
                    html: htmlMessage,
                    dept: document.getElementById("facultyDept").value === "Computer Engineering" ? "CE" : "IT"
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

            // Extract deadline
            const enableDeadline = document.getElementById("enableDeadline")?.checked;
            const deadlineVal = document.getElementById("deadlineVal")?.value;
            let deadline = "-";
            if (enableDeadline && deadlineVal) {
                const dateObj = new Date(deadlineVal);
                if (!isNaN(dateObj)) {
                    const dd = String(dateObj.getDate()).padStart(2, '0');
                    const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const yyyy = dateObj.getFullYear();
                    let hours = dateObj.getHours();
                    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    const hh = String(hours).padStart(2, '0');
                    deadline = `${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}`;
                }
            }

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
            syncPrintReportArea();

            const photoMethodText = document.getElementById("pPhotoMethod")?.innerText || "Google Drive Link";
            const photoZipText = document.getElementById("pPhotoZip")?.innerText || "Not Uploaded";
            const driveLinkText = document.getElementById("pDriveLink")?.innerText || "Not Provided";
            const rawDriveLink = document.getElementById("driveLink")?.value || "";

            const deptTitle = getDepartmentTitle(document.getElementById("facultyDept")?.value);
            const docTitle = getCleanActivityTitle(reportTypeLabel);

            let previewHtml = `
                <div class="text-center border-bottom pb-3 mb-4">
                    <h4 class="fw-bold mb-0 text-dark text-uppercase" style="font-family: 'Playfair Display', serif; letter-spacing: 0.5px;">${deptTitle}</h4>
                    <h5 class="mt-2 fw-semibold text-danger" style="font-family: 'Playfair Display', serif;">${docTitle}</h5>
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
                        <th class="table-light">Faculty Coordinator(s)</th><td colspan="3">${coordinators || "-"}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Brief Objective</th><td colspan="3" class="small">${briefObjective || "-"}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Photo Method</th><td>${photoMethodText}</td>
                        <th class="table-light">Photos ZIP File</th><td class="small fw-semibold">${photoZipText}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Google Drive Link</th>
                        <td colspan="3">
                            ${rawDriveLink && photoMethod === "drive" ? `<a href="${rawDriveLink}" target="_blank" class="text-danger fw-bold text-decoration-underline">${rawDriveLink}</a>` : driveLinkText}
                        </td>
                    </tr>
                    <tr class="table-danger">
                        <th class="text-danger fw-bold">Submission Deadline</th><td colspan="3" class="fw-bold text-danger" style="font-size: 15px;">${deadline}</td>
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

        // ── Sync Data to Printable Report Area ──
        function syncPrintReportArea() {
            const reportTitle = document.getElementById("reportTitle")?.value || "-";
            const academicYear = document.getElementById("academicYear")?.value || "-";
            const reportSelect = document.getElementById("reportType");
            let reportTypeLabel = reportSelect?.options[reportSelect.selectedIndex]?.text || "-";
            if (reportSelect?.value === "other") {
                const customType = document.getElementById("customReportType")?.value.trim();
                if (customType) {
                    reportTypeLabel = `Other (${customType})`;
                }
            }
            const activityDate = document.getElementById("activityDate")?.value || "-";
            const startTime = document.getElementById("startTime")?.value || "";
            const endTime = document.getElementById("endTime")?.value || "";
            const venue = document.getElementById("venue")?.value || "-";
            const semester = document.getElementById("semester")?.value || "-";
            const divisionClass = document.getElementById("divisionClass")?.value || "-";
            const participantsCount = document.getElementById("participantsCount")?.value || "-";
            const coordinators = document.getElementById("coordinators")?.value || "-";
            const briefObjective = document.getElementById("briefObjective")?.value || "-";

            // Extract deadline
            const enableDeadline = document.getElementById("enableDeadline")?.checked;
            const deadlineVal = document.getElementById("deadlineVal")?.value;
            let deadline = "-";
            if (enableDeadline && deadlineVal) {
                const dateObj = new Date(deadlineVal);
                if (!isNaN(dateObj)) {
                    const dd = String(dateObj.getDate()).padStart(2, '0');
                    const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const yyyy = dateObj.getFullYear();
                    let hours = dateObj.getHours();
                    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    const hh = String(hours).padStart(2, '0');
                    deadline = `${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}`;
                }
            }

            // New core fields
            const batch = document.getElementById("batch")?.value || "-";
            const studentCoordinator = document.getElementById("studentCoordinator")?.value || "-";
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

            // Photo submission details
            const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
            const zipInput = document.getElementById("activityPhotos");
            const rawDriveLink = document.getElementById("driveLink")?.value || "";

            let photoMethodText = "Google Drive Link";
            let photoZipText = "Not Uploaded (Using Google Drive Link)";
            let driveLinkText = rawDriveLink || "Not Provided";

            if (photoMethod === "zip") {
                photoMethodText = "Upload ZIP File (Max 5 MB)";
                photoZipText = zipInput && zipInput.files.length > 0 ? zipInput.files[0].name : "Not Uploaded";
                driveLinkText = "Not Provided";
            } else if (photoMethod === "email") {
                photoMethodText = "ZIP > 5 MB (Attach via Email Reply)";
                photoZipText = "My ZIP file is larger than 5 MB. I will submit the report first and attach the ZIP file in the email reply I receive.";
                driveLinkText = "Not Provided (Attaching via Email Reply)";
            }

            const deptTitle = getDepartmentTitle(document.getElementById("facultyDept")?.value);
            const docTitle = getCleanActivityTitle(reportTypeLabel);

            // Sync details to Printable Area
            const pDeptHeader = document.getElementById("printDeptHeader");
            if (pDeptHeader) pDeptHeader.innerText = deptTitle;

            const pHeader = document.getElementById("printHeaderTitle");
            if (pHeader) pHeader.innerText = docTitle;
            const setTxt = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.innerText = (val !== undefined && val !== null && val.trim() !== "") ? val : "-";
            };

            setTxt("pFacultyName", document.getElementById("facultySearch")?.value);
            setTxt("pFacultyEmpId", document.getElementById("facultyEmpId")?.value);
            setTxt("pFacultyDesignation", document.getElementById("facultyDesignation")?.value);
            setTxt("pFacultyEmail", document.getElementById("facultyEmail")?.value);
            setTxt("pFacultyPhone", document.getElementById("facultyPhone")?.value);
            setTxt("pFacultyDept", document.getElementById("facultyDept")?.value);

            setTxt("pAcademicYear", academicYear);
            setTxt("pReportType", reportTypeLabel);
            setTxt("pReportTitle", reportTitle);
            setTxt("pActivityDate", activityDate);
            setTxt("pActivityTime", (startTime || endTime) ? `${startTime} to ${endTime}` : "-");
            setTxt("pVenue", venue);
            setTxt("pProgramme", progs.join(", "));
            setTxt("pSemesterClass", (semester || divisionClass) ? `Sem ${semester} (${divisionClass})` : "-");
            setTxt("pParticipantsCount", participantsCount);
            setTxt("pBatch", batch);
            setTxt("pStudentCoordinator", studentCoordinator);
            setTxt("pPublishWebsite", publishWebsite);
            setTxt("pPressNote", pressNote);
            setTxt("pCoordinators", coordinators);
            setTxt("pBriefObjective", briefObjective);
            setTxt("pPhotoMethod", photoMethodText);
            setTxt("pPhotoZip", photoZipText);
            setTxt("pDriveLink", driveLinkText);
            setTxt("pDeadline", deadline);

            const printDynamicTable = document.getElementById("pDynamicMetaTable");
            if (printDynamicTable) {
                printDynamicTable.innerHTML = specificHtml || "<tr><td colspan='4' class='text-center text-muted'>No dynamic fields configured.</td></tr>";
            }

            // Sync Signatures in Printable Area
            const facultyNameVal = document.getElementById("facultySearch")?.value?.trim();
            const facultyDesgVal = document.getElementById("facultyDesignation")?.value?.trim();

            const sigDevNameEl = document.getElementById("sigDevName");
            if (sigDevNameEl) {
                sigDevNameEl.innerText = "Mr. Dev K Dholakiya";
            }
            const sigDevTitleEl = document.getElementById("sigDevTitle");
            if (sigDevTitleEl) {
                sigDevTitleEl.innerText = "Department of IT / ICT / CE / CSE";
            }

            const sigReqNameEl = document.getElementById("sigRequestedByName");
            if (sigReqNameEl) {
                sigReqNameEl.innerText = facultyNameVal || "Faculty Name";
            }
            const sigReqTitleEl = document.getElementById("sigRequestedByTitle");
            if (sigReqTitleEl) {
                sigReqTitleEl.innerText = facultyDesgVal ? `${facultyDesgVal} (Requested By)` : "Requested By (Faculty)";
            }

            const sigHodNameEl = document.getElementById("sigHodName");
            if (sigHodNameEl) {
                sigHodNameEl.innerText = "Prof. Dhaval Chandarana";
            }

            const deptVal = (document.getElementById("facultyDept")?.value || "IT").toUpperCase().trim();
            const sigHodTitleEl = document.getElementById("sigHodTitle");
            if (sigHodTitleEl) {
                if (deptVal.includes("BOTH") || (deptVal.includes("CE") && deptVal.includes("IT"))) {
                    sigHodTitleEl.innerText = "Head of Department (CE & IT)";
                } else if (deptVal.includes("COMPUTER") || deptVal.includes("CE")) {
                    sigHodTitleEl.innerText = "Head of Department (CE & IT)";
                } else {
                    sigHodTitleEl.innerText = "Head of Department (CE & IT)";
                }
            }
        }

        // ── Print Report Trigger ──
        function triggerPrint() {
            syncPrintReportArea();
            const modalEl = document.getElementById("previewModal");
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            window.print();
        }

        window.addEventListener("beforeprint", syncPrintReportArea);

        // ── Client-side PDF Report Generator for Automatic Email Attachment ──
        function generateReportPdfBase64() {
            syncPrintReportArea();
            const origElement = document.getElementById("printReportArea");
            if (!origElement || typeof html2pdf === "undefined") {
                return Promise.resolve(null);
            }

            // Create temporary container reset to viewport origin (0,0) with exact A4 printable canvas width (718px)
            const container = document.createElement("div");
            container.style.position = "fixed";
            container.style.top = "0px";
            container.style.left = "0px";
            container.style.width = "718px";
            container.style.zIndex = "-9999";
            container.style.opacity = "0.01";
            container.style.pointerEvents = "none";
            container.style.background = "#ffffff";
            container.style.color = "#000000";
            container.style.boxSizing = "border-box";
            container.style.margin = "0";
            container.style.padding = "0";

            const clone = origElement.cloneNode(true);
            clone.id = "pdfPrintClone";
            clone.style.display = "block";
            clone.style.width = "718px";
            clone.style.boxSizing = "border-box";
            clone.style.padding = "10px 0";
            clone.style.margin = "0";
            clone.style.background = "#ffffff";
            clone.style.color = "#000000";
            clone.style.fontFamily = "'Lora', Georgia, serif";

            // Enforce explicit font styling on clone elements matching print view
            const printHeaders = clone.querySelectorAll(".print-title, .print-section-title, #printDeptHeader");
            printHeaders.forEach(h => {
                h.style.fontFamily = "'Playfair Display', Georgia, serif";
            });

            // Ensure table styles in clone fit 100% within the 718px width
            const tables = clone.querySelectorAll("table");
            tables.forEach(t => {
                t.style.width = "100%";
                t.style.margin = "0 0 15px 0";
                t.style.boxSizing = "border-box";
                t.style.tableLayout = "fixed";
                t.style.wordBreak = "break-word";
                t.style.overflowWrap = "break-word";
                t.style.fontFamily = "'Lora', Georgia, serif";
            });

            const cells = clone.querySelectorAll("th, td");
            cells.forEach(c => {
                c.style.wordBreak = "break-word";
                c.style.overflowWrap = "break-word";
                c.style.whiteSpace = "normal";
                c.style.boxSizing = "border-box";
                c.style.fontFamily = "'Lora', Georgia, serif";
            });

            // Force Section 3 (Activity-Specific Details) to start cleanly at the top of Page 2
            const dynamicHeader = clone.querySelector("#pDynamicSecHeader");
            if (dynamicHeader) {
                dynamicHeader.style.pageBreakBefore = "always";
                dynamicHeader.style.breakBefore = "page";
                dynamicHeader.style.marginTop = "0";
            }

            // Ensure signatures container stays formatted properly in cloned PDF DOM
            const sigsContainer = clone.querySelector(".print-signatures-container");
            if (sigsContainer) {
                sigsContainer.style.width = "100%";
                sigsContainer.style.boxSizing = "border-box";
                sigsContainer.style.marginTop = "60px";
                sigsContainer.style.pageBreakInside = "avoid";
                sigsContainer.style.breakInside = "avoid";
            }
            const sigsRow = clone.querySelector(".print-signatures-row");
            if (sigsRow) {
                sigsRow.style.width = "100%";
                sigsRow.style.display = "flex";
                sigsRow.style.justifyContent = "space-between";
                sigsRow.style.marginBottom = "55px";
            }
            const sigsCenter = clone.querySelector(".print-signatures-center");
            if (sigsCenter) {
                sigsCenter.style.width = "100%";
                sigsCenter.style.display = "flex";
                sigsCenter.style.justifyContent = "center";
            }
            const sigBlocks = clone.querySelectorAll(".sig-block");
            sigBlocks.forEach(b => {
                b.style.paddingTop = "35px";
            });

            container.appendChild(clone);
            document.body.appendChild(container);

            const reportTitle = document.getElementById("reportTitle")?.value || "Activity_Report";
            const cleanFileName = reportTitle.replace(/[^a-zA-Z0-9_-]/g, "_") + "_Documentation.pdf";

            const opt = {
                margin:       [10, 10, 10, 10], // Standard 10mm A4 Margins
                filename:     cleanFileName,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 2, 
                    useCORS: true, 
                    logging: false, 
                    scrollX: 0, 
                    scrollY: 0,
                    x: 0,
                    y: 0,
                    width: 718,
                    windowWidth: 718
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
            };

            return html2pdf().set(opt).from(clone).outputPdf('datauristring').then(pdfDataUrl => {
                if (document.body.contains(container)) {
                    document.body.removeChild(container);
                }
                return {
                    data: pdfDataUrl,
                    filename: cleanFileName
                };
            }).catch(err => {
                if (document.body.contains(container)) {
                    document.body.removeChild(container);
                }
                console.error("PDF generation error:", err);
                return null;
            });
        }

        // ── Form Submission to send parallel emails with ZIP & PDF attachment ──
        function simulateFormSubmission() {
            if (!validateFullForm()) return;

            const submitBtn = document.getElementById("submitBtn");
            const facultyEmail = document.getElementById("facultyEmail").value;
            const facultyName = document.getElementById("facultySearch")?.value || document.getElementById("refName")?.value || "Faculty Member";
            const reportTitle = document.getElementById("reportTitle").value || "New Report";

            if (!facultyEmail) {
                showToast("ERROR: Faculty Email is missing. Please re-fill Section 1.");
                return;
            }

            // Trigger OTP verification if service script is active
            if (typeof window.triggerOtpVerification === "function") {
                window.triggerOtpVerification(facultyEmail, facultyName)
                    .then(() => {
                        executeActualSubmission();
                    })
                    .catch(err => {
                        console.log("OTP Verification cancelled or bypassed:", err);
                    });
            } else {
                executeActualSubmission();
            }

            function executeActualSubmission() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Generating PDF & Preparing Emails...</span>
                `;

                const photoMethod = document.querySelector('input[name="photoMethod"]:checked')?.value || "drive";
                const photosInput = document.getElementById("activityPhotos");
                const hasFile = (photoMethod === "zip" && photosInput && photosInput.files.length > 0);

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

                Promise.all([
                    generateReportPdfBase64(),
                    getAttachmentData()
                ]).then(([pdfObj, zipObj]) => {
                    submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Sending Emails with PDF Attachment...</span>
                    `;

                    const attachmentsList = [];
                    if (pdfObj && pdfObj.data) {
                        attachmentsList.push(pdfObj);
                    }
                    if (zipObj && zipObj.attachment && zipObj.filename) {
                        attachmentsList.push({
                            data: zipObj.attachment,
                            filename: zipObj.filename
                        });
                    }

                    const htmlMessageFaculty = generateReportHtml(true);
                    const htmlMessageAdmin = generateReportHtml(false);
                    const adminEmail = "dkdholakiya@gmiu.edu.in";

                    const ccSelect = document.getElementById("ccEmails");
                    const selectedCcEmails = ccSelect ? Array.from(ccSelect.selectedOptions).map(option => option.value) : [];

                    // Extract formatted deadline to append to subject
                    const enableDeadline = document.getElementById("enableDeadline")?.checked;
                    const deadlineVal = document.getElementById("deadlineVal")?.value;
                    let subjectDeadlineSuffix = "";
                    if (enableDeadline && deadlineVal) {
                        const dateObj = new Date(deadlineVal);
                        if (!isNaN(dateObj)) {
                            const dd = String(dateObj.getDate()).padStart(2, '0');
                            const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const yyyy = dateObj.getFullYear();
                            let hours = dateObj.getHours();
                            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                            const ampm = hours >= 12 ? 'PM' : 'AM';
                            hours = hours % 12;
                            hours = hours ? hours : 12;
                            const hh = String(hours).padStart(2, '0');
                            subjectDeadlineSuffix = ` [Deadline: ${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}]`;
                        }
                    }

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
                                    subject: `Submitted Report Copy: ${reportTitle}${subjectDeadlineSuffix}`,
                                    html: htmlMessageFaculty
                                },
                                {
                                    to: adminEmail,
                                    cc: selectedCcEmails,
                                    subject: `Make New Report : ${reportTitle} (Faculty Copy)${subjectDeadlineSuffix}`,
                                    html: htmlMessageAdmin
                                }
                            ],
                            attachments: attachmentsList,
                            attachment: zipObj ? zipObj.attachment : "",
                            filename: zipObj ? zipObj.filename : "",
                            dept: document.getElementById("facultyDept").value === "Computer Engineering" ? "CE" : "IT"
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
        }

        // ── Reset Form values ──
        function resetReportForm() {
            document.getElementById("reportForm").reset();
            document.getElementById("reportForm").classList.remove("was-validated");

            // Set default today's date for activityDate
            const activityDateInput = document.getElementById("activityDate");
            if (activityDateInput) {
                activityDateInput.value = getTodayDateString();
            }

            // Reset CC emails selection to defaults (HOD + Department Incharge HOD)
            if (typeof window.setDefaultCcEmails === "function") {
                window.setDefaultCcEmails();
            } else {
                const ccSelect = document.getElementById("ccEmails");
                if (ccSelect) {
                    Array.from(ccSelect.options).forEach(option => option.selected = false);
                    if (typeof window.syncCcEmailsUi === "function") {
                        window.syncCcEmailsUi();
                    }
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

            const customIntCollabInput = document.getElementById("customIntCollaborationType");
            if (customIntCollabInput) {
                customIntCollabInput.value = "";
                customIntCollabInput.removeAttribute("required");
            }
            const customIntCollabWrap = document.getElementById("customIntCollaborationTypeWrap");
            if (customIntCollabWrap) customIntCollabWrap.classList.add("d-none");

            const customCentralCatInput = document.getElementById("customCentralCategory");
            if (customCentralCatInput) {
                customCentralCatInput.value = "";
                customCentralCatInput.removeAttribute("required");
            }
            const customCentralCatWrap = document.getElementById("customCentralCategoryWrap");
            if (customCentralCatWrap) customCentralCatWrap.classList.add("d-none");

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
            toggleIntCollaborationCustomType();
            toggleCentralCustomCategory();

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
            if (typeof updateBriefObjectiveWordCount === "function") updateBriefObjectiveWordCount();

            // Clear file badge lists
            document.querySelectorAll(".selected-files-list").forEach(list => list.innerHTML = "");

            // Reset ZIP and Drive validations to default required state
            const photosInput = document.getElementById("activityPhotos");
            const driveInput = document.getElementById("driveLink");
            photosInput.classList.remove("is-invalid");
            driveInput.classList.remove("is-invalid");
            
            const driveRadio = document.getElementById("photoMethodDrive");
            if (driveRadio) driveRadio.checked = true;
            const largeZipChk = document.getElementById("largeZipCheckbox");
            if (largeZipChk) {
                largeZipChk.checked = false;
                largeZipChk.classList.remove("is-invalid");
            }
            togglePhotoMethod();

            // Reset Deadline check and picker
            const enableDeadlineChk = document.getElementById("enableDeadline");
            if (enableDeadlineChk) {
                enableDeadlineChk.checked = false;
                const wrap = document.getElementById("deadlinePickerWrap");
                const valInput = document.getElementById("deadlineVal");
                if (wrap) wrap.classList.add("d-none");
                if (valInput) {
                    valInput.removeAttribute("required");
                    valInput.value = "";
                    valInput.classList.remove("is-invalid");
                }
                const display = document.getElementById("deadlineDisplay");
                if (display) {
                    display.classList.add("d-none");
                    display.innerText = "";
                }
            }

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

            const enableDeadline = document.getElementById("enableDeadline")?.checked;
            const deadlineVal = document.getElementById("deadlineVal")?.value;
            let deadline = "-";
            if (enableDeadline && deadlineVal) {
                const dateObj = new Date(deadlineVal);
                if (!isNaN(dateObj)) {
                    const dd = String(dateObj.getDate()).padStart(2, '0');
                    const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const yyyy = dateObj.getFullYear();
                    let hours = dateObj.getHours();
                    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    const hh = String(hours).padStart(2, '0');
                    deadline = `${dd}/${mm}/${yyyy} ${hh}:${minutes} ${ampm}`;
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
                activityDetails,
                deadline
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
        function showToast(message, type = null) {
            const toast = document.getElementById("gmiuToast");
            const toastMsg = document.getElementById("toastMessage");
            if (!toast || !toastMsg) return;

            toastMsg.innerText = message;
            const icon = toast.querySelector("i");
            const isError = type === 'error' || (typeof message === 'string' && message.toUpperCase().startsWith("ERROR"));

            if (isError) {
                toast.classList.add("toast-error");
                toast.classList.remove("toast-success");
                if (icon) icon.className = "bi bi-exclamation-triangle-fill text-danger fs-5";
            } else {
                toast.classList.add("toast-success");
                toast.classList.remove("toast-error");
                if (icon) icon.className = "bi bi-check-circle-fill text-success fs-5";
            }

            toast.classList.add("show");
            setTimeout(() => {
                toast.classList.remove("show");
            }, 3800);
        }

        // Helper to get today's date in YYYY-MM-DD format
        function getTodayDateString() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        // Helper to get future date (+2 days default) in YYYY-MM-DD format for deadlines
        function getFutureDateString(daysAhead = 2) {
            const future = new Date();
            future.setDate(future.getDate() + daysAhead);
            const yyyy = future.getFullYear();
            const mm = String(future.getMonth() + 1).padStart(2, '0');
            const dd = String(future.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        // Redundant FAB toggle logic removed
    </script>

    <!-- ══════════════════════════════════════════════════════════
         INTERACTIVE VISUAL CLOCK TIME PICKER MODAL
         ══════════════════════════════════════════════════════════ -->
    <div class="modal fade clock-picker-modal" id="clockPickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content clock-modal-content">
                
                <!-- Modal Header / Selected Time Banner -->
                <div class="clock-modal-header">
                    <div class="clock-display-wrap">
                        <div class="clock-time-display">
                            <span class="clock-num-box active" id="clockHourBox" onclick="switchClockMode('hour')" title="Click to select hour">11</span>
                            <span class="clock-colon">:</span>
                            <span class="clock-num-box" id="clockMinBox" onclick="switchClockMode('minute')" title="Click to select minute">05</span>
                        </div>
                        <div class="clock-ampm-wrap">
                            <button type="button" class="clock-ampm-btn active" id="clockAmBtn" onclick="setClockAmPm('AM')">AM</button>
                            <button type="button" class="clock-ampm-btn" id="clockPmBtn" onclick="setClockAmPm('PM')">PM</button>
                        </div>
                    </div>
                    <div class="clock-mode-indicator" id="clockModeTitle">SELECT HOUR</div>
                </div>

                <!-- Modal Body (Interactive Circular Clock Dial) -->
                <div class="clock-modal-body text-center">
                    <div class="clock-dial-container" id="clockDialContainer">
                        <div class="clock-center-pin"></div>
                        <div class="clock-hand" id="clockHand">
                            <div class="clock-hand-head"></div>
                        </div>
                        <div class="clock-face-numbers" id="clockFaceNumbers">
                            <!-- Dynamically generated dial numbers -->
                        </div>
                    </div>

                    <!-- Quick Preset Pills -->
                    <div class="clock-presets-bar mt-3">
                        <span class="text-muted small me-1" style="font-family:'Share Tech', monospace; font-size:11px;">QUICK:</span>
                        <button type="button" class="btn clock-preset-btn" onclick="applyClockPreset('09:00 AM')">09:00 AM</button>
                        <button type="button" class="btn clock-preset-btn" onclick="applyClockPreset('10:30 AM')">10:30 AM</button>
                        <button type="button" class="btn clock-preset-btn" onclick="applyClockPreset('11:05 PM')">11:05 PM</button>
                        <button type="button" class="btn clock-preset-btn" onclick="applyClockPreset('02:00 PM')">02:00 PM</button>
                        <button type="button" class="btn clock-preset-btn" onclick="applyClockPreset('04:30 PM')">04:30 PM</button>
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="clock-modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill text-light border-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 rounded-pill shadow-sm" onclick="confirmClockSelection()">Set Time <i class="bi bi-check2 ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         INTERACTIVE VISUAL CLOCK TIME PICKER SCRIPT
         ══════════════════════════════════════════════════════════ -->
    <script>
        let clockState = {
            targetInputId: null,
            hour: 11,
            minute: 5,
            ampm: 'PM',
            mode: 'hour', // 'hour' or 'minute'
            isDragging: false,
            bsModal: null
        };

        function openClockPicker(inputId) {
            clockState.targetInputId = inputId;
            const inputEl = document.getElementById(inputId);
            const existingVal = inputEl ? inputEl.value.trim() : '';

            if (existingVal) {
                const match = existingVal.match(/^(\d{1,2}):(\d{2})(?:\s*(AM|PM))?/i);
                if (match) {
                    let parsedH = parseInt(match[1], 10);
                    let parsedM = parseInt(match[2], 10);
                    let parsedAmPm = match[3] ? match[3].toUpperCase() : null;

                    if (!parsedAmPm) {
                        if (parsedH >= 12) {
                            parsedAmPm = 'PM';
                            if (parsedH > 12) parsedH -= 12;
                        } else {
                            parsedAmPm = 'AM';
                            if (parsedH === 0) parsedH = 12;
                        }
                    }
                    clockState.hour = parsedH >= 1 && parsedH <= 12 ? parsedH : 11;
                    clockState.minute = parsedM >= 0 && parsedM <= 59 ? parsedM : 5;
                    clockState.ampm = parsedAmPm || 'PM';
                }
            } else {
                clockState.hour = 11;
                clockState.minute = 5;
                clockState.ampm = 'PM';
            }

            clockState.mode = 'hour';

            const modalEl = document.getElementById('clockPickerModal');
            if (!clockState.bsModal) {
                clockState.bsModal = new bootstrap.Modal(modalEl);
                initClockDialEvents();

                if (modalEl) {
                    modalEl.addEventListener('shown.bs.modal', () => {
                        renderClockFace();
                    });
                    window.addEventListener('resize', () => {
                        if (modalEl.classList.contains('show')) {
                            renderClockFace();
                        }
                    });
                }
            }
            clockState.bsModal.show();
            renderClockFace();
            setTimeout(renderClockFace, 60);
            setTimeout(renderClockFace, 200);
        }

        function switchClockMode(mode) {
            clockState.mode = mode;
            renderClockFace();
        }

        function setClockAmPm(ampm) {
            clockState.ampm = ampm;
            renderClockFace();
        }

        function renderClockFace() {
            const hourBox = document.getElementById('clockHourBox');
            const minBox = document.getElementById('clockMinBox');
            const amBtn = document.getElementById('clockAmBtn');
            const pmBtn = document.getElementById('clockPmBtn');
            const modeTitle = document.getElementById('clockModeTitle');
            const faceNumbers = document.getElementById('clockFaceNumbers');
            const hand = document.getElementById('clockHand');
            const container = document.getElementById('clockDialContainer');

            if (!hourBox || !minBox) return;

            hourBox.innerText = String(clockState.hour).padStart(2, '0');
            minBox.innerText = String(clockState.minute).padStart(2, '0');

            if (clockState.mode === 'hour') {
                hourBox.classList.add('active');
                minBox.classList.remove('active');
                modeTitle.innerText = 'SELECT HOUR';
            } else {
                minBox.classList.add('active');
                hourBox.classList.remove('active');
                modeTitle.innerText = 'SELECT MINUTE';
            }

            if (clockState.ampm === 'AM') {
                amBtn.classList.add('active');
                pmBtn.classList.remove('active');
            } else {
                pmBtn.classList.add('active');
                amBtn.classList.remove('active');
            }

            faceNumbers.innerHTML = '';

            let containerW = 250;
            let containerH = 250;
            if (container) {
                const rect = container.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    containerW = rect.width;
                    containerH = rect.height;
                } else if (container.clientWidth > 0 && container.clientHeight > 0) {
                    containerW = container.clientWidth;
                    containerH = container.clientHeight;
                }
            }

            const centerX = containerW / 2;
            const centerY = containerH / 2;
            const radius = Math.min(centerX, centerY) - 30;

            if (clockState.mode === 'hour') {
                for (let h = 1; h <= 12; h++) {
                    const numEl = document.createElement('div');
                    numEl.className = 'clock-number' + (h === clockState.hour ? ' active' : '');
                    numEl.innerText = h;

                    const angleDeg = (h % 12) * 30;
                    const angleRad = (angleDeg - 90) * (Math.PI / 180);
                    const x = centerX + radius * Math.cos(angleRad);
                    const y = centerY + radius * Math.sin(angleRad);

                    numEl.style.left = `${x}px`;
                    numEl.style.top = `${y}px`;

                    numEl.onclick = (e) => {
                        e.stopPropagation();
                        clockState.hour = h;
                        renderClockFace();
                        setTimeout(() => switchClockMode('minute'), 200);
                    };

                    faceNumbers.appendChild(numEl);
                }

                const handAngle = (clockState.hour % 12) * 30;
                hand.style.transform = `rotate(${handAngle}deg)`;
            } else {
                for (let m = 0; m < 60; m += 5) {
                    const numEl = document.createElement('div');
                    const isClosest = Math.round(clockState.minute / 5) * 5 % 60 === m;
                    numEl.className = 'clock-number' + (isClosest ? ' active' : '');
                    numEl.innerText = String(m).padStart(2, '0');

                    const angleDeg = m * 6;
                    const angleRad = (angleDeg - 90) * (Math.PI / 180);
                    const x = centerX + radius * Math.cos(angleRad);
                    const y = centerY + radius * Math.sin(angleRad);

                    numEl.style.left = `${x}px`;
                    numEl.style.top = `${y}px`;

                    numEl.onclick = (e) => {
                        e.stopPropagation();
                        clockState.minute = m;
                        renderClockFace();
                    };

                    faceNumbers.appendChild(numEl);
                }

                const handAngle = clockState.minute * 6;
                hand.style.transform = `rotate(${handAngle}deg)`;
            }
        }

        function initClockDialEvents() {
            const container = document.getElementById('clockDialContainer');
            if (!container) return;

            const handlePointer = (e) => {
                const rect = container.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;

                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;

                const dx = clientX - cx;
                const dy = clientY - cy;

                let angleRad = Math.atan2(dy, dx) + Math.PI / 2;
                if (angleRad < 0) angleRad += 2 * Math.PI;

                let deg = angleRad * (180 / Math.PI);

                if (clockState.mode === 'hour') {
                    let h = Math.round(deg / 30);
                    if (h === 0) h = 12;
                    clockState.hour = h;
                } else {
                    let m = Math.round(deg / 6) % 60;
                    clockState.minute = m;
                }
                renderClockFace();
            };

            container.addEventListener('pointerdown', (e) => {
                clockState.isDragging = true;
                handlePointer(e);
            });

            window.addEventListener('pointermove', (e) => {
                if (clockState.isDragging) {
                    handlePointer(e);
                }
            });

            window.addEventListener('pointerup', () => {
                if (clockState.isDragging) {
                    clockState.isDragging = false;
                    if (clockState.mode === 'hour') {
                        setTimeout(() => switchClockMode('minute'), 200);
                    }
                }
            });
        }

        function applyClockPreset(presetStr) {
            const match = presetStr.match(/^(\d{2}):(\d{2})\s*(AM|PM)$/i);
            if (match) {
                clockState.hour = parseInt(match[1], 10);
                clockState.minute = parseInt(match[2], 10);
                clockState.ampm = match[3].toUpperCase();
                renderClockFace();
            }
        }

        function confirmClockSelection() {
            if (!clockState.targetInputId) return;

            const formattedH = String(clockState.hour).padStart(2, '0');
            const formattedM = String(clockState.minute).padStart(2, '0');
            const resultStr = `${formattedH}:${formattedM} ${clockState.ampm}`;

            const inputEl = document.getElementById(clockState.targetInputId);
            if (inputEl) {
                inputEl.value = resultStr;
                inputEl.classList.remove('is-invalid');
                inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                inputEl.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (clockState.bsModal) {
                clockState.bsModal.hide();
            }
        }
    </script>

    <!-- ══════════════════════════════════════════════════════════
         INTERACTIVE VISUAL DATE PICKER MODAL
         ══════════════════════════════════════════════════════════ -->
    <div class="modal fade date-picker-modal" id="datePickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content date-modal-content">
                
                <!-- Header Banner -->
                <div class="date-modal-header">
                    <div class="date-year-badge" id="dateHeaderYear">2026</div>
                    <div class="date-selected-title" id="dateHeaderFull">Tue, 28 Jul</div>
                </div>

                <!-- Month/Year Navigation -->
                <div class="date-nav-bar">
                    <button type="button" class="date-nav-btn" onclick="navMonth(-1)" title="Previous Month">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="date-month-title" id="dateMonthTitle">July 2026</div>
                    <button type="button" class="date-nav-btn" onclick="navMonth(1)" title="Next Month">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <!-- Body Grid -->
                <div class="date-modal-body">
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Day headers & dates rendered via JS -->
                    </div>

                    <!-- Presets -->
                    <div class="date-presets-bar">
                        <span class="text-muted small me-1" style="font-family:'Share Tech', monospace; font-size:11px;">QUICK:</span>
                        <button type="button" class="btn date-preset-btn" onclick="applyDatePreset('today')">Today</button>
                        <button type="button" class="btn date-preset-btn" onclick="applyDatePreset('tomorrow')">Tomorrow</button>
                        <button type="button" class="btn date-preset-btn" onclick="applyDatePreset('next_monday')">Next Monday</button>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="date-modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill text-light border-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm px-4 rounded-pill shadow-sm" onclick="confirmDateSelection()">Set Date <i class="bi bi-check2 ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         INTERACTIVE VISUAL DATE PICKER SCRIPT
         ══════════════════════════════════════════════════════════ -->
    <script>
        let dateState = {
            targetInputId: null,
            viewYear: 2026,
            viewMonth: 6,
            selectedYear: 2026,
            selectedMonth: 6,
            selectedDay: 28,
            bsModal: null
        };

        const MONTH_NAMES = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        const DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        function openDatePicker(inputId) {
            dateState.targetInputId = inputId;
            const inputEl = document.getElementById(inputId);
            const existingVal = inputEl ? inputEl.value.trim() : '';

            let d = new Date();
            if (existingVal) {
                let match = existingVal.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                if (match) {
                    d = new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]));
                } else {
                    match = existingVal.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                    if (match) {
                        d = new Date(parseInt(match[3]), parseInt(match[2]) - 1, parseInt(match[1]));
                    }
                }
            }

            if (isNaN(d.getTime())) d = new Date();

            dateState.selectedYear = d.getFullYear();
            dateState.selectedMonth = d.getMonth();
            dateState.selectedDay = d.getDate();

            dateState.viewYear = dateState.selectedYear;
            dateState.viewMonth = dateState.selectedMonth;

            renderCalendar();

            const modalEl = document.getElementById('datePickerModal');
            if (!dateState.bsModal) {
                dateState.bsModal = new bootstrap.Modal(modalEl);
            }
            dateState.bsModal.show();
        }

        function navMonth(delta) {
            dateState.viewMonth += delta;
            if (dateState.viewMonth > 11) {
                dateState.viewMonth = 0;
                dateState.viewYear++;
            } else if (dateState.viewMonth < 0) {
                dateState.viewMonth = 11;
                dateState.viewYear--;
            }
            renderCalendar();
        }

        function renderCalendar() {
            const headerYear = document.getElementById('dateHeaderYear');
            const headerFull = document.getElementById('dateHeaderFull');
            const monthTitle = document.getElementById('dateMonthTitle');
            const calendarGrid = document.getElementById('calendarGrid');

            if (!calendarGrid) return;

            const selDateObj = new Date(dateState.selectedYear, dateState.selectedMonth, dateState.selectedDay);
            const dayName = DAY_NAMES[selDateObj.getDay()];
            const monthShort = MONTH_NAMES[dateState.selectedMonth].slice(0, 3);
            
            headerYear.innerText = dateState.selectedYear;
            headerFull.innerText = `${dayName}, ${dateState.selectedDay} ${monthShort}`;
            monthTitle.innerText = `${MONTH_NAMES[dateState.viewMonth]} ${dateState.viewYear}`;

            calendarGrid.innerHTML = '';

            const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
            weekdays.forEach(w => {
                const wEl = document.createElement('div');
                wEl.className = 'calendar-weekday';
                wEl.innerText = w;
                calendarGrid.appendChild(wEl);
            });

            const firstDayIndex = new Date(dateState.viewYear, dateState.viewMonth, 1).getDay();
            const daysInMonth = new Date(dateState.viewYear, dateState.viewMonth + 1, 0).getDate();
            const daysInPrevMonth = new Date(dateState.viewYear, dateState.viewMonth, 0).getDate();

            const today = new Date();

            for (let i = firstDayIndex - 1; i >= 0; i--) {
                const prevDay = daysInPrevMonth - i;
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerText = prevDay;
                cell.onclick = () => {
                    navMonth(-1);
                    selectDate(dateState.viewYear, dateState.viewMonth, prevDay);
                };
                calendarGrid.appendChild(cell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('div');
                let classes = 'calendar-day-cell';

                if (dateState.viewYear === today.getFullYear() &&
                    dateState.viewMonth === today.getMonth() &&
                    day === today.getDate()) {
                    classes += ' today';
                }

                if (dateState.viewYear === dateState.selectedYear &&
                    dateState.viewMonth === dateState.selectedMonth &&
                    day === dateState.selectedDay) {
                    classes += ' active';
                }

                cell.className = classes;
                cell.innerText = day;
                cell.onclick = () => {
                    selectDate(dateState.viewYear, dateState.viewMonth, day);
                };

                calendarGrid.appendChild(cell);
            }

            const totalCells = firstDayIndex + daysInMonth;
            const remainingCells = (totalCells > 35 ? 42 : 35) - totalCells;
            for (let day = 1; day <= remainingCells; day++) {
                const cell = document.createElement('div');
                cell.className = 'calendar-day-cell other-month';
                cell.innerText = day;
                cell.onclick = () => {
                    navMonth(1);
                    selectDate(dateState.viewYear, dateState.viewMonth, day);
                };
                calendarGrid.appendChild(cell);
            }
        }

        function selectDate(y, m, d) {
            dateState.selectedYear = y;
            dateState.selectedMonth = m;
            dateState.selectedDay = d;
            renderCalendar();
        }

        function applyDatePreset(preset) {
            const now = new Date();
            if (preset === 'today') {
                selectDate(now.getFullYear(), now.getMonth(), now.getDate());
            } else if (preset === 'tomorrow') {
                const tom = new Date(now);
                tom.setDate(now.getDate() + 1);
                selectDate(tom.getFullYear(), tom.getMonth(), tom.getDate());
            } else if (preset === 'next_monday') {
                const nextMon = new Date(now);
                const dayOfWeek = nextMon.getDay();
                const daysUntilMon = (8 - dayOfWeek) % 7 || 7;
                nextMon.setDate(now.getDate() + daysUntilMon);
                selectDate(nextMon.getFullYear(), nextMon.getMonth(), nextMon.getDate());
            }
            dateState.viewYear = dateState.selectedYear;
            dateState.viewMonth = dateState.selectedMonth;
            renderCalendar();
        }

        function confirmDateSelection() {
            if (!dateState.targetInputId) return;

            const yyyy = dateState.selectedYear;
            const mm = String(dateState.selectedMonth + 1).padStart(2, '0');
            const dd = String(dateState.selectedDay).padStart(2, '0');
            const resultStr = `${yyyy}-${mm}-${dd}`;

            const inputEl = document.getElementById(dateState.targetInputId);
            if (inputEl) {
                inputEl.value = resultStr;
                inputEl.classList.remove('is-invalid');
                inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                inputEl.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (dateState.bsModal) {
                dateState.bsModal.hide();
            }
        }
    </script>
</body>

</html>
</body>

</html>