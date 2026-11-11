<?php
/**
 * Global Helpers for Engineering Dept CE & IT Portal
 */

if (!function_exists('v_asset')) {
    /**
     * Appends file modification timestamp to CSS & JS assets to prevent stale browser caches.
     *
     * @param string $path
     * @return string
     */
    function v_asset($path) {
        $cleanPath = ltrim($path, '/');
        $fullPath = rtrim(BASE_PATH, '/\\') . '/' . $cleanPath;
        if (file_exists($fullPath)) {
            return (defined('BASE_URL') ? BASE_URL . '/' : '') . $cleanPath . '?v=' . filemtime($fullPath);
        }
        return (defined('BASE_URL') ? BASE_URL . '/' : '') . $cleanPath . '?v=' . time();
    }
}

if (!function_exists('base_url')) {
    /**
     * Prepend BASE_URL to a relative route/path.
     *
     * @param string $path
     * @return string
     */
    function base_url($path = '') {
        $base = defined('BASE_URL') ? BASE_URL : '';
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('app_config')) {
    /**
     * Get an application configuration value.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function app_config($key = null, $default = null) {
        static $config = null;
        if ($config === null) {
            $configFile = defined('BASE_PATH') ? BASE_PATH . '/config/app.php' : __DIR__ . '/../config/app.php';
            $config = file_exists($configFile) ? require $configFile : [];
        }
        if ($key === null) {
            return $config;
        }
        return $config[$key] ?? $default;
    }
}

if (!function_exists('get_portal_brand_name')) {
    /**
     * Official brand name for all emails, dashboard, and headers.
     *
     * @return string
     */
    function get_portal_brand_name() {
        return 'Engineering Dept CE & IT';
    }
}

if (!function_exists('get_portal_font_config')) {
    /**
     * Reads and parses assets/css/font.css as the Single Source of Truth (SSOT).
     * Extracts the Google Font import URL, font family stack, and font name.
     *
     * @return array
     */
    function get_portal_font_config() {
        static $fontConfig = null;
        if ($fontConfig !== null) {
            return $fontConfig;
        }

        $baseDir = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
        $cssPath = $baseDir . '/assets/css/font.css';

        $importUrl = 'https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap';
        $fontFamily = "'PT Serif', Georgia, 'Times New Roman', serif";
        $fontName = 'PT Serif';

        if (file_exists($cssPath)) {
            $content = file_get_contents($cssPath);

            // Extract @import url('...')
            if (preg_match('/@import\s+url\([\'"]?([^\'")]+)[\'"]?\);/i', $content, $m)) {
                $importUrl = trim($m[1]);
            }

            // Extract --font-primary: ...;
            if (preg_match('/--font-primary:\s*([^;]+);/i', $content, $m)) {
                $fontFamily = trim($m[1]);
            }

            // Extract primary font name inside quotes or first item
            if (preg_match('/[\'"]([^\'",]+)[\'"]/', $fontFamily, $m)) {
                $fontName = trim($m[1]);
            } elseif (preg_match('/^([^,]+)/', $fontFamily, $m)) {
                $fontName = trim($m[1]);
            }
        }

        $fontConfig = [
            'import_url'  => $importUrl,
            'font_family' => $fontFamily,
            'font_name'   => $fontName
        ];

        return $fontConfig;
    }
}

if (!function_exists('apply_portal_font_to_html')) {
    /**
     * Automatically applies the active portal font from assets/css/font.css
     * to any outgoing email HTML.
     * Injects <link> and <style> in <head> and enforces inline typography.
     *
     * @param string $html
     * @return string
     */
    function apply_portal_font_to_html($html) {
        if (empty($html) || !is_string($html)) {
            return $html;
        }

        $font = get_portal_font_config();
        $importUrl = htmlspecialchars($font['import_url'], ENT_QUOTES, 'UTF-8');
        $fontFamily = $font['font_family'];

        // Normalize any hardcoded inline or style block font-family declarations to the active portal font
        $html = preg_replace('/font-family\s*:\s*[^;!}]+?(?=[;!}"]|$)/i', 'font-family: ' . $fontFamily, $html);

        $headTag = "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n" .
                   "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n" .
                   "<link href=\"{$importUrl}\" rel=\"stylesheet\">\n" .
                   "<style id=\"portal-mail-font\">\n" .
                   "  @import url('{$importUrl}');\n" .
                   "  body, table, td, th, div, span, p, a, h1, h2, h3, h4, h5, h6, strong, b, em, label, input, button {\n" .
                   "    font-family: {$fontFamily} !important;\n" .
                   "  }\n" .
                   "</style>\n";

        // If <head> exists, insert right after <head>
        if (stripos($html, '<head>') !== false) {
            $html = preg_replace('/<head>/i', "<head>\n" . $headTag, $html, 1);
        } elseif (stripos($html, '<head') !== false) {
            $html = preg_replace('/(<head[^>]*>)/i', "$1\n" . $headTag, $html, 1);
        } elseif (stripos($html, '<body') !== false) {
            // No head tag, insert before body
            $html = preg_replace('/(<body[^>]*>)/i', "<head>\n" . $headTag . "</head>\n$1", $html, 1);
        } else {
            // Partial snippet: wrap in div with style and font import
            $html = "<div style=\"font-family: {$fontFamily};\">\n" . $headTag . $html . "\n</div>";
        }

        return $html;
    }
}
