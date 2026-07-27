/**
 * ══════════════════════════════════════════════════════════════
 *  GMIU IT Department — Google Sheets Auto-Fill Script
 *  Paste this ENTIRE file's content into:
 *  Google Sheet → Extensions → Apps Script → Code.gs
 * ══════════════════════════════════════════════════════════════
 */

// ── Column header row ──
const HEADERS = [
  "Sr. No.",
  "Submitted On",
  "Faculty Name",
  "Faculty Email",
  "Academic Year",
  "Report Type",
  "Report Title",
  "Activity Date",
  "Start Time",
  "End Time",
  "Venue",
  "Programme(s)",
  "Semester",
  "Division / Class",
  "No. of Participants",
  "Faculty Coordinator(s)",
  "Google Drive Link",
  "Batch",
  "Student Coordinator",
  "Publish on Website",
  "Press Note",
  "Placement Activity Type",
  "Activity Details",
  "Status",
  "Deadline"
];

/**
 * Handle GET requests (used for CORS preflight & testing)
 */
function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: "GMIU IT Sheets API is running." }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Initialize sheet layout according to user design
 */
function initializeSheet() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName("2026-27 ODD") || ss.getSheetByName("Sheet1") || ss.getSheets()[0];
  sheet.setName("2026-27 ODD");

  sheet.clear();

  // Set custom row heights
  sheet.setRowHeight(1, 35); // Title row height
  sheet.setRowHeight(2, 28); // Subtitle row height
  sheet.setRowHeight(3, 20); // Blank row
  sheet.setRowHeight(4, 25); // Header row height
  sheet.setRowHeight(5, 20); // Blank row

  // 1. Title Row (Row 1)
  sheet.getRange("A1").setValue("Gyanmanjari Innovative University");
  sheet.getRange("A1:Y1").merge();
  const titleRange = sheet.getRange("A1:Y1");
  titleRange.setFontWeight("bold");
  titleRange.setFontSize(14);
  titleRange.setHorizontalAlignment("center");
  titleRange.setVerticalAlignment("middle");
  titleRange.setBorder(true, true, true, true, false, false, "#000000", SpreadsheetApp.BorderStyle.SOLID_MEDIUM);

  // 2. Subtitle Row (Row 2)
  sheet.getRange("A2").setValue("Department Of Information Technology");
  sheet.getRange("A2:Y2").merge();
  const subtitleRange = sheet.getRange("A2:Y2");
  subtitleRange.setFontWeight("bold");
  subtitleRange.setFontSize(12);
  subtitleRange.setHorizontalAlignment("center");
  subtitleRange.setVerticalAlignment("middle");
  subtitleRange.setBorder(true, true, true, true, false, false, "#000000", SpreadsheetApp.BorderStyle.SOLID_MEDIUM);

  // 3. Row 3 is a blank row

  // 4. Header Row (Row 4)
  const headerRange = sheet.getRange(4, 1, 1, HEADERS.length);
  headerRange.setValues([HEADERS]);
  headerRange.setBackground("#1a237e");
  headerRange.setFontColor("#ffffff");
  headerRange.setFontWeight("bold");
  headerRange.setFontSize(10);
  headerRange.setHorizontalAlignment("center");
  headerRange.setVerticalAlignment("middle");
  headerRange.setBorder(true, true, true, true, true, true, "#000000", SpreadsheetApp.BorderStyle.SOLID);
  sheet.setFrozenRows(4);

  // 5. Row 5 is a blank row

  // ── 6. Status Column Dropdown & Colors (Column X, Row 6 onwards) ──
  const statusRange = sheet.getRange("X6:X");

  // Set data validation dropdown
  const rule = SpreadsheetApp.newDataValidation()
    .requireValueInList(["Pending", "In Process", "Complete"], true)
    .setAllowInvalid(false)
    .build();
  statusRange.setDataValidation(rule);

  // Set conditional formatting colors
  const condRules = [];

  // Pending: background red, text white
  condRules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo("Pending")
    .setBackground("#ea4335")
    .setFontColor("#ffffff")
    .setRanges([statusRange])
    .build());

  // In Process: background yellow, text white
  condRules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo("In Process")
    .setBackground("#fbbc05")
    .setFontColor("#ffffff")
    .setRanges([statusRange])
    .build());

  // Complete: background green, text white
  condRules.push(SpreadsheetApp.newConditionalFormatRule()
    .whenTextEqualTo("Complete")
    .setBackground("#34a853")
    .setFontColor("#ffffff")
    .setRanges([statusRange])
    .build());

  sheet.setConditionalFormatRules(condRules);

  // Set format for Deadline Column (Column Y / 25)
  sheet.getRange("Y6:Y").setNumberFormat("dd/MM/yyyy hh:mm AM/PM");

  // Auto-resize all columns
  sheet.autoResizeColumns(1, HEADERS.length);
}

/**
 * Handle POST requests from the report form
 */
function doPost(e) {
  try {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName("2026-27 ODD") || ss.getSheetByName("Sheet1") || ss.getSheets()[0];
    sheet.setName("2026-27 ODD");

    // ── Auto-initialize layout if sheet is empty/cleared ──
    if (sheet.getLastRow() < 4) {
      initializeSheet();
    }

    // ── Parse incoming JSON payload ──
    let data = {};
    if (e && e.postData && e.postData.contents) {
      data = JSON.parse(e.postData.contents);
    }

    const now = new Date();
    const submittedOn = Utilities.formatDate(now, Session.getScriptTimeZone(), "dd/MM/yyyy HH:mm:ss");

    // Calculate target row and Sr. No. (leaving row 3 and 5 blank)
    let targetRow = sheet.getLastRow() + 1;
    if (targetRow < 6) {
      targetRow = 6;
    }
    const srNo = targetRow - 5;

    // ── Build the new row ──
    const newRow = [
      srNo,                                        // A: Sr. No.
      submittedOn,                                 // B: Submitted On
      data.facultyName || "-",                // C: Faculty Name
      data.facultyEmail || "-",                // D: Faculty Email
      data.academicYear || "-",                // E: Academic Year
      data.reportType || "-",                // F: Report Type
      data.reportTitle || "-",                // G: Report Title
      data.activityDate || "-",                // H: Activity Date
      data.startTime || "-",                // I: Start Time
      data.endTime || "-",                // J: End Time
      data.venue || "-",                // K: Venue
      data.programmes || "-",                // L: Programme(s)
      data.semester || "-",                // M: Semester
      data.division || "-",                // N: Division/Class
      data.participants || "-",                // O: No. of Participants
      data.coordinators || "-",                // P: Faculty Coordinator(s)
      data.driveLink || "-",                // Q: Google Drive Link
      data.batch || "-",                // R: Batch
      data.studentCoordinator || "-",              // S: Student Coordinator
      data.publishWebsite || "-",                // T: Publish on Website
      data.pressNote || "-",                // U: Press Note
      data.placementActType || "-",                // V: Placement Activity Type
      data.activityDetails || "-",                // W: Activity Details
      "Pending",                                   // X: Status
      data.deadline || "-"                         // Y: Deadline
    ];

    // Write the new row
    sheet.getRange(targetRow, 1, 1, newRow.length).setValues([newRow]);

    // ── Style the new data row ──
    const dataRange = sheet.getRange(targetRow, 1, 1, HEADERS.length);

    // Alternate row shading
    if (targetRow % 2 === 0) {
      dataRange.setBackground("#e8eaf6");
    } else {
      dataRange.setBackground("#ffffff");
    }
    dataRange.setVerticalAlignment("middle");
    dataRange.setWrap(true);
    dataRange.setBorder(true, true, true, true, true, true, "#dddddd", SpreadsheetApp.BorderStyle.SOLID);

    // Highlight the Sr. No. column
    sheet.getRange(targetRow, 1).setFontWeight("bold").setHorizontalAlignment("center");

    // Set format for Deadline cell (Column 25 / Y)
    sheet.getRange(targetRow, 25).setNumberFormat("dd/MM/yyyy hh:mm AM/PM");

    // ── Status Column Dropdown (Preserving colors by copying validation from R8 or row above) ──
    const statusCell = sheet.getRange(targetRow, 24);
    let templateRow = 8; // Row 8 has the user's custom colored dropdown validation

    // Check if the cell above is valid to use as template
    if (targetRow > 6) {
      templateRow = targetRow - 1;
    }

    try {
      const templateCell = sheet.getRange(templateRow, HEADERS.length);
      // Copy only data validation rule to keep color chip style
      templateCell.copyTo(statusCell, SpreadsheetApp.CopyPasteType.PASTE_DATA_VALIDATION, false);
    } catch (e) {
      // Fallback if template cell fails
      const rule = SpreadsheetApp.newDataValidation()
        .requireValueInList(["Pending", "In Process", "Complete"], true)
        .setAllowInvalid(false)
        .build();
      statusCell.setDataValidation(rule);
    }

    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: "Report data saved to Google Sheet.",
        row: targetRow,
        srNo: srNo
      }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        error: err.toString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Fix validation for existing rows (6, 7, 8, etc.) by copying the validation from R8 (which has colors)
 */
function fixValidationForExistingRows() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName("Sheet1") || ss.getSheets()[0];
  const lastRow = sheet.getLastRow();

  if (lastRow >= 6) {
    const templateCell = sheet.getRange(8, 24); // Cell X8 has your custom colored validation
    const targetRange = sheet.getRange(6, 24, lastRow - 5, 1); // X6 to X[lastRow]
    templateCell.copyTo(targetRange, SpreadsheetApp.CopyPasteType.PASTE_DATA_VALIDATION, false);
  }
}

