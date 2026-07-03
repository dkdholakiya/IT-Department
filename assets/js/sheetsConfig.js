/**
 * Google Sheets Configuration
 * GMIU IT Department — Report Management System
 *
 * SETUP INSTRUCTIONS:
 * 1. Open your Google Sheet:
 *    https://docs.google.com/spreadsheets/d/1toNcrcsuJcdakW9cP2Az-HG3S7z9BJrusKJoxX1p47A
 *
 * 2. Click  Extensions → Apps Script
 *
 * 3. Delete any existing code and paste the content from:
 *    assets/js/google-apps-script.js  (copy that file's content)
 *
 * 4. Click Save (💾), then Deploy → New Deployment
 *    - Type: Web App
 *    - Execute as: Me
 *    - Who has access: Anyone
 *    Click Deploy → Authorize → Deploy
 *
 * 5. Copy the Web App URL and paste it below as SHEETS_WEBAPP_URL
 */

const SHEETS_CONFIG = {
    // ← Paste your Apps Script Web App URL here after deployment
    WEBAPP_URL: "https://script.google.com/macros/s/AKfycbzAZyhE8DkK_DnrDhxj4fCx75TlDFRufpUyD7cgI6TXg39HC2hTgXXNGPaw_pg9m2UIiQ/exec",

    // Web App URL for Zero Student Log Spreadsheet
    ZERO_WEBAPP_URL: "https://script.google.com/macros/s/AKfycbwJVs-JUh-TmNG0vbEhQcFnjFTQqUcw9YrHw55SAQF7DjbTFbBrUJYFmk7dz-nhQMzV/exec",

    // Sheet tab name where data will be written (default: Sheet1)
    SHEET_NAME: "2026-27 ODD",

    // Enable/disable Google Sheets integration (set false to disable)
    ENABLED: true
};
