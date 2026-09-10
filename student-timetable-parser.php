<?php
/**
 * Student Timetable Excel Parser & Cache Generator
 */

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
