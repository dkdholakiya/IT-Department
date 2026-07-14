/**
 * ══════════════════════════════════════════════════════════════
 *  GMIU IT Department — Zero Student Google Sheets Auto-Fill Script
 *  Paste this ENTIRE file's content into:
 *  Google Sheet (Zero Student Sheet) → Extensions → Apps Script → Code.gs
 * 
 *  Spreadsheet URL: https://docs.google.com/spreadsheets/d/1OX9J8SC04wj3t9sgleYrbUR7EU8oNSoBZQ4tRjXU0RY/edit#gid=0
 * ══════════════════════════════════════════════════════════════
 */

// Column header row matching the design
const HEADERS = [
  "DATE",
  "DEPT",
  "CLAS",
  "SUBJECT",
  "FAC",
  "BRANCH",
  "SEM.",
  "TIME IN",
  "TIME OUT",
  "REMARKS"
];

/**
 * Handle GET requests (for API health check & CORS preflight)
 */
function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: "GMIU Zero Student Sheet API is running." }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Initialize sheet layout matching the template exactly
 */
function initializeZeroSheet() {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  const sheet = ss.getSheetByName("2026-27 ODD") || ss.getSheetByName("Sheet1") || ss.getSheets()[0];
  sheet.setName("2026-27 ODD");
  
  sheet.clear();
  
  // Set custom row heights
  sheet.setRowHeight(1, 20); // Spacer Row 1
  sheet.setRowHeight(2, 40); // Title Row 2
  sheet.setRowHeight(3, 20); // Spacer Row 3
  sheet.setRowHeight(4, 30); // Header Row 4
  
  // 1. Title Row (Row 2) - "Zero students report as per Time Table"
  sheet.getRange("A2").setValue("Zero students report as per Time Table");
  sheet.getRange("A2:J2").merge();
  const titleRange = sheet.getRange("A2:J2");
  titleRange.setFontFamily("Times New Roman");
  titleRange.setFontStyle("italic");
  titleRange.setFontWeight("bold");
  titleRange.setFontSize(18);
  titleRange.setHorizontalAlignment("center");
  titleRange.setVerticalAlignment("middle");

  // 2. Header Row (Row 4) - Purple backgrounds
  const headerRange = sheet.getRange(4, 1, 1, HEADERS.length);
  headerRange.setValues([HEADERS]);
  headerRange.setBackground("#9400d3"); // Vibrant deep purple header
  headerRange.setFontColor("#ffffff");
  headerRange.setFontWeight("bold");
  headerRange.setFontSize(11);
  headerRange.setFontFamily("Arial");
  headerRange.setHorizontalAlignment("center");
  headerRange.setVerticalAlignment("middle");
  headerRange.setBorder(true, true, true, true, true, true, "#000000", SpreadsheetApp.BorderStyle.SOLID);
  sheet.setFrozenRows(4);

  // Auto-resize columns
  sheet.autoResizeColumns(1, HEADERS.length);
}

/**
 * Handle POST requests from the Zero Student log form
 */
function doPost(e) {
  try {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName("2026-27 ODD") || ss.getSheetByName("Sheet1") || ss.getSheets()[0];
    sheet.setName("2026-27 ODD");

    // Auto-initialize layout if sheet is empty/cleared or if headers don't match the new design
    let headersMatch = true;
    try {
      const currentHeaders = sheet.getRange(4, 1, 1, HEADERS.length).getValues()[0];
      for (let i = 0; i < HEADERS.length; i++) {
        if (currentHeaders[i] !== HEADERS[i]) {
          headersMatch = false;
          break;
        }
      }
    } catch(err) {
      headersMatch = false;
    }

    if (sheet.getLastRow() < 4 || !headersMatch) {
      initializeZeroSheet();
    }

    // Parse incoming JSON payload
    let data = {};
    if (e && e.postData && e.postData.contents) {
      data = JSON.parse(e.postData.contents);
    }

    let targetRow = sheet.getLastRow() + 1;
    if (targetRow < 5) {
      targetRow = 5;
    }

    // Check for duplicate entry (same date, room, subject, faculty, timeIn, timeOut)
    const lastRow = sheet.getLastRow();
    let isDuplicate = false;
    
    if (lastRow >= 5) {
      const values = sheet.getRange(5, 1, lastRow - 4, 10).getValues();
      const newDate = (data.date || "").toString().trim().toUpperCase();
      const newRoom = (data.room || "").toString().trim().toUpperCase();
      const newSubject = (data.subject || "").toString().trim().toUpperCase();
      const newFaculty = (data.faculty || "").toString().trim().toUpperCase();
      const newTimeIn = (data.timeIn || "").toString().trim().toUpperCase();
      const newTimeOut = (data.timeOut || "").toString().trim().toUpperCase();
      
      for (let i = 0; i < values.length; i++) {
        const rowVal = values[i];
        const existingDate = rowVal[0].toString().trim().toUpperCase();
        const existingRoom = rowVal[2].toString().trim().toUpperCase();
        const existingSubject = rowVal[3].toString().trim().toUpperCase();
        const existingFaculty = rowVal[4].toString().trim().toUpperCase();
        const existingTimeIn = rowVal[7].toString().trim().toUpperCase();
        const existingTimeOut = rowVal[8].toString().trim().toUpperCase();
        
        if (existingDate === newDate &&
            existingRoom === newRoom &&
            existingSubject === newSubject &&
            existingFaculty === newFaculty &&
            existingTimeIn === newTimeIn &&
            existingTimeOut === newTimeOut) {
          isDuplicate = true;
          break;
        }
      }
    }

    if (isDuplicate) {
      return ContentService
        .createTextOutput(JSON.stringify({
          success: true,
          duplicate: true,
          message: "Duplicate entry. Record already exists in Google Sheet."
        }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    // Build the new row matching columns A-J
    const newRow = [
      data.date      || "-", // A: DATE
      data.dept      || "-", // B: DEPT
      data.room      || "-", // C: CLAS
      data.subject   || "-", // D: SUBJECT
      data.faculty   || "-", // E: FAC
      data.branch    || "-", // F: BRANCH
      data.semester  || "-", // G: SEM.
      data.timeIn    || "-", // H: TIME IN
      data.timeOut   || "-", // I: TIME OUT
      data.remarks   || "NO STUDENT" // J: REMARKS
    ];

    // Write row to sheet
    sheet.getRange(targetRow, 1, 1, newRow.length).setValues([newRow]);

    // Format new data row
    const rowRange = sheet.getRange(targetRow, 1, 1, HEADERS.length);
    rowRange.setFontFamily("Arial");
    rowRange.setFontSize(10);
    rowRange.setVerticalAlignment("middle");
    rowRange.setHorizontalAlignment("center");
    rowRange.setBorder(true, true, true, true, true, true, "#cccccc", SpreadsheetApp.BorderStyle.SOLID);

    // Left align BRANCH (Column F)
    sheet.getRange(targetRow, 6).setHorizontalAlignment("left");

    // Highlight REMARKS cell with light blue background matching the image
    const remarksCell = sheet.getRange(targetRow, 10);
    remarksCell.setBackground("#a4c2f4");
    remarksCell.setFontColor("#000000");
    remarksCell.setFontWeight("bold");

    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: "Zero Student Log saved successfully to Google Sheet.",
        row: targetRow
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
