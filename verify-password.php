<?php
/**
 * GMIU IT Department Password Verification Backend
 */

// Enable CORS and configure JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Ensure the request method is POST
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

// Configure the correct password here
// Default password: gmiu@it
$correct_password = "gmiu@it";

$is_valid = false;
if ($password === $correct_password) {
    $is_valid = true;
}

if ($is_valid) {
    echo json_encode(["success" => true, "message" => "Authentication successful."]);
} else {
    echo json_encode(["success" => false, "error" => "Invalid password."]);
}
exit;
