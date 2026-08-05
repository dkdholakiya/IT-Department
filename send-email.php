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

/**
 * Automatically logs email transmission details (sender, recipient, IP, PC name, date, time, location, module)
 * to Google Sheets via Google Apps Script Web App endpoint.
 */
function track_email_execution($input, $config, $sender_email) {
    try {
        $tracking_url = $config['mail_tracking_webapp_url'] ?? '';
        
        // 1. Timestamp in IST
        date_default_timezone_set('Asia/Kolkata');
        $timestamp = date('Y-m-d h:i:s A');

        // 2. IP Address Detection
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $display_ip = $ip;
        if ($ip === '::1' || $ip === '127.0.0.1') {
            $local_ipv4 = @gethostbyname(gethostname());
            if (!empty($local_ipv4) && filter_var($local_ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $local_ipv4 !== '127.0.0.1') {
                $display_ip = "127.0.0.1 ({$local_ipv4})";
            } else {
                $display_ip = '127.0.0.1';
            }
        }



        // 3. PC Name / Hostname Detection
        $pc_name = '';
        if (!empty($ip) && $ip !== '127.0.0.1' && $ip !== '::1') {
            $pc_name = @gethostbyaddr($ip);
        }
        if (empty($pc_name) || $pc_name === $ip) {
            $pc_name = getenv('COMPUTERNAME') ?: (getenv('HOSTNAME') ?: (@gethostname() ?: 'Workstation / Client PC'));
        }

        // 4. Module / Route Detection
        $module_raw = $input['module'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $detected_module = 'Unknown Module';

        if (stripos($referer, 'zero-student-report') !== false || $module_raw === 'zero') {
            $detected_module = 'Zero Student Report (zero-student-report)';
        } elseif (stripos($referer, 'ctlactivity') !== false) {
            $detected_module = 'CTL Activity Report (ctlactivity)';
        } elseif (stripos($referer, 'ctldrive') !== false) {
            $detected_module = 'CTL Drive (ctldrive)';
        } elseif (stripos($referer, 'report') !== false) {
            $detected_module = 'Faculty Activity Report (report)';
        } elseif (!empty($module_raw)) {
            $detected_module = ucfirst($module_raw) . ' Module';
        } elseif (!empty($referer)) {
            $detected_module = 'Web Module (' . parse_url($referer, PHP_URL_PATH) . ')';
        }

        // 5. Geolocation Resolution (Cached in session for zero latency)
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $location_info = $_SESSION['cached_geo_loc'] ?? 'Localhost / Internal LAN';
        $isp_info = $_SESSION['cached_geo_isp'] ?? 'Local Network';

        if (!isset($_SESSION['cached_geo_loc'])) {
            $is_local = ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0 || strpos($ip, '172.16.') === 0);
            $query_ip = $ip;
            if ($is_local) {
                $pub_ctx = stream_context_create(['http' => ['timeout' => 1]]);
                $public_ip_raw = @file_get_contents('https://api.ipify.org?format=json', false, $pub_ctx);
                if ($public_ip_raw) {
                    $pub_json = json_decode($public_ip_raw, true);
                    if (!empty($pub_json['ip'])) {
                        $query_ip = $pub_json['ip'];
                    }
                }
            }

            if (!empty($query_ip)) {
                $geo_ctx = stream_context_create(['http' => ['timeout' => 1]]);
                $geo_raw = @file_get_contents("http://ip-api.com/json/{$query_ip}?fields=status,country,regionName,city,isp,org,query", false, $geo_ctx);
                if ($geo_raw) {
                    $geo = json_decode($geo_raw, true);
                    if ($geo && isset($geo['status']) && $geo['status'] === 'success') {
                        $city = $geo['city'] ?? '';
                        $region = $geo['regionName'] ?? '';
                        $country = $geo['country'] ?? '';
                        $loc_parts = array_filter([$city, $region, $country]);
                        $location_info = !empty($loc_parts) ? implode(', ', $loc_parts) : 'Unknown Location';
                        $isp_info = $geo['isp'] ?? ($geo['org'] ?? 'Unknown ISP');
                        if ($is_local) {
                            $location_info .= " (Public IP: {$query_ip})";
                        }
                        $_SESSION['cached_geo_loc'] = $location_info;
                        $_SESSION['cached_geo_isp'] = $isp_info;
                    }
                }
            }
        }


        // 6. Detailed User, Identity & Session Info
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $sess_user = $_SESSION['user_name'] ?? ($_SESSION['user'] ?? ($_SESSION['username'] ?? ($_SESSION['email'] ?? '')));
        $faculty_name = $input['faculty_name'] ?? ($input['prepared_by'] ?? ($input['faculty'] ?? ($input['name'] ?? '')));
        $faculty_email = $input['faculty_email'] ?? ($input['ref_email'] ?? '');
        $faculty_emp = $input['faculty_emp_id'] ?? ($input['emp_id'] ?? '');

        $user_parts = array_filter([
            $sess_user ? "Session: {$sess_user}" : '',
            $faculty_name ? "Faculty: {$faculty_name}" : '',
            $faculty_email ? "Email: {$faculty_email}" : '',
            $faculty_emp ? "EmpID: {$faculty_emp}" : ''
        ]);
        $user_info = !empty($user_parts) ? implode(' | ', $user_parts) : 'Guest / System Web User';

        // 7. Network Port, Language & User-Agent Parsing
        $remote_port = $_SERVER['REMOTE_PORT'] ?? '';
        $accept_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $lang_clean = !empty($accept_lang) ? explode(',', $accept_lang)[0] : 'en-US';

        $raw_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User-Agent';
        $sec_ua = $_SERVER['HTTP_SEC_CH_UA'] ?? '';
        
        $os_label = 'Unknown OS';
        $arch = (stripos($raw_ua, 'Win64') !== false || stripos($raw_ua, 'x64') !== false) ? ' 64-bit' : '';
        if (stripos($raw_ua, 'Windows NT 10.0') !== false) $os_label = 'Windows 10/11' . $arch;
        elseif (stripos($raw_ua, 'Windows NT 6.3') !== false) $os_label = 'Windows 8.1' . $arch;
        elseif (stripos($raw_ua, 'Windows NT 6.1') !== false) $os_label = 'Windows 7' . $arch;
        elseif (stripos($raw_ua, 'Macintosh') !== false) $os_label = 'macOS';
        elseif (stripos($raw_ua, 'Android') !== false) $os_label = 'Android Mobile';
        elseif (stripos($raw_ua, 'iPhone') !== false || stripos($raw_ua, 'iPad') !== false) $os_label = 'iOS Mobile';
        elseif (stripos($raw_ua, 'Linux') !== false) $os_label = 'Linux';

        $browser_label = 'Web Browser';
        if (!empty($input['browser'])) {
            $browser_label = $input['browser'];
        } elseif (stripos($sec_ua, 'Brave') !== false || stripos($raw_ua, 'Brave') !== false || isset($_SERVER['HTTP_SEC_GPC'])) {
            $browser_label = 'Brave Browser';
        } elseif (stripos($raw_ua, 'Edg/') !== false || stripos($raw_ua, 'Edge/') !== false) {
            $browser_label = 'Microsoft Edge';
        } elseif (stripos($raw_ua, 'OPR/') !== false || stripos($raw_ua, 'Opera') !== false) {
            $browser_label = 'Opera Browser';
        } elseif (stripos($raw_ua, 'Firefox') !== false) {
            $browser_label = 'Mozilla Firefox';
        } elseif (stripos($raw_ua, 'Chrome') !== false) {
            $browser_label = 'Google Chrome';
        } elseif (stripos($raw_ua, 'Safari') !== false) {
            $browser_label = 'Apple Safari';
        }



        $user_agent = "{$browser_label} ({$os_label})";

        // 8. Email Recipients, Subject & Attachments Summary
        $to_str = '';
        $cc_str = '';
        $subject_str = '';

        if (!empty($input['emails']) && is_array($input['emails'])) {
            $to_arr = [];
            $cc_arr = [];
            $sub_arr = [];
            foreach ($input['emails'] as $em) {
                if (!empty($em['to'])) $to_arr[] = $em['to'];
                if (!empty($em['cc'])) {
                    if (is_array($em['cc'])) $cc_arr = array_merge($cc_arr, $em['cc']);
                    else $cc_arr[] = $em['cc'];
                }
                if (!empty($em['subject'])) $sub_arr[] = $em['subject'];
            }
            $to_str = implode(', ', array_unique($to_arr));
            $cc_str = implode(', ', array_unique($cc_arr));
            $subject_str = implode(' | ', array_unique($sub_arr));
        } else {
            $to_str = $input['to'] ?? '';
            $cc_str = is_array($input['cc'] ?? null) ? implode(', ', $input['cc']) : ($input['cc'] ?? '');
            $subject_str = $input['subject'] ?? '';
        }

        // Attachments check
        $attachment_files = [];
        if (!empty($input['filename'])) $attachment_files[] = $input['filename'];
        if (!empty($input['attachments']) && is_array($input['attachments'])) {
            foreach ($input['attachments'] as $att) {
                if (!empty($att['filename'])) $attachment_files[] = $att['filename'];
            }
        }
        if (!empty($input['emails']) && is_array($input['emails'])) {
            foreach ($input['emails'] as $item) {
                if (!empty($item['filename'])) $attachment_files[] = $item['filename'];
                if (!empty($item['attachments']) && is_array($item['attachments'])) {
                    foreach ($item['attachments'] as $att) {
                        if (!empty($att['filename'])) $attachment_files[] = $att['filename'];
                    }
                }
            }
        }
        $attachments_str = !empty($attachment_files) ? implode(', ', array_unique($attachment_files)) : 'None (Inline Body)';

        $payload = [
            'timestamp' => $timestamp,
            'module' => $detected_module,
            'referer' => $referer ?: 'Direct Web Endpoint',
            'smtp_sender' => $sender_email,
            'to' => $to_str,
            'cc' => $cc_str,
            'subject' => $subject_str,
            'attachments' => $attachments_str,
            'ip' => $display_ip,
            'port' => $remote_port,
            'pc_name' => $pc_name,
            'location' => $location_info,
            'isp' => $isp_info,
            'user_info' => $user_info,
            'user_agent' => $user_agent,
            'language' => $lang_clean
        ];


        // Send payload to Google Sheets Apps Script endpoint
        if (!empty($tracking_url)) {
            $json_data = json_encode($payload);
            $content_len = strlen($json_data);
            if (function_exists('curl_init')) {
                $ch = curl_init($tracking_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . $content_len
                ]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 4);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
            } else {
                $ctx = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/json\r\nContent-Length: " . $content_len . "\r\n",
                        'content' => $json_data,
                        'timeout' => 4
                    ]
                ]);
                @file_get_contents($tracking_url, false, $ctx);
            }
        }
    } catch (Exception $e) {
        error_log("Email Tracking Error: " . $e->getMessage());
    }
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
    // Multi-email request: Respond to browser instantly (<30ms)
    send_instant_success_response();
    
    // Background execution: track & send emails
    track_email_execution($input, $config, $email);
    
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
    
    // Respond to browser instantly (<30ms)
    send_instant_success_response();
    
    // Background execution: track & send email
    track_email_execution($input, $config, $email);
    
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
