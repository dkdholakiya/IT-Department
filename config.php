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
    // Gmail SMTP credentials
    'smtp_email' => 'adminit@gmiu.edu.in',
    'smtp_password' => 'gevb rfuj sxoj lwwc',

    // Google Sheets Apps Script Web App URLs
    'sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbzAZyhE8DkK_DnrDhxj4fCx75TlDFRufpUyD7cgI6TXg39HC2hTgXXNGPaw_pg9m2UIiQ/exec',
    'zero_sheets_webapp_url' => 'https://script.google.com/macros/s/AKfycbwJVs-JUh-TmNG0vbEhQcFnjFTQqUcw9YrHw55SAQF7DjbTFbBrUJYFmk7dz-nhQMzV/exec',

    // Password configuration
    'password_required' => 0, // Toggle: 1 to require password, 0 to bypass (matches verify-password.php current state)
    'correct_password' => '$2y$10$JWqQqWzuv9HgV9d5B0jxjOskIXi13KlIGI20SjiwRadRrPYbOcYPC', // Bcrypt hash of 'gmiu@it'
];
