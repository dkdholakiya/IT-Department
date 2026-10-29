<?php
// Retrieve error code
$status = $status ?? ($_SERVER['REDIRECT_STATUS'] ?? $_GET['code'] ?? 404);
$status = (int)$status;

// Validate code and set messages
$error_titles = [
    400 => "Bad Request",
    401 => "Unauthorized Access",
    403 => "Access Forbidden",
    404 => "Page Not Found",
    500 => "Internal Server Error",
    503 => "Service Unavailable"
];

$error_descriptions = [
    400 => "The request could not be understood by the server due to malformed syntax.",
    401 => "You must authenticate to access this resource.",
    403 => "You do not have permission to access this directory or page on this server.",
    404 => "The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.",
    500 => "The server encountered an internal error or misconfiguration and was unable to complete your request.",
    503 => "The server is temporarily unable to service your request due to maintenance downtime or capacity problems."
];

$title = $error_titles[$status] ?? "Unexpected Error";
$description = $error_descriptions[$status] ?? "An unexpected error occurred while processing your request.";

// Set proper HTTP response code
http_response_code($status);

// Calculate base directory dynamically (subdirectory-safe)
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_dir = '/' . trim(str_replace('error.php', '', $script_name), '/') . '/';
$base_dir = ($base_dir === '//') ? '/' : $base_dir;
$page_title = $status . ' ' . $title . ' — Faculty Portal';
$page_css = [
    'assets/css/portal.css',
    'assets/css/error.css',
];
$body_class = (isset($_COOKIE['portal_dept']) && $_COOKIE['portal_dept'] === 'CE') ? 'ce-active' : '';
include 'head.php';
?>
    <!-- Background particles -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
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
    <div class="orb orb-3" aria-hidden="true"></div>

    <div class="error-container">
        <div class="error-card">
            <h1 class="error-code"><?php echo $status; ?></h1>
            <h2 class="error-title"><?php echo htmlspecialchars($title); ?></h2>
            <p class="error-desc"><?php echo htmlspecialchars($description); ?></p>
            
            <a href="<?php echo base_url('/'); ?>" class="error-home-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                <span>Back to Portal Home</span>
            </a>
        </div>
    </div>

    <script>
        // Adjust background particles/orbs based on local storage department selection
        const savedDept = localStorage.getItem('portal_dept');
        if (savedDept === 'CE') {
            document.body.classList.add('ce-active');
        } else {
            document.body.classList.remove('ce-active');
        }
    </script>
    <?php include_once 'theme-toggle.php'; ?>
</body>
</html>
