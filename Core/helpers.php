<?php
/**
 * Global Helpers for IT Department Portal
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
