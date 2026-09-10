/**
 * GMIU IT Department — Faculty Timetable Exporter Google Apps Script
 * 
 * INSTRUCTIONS:
 * 1. Open your Faculty Timetable Google Sheet -> Extensions -> Apps Script
 * 2. Delete ALL code in Code.gs and paste this code.
 * 3. Select 'authorizeScript' from top dropdown next to Debug, and click 'Run'.
 * 4. Click 'Review permissions' -> 'Advanced' -> 'Go to project (unsafe)' -> 'Allow'.
 * 5. Click Deploy -> Manage deployments -> Pencil (Edit) -> Version: New version -> Deploy.
 */

function doGet(e) {
  try {
    var sheetId = "12SGzk1LqNSN1DH8h0bSGg88JGSORMPO7V_QUKc65-ys";
    try {
      if (typeof SpreadsheetApp !== 'undefined' && SpreadsheetApp.getActiveSpreadsheet()) {
        sheetId = SpreadsheetApp.getActiveSpreadsheet().getId();
      }
    } catch(err) {}

    var token = ScriptApp.getOAuthToken();
    var response = null;

    // Method 1: Official Drive API v3 Export (Works on Domain/Restricted Sheets)
    try {
      var driveUrl = "https://www.googleapis.com/drive/v3/files/" + sheetId + "/export?mimeType=application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
      response = UrlFetchApp.fetch(driveUrl, {
        headers: {
          'Authorization': 'Bearer ' + token
        },
        muteHttpExceptions: true
      });
    } catch(err1) {}

    // Method 2: Direct Docs Export Fallback
    if (!response || response.getResponseCode() !== 200) {
      var docsUrl = "https://docs.google.com/spreadsheets/d/" + sheetId + "/export?format=xlsx";
      response = UrlFetchApp.fetch(docsUrl, {
        headers: {
          'Authorization': 'Bearer ' + token
        },
        muteHttpExceptions: true
      });
    }

    if (response.getResponseCode() !== 200) {
      return ContentService.createTextOutput(JSON.stringify({
        success: false,
        error: "Google export HTTP " + response.getResponseCode() + ": " + response.getContentText()
      })).setMimeType(ContentService.MimeType.JSON);
    }

    var bytes = response.getBlob().getBytes();
    var base64 = Utilities.base64Encode(bytes);

    return ContentService.createTextOutput(JSON.stringify({
      success: true,
      target: "faculty",
      base64: base64
    })).setMimeType(ContentService.MimeType.JSON);

  } catch(err) {
    return ContentService.createTextOutput(JSON.stringify({
      success: false,
      error: err.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

// Run this function ONCE in editor to approve UrlFetch & Drive permissions prompt
function authorizeScript() {
  DriveApp.getRootFolder();
  SpreadsheetApp.getActiveSpreadsheet();
  UrlFetchApp.fetch("https://docs.google.com");
}
