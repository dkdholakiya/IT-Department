<?php
/**
 * Faculty Timetable Auto-Updater & Google Sheets Syncer
 */

require_once 'timetable-compiler.php';
require_once 'sync-timetables.php';

$res = [];
if (isset($_GET['sync']) && $_GET['sync'] === 'google') {
    $syncRes = syncFacultyTimetable();
    if ($syncRes['success']) {
        $success = true;
        $message = $syncRes['message'];
        $facultyCount = $syncRes['count'] ?? 0;
    } else {
        $success = false;
        $message = $syncRes['message'];
        $facultyCount = 0;
    }
} else {
    $res = compileTimetableData();
    $success = $res['success'] ?? false;
    $message = $res['message'] ?? '';
    $facultyCount = $res['count'] ?? 0;
}
$page_title = 'Timetable Data Syncer — CE & IT Department';
$page_css = [
    'assets/css/portal.css',
    'assets/css/faculty.css',
    'assets/css/timetable-status.css',
];
include 'head.php';
?>
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
        <a href="<?php echo base_url('timetable'); ?>" class="back-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Back to Timetable
        </a>
    </div>
    <?php include_once 'theme-toggle.php'; ?>
</body>
</html>
