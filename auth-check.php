<?php
/**
 * GMIU IT Department — Server-Side Authentication Helper
 * Securely blocks access to PHP pages if session is not authenticated
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$password_required = 1;

// Read setting from verify-password.php directly without executing it (avoid sending headers)
$verify_file = __DIR__ . '/verify-password.php';
if (file_exists($verify_file)) {
    $verify_content = file_get_contents($verify_file);
    // Use regex to parse $password_required value (0 or false)
    if (preg_match('/\$password_required\s*=\s*(0|false)/i', $verify_content)) {
        $password_required = 0;
    }
}

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
