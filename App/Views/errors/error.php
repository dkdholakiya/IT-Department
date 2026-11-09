<?php
/**
 * Universal Error Page Handler (400, 401, 403, 404, 405, 500, 503)
 * Department of CE & IT — Smart Management Dashboard
 */

// Retrieve error code
$status = $status ?? ($_SERVER['REDIRECT_STATUS'] ?? $_GET['code'] ?? 404);
$status = (int)$status;

// Validate code and set messages
$error_titles = [
    400 => "Bad Request",
    401 => "Unauthorized Access",
    403 => "Access Forbidden",
    404 => "Page Not Found",
    405 => "Method Not Allowed",
    500 => "Internal Server Error",
    503 => "Service Unavailable"
];

$error_descriptions = [
    400 => "The request could not be processed due to invalid or malformed request parameters.",
    401 => "Authentication is required to access this departmental workspace or resource.",
    403 => "Access to this resource is restricted. You do not have permission to view this directory.",
    404 => "The requested portal page could not be located. It may have been moved, renamed, or updated.",
    405 => "The HTTP method used for this request is not supported for this endpoint.",
    500 => "The server encountered an internal error while processing your request. Please try again shortly.",
    503 => "The portal service is temporarily undergoing maintenance or experiencing peak load."
];

$title = $error_titles[$status] ?? "System Notification";
$description = $error_descriptions[$status] ?? "An unexpected event occurred while processing your request.";

// Set proper HTTP response code
if (!headers_sent()) {
    http_response_code($status);
}

$page_title = $status . ' ' . $title . ' — CE & IT Portal';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN ERROR WRAPPER: Responsive Ambient Viewport Centering -->
  <div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 select-none relative overflow-hidden">
    
    <!-- Background Ambient Glow Discs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[var(--color-primary)]/15 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-[var(--color-primary)]/15 blur-3xl pointer-events-none"></div>

    <!-- Center Glassmorphic Error Card -->
    <div class="glass-card w-full max-w-lg p-7 sm:p-10 flex flex-col items-center text-center shadow-2xl relative border border-white/70 dark:border-white/10">
      
      <!-- Status Code Hero Pill -->
      <div class="px-5 py-2 rounded-full glass-pill text-xs font-extrabold uppercase tracking-widest text-[var(--color-primary)] flex items-center gap-2 mb-4 shadow-sm">
        <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-ping"></span>
        <span>HTTP Status <?php echo $status; ?></span>
      </div>

      <!-- Giant Monospace / PT Serif Number -->
      <h1 class="text-6xl sm:text-7xl font-extrabold tracking-tight text-[var(--text-primary)] leading-none my-1 font-mono">
        <?php echo $status; ?>
      </h1>

      <!-- Error Title -->
      <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[var(--text-primary)] mt-3">
        <?php echo htmlspecialchars($title); ?>
      </h2>

      <!-- Description -->
      <p class="text-xs sm:text-sm text-[var(--text-muted)] mt-2 max-w-sm leading-relaxed">
        <?php echo htmlspecialchars($description); ?>
      </p>

      <!-- Department Context Pill -->
      <div class="mt-4 px-3 py-1 rounded-full bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-[11px] font-semibold text-[var(--text-secondary)]">
        Department of Computer Engineering & Information Technology
      </div>

      <!-- Action Navigation Buttons -->
      <div class="mt-7 flex flex-wrap items-center justify-center gap-2.5 w-full">
        <!-- Dashboard Button -->
        <a href="<?php echo base_url('dashboard'); ?>" class="h-10 px-5 rounded-full bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-110 active:scale-95 transition shadow-md flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
          </svg>
          <span>Dashboard Overview</span>
        </a>

        <!-- Portal Landing Button -->
        <a href="<?php echo base_url('/'); ?>" class="h-10 px-4 rounded-full glass-pill text-xs font-bold text-[var(--text-primary)] hover:bg-white/80 active:scale-95 transition flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          </svg>
          <span>3D Portal</span>
        </a>

        <!-- Back Button -->
        <button type="button" onclick="history.back()" class="h-10 px-3.5 rounded-full glass-pill text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] active:scale-95 transition flex items-center gap-1.5" title="Go back">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
          </svg>
          <span>Back</span>
        </button>
      </div>

    </div>
  </div>

</body>
</html>
