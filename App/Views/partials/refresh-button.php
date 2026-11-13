<?php
/**
 * Responsive Bottom-Left Hard Refresh & Cache Bust Button Partial
 * Fixed in the bottom-left corner on mobile & tablet viewports (<1024px).
 * Tapping triggers hard cache-busting reload, purges browser caches, and reloads fresh DOM.
 */
?>
<!-- Responsive Bottom-Left Hard Refresh & Cache Bust Floating Pill -->
<div id="responsiveRefreshContainer" class="fixed bottom-4 left-4 z-40 lg:hidden flex items-center select-none pointer-events-auto" style="touch-action: manipulation;">
  <button
    id="responsiveHardRefreshBtn"
    type="button"
    class="side-menu-glass h-11 px-3.5 rounded-full flex items-center gap-2 text-[var(--text-primary)] hover:scale-105 active:scale-95 transition-all duration-300 shadow-xl cursor-pointer group border border-white/60 dark:border-white/15"
    title="Hard Refresh Page & Clear Browser Cache"
    aria-label="Hard Refresh and Cache Bust"
  >
    <!-- Rotating Reload Icon with Primary Accent Background -->
    <span class="w-7 h-7 rounded-full bg-[var(--color-primary)]/15 flex items-center justify-center text-[var(--color-primary)] group-hover:bg-[var(--color-primary)] group-hover:text-white transition-all duration-300 shrink-0">
      <svg id="refreshBtnIcon" class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
      </svg>
</span>
    
  </button>
</div>

<script>
  (function () {
    var btn = document.getElementById('responsiveHardRefreshBtn');
    if (!btn) return;

    btn.addEventListener('click', async function (e) {
      e.preventDefault();
      e.stopPropagation();

      if (btn.dataset.busy === 'true') return;
      btn.dataset.busy = 'true';
      btn.style.pointerEvents = 'none';

      var icon = document.getElementById('refreshBtnIcon');
      var label = document.getElementById('refreshBtnText');

      if (icon) {
        icon.classList.add('animate-spin');
        icon.style.transform = 'rotate(360deg)';
      }
      if (label) {
        label.textContent = 'Clearing...';
      }

      // Show feedback toast notification
      if (typeof window.showPortalToast === 'function') {
        window.showPortalToast('⚡ Hard refreshing & busting cache...', 'info');
      } else if (typeof window.showToast === 'function') {
        window.showToast('⚡ Hard refreshing & busting cache...', 'info');
      }

      // 1. Clear CacheStorage API entries (Service Worker / PWA caches)
      if ('caches' in window) {
        try {
          var cacheKeys = await caches.keys();
          await Promise.all(cacheKeys.map(function (k) { return caches.delete(k); }));
        } catch (err) {}
      }

      // 2. Clear Session Preloader/Splash flags for complete fresh bootstrap
      try {
        sessionStorage.removeItem('gmiu_app_splash_shown');
      } catch (err) {}

      // 3. Construct Cache-Busted URL with fresh timestamp
      var targetUrl = new URL(window.location.href);
      targetUrl.searchParams.delete('_nocache');
      targetUrl.searchParams.delete('_v');
      targetUrl.searchParams.delete('_t');
      targetUrl.searchParams.set('_v', Date.now().toString());
      targetUrl.searchParams.set('_nocache', '1');

      // 4. Perform Hard Reload
      setTimeout(function () {
        window.location.replace(targetUrl.toString());
        setTimeout(function () {
          window.location.reload(true);
        }, 300);
      }, 200);
    });
  })();
</script>

