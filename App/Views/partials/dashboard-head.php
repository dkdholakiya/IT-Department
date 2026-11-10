<?php
/**
 * Shared Dashboard & Modern Theme Head Partial
 * Department of CE & IT Portal
 *
 * Variables supported:
 * @var string|null       $page_title   Page title
 * @var string|null       $body_class   CSS classes for <body> tag
 * @var string|null       $extra_head   Extra HTML (scripts, meta, inline styles) inside <head>
 * @var array|string|null $page_css     Optional extra CSS files to load
 * @var bool|null         $include_body Whether to output <body> tag (defaults to true)
 */

$title = $page_title ?? 'CE & IT Department Portal';
$bodyClass = $body_class ?? 'antialiased selection:bg-[var(--color-primary)] selection:text-white lg:h-screen lg:w-screen lg:overflow-hidden';
$includeBody = $include_body ?? true;

// Optional extra css
$cssList = [];
if (!empty($page_css)) {
    $cssList = is_array($page_css) ? $page_css : [$page_css];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Immediate Theme & Dynamic Color Preloader (Eliminates Flash of Default Theme / Lime Green on Refresh) -->
  <script>
    (function() {
      try {
        var theme = localStorage.getItem('portal_theme') || 'light';
        if (theme === 'dark') {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }

        var hex = localStorage.getItem('portal_theme_color') || '#10B981';
        if (hex) {
          var clean = hex.replace('#', '');
          if (clean.length === 3) clean = clean.split('').map(function(c) { return c + c; }).join('');
          var num = parseInt(clean, 16);
          var r = (num >> 16) & 255, g = (num >> 8) & 255, b = num & 255;

          var rN = r / 255, gN = g / 255, bN = b / 255;
          var max = Math.max(rN, gN, bN), min = Math.min(rN, gN, bN);
          var h = 0, s = 0, l = (max + min) / 2;
          if (max !== min) {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
              case rN: h = (gN - bN) / d + (gN < bN ? 6 : 0); break;
              case gN: h = (bN - rN) / d + 2; break;
              case bN: h = (rN - gN) / d + 4; break;
            }
            h /= 6;
          }
          var hue = Math.round(h * 360);

          function hslToHex(h, s, l) {
            h = ((h % 360) + 360) % 360;
            s = Math.max(0, Math.min(100, s)) / 100;
            l = Math.max(0, Math.min(100, l)) / 100;
            var c = (1 - Math.abs(2 * l - 1)) * s;
            var x = c * (1 - Math.abs((h / 60) % 2 - 1));
            var m = l - c / 2;
            var rP = 0, gP = 0, bP = 0;
            if (h < 60) { rP = c; gP = x; }
            else if (h < 120) { rP = x; gP = c; }
            else if (h < 180) { gP = c; bP = x; }
            else if (h < 240) { gP = x; bP = c; }
            else if (h < 300) { rP = x; bP = c; }
            else { rP = c; bP = x; }
            function toHex(v) { return Math.round((v + m) * 255).toString(16).padStart(2, '0'); }
            return '#' + toHex(rP) + toHex(gP) + toHex(bP);
          }

          function adjustBrightness(percent) {
            var rB = Math.min(255, Math.max(0, Math.round(r * (1 + percent / 100))));
            var gB = Math.min(255, Math.max(0, Math.round(g * (1 + percent / 100))));
            var bB = Math.min(255, Math.max(0, Math.round(b * (1 + percent / 100))));
            return '#' + ((1 << 24) + (rB << 16) + (gB << 8) + bB).toString(16).slice(1);
          }

          var darkShade = adjustBrightness(-22);
          var lightShade = adjustBrightness(28);
          var aiTop = hslToHex(hue, 95, 92);
          var aiMid = hslToHex(hue, 92, 64);
          var aiBot = hslToHex(hue, 85, 46);

          var root = document.documentElement;
          root.style.setProperty('--color-primary', hex);
          root.style.setProperty('--color-primary-dark', darkShade);
          root.style.setProperty('--color-primary-light', lightShade);
          root.style.setProperty('--color-primary-rgb', r + ', ' + g + ', ' + b);
          root.style.setProperty('--sidebar-active-bg', hex);
          root.style.setProperty('--bar-active', hex);
          root.style.setProperty('--ai-btn-grad', 'linear-gradient(180deg, ' + aiTop + ' 0%, ' + aiMid + ' 48%, ' + aiBot + ' 100%)');
          root.style.setProperty('--ai-btn-shadow', 'rgba(' + r + ', ' + g + ', ' + b + ', 0.38)');

          var isDark = theme === 'dark';
          var borderAlpha = isDark ? 0.44 : 0.36;
          var borderStrongAlpha = isDark ? 0.65 : 0.52;
          var borderLightAlpha = isDark ? 0.25 : 0.18;
          root.style.setProperty('--glass-border', 'rgba(' + r + ', ' + g + ', ' + b + ', ' + borderAlpha + ')');
          root.style.setProperty('--glass-border-strong', 'rgba(' + r + ', ' + g + ', ' + b + ', ' + borderStrongAlpha + ')');
          root.style.setProperty('--glass-border-light', 'rgba(' + r + ', ' + g + ', ' + b + ', ' + borderLightAlpha + ')');
          root.style.setProperty('--card-outline-color', 'rgba(' + r + ', ' + g + ', ' + b + ', ' + borderAlpha + ')');
          root.style.setProperty('--btn-outline-color', 'rgba(' + r + ', ' + g + ', ' + b + ', ' + borderStrongAlpha + ')');
          root.style.setProperty('--glass-shadow', isDark 
            ? '0 14px 34px -4px rgba(0, 0, 0, 0.60), 0 0 0 1px rgba(' + r + ', ' + g + ', ' + b + ', 0.28)' 
            : '0 10px 28px -6px rgba(' + r + ', ' + g + ', ' + b + ', 0.16), 0 2px 8px -1px rgba(10, 9, 9, 0.04)');

          if (isDark) {
            root.style.setProperty('--bg-mesh-1', hslToHex((hue + 30) % 360, 45, 10));
            root.style.setProperty('--bg-mesh-2', hslToHex(hue, 50, 12));
            root.style.setProperty('--bg-mesh-3', hslToHex(hue, 35, 5));
            root.style.setProperty('--bg-mesh-4', hslToHex(hue, 55, 14));
          } else if (hex.toUpperCase() !== '#10B981') {
            root.style.setProperty('--bg-mesh-1', hslToHex((hue + 45) % 360, 52, 86));
            root.style.setProperty('--bg-mesh-2', hslToHex(hue, 48, 85));
            root.style.setProperty('--bg-mesh-3', hslToHex(hue, 38, 95));
            root.style.setProperty('--bg-mesh-4', hslToHex(hue, 55, 82));
          }
        }
      } catch (e) {}
    })();
  </script>

  <!-- Immediate Desktop Sidebar State Preloader (Eliminates Sidebar Delayed Expansion / Flash on Refresh) -->
  <script>
    (function() {
      try {
        var desktopMode = localStorage.getItem('portal_sidebar_desktop_mode');
        if (window.innerWidth >= 1024) {
          if (desktopMode === 'expanded') {
            document.documentElement.classList.add('sidebar-desktop-expanded', 'sidebar-no-transition');
          } else if (desktopMode === 'hidden') {
            document.documentElement.classList.add('sidebar-desktop-hidden', 'sidebar-no-transition');
          }
        }
      } catch (e) {}
    })();
  </script>

  <!-- High-Definition Dynamic Favicons -->
  <link rel="icon" type="image/png" sizes="64x64" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-64x64.png') : 'assets/images/favicon-64x64.png'; ?>">
  <link rel="shortcut icon" type="image/png" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-64x64.png') : 'assets/images/favicon-64x64.png'; ?>">
  <link rel="icon" type="image/svg+xml" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon.svg') : 'assets/images/favicon.svg'; ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-32x32.png') : 'assets/images/favicon-32x32.png'; ?>">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script>
    tailwind.config = {
      darkMode: 'class'
    };
  </script>

  <!-- Google Fonts Preconnect & Unified Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Modular Layout & Theme Stylesheets (Shared Across All Portal Pages) -->
  <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset('assets/css/theme.css') : 'assets/css/theme.css'; ?>">
  <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset('assets/css/navbar.css') : 'assets/css/navbar.css'; ?>">
  <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset('assets/css/sidebar.css') : 'assets/css/sidebar.css'; ?>">
  <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset('assets/css/toast.css') : 'assets/css/toast.css'; ?>">

  <!-- Universal Toast Notification System Engine (Bootstrap 5 Toast Top-Right) -->
  <script>
    window.PORTAL_LOGO_URL = '<?php echo function_exists("v_asset") ? v_asset("assets/images/favicon-64x64.png") : "assets/images/favicon-64x64.png"; ?>';
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/toast.js') : 'assets/js/toast.js'; ?>"></script>

  <!-- Global Typography -->
  <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset('assets/css/font.css') : 'assets/css/font.css'; ?>">

  <!-- Page-Specific Stylesheets (e.g. dashboard.css, calendar.css) -->
  <?php foreach ($cssList as $css): ?>
    <?php if (preg_match('#^(https?:)?//#', $css)): ?>
      <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
    <?php else: ?>
      <link rel="stylesheet" href="<?php echo function_exists('v_asset') ? v_asset($css) : $css; ?>">
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- Extra Head Elements -->
  <?php if (!empty($extra_head)): ?>
    <?php echo $extra_head; ?>
  <?php endif; ?>
</head>
<?php if ($includeBody): ?>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">
  <script>
    try {
      if (localStorage.getItem('portal_theme') === 'dark') {
        document.body.classList.add('theme-dark');
      }
    } catch(e) {}
  </script>
  <?php
  // Check and dispatch PHP session flash notifications if present
  if (session_status() === PHP_SESSION_NONE) { @session_start(); }
  $flashMsg = $_SESSION['flash_message'] ?? $_SESSION['flash_success'] ?? $_SESSION['flash_error'] ?? null;
  $flashType = isset($_SESSION['flash_error']) ? 'error' : (isset($_SESSION['flash_success']) ? 'success' : 'theme');
  if ($flashMsg) {
      unset($_SESSION['flash_message'], $_SESSION['flash_success'], $_SESSION['flash_error']);
      echo '<script>window.addEventListener("DOMContentLoaded", function() { if (window.PortalToast) { window.PortalToast.show(' . json_encode($flashMsg) . ', ' . json_encode($flashType) . '); } });</script>';
  }
  ?>
<?php endif; ?>
