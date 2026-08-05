<?php 
require_once 'auto-cache-bust.php';

// Public access: session start and security headers without auth check redirection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

$studentUploadDir = __DIR__ . '/uploads/student_timetable/';
if (!is_dir($studentUploadDir)) {
    @mkdir($studentUploadDir, 0777, true);
}

// Function to pre-parse student timetable Excel sheet into server-side JSON cache
function parseExcelToTtCache($excelFile, $cacheJsonPath) {
    if (!file_exists($excelFile)) return false;
    
    $scratchDir = __DIR__ . '/scratch';
    if (!is_dir($scratchDir)) {
        @mkdir($scratchDir, 0777, true);
    }
    
    $tempZip = $scratchDir . '/temp_st_' . md5($excelFile) . '.zip';
    $unzipDir = $scratchDir . '/unzipped_st_' . md5($excelFile);

    if (is_dir($unzipDir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($unzipDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }
        @rmdir($unzipDir);
    }
    @mkdir($unzipDir, 0777, true);

    @copy($excelFile, $tempZip);

    $unzipped = false;
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive;
        if ($zip->open($tempZip) === TRUE) {
            $zip->extractTo($unzipDir);
            $zip->close();
            $unzipped = true;
        }
    }

    if (!$unzipped) {
        $winZip = str_replace('/', '\\', $tempZip);
        $winUnzipDir = str_replace('/', '\\', $unzipDir);
        $cmd = 'powershell -Command "Expand-Archive -Force -Path \"' . $winZip . '\" -DestinationPath \"' . $winUnzipDir . '\""';
        exec($cmd, $output, $return_var);
        if ($return_var === 0) {
            $unzipped = true;
        }
    }

    @unlink($tempZip);

    if (!$unzipped) {
        return false;
    }

    // 1. Shared Strings
    $sharedStrings = [];
    $stringsFile = $unzipDir . '/xl/sharedStrings.xml';
    if (file_exists($stringsFile)) {
        $stringsXML = file_get_contents($stringsFile);
        $xml = simplexml_load_string($stringsXML);
        if ($xml && isset($xml->si)) {
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string)$si->t;
                } else {
                    $parts = [];
                    foreach ($si->r as $r) {
                        $parts[] = (string)$r->t;
                    }
                    $sharedStrings[] = implode("", $parts);
                }
            }
        }
    }

    // 2. Workbook Relations
    $relsFile = $unzipDir . '/xl/_rels/workbook.xml.rels';
    $xmlRels = file_exists($relsFile) ? simplexml_load_string(file_get_contents($relsFile)) : null;
    $rels = [];
    if ($xmlRels) {
        foreach ($xmlRels->Relationship as $r) {
            $rels[(string)$r['Id']] = (string)$r['Target'];
        }
    }

    // 3. Workbook Sheets
    $workbookFile = $unzipDir . '/xl/workbook.xml';
    $xmlWorkbook = file_exists($workbookFile) ? simplexml_load_string(file_get_contents($workbookFile)) : null;
    $sheets = [];
    if ($xmlWorkbook) {
        foreach ($xmlWorkbook->sheets->sheet as $s) {
            $sheetName = trim((string)$s['name']);
            $rId = (string)$s->attributes('r', true)->id;
            $targetFile = $rels[$rId] ?? '';
            $sheets[$sheetName] = 'xl/' . $targetFile;
        }
    }

    $colLetterToNum = function($col) {
        $col = strtoupper($col);
        $len = strlen($col);
        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $num - 1;
    };

    $getCellCoords = function($ref) use ($colLetterToNum) {
        preg_match('/^([A-Z]+)([0-9]+)$/i', $ref, $matches);
        $col = $colLetterToNum($matches[1] ?? 'A');
        $row = intval($matches[2] ?? 1) - 1;
        return [$col, $row];
    };

    $result = [
        'SheetNames' => array_keys($sheets),
        'SheetsData' => [],
        'MergesData' => []
    ];

    foreach ($sheets as $sheetName => $relPath) {
        $sheetFile = $unzipDir . '/' . $relPath;
        if (!file_exists($sheetFile)) {
            $result['SheetsData'][$sheetName] = [];
            $result['MergesData'][$sheetName] = null;
            continue;
        }

        $sheetXML = file_get_contents($sheetFile);
        $xml = simplexml_load_string($sheetXML);
        if (!$xml) {
            $result['SheetsData'][$sheetName] = [];
            $result['MergesData'][$sheetName] = null;
            continue;
        }

        $rowsData = [];
        $maxRow = 0;
        $maxCol = 0;

        if (isset($xml->sheetData->row)) {
            foreach ($xml->sheetData->row as $row) {
                foreach ($row->c as $c) {
                    $ref = (string)$c['r'];
                    list($cIdx, $rIdx) = $getCellCoords($ref);

                    $val = "";
                    if (isset($c->v)) {
                        $v = (string)$c->v;
                        if (isset($c['t']) && (string)$c['t'] === 's') {
                            $val = $sharedStrings[intval($v)] ?? $v;
                        } else {
                            $val = $v;
                        }
                    } else if (isset($c->is->t)) {
                        $val = (string)$c->is->t;
                    }

                    if (!isset($rowsData[$rIdx])) {
                        $rowsData[$rIdx] = [];
                    }
                    $rowsData[$rIdx][$cIdx] = $val;

                    if ($rIdx > $maxRow) $maxRow = $rIdx;
                    if ($cIdx > $maxCol) $maxCol = $cIdx;
                }
            }
        }

        $denseMatrix = [];
        for ($r = 0; $r <= $maxRow; $r++) {
            $rowArr = [];
            for ($c = 0; $c <= $maxCol; $c++) {
                $rowArr[] = isset($rowsData[$r][$c]) ? $rowsData[$r][$c] : "";
            }
            $denseMatrix[] = $rowArr;
        }

        $result['SheetsData'][$sheetName] = $denseMatrix;

        $mergesArr = [];
        if (isset($xml->mergeCells->mergeCell)) {
            foreach ($xml->mergeCells->mergeCell as $mc) {
                $ref = (string)$mc['ref'];
                if (strpos($ref, ':') !== false) {
                    list($startRef, $endRef) = explode(':', $ref);
                    list($sC, $sR) = $getCellCoords($startRef);
                    list($eC, $eR) = $getCellCoords($endRef);
                    $mergesArr[] = [
                        's' => ['r' => $sR, 'c' => $sC],
                        'e' => ['r' => $eR, 'c' => $eC]
                    ];
                }
            }
        }

        $result['MergesData'][$sheetName] = !empty($mergesArr) ? $mergesArr : null;
    }

    if (is_dir($unzipDir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($unzipDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }
        @rmdir($unzipDir);
    }

    $jsonStr = json_encode($result, JSON_UNESCAPED_UNICODE);
    file_put_contents($cacheJsonPath, $jsonStr);
    return true;
}

// Handle file upload if POST request contains uploaded file
$uploadMessage = '';
$uploadSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['timetable_file'])) {
    $file = $_FILES['timetable_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, ['xlsx', 'xls'])) {
        // Clean old files in student_timetable directory
        $oldFiles = glob($studentUploadDir . '*.{xlsx,xls}', GLOB_BRACE);
        foreach ($oldFiles as $oldFile) {
            @unlink($oldFile);
        }
        @unlink($studentUploadDir . 'student_timetable_cache.json');
        
        $targetPath = $studentUploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $uploadSuccess = true;
            $uploadMessage = "File uploaded successfully!";
            parseExcelToTtCache($targetPath, $studentUploadDir . 'student_timetable_cache.json');
        } else {
            $uploadMessage = "Error saving uploaded file.";
        }
    } else {
        $uploadMessage = "Invalid file format. Please upload an Excel (.xlsx or .xls) file.";
    }
}

// Check for existing files in uploads/student_timetable/
$excelFiles = glob($studentUploadDir . '*.{xlsx,xls}', GLOB_BRACE);
$excelExists = !empty($excelFiles);
$activeExcelFile = $excelExists ? basename($excelFiles[0]) : '';
$activeExcelPath = $excelExists ? 'uploads/student_timetable/' . basename($excelFiles[0]) : '';
$excelMTime = ($excelExists && file_exists($studentUploadDir . $activeExcelFile)) ? filemtime($studentUploadDir . $activeExcelFile) : 0;

$cacheJsonPath = $studentUploadDir . 'student_timetable_cache.json';
$preloadedJsonData = null;

if ($excelExists) {
    $fullExcelPath = $studentUploadDir . $activeExcelFile;
    if (!file_exists($cacheJsonPath) || filemtime($cacheJsonPath) < filemtime($fullExcelPath)) {
        parseExcelToTtCache($fullExcelPath, $cacheJsonPath);
    }
    if (file_exists($cacheJsonPath)) {
        $preloadedJsonData = file_get_contents($cacheJsonPath);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Department of CE & IT — Student Class Timetable Viewer for weekly academic schedules.">
    <title>Student Timetable — CE & IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts & Preconnect for Fast Loading -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kameron:wght@400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto+Slab:wght@100..900&display=swap" rel="stylesheet">

    <!-- Stylesheets with Auto Cache Busting -->
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/portal.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/faculty.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/timetable.css'); ?>">
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">

    <style>
        /* Modern Dark Glassmorphic Theme (Matches Website Aesthetic) */
        .student-tt-card,
        .student-tt-card * {
            font-family: 'Kameron', Georgia, serif;
        }

        .student-tt-card {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 41, 59, 0.65) 100%);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(24px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            margin-top: 24px;
            overflow-x: auto;
            color: #f8fafc;
            transition: all 0.3s ease;
        }

        .student-tt-card table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background-color: rgba(11, 21, 48, 0.45);
            color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .student-tt-card th, 
        .student-tt-card td {
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 14px;
            line-height: 1.5;
        }

        .student-tt-card .bold {
            font-weight: 700;
        }

        .student-tt-card .university-header {
            font-family: 'Kameron', Georgia, serif;
            font-size: 21px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 14px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.8) 50%, rgba(15, 23, 42, 0.95) 100%);
            color: #ffffff;
            border-bottom: 2px solid rgba(56, 189, 248, 0.4);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .student-tt-card .institute-header {
            font-family: 'Kameron', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            padding: 9px;
            background: rgba(15, 23, 42, 0.7);
            color: #38bdf8;
            letter-spacing: 0.5px;
        }

        .student-tt-card .title-header {
            font-family: 'Kameron', Georgia, serif;
            font-size: 17px;
            font-weight: 700;
            background: rgba(30, 41, 59, 0.75);
            color: #fca5a5;
            padding: 10px;
            letter-spacing: 0.5px;
        }

        .student-tt-card .header-info-row td {
            background: rgba(15, 23, 42, 0.5);
            color: #e2e8f0;
            font-weight: 600;
            font-size: 13.5px;
        }

        .student-tt-card .days-header-row td {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%);
            color: #38bdf8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 13.5px;
            padding: 12px 14px;
            border-bottom: 2px solid rgba(56, 189, 248, 0.35);
        }

        .student-tt-card .recess-row {
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.18) 0%, rgba(245, 158, 11, 0.28) 50%, rgba(217, 119, 6, 0.18) 100%) !important;
            color: #fbbf24 !important;
            border: 1px solid rgba(251, 191, 36, 0.35) !important;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px !important;
        }

        .student-tt-card .logo-cell {
            width: 170px;
            height: 135px;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            background: rgba(11, 21, 48, 0.6);
        }
        
        .student-tt-card .logo-cell img {
            width: 100%;
            height: 100%;
            max-height: 130px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
        }

        .student-tt-card .logo-fallback {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #0b1530 0%, #1e293b 100%);
            color: #38bdf8;
            border-radius: 50%;
            font-weight: 800;
            font-size: 22px;
            margin: 0 auto;
            border: 2px solid rgba(56, 189, 248, 0.4);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.25);
        }

        /* Class Slot Badges (Vertical Stack with Glow Effects) */
        .slot-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.3) 0%, rgba(15, 23, 42, 0.6) 100%);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #e0f2fe;
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 600;
            font-size: 13.5px;
            line-height: 1.35;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transition: all 0.25s ease;
        }

        .slot-pill:hover {
            transform: translateY(-2px);
            border-color: rgba(56, 189, 248, 0.6);
            box-shadow: 0 6px 16px rgba(56, 189, 248, 0.35);
        }

        .slot-pill .fac-init {
            display: inline-block;
            color: #fb7185;
            font-weight: 800;
            font-size: 12px;
            margin-top: 3px;
        }

        .slot-pill .room-tag {
            display: inline-block;
            color: #38bdf8;
            font-weight: 600;
            font-size: 11.5px;
            margin-left: 3px;
        }

        /* Bottom Details Cards */
        .student-tt-card .details-section {
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .student-tt-card .details-column {
            flex: 1;
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: 4px solid #10b981;
            border-radius: 14px;
            padding: 18px;
            line-height: 1.65;
            font-size: 13px;
            color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            transition: all 0.25s ease;
        }

        .student-tt-card .details-column:nth-child(2) {
            border-left-color: #f43f5e;
        }

        .student-tt-card .details-column:nth-child(3) {
            border-left-color: #38bdf8;
        }

        .student-tt-card .details-column:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .student-tt-card .details-column strong {
            color: #34d399;
            font-size: 13.5px;
            display: inline-block;
            margin-bottom: 6px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Signatures Section */
        .student-tt-card .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #f8fafc;
            gap: 15px;
        }

        .student-tt-card .signatures div {
            width: 23%;
            border: none;
            border-top: 1.5px dashed rgba(255, 255, 255, 0.35);
            border-radius: 0;
            padding-top: 10px;
            background: transparent;
            padding-bottom: 8px;
        }

        .student-tt-card .signatures span {
            display: block;
            font-weight: 400;
            font-size: 11.5px;
            margin-top: 4px;
            color: #94a3b8;
        }

        /* Toolbar Controls */
        .action-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .action-toolbar-right {
            display: flex;
            gap: 10px;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }

        /* ── Custom Screen Responsive Styles ── */
        .controls-card.empty-state-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
            gap: 20px;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 54px;
        }

        .empty-state-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 800;
            color: #f87171;
            margin: 0;
        }

        .empty-state-desc {
            color: #94a3b8;
            font-size: 14.5px;
            max-width: 560px;
            line-height: 1.6;
            margin: 0;
        }

        .upload-form {
            margin-top: 10px;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .upload-btn {
            cursor: pointer;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25) !important;
        }

        .upload-btn:hover {
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35) !important;
        }

        .student-select-wrap {
            position: relative;
            width: 100%;
        }

        .student-select-wrap .custom-form-select {
            width: 100%;
            height: 46px;
            background: rgba(15, 23, 42, 0.6);
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 0 42px 0 16px;
            font-size: 14px;
            font-weight: 600;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .student-select-wrap .custom-form-select:hover {
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.25);
        }

        /* Mobile Layout Styling */
        .desktop-timetable-view {
            width: 100%;
        }

        .mobile-timetable-view {
            display: none;
            width: 100%;
        }

        .mobile-meta-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .meta-badge {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            flex: 1 1 calc(33.333% - 10px);
            min-width: 120px;
        }

        .mobile-tabs-container {
            display: flex;
            overflow-x: auto;
            gap: 8px;
            padding: 4px 0 12px;
            margin-bottom: 20px;
            scrollbar-width: none; /* Hide scrollbar for Firefox */
            -ms-overflow-style: none; /* Hide scrollbar for IE/Edge */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mobile-tabs-container::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome/Safari/Opera */
        }

        .day-tab-btn {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            flex: 1;
            text-align: center;
            white-space: nowrap;
        }

        .day-tab-btn:hover {
            color: #ffffff;
            border-color: rgba(56, 189, 248, 0.4);
            background: rgba(56, 189, 248, 0.1);
        }

        .day-tab-btn.active {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-color: #38bdf8;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.35);
        }

        .mobile-slots-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .mobile-slot-card {
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.65) 0%, rgba(30, 41, 59, 0.55) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 14px 18px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.25s ease;
        }

        .mobile-slot-card:hover {
            transform: translateY(-2px);
            border-color: rgba(56, 189, 248, 0.3);
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.15);
        }

        .mobile-slot-time {
            width: 90px;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 700;
            color: #38bdf8;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding-right: 12px;
            margin-right: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.3;
        }

        .mobile-slot-details {
            flex-grow: 1;
        }

        .mobile-slot-subject {
            font-size: 14.5px;
            font-weight: 600;
            color: #f8fafc;
            line-height: 1.4;
        }

        .mobile-slot-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
        }

        .mobile-slot-faculty {
            color: #fb7185;
            font-size: 12px;
            font-weight: 700;
            background: rgba(251, 113, 133, 0.1);
            padding: 2px 8px;
            border-radius: 6px;
        }

        .mobile-slot-room {
            color: #38bdf8;
            font-size: 12px;
            font-weight: 600;
            background: rgba(56, 189, 248, 0.1);
            padding: 2px 8px;
            border-radius: 6px;
        }

        .mobile-recess-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, rgba(217, 119, 6, 0.1) 0%, rgba(245, 158, 11, 0.15) 50%, rgba(217, 119, 6, 0.1) 100%);
            border: 1px solid rgba(251, 191, 36, 0.2);
            color: #fbbf24;
            padding: 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            margin: 6px 0;
        }

        .mobile-empty-state {
            text-align: center;
            padding: 30px 16px;
            color: #94a3b8;
            font-size: 14px;
            font-style: italic;
            background: rgba(15, 23, 42, 0.3);
            border-radius: 14px;
            border: 1px dashed rgba(255, 255, 255, 0.08);
        }

        /* ══════════════════════════════════════════════════════════
           Light Theme Specific Overrides for Student Timetable
           ══════════════════════════════════════════════════════════ */
        html[data-theme="light"] .student-tt-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(20px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.06), 0 2px 6px rgba(0, 0, 0, 0.03);
            color: #0f172a;
        }

        html[data-theme="light"] .controls-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        html[data-theme="light"] .student-select-wrap .custom-form-select {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .student-select-wrap .custom-form-select:hover,
        html[data-theme="light"] .student-select-wrap .custom-form-select:focus {
            border-color: #c0392b;
            box-shadow: 0 0 10px rgba(192, 57, 43, 0.15);
        }

        html[data-theme="light"] body.ce-active .student-select-wrap .custom-form-select:hover,
        html[data-theme="light"] body.ce-active .student-select-wrap .custom-form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 10px rgba(37, 99, 235, 0.15);
        }

        html[data-theme="light"] .student-select-wrap .select-arrow {
            color: #475569;
        }

        html[data-theme="light"] .student-tt-card table {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .student-tt-card th, 
        html[data-theme="light"] .student-tt-card td {
            border: 1px solid #cbd5e1;
            color: #0f172a;
        }

        html[data-theme="light"] .student-tt-card .university-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #ffffff;
            border-bottom: 2px solid #0284c7;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        html[data-theme="light"] .student-tt-card .institute-header {
            background: #f0f9ff;
            color: #0284c7;
            border-bottom: 1px solid #e0f2fe;
        }

        html[data-theme="light"] .student-tt-card .title-header {
            background: #fef2f2;
            color: #dc2626;
            border-bottom: 1px solid #fee2e2;
        }

        html[data-theme="light"] .student-tt-card .header-info-row td {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        html[data-theme="light"] .student-tt-card .days-header-row td {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #0f172a;
            border: 1px solid #cbd5e1;
            border-bottom: 2px solid #94a3b8;
        }

        html[data-theme="light"] .student-tt-card .recess-row {
            background: linear-gradient(90deg, #fef3c7 0%, #fde68a 50%, #fef3c7 100%) !important;
            color: #92400e !important;
            border: 1px solid #f59e0b !important;
        }

        html[data-theme="light"] .student-tt-card .logo-cell {
            background: #f8fafc;
        }

        html[data-theme="light"] .slot-pill {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #cbd5e1;
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .slot-pill:hover {
            border-color: #0284c7;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
        }

        html[data-theme="light"] .slot-pill .fac-init {
            color: #e11d48;
        }

        html[data-theme="light"] .slot-pill .room-tag {
            color: #0284c7;
        }

        html[data-theme="light"] .student-tt-card .details-section {
            border-top: 1px solid #e2e8f0;
        }

        html[data-theme="light"] .student-tt-card .details-column {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #334155;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(1) {
            border-left-color: #10b981;
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(1) strong {
            color: #047857;
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(2) {
            border-left-color: #f43f5e;
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(2) strong {
            color: #e11d48;
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(3) {
            border-left-color: #0284c7;
        }

        html[data-theme="light"] .student-tt-card .details-column:nth-child(3) strong {
            color: #0369a1;
        }

        html[data-theme="light"] .student-tt-card .signatures {
            color: #0f172a;
        }

        html[data-theme="light"] .student-tt-card .signatures div {
            border-top: 1.5px dashed #64748b;
        }

        html[data-theme="light"] .student-tt-card .signatures span {
            color: #64748b;
        }

        html[data-theme="light"] .meta-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        html[data-theme="light"] .mobile-tabs-container {
            border-bottom: 1px solid #e2e8f0;
        }

        html[data-theme="light"] .day-tab-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        html[data-theme="light"] .day-tab-btn:hover {
            color: #0284c7;
            border-color: #38bdf8;
            background: #f0f9ff;
        }

        html[data-theme="light"] .day-tab-btn.active {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-color: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }

        html[data-theme="light"] .mobile-slot-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .mobile-slot-card:hover {
            border-color: #38bdf8;
            box-shadow: 0 6px 16px rgba(56, 189, 248, 0.15);
        }

        html[data-theme="light"] .mobile-slot-time {
            color: #0284c7;
            border-right: 1px solid #e2e8f0;
        }

        html[data-theme="light"] .mobile-slot-subject {
            color: #0f172a;
        }

        html[data-theme="light"] .mobile-slot-faculty {
            color: #e11d48;
            background: #ffe4e6;
        }

        html[data-theme="light"] .mobile-slot-room {
            color: #0284c7;
            background: #e0f2fe;
        }

        html[data-theme="light"] .mobile-recess-divider {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        html[data-theme="light"] .mobile-empty-state {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #64748b;
        }

        html[data-theme="light"] .controls-card.empty-state-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        html[data-theme="light"] .empty-state-title {
            color: #dc2626;
        }

        html[data-theme="light"] .empty-state-desc {
            color: #475569;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .desktop-timetable-view {
                display: none;
            }
            .mobile-timetable-view {
                display: block;
            }
            .controls-card {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 16px !important;
                padding: 18px 16px !important;
            }
            .selector-group {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
                max-width: 100% !important;
            }
            .student-tt-card {
                padding: 16px !important;
                border-radius: 16px !important;
                margin-top: 16px !important;
            }
            .student-tt-card .details-section {
                flex-direction: column !important;
                gap: 12px !important;
                padding-top: 16px !important;
                margin-top: 20px !important;
            }
            .student-tt-card .details-column {
                border-left-width: 3px !important;
                padding: 12px 14px !important;
            }
            .student-tt-card .details-column strong {
                font-size: 12.5px !important;
            }
            
            .action-toolbar-right {
                width: 100%;
            }
            .print-btn {
                width: 100%;
                justify-content: center;
                height: 46px;
            }
        }

        /* Printable Styles for Physical Printing (Single Landscape A4 Page - No Scrollbars) */
        @media print {
            .desktop-timetable-view {
                display: block !important;
            }
            .mobile-timetable-view {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 5mm 8mm 10mm 8mm;
            }

            *, *::before, *::after {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                box-sizing: border-box !important;
                overflow: visible !important;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 10.5px !important;
                overflow: visible !important;
            }

            /* Hide all interactive, header, footer & floating nav buttons */
            .particles, .orb, .rp-header, .controls-card, .action-toolbar, .rp-footer, 
            .fab-nav, .fab-btn, .fab-menu, .fab-overlay, #fabNav, #fabBtn, #fabOverlay, 
            .fab-container, .navbar {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                width: 0 !important;
                height: 0 !important;
            }

            .timetable-container {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            .student-tt-card {
                width: 100% !important;
                height: auto !important;
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                backdrop-filter: none !important;
                overflow: visible !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .student-tt-card table {
                width: 100% !important;
                border-collapse: collapse !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: 2px solid #000000 !important;
                overflow: visible !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .student-tt-card th, 
            .student-tt-card td {
                border: 1px solid #000000 !important;
                color: #000000 !important;
                padding: 4px 6px !important;
                font-size: 10.5px !important;
                vertical-align: middle !important;
            }

            .student-tt-card .logo-cell {
                width: 130px !important;
                height: 85px !important;
                padding: 2px !important;
                background: transparent !important;
            }

            .student-tt-card .logo-cell img {
                max-height: 80px !important;
                filter: none !important;
            }

            .student-tt-card .university-header {
                font-size: 16px !important;
                padding: 5px !important;
                background: #f1f5f9 !important;
                color: #000000 !important;
                border-bottom: 2px solid #000000 !important;
                text-shadow: none !important;
            }

            .student-tt-card .institute-header {
                font-size: 12.5px !important;
                padding: 3px !important;
                background: #f8fafc !important;
                color: #000000 !important;
            }

            .student-tt-card .title-header {
                font-size: 13px !important;
                padding: 4px !important;
                background: #f1f5f9 !important;
                color: #000000 !important;
            }

            .student-tt-card .header-info-row td {
                font-size: 10.5px !important;
                padding: 4px !important;
                background: #ffffff !important;
                color: #000000 !important;
            }

            .student-tt-card .days-header-row td {
                font-size: 11px !important;
                padding: 5px !important;
                background: #e2e8f0 !important;
                color: #000000 !important;
            }

            .student-tt-card .recess-row {
                background: #fef3c7 !important;
                color: #92400e !important;
                font-size: 10px !important;
                padding: 3px !important;
                border: 1px solid #000000 !important;
            }

            .slot-pill {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                background: transparent !important;
                border: none !important;
                color: #000000 !important;
                padding: 0 !important;
                font-size: 10.5px !important;
                box-shadow: none !important;
                text-align: center !important;
            }

            .slot-pill .fac-init {
                display: inline-block !important;
                color: #000000 !important;
                font-weight: bold !important;
                margin-top: 2px !important;
            }

            .slot-pill .room-tag {
                display: inline-block !important;
                color: #333333 !important;
                font-weight: normal !important;
                margin-left: 2px !important;
            }

            .student-tt-card .details-section {
                display: flex !important;
                margin-top: 10px !important;
                margin-bottom: 15px !important;
                gap: 10px !important;
                padding-top: 6px !important;
                border-top: 1px solid #000000 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .student-tt-card .details-column {
                background: #ffffff !important;
                border: 1px solid #000000 !important;
                border-left: 3px solid #000000 !important;
                color: #000000 !important;
                padding: 6px 8px !important;
                font-size: 9.5px !important;
                line-height: 1.35 !important;
            }

            .student-tt-card .details-column strong {
                color: #000000 !important;
                font-size: 10.5px !important;
                text-decoration: underline !important;
            }

            .student-tt-card .signatures {
                display: flex !important;
                justify-content: space-between !important;
                margin-top: 25px !important;
                margin-bottom: 12px !important;
                font-size: 10.5px !important;
                font-weight: bold !important;
                color: #000000 !important;
                gap: 15px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .student-tt-card .signatures div {
                border: none !important;
                border-top: 1.5px dashed #000000 !important;
                border-radius: 0 !important;
                background: transparent !important;
                color: #000000 !important;
                padding-top: 8px !important;
                padding-bottom: 0 !important;
            }

            .student-tt-card .signatures span {
                color: #333333 !important;
                font-size: 9px !important;
            }
        }
    </style>
</head>

<body>

    <?php include_once 'theme-toggle.php'; ?>

    <!-- ░░ Particles ░░ -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Glowing Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>

    <!-- Page Header -->
    <header class="rp-header">
        <div class="rp-header-inner container">
            <a href="./" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Portal
            </a>

            <div class="rp-header-center">
                <div class="rp-dept-badge">
                    <span class="rp-badge-dot"></span>
                    <span id="rpDeptBadgeText">Department of Information Technology</span>
                </div>
                <h1 class="rp-title" id="pageTitleMain">Student Class Timetable Module</h1>
            </div>

            <span class="portal-badge" id="portalBadge">Student Timetable</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="timetable-container container">

        <?php if (!$excelExists): ?>
            <!-- Clean Empty State when no Excel file is uploaded -->
            <div class="controls-card empty-state-card">
                <div class="empty-state-icon">📅</div>
                <h2 class="empty-state-title">No Student Timetable File Found</h2>
                <p class="empty-state-desc">
                    No Excel schedule file was found inside <code>uploads/student_timetable/</code>.<br>
                    Please place your student timetable Excel file into <code>uploads/student_timetable/</code> or upload it using the button below.
                </p>
                <form method="POST" enctype="multipart/form-data" class="upload-form">
                    <input type="file" name="timetable_file" accept=".xlsx, .xls" required style="display: none;" id="excelFileInput" onchange="this.form.submit()">
                    <label for="excelFileInput" class="print-btn upload-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                        Upload Timetable Excel (.xlsx)
                    </label>
                </form>
            </div>

        <?php else: ?>

            <!-- Combined Controls Card (Sheet Selector + Print Button) -->
            <div class="controls-card">
                <div class="selector-group">
                    <span class="selector-label">Select Timetable Sheet:</span>
                    <div class="select-wrap student-select-wrap">
                        <select id="sheetSelect" class="custom-form-select">
                            <option value="">Loading Timetable Sheets...</option>
                        </select>
                        <span class="select-arrow">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="action-toolbar-right">
                    <button type="button" class="print-btn" onclick="window.print();">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Print / Download PDF
                    </button>
                </div>
            </div>

            <!-- Student Timetable Printable Card -->
            <div class="student-tt-card" id="printableTimetableCard">
                
                <!-- Desktop Timetable View -->
                <div class="desktop-timetable-view">
                    <table>
                        <!-- Header Rows -->
                        <tr>
                            <td colspan="8" class="title-header" id="stTitleHeader">Time Table</td>
                        </tr>
                        <tr class="header-info-row bold">
                            <td colspan="3" id="stSemesterVal">Semester : -</td>
                            <td colspan="2" id="stClassroomVal">Class Room : -</td>
                            <td colspan="3" id="stWefVal">W.E.F.: -</td>
                        </tr>

                        <!-- Days and Columns -->
                        <thead id="stTableHead">
                            <tr class="days-header-row bold">
                                <td width="5%">SR. NO.</td>
                                <td width="15%">TIME</td>
                                <td width="13%">MON</td>
                                <td width="13%">TUE</td>
                                <td width="13%">WED</td>
                                <td width="13%">THU</td>
                                <td width="13%">FRI</td>
                                <td width="15%">SAT</td>
                            </tr>
                        </thead>

                        <!-- Schedule Body -->
                        <tbody id="stTableBody">
                            <!-- Loaded dynamically from Excel sheet in uploads/student_timetable/ -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Timetable View -->
                <div class="mobile-timetable-view">
                    <!-- Mobile metadata display -->
                    <div class="mobile-meta-container">
                        <div class="meta-badge" id="mSemesterVal">Semester : -</div>
                        <div class="meta-badge" id="mClassroomVal">Class Room : -</div>
                        <div class="meta-badge" id="mWefVal">W.E.F.: -</div>
                    </div>
                    
                    <!-- Mobile Day Selector Tab Bar -->
                    <div class="mobile-tabs-container">
                        <button type="button" class="day-tab-btn" data-day="MON">Mon</button>
                        <button type="button" class="day-tab-btn" data-day="TUE">Tue</button>
                        <button type="button" class="day-tab-btn" data-day="WED">Wed</button>
                        <button type="button" class="day-tab-btn" data-day="THU">Thu</button>
                        <button type="button" class="day-tab-btn" data-day="FRI">Fri</button>
                        <button type="button" class="day-tab-btn" data-day="SAT">Sat</button>
                    </div>

                    <!-- Mobile dynamic slot list -->
                    <div class="mobile-slots-list" id="mSlotsList">
                        <div class="mobile-empty-state">Loading timetable slots...</div>
                    </div>
                </div>

                <!-- Subject and Faculty Details -->
                <div class="details-section" id="stDetailsSection">
                    <div class="details-column" id="stSubjectCol">
                        <strong>Subject</strong><br>-
                    </div>
                    <div class="details-column" id="stFacultyCol">
                        <strong>Faculty Details</strong><br>-
                    </div>
                    <div class="details-column" id="stLocationCol">
                        <strong>Location</strong><br>-
                    </div>
                </div>

            </div>

        <?php endif; ?>

    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Navigation Hub (Bottom Right) -->
    <?php 
    $active_page = 'student-timetable';
    include 'fab-nav.php'; 
    ?>

    <!-- Data & Excel Parsing Libraries -->
    <?php if ($excelExists): ?>
    <script>
        const preloadedWorkbook = <?php echo !empty($preloadedJsonData) ? $preloadedJsonData : 'null'; ?>;
    </script>
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const excelPath = "<?php echo $activeExcelPath; ?>";
            const excelMTime = "<?php echo $excelMTime; ?>";
            const cacheKey = "st_tt_data_v3_" + excelMTime;

            const sheetSelect = document.getElementById("sheetSelect");

            const stTitleHeader = document.getElementById("stTitleHeader");
            const stSemesterVal = document.getElementById("stSemesterVal");
            const stClassroomVal = document.getElementById("stClassroomVal");
            const stWefVal = document.getElementById("stWefVal");
            const stTableBody = document.getElementById("stTableBody");
            const stSubjectCol = document.getElementById("stSubjectCol");
            const stFacultyCol = document.getElementById("stFacultyCol");
            const stLocationCol = document.getElementById("stLocationCol");

            const itBtn = document.getElementById("dept-it-btn");
            const ceBtn = document.getElementById("dept-ce-btn");
            const rpDeptBadgeText = document.getElementById("rpDeptBadgeText");
            const portalBadge = document.getElementById("portalBadge");

            let cachedWorkbook = preloadedWorkbook;

            // Fast instant load from preloaded object or sessionStorage fallback
            if (!cachedWorkbook) {
                try {
                    const stored = sessionStorage.getItem(cacheKey);
                    if (stored) {
                        cachedWorkbook = JSON.parse(stored);
                    }
                } catch(e) {}
            }

            if (cachedWorkbook && cachedWorkbook.SheetNames && cachedWorkbook.SheetNames.length > 0) {
                initTimetable(cachedWorkbook);
            } else {
                // Dynamic fallback if preloaded data is missing
                const script = document.createElement('script');
                script.src = "https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js";
                script.onload = () => {
                    fetch(excelPath)
                        .then(res => res.arrayBuffer())
                        .then(buffer => {
                            const data = new Uint8Array(buffer);
                            const wb = XLSX.read(data, { type: 'array' });
                            cachedWorkbook = {
                                SheetNames: wb.SheetNames,
                                SheetsData: {},
                                MergesData: {}
                            };
                            wb.SheetNames.forEach(sName => {
                                const sheet = wb.Sheets[sName];
                                cachedWorkbook.SheetsData[sName] = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: "" });
                                cachedWorkbook.MergesData[sName] = sheet['!merges'] || null;
                            });
                            try {
                                sessionStorage.setItem(cacheKey, JSON.stringify(cachedWorkbook));
                            } catch(e) {}
                            initTimetable(cachedWorkbook);
                        })
                        .catch(err => {
                            console.error("Error loading student timetable Excel:", err);
                        });
                };
                document.head.appendChild(script);
            }

            function initTimetable(wbData) {
                const allSheets = wbData.SheetNames;
                sheetSelect.innerHTML = "";

                if (!allSheets || allSheets.length === 0) {
                    sheetSelect.innerHTML = `<option value="">No sheets found in Excel file</option>`;
                    return;
                }

                const savedSheet = localStorage.getItem("gmiu_student_tt_sheet");
                let defaultSheet = allSheets[0];
                if (savedSheet && allSheets.includes(savedSheet)) {
                    defaultSheet = savedSheet;
                }

                allSheets.forEach((sName, index) => {
                    const option = document.createElement("option");
                    option.value = sName;
                    option.textContent = `${index + 1}. ${sName}`;
                    if (sName === defaultSheet) option.selected = true;
                    sheetSelect.appendChild(option);
                });

                renderSheet(defaultSheet);
            }

            // Sheet Selection Change Listener
            sheetSelect.addEventListener("change", (e) => {
                if (cachedWorkbook && e.target.value) {
                    try { localStorage.setItem("gmiu_student_tt_sheet", e.target.value); } catch(err) {}
                    renderSheet(e.target.value);
                }
            });

            // Render selected sheet contents from Excel file
            function renderSheet(sheetName) {
                if (!cachedWorkbook || !cachedWorkbook.SheetsData[sheetName]) return;

                const rows = cachedWorkbook.SheetsData[sheetName];
                const sheetMerges = cachedWorkbook.MergesData[sheetName];

                if (!rows || rows.length === 0) return;

                // Extract Metadata dynamically from top 8 rows of Excel sheet
                let title = "";
                let semester = "";
                let classroom = "";
                let wef = "";

                // Scan top 8 rows of sheet for metadata
                for (let i = 0; i < Math.min(8, rows.length); i++) {
                    const row = rows[i];
                    if (!row) continue;

                    for (let c = 0; c < row.length; c++) {
                        const cellVal = String(row[c] || "").trim();
                        if (!cellVal) continue;
                        const lower = cellVal.toLowerCase();

                        // Title match
                        if ((lower.includes("time table") || lower.includes("timetable")) && !title) {
                            title = cellVal;
                        }
                        // Semester match
                        if (lower.includes("semester") && !semester) {
                            semester = cellVal;
                        }
                        // Classroom match
                        if ((lower.includes("class room") || lower.includes("classroom") || lower.includes("room :") || lower.includes("room:")) && !classroom && !lower.includes("b11") && !lower.includes("x11") && !lower.includes("x22")) {
                            classroom = cellVal;
                        }
                        // W.E.F. match
                        if (lower.includes("w.e.f") && !wef) {
                            wef = cellVal;
                        }
                    }
                }

                // Format header values strictly from Excel sheet data 1:1
                stTitleHeader.textContent = title ? (String(title).toLowerCase().startsWith("time table") ? title : `Time Table: ${title}`) : `Time Table: ${sheetName}`;
                stSemesterVal.textContent = semester ? (String(semester).toLowerCase().startsWith("semester") ? semester : `Semester : ${semester}`) : "Semester : -";
                stClassroomVal.textContent = classroom ? (String(classroom).toLowerCase().startsWith("class room") || String(classroom).toLowerCase().startsWith("classroom") ? classroom : `Class Room : ${classroom}`) : "Class Room : -";
                stWefVal.textContent = wef ? (String(wef).toLowerCase().startsWith("w.e.f") ? wef : `W.E.F.: ${wef}`) : "W.E.F.: -";

                // Set mobile metadata as well
                const mSemesterVal = document.getElementById("mSemesterVal");
                const mClassroomVal = document.getElementById("mClassroomVal");
                const mWefVal = document.getElementById("mWefVal");
                if (mSemesterVal) mSemesterVal.textContent = semester ? (String(semester).toLowerCase().startsWith("semester") ? semester : `Semester : ${semester}`) : "Semester : -";
                if (mClassroomVal) mClassroomVal.textContent = classroom ? (String(classroom).toLowerCase().startsWith("class room") || String(classroom).toLowerCase().startsWith("classroom") ? classroom : `Class Room : ${classroom}`) : "Class Room : -";
                if (mWefVal) mWefVal.textContent = wef ? (String(wef).toLowerCase().startsWith("w.e.f") ? wef : `W.E.F.: ${wef}`) : "W.E.F.: -";

                // Locate header row (SR. NO. or TIME or MON)
                let headerIdx = -1;
                for (let i = 0; i < Math.min(10, rows.length); i++) {
                    if (!rows[i]) continue;
                    const rStr = rows[i].join(" ").toLowerCase();
                    if (rStr.includes("sr. no.") || rStr.includes("time") || rStr.includes("mon")) {
                        headerIdx = i;
                        break;
                    }
                }
                if (headerIdx === -1) headerIdx = 4; // Fallback

                // Build index of schedule row candidates
                const scheduleRowIndices = [];
                for (let i = headerIdx + 1; i < rows.length; i++) {
                    const row = rows[i];
                    if (!row || row.length === 0) continue;
                    const rowStr = row.join(" ").trim();
                    if (!rowStr) continue;
                    const lowerRowStr = rowStr.toLowerCase();
                    if (lowerRowStr.includes("total load") || 
                        lowerRowStr.includes("co-ordinator") || 
                        lowerRowStr.includes("subject") || 
                        lowerRowStr.includes("faculty") || 
                        lowerRowStr.includes("location") ||
                        lowerRowStr.includes("load distribution")) {
                        break;
                    }
                    scheduleRowIndices.push(i);
                }

                // Map merged cells (rowspans) from sheetMerges or consecutive empty slot detection
                const mergeGrid = {}; // key: "rowIdx,colIdx" -> { rowspan, skip }

                if (sheetMerges) {
                    sheetMerges.forEach(m => {
                        const startR = m.s.r;
                        const endR = m.e.r;
                        const startC = m.s.c;
                        const endC = m.e.c;

                        if (endR > startR) {
                            mergeGrid[`${startR},${startC}`] = { rowspan: endR - startR + 1 };
                            for (let r = startR + 1; r <= endR; r++) {
                                mergeGrid[`${r},${startC}`] = { skip: true };
                            }
                        }
                    });
                } else {
                    // Fallback automatic 2-hour slot detection: if slot 1 has a lecture and slot 2 in same block is empty/dash
                    for (let idx = 0; idx < scheduleRowIndices.length - 1; idx++) {
                        const currR = scheduleRowIndices[idx];
                        const nextR = scheduleRowIndices[idx + 1];

                        const currRow = rows[currR];
                        const nextRow = rows[nextR];

                        const isCurrRecess = currRow.join(" ").toLowerCase().includes("recess") || currRow.join(" ").toLowerCase().includes("break");
                        const isNextRecess = nextRow.join(" ").toLowerCase().includes("recess") || nextRow.join(" ").toLowerCase().includes("break");

                        if (!isCurrRecess && !isNextRecess) {
                            for (let c = 2; c <= 7; c++) {
                                const currVal = String(currRow[c] || "").trim();
                                const nextVal = String(nextRow[c] || "").trim();

                                if (currVal && (!nextVal || nextVal === "-")) {
                                    mergeGrid[`${currR},${c}`] = { rowspan: 2 };
                                    mergeGrid[`${nextR},${c}`] = { skip: true };
                                }
                            }
                        }
                    }
                }

                // Initialize mobile days data dictionary
                const daysData = {
                    'MON': [],
                    'TUE': [],
                    'WED': [],
                    'THU': [],
                    'FRI': [],
                    'SAT': []
                };

                // Render timetable schedule rows
                let bodyHtml = "";
                let srNo = 1;
                const subjectsSet = new Set();
                const facultyInitialsSet = new Set();

                scheduleRowIndices.forEach(i => {
                    const row = rows[i];
                    const rowStr = row.join(" ").trim();
                    const isRecess = rowStr.toLowerCase().includes("recess") || rowStr.toLowerCase().includes("break");

                    if (isRecess) {
                        const recessLabel = row.find(c => String(c).toLowerCase().includes("recess") || String(c).toLowerCase().includes("break")) || "RECESS";
                        bodyHtml += `
                            <tr class="recess-row">
                                <td colspan="8">${recessLabel}</td>
                            </tr>
                        `;

                        // Add recess to all mobile days
                        const timeCell = row[1] || row[0] || "RECESS";
                        const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                        dayNames.forEach(day => {
                            daysData[day].push({
                                isRecess: true,
                                label: recessLabel,
                                time: timeCell
                            });
                        });
                        return;
                    }

                    // Extract Time slot and Day columns
                    const timeCell = row[1] || row[0] || "";
                    if (!timeCell || (!String(timeCell).includes(":") && !String(timeCell).match(/\d{1,2}/))) return;

                    let rowHtml = `
                        <tr>
                            <td class="bold">${srNo++}</td>
                            <td class="bold">${timeCell}</td>
                    `;

                    // Day columns (columns 2 to 7 -> MON to SAT)
                    const dayNames = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                    for (let c = 2; c <= 7; c++) {
                        const dayName = dayNames[c - 2];
                        const cellKey = `${i},${c}`;
                        const mergeInfo = mergeGrid[cellKey] || {};

                        if (mergeInfo.skip) {
                            // Skip rendering cell because it is merged with the cell above
                            continue;
                        }

                        const rSpanAttr = mergeInfo.rowspan > 1 ? ` rowspan="${mergeInfo.rowspan}"` : '';
                        const cellVal = String(row[c] || "").trim();

                        if (cellVal) {
                            // Extract all faculty initials in parentheses for the legend set (e.g. PKG, JBS, SBC)
                            const facMatches = [...cellVal.matchAll(/\(([A-Z]{2,4})\)/gi)];
                            facMatches.forEach(m => facultyInitialsSet.add(m[1].toUpperCase()));

                            // Format multi-line cell content for display (preserve all lines & batches)
                            let formattedContent = cellVal
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/\r\n|\r|\n/g, "<br>");

                            // Highlight faculty initials in accent color
                            formattedContent = formattedContent.replace(/\(([A-Z]{2,4})\)/gi, '<span class="fac-init">($1)</span>');

                            // Highlight room codes in cyan/muted tag
                            formattedContent = formattedContent.replace(/\(([A-Z]{1,2}-\d{1,2})\)/gi, '<span class="room-tag">($1)</span>');

                            rowHtml += `<td${rSpanAttr}><div class="slot-pill">${formattedContent}</div></td>`;

                            // Mobile schedule data structure population
                            let timeString = timeCell;
                            if (mergeInfo.rowspan > 1) {
                                const currentIdxInIndices = scheduleRowIndices.indexOf(i);
                                const endRowIdx = scheduleRowIndices[currentIdxInIndices + mergeInfo.rowspan - 1];
                                const endRow = rows[endRowIdx];
                                if (endRow) {
                                    const endTimeCell = endRow[1] || endRow[0] || "";
                                    timeString = mergeTimes(timeCell, endTimeCell);
                                }
                            }

                            let subjectName = cellVal;
                            let facultyInitials = "";
                            let roomTag = "";

                            const facMatch = cellVal.match(/\(([A-Z]{2,4})\)/i);
                            if (facMatch) {
                                facultyInitials = facMatch[1].toUpperCase();
                                subjectName = subjectName.replace(facMatch[0], "").trim();
                            }

                            const roomMatch = cellVal.match(/\(([A-Z]{1,2}-\d{1,2})\)/i);
                            if (roomMatch) {
                                roomTag = roomMatch[1].toUpperCase();
                                subjectName = subjectName.replace(roomMatch[0], "").trim();
                            }

                            subjectName = subjectName.replace(/\r\n|\r|\n/g, " / ").replace(/\s+/g, " ").trim();

                            daysData[dayName].push({
                                isRecess: false,
                                time: timeString,
                                subject: subjectName,
                                faculty: facultyInitials,
                                room: roomTag
                            });
                        } else {
                            rowHtml += `<td${rSpanAttr}>-</td>`;
                        }
                    }

                    rowHtml += `</tr>`;
                    bodyHtml += rowHtml;
                });

                stTableBody.innerHTML = bodyHtml || `<tr><td colspan="8">No schedule data found in sheet.</td></tr>`;

                // Store mobile data in global scope
                window.mobileDaysData = daysData;

                // Determine which tab should be loaded
                const weekdayNames = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
                const currentDayIndex = new Date().getDay();
                let dayToSelect = weekdayNames[currentDayIndex];
                if (dayToSelect === "SUN" || !daysData[dayToSelect]) {
                    dayToSelect = "MON";
                }

                if (window.currentActiveDay && daysData[window.currentActiveDay]) {
                    dayToSelect = window.currentActiveDay;
                }

                renderMobileDay(dayToSelect);

                // Locate Legend Section below the main schedule grid (rows containing "Subject" / "Location")
                let legendStartIdx = -1;
                for (let i = headerIdx + 1; i < rows.length; i++) {
                    const rStr = (rows[i] || []).join(" ").toLowerCase();
                    if (rStr.includes("subject") || rStr.includes("location") || rStr.includes("faculty")) {
                        legendStartIdx = i;
                        break;
                    }
                }

                const excelSubjectList = [];
                const excelFacultyList = [];
                let detectedLocation = "";

                if (legendStartIdx !== -1) {
                    for (let i = legendStartIdx; i < rows.length; i++) {
                        const row = rows[i];
                        if (!row || row.length === 0) continue;

                        const rowStr = row.join(" ").trim();
                        if (rowStr.toLowerCase().includes("co-ordinator") || rowStr.toLowerCase().includes("head of department")) {
                            break; // Stop at signature row
                        }

                        // 1. Extract Subject entries (columns 0 & 1)
                        const c0 = String(row[0] || "").trim();
                        const c1 = String(row[1] || "").trim();
                        if (c0 && c0.toLowerCase() !== "subject") {
                            if (c0.includes(":") && c1 && !c1.toLowerCase().includes("prof")) {
                                excelSubjectList.push(`${c0} ${c1}`);
                            } else if (c0.includes(":") && c0.length > 5) {
                                excelSubjectList.push(c0);
                            } else if (c0 && c1 && !c1.toLowerCase().includes("prof") && !c1.toLowerCase().includes("location")) {
                                excelSubjectList.push(`${c0} : ${c1}`);
                            }
                        }

                        // 2. Extract Faculty entries (columns 2 to 6)
                        for (let c = 2; c < row.length; c++) {
                            const cellVal = String(row[c] || "").trim();
                            if (!cellVal || cellVal.toLowerCase() === "location") continue;

                            if (cellVal.includes(":") && (cellVal.toLowerCase().includes("prof") || cellVal.toLowerCase().includes("dr.") || cellVal.toLowerCase().includes("mr.") || cellVal.toLowerCase().includes("ms."))) {
                                excelFacultyList.push(cellVal);
                            } else if (cellVal.includes(":") && c < row.length - 1) {
                                const nextCell = String(row[c+1] || "").trim();
                                if (nextCell.toLowerCase().includes("prof") || nextCell.toLowerCase().includes("dr") || nextCell.toLowerCase().includes("mr") || nextCell.toLowerCase().includes("ms")) {
                                    excelFacultyList.push(`${cellVal} ${nextCell}`);
                                }
                            }
                        }

                        // 3. Extract Location cell
                        for (let c = 0; c < row.length; c++) {
                            const cellVal = String(row[c] || "").trim();
                            if (cellVal && (cellVal.toLowerCase().includes("gf-") || cellVal.toLowerCase().includes("tf-") || cellVal.toLowerCase().includes("ff-") || cellVal.toLowerCase().includes("sf-"))) {
                                detectedLocation = cellVal;
                            }
                        }
                    }
                }

                // Render Subject Column
                let subHtml = `<strong>Subject</strong><br>`;
                if (excelSubjectList.length > 0) {
                    excelSubjectList.forEach(line => {
                        subHtml += `${line}<br>`;
                    });
                } else {
                    subjectsSet.forEach(sub => {
                        subHtml += `${sub}<br>`;
                    });
                }
                stSubjectCol.innerHTML = subHtml || `<strong>Subject</strong><br>-`;

                // Render Faculty Column
                let facHtml = `<strong>Faculty Details</strong><br>`;
                if (excelFacultyList.length > 0) {
                    excelFacultyList.forEach(line => {
                        facHtml += `${line}<br>`;
                    });
                } else {
                    facultyInitialsSet.forEach(init => {
                        facHtml += `${init} : Prof. ${init}<br>`;
                    });
                }
                stFacultyCol.innerHTML = facHtml || `<strong>Faculty Details</strong><br>-`;

                // Render Location Column
                const finalLocation = detectedLocation || classroom.replace(/Class Room\s*:\s*/i, "");
                stLocationCol.innerHTML = `<strong>Location</strong><br>${finalLocation}`;
            }

            // Time merging helper
            function mergeTimes(t1, t2) {
                if (!t1) return t2 || "";
                if (!t2) return t1 || "";
                const parts1 = String(t1).split('-');
                const parts2 = String(t2).split('-');
                if (parts1.length === 2 && parts2.length === 2) {
                    return parts1[0].trim() + ' - ' + parts2[1].trim();
                }
                return t1 + ' - ' + t2;
            }

            // Mobile day-by-day card list renderer
            function renderMobileDay(dayName) {
                window.currentActiveDay = dayName;

                // Update active tab button style
                const dayTabs = document.querySelectorAll(".day-tab-btn");
                dayTabs.forEach(tab => {
                    if (tab.getAttribute("data-day") === dayName) {
                        tab.classList.add("active");
                    } else {
                        tab.classList.remove("active");
                    }
                });

                const mSlotsList = document.getElementById("mSlotsList");
                if (!mSlotsList) return;

                const slots = (window.mobileDaysData && window.mobileDaysData[dayName]) || [];
                if (slots.length === 0) {
                    mSlotsList.innerHTML = `<div class="mobile-empty-state">No lectures or breaks scheduled for ${dayName}.</div>`;
                    return;
                }

                let slotsHtml = "";
                slots.forEach(slot => {
                    if (slot.isRecess) {
                        slotsHtml += `
                            <div class="mobile-recess-divider">
                                <span>${slot.label} (${slot.time})</span>
                            </div>
                        `;
                    } else {
                        const facultyText = slot.faculty ? `<span class="mobile-slot-faculty">Prof. ${slot.faculty}</span>` : "";
                        const roomText = slot.room ? `<span class="mobile-slot-room">Room: ${slot.room}</span>` : "";
                        
                        const timeParts = slot.time.split('-');
                        const startTime = timeParts[0] ? timeParts[0].trim() : "";
                        const endTime = timeParts[1] ? timeParts[1].trim() : "";

                        slotsHtml += `
                            <div class="mobile-slot-card">
                                <div class="mobile-slot-time">
                                    <span>${startTime}</span>
                                    <span style="font-size: 10px; color: #64748b; font-weight: normal; margin-top: 2px; text-transform: lowercase;">to</span>
                                    <span>${endTime}</span>
                                </div>
                                <div class="mobile-slot-details">
                                    <div class="mobile-slot-subject">${slot.subject}</div>
                                    ${(facultyText || roomText) ? `
                                    <div class="mobile-slot-meta">
                                        ${facultyText}
                                        ${roomText}
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    }
                });

                mSlotsList.innerHTML = slotsHtml;
            }

            // Wire up event listeners for mobile day tabs
            const dayTabs = document.querySelectorAll(".day-tab-btn");
            dayTabs.forEach(tab => {
                tab.addEventListener("click", () => {
                    const day = tab.getAttribute("data-day");
                    renderMobileDay(day);
                });
            });
        });
    </script>
    <?php endif; ?>
</body>

</html>
