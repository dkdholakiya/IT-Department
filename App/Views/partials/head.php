<?php
/**
 * Shared HTML Head Partial
 * Department of CE & IT — Faculty & Student Portal
 *
 * Variables supported:
 * @var string|null $page_title       Page title (falls back to $title or default)
 * @var string|null $page_description Meta description (falls back to $description or default)
 * @var array|string|null $page_css   Array or string of CSS files relative to root or URLs
 * @var string|null $extra_head       Raw HTML for extra scripts/styles/meta in <head>
 * @var string|null $body_class       CSS classes for <body> tag
 * @var bool|null   $include_body     Whether to output <body> tag (defaults to true)
 */

$title = $page_title ?? $title ?? 'CE & IT Department — Faculty Portal';
$description = $page_description ?? $description ?? 'Department of CE & IT — Academic Portal for faculty, students, schedules, reports, and departmental activities.';
$includeBody = $include_body ?? true;
$closeHead = $close_head ?? true;
$bodyClass = $body_class ?? '';

// Ensure page_css is an array
$cssList = [];
if (!empty($page_css)) {
    $cssList = is_array($page_css) ? $page_css : [$page_css];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Favicons -->
    <link rel="shortcut icon" href="<?php echo v_asset('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo v_asset('assets/images/favicon.ico'); ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo v_asset('assets/images/apple-touch-icon.png'); ?>">

    <!-- Google Fonts Preconnect & Unified Font Families -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Kameron:wght@400..700&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap" rel="stylesheet">

    <!-- Page-Specific Stylesheets -->
    <?php foreach ($cssList as $css): ?>
        <?php if (preg_match('#^(https?:)?//#', $css)): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php else: ?>
            <link rel="stylesheet" href="<?php echo v_asset($css); ?>">
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Light Theme Stylesheet (Global) -->
    <link rel="stylesheet" href="<?php echo v_asset('assets/css/theme-light.css'); ?>">

    <!-- Extra Head Elements (e.g. CDNs, inline styles) -->
    <?php if (!empty($extra_head)): ?>
        <?php echo $extra_head; ?>
    <?php endif; ?>
<?php if ($closeHead): ?>
</head>

<?php if ($includeBody): ?>
<body<?php echo !empty($bodyClass) ? ' class="' . htmlspecialchars($bodyClass) . '"' : ''; ?>>
<?php endif; ?>
<?php endif; ?>
