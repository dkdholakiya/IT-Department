<?php
/**
 * Faculty Timetable Auto-Updater from Excel
 */

require_once __DIR__ . '/timetable-compiler.php';
$res = compileTimetableData();
$success = $res['success'] ?? false;
$message = $res['message'] ?? '';
$facultyCount = $res['count'] ?? 0;
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
