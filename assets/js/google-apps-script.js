/**
 * ══════════════════════════════════════════════════════════════
 *  GMIU IT Department — Google Sheets Auto-Fill Script
 *  Paste this ENTIRE file's content into:
 *  Google Sheet → Extensions → Apps Script → Code.gs
 * ══════════════════════════════════════════════════════════════
 *
 *  DEPLOYMENT STEPS:
 *  1. Paste code into Apps Script editor
 *  2. Save (Ctrl+S)
 *  3. Click "Deploy" → "New deployment"
 *  4. Select type: Web App
 *  5. Execute as: Me
 *  6. Who has access: Anyone
 *  7. Click Deploy → Authorize → Deploy
 *  8. Copy the Web App URL
 *  9. Paste it into assets/js/sheetsConfig.js as WEBAPP_URL
 * ══════════════════════════════════════════════════════════════
 */

// ── Column header row (written automatically on first run) ──
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
  "Brief Objective",
  "Google Drive Link",
  "Status"
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
 * Handle POST requests from the report form
 */
function doPost(e) {
  try {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName("Sheet1") || ss.getSheets()[0];

    // ── Auto-create header row if sheet is empty ──
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(HEADERS);

      // Style the header row
      const headerRange = sheet.getRange(1, 1, 1, HEADERS.length);
      headerRange.setBackground("#1a237e");
      headerRange.setFontColor("#ffffff");
      headerRange.setFontWeight("bold");
      headerRange.setFontSize(10);
      headerRange.setHorizontalAlignment("center");
      sheet.setFrozenRows(1);

      // Auto-resize all columns
      sheet.autoResizeColumns(1, HEADERS.length);
    }

    // ── Parse incoming JSON payload ──
    let data = {};
    if (e && e.postData && e.postData.contents) {
      data = JSON.parse(e.postData.contents);
    }

    const now = new Date();
    const submittedOn = Utilities.formatDate(now, Session.getScriptTimeZone(), "dd/MM/yyyy HH:mm:ss");
    const srNo = sheet.getLastRow(); // Row count (excluding header = Sr. No.)

    // ── Build the new row ──
    const newRow = [
      srNo,                                        // A: Sr. No.
      submittedOn,                                 // B: Submitted On
      data.facultyName      || "-",                // C: Faculty Name
      data.facultyEmail     || "-",                // D: Faculty Email
      data.academicYear     || "-",                // E: Academic Year
      data.reportType       || "-",                // F: Report Type
      data.reportTitle      || "-",                // G: Report Title
      data.activityDate     || "-",                // H: Activity Date
      data.startTime        || "-",                // I: Start Time
      data.endTime          || "-",                // J: End Time
      data.venue            || "-",                // K: Venue
      data.programmes       || "-",                // L: Programme(s)
      data.semester         || "-",                // M: Semester
      data.division         || "-",                // N: Division/Class
      data.participants     || "-",                // O: No. of Participants
      data.coordinators     || "-",                // P: Faculty Coordinator(s)
      data.objective        || "-",                // Q: Brief Objective
      data.driveLink        || "-",                // R: Google Drive Link
      "Pending"                                    // S: Status
    ];

    sheet.appendRow(newRow);

    // ── Style the new data row ──
    const lastRow = sheet.getLastRow();
    const dataRange = sheet.getRange(lastRow, 1, 1, HEADERS.length);

    // Alternate row shading
    if (lastRow % 2 === 0) {
      dataRange.setBackground("#e8eaf6");
    } else {
      dataRange.setBackground("#ffffff");
    }
    dataRange.setVerticalAlignment("middle");
    dataRange.setWrap(true);

    // Highlight the Sr. No. column
    sheet.getRange(lastRow, 1).setFontWeight("bold").setHorizontalAlignment("center");

    // Add dropdown validation for Status column (column S)
    const statusCell = sheet.getRange(lastRow, HEADERS.length);
    const rule = SpreadsheetApp.newDataValidation()
      .requireValueInList(["Pending", "In Process", "Complete"], true)
      .setAllowInvalid(false)
      .build();
    statusCell.setDataValidation(rule);

    // Auto-resize columns after data entry (optional, can be slow on large sheets)
    // sheet.autoResizeColumns(1, HEADERS.length);

    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: "Report data saved to Google Sheet.",
        row: lastRow,
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
