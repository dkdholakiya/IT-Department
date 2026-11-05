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

  <!-- High-Definition Dynamic Favicons -->
  <link rel="icon" type="image/svg+xml" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon.svg') : 'assets/images/favicon.svg'; ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-32x32.png') : 'assets/images/favicon-32x32.png'; ?>">
  <link rel="shortcut icon" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon.ico') : 'assets/images/favicon.ico'; ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon.ico') : 'assets/images/favicon.ico'; ?>" type="image/x-icon">

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
<?php endif; ?>
