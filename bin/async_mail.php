<?php
/**
 * Asynchronous Background Email Dispatcher
 * Allows the login authentication request to return in <50ms without waiting for SMTP roundtrips.
 */

if (php_sapi_name() !== 'cli' && !defined('STDIN')) {
    http_response_code(403);
    exit("CLI execution only.\n");
}

$recipientEmail = $argv[1] ?? '';
$recipientName  = $argv[2] ?? 'Faculty Member';
$otp            = $argv[3] ?? '';
$deptCode       = $argv[4] ?? 'IT';

if (empty($recipientEmail) || empty($otp)) {
    exit("Missing parameters.\n");
}

$baseDir = dirname(__DIR__);
$configFile = $baseDir . '/config/app.php';
$config = file_exists($configFile) ? (include $configFile) : [];

$isCe = ($deptCode === 'CE');
if ($isCe) {
    $smtpUser = $config['smtp_email_ce'] ?? ($config['smtp_email'] ?? 'admincecse@gmiu.edu.in');
    $smtpPass = $config['smtp_password_ce'] ?? ($config['smtp_password'] ?? '');
    $fromName = 'CE Department Portal';
} else {
    $smtpUser = $config['smtp_email'] ?? 'adminit@gmiu.edu.in';
    $smtpPass = $config['smtp_password'] ?? '';
    $fromName = 'IT Department Portal';
}

$subject  = "Your Login Verification Code: {$otp}";

date_default_timezone_set('Asia/Kolkata');
$timeStr = date('d M Y, h:i A');
$digits  = str_split($otp);
$digitHtml = '';
foreach ($digits as $d) {
    $digitHtml .= "<span style=\"display: inline-block; width: 44px; height: 52px; line-height: 52px; margin: 0 4px; background: #ffffff; border: 2px solid #7c3aed; border-radius: 12px; font-size: 26px; font-weight: 800; color: #0f172a; text-align: center; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);\">{$d}</span>";
}

$deptFullName = ($deptCode === 'CE') ? 'DEPARTMENT OF COMPUTER ENGINEERING' : 'DEPARTMENT OF INFORMATION TECHNOLOGY';
$portalName   = ($deptCode === 'CE') ? 'CE Academic Portal' : 'IT Academic Portal';

$htmlBody = "
<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Login Verification Code</title>
</head>
<body style=\"margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;\">
    <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color:#f8fafc; padding: 30px 10px;\">
        <tr>
            <td align=\"center\">
                <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width: 560px; background-color:#ffffff; border-radius: 18px; overflow:hidden; box-shadow: 0 14px 35px rgba(0,0,0,0.07); border: 1px solid #e2e8f0;\">
                    <tr>
                        <td style=\"height: 5px; background: linear-gradient(90deg, #7c3aed 0%, #2563eb 50%, #10b981 100%);\"></td>
                    </tr>
                    <tr>
                        <td style=\"background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 32px 25px; text-align: center;\">
                            <div style=\"display: inline-block; padding: 5px 14px; background: rgba(124, 58, 237, 0.25); border: 1px solid rgba(167, 139, 250, 0.4); border-radius: 50px; color: #c4b5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px;\">
                                Faculty Authentication Protocol
                            </div>
                            <h1 style=\"color: #ffffff; margin: 0; font-size: 19px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;\">
                                {$deptFullName}
                            </h1>
                            <p style=\"color: #94a3b8; margin: 6px 0 0 0; font-size: 13px;\">
                                {$portalName} &bull; Smart Management Dashboard
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"padding: 35px 30px; text-align: center; background-color: #ffffff;\">
                            <p style=\"font-size: 15px; color: #1e293b; margin: 0 0 12px 0; text-align: left;\">
                                Hello <strong>" . htmlspecialchars($recipientName) . "</strong>,
                            </p>
                            <p style=\"font-size: 14px; color: #475569; margin: 0 0 24px 0; line-height: 1.6; text-align: left;\">
                                A sign-in request was initiated for your faculty account. Please enter the 6-digit One-Time Passcode below to complete your authentication and access the portal:
                            </p>
                            <div style=\"background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 15px; margin: 24px 0;\">
                                <div style=\"font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;\">
                                    Your 6-Digit Verification Code
                                </div>
                                <div style=\"display: inline-block;\">
                                    {$digitHtml}
                                </div>
                            </div>
                            <div style=\"background-color: #fff8f8; border-left: 4px solid #ef4444; border-radius: 6px; padding: 12px 16px; margin: 20px 0; text-align: left;\">
                                <p style=\"font-size: 12.5px; color: #991b1b; margin: 0; font-weight: 600;\">
                                    🔒 This code will expire in <strong>5 minutes</strong>. Do not share this passcode with anyone.
                                </p>
                            </div>
                            <p style=\"font-size: 12px; color: #64748b; margin: 16px 0 0 0; text-align: center;\">
                                Requested for <strong style=\"color: #7c3aed;\">" . htmlspecialchars($recipientEmail) . "</strong> on {$timeStr} IST
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"background-color: #f8fafc; padding: 18px 25px; text-align: center; border-top: 1px solid #e2e8f0;\">
                            <p style=\"font-size: 11px; color: #94a3b8; margin: 0;\">
                                Automated Authentication Service &bull; Gyanmanjari Innovative University
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";

if (empty($smtpUser) || empty($smtpPass)) {
    error_log("[ASYNC-MAIL] Warning: SMTP credentials missing.");
    exit(1);
}

try {
    $timeout = 10;
    $smtp = @fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, $timeout);
    if (!$smtp) {
        error_log("[ASYNC-MAIL] Connection failed: {$errstr} ({$errno})");
        exit(1);
    }

    $getResponse = function($smtp) {
        $data = "";
        while ($str = fgets($smtp, 515)) {
            $data .= $str;
            if (substr($str, 3, 1) === " ") break;
        }
        return $data;
    };

    $getResponse($smtp);
    fwrite($smtp, "EHLO localhost\r\n");
    $getResponse($smtp);

    fwrite($smtp, "AUTH LOGIN\r\n");
    $getResponse($smtp);

    fwrite($smtp, base64_encode($smtpUser) . "\r\n");
    $getResponse($smtp);

    fwrite($smtp, base64_encode($smtpPass) . "\r\n");
    $response = $getResponse($smtp);
    if (strpos($response, "235") === false) {
        error_log("[ASYNC-MAIL] Authentication rejected: " . trim($response));
        fclose($smtp);
        exit(1);
    }

    fwrite($smtp, "MAIL FROM: <$smtpUser>\r\n");
    $getResponse($smtp);

    fwrite($smtp, "RCPT TO: <$recipientEmail>\r\n");
    $response = $getResponse($smtp);
    if (strpos($response, "250") === false && strpos($response, "251") === false) {
        error_log("[ASYNC-MAIL] Recipient rejected: " . trim($response));
        fclose($smtp);
        exit(1);
    }

    fwrite($smtp, "DATA\r\n");
    $getResponse($smtp);

    $headers = [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "To: $recipientEmail",
        "From: \"$fromName\" <$smtpUser>",
        "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
        "Date: " . date("r"),
        "Message-ID: <login-otp-" . time() . "-" . uniqid() . "@gmiu.edu.in>"
    ];

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.\r\n";
    fwrite($smtp, $message);
    $getResponse($smtp);

    fwrite($smtp, "QUIT\r\n");
    fclose($smtp);
    error_log("[ASYNC-MAIL] Successfully sent OTP to {$recipientEmail}");
    exit(0);
} catch (\Throwable $e) {
    error_log("[ASYNC-MAIL] Exception: " . $e->getMessage());
    exit(1);
}

