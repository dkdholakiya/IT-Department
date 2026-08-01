<?php
/**
 * GMIU IT Department — Live Server Automatic Cache Busting Engine
 * Eliminates the need for users to press Ctrl + Shift + R after live data updates.
 */

// Prevent browsers and proxy servers from caching dynamic HTML page responses
if (!headers_sent()) {
    header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}

/**
 * Helper: v_asset($path)
 * Appends file modification timestamp to CSS & JS assets (e.g. assets/js/facultyData.js?v=1785567890).
 * Every time a file is edited or re-uploaded on live, the timestamp changes, forcing browsers
 * to automatically fetch the updated version without manual Ctrl+Shift+R hard reloads.
 */
if (!function_exists('v_asset')) {
    function v_asset($path) {
        $cleanPath = ltrim($path, '/');
        $fullPath = __DIR__ . '/' . $cleanPath;
        if (file_exists($fullPath)) {
            return $cleanPath . '?v=' . filemtime($fullPath);
        }
        return $cleanPath . '?v=' . time();
    }
}
