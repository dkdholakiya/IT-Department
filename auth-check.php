<?php
/**
 * GMIU IT Department — Server-Side Authentication Helper
 * Securely blocks access to PHP pages if session is not authenticated
 */

// Disable displaying PHP errors to browser in production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Still log all errors internally

// Configure secure session cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] == 443)) {
    ini_set('session.cookie_secure', 1);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add security hardening headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

define('SECURE_ACCESS', true);

// Load secure config
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    header("HTTP/1.1 500 Internal Server Error");
    exit("Server Configuration Error: config.php not found.");
}
$config = include $config_file;

$password_required = $config['password_required'] ?? 1;

// Determine if user is authenticated
$authenticated = false;
if ($password_required == 0 || (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true)) {
    $authenticated = true;
}

if (!$authenticated) {
    // Save current page name to prompt the user correctly
    $current_page = basename($_SERVER['PHP_SELF']);
    $page_label = "this protected resource";
    if (stripos($current_page, 'ctlactivity') !== false) {
        $page_label = "CTL Activity Dashboard";
    } else if (stripos($current_page, 'ctldrive') !== false) {
        $page_label = "Drive Folder Scanner";
    }

    // Include the login page layout and exit
    include __DIR__ . '/auth-login-page.php';
    exit;
}

