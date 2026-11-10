<?php
/**
 * Shared Bootstrap 5 Toast Container Partial
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Provides a unified top-right Bootstrap 5 Toast container for all portal pages.
 * Supports auto-stacking, dynamic portal theme integration, and colored status variants:
 * - Green (Success / Right)
 * - Red (Error / Wrong)
 * - Amber (Warning)
 * - Blue (Info)
 * - Dynamic Theme Color (Default portal theme from Color Studio)
 * - Custom Color presets (Purple, Pink, Orange, Teal, or custom Hex)
 */
$toastLogoUrl = function_exists('v_asset') ? v_asset('assets/images/favicon-64x64.png') : (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/assets/images/favicon-64x64.png' : 'assets/images/favicon-64x64.png');
?>
<!-- ================= GLOBAL BOOTSTRAP TOAST CONTAINER (TOP-RIGHT) ================= -->
<div aria-live="polite" aria-atomic="true" class="position-relative toast-portal-root">
  <div id="portalToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 999999;" data-portal-toast-container>
    <!-- Dynamic Bootstrap 5 Toasts injected here by assets/js/toast.js -->
  </div>
</div>
<script>
  if (!window.PORTAL_LOGO_URL) {
    window.PORTAL_LOGO_URL = '<?php echo htmlspecialchars($toastLogoUrl, ENT_QUOTES, "UTF-8"); ?>';
  }
</script>

