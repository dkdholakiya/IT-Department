/**
 * ══════════════════════════════════════════════════════════════
 *  GMIU IT Department — Zero Student Google Sheets Auto-Fill Script
 *  Paste this ENTIRE file's content into:
 *  Google Sheet (Zero Student Sheet) → Extensions → Apps Script → Code.gs
 * 
 *  Spreadsheet URL: https://docs.google.com/spreadsheets/d/1OX9J8SC04wj3t9sgleYrbUR7EU8oNSoBZQ4tRjXU0RY/edit#gid=0
 * ══════════════════════════════════════════════════════════════
 */

// Primary 10-Column Header design matching user layout
const HEADERS_10 = [
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

// Extended 14-Column Header layout for backward compatibility
const HEADERS_14 = [
  "DATE",
  "SR NO",
  "CLASS/LAB",
  "SUBJECT",
  "FACULTY",
  "ALTERATION",
  "P/T",
  "BRANCH",
  "SEM.",
  "TIME IN",
  "TIME OUT",
  "REMARKS",
  "NO OF STUDENTS",
  "REMARK (Academic Convener)"
];

// Default headers array
const HEADERS = HEADERS_10;

/**
 * Handle GET requests (for API health check & CORS preflight)
 */
function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: "GMIU Zero Student Sheet API is running." }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Initialize sheet layout matching the template (defaults to 10-column design)
 */
function initializeZeroSheet(headersToUse) {
  const activeHeaders = headersToUse || HEADERS_10;
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
  const titleText = (activeHeaders.length === 14)
    ? "(ENG.) Lobby super-vision late time faculty report and remarks (2025-26)"
    : "Zero students report as per Time Table";
    
  sheet.getRange(2, 1).setValue(titleText);
  sheet.getRange(2, 1, 1, activeHeaders.length).merge();
  const titleRange = sheet.getRange(2, 1, 1, activeHeaders.length);
  titleRange.setFontFamily("Times New Roman");
  titleRange.setFontStyle("italic");
  titleRange.setFontWeight("bold");
  titleRange.setFontSize(18);
  titleRange.setHorizontalAlignment("center");
  titleRange.setVerticalAlignment("middle");

  // 2. Header Row (Row 4) - Purple backgrounds
  const headerRange = sheet.getRange(4, 1, 1, activeHeaders.length);
  headerRange.setValues([activeHeaders]);
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
  sheet.autoResizeColumns(1, activeHeaders.length);
}

/**
 * Handle POST requests from the Zero Student log form
 */
function doPost(e) {
  try {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = ss.getSheetByName("2026-27 ODD") || ss.getSheetByName("Sheet1") || ss.getSheets()[0];
    sheet.setName("2026-27 ODD");

    // Auto-detect existing sheet column layout (10-column vs 14-column)
    let currentHeaders = [];
    let is14Column = false;
    let headersValid = true;

    try {
      if (sheet.getLastRow() >= 4) {
        currentHeaders = sheet.getRange(4, 1, 1, 14).getValues()[0].map(h => (h || "").toString().trim().toUpperCase());
        if (currentHeaders.indexOf("REMARK (ACADEMIC CONVENER)") !== -1 || currentHeaders.length >= 14 && currentHeaders[13] !== "") {
          is14Column = true;
        }
      }
    } catch(err) {
      headersValid = false;
    }

    const activeHeaders = is14Column ? HEADERS_14 : HEADERS_10;

    if (sheet.getLastRow() < 4 || !headersValid) {
      initializeZeroSheet(HEADERS_10);
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

    // Column mapping indices based on layout
    // 10-col: DATE(0), DEPT(1), CLAS(2), SUBJECT(3), FAC(4), BRANCH(5), SEM(6), TIME IN(7), TIME OUT(8), REMARKS(9)
    // 14-col: DATE(0), SR NO(1), CLASS/LAB(2), SUBJECT(3), FACULTY(4), ALT(5), PT(6), BRANCH(7), SEM(8), TIME IN(9), TIME OUT(10), REMARKS(11), STU(12), CONVENER(13)
    const idxDate = 0;
    const idxRoom = 2;
    const idxSubject = 3;
    const idxFaculty = 4;
    const idxTimeIn = is14Column ? 9 : 7;
    const idxTimeOut = is14Column ? 10 : 8;
    const idxBranch = is14Column ? 7 : 5;
    const idxRemarks = is14Column ? 11 : 9;

    // Check for duplicate entry (same date, room, subject, faculty, timeIn, timeOut)
    const lastRow = sheet.getLastRow();
    let isDuplicate = false;
    
    if (lastRow >= 5) {
      const colCount = is14Column ? 14 : 10;
      const displayValues = sheet.getRange(5, 1, lastRow - 4, colCount).getDisplayValues();
      const rawValues = sheet.getRange(5, 1, lastRow - 4, colCount).getValues();
      
      const newIsoDate = parseToIsoDate(data.date);
      const newRoom = cleanRoom(data.room);
      const newSubject = (data.subject || "").toString().trim().toUpperCase().replace(/[\s\-]/g, "");
      const newFaculty = (data.faculty || "").toString().trim().toUpperCase();
      const newTimeInMin = parseTimeToMin(data.timeIn);
      const newTimeOutMin = parseTimeToMin(data.timeOut);
      
      for (let i = 0; i < displayValues.length; i++) {
        const rowDisp = displayValues[i];
        const rowRaw = rawValues[i];
        
        const existIsoDate = parseToIsoDate(rowDisp[idxDate] || rowRaw[idxDate]);
        const existRoom = cleanRoom(rowDisp[idxRoom]);
        const existSubject = (rowDisp[idxSubject] || "").toString().trim().toUpperCase().replace(/[\s\-]/g, "");
        const existFaculty = (rowDisp[idxFaculty] || "").toString().trim().toUpperCase();
        const existTimeInMin = parseTimeToMin(rowDisp[idxTimeIn] || rowRaw[idxTimeIn]);
        const existTimeOutMin = parseTimeToMin(rowDisp[idxTimeOut] || rowRaw[idxTimeOut]);
        
        const dateMatches = (existIsoDate && newIsoDate) ? (existIsoDate === newIsoDate) : true;
        const roomMatches = (existRoom === newRoom);
        const subjectMatches = (existSubject === newSubject);
        const facultyMatches = (existFaculty === newFaculty);
        
        const timeInMatches = (newTimeInMin !== null && existTimeInMin !== null) ? (newTimeInMin === existTimeInMin) : true;
        const timeOutMatches = (newTimeOutMin !== null && existTimeOutMin !== null) ? (newTimeOutMin === existTimeOutMin) : true;
        
        if (dateMatches && roomMatches && subjectMatches && facultyMatches && timeInMatches && timeOutMatches) {
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

    // Build the new row matching column layout
    let newRow = [];
    if (is14Column) {
      newRow = [
        data.date         || "-", // A: DATE
        data.srNo         || "-", // B: SR NO
        data.room         || "-", // C: CLASS/LAB
        data.subject      || "-", // D: SUBJECT
        data.faculty      || "-", // E: FACULTY
        data.alteration   || "---", // F: ALTERATION
        data.pt           || "---", // G: P/T
        data.branch       || "-", // H: BRANCH
        data.semester     || "-", // I: SEM.
        data.timeIn       || "-", // J: TIME IN
        data.timeOut      || "-", // K: TIME OUT
        data.remarks      || "NO STUDENT", // L: REMARKS
        data.noOfStudents || "---", // M: NO OF STUDENTS
        data.convenerRemark || "---" // N: REMARK (Academic Convener)
      ];
    } else {
      newRow = [
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
    }

    // Write row to sheet
    sheet.getRange(targetRow, 1, 1, newRow.length).setValues([newRow]);

    // Format new data row
    const rowRange = sheet.getRange(targetRow, 1, 1, activeHeaders.length);
    rowRange.setFontFamily("Arial");
    rowRange.setFontSize(10);
    rowRange.setVerticalAlignment("middle");
    rowRange.setHorizontalAlignment("center");
    rowRange.setBorder(true, true, true, true, true, true, "#cccccc", SpreadsheetApp.BorderStyle.SOLID);

    // Left align BRANCH column
    sheet.getRange(targetRow, idxBranch + 1).setHorizontalAlignment("left");

    // Highlight REMARKS cell with light blue background
    const remarksCell = sheet.getRange(targetRow, idxRemarks + 1);
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

// ── Helper functions for robust duplicate checking ──
function parseToIsoDate(dStr) {
  if (!dStr) return "";
  var s = dStr.toString().trim();
  var months = {"JAN":1,"FEB":2,"MAR":3,"APR":4,"MAY":5,"JUN":6,"JUL":7,"AUG":8,"SEP":9,"OCT":10,"NOV":11,"DEC":12};
  
  var m1 = s.match(/^(\d{1,2})[\/\-\s]+([A-Za-z]{3})[\/\-\s]+(\d{4})$/);
  if (m1) {
    var day = parseInt(m1[1], 10);
    var mon = months[m1[2].toUpperCase()];
    var yr = parseInt(m1[3], 10);
    if (mon) return yr + "-" + (mon < 10 ? "0" + mon : mon) + "-" + (day < 10 ? "0" + day : day);
  }
  
  var m2 = s.match(/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/);
  if (m2) {
    var yr = parseInt(m2[1], 10);
    var mon = parseInt(m2[2], 10);
    var day = parseInt(m2[3], 10);
    return yr + "-" + (mon < 10 ? "0" + mon : mon) + "-" + (day < 10 ? "0" + day : day);
  }

  var m3 = s.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
  if (m3) {
    var p1 = parseInt(m3[1], 10);
    var p2 = parseInt(m3[2], 10);
    var yr = parseInt(m3[3], 10);
    var day = p1 > 12 ? p1 : (p2 > 12 ? p1 : p2);
    var mon = p1 > 12 ? p2 : (p2 > 12 ? p1 : p2);
    return yr + "-" + (mon < 10 ? "0" + mon : mon) + "-" + (day < 10 ? "0" + day : day);
  }

  var d = new Date(s);
  if (!isNaN(d.getTime())) {
    var yr = d.getFullYear();
    var mon = d.getMonth() + 1;
    var day = d.getDate();
    return yr + "-" + (mon < 10 ? "0" + mon : mon) + "-" + (day < 10 ? "0" + day : day);
  }
  return s.toUpperCase().trim();
}

function parseTimeToMin(tStr) {
  if (!tStr) return null;
  var s = tStr.toString().trim().toLowerCase();
  var isPm = s.indexOf("pm") !== -1;
  var isAm = s.indexOf("am") !== -1;
  var clean = s.replace(/[^0-9:]/g, "");
  var parts = clean.split(":");
  if (parts.length < 2) return null;
  var hrs = parseInt(parts[0], 10);
  var mins = parseInt(parts[1], 10);
  if (isNaN(hrs) || isNaN(mins)) return null;
  if (isPm && hrs < 12) hrs += 12;
  if (isAm && hrs === 12) hrs = 0;
  if (!isPm && !isAm && hrs >= 1 && hrs <= 7) hrs += 12;
  return hrs * 60 + mins;
}

function cleanRoom(rStr) {
  if (!rStr) return "";
  return rStr.toString().toUpperCase().replace(/[\s\-]/g, "");
}

