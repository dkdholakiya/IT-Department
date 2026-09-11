<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$url = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($url)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required parameter: url'
    ]);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid URL provided.'
    ]);
    exit;
}

// Fetch URL using cURL with modern browser headers
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, Gecko) Chrome/125.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9',
    'Cache-Control: no-cache'
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($html === false || !empty($curlError)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => "cURL request failed: " . ($curlError ?: 'Unknown network error')
    ]);
    exit;
}

if ($httpCode >= 400) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => "Target URL returned HTTP status code {$httpCode}"
    ]);
    exit;
}

// Helper to convert relative URLs to absolute
function rel2abs($rel, $base) {
    if (empty($rel)) return '';
    if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
    if (str_starts_with($rel, '//')) {
        $scheme = parse_url($base, PHP_URL_SCHEME) ?: 'https';
        return $scheme . ':' . $rel;
    }

    $baseParts = parse_url($base);
    $scheme = $baseParts['scheme'] ?? 'https';
    $host = $baseParts['host'] ?? '';
    $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
    $path = $baseParts['path'] ?? '/';

    if (str_starts_with($rel, '/')) {
        return "{$scheme}://{$host}{$port}{$rel}";
    }

    $dir = dirname($path);
    if ($dir === '.' || $dir === '/') {
        $dir = '';
    }

    $abs = "{$dir}/{$rel}";
    
    $stack = [];
    foreach (explode('/', $abs) as $segment) {
        if ($segment === '' || $segment === '.') continue;
        if ($segment === '..') {
            array_pop($stack);
        } else {
            $stack[] = $segment;
        }
    }
    return "{$scheme}://{$host}{$port}/" . implode('/', $stack);
}

// Parse HTML DOM
libxml_use_internal_errors(true);
$dom = new DOMDocument();
@$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
$tables = $xpath->query('//table');

$semGroups = [];
$tableIndex = 0;
$totalSubjects = 0;

foreach ($tables as $table) {
    $tableText = $table->textContent;
    
    // Check if table contains syllabus signature keywords
    if (!preg_match('/SUBJECT\s*CODE/i', $tableText) && 
        !preg_match('/SUB\s*CODE/i', $tableText) && 
        !preg_match('/COURSE\s*CODE/i', $tableText)) {
        continue;
    }

    $tableIndex++;
    $semName = "Semester {$tableIndex}";

    // Locate heading prior to table
    $prevNode = $table->previousSibling;
    $foundHeading = null;
    
    for ($i = 0; $i < 6 && $prevNode; $i++) {
        if ($prevNode->nodeType === XML_ELEMENT_NODE) {
            $nodeText = trim($prevNode->textContent);
            if (preg_match('/(sem(?:ester)?\s*[\dIVX]+|year\s*[\dIVX]+)/i', $nodeText, $matches)) {
                $foundHeading = $matches[0];
                break;
            }
        }
        $prevNode = $prevNode->previousSibling;
    }

    if ($foundHeading) {
        $semName = strtoupper(preg_replace('/\s+/', ' ', $foundHeading));
    }

    $rows = [];
    $trNodes = $xpath->query('.//tr', $table);

    foreach ($trNodes as $tr) {
        $cellNodes = $xpath->query('.//td | .//th', $tr);
        $cells = [];
        foreach ($cellNodes as $cell) {
            $cells[] = trim(preg_replace('/\s+/', ' ', $cell->textContent));
        }

        if (count($cells) === 0) continue;

        $firstCell = strtoupper($cells[0]);
        if (str_contains($firstCell, 'SUBJECT CODE') || str_contains($firstCell, 'SUB CODE') || str_contains($firstCell, 'SR NO') || str_contains($firstCell, 'SR. NO')) {
            continue;
        }

        $isFirstNum = preg_match('/^\d+$/', $cells[0]);
        $offset = ($isFirstNum && count($cells) > 7) ? 1 : 0;

        $code   = $cells[0 + $offset] ?? '';
        $name   = $cells[1 + $offset] ?? '';
        $short  = $cells[2 + $offset] ?? '-';
        $l      = $cells[3 + $offset] ?? '0';
        $t      = $cells[4 + $offset] ?? '0';
        $p      = $cells[5 + $offset] ?? '0';
        $credit = $cells[6 + $offset] ?? ($cells[7] ?? '0');

        $link = '';
        if (isset($cellNodes[0 + $offset])) {
            $aNodes = $xpath->query('.//a[@href]', $cellNodes[0 + $offset]);
            if ($aNodes->length > 0) {
                $rawHref = trim($aNodes->item(0)->getAttribute('href'));
                if (!empty($rawHref) && $rawHref !== '#') {
                    $link = rel2abs($rawHref, $url);
                }
            }
        }
        if (empty($link)) {
            $aNodes = $xpath->query('.//a[@href]', $tr);
            foreach ($aNodes as $aNode) {
                $rawHref = trim($aNode->getAttribute('href'));
                if (!empty($rawHref) && $rawHref !== '#' && (str_contains(strtolower($rawHref), 'syllabus') || str_contains(strtolower($rawHref), '.pdf'))) {
                    $link = rel2abs($rawHref, $url);
                    break;
                }
            }
        }

        if (!empty($code) || !empty($name)) {
            $rows[] = [
                'code'   => $code ?: 'N/A',
                'name'   => $name ?: 'N/A',
                'short'  => $short ?: '-',
                'l'      => $l ?: '0',
                't'      => $t ?: '0',
                'p'      => $p ?: '0',
                'credit' => $credit ?: '0',
                'link'   => $link ?: ''
            ];
            $totalSubjects++;
        }
    }

    if (count($rows) > 0) {
        if (!isset($semGroups[$semName])) {
            $semGroups[$semName] = [];
        }
        $semGroups[$semName] = array_merge($semGroups[$semName], $rows);
    }
}

echo json_encode([
    'success'        => true,
    'url'            => $url,
    'totalSemesters' => count($semGroups),
    'totalSubjects'  => $totalSubjects,
    'data'           => $semGroups
]);
