<?php
/**
 * GMIU IT Department Password Verification Backend
 */

// Enable CORS and configure JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start PHP Session
session_start();

// Password Switch: 1 = ON (Ask for password), 0 = OFF (Password not asked)
$password_required = 1;

// Configure the correct password here
// Default password: gmiu@it
$correct_password = "gmiuu@it";

// Clear session action
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    $_SESSION['authenticated'] = false;
    session_destroy();
    echo json_encode(["success" => true, "message" => "Session cleared."]);
    exit;
}

// If method is GET, return the password status
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    echo json_encode([
        "success" => true,
        "password_required" => ($password_required == 1)
    ]);
    exit;
}

// If password is turned off (0), immediately allow entry without checking
if ($password_required == 0) {
    $_SESSION['authenticated'] = true;
    echo json_encode(["success" => true, "message" => "Authentication successful (Password disabled)."]);
    exit;
}

// Ensure the request method is POST for verification
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method Not Allowed."]);
    exit;
}

// Read and parse the incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$password = $input['password'] ?? '';

if (empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Password is required."]);
    exit;
}

$is_valid = false;
if ($password === $correct_password) {
    $is_valid = true;
}

if ($is_valid) {
    $_SESSION['authenticated'] = true;
    echo json_encode(["success" => true, "message" => "Authentication successful."]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid password."]);
}
exit;

