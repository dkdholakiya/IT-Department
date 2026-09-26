<?php
/**
 * GMIU IT Department — Google Sheets Backend Proxy
 * Routes client-side log requests through the backend to protect Apps Script URLs.
 */

// Enable session parsing
session_start();

// Define access guard
define('SECURE_ACCESS', true);

// Configure response headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Restrict to POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method Not Allowed. Only POST requests are permitted."]);
    exit;
}

// Load configuration
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Configuration file not found."]);
    exit;
}
$config = include $config_file;

// Determine target Sheet
$target = $_GET['target'] ?? '';
$target_url = '';

if ($target === 'report') {
    $target_url = $config['sheets_webapp_url'] ?? '';
} else if ($target === 'zero') {
    // SECURITY check: Zero student logs require authentication or same-origin request
    $password_required = $config['password_required'] ?? 1;
    $isSameOrigin = (!empty($_SERVER['HTTP_REFERER']) && isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false);
    $authenticated = ($password_required == 0 || (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) || $isSameOrigin);
    
    if (!$authenticated) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Unauthorized access. Session not authenticated."]);
        exit;
    }
    $target_url = $config['zero_sheets_webapp_url'] ?? '';
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid target parameter specified."]);
    exit;
}

if (empty($target_url)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Target Apps Script URL is not configured."]);
    exit;
}

// Read the incoming JSON body
$payload = file_get_contents('php://input');

// Execute POST request to Google Apps Script with automatic 3-pass retry logic
$response = false;
$http_code = 0;
$last_error = '';

$max_attempts = 3;
for ($attempt = 1; $attempt <= $max_attempts; $attempt++) {
    $ch = curl_init($target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow Google Apps Script redirects
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);   // 12s connect timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);          // 25s execution timeout per attempt
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Force HTTP/1.1 for Google compatibility

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $last_error = curl_error($ch);
    curl_close($ch);

    // If Google Apps Script returned a valid JSON response body with success or duplicate indicator
    if (!empty($response)) {
        $decoded = json_decode($response, true);
        if (is_array($decoded) && (isset($decoded['success']) || isset($decoded['duplicate']) || isset($decoded['message']))) {
            $http_code = 200; // Force 200 OK since Apps Script executed and returned structured JSON result
            break;
        }
    }

    if ($response !== false && $http_code >= 200 && $http_code < 400) {
        break; // Success!
    }

    if ($attempt < $max_attempts) {
        usleep($attempt * 400000); // 400ms, 800ms progressive backoff
    }
}

if ($response === false || $http_code >= 400) {
    http_response_code($http_code > 0 ? $http_code : 502);
    echo json_encode([
        "success" => false,
        "error" => "Failed to communicate with Google Sheets.",
        "details" => $last_error ?: "Target Google Apps Script returned HTTP status {$http_code}"
    ]);
} else {
    http_response_code($http_code);
    echo $response;
}
exit;
