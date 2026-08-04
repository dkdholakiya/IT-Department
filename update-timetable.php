<?php
/**
 * Faculty Timetable Auto-Updater from Excel
 */

$excelFile = __DIR__ . '/uploads/timetable/timetable.xlsx';
if (!file_exists($excelFile)) {
    $glob = glob(__DIR__ . '/uploads/timetable/*.xlsx');
    if (!empty($glob)) $excelFile = $glob[0];
}
$tempZip = __DIR__ . '/scratch/temp_timetable.zip';
$unzipDir = __DIR__ . '/scratch/unzipped_timetable';

$success = false;
$message = "";
$facultyCount = 0;

if (!file_exists($excelFile)) {
    $message = "Excel file not found inside uploads/timetable/ directory.";
} else {
    // Clean up old unzip dir
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
    @mkdir($unzipDir);

    // Copy to zip and extract
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
        // Fallback to PowerShell with properly escaped double quotes for cmd.exe
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
        $message = "Failed to unzip the Excel file. Please ensure php_zip extension is enabled or PowerShell is accessible.";
    } else {
        // Run parser
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

        // Load workbook relations
        $relsXML = file_get_contents($unzipDir . '/xl/_rels/workbook.xml.rels');
        $xmlRels = simplexml_load_string($relsXML);
        $rels = [];
        if ($xmlRels) {
            foreach ($xmlRels->Relationship as $r) {
                $rels[(string)$r['Id']] = (string)$r['Target'];
            }
        }

        // Load workbook sheets
        $workbookXML = file_get_contents($unzipDir . '/xl/workbook.xml');
        $xmlWorkbook = simplexml_load_string($workbookXML);
        $sheets = [];
        if ($xmlWorkbook) {
            foreach ($xmlWorkbook->sheets->sheet as $s) {
                $sheetName = trim((string)$s['name']);

                // Skip duplicate/copy sheets (e.g. "Copy of AMI")
                if (preg_match('/^copy\s+of/i', $sheetName) || preg_match('/^sheet\d+$/i', $sheetName)) {
                    continue;
                }

                $rId = (string)$s->attributes('r', true)->id;
                $targetFile = $rels[$rId] ?? '';
                $sheets[$sheetName] = 'xl/' . $targetFile;
            }
        }

        function getCellColRow($ref) {
            preg_match('/^([A-Z]+)([0-9]+)$/', $ref, $matches);
            return [$matches[1], intval($matches[2])];
        }

        function parseSlot($rows, $rowIdx, $timeRange, $days, $facInit) {
            $slot = [
                'time' => $timeRange,
                'isRecess' => false
            ];
            foreach ($days as $day => $col) {
                $cellVal = isset($rows[$rowIdx][$col]) ? $rows[$rowIdx][$col] : '';
                $slot[$day] = parseCell($cellVal, $facInit);
            }
            return $slot;
        }

        function parseCell($val, $facInit) {
            $val = trim($val);
            if (empty($val)) {
                return ['class' => '', 'room' => '', 'occupied' => false];
            }
            
            $room = '';
            if (preg_match('/\b([A-Z]{2}-\d+[A-Z]?)\b/i', $val, $m)) {
                $room = strtoupper($m[1]);
            } else if (preg_match('/\((.*?)\)/', $val, $m)) {
                $room = trim($m[1]);
            }
            
            $classClean = $val;
            if ($room) {
                $classClean = preg_replace('/\(' . preg_quote($room, '/') . '\)/i', '', $classClean);
                $classClean = preg_replace('/\b' . preg_quote($room, '/') . '\b/i', '', $classClean);
            }
            
            $classClean = preg_replace('/\(' . preg_quote($facInit, '/') . '\)/i', '', $classClean);
            $classClean = preg_replace('/\([A-Z]{3}\)/', '', $classClean);
            
            $classClean = preg_replace('/[\r\n]+/', ' ', $classClean);
            $classClean = preg_replace('/\s+/', ' ', $classClean);
            $classClean = trim($classClean, " \t\n\r\0\x0B-(),");
            
            return [
                'class' => $classClean,
                'room' => $room,
                'occupied' => true
            ];
        }

        $timetableDataset = [];

        foreach ($sheets as $sheetName => $relPath) {
            $sheetFile = $unzipDir . '/' . $relPath;
            if (!file_exists($sheetFile)) continue;
            
            $sheetXML = file_get_contents($sheetFile);
            $xml = simplexml_load_string($sheetXML);
            if (!$xml) continue;
            
            $rows = [];
            foreach ($xml->sheetData->row as $row) {
                $r_idx = intval($row['r']);
                foreach ($row->c as $c) {
                    $ref = (string)$c['r'];
                    list($col, $row_num) = getCellColRow($ref);
                    
                    $val = "";
                    if (isset($c->v)) {
                        $v = (string)$c->v;
                        if (isset($c['t']) && (string)$c['t'] === 's') {
                            $val = $sharedStrings[intval($v)] ?? $v;
                        } else {
                            $val = $v;
                        }
                    }
                    $rows[$r_idx][$col] = $val;
                }
            }
            
            // Parse merge cells to duplicate values into merged slot cells
            $mergeCells = [];
            if (isset($xml->mergeCells->mergeCell)) {
                foreach ($xml->mergeCells->mergeCell as $mc) {
                    $ref = (string)$mc['ref'];
                    if (strpos($ref, ':') !== false) {
                        list($start, $end) = explode(':', $ref);
                        list($startCol, $startRow) = getCellColRow($start);
                        list($endCol, $endRow) = getCellColRow($end);
                        $mergeCells[] = [
                            'startCol' => $startCol,
                            'startRow' => $startRow,
                            'endCol' => $endCol,
                            'endRow' => $endRow
                        ];
                    }
                }
            }

            // Apply merged cells values
            foreach ($mergeCells as $range) {
                $val = isset($rows[$range['startRow']][$range['startCol']]) ? $rows[$range['startRow']][$range['startCol']] : '';
                if ($val !== '') {
                    $startColNum = ord($range['startCol']);
                    $endColNum = ord($range['endCol']);
                    for ($r = $range['startRow']; $r <= $range['endRow']; $r++) {
                        for ($cNum = $startColNum; $cNum <= $endColNum; $cNum++) {
                            $cLetter = chr($cNum);
                            if (!isset($rows[$r][$cLetter]) || $rows[$r][$cLetter] === '') {
                                $rows[$r][$cLetter] = $val;
                            }
                        }
                    }
                }
            }
            
            $facultyName = "Unknown Faculty";
            $facultyInitials = $sheetName;
            
            $row3Val = isset($rows[3]['C']) ? $rows[3]['C'] : (isset($rows[3]['B']) ? $rows[3]['B'] : '');
            if (preg_match('/PERSONAL\s*[-|]?\s*TIME\s*TABLE\s*[-|]?\s*(.*?)\s*\((.*?)\)/i', $row3Val, $m)) {
                $facultyName = trim($m[1]);
                $facultyInitials = trim($m[2]);
            } else if (preg_match('/PERSONAL\s*[-|]?\s*TIME\s*TABLE\s*[-|]?\s*(.*)/i', $row3Val, $m)) {
                $rawTitle = trim($m[1]);
                $rawTitle = preg_replace('/^Faculty\s+Short\s+Name\s*:\s*/i', '', $rawTitle);
                $rawTitle = trim($rawTitle);
                if (!empty($rawTitle) && strlen($rawTitle) > 3) {
                    $facultyName = $rawTitle;
                }
            }
            
            if (strtoupper($facultyName) == "UNKNOWN FACULTY" || empty($facultyName)) {
                $facultyName = "Prof. " . $sheetName;
            }
            
            // Find the header row (contains "SR. NO." or "TIME")
            $headerRow = 5; // Default fallback
            for ($r = 1; $r <= 10; $r++) {
                if (!isset($rows[$r])) continue;
                $valA = isset($rows[$r]['A']) ? trim($rows[$r]['A']) : '';
                $valB = isset($rows[$r]['B']) ? trim($rows[$r]['B']) : '';
                if (stripos($valA, 'SR. NO.') !== false || stripos($valB, 'TIME') !== false) {
                    $headerRow = $r;
                    break;
                }
            }

            $department = "Information Technology";
            $infoRowIdx = $headerRow - 1;
            if ($infoRowIdx >= 1 && isset($rows[$infoRowIdx])) {
                $infoRowText = '';
                foreach ($rows[$infoRowIdx] as $val) {
                    $infoRowText .= ' ' . $val;
                }
                if (stripos($infoRowText, 'CE') !== false && stripos($infoRowText, 'IT') === false) {
                    $department = "Computer Engineering";
                }
            }
            
            // Extract semesterInfo from column C of infoRow (headerRow - 1)
            $semesterInfo = isset($rows[$infoRowIdx]['C']) ? trim($rows[$infoRowIdx]['C']) : '';
            
            $schedule = [];
            $days = ['MON' => 'C', 'TUE' => 'D', 'WED' => 'E', 'THU' => 'F', 'FRI' => 'G', 'SAT' => 'H'];
            
            // Loop dynamically through rows starting after headerRow
            for ($r = $headerRow + 1; $r < 100; $r++) {
                if (!isset($rows[$r])) {
                    break;
                }
                
                $valA = isset($rows[$r]['A']) ? trim($rows[$r]['A']) : '';
                $valB = isset($rows[$r]['B']) ? trim($rows[$r]['B']) : '';
                
                // If we reach the TOTAL Load row, stop parsing the schedule
                if (stripos($valA, 'TOTAL Load') !== false || stripos($valB, 'TOTAL Load') !== false) {
                    break;
                }
                
                // Determine if it is a recess row (either A, B, or any day column contains recess/break keywords)
                $isRecess = false;
                $label = '';
                if (stripos($valA, 'RECESS') !== false || stripos($valA, 'BREAK') !== false) {
                    $isRecess = true;
                    $label = $valA;
                } else if (stripos($valB, 'RECESS') !== false || stripos($valB, 'BREAK') !== false) {
                    $isRecess = true;
                    $label = $valB;
                } else {
                    // Check day columns (C to H)
                    $dayCols = ['C', 'D', 'E', 'F', 'G', 'H'];
                    foreach ($dayCols as $col) {
                        $cellVal = isset($rows[$r][$col]) ? trim($rows[$r][$col]) : '';
                        if (stripos($cellVal, 'RECESS') !== false || stripos($cellVal, 'BREAK') !== false) {
                            $isRecess = true;
                            $label = $cellVal;
                            break;
                        }
                    }
                }
                
                if ($isRecess) {
                    $recessTime = '';
                    $labelUpper = strtoupper($label);
                    
                    if (strpos($labelUpper, 'MORNING SHIFT RECESS - 1') !== false || strpos($labelUpper, 'MORNING SHIFT RECESS-1') !== false) {
                        $recessTime = '09:30 to 10:00';
                    } else if (preg_match('/RECESS\s*-\s*2(?!\d)/', $labelUpper) && (strpos($labelUpper, 'MORNING') !== false || strpos($labelUpper, 'MRING') !== false)) {
                        $recessTime = '12:00 to 12:15';
                    } else if (preg_match('/RECESS\s*-\s*1(?!\d)/', $labelUpper)) {
                        $recessTime = '01:00 to 01:45';
                    } else if (preg_match('/RECESS\s*-\s*2(?!\d)/', $labelUpper)) {
                        $recessTime = '03:45 to 04:00';
                    }
                    
                    if (empty($recessTime)) {
                        $recessTime = $valB;
                    }
                    
                    $schedule[] = [
                        'time' => $recessTime,
                        'isRecess' => true,
                        'label' => $label
                    ];
                } else if (!empty($valB) && preg_match('/\d{1,2}:\d{2}/', $valB)) {
                    // Regular class slot
                    $schedule[] = parseSlot($rows, $r, $valB, $days, $facultyInitials);
                }
            }
            
            $timetableDataset[$facultyInitials] = [
                'name' => $facultyName,
                'initials' => $facultyInitials,
                'department' => $department,
                'semesterInfo' => $semesterInfo,
                'schedule' => $schedule
            ];
        }

        $jsContent = "// Auto-generated Faculty Timetable Data\n";
        $jsContent .= "const timetableData = " . json_encode($timetableDataset, JSON_UNESCAPED_UNICODE) . ";\n";

        if (file_put_contents(__DIR__ . '/assets/js/timetableData.js', $jsContent)) {
            @touch(__DIR__ . '/assets/js/timetableData.js');
            $success = true;
            $facultyCount = count($timetableDataset);
            $message = "Successfully processed and compiled schedules for $facultyCount faculty members.";
        } else {
            $message = "Failed to write compiled timetable data to assets/js/timetableData.js.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Data Syncer</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Share+Tech&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/portal.css?v=3">
    <link rel="stylesheet" href="assets/css/faculty.css?v=3">
    <link rel="stylesheet" href="assets/css/theme-light.css">
    <style>
        body {
            background-color: #060d1f;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: #f8fafc;
            margin: 0;
        }
        .status-card {
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .status-icon {
            font-size: 54px;
            margin-bottom: 20px;
        }
        .status-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        .status-desc {
            font-size: 14.5px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: rgba(192, 57, 43, 0.2);
            border: 1px solid #c0392b;
            color: #fca5a5;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }
        .back-btn:hover {
            background: #c0392b;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(192, 57, 43, 0.4);
        }
    </style>
</head>
<body>
    <div class="status-card">
        <?php if ($success): ?>
            <div class="status-icon">🚀</div>
            <div class="status-title" style="color: #34d399;">Sync Complete!</div>
            <div class="status-desc"><?php echo htmlspecialchars($message); ?></div>
        <?php else: ?>
            <div class="status-icon">⚠️</div>
            <div class="status-title" style="color: #f87171;">Sync Failed</div>
            <div class="status-desc"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <a href="timetable" class="back-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Back to Timetable
        </a>
    </div>
    <?php include_once 'theme-toggle.php'; ?>
</body>
</html>
