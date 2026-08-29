<?php
require_once 'auto-cache-bust.php';
$active_page = 'workload-summary';
$excelFile = __DIR__ . '/uploads/timetable/timetable.xlsx';
if (!file_exists($excelFile)) {
    $glob = glob(__DIR__ . '/uploads/timetable/*.xlsx');
    if (!empty($glob)) $excelFile = $glob[0];
}
$excelExists = file_exists($excelFile);
$jsDataFile = __DIR__ . '/assets/js/timetableData.js';
$jsDataExists = file_exists($jsDataFile);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Faculty Workload & Timetable Audit Summary — Department of CE & IT">
    <title>Faculty Workload Summary — CE & IT Department</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/portal.css?v=4">
    <link rel="stylesheet" href="assets/css/faculty.css?v=4">
    <link rel="stylesheet" href="assets/css/timetable.css?v=9">

    <style>
        body,
        body * {
            font-family: 'Playfair Display', serif !important;
        }

        .print-only-header {
            display: none;
        }

        /* ── Simple Black & White Print Layout ── */
        @media print {
            .rp-header,
            .particles,
            .orb,
            .theme-switcher-container,
            .fab-nav,
            .stats-grid,
            .summary-controls,
            .summary-table-header,
            .view-btn,
            .summary-table th:last-child,
            .summary-table td:last-child {
                display: none !important;
            }

            html, body, body * {
                font-family: Arial, Helvetica, sans-serif !important;
                background: #ffffff !important;
                color: #000000 !important;
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .summary-container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .summary-table-card {
                background: #ffffff !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            .print-only-header {
                display: block !important;
                text-align: center;
                margin-bottom: 16px;
                border-bottom: 2px solid #000000;
                padding-bottom: 8px;
            }

            .print-only-header h1 {
                font-size: 16pt !important;
                margin: 0 0 4px 0 !important;
                color: #000000 !important;
                font-weight: bold !important;
            }

            .print-only-header p {
                font-size: 9.5pt !important;
                margin: 0 !important;
                color: #000000 !important;
            }

            .summary-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 10px !important;
                font-size: 9.5pt !important;
            }

            .summary-table th,
            .summary-table td {
                border: 1px solid #000000 !important;
                padding: 6px 8px !important;
                color: #000000 !important;
                background: #ffffff !important;
                text-align: left !important;
            }

            .summary-table th {
                background: #f2f2f2 !important;
                font-weight: bold !important;
                text-transform: none !important;
                font-size: 9.5pt !important;
            }

            .fac-avatar-sm {
                display: none !important;
            }

            .fac-name-text {
                color: #000000 !important;
                font-weight: bold !important;
                font-size: 10pt !important;
            }

            .fac-sub-text {
                color: #000000 !important;
                font-size: 8.5pt !important;
            }

            .dept-tag {
                background: none !important;
                border: none !important;
                padding: 0 !important;
                color: #000000 !important;
                font-weight: normal !important;
                font-size: 9.5pt !important;
                text-transform: none !important;
            }

            .day-pill {
                background: none !important;
                border: 1px solid #666666 !important;
                color: #000000 !important;
                font-size: 8.5pt !important;
                padding: 1px 4px !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .day-pill.active {
                background: #e6e6e6 !important;
                font-weight: bold !important;
            }

            .load-badge-main {
                background: none !important;
                border: none !important;
                color: #000000 !important;
                font-weight: bold !important;
                font-size: 9.5pt !important;
                padding: 0 !important;
            }
        }

        .summary-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 30px 20px 80px 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg, rgba(15, 23, 42, 0.65));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: var(--shadow-sm, 0 4px 20px rgba(0,0,0,0.15));
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(192, 57, 43, 0.4);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(192, 57, 43, 0.15);
            color: #c0392b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-info .stat-value {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
            color: var(--text-color, #f8fafc);
        }

        .stat-info .stat-label {
            font-size: 13px;
            color: var(--text-muted, #94a3b8);
            margin-top: 4px;
            font-weight: 500;
        }

        .summary-controls {
            background: var(--card-bg, rgba(15, 23, 42, 0.65));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .summary-search {
            position: relative;
            flex: 1;
            min-width: 260px;
        }

        .summary-search input {
            width: 100%;
            padding: 12px 18px 12px 42px;
            background: var(--input-bg, rgba(0, 0, 0, 0.2));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.15));
            border-radius: 10px;
            color: var(--text-color, #f8fafc);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.3s ease;
        }

        .summary-search input:focus {
            border-color: #c0392b;
            box-shadow: 0 0 12px rgba(192, 57, 43, 0.25);
        }

        .summary-search svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted, #94a3b8);
            pointer-events: none;
        }

        .summary-filters {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 9px 18px;
            border-radius: 10px;
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.15));
            background: var(--filter-bg, transparent);
            color: var(--text-muted, #94a3b8);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .filter-btn:hover {
            color: var(--text-color, #fff);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .filter-btn.active {
            background: #c0392b;
            border-color: #c0392b;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(192, 57, 43, 0.35);
        }

        .sort-select {
            padding: 9px 16px;
            border-radius: 10px;
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.15));
            background: var(--input-bg, rgba(0, 0, 0, 0.2));
            color: var(--text-color, #f8fafc);
            font-size: 13.5px;
            font-family: inherit;
            outline: none;
            cursor: pointer;
        }

        /* Workload Summary Table Styling */
        .summary-table-card {
            background: var(--card-bg, rgba(15, 23, 42, 0.65));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(16px);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md, 0 10px 30px rgba(0, 0, 0, 0.2));
        }

        .summary-table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .summary-table-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-color, #f8fafc);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .summary-table th {
            background: var(--th-bg, rgba(0, 0, 0, 0.25));
            padding: 16px 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted, #94a3b8);
            border-bottom: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
        }

        .summary-table td {
            padding: 16px 20px;
            font-size: 14px;
            color: var(--text-color, #e2e8f0);
            border-bottom: 1px solid var(--border-color, rgba(255, 255, 255, 0.05));
            vertical-align: middle;
        }

        .summary-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .summary-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .fac-cell {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .fac-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c0392b, #8e44ad);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .fac-name-text {
            font-weight: 600;
            color: var(--text-color, #f8fafc);
            font-size: 14.5px;
        }

        .fac-sub-text {
            font-size: 12px;
            color: var(--text-muted, #94a3b8);
        }

        .dept-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dept-tag.it {
            background: rgba(192, 57, 43, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(192, 57, 43, 0.3);
        }

        .dept-tag.ce {
            background: rgba(52, 152, 219, 0.15);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .dept-tag.both {
            background: rgba(155, 89, 182, 0.15);
            color: #9b59b6;
            border: 1px solid rgba(155, 89, 182, 0.3);
        }

        .day-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted, #94a3b8);
        }

        .day-pill.active {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .load-badge-main {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 14px;
            background: rgba(192, 57, 43, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(192, 57, 43, 0.3);
        }

        .load-badge-main.high {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.4);
        }

        .load-badge-main.normal {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .load-badge-main.low {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.12));
            color: var(--text-color, #f8fafc);
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .view-btn:hover {
            background: #c0392b;
            border-color: #c0392b;
            color: #fff;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 10px;
            background: rgba(52, 211, 153, 0.15);
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: #34d399;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background: #34d399;
            color: #060d1f;
            box-shadow: 0 4px 15px rgba(52, 211, 153, 0.4);
        }

        /* ══════════════════════════════════════════════════════════
           Responsive Breakpoints for All Screen Sizes
           ══════════════════════════════════════════════════════════ */

        /* Large Displays / Desktop (Max 1200px) */
        @media (max-width: 1200px) {
            .summary-container {
                max-width: 100%;
                padding: 24px 20px 60px 20px;
            }
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 14px;
            }
            .stat-card {
                padding: 16px 18px;
                gap: 12px;
            }
            .stat-icon {
                width: 42px;
                height: 42px;
                font-size: 20px;
            }
            .stat-info .stat-value {
                font-size: 22px;
            }
        }

        /* Tablets & Small Laptops (Max 992px) */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }
            .summary-controls {
                flex-direction: column;
                align-items: stretch;
                padding: 16px;
                gap: 12px;
            }
            .summary-search {
                width: 100%;
                min-width: 100%;
            }
            .summary-filters {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            .filter-btn {
                text-align: center;
                padding: 8px 10px;
                font-size: 12.5px;
                width: 100%;
            }
            .sort-select {
                grid-column: span 2;
                width: 100%;
            }
            .print-btn {
                grid-column: span 1;
                width: 100%;
                justify-content: center;
                padding: 8px 10px;
                font-size: 12.5px;
            }
            .summary-table th:nth-child(4),
            .summary-table td:nth-child(4) {
                display: none; /* Hide individual daily pills on tablet to keep table compact */
            }
        }

        /* Tablets (Max 768px) */
        @media (max-width: 768px) {
            .rp-header-inner {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 10px;
            }
            .rp-header-center {
                align-items: center;
            }
            .portal-badge {
                display: none;
            }
            .summary-table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 14px 16px;
            }
            .summary-table th, 
            .summary-table td {
                padding: 12px 14px;
                font-size: 13.5px;
            }
            .fac-cell {
                gap: 10px;
            }
            .fac-avatar-sm {
                width: 34px;
                height: 34px;
                font-size: 12px;
            }
            .fac-name-text {
                font-size: 13.5px;
            }
        }

        /* Mobile Phones (Max 576px) */
        @media (max-width: 576px) {
            .summary-container {
                padding: 14px 12px 50px 12px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .stat-card {
                padding: 14px 12px;
                gap: 10px;
                border-radius: 12px;
            }
            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
                border-radius: 8px;
            }
            .stat-info .stat-value {
                font-size: 19px;
            }
            .stat-info .stat-label {
                font-size: 11px;
            }
            .summary-filters {
                grid-template-columns: 1fr;
            }
            .sort-select, .print-btn {
                grid-column: span 1;
            }
            .summary-table th:nth-child(1),
            .summary-table td:nth-child(1) {
                display: none; /* Hide Sr No on mobile */
            }
            .summary-table th:nth-child(3),
            .summary-table td:nth-child(3) {
                display: none; /* Hide Department column on small mobile screens */
            }
            .view-btn {
                padding: 5px 10px;
                font-size: 11.5px;
            }
            .load-badge-main {
                padding: 4px 10px;
                font-size: 12.5px;
            }
        }

        /* Tiny Mobile Phones (Max 380px) */
        @media (max-width: 380px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .fac-name-text {
                font-size: 12.5px;
            }
            .fac-sub-text {
                font-size: 11px;
            }
        }
    </style>
    <link rel="stylesheet" href="assets/css/theme-light.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- Background Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>

    <!-- Header -->
    <header class="rp-header">
        <div class="rp-header-inner container">
            <a href="timetable" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Back to Timetable
            </a>

            <div class="rp-header-center">
                <div class="rp-dept-badge">
                    <span class="rp-badge-dot"></span>
                    <span>Department of CE & IT — Faculty Workload Audit</span>
                </div>
                <h1 class="rp-title">Faculty Workload Audit & Total Load Summary</h1>
            </div>

            <span class="portal-badge">Workload Audit</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="summary-container container">

        <?php if (!$excelExists || !$jsDataExists): ?>
            <div class="controls-card" style="text-align: center; padding: 60px 24px;">
                <div style="font-size: 54px;">⚠️</div>
                <h2 style="color: #fbbf24; font-size: 22px; margin-top: 15px;">Database Sync Needed</h2>
                <p style="color: #94a3b8; font-size: 14.5px; margin: 10px 0 20px 0;">Please run the update script to compile the latest faculty timetable data.</p>
                <a href="update-timetable" class="back-btn" style="text-decoration: none;">Run Timetable Sync</a>
            </div>
        <?php else: ?>

            <!-- Printable Header (Visible only when printing) -->
            <div class="print-only-header">
                <h1>Department of CE & IT — Faculty Workload Summary</h1>
                <p>Official Weekly Teaching Load & Schedule Breakdown Report | Printed: <?php echo date('d M Y'); ?></p>
            </div>

            <!-- Stats Overview Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalFacultyCount">0</div>
                        <div class="stat-label">Faculty Members Loaded</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-info">
                        <div class="stat-value" id="totalTeachingHours">0 Hrs</div>
                        <div class="stat-label">Total Department Workload</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <div class="stat-value" id="avgWorkload">0 Hrs</div>
                        <div class="stat-label">Average Load / Faculty</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="maxWorkload">0 Hrs</div>
                        <div class="stat-label">Peak Workload Recorded</div>
                    </div>
                </div>
            </div>

            <!-- Controls & Filters Bar -->
            <div class="summary-controls">
                <div class="summary-search">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="summarySearchInput" placeholder="Search by faculty name or initials (e.g. APG, Remona)..." autocomplete="off">
                </div>

                <div class="summary-filters">
                    <button type="button" class="filter-btn active" data-filter="ALL">All Departments</button>
                    <button type="button" class="filter-btn" data-filter="Information Technology">IT Dept</button>
                    <button type="button" class="filter-btn" data-filter="Computer Engineering">CE Dept</button>

                    <select id="sortSelect" class="sort-select">
                        <option value="load-desc">Sort by Load (High to Low)</option>
                        <option value="load-asc">Sort by Load (Low to High)</option>
                        <option value="name-asc">Sort by Name (A to Z)</option>
                        <option value="initials-asc">Sort by Initials</option>
                    </select>

                    <button type="button" class="print-btn" onclick="window.print()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                            <path d="M6 14h12v8H6z" />
                        </svg>
                        Print Summary
                    </button>
                </div>
            </div>

            <!-- Main Workload Table Card -->
            <div class="summary-table-card">
                <div class="summary-table-header">
                    <div class="summary-table-title">
                        <span>📅</span> Faculty Teaching Load & Schedule Verification
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted, #94a3b8);" id="showingCount">
                        Showing 0 members
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">#</th>
                                <th>Faculty Member</th>
                                <th>Department</th>
                                <th>Daily Schedule Breakdown</th>
                                <th style="text-align: center;">Total Load</th>
                                <th style="text-align: center;">TT Match</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                            <!-- Populated via Javascript -->
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </main>

    <!-- Include Data Files -->
    <script src="<?php echo v_asset('assets/js/facultyData.js'); ?>"></script>
    <script src="<?php echo v_asset('assets/js/timetableData.js'); ?>"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof timetableData === "undefined" || typeof facultyData === "undefined") return;

            let allRows = [];
            let currentFilter = "ALL";
            let currentSearch = "";
            let currentSort = "load-desc";

            // Process data for faculty members in timetableData that exist in facultyData.js
            Object.keys(timetableData).forEach(initials => {
                const info = timetableData[initials];
                let facultyObj = facultyData.find(f => f.initials && f.initials.toUpperCase() === initials.toUpperCase());
                if (!facultyObj) return;

                let name = facultyObj.name;
                let dept = facultyObj.department;
                let designation = facultyObj.designation || "Lecturer";
                let avatarClass = facultyObj.avatarClass || `av-${initials.toLowerCase()}`;

                let daily = { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 };
                
                if (info.schedule && Array.isArray(info.schedule)) {
                    info.schedule.forEach(slot => {
                        if (slot.isRecess) return;
                        ["MON", "TUE", "WED", "THU", "FRI", "SAT"].forEach(day => {
                            if (slot[day] && slot[day].occupied) {
                                daily[day]++;
                            }
                        });
                    });
                }

                let totalLoad = Object.values(daily).reduce((a, b) => a + b, 0);

                allRows.push({
                    initials: initials,
                    name: name,
                    department: dept,
                    designation: designation,
                    avatarClass: avatarClass,
                    daily: daily,
                    totalLoad: totalLoad
                });
            });

            // Add any faculty from facultyData that might not have a timetable tab yet
            facultyData.forEach(f => {
                // Exclude non-teaching administrative/developer roles (Developer, Admin)
                if (f.designation && (
                    f.designation.toLowerCase().includes("developer") ||
                    f.designation.toLowerCase().includes("admin")
                )) return;

                if (!allRows.some(r => r.initials.toUpperCase() === f.initials.toUpperCase())) {
                    allRows.push({
                        initials: f.initials,
                        name: f.name,
                        department: f.department,
                        designation: f.designation,
                        avatarClass: f.avatarClass,
                        daily: { MON: 0, TUE: 0, WED: 0, THU: 0, FRI: 0, SAT: 0 },
                        totalLoad: 0
                    });
                }
            });

            // Update stats
            function updateStats(rows) {
                const totalFac = rows.length;
                const totalHours = rows.reduce((sum, r) => sum + r.totalLoad, 0);
                const avgHours = totalFac > 0 ? (totalHours / totalFac).toFixed(1) : "0.0";
                const maxHours = rows.length > 0 ? Math.max(...rows.map(r => r.totalLoad)) : 0;

                document.getElementById("totalFacultyCount").textContent = totalFac;
                document.getElementById("totalTeachingHours").textContent = `${totalHours} Hrs`;
                document.getElementById("avgWorkload").textContent = `${avgHours} Hrs`;
                document.getElementById("maxWorkload").textContent = `${maxHours} Hrs`;
            }

            // Render table
            function renderTable() {
                let filtered = allRows.filter(r => {
                    // Exclude non-teaching administrative & developer entries (Developer, Admin)
                    if (r.designation && (
                        r.designation.toLowerCase().includes("developer") ||
                        r.designation.toLowerCase().includes("admin")
                    )) return false;

                    let matchesFilter = (currentFilter === "ALL") || (r.department === currentFilter) || (r.department === "Both");
                    let query = currentSearch.toLowerCase().trim();
                    let matchesSearch = !query || 
                        r.name.toLowerCase().includes(query) || 
                        r.initials.toLowerCase().includes(query) || 
                        r.department.toLowerCase().includes(query) ||
                        r.designation.toLowerCase().includes(query);
                    return matchesFilter && matchesSearch;
                });

                // Sorting
                filtered.sort((a, b) => {
                    if (currentSort === "load-desc") return b.totalLoad - a.totalLoad;
                    if (currentSort === "load-asc") return a.totalLoad - b.totalLoad;
                    if (currentSort === "name-asc") return a.name.localeCompare(b.name);
                    if (currentSort === "initials-asc") return a.initials.localeCompare(b.initials);
                    return 0;
                });

                updateStats(filtered);

                const tbody = document.getElementById("summaryTableBody");
                document.getElementById("showingCount").textContent = `Showing ${filtered.length} of ${allRows.length} members`;

                if (filtered.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted, #94a3b8);">
                                🔍 No faculty members found matching your filter criteria.
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = "";
                filtered.forEach((r, idx) => {
                    let deptClass = "it";
                    if (r.department === "Computer Engineering") deptClass = "ce";
                    else if (r.department === "Both") deptClass = "both";

                    let loadBadgeClass = "normal";
                    if (r.totalLoad >= 20) loadBadgeClass = "high";
                    else if (r.totalLoad <= 10) loadBadgeClass = "low";

                    let daysHtml = ["MON", "TUE", "WED", "THU", "FRI", "SAT"].map(day => {
                        let count = r.daily[day];
                        let activeClass = count > 0 ? "active" : "";
                        return `<span class="day-pill ${activeClass}" title="${day}: ${count} Hrs">${count}</span>`;
                    }).join(" ");

                    html += `
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-muted, #94a3b8);">${idx + 1}</td>
                            <td>
                                <div class="fac-cell">
                                    <div class="fac-avatar-sm">${r.initials}</div>
                                    <div>
                                        <div class="fac-name-text">${r.name}</div>
                                        <div class="fac-sub-text">${r.designation} (${r.initials})</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="dept-tag ${deptClass}">${r.department}</span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    ${daysHtml}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="load-badge-main ${loadBadgeClass}">
                                    ${r.totalLoad} Hrs
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; ${r.totalLoad > 0 ? 'background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);' : 'background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4);'}">
                                    ${r.totalLoad > 0 ? '✓ Matched' : '⚠️ No Schedule'}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="timetable?initials=${encodeURIComponent(r.initials)}" class="view-btn">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Grid
                                </a>
                            </td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            }

            // Event Listeners
            document.querySelectorAll(".filter-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
                    this.classList.add("active");
                    currentFilter = this.getAttribute("data-filter");
                    renderTable();
                });
            });

            document.getElementById("summarySearchInput").addEventListener("input", function() {
                currentSearch = this.value;
                renderTable();
            });

            document.getElementById("sortSelect").addEventListener("change", function() {
                currentSort = this.value;
                renderTable();
            });

            // Initial Render
            renderTable();
        });
    </script>
    <?php include 'fab-nav.php'; ?>
</body>

</html>
