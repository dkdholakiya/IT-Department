<?php
/**
 * GMIU IT Department — 6-Digit Email OTP Verification Backend Service
 * Generates, emails, and verifies OTP codes for faculty report submissions.
 */

// Hardening Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// Handle CORS Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start PHP session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load Secure Configuration
define('SECURE_ACCESS', true);
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Configuration file not found."]);
    exit;
}
$config = include $config_file;

$otp_enabled = $config['otp_enabled'] ?? 1;

// Parse payload cleanly (JSON body, POST data, and GET query params)
$raw_input = file_get_contents('php://input');
$json_input = json_decode($raw_input, true);
$input = is_array($json_input) ? $json_input : [];
$input = array_merge($_GET, $_POST, $input);

$action = $input['action'] ?? $_GET['action'] ?? $_POST['action'] ?? '';

// Check OTP feature status
if ($action === 'check_status') {
    echo json_encode(["success" => true, "otp_enabled" => (int)$otp_enabled]);
    exit;
}

// Action 1: Send OTP
if ($action === 'send_otp') {
    if ($otp_enabled == 0) {
        echo json_encode(["success" => true, "message" => "OTP is disabled in system config.", "bypass" => true]);
        exit;
    }

    $email = strtolower(trim($input['email'] ?? ''));
    $faculty_name = trim($input['faculty_name'] ?? 'Faculty Member');

    if (empty($email)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Faculty email address is required."]);
        exit;
    }

    if (substr($email, -12) !== '@gmiu.edu.in') {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Forbidden. OTP can only be sent to official @gmiu.edu.in email address."]);
        exit;
    }

    // Rate Limiting: 15 seconds cooldown between resend requests
    if (isset($_SESSION['otp_store'][$email])) {
        $prev = $_SESSION['otp_store'][$email];
        $time_since_last = time() - ($prev['created_at'] ?? 0);
        if ($time_since_last < 15) {
            $wait = 15 - $time_since_last;
            http_response_code(429);
            echo json_encode(["success" => false, "error" => "Please wait {$wait} seconds before requesting a new OTP code."]);
            exit;
        }
    }

    // Generate 6-Digit Random Code
    try {
        $otp = sprintf('%06d', random_int(100000, 999999));
    } catch (Exception $e) {
        $otp = sprintf('%06d', rand(100000, 999999));
    }

    // Store in Session
    if (!isset($_SESSION['otp_store'])) {
        $_SESSION['otp_store'] = [];
    }

    $_SESSION['otp_store'][$email] = [
        'code' => $otp,
        'created_at' => time(),
        'expires_at' => time() + 300, // 5 minutes validity
        'verified' => false
    ];

    // Detect Department (CE or IT) from input or email address
    $dept_input = strtoupper(trim($input['dept'] ?? ''));
    $is_ce = ($dept_input === 'CE' || strpos($dept_input, 'COMPUTER') !== false || stripos($email, 'admincecse') !== false || stripos($email, 'ce') !== false);
    $dept_code = $is_ce ? 'CE' : 'IT';

    // Set SMTP credentials, sender name, and dynamic subject based on department
    if ($is_ce) {
        $smtp_email = $config['smtp_email_ce'] ?? ($config['smtp_email'] ?? '');
        $smtp_password = $config['smtp_password_ce'] ?? ($config['smtp_password'] ?? '');
        $from_name = 'CE Department Verification';
        $subject = "Your OTP for CE Report Verification";
    } else {
        $smtp_email = $config['smtp_email'] ?? '';
        $smtp_password = $config['smtp_password'] ?? '';
        $from_name = 'IT Department Verification';
        $subject = "Your OTP for IT Report Verification";
    }

    $html_body = generate_otp_email_html($otp, $faculty_name, $email, $dept_code);

    if (empty($smtp_email) || empty($smtp_password)) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "SMTP credentials misconfigured."]);
        exit;
    }

    try {
        send_otp_smtp_mail($email, $subject, $html_body, $smtp_email, $smtp_password, $from_name);
        echo json_encode([
            "success" => true,
            "message" => "6-Digit OTP dispatched successfully.",
            "expires_in" => 300
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Failed to deliver OTP email: " . $e->getMessage()]);
    }
    exit;
}

// Action 2: Verify OTP
if ($action === 'verify_otp') {
    if ($otp_enabled == 0) {
        echo json_encode(["success" => true, "message" => "OTP is disabled in system config.", "bypass" => true]);
        exit;
    }

    $email = strtolower(trim($input['email'] ?? ''));
    $submitted_otp = trim($input['otp'] ?? '');

    if (empty($email) || empty($submitted_otp)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Email address and 6-digit OTP code are required."]);
        exit;
    }

    if (!isset($_SESSION['otp_store'][$email])) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "No active OTP request found for this email. Please request a new OTP."]);
        exit;
    }

    $stored = $_SESSION['otp_store'][$email];

    // Check expiration
    if (time() > $stored['expires_at']) {
        unset($_SESSION['otp_store'][$email]);
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "OTP verification code has expired. Click 'Resend OTP' to get a new code."]);
        exit;
    }

    // Check OTP match
    if ($stored['code'] !== $submitted_otp) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Invalid 6-digit OTP code. Please verify the code sent to your email."]);
        exit;
    }

    // Verification successful: Mark session as verified and clear code to prevent reuse
    $_SESSION['otp_store'][$email]['verified'] = true;
    unset($_SESSION['otp_store'][$email]['code']);

    echo json_encode([
        "success" => true,
        "message" => "OTP verified successfully. Proceeding with report submission."
    ]);
    exit;
}

http_response_code(400);
echo json_encode(["success" => false, "error" => "Invalid endpoint action."]);
exit;


/**
 * Helper: Generate Executive HTML OTP Email Template
 */
function generate_otp_email_html($otp, $faculty_name, $email, $dept_code = 'IT') {
    date_default_timezone_set('Asia/Kolkata');
    $time_str = date('d M Y, h:i A');
    $digits = str_split($otp);
    $digit_html = '';
    foreach ($digits as $d) {
        $digit_html .= "<span style=\"display: inline-block; width: 44px; height: 52px; line-height: 52px; margin: 0 4px; background: #ffffff; border: 2px solid #2563eb; border-radius: 10px; font-size: 26px; font-weight: 800; color: #0f172a; text-align: center; box-shadow: 0 3px 8px rgba(37, 99, 235, 0.12);\">{$d}</span>";
    }

    $dept_full_name = ($dept_code === 'CE') ? 'DEPARTMENT OF COMPUTER ENGINEERING' : 'DEPARTMENT OF INFORMATION TECHNOLOGY';
    $portal_name = ($dept_code === 'CE') ? 'CE Activity Portal' : 'IT Activity Portal';
    $hub_name = ($dept_code === 'CE') ? 'CE Department Verification Hub' : 'IT Department Verification Hub';

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset=\"utf-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <title>Faculty Verification Code</title>
    </head>
    <body style=\"margin:0; padding:0; background-color:#f1f5f9; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;\">
        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color:#f1f5f9; padding: 30px 10px;\">
            <tr>
                <td align=\"center\">
                    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width: 580px; background-color:#ffffff; border-radius: 16px; overflow:hidden; box-shadow: 0 12px 30px rgba(0,0,0,0.08); border: 1px solid #cbd5e1;\">
                        <!-- Top Accent Bar -->
                        <tr>
                            <td style=\"height: 5px; background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 50%, #dc2626 100%);\"></td>
                        </tr>
                        <!-- Header Section -->
                        <tr>
                            <td style=\"background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 25px; text-align: center;\">
                                <div style=\"display: inline-block; padding: 6px 14px; background: rgba(37, 99, 235, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 50px; color: #93c5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px;\">
                                    Security Verification Protocol
                                </div>
                                <h1 style=\"color: #ffffff; margin: 0; font-size: 19px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; font-family: 'Segoe UI', Arial, sans-serif;\">
                                    {$dept_full_name}
                                </h1>
                                <p style=\"color: #94a3b8; margin: 6px 0 0 0; font-size: 13px; font-weight: 500;\">
                                    Faculty Activity Report Authorization
                                </p>
                            </td>
                        </tr>
                        <!-- Main Content Body -->
                        <tr>
                            <td style=\"padding: 35px 30px; text-align: center; background-color: #ffffff;\">
                                <p style=\"font-size: 15px; color: #1e293b; margin: 0 0 14px 0; text-align: left;\">
                                    Hello <strong>" . htmlspecialchars($faculty_name) . "</strong>,
                                </p>
                                <p style=\"font-size: 14px; color: #475569; margin: 0 0 24px 0; line-height: 1.6; text-align: left;\">
                                    A report submission request was initiated under your profile on the <strong>{$portal_name}</strong>. Please use the 6-digit One-Time Password (OTP) below to authorize and finalize your submission:
                                </p>
                                
                                <!-- OTP Box Card Display -->
                                <div style=\"background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 14px; padding: 22px 15px; margin: 25px 0;\">
                                    <div style=\"font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;\">
                                        Your 6-Digit Passcode
                                    </div>
                                    <div style=\"display: inline-block; cursor: pointer; user-select: all; -webkit-user-select: all; padding: 6px 12px; border-radius: 10px; transition: background 0.2s;\" title=\"Click to select & copy passcode\">
                                        {$digit_html}
                                    </div>
                                </div>
                                
                                <!-- Confidentiality Alert Box -->
                                <div style=\"background-color: #fff8f8; border-left: 4px solid #ef4444; border-radius: 6px; padding: 12px 16px; margin: 24px 0 18px 0; text-align: left;\">
                                    <p style=\"font-size: 13px; color: #991b1b; margin: 0; font-weight: 600; line-height: 1.5;\">
                                        🔒 <strong>Security Warning:</strong> This code will expire in <strong>5 minutes</strong>. Do not share this OTP code with anyone. IT personnel will never ask for your passcode.
                                    </p>
                                </div>

                                <!-- Transaction Audit Details -->
                                <p style=\"font-size: 12px; color: #64748b; margin: 15px 0 0 0; text-align: center;\">
                                    Requested for <strong style=\"color: #2563eb;\">" . htmlspecialchars($email) . "</strong> on {$time_str} IST
                                </p>
                            </td>
                        </tr>
                        <!-- Footer Section -->
                        <tr>
                            <td style=\"background-color: #f8fafc; padding: 20px 25px; text-align: center; border-top: 1px solid #e2e8f0;\">
                                <p style=\"font-size: 11px; color: #64748b; margin: 0 0 4px 0; font-weight: 500;\">
                                    {$hub_name} &bull; Automated System Dispatch
                                </p>
                                <p style=\"font-size: 10.5px; color: #94a3b8; margin: 0;\">
                                    This is a system-generated email. Please do not reply directly to this message.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
}

/**
 * Helper: Direct Socket SSL SMTP Email Sender for OTP
 */
function send_otp_smtp_mail($to, $subject, $html, $username, $password, $from_name = 'IT Department Verification') {
    $timeout = 15;
    $smtp = @fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, $timeout);
    if (!$smtp) {
        throw new Exception("Could not connect to Gmail SMTP: {$errstr} ({$errno})");
    }

    $get_response = function($smtp) {
        $data = "";
        while ($str = fgets($smtp, 515)) {
            $data .= $str;
            if (substr($str, 3, 1) === " ") {
                break;
            }
        }
        return $data;
    };

    $get_response($smtp);

    fwrite($smtp, "EHLO localhost\r\n");
    $get_response($smtp);

    fwrite($smtp, "AUTH LOGIN\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "334") === false) {
        throw new Exception("AUTH LOGIN failed: " . trim($response));
    }

    fwrite($smtp, base64_encode($username) . "\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "334") === false) {
        throw new Exception("Username rejected: " . trim($response));
    }

    fwrite($smtp, base64_encode($password) . "\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "235") === false) {
        throw new Exception("Authentication failed: " . trim($response));
    }

    fwrite($smtp, "MAIL FROM: <$username>\r\n");
    $get_response($smtp);

    fwrite($smtp, "RCPT TO: <$to>\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "250") === false && strpos($response, "251") === false) {
        throw new Exception("Recipient <$to> rejected: " . trim($response));
    }

    fwrite($smtp, "DATA\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "354") === false) {
        throw new Exception("DATA rejected: " . trim($response));
    }

    $headers = [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "To: $to",
        "From: \"$from_name\" <$username>",
        "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
        "Date: " . date("r"),
        "Message-ID: <otp-" . time() . "-" . uniqid() . "@gmiu.edu.in>"
    ];

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $html . "\r\n.\r\n";
    fwrite($smtp, $message);
    $response = $get_response($smtp);

    if (strpos($response, "250") === false) {
        throw new Exception("Sending OTP email failed: " . trim($response));
    }

    fwrite($smtp, "QUIT\r\n");
    fclose($smtp);
    return true;
}
