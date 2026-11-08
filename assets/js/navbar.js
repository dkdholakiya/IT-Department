/**
 * Shared Top Navigation Bar Controller
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Controls:
 * - Mobile user menu downside collapsible tray (#mobileSearchContainer)
 * - Theme Switcher (Dark Glass / Light Glass)
 * - Color Studio Palette Chooser (transforms accent colors live across the portal)
 * - Notifications popover and alerts
 * - Synchronized desktop & mobile search with 'portal:search' event
 * - Voice assistant command mock
 */

(function () {
  'use strict';

  // --- STATE ---
  const navbarState = {
    theme: localStorage.getItem('portal_theme') || 'light',
    primaryHex: localStorage.getItem('portal_theme_color') || '#10B981',
    mobileUserTrayOpen: false
  };

  // --- 1. TOAST HELPER ---
  function showPortalToast(message) {
    let toast = document.getElementById('portalToastNotification');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'portalToastNotification';
      toast.className = 'fixed bottom-5 right-5 z-[9999] px-4 py-2.5 rounded-2xl glass-pill text-xs font-bold text-[var(--text-primary)] shadow-2xl border border-white/70 transition-all duration-300 opacity-0 translate-y-3 pointer-events-none flex items-center gap-2';
      document.body.appendChild(toast);
    }
    toast.innerHTML = `<span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span><span>${message}</span>`;
    toast.classList.remove('opacity-0', 'translate-y-3', 'pointer-events-none');
    toast.classList.add('opacity-100', 'translate-y-0');

    clearTimeout(window.__portalToastTimeout);
    window.__portalToastTimeout = setTimeout(() => {
      toast.classList.remove('opacity-100', 'translate-y-0');
      toast.classList.add('opacity-0', 'translate-y-3', 'pointer-events-none');
    }, 2800);
  }
  window.showPortalToast = showPortalToast;

  // --- 2. COLOR STUDIO CONTROLLER ---
  function hexToRgb(hex) {
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    const num = parseInt(hex, 16);
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
  }

  function adjustBrightness(hex, percent) {
    const rgb = hexToRgb(hex);
    const r = Math.min(255, Math.max(0, Math.round(rgb.r * (1 + percent / 100))));
    const g = Math.min(255, Math.max(0, Math.round(rgb.g * (1 + percent / 100))));
    const b = Math.min(255, Math.max(0, Math.round(rgb.b * (1 + percent / 100))));
    return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
  }

  function rgbToHsl(r, g, b) {
    r /= 255; g /= 255; b /= 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h = 0, s = 0, l = (max + min) / 2;
    if (max !== min) {
      const d = max - min;
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      switch (max) {
        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
        case g: h = (b - r) / d + 2; break;
        case b: h = (r - g) / d + 4; break;
      }
      h /= 6;
    }
    return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
  }

  function hslToHex(h, s, l) {
    h = ((h % 360) + 360) % 360;
    s = Math.max(0, Math.min(100, s)) / 100;
    l = Math.max(0, Math.min(100, l)) / 100;
    const c = (1 - Math.abs(2 * l - 1)) * s;
    const x = c * (1 - Math.abs((h / 60) % 2 - 1));
    const m = l - c / 2;
    let r = 0, g = 0, b = 0;
    if (h < 60) { r = c; g = x; }
    else if (h < 120) { r = x; g = c; }
    else if (h < 180) { g = c; b = x; }
    else if (h < 240) { g = x; b = c; }
    else if (h < 300) { r = x; b = c; }
    else { r = c; b = x; }
    const toHex = (v) => Math.round((v + m) * 255).toString(16).padStart(2, '0');
    return '#' + toHex(r) + toHex(g) + toHex(b);
  }

  function applyCustomColorPalette(hex) {
    if (!hex) return;
    navbarState.primaryHex = hex;
    localStorage.setItem('portal_theme_color', hex);

    const rgb = hexToRgb(hex);
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    const darkShade = adjustBrightness(hex, -22);
    const lightShade = adjustBrightness(hex, 28);
    const root = document.documentElement;

    const darkRoleColor = hslToHex(hsl.h, Math.min(85, hsl.s), 18);
    const lightRoleColor = hslToHex(hsl.h, Math.min(90, hsl.s), 72);
    root.style.setProperty('--color-primary', hex);
    root.style.setProperty('--color-primary-dark', darkShade);
    root.style.setProperty('--color-primary-light', lightShade);
    root.style.setProperty('--color-primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
    root.style.setProperty('--text-accent', navbarState.theme === 'dark' ? lightShade : darkShade);
    root.style.setProperty('--text-role', navbarState.theme === 'dark' ? lightRoleColor : darkRoleColor);
    root.style.setProperty('--sidebar-active-bg', hex);
    root.style.setProperty('--bar-active', hex);

    // Dynamic High-Definition Glass Borders & Card/Button Outlines
    const isDark = navbarState.theme === 'dark';
    const borderAlpha = isDark ? 0.44 : 0.36;
    const borderStrongAlpha = isDark ? 0.65 : 0.52;
    const borderLightAlpha = isDark ? 0.25 : 0.18;
    root.style.setProperty('--glass-border', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${borderAlpha})`);
    root.style.setProperty('--glass-border-strong', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${borderStrongAlpha})`);
    root.style.setProperty('--glass-border-light', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${borderLightAlpha})`);
    root.style.setProperty('--card-outline-color', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${borderAlpha})`);
    root.style.setProperty('--btn-outline-color', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${borderStrongAlpha})`);
    root.style.setProperty('--glass-shadow', isDark 
      ? `0 14px 34px -4px rgba(0, 0, 0, 0.60), 0 0 0 1px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.28)` 
      : `0 10px 28px -6px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.16), 0 2px 8px -1px rgba(10, 9, 9, 0.04)`);

    // Dynamic Ambient Mesh Wallpaper Colors
    const isDefaultGreen = hex.toUpperCase() === '#10B981';
    if (navbarState.theme === 'dark') {
      root.style.setProperty('--bg-mesh-1', hslToHex((hsl.h + 30) % 360, 45, 10));
      root.style.setProperty('--bg-mesh-2', hslToHex(hsl.h, 50, 12));
      root.style.setProperty('--bg-mesh-3', hslToHex(hsl.h, 35, 5));
      root.style.setProperty('--bg-mesh-4', hslToHex(hsl.h, 55, 14));
    } else {
      if (isDefaultGreen) {
        root.style.setProperty('--bg-mesh-1', '#C7E0ED');
        root.style.setProperty('--bg-mesh-2', '#D4E8C7');
        root.style.setProperty('--bg-mesh-3', '#F2F7F0');
        root.style.setProperty('--bg-mesh-4', '#C9E3B8');
      } else {
        root.style.setProperty('--bg-mesh-1', hslToHex((hsl.h + 45) % 360, 52, 86));
        root.style.setProperty('--bg-mesh-2', hslToHex(hsl.h, 48, 85));
        root.style.setProperty('--bg-mesh-3', hslToHex(hsl.h, 38, 95));
        root.style.setProperty('--bg-mesh-4', hslToHex(hsl.h, 55, 82));
      }
    }

    // AI Assistant shiny gradient
    const aiTop = hslToHex(hsl.h, 95, 92);
    const aiMid = hslToHex(hsl.h, 92, 64);
    const aiBot = hslToHex(hsl.h, 85, 46);
    root.style.setProperty('--ai-btn-grad', `linear-gradient(180deg, ${aiTop} 0%, ${aiMid} 48%, ${aiBot} 100%)`);
    root.style.setProperty('--ai-btn-shadow', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.38)`);

    // Update Color Studio UI elements
    const activeDot = document.getElementById('activeColorDot');
    if (activeDot) activeDot.style.backgroundColor = hex;

    const mobileActiveDot = document.getElementById('mobileActiveColorDot');
    if (mobileActiveDot) mobileActiveDot.style.backgroundColor = hex;

    const readout = document.getElementById('activeHexReadout');
    if (readout) readout.textContent = `Active Palette: ${hex.toUpperCase()}`;

    const customPicker = document.getElementById('desktopCustomColorInput');
    if (customPicker) customPicker.value = hex;

    const mobileCustomPicker = document.getElementById('mobileCustomColorInput');
    if (mobileCustomPicker) mobileCustomPicker.value = hex;

    // Highlight active preset ring
    document.querySelectorAll('.color-preset-btn').forEach(btn => {
      const match = btn.getAttribute('data-theme-color')?.toLowerCase() === hex.toLowerCase();
      btn.classList.toggle('ring-2', match);
      btn.classList.toggle('ring-offset-1', match);
      btn.classList.toggle('ring-current', match);
    });

    window.dispatchEvent(new CustomEvent('portal:theme-color-changed', {
      detail: { hex, rgb, darkShade, lightShade }
    }));
  }
  window.applyCustomColorPalette = applyCustomColorPalette;

  // --- 3. THEME CONTROLLER (Light / Dark Glass) ---
  function applyTheme(mode) {
    navbarState.theme = mode === 'dark' ? 'dark' : 'light';
    localStorage.setItem('portal_theme', navbarState.theme);

    document.body.classList.toggle('theme-dark', navbarState.theme === 'dark');
    document.documentElement.classList.toggle('dark', navbarState.theme === 'dark');

    const activeBtnClass = 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm transition';
    const inactiveBtnClass = 'w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition';

    ['themeLightBtn', 'mobileThemeLightBtn'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.className = (navbarState.theme === 'light') ? activeBtnClass : inactiveBtnClass;
    });

    ['themeDarkBtn', 'mobileThemeDarkBtn'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.className = (navbarState.theme === 'dark') ? activeBtnClass : inactiveBtnClass;
    });

    applyCustomColorPalette(navbarState.primaryHex);

    window.dispatchEvent(new CustomEvent('portal:theme-mode-changed', {
      detail: { theme: navbarState.theme }
    }));
  }
  window.applyTheme = applyTheme;

  // --- 4. INITIALIZATION ---
  function initNavbar() {
    // 1. Initial theme & color bootstrap
    applyTheme(navbarState.theme);
    applyCustomColorPalette(navbarState.primaryHex);

    // 2. Mobile User Menu Tray Toggle (#mobileUserMenuBtn)
    const mobileUserMenuBtn = document.getElementById('mobileUserMenuBtn');
    const mobileSearchContainer = document.getElementById('mobileSearchContainer');
    const mobileUserMenuChevron = document.getElementById('mobileUserMenuChevron');

    if (mobileUserMenuBtn && mobileSearchContainer) {
      mobileUserMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        navbarState.mobileUserTrayOpen = !navbarState.mobileUserTrayOpen;
        mobileSearchContainer.classList.toggle('hidden', !navbarState.mobileUserTrayOpen);
        if (mobileUserMenuChevron) {
          mobileUserMenuChevron.classList.toggle('rotate-180', navbarState.mobileUserTrayOpen);
        }
        // Close popovers if open
        document.getElementById('colorPickerPopover')?.classList.add('hidden');
        document.getElementById('notificationsPopover')?.classList.add('hidden');
      });
    }

    // 3. Theme switch buttons
    const themeLightBtn = document.getElementById('themeLightBtn');
    const themeDarkBtn = document.getElementById('themeDarkBtn');
    const mobileThemeLightBtn = document.getElementById('mobileThemeLightBtn');
    const mobileThemeDarkBtn = document.getElementById('mobileThemeDarkBtn');

    if (themeLightBtn) themeLightBtn.addEventListener('click', () => { applyTheme('light'); showPortalToast('Switched to Glass Light Mode'); });
    if (themeDarkBtn) themeDarkBtn.addEventListener('click', () => { applyTheme('dark'); showPortalToast('Switched to Dark Glass Mode'); });
    if (mobileThemeLightBtn) mobileThemeLightBtn.addEventListener('click', () => { applyTheme('light'); showPortalToast('Switched to Glass Light Mode'); });
    if (mobileThemeDarkBtn) mobileThemeDarkBtn.addEventListener('click', () => { applyTheme('dark'); showPortalToast('Switched to Dark Glass Mode'); });

    // 4. Color Studio Popover & Buttons
    const colorPickerToggleBtn = document.getElementById('colorPickerToggleBtn');
    const colorPickerPopover = document.getElementById('colorPickerPopover');
    const customColorInput = document.getElementById('desktopCustomColorInput');
    const mobileCustomColorInput = document.getElementById('mobileCustomColorInput');

    function toggleColorStudioPopover(e) {
      e.preventDefault();
      e.stopPropagation();
      if (colorPickerPopover) {
        colorPickerPopover.classList.toggle('hidden');
      }
      document.getElementById('notificationsPopover')?.classList.add('hidden');
    }

    if (colorPickerToggleBtn) {
      colorPickerToggleBtn.addEventListener('click', toggleColorStudioPopover);
    }

    if (customColorInput) {
      ['input', 'change'].forEach(evt => {
        customColorInput.addEventListener(evt, (e) => {
          applyCustomColorPalette(e.target.value);
        });
      });
    }

    if (mobileCustomColorInput) {
      ['input', 'change'].forEach(evt => {
        mobileCustomColorInput.addEventListener(evt, (e) => {
          applyCustomColorPalette(e.target.value);
        });
      });
    }

    document.querySelectorAll('.color-preset-btn').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const color = btn.getAttribute('data-theme-color');
        if (color) {
          applyCustomColorPalette(color);
          showPortalToast(`Applied Palette: ${color.toUpperCase()}`);
        }
      });
    });

    // 5. Notifications Popover
    const notificationsBtn = document.getElementById('notificationsBtn');
    const mobileNotifBtn = document.getElementById('mobileNotifBtn');
    const notificationsPopover = document.getElementById('notificationsPopover');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    function toggleNotifications(e) {
      e.stopPropagation();
      if (notificationsPopover) notificationsPopover.classList.toggle('hidden');
      if (colorPickerPopover) colorPickerPopover.classList.add('hidden');
    }

    if (notificationsBtn) notificationsBtn.addEventListener('click', toggleNotifications);
    if (mobileNotifBtn) mobileNotifBtn.addEventListener('click', toggleNotifications);

    if (markAllReadBtn) {
      markAllReadBtn.addEventListener('click', () => {
        const notifBadge = document.getElementById('notifBadge');
        const notifCountPill = document.getElementById('notifCountPill');
        if (notifBadge) notifBadge.classList.add('hidden');
        if (notifCountPill) {
          notifCountPill.textContent = '0 New';
          notifCountPill.className = 'px-2 py-0.5 text-[11px] font-bold rounded-full bg-black/5 dark:bg-white/10 text-[var(--text-muted)]';
        }
        showPortalToast('All notifications marked as read');
      });
    }

    // 6. Search Input Synchronizer (Desktop & Mobile)
    const dashboardSearchInput = document.getElementById('dashboardSearchInput');
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    function broadcastSearch(query) {
      if (clearSearchBtn) {
        clearSearchBtn.classList.toggle('hidden', query.length === 0);
      }
      window.dispatchEvent(new CustomEvent('portal:search', { detail: { query } }));
    }

    if (dashboardSearchInput) {
      dashboardSearchInput.addEventListener('input', (e) => {
        const q = e.target.value;
        if (mobileSearchInput) mobileSearchInput.value = q;
        broadcastSearch(q);
      });
    }

    if (mobileSearchInput) {
      mobileSearchInput.addEventListener('input', (e) => {
        const q = e.target.value;
        if (dashboardSearchInput) dashboardSearchInput.value = q;
        broadcastSearch(q);
      });
    }

    if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', () => {
        if (dashboardSearchInput) dashboardSearchInput.value = '';
        if (mobileSearchInput) mobileSearchInput.value = '';
        broadcastSearch('');
        dashboardSearchInput?.focus();
      });
    }

    // 7. Voice Command Mock
    ['voiceCommandBtn', 'mobileVoiceBtn'].forEach((id) => {
      const btn = document.getElementById(id);
      if (btn) {
        btn.addEventListener('click', () => {
          showPortalToast('🎙️ Voice Assistant: Listening for commands...');
        });
      }
    });

    // 8. Dismiss popovers when clicking outside
    document.addEventListener('click', (e) => {
      if (notificationsPopover && !notificationsPopover.contains(e.target) && e.target !== notificationsBtn && e.target !== mobileNotifBtn) {
        notificationsPopover.classList.add('hidden');
      }
      if (colorPickerPopover && !colorPickerPopover.contains(e.target) &&
          e.target !== colorPickerToggleBtn && !colorPickerToggleBtn?.contains(e.target)) {
        colorPickerPopover.classList.add('hidden');
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavbar);
  } else {
    initNavbar();
  }
})();

