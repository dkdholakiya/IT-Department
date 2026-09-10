<?php
/**
 * GMIU IT Department — Google Sheets One-Click Timetable Downloader & Syncer
 * 
 * Downloads, renames, cleans up old files, and compiles Excel sheets for:
 * 1. Student Timetable -> uploads/student_timetable/student_timetable.xlsx
 * 2. Faculty Timetable -> uploads/timetable/timetable.xlsx
 */

// Enable session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

require_once __DIR__ . '/auto-cache-bust.php';
require_once __DIR__ . '/student-timetable-parser.php';
require_once __DIR__ . '/timetable-compiler.php';

// Default Google Sheets IDs & URLs
$DEFAULT_STUDENT_SHEET_ID = "1Xz1FnvlfESkdrx3ot1_Vt7UCc0bLiTM0cuFYZy3UqWE";
$DEFAULT_FACULTY_SHEET_ID = "12SGzk1LqNSN1DH8h0bSGg88JGSORMPO7V_QUKc65-ys";

// Helper function to extract Sheet ID from Google Sheets URL
function extractGoogleSheetId($urlOrId) {
    $urlOrId = trim($urlOrId);
    if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $urlOrId, $matches)) {
        return $matches[1];
    }
    if (preg_match('/^[a-zA-Z0-9-_]+$/', $urlOrId)) {
        return $urlOrId;
    }
    return false;
}

// Download sheet via Google Apps Script Web App (if configured)
function downloadViaAppsScript($targetType) {
    if (!defined('SECURE_ACCESS')) {
        define('SECURE_ACCESS', true);
    }
    $configFile = __DIR__ . '/config.php';
    if (!file_exists($configFile)) {
        return [
            'success' => false,
            'message' => 'config.php file missing on live server.'
        ];
    }
    $config = include $configFile;
    
    $webAppUrl = '';
    if ($targetType === 'student' && !empty($config['student_timetable_webapp_url'])) {
        $webAppUrl = $config['student_timetable_webapp_url'];
    } else if ($targetType === 'faculty' && !empty($config['faculty_timetable_webapp_url'])) {
        $webAppUrl = $config['faculty_timetable_webapp_url'];
    }
    
    if (empty($webAppUrl)) {
        return [
            'success' => false,
            'message' => 'Google Apps Script Web App URL is not configured in config.php on live server. Please upload the updated config.php file.'
        ];
    }
    
    $url = $webAppUrl . (strpos($webAppUrl, '?') !== false ? '&' : '?') . 'target=' . urlencode($targetType);
    
    $data = false;
    $httpCode = 0;
    $curlErr = '';
    
    // Method 1: cURL
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
    }
    
    // Method 2: file_get_contents stream context fallback if cURL failed
    if (($data === false || $httpCode !== 200) && ini_get('allow_url_fopen')) {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
                'follow_location' => 1,
                'timeout' => 60
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        $context = stream_context_create($opts);
        $streamData = @file_get_contents($url, false, $context);
        if ($streamData !== false) {
            $data = $streamData;
            $httpCode = 200;
        }
    }
    
    if ($data === false || ($httpCode !== 200 && $httpCode !== 0)) {
        return [
            'success' => false,
            'httpCode' => $httpCode,
            'message' => 'Failed to reach Apps Script Web App (HTTP ' . $httpCode . ($curlErr ? ' Error: ' . $curlErr : '') . '). Check server internet/cURL access.'
        ];
    }
    
    $json = @json_decode($data, true);
    if ($json) {
        if (isset($json['success']) && $json['success'] && !empty($json['base64'])) {
            $binary = base64_decode($json['base64']);
            if ($binary && substr($binary, 0, 4) === "PK\x03\x04") {
                return [
                    'success' => true,
                    'data' => $binary,
                    'size' => strlen($binary)
                ];
            }
        }
        if (isset($json['error'])) {
            return [
                'success' => false,
                'httpCode' => $httpCode,
                'message' => 'Apps Script Error: ' . $json['error']
            ];
        }
        if (!isset($json['base64'])) {
            return [
                'success' => false,
                'httpCode' => $httpCode,
                'message' => 'Apps Script Web App returned JSON schedule instead of Excel binary. Please update Apps Script code and deploy as New Version.'
            ];
        }
    }
    
    if (substr($data, 0, 4) === "PK\x03\x04") {
        return [
            'success' => true,
            'data' => $data,
            'size' => strlen($data)
        ];
    }
    
    return [
        'success' => false,
        'httpCode' => $httpCode,
        'message' => 'Apps Script Web App returned unrecognized response.'
    ];
}

// Download file via cURL with redirect following and multiple URL endpoints
function downloadGoogleSheetXlsx($sheetId, $targetType = 'all') {
    // Try Google Apps Script Web App first (bypasses private permissions)
    $appScriptRes = downloadViaAppsScript($targetType);
    if ($appScriptRes) {
        if ($appScriptRes['success'] ?? false) {
            return $appScriptRes;
        }
        // If Apps Script Web App returned a specific message, return it directly so user sees real status!
        if (!empty($appScriptRes['message'])) {
            return $appScriptRes;
        }
    }

    // Fallback: Try standard export URL & published URL
    $urls = [
        "https://docs.google.com/spreadsheets/d/" . $sheetId . "/export?format=xlsx",
        "https://docs.google.com/spreadsheets/d/" . $sheetId . "/pub?output=xlsx"
    ];

    $lastError = "";
    $lastHttpCode = 0;
    
    foreach ($urls as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/octet-stream, */*',
            'Accept-Language: en-US,en;q=0.9'
        ]);
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $lastHttpCode = $httpCode;
        if ($data === false || $httpCode !== 200) {
            $lastError = "HTTP Status $httpCode. " . ($error ? "Error: $error" : "");
            continue;
        }
        
        // Check magic bytes for ZIP/XLSX file format (PK\x03\x04)
        $magic = substr($data, 0, 4);
        if ($magic === "PK\x03\x04") {
            return [
                'success' => true,
                'data' => $data,
                'size' => strlen($data)
            ];
        }
        
        if (strpos($data, '<!DOCTYPE') !== false || strpos($data, '<html') !== false || strpos($data, 'accounts.google.com') !== false) {
            $lastError = "Google Sheet Access Restricted (HTTP 401 / Login Required). Please change Share settings to 'Anyone with the link can view' OR configure Apps Script Web App URL in config.php.";
            continue;
        }
        
        $lastError = "Downloaded content is invalid (Not a valid Excel .xlsx binary file).";
    }

    return [
        'success' => false,
        'httpCode' => $lastHttpCode,
        'message' => $lastError ?: "Failed to download Excel file from Google Sheets."
    ];
}

// Function to sync Student Timetable
function syncStudentTimetable($sheetId = null) {
    global $DEFAULT_STUDENT_SHEET_ID;
    $sheetId = extractGoogleSheetId($sheetId ?: $DEFAULT_STUDENT_SHEET_ID);
    
    if (!$sheetId) {
        return ['success' => false, 'message' => 'Invalid Student Google Sheet ID.'];
    }
    
    $download = downloadGoogleSheetXlsx($sheetId, 'student');
    if (!$download['success']) {
        return $download;
    }
    
    $dir = __DIR__ . '/uploads/student_timetable/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    
    // 1. Remove all old Excel files in uploads/student_timetable/
    $oldFiles = glob($dir . '*.{xlsx,xls,XLSX,XLS}', GLOB_BRACE);
    if ($oldFiles) {
        foreach ($oldFiles as $f) {
            @unlink($f);
        }
    }
    @unlink($dir . 'student_timetable_cache.json');
    
    // 2. Save new file as student_timetable.xlsx
    $targetFile = $dir . 'student_timetable.xlsx';
    if (file_put_contents($targetFile, $download['data']) === false) {
        return ['success' => false, 'message' => 'Failed to save downloaded Student Excel file to server.'];
    }
    
    // 3. Rebuild JSON cache immediately to prevent mismatch
    $cacheFile = $dir . 'student_timetable_cache.json';
    $parseRes = parseExcelToTtCache($targetFile, $cacheFile);
    
    if (!$parseRes || !file_exists($cacheFile)) {
        return ['success' => false, 'message' => 'Downloaded Excel file successfully, but failed to parse Student timetable structure.'];
    }
    
    return [
        'success' => true,
        'message' => 'Student Timetable updated & cache rebuilt successfully!',
        'file' => 'uploads/student_timetable/student_timetable.xlsx',
        'size' => number_format($download['size'] / 1024, 1) . ' KB'
    ];
}

// Function to sync Faculty Timetable
function syncFacultyTimetable($sheetId = null) {
    global $DEFAULT_FACULTY_SHEET_ID;
    $sheetId = extractGoogleSheetId($sheetId ?: $DEFAULT_FACULTY_SHEET_ID);
    
    if (!$sheetId) {
        return ['success' => false, 'message' => 'Invalid Faculty Google Sheet ID.'];
    }
    
    $download = downloadGoogleSheetXlsx($sheetId, 'faculty');
    if (!$download['success']) {
        return $download;
    }
    
    $dir = __DIR__ . '/uploads/timetable/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    
    // 1. Remove all old Excel files in uploads/timetable/
    $oldFiles = glob($dir . '*.{xlsx,xls,XLSX,XLS}', GLOB_BRACE);
    if ($oldFiles) {
        foreach ($oldFiles as $f) {
            @unlink($f);
        }
    }
    
    // 2. Save new file as timetable.xlsx
    $targetFile = $dir . 'timetable.xlsx';
    if (file_put_contents($targetFile, $download['data']) === false) {
        return ['success' => false, 'message' => 'Failed to save downloaded Faculty Excel file to server.'];
    }
    
    // 3. Recompile timetable JS data immediately to prevent mismatch
    $compileRes = compileTimetableData();
    if (!($compileRes['success'] ?? false)) {
        return [
            'success' => false,
            'message' => 'Downloaded Faculty Excel file successfully, but compilation failed: ' . ($compileRes['message'] ?? 'Unknown error')
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Faculty Timetable downloaded & compiled successfully! (' . ($compileRes['count'] ?? 0) . ' faculty members)',
        'file' => 'uploads/timetable/timetable.xlsx',
        'size' => number_format($download['size'] / 1024, 1) . ' KB',
        'count' => $compileRes['count'] ?? 0
    ];
}

// Execute sync if requested via API / CLI / Form POST
$action = $_REQUEST['action'] ?? '';
if (empty($action) && php_sapi_name() === 'cli' && isset($argv[1])) {
    $rawArg = $argv[1];
    if (strpos($rawArg, '=') !== false) {
        parse_str($rawArg, $parsedArgs);
        $action = $parsedArgs['action'] ?? '';
    } else {
        $action = $rawArg;
    }
}

if (!empty($action) && in_array($action, ['sync_all', 'sync_student', 'sync_faculty', 'cli'])) {
    // Password authentication check (for Web requests when password_required is enabled)
    if (php_sapi_name() !== 'cli') {
        $configFile = __DIR__ . '/config.php';
        $config = file_exists($configFile) ? include $configFile : [];
        $password_required = $config['password_required'] ?? 1;
        
        if ($password_required == 1) {
            $submittedPwd = $_REQUEST['password'] ?? '';
            $isAutoSync = (!empty($_REQUEST['auto']) && !empty($config['auto_sync_enabled']) && $config['auto_sync_enabled'] == 1);
            $isAuth = $isAutoSync;
            
            if (!$isAuth && !empty($submittedPwd)) {
                $correct_password = $config['correct_password'] ?? '';
                if (strpos($correct_password, '$2y$') === 0 && strlen($correct_password) === 60) {
                    if (password_verify($submittedPwd, $correct_password)) {
                        $isAuth = true;
                    }
                } else if ($submittedPwd === $correct_password) {
                    $isAuth = true;
                }
            }
            
            if (!$isAuth) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'auth_required' => true,
                    'message' => 'Password authentication required to sync Google Sheets.'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }

    $studentSheetId = $_REQUEST['student_sheet_id'] ?? $DEFAULT_STUDENT_SHEET_ID;
    $facultySheetId = $_REQUEST['faculty_sheet_id'] ?? $DEFAULT_FACULTY_SHEET_ID;
    
    $results = [];
    
    if ($action === 'sync_student') {
        $results['student'] = syncStudentTimetable($studentSheetId);
    } else if ($action === 'sync_faculty') {
        $results['faculty'] = syncFacultyTimetable($facultySheetId);
    } else { // sync_all or cli
        $results['student'] = syncStudentTimetable($studentSheetId);
        $results['faculty'] = syncFacultyTimetable($facultySheetId);
    }
    
    // Check overall success
    $allSuccess = true;
    foreach ($results as $k => $r) {
        if (!($r['success'] ?? false)) {
            $allSuccess = false;
        }
    }
    
    $response = [
        'success' => $allSuccess,
        'timestamp' => date('Y-m-d H:i:s'),
        'results' => $results
    ];
    
    // CLI Output mode
    if (php_sapi_name() === 'cli' || $action === 'cli') {
        echo "====================================================\n";
        echo " GMIU TIMETABLE ONE-CLICK DOWNLOAD & SYNC UTILITY  \n";
        echo "====================================================\n\n";
        
        if (isset($results['student'])) {
            echo "[1] STUDENT TIMETABLE:\n";
            if ($results['student']['success']) {
                echo "    ✅ SUCCESS: " . $results['student']['message'] . "\n";
                echo "    File: " . $results['student']['file'] . " (" . $results['student']['size'] . ")\n\n";
            } else {
                echo "    ❌ ERROR: " . $results['student']['message'] . "\n\n";
            }
        }
        
        if (isset($results['faculty'])) {
            echo "[2] FACULTY TIMETABLE:\n";
            if ($results['faculty']['success']) {
                echo "    ✅ SUCCESS: " . $results['faculty']['message'] . "\n";
                echo "    File: " . $results['faculty']['file'] . " (" . $results['faculty']['size'] . ")\n\n";
            } else {
                echo "    ❌ ERROR: " . $results['faculty']['message'] . "\n\n";
            }
        }
        
        if ($allSuccess) {
            echo "🎉 ALL TIMETABLES DOWNLOADED AND UPDATED LIVE WITH ZERO MISMATCH!\n";
        } else {
            echo "⚠️ Sync completed with errors.\n";
            echo "   FIX: Update Code.gs in Google Apps Script editor with updated template and deploy as New Version.\n";
        }
        exit(0);
    }
    
    // AJAX JSON response mode
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Current file info status for Web UI display
$stDir = __DIR__ . '/uploads/student_timetable/';
$stFiles = glob($stDir . '*.{xlsx,xls}', GLOB_BRACE);
$stCurrentFile = !empty($stFiles) ? basename($stFiles[0]) : null;
$stMTime = $stCurrentFile ? date('d M Y, h:i A', filemtime($stDir . $stCurrentFile)) : 'None';
$stSize = $stCurrentFile ? number_format(filesize($stDir . $stCurrentFile) / 1024, 1) . ' KB' : '0 KB';

$ftDir = __DIR__ . '/uploads/timetable/';
$ftFiles = glob($ftDir . '*.{xlsx,xls}', GLOB_BRACE);
$ftCurrentFile = !empty($ftFiles) ? basename($ftFiles[0]) : null;
$ftMTime = $ftCurrentFile ? date('d M Y, h:i A', filemtime($ftDir . $ftCurrentFile)) : 'None';
$ftSize = $ftCurrentFile ? number_format(filesize($ftDir . $ftCurrentFile) / 1024, 1) . ' KB' : '0 KB';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Click Google Sheets Timetable Syncer — GMIU</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/faculty.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">
    <style>
        body {
            background-color: #060d1f;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: #f8fafc;
            margin: 0;
            padding: 40px 20px;
        }

        .sync-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.7) 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(24px);
            border-radius: 20px;
            padding: 40px;
            max-width: 850px;
            margin: 0 auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }

        .sync-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .sync-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #38bdf8;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sync-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 10px 0;
        }

        .sync-subtitle {
            color: #94a3b8;
            font-size: 15px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .main-sync-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 18px 28px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(2, 132, 199, 0.4);
            margin-bottom: 35px;
        }

        .main-sync-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(2, 132, 199, 0.6);
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
        }

        .main-sync-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 640px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        .sheet-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sheet-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sheet-meta {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .sheet-meta strong {
            color: #cbd5e1;
        }

        .sub-sync-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #f8fafc;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .sub-sync-btn:hover {
            background: rgba(56, 189, 248, 0.2);
            border-color: #38bdf8;
            color: #38bdf8;
        }

        .result-box {
            display: none;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 20px;
            margin-top: 25px;
        }

        .result-box.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .status-badge.success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .status-badge.error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .perm-help-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-top: 15px;
            color: #fbbf24;
            font-size: 13.5px;
            line-height: 1.6;
        }

        .perm-help-box strong {
            color: #fff;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="sync-card">
        <div class="sync-header">
            <div class="sync-badge">⚡ Instant Google Sheets Sync</div>
            <h1 class="sync-title">1-Click Timetable Downloader</h1>
            <p class="sync-subtitle">
                Automatically fetches live Excel files directly from Google Sheets, replaces old files, and compiles database tables with zero mismatch.
            </p>
        </div>

        <?php if (isset($response)): ?>
            <div class="result-box show">
                <span class="status-badge <?php echo $response['success'] ? 'success' : 'error'; ?>">
                    <?php echo $response['success'] ? '✓ Sync Completed Successfully' : '⚠️ Sync Completed with Errors'; ?>
                </span>
                
                <div style="font-size: 14px; line-height: 1.8;">
                    <strong>Student Timetable:</strong> 
                    <span style="color: <?php echo ($response['results']['student']['success'] ?? false) ? '#34d399' : '#f87171'; ?>;">
                        <?php echo htmlspecialchars($response['results']['student']['message'] ?? 'Not synced'); ?>
                    </span><br>

                    <strong>Faculty Timetable:</strong> 
                    <span style="color: <?php echo ($response['results']['faculty']['success'] ?? false) ? '#34d399' : '#f87171'; ?>;">
                        <?php echo htmlspecialchars($response['results']['faculty']['message'] ?? 'Not synced'); ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="sync-timetables.php" id="syncForm">
            <input type="hidden" name="action" id="actionInput" value="sync_all">
            
            <button type="button" class="main-sync-btn" id="mainSyncBtn" onclick="runSync('sync_all')">
                <span class="spinner" id="mainSpinner"></span>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" id="mainIcon">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span id="mainBtnText">1-Click Download & Update All Timetables</span>
            </button>
        </form>

        <div class="grid-cards">
            <!-- Student Timetable Card -->
            <div class="sheet-card">
                <div>
                    <div class="sheet-card-title">
                        <span>🎓 Student Timetable</span>
                    </div>
                    <div class="sheet-meta">
                        <strong>Current File:</strong> <?php echo htmlspecialchars($stCurrentFile ?: 'None'); ?><br>
                        <strong>Last Updated:</strong> <?php echo $stMTime; ?><br>
                        <strong>Size:</strong> <?php echo $stSize; ?><br>
                        <strong>Google Sheet ID:</strong> <code><?php echo $DEFAULT_STUDENT_SHEET_ID; ?></code>
                    </div>
                </div>
                <button type="button" class="sub-sync-btn" onclick="runSync('sync_student')">
                    Sync Student Sheet Only
                </button>
            </div>

            <!-- Faculty Timetable Card -->
            <div class="sheet-card">
                <div>
                    <div class="sheet-card-title">
                        <span>👨‍🏫 Faculty Timetable</span>
                    </div>
                    <div class="sheet-meta">
                        <strong>Current File:</strong> <?php echo htmlspecialchars($ftCurrentFile ?: 'None'); ?><br>
                        <strong>Last Updated:</strong> <?php echo $ftMTime; ?><br>
                        <strong>Size:</strong> <?php echo $ftSize; ?><br>
                        <strong>Google Sheet ID:</strong> <code><?php echo $DEFAULT_FACULTY_SHEET_ID; ?></code>
                    </div>
                </div>
                <button type="button" class="sub-sync-btn" onclick="runSync('sync_faculty')">
                    Sync Faculty Sheet Only
                </button>
            </div>
        </div>

        <div id="liveResultBox" class="result-box"></div>

        <div class="perm-help-box">
            🔒 <strong>Google Sheets Privacy Note:</strong><br>
            Your Google Sheets <strong>STAY 100% PRIVATE</strong> (Restricted / GYANMANJARI INNOVATIVE UNIVERSITY).<br>
            The deployed Google Apps Script Web App securely reads the private sheet using your Editor account permissions and sends the updated timetable Excel file directly to your website.
        </div>

        <div class="nav-links">
            <a href="student-timetable" class="nav-btn">
                🎓 View Student Timetable
            </a>
            <a href="timetable" class="nav-btn">
                👨‍🏫 View Faculty Timetable
            </a>
            <a href="./" class="nav-btn">
                🏠 Back to Portal
            </a>
        </div>
    </div>

    <script src="<?php echo v_asset('assets/js/timetableSync.js'); ?>"></script>
    <script>
        function runSync(actionType, providedPassword) {
            const btn = document.getElementById('mainSyncBtn');
            const spinner = document.getElementById('mainSpinner');
            const icon = document.getElementById('mainIcon');
            const btnText = document.getElementById('mainBtnText');
            const liveBox = document.getElementById('liveResultBox');

            btn.disabled = true;
            spinner.style.display = 'block';
            icon.style.display = 'none';
            btnText.textContent = 'Downloading & Updating Timetables...';

            let postBody = 'action=' + encodeURIComponent(actionType);
            if (providedPassword) {
                postBody += '&password=' + encodeURIComponent(providedPassword);
            }

            fetch('sync-timetables.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: postBody
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                spinner.style.display = 'none';
                icon.style.display = 'block';
                btnText.textContent = '1-Click Download & Update All Timetables';

                if (data.auth_required) {
                    promptSyncPasswordModal((validPwd) => {
                        runSync(actionType, validPwd);
                    });
                    return;
                }

                liveBox.classList.add('show');
                
                let html = `<span class="status-badge ${data.success ? 'success' : 'error'}">` +
                           `${data.success ? '✓ Download & Sync Complete!' : '⚠️ Sync Completed with Issues'}` +
                           `</span><div style="font-size: 14.5px; line-height: 1.8; margin-top: 10px;">`;
                
                let showFixGuide = false;

                if (data.results) {
                    if (data.results.student) {
                        const st = data.results.student;
                        html += `<strong>Student Timetable:</strong> <span style="color:${st.success ? '#34d399' : '#f87171'}">${st.message}</span>`;
                        if (st.size) html += ` <code>(${st.size})</code>`;
                        html += `<br>`;
                        if (!st.success) showFixGuide = true;
                    }
                    if (data.results.faculty) {
                        const ft = data.results.faculty;
                        html += `<strong>Faculty Timetable:</strong> <span style="color:${ft.success ? '#34d399' : '#f87171'}">${ft.message}</span>`;
                        if (ft.size) html += ` <code>(${ft.size})</code>`;
                        html += `<br>`;
                        if (!ft.success) showFixGuide = true;
                    }
                }

                html += `</div>`;
                liveBox.innerHTML = html;
            })
            .catch(err => {
                btn.disabled = false;
                spinner.style.display = 'none';
                icon.style.display = 'block';
                btnText.textContent = '1-Click Download & Update All Timetables';

                liveBox.classList.add('show');
                liveBox.innerHTML = `<span class="status-badge error">❌ Request Failed</span><p style="color:#f87171; margin-top:10px;">Error communicating with server: ${err.message}</p>`;
            });
        }
    </script>
</body>
</html>
