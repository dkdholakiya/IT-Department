<?php
/**
 * GMIU IT Department Email Service Backend
 * Native PHP replacement for Node.js server.js
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
    echo json_encode(["success" => false, "error" => "Method Not Allowed. Only POST requests are permitted."]);
    exit;
}

// Read and parse the incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid JSON payload."]);
    exit;
}

$to = $input['to'] ?? '';
$cc = $input['cc'] ?? [];
$subject = $input['subject'] ?? '';
$html = $input['html'] ?? '';
$attachment = $input['attachment'] ?? '';
$filename = $input['filename'] ?? '';

if (empty($to)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => 'Recipient email ("to") is required.']);
    exit;
}

// Dynamically parse Gmail SMTP credentials from emailConfig.js
$configFile = __DIR__ . '/assets/js/emailConfig.js';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Configuration file emailConfig.js not found."]);
    exit;
}

$configContent = file_get_contents($configFile);
preg_match('/email\s*:\s*["\']([^"\']+)["\']/', $configContent, $emailMatches);
preg_match('/appPassword\s*:\s*["\']([^"\']+)["\']/', $configContent, $passwordMatches);

$email = $emailMatches[1] ?? '';
$appPassword = $passwordMatches[1] ?? '';

if (empty($email) || empty($appPassword)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to parse credentials from emailConfig.js."]);
    exit;
}

try {
    send_smtp_email($to, $cc, $subject, $html, $email, $appPassword, $attachment, $filename);
    echo json_encode(["success" => true, "message" => "Email sent successfully!"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

/**
 * Direct SMTP socket client to send mail via Gmail SSL port 465
 */
function send_smtp_email($to, $cc, $subject, $html, $username, $password, $attachment = '', $filename = '') {
    $timeout = 15;
    $smtp = fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, $timeout);
    if (!$smtp) {
        throw new Exception("Could not connect to SMTP server: $errstr ($errno)");
    }

    // Read SMTP responses helper
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

    $get_response($smtp); // Read 220 greeting

    fwrite($smtp, "EHLO localhost\r\n");
    $get_response($smtp);

    fwrite($smtp, "AUTH LOGIN\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "334") === false) {
        throw new Exception("AUTH LOGIN not accepted: " . trim($response));
    }

    fwrite($smtp, base64_encode($username) . "\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "334") === false) {
        throw new Exception("Username not accepted: " . trim($response));
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
        throw new Exception("RCPT TO <$to> failed: " . trim($response));
    }

    if (is_array($cc)) {
        foreach ($cc as $cc_email) {
            $cc_email = trim($cc_email);
            if (!empty($cc_email)) {
                fwrite($smtp, "RCPT TO: <$cc_email>\r\n");
                $response = $get_response($smtp);
                if (strpos($response, "250") === false && strpos($response, "251") === false) {
                    throw new Exception("RCPT TO CC <$cc_email> failed: " . trim($response));
                }
            }
        }
    }

    fwrite($smtp, "DATA\r\n");
    $response = $get_response($smtp);
    if (strpos($response, "354") === false) {
        throw new Exception("DATA command failed: " . trim($response));
    }

    // Clean data URI from attachment if present
    if (!empty($attachment) && preg_match('/^data:.*;base64,/', $attachment)) {
        $attachment = preg_replace('/^data:.*;base64,/', '', $attachment);
    }

    if (!empty($attachment) && !empty($filename)) {
        $boundary = md5(uniqid(time(), true));

        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: multipart/mixed; boundary=\"$boundary\"",
            "To: $to",
            "From: \"IT Department\" <$username>",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?="
        ];

        if (is_array($cc) && count($cc) > 0) {
            $headers[] = "Cc: " . implode(", ", $cc);
        }

        $headers[] = "Date: " . date("r");
        $headers[] = "Message-ID: <" . time() . "-" . uniqid() . "@gmiu.edu.in>";

        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $html . "\r\n\r\n";

        $body .= "--$boundary\r\n";
        $body .= "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name=\"$filename\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
        $body .= chunk_split($attachment) . "\r\n";

        $body .= "--$boundary--";

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
    } else {
        // Format standard MIME headers
        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "To: $to",
            "From: \"IT Department\" <$username>",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?="
        ];

        if (is_array($cc) && count($cc) > 0) {
            $headers[] = "Cc: " . implode(", ", $cc);
        }

        $headers[] = "Date: " . date("r");
        $headers[] = "Message-ID: <" . time() . "-" . uniqid() . "@gmiu.edu.in>";

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $html . "\r\n.\r\n";
    }

    fwrite($smtp, $message);
    $response = $get_response($smtp);
    if (strpos($response, "250") === false) {
        throw new Exception("Sending message failed: " . trim($response));
    }

    fwrite($smtp, "QUIT\r\n");
    fclose($smtp);

    return true;
}
