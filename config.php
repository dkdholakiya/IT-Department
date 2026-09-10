<?php
/**
 * GMIU IT Department — Secure Configuration File
 * Protects credentials and API parameters from direct web exposure.
 */

// Deny direct access from the browser
if (!defined('SECURE_ACCESS')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access not permitted.");
}

return [
    // IT Department Gmail SMTP credentials
    'smtp_email' => 'adminit@gmiu.edu.in',
    'smtp_password' => 'gevb rfuj sxoj lwwc',

    // CE Department Gmail SMTP credentials
    'smtp_email_ce' => 'admincecse@gmiu.edu.in',
    'smtp_password_ce' => 'wrws fjmw sqig bxxn',

    'mail_enabled' => 1, // Toggle: 1 to enable email system, 0 to disable
    'zero_mail_enabled' => 1, // Toggle: 1 to enable email for zero student report, 0 to disable
    'otp_enabled' => 1, // Toggle: 1 to require 6-digit email OTP verification before submitting report, 0 to bypass
    'ctl_excel_server_error' => 1, // Toggle: 1 to simulate Server Error after Excel upload in CTL activity, 0 to process normally

    // Google Sheets Apps Script Web App URLs
    'sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbzgEUHcbbF7k5gp8RyEJxwzPiUeTeu6wLIZOPbNn-ALnsGPPzyNEKVx9q5ZnHKESHEUxA/exec',
    'zero_sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbw1zMUC-u2HP1S1ld58Dc4HtIEqWTrcR8h1G0wNfc6bod2KW3rX9knynzW99c2z0oWf/exec',
    'mail_tracking_webapp_url' => 'https://script.google.com/macros/s/AKfycbwAJoWNmmuaGA4lJo-ctUrlW_-cfbSRC6DUl4qWfTvn5F20FgkoVqdV3OyHn1Ds6dq1bg/exec', // Paste your deployed Google Apps Script Web App URL here for Email Audit Logs

    // Auto-Sync Configuration (Background Google Sheets Live Sync)
    'student_timetable_webapp_url' => 'https://script.google.com/macros/s/AKfycby5yeTrt5piGKzvwV0UCoV5eHkzvuBETo9mCn5YWSL0W-mt_06jq3BgmVV0l69rBHM/exec', // Google Apps Script Web App URL for Student Timetable
    'faculty_timetable_webapp_url' => 'https://script.google.com/macros/s/AKfycbzQX2jaQ2BQEFkKefKdSurJK6etBRmfZXrj7zg1-5TOOOyeV-m6dx4MYfP3_zBaP08CuA/exec', // Google Apps Script Web App URL for Faculty Timetable

    'auto_sync_enabled' => 1, // Toggle: 1 to enable automatic background live sheet sync, 0 to disable
    'auto_sync_interval' => 1800, // Auto-sync interval in seconds (default: 60)


    // Password configuration
    'password_required' => 1, /// Toggle: 1 to require password, 0 to bypass (matches verify-password.php current state)
    'correct_password' => '$2y$10$/uEX4Ru39hvF.sbH7oVCw.HTPqwMzEoqum4fYQqJkkQR3lGA0mWPG', // Bcrypt hash of 'itce@#@#'
];
