<?php
/**
 * GMIU IT Department Email Service Backend
 * Native PHP replacement for Node.js server.js
 */

// Enable background execution
ignore_user_abort(true);
set_time_limit(0);

// Add security hardening headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

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

// SECURITY: Open Relay Prevention
// Extract and check all target email addresses (both 'to' and 'cc' keys)
$emails_to_validate = [];
$emails = $input['emails'] ?? [];
if (!empty($emails) && is_array($emails)) {
    foreach ($emails as $item) {
        if (!empty($item['to'])) {
            $emails_to_validate[] = $item['to'];
        }
        if (!empty($item['cc']) && is_array($item['cc'])) {
            foreach ($item['cc'] as $cc_email) {
                $emails_to_validate[] = $cc_email;
            }
        }
    }
} else {
    $to = $input['to'] ?? '';
    if (!empty($to)) {
        $emails_to_validate[] = $to;
    }
    $cc = $input['cc'] ?? [];
    if (!empty($cc) && is_array($cc)) {
        foreach ($cc as $cc_email) {
            $emails_to_validate[] = $cc_email;
        }
    }
}

// Validate all target email addresses strictly end with @gmiu.edu.in
foreach ($emails_to_validate as $check_email) {
    $check_email = trim($check_email);
    if (empty($check_email)) {
        continue;
    }
    if (substr(strtolower($check_email), -12) !== '@gmiu.edu.in') {
        http_response_code(403);
        echo json_encode(["success" => false, "error" => "Forbidden. Recipient email address must be an official @gmiu.edu.in domain."]);
        exit;
    }
}

// Load secure config
define('SECURE_ACCESS', true);
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Configuration file not found."]);
    exit;
}
$config = include $config_file;

$mail_enabled = $config['mail_enabled'] ?? 1;
if ($mail_enabled == 0) {
    echo json_encode(["success" => true, "message" => "Email transmission is disabled in config."]);
    exit;
}

$module = $input['module'] ?? '';
if ($module === 'zero') {
    $zero_mail_enabled = $config['zero_mail_enabled'] ?? 1;
    if ($zero_mail_enabled == 0) {
        echo json_encode(["success" => true, "message" => "Email transmission is disabled for Zero Student module."]);
        exit;
    }
}

$dept = $input['dept'] ?? '';

// If dept not explicitly provided, auto-detect from recipient email
if (empty($dept)) {
    $first_to = '';
    if (!empty($emails) && is_array($emails)) {
        $first_to = $emails[0]['to'] ?? '';
    } else {
        $first_to = $input['to'] ?? '';
    }
    if (!empty($first_to) && (stripos($first_to, 'admincecse') !== false || stripos($first_to, 'ce') !== false)) {
        $dept = 'CE';
    }
}

// Default to IT SMTP credentials and From Name (adminit@gmiu.edu.in)
$email = $config['smtp_email'] ?? '';
$appPassword = $config['smtp_password'] ?? '';
$from_name = 'IT Department';

// If target department is Computer Engineering, use CE credentials (admincecse@gmiu.edu.in)
if (strtoupper($dept) === 'CE') {
    $email = $config['smtp_email_ce'] ?? $email;
    $appPassword = $config['smtp_password_ce'] ?? $appPassword;
    $from_name = 'CE Department';
}

if (empty($email) || empty($appPassword)) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to load SMTP configuration."]);
    exit;
}

function send_instant_success_response() {
    // Disable compression so we can flush buffers correctly
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
    }
    ini_set('zlib.output_compression', '0');

    // Turn off output buffering
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    ob_start();
    echo json_encode(["success" => true, "message" => "Email transmission started in background."]);
    $size = ob_get_length();
    header("Content-Length: $size");
    header("Connection: close");
    ob_end_flush();
    flush();

    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
}

$emails = $input['emails'] ?? [];
$attachment = $input['attachment'] ?? '';
$filename = $input['filename'] ?? '';
$attachments = $input['attachments'] ?? [];

if (!empty($emails) && is_array($emails)) {
    // Multi-email request
    send_instant_success_response();
    
    try {
        foreach ($emails as $item) {
            $to = $item['to'] ?? '';
            $cc = $item['cc'] ?? [];
            $subject = $item['subject'] ?? '';
            $html = $item['html'] ?? '';
            $item_attachments = $item['attachments'] ?? $attachments;
            $item_attachment = $item['attachment'] ?? $attachment;
            $item_filename = $item['filename'] ?? $filename;
            if (!empty($to)) {
                send_smtp_email($to, $cc, $subject, $html, $email, $appPassword, $item_attachment, $item_filename, $from_name, $item_attachments);
            }
        }
    } catch (Exception $e) {
        error_log("Multi-SMTP Error: " . $e->getMessage());
    }
    exit;
} else {
    // Single email request (backward compatible)
    $to = $input['to'] ?? '';
    $cc = $input['cc'] ?? [];
    $subject = $input['subject'] ?? '';
    $html = $input['html'] ?? '';
    
    if (empty($to)) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => 'Recipient email ("to") is required.']);
        exit;
    }
    
    send_instant_success_response();
    
    try {
        send_smtp_email($to, $cc, $subject, $html, $email, $appPassword, $attachment, $filename, $from_name, $attachments);
    } catch (Exception $e) {
        error_log("Single SMTP Error: " . $e->getMessage());
    }
    exit;
}


/**
 * Direct SMTP socket client to send mail via Gmail SSL port 465
 */
function send_smtp_email($to, $cc, $subject, $html, $username, $password, $attachment = '', $filename = '', $from_name = 'IT Department', $attachments = []) {
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

    // Build attachments list
    $all_attachments = [];
    if (!empty($attachments) && is_array($attachments)) {
        $all_attachments = $attachments;
    } else if (!empty($attachment) && !empty($filename)) {
        $all_attachments[] = ['data' => $attachment, 'filename' => $filename];
    }

    if (!empty($all_attachments)) {
        $boundary = md5(uniqid(time(), true));

        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: multipart/mixed; boundary=\"$boundary\"",
            "To: $to",
            "From: \"$from_name\" <$username>",
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

        foreach ($all_attachments as $att) {
            $att_data = $att['data'] ?? $att['attachment'] ?? '';
            $att_filename = $att['filename'] ?? 'attachment';

            if (preg_match('/^data:.*;base64,/', $att_data)) {
                $att_data = preg_replace('/^data:.*;base64,/', '', $att_data);
            }

            if (empty($att_data)) continue;

            $mime_type = "application/octet-stream";
            if (preg_match('/\.pdf$/i', $att_filename)) {
                $mime_type = "application/pdf";
            } else if (preg_match('/\.zip$/i', $att_filename)) {
                $mime_type = "application/zip";
            } else if (preg_match('/\.xlsx$/i', $att_filename)) {
                $mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
            }

            $body .= "--$boundary\r\n";
            $body .= "Content-Type: {$mime_type}; name=\"{$att_filename}\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"{$att_filename}\"\r\n\r\n";
            $body .= chunk_split($att_data) . "\r\n";
        }

        $body .= "--$boundary--";

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
    } else {
        // Format standard MIME headers
        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "To: $to",
            "From: \"$from_name\" <$username>",
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
