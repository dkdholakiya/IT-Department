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
    'zero_mail_enabled' => 0, // Toggle: 1 to enable email for zero student report, 0 to disable
    'otp_enabled' => 1, // Toggle: 1 to require 6-digit email OTP verification before submitting report, 0 to bypass

    // Google Sheets Apps Script Web App URLs
    'sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbzgEUHcbbF7k5gp8RyEJxwzPiUeTeu6wLIZOPbNn-ALnsGPPzyNEKVx9q5ZnHKESHEUxA/exec',
    'zero_sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbw1zMUC-u2HP1S1ld58Dc4HtIEqWTrcR8h1G0wNfc6bod2KW3rX9knynzW99c2z0oWf/exec',
    'mail_tracking_webapp_url' => 'https://script.google.com/macros/s/AKfycbzxEuVzCR9mSSzoOWpRo2stvO5Bcn-MGRgpbCanDvnErmfQ-kZKXaIF7cXmf_az8rFEBg/exec', // Paste your deployed Google Apps Script Web App URL here for Email Audit Logs


    // Password configuration
    'password_required' => 1, // Toggle: 1 to require password, 0 to bypass (matches verify-password.php current state)
    'correct_password' => '$2y$10$bU1WzHy8QBFzpbkR7hkT2O3q3XB0OTxlEX7DzLeBix0yaPpMPZ6uS', // Bcrypt hash of 'itce@2026'
];
