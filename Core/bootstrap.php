<?php
/**
 * Application Bootstrap & Initialization
 */

// 1. Timezone and Error Reporting
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Define Core Path Constants
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/App');
}
if (!defined('CORE_PATH')) {
    define('CORE_PATH', BASE_PATH . '/Core');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . '/config');
}
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Ensure includes resolve cleanly from App/Views/partials, App/Services, and BASE_PATH
set_include_path(get_include_path() . PATH_SEPARATOR . APP_PATH . '/Views/partials' . PATH_SEPARATOR . APP_PATH . '/Services' . PATH_SEPARATOR . BASE_PATH);

// 3. Dynamic Subdirectory-Aware BASE_URL
$protocol = (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $protocol . $host;

// Subdirectory detection (e.g. /IT or /it on localhost)
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if (!empty($scriptDir) && $scriptDir !== '/' && $scriptDir !== '.') {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '';
    if (stripos($reqUri, $scriptDir) === 0) {
        $baseUrl .= substr($reqUri, 0, strlen($scriptDir));
    } else {
        $baseUrl .= $scriptDir;
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $baseUrl);
}

// 4. Session Initialization with Security Best Practices (24-Hour Session Lifetime)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 86400); // 24 hours in seconds

$isHttps = (
    (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
);
if ($isHttps) {
    ini_set('session.cookie_secure', 1);
}

if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
} else {
    session_set_cookie_params(86400, '/', '', $isHttps, true);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Output Buffering
if (ob_get_level() === 0) {
    ob_start();
}

// 6. Security & Cache-Busting Headers
if (!headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");

    // Auto Cache-Bust: Force full revalidation if cache-bust query param is present
    if (!empty($_GET['_nocache']) || !empty($_GET['_v']) || !empty($_GET['_t'])) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
}

// 7. Load Global Helpers
require_once __DIR__ . '/helpers.php';
