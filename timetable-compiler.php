<?php
/**
 * Faculty Timetable Automatic Excel Compiler
 * Compiles uploads/timetable/timetable.xlsx into assets/js/timetableData.js
 */

function compileTimetableData() {
    $excelFile = __DIR__ . '/uploads/timetable/timetable.xlsx';
    if (!file_exists($excelFile)) {
        $glob = glob(__DIR__ . '/uploads/timetable/*.xlsx');
        if (!empty($glob)) $excelFile = $glob[0];
    }
    
    if (!file_exists($excelFile)) {
        return ['success' => false, 'message' => 'Excel file not found inside uploads/timetable/ directory.'];
    }

    $scratchDir = __DIR__ . '/scratch';
    if (!is_dir($scratchDir)) {
        @mkdir($scratchDir, 0777, true);
    }

    $tempZip = $scratchDir . '/temp_timetable.zip';
    $unzipDir = $scratchDir . '/unzipped_timetable';

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
    @mkdir($unzipDir, 0777, true);

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
        return ['success' => false, 'message' => 'Failed to unzip Excel file. Ensure php_zip or PowerShell is available.'];
    }

    // Read shared strings
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
    $relsFile = $unzipDir . '/xl/_rels/workbook.xml.rels';
    if (!file_exists($relsFile)) {
        return ['success' => false, 'message' => 'Invalid Excel structure: workbook.xml.rels missing.'];
    }
    $relsXML = file_get_contents($relsFile);
    $xmlRels = simplexml_load_string($relsXML);
    $rels = [];
    if ($xmlRels) {
        foreach ($xmlRels->Relationship as $r) {
            $rels[(string)$r['Id']] = (string)$r['Target'];
        }
    }

    // Load workbook sheets
    $workbookFile = $unzipDir . '/xl/workbook.xml';
    if (!file_exists($workbookFile)) {
        return ['success' => false, 'message' => 'Invalid Excel structure: workbook.xml missing.'];
    }
    $workbookXML = file_get_contents($workbookFile);
    $xmlWorkbook = simplexml_load_string($workbookXML);
    $sheets = [];
    if ($xmlWorkbook) {
        foreach ($xmlWorkbook->sheets->sheet as $s) {
            $sheetName = trim((string)$s['name']);
            if (preg_match('/^copy\s+of/i', $sheetName) || preg_match('/^sheet\d+$/i', $sheetName)) {
                continue;
            }
            $rId = (string)$s->attributes('r', true)->id;
            $targetFile = $rels[$rId] ?? '';
            $sheets[$sheetName] = 'xl/' . $targetFile;
        }
    }

    if (!function_exists('tsc_getCellColRow')) {
        function tsc_getCellColRow($ref) {
            preg_match('/^([A-Z]+)([0-9]+)$/', $ref, $matches);
            return [$matches[1], intval($matches[2])];
        }
    }

    if (!function_exists('tsc_parseSlot')) {
        function tsc_parseSlot($rows, $rowIdx, $timeRange, $days, $facInit) {
            $slot = [
                'time' => $timeRange,
                'isRecess' => false
            ];
            foreach ($days as $day => $col) {
                $cellVal = isset($rows[$rowIdx][$col]) ? $rows[$rowIdx][$col] : '';
                $slot[$day] = tsc_parseCell($cellVal, $facInit);
            }
            return $slot;
        }
    }

    if (!function_exists('tsc_parseCell')) {
        function tsc_parseCell($val, $facInit) {
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
                list($col, $row_num) = tsc_getCellColRow($ref);
                
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
        
        // Parse merge cells
        $mergeCells = [];
        if (isset($xml->mergeCells->mergeCell)) {
            foreach ($xml->mergeCells->mergeCell as $mc) {
                $ref = (string)$mc['ref'];
                if (strpos($ref, ':') !== false) {
                    list($start, $end) = explode(':', $ref);
                    list($startCol, $startRow) = tsc_getCellColRow($start);
                    list($endCol, $endRow) = tsc_getCellColRow($end);
                    $mergeCells[] = [
                        'startCol' => $startCol,
                        'startRow' => $startRow,
                        'endCol' => $endCol,
                        'endRow' => $endRow
                    ];
                }
            }
        }

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
        
        $headerRow = 5;
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
        
        $semesterInfo = isset($rows[$infoRowIdx]['C']) ? trim($rows[$infoRowIdx]['C']) : '';
        
        $schedule = [];
        $days = ['MON' => 'C', 'TUE' => 'D', 'WED' => 'E', 'THU' => 'F', 'FRI' => 'G', 'SAT' => 'H'];
        
        for ($r = $headerRow + 1; $r < 100; $r++) {
            if (!isset($rows[$r])) break;
            
            $valA = isset($rows[$r]['A']) ? trim($rows[$r]['A']) : '';
            $valB = isset($rows[$r]['B']) ? trim($rows[$r]['B']) : '';
            
            if (stripos($valA, 'TOTAL Load') !== false || stripos($valB, 'TOTAL Load') !== false) {
                break;
            }
            
            $isRecess = false;
            $label = '';
            if (stripos($valA, 'RECESS') !== false || stripos($valA, 'BREAK') !== false) {
                $isRecess = true;
                $label = $valA;
            } else if (stripos($valB, 'RECESS') !== false || stripos($valB, 'BREAK') !== false) {
                $isRecess = true;
                $label = $valB;
            } else {
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
                $schedule[] = tsc_parseSlot($rows, $r, $valB, $days, $facultyInitials);
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

    $jsTargetFile = __DIR__ . '/assets/js/timetableData.js';
    if (file_put_contents($jsTargetFile, $jsContent)) {
        @touch($jsTargetFile);
        $count = count($timetableDataset);
        return [
            'success' => true,
            'count' => $count,
            'message' => "Successfully processed and compiled schedules for $count faculty members."
        ];
    }

    return ['success' => false, 'message' => 'Failed to write compiled timetable data to assets/js/timetableData.js.'];
}
