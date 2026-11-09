<?php
/**
 * Faculty Timetable Auto-Updater & Google Sheets Syncer
 * Department of CE & IT — Smart Management Dashboard
 */

$compilerFile = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2)) . '/Services/timetable-compiler.php';
if (file_exists($compilerFile)) {
    require_once $compilerFile;
} elseif (file_exists('timetable-compiler.php')) {
    require_once 'timetable-compiler.php';
}

$syncFile = (defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2)) . '/Services/sync-timetables.php';
if (file_exists($syncFile)) {
    require_once $syncFile;
} elseif (file_exists('sync-timetables.php')) {
    require_once 'sync-timetables.php';
}

$syncSource = 'Local Excel Database';
if (isset($_GET['sync']) && $_GET['sync'] === 'google') {
    $syncSource = 'Google Sheets Live Sync';
    if (function_exists('syncFacultyTimetable')) {
        $syncRes = syncFacultyTimetable();
        $success = $syncRes['success'] ?? false;
        $message = $syncRes['message'] ?? 'Synchronization completed.';
        $facultyCount = $syncRes['count'] ?? 0;
    } else {
        $success = false;
        $message = 'Sync function not found.';
        $facultyCount = 0;
    }
} else {
    if (function_exists('compileTimetableData')) {
        $res = compileTimetableData();
        $success = $res['success'] ?? false;
        $message = $res['message'] ?? 'Compilation completed.';
        $facultyCount = $res['count'] ?? 0;
    } else {
        $success = false;
        $message = 'Compiler function not found.';
        $facultyCount = 0;
    }
}

$page_title = 'Timetable Sync Status — CE & IT Department';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard Layout Exactly (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'timetable';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Timetable Schedules';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + STATUS WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Timetable Syncer';
      $nav_subtitle = 'CE & IT Department • Auto-Compilation & Live Sync';
      $nav_search_placeholder = 'Search timetable schedules...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN STATUS WORKSPACE ================= -->
      <main class="flex-1 min-h-0 min-w-0 flex items-center justify-center p-2 sm:p-4">
        <div class="glass-card w-full max-w-xl p-6 sm:p-8 flex flex-col items-center text-center shadow-2xl relative overflow-hidden">
          
          <!-- Decorative Top Glow Accent -->
          <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-48 h-24 rounded-full <?php echo $success ? 'bg-[var(--color-primary)]/20' : 'bg-rose-500/20'; ?> blur-2xl pointer-events-none"></div>

          <!-- Status Icon Badge -->
          <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl mb-4 relative shadow-sm <?php echo $success ? 'bg-[var(--color-primary)]/15 text-[var(--color-primary)]' : 'bg-rose-500/15 text-rose-500'; ?>">
            <span class="absolute inset-0 rounded-full <?php echo $success ? 'bg-[var(--color-primary)]/20 animate-ping' : 'bg-rose-500/20'; ?>"></span>
            <span class="relative"><?php echo $success ? '🚀' : '⚠️'; ?></span>
          </div>

          <!-- Status Title -->
          <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[var(--text-primary)]">
            <?php echo $success ? 'Timetable Compilation Complete!' : 'Synchronization Issue Encountered'; ?>
          </h2>

          <!-- Status Source Pill -->
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full glass-pill text-xs font-semibold text-[var(--text-muted)] mt-2">
            <span class="w-1.5 h-1.5 rounded-full <?php echo $success ? 'bg-[var(--color-primary)]' : 'bg-rose-500'; ?>"></span>
            <span>Source: <?php echo htmlspecialchars($syncSource); ?></span>
          </div>

          <!-- Detailed Message Box -->
          <div class="w-full mt-5 p-4 rounded-2xl glass-card bg-white/40 dark:bg-black/20 text-left border border-white/40 dark:border-white/10 text-xs sm:text-sm text-[var(--text-secondary)] leading-relaxed">
            <div class="font-bold text-[var(--text-primary)] mb-1 flex items-center gap-2">
              <svg class="w-4 h-4 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
              </svg>
              <span>Execution Summary</span>
            </div>
            <p class="break-words"><?php echo htmlspecialchars($message); ?></p>
          </div>

          <!-- Stat Chips Grid -->
          <div class="grid grid-cols-2 gap-3 w-full mt-4">
            <div class="p-3 rounded-2xl glass-pill flex flex-col items-center">
              <span class="text-[11px] font-medium text-[var(--text-muted)]">Faculty Count</span>
              <span class="text-lg font-bold text-[var(--text-primary)] mt-0.5"><?php echo (int)$facultyCount; ?></span>
            </div>
            <div class="p-3 rounded-2xl glass-pill flex flex-col items-center">
              <span class="text-[11px] font-medium text-[var(--text-muted)]">Synced At</span>
              <span class="text-xs font-bold text-[var(--text-primary)] mt-1"><?php echo date('h:i A'); ?></span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5 w-full">
            <a href="<?php echo base_url('timetable'); ?>" class="h-10 px-5 rounded-full bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-110 active:scale-95 transition shadow-md flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
              </svg>
              <span>Back to Timetable</span>
            </a>

            <a href="<?php echo base_url('update-timetable?sync=google'); ?>" class="h-10 px-4 rounded-full glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-white/80 active:scale-95 transition flex items-center gap-1.5" title="Force Google Sheets Sync">
              <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
              <span>Re-Sync Google Sheets</span>
            </a>
          </div>

        </div>
      </main>

    </div>
  </div>

  <!-- Shared Modular Scripts -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
</body>
</html>
