/**
 * ══════════════════════════════════════════════════════════════════════════════
 *  GMIU IT Department — Complete Email Sender Audit & Geolocation Tracker
 *  Paste this ENTIRE file's content into:
 *  Google Sheet → Extensions → Apps Script → Code.gs
 * ══════════════════════════════════════════════════════════════════════════════
 */

/**
 * Handle GET requests for testing endpoint status
 */
function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: "GMIU Email Audit Tracker API is active." }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Handle POST requests from send-email.php
 */
function doPost(e) {
  try {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    
    var headers = [
      "Timestamp (IST)", 
      "Module / Page", 
      "Page Referer URL",
      "SMTP Sender Email", 
      "Recipient Email (To)", 
      "CC Emails", 
      "Email Subject", 
      "Attachment File(s)",
      "Client IP Address", 
      "Client Port",
      "PC / Hostname", 
      "Location (City, Region, Country)", 
      "ISP / Network Provider", 
      "User / Faculty Details", 
      "Browser / OS (User Agent)"
    ];

    // Auto-create headers with dark styling if sheet is newly created
    if (sheet.getLastRow() === 0) {
      sheet.appendRow(headers);
      
      // Apply professional dark header styling
      var headerRange = sheet.getRange(1, 1, 1, headers.length);
      headerRange.setFontWeight("bold");
      headerRange.setBackground("#0f172a");
      headerRange.setFontColor("#ffffff");
      headerRange.setHorizontalAlignment("center");
      sheet.setRowHeight(1, 38);
      sheet.setFrozenRows(1);
    }
    
    // Parse incoming audit log data from send-email.php
    var data = JSON.parse(e.postData.contents);
    
    // Append audit log row
    sheet.appendRow([
      data.timestamp || new Date().toLocaleString("en-US", { timeZone: "Asia/Kolkata" }),
      data.module || "Unknown Module",
      data.referer || "",
      data.smtp_sender || "",
      data.to || "",
      data.cc || "",
      data.subject || "",
      data.attachments || "None (Inline Body)",
      data.ip || "",
      data.port || "",
      data.pc_name || "",
      data.location || "",
      data.isp || "",
      data.user_info || "",
      data.user_agent || ""
    ]);
    
    var lastRow = sheet.getLastRow();
    sheet.getRange(lastRow, 1, 1, headers.length).setVerticalAlignment("middle");
    
    return ContentService.createTextOutput(JSON.stringify({ status: "success", message: "Audit log successfully recorded" }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({ status: "error", message: error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
