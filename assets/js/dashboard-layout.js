/**
 * GMIU Portal — Unified Dashboard & Academic Layout Engine
 * Features:
 * - Dynamic Theme Palette Injection (Live Hex Color calculation & CSS variable updates)
 * - Dark Glass & Light Glass Mode Toggle with Persistence (localStorage)
 * - Samsung Edge Pull Handle & Mobile Sidebar Drawer Engine
 * - Desktop 3-State Sidebar (Compact Icons, Full Text Expanded, Hidden)
 * - Color Studio & Notifications Popovers
 * - Universal Toast Notifications
 */
(function (window) {
  'use strict';

  // Shared Layout State
  const layoutState = {
    theme: localStorage.getItem('portal_theme') || 'light',
    primaryHex: localStorage.getItem('portal_theme_color') || '#10B981',
    desktopSidebarMode: localStorage.getItem('portal_sidebar_desktop_mode') || 'compact', // 'compact' | 'expanded' | 'hidden'
    mobileExpandedNames: localStorage.getItem('portal_sidebar_mobile_expanded') === 'true'
  };

  // Helper: Hex to RGB
  function hexToRgb(hex) {
    let clean = hex.replace('#', '');
    if (clean.length === 3) {
      clean = clean.split('').map(c => c + c).join('');
    }
    const num = parseInt(clean, 16);
    return {
      r: (num >> 16) & 255,
      g: (num >> 8) & 255,
      b: num & 255
    };
  }

  // Helper: RGB to HSL
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

  // Helper: HSL to Hex
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
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`.toUpperCase();
  }

  // --- 1. DYNAMIC COLOR PALETTE ENGINE ---
  function applyCustomColorPalette(hexColor) {
    if (!hexColor || !/^#[0-9A-Fa-f]{6}$/.test(hexColor)) return;
    layoutState.primaryHex = hexColor.toUpperCase();
    localStorage.setItem('portal_theme_color', hexColor.toUpperCase());

    const rgb = hexToRgb(hexColor);
    const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
    const root = document.documentElement;

    const lightShade = hslToHex(hsl.h, Math.min(95, hsl.s + 20), Math.min(82, hsl.l + 24));
    const darkShade = hslToHex(hsl.h, Math.min(90, hsl.s + 10), Math.max(18, hsl.l - 18));
    const complementaryPeak = hslToHex((hsl.h + 215) % 360, 85, 52);

    const fontPrimaryLight = hslToHex(hsl.h, Math.min(65, hsl.s + 15), 14);
    const fontSecondaryLight = hslToHex(hsl.h, Math.min(50, hsl.s), 28);
    const fontMutedLight = hslToHex(hsl.h, Math.min(35, hsl.s), 44);

    const fontPrimaryDark = hslToHex(hsl.h, Math.min(70, hsl.s), 95);
    const fontSecondaryDark = hslToHex(hsl.h, Math.min(55, hsl.s), 80);
    const fontMutedDark = hslToHex(hsl.h, Math.min(40, hsl.s), 64);

    root.style.setProperty('--color-primary', layoutState.primaryHex);
    root.style.setProperty('--color-primary-dark', darkShade);
    root.style.setProperty('--color-primary-light', lightShade);
    root.style.setProperty('--color-primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
    root.style.setProperty('--bar-active', layoutState.primaryHex);
    root.style.setProperty('--sidebar-active-bg', layoutState.primaryHex);

    const isDefaultGreen = layoutState.primaryHex === '#10B981' || layoutState.primaryHex === '#729C51';
    root.style.setProperty('--bar-peak', isDefaultGreen ? '#EB5011' : complementaryPeak);
    root.style.setProperty('--color-alert', isDefaultGreen ? '#EB5011' : complementaryPeak);

    const aiTop = hslToHex(hsl.h, 95, 92);
    const aiMid = hslToHex(hsl.h, 92, 64);
    const aiBot = hslToHex(hsl.h, 85, 46);
    root.style.setProperty('--ai-btn-grad', `linear-gradient(180deg, ${aiTop} 0%, ${aiMid} 48%, ${aiBot} 100%)`);
    root.style.setProperty('--ai-btn-shadow', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.38)`);

    if (layoutState.theme === 'dark') {
      root.style.setProperty('--bg-mesh-1', hslToHex((hsl.h + 30) % 360, 45, 10));
      root.style.setProperty('--bg-mesh-2', hslToHex(hsl.h, 50, 12));
      root.style.setProperty('--bg-mesh-3', hslToHex(hsl.h, 35, 5));
      root.style.setProperty('--bg-mesh-4', hslToHex(hsl.h, 55, 14));
      root.style.setProperty('--drawer-bg', `linear-gradient(160deg, rgba(22, 34, 26, 0.62) 0%, rgba(14, 22, 17, 0.44) 60%, rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.18) 100%)`);
      root.style.setProperty('--glass-surface', 'rgba(16, 24, 18, 0.78)');
      root.style.setProperty('--glass-surface-strong', 'rgba(20, 30, 23, 0.95)');
      root.style.setProperty('--glass-pill', 'rgba(24, 36, 27, 0.88)');
      root.style.setProperty('--text-primary', fontPrimaryDark);
      root.style.setProperty('--text-secondary', fontSecondaryDark);
      root.style.setProperty('--text-muted', fontMutedDark);
      root.style.setProperty('--text-accent', lightShade);
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
      root.style.setProperty('--drawer-bg', `linear-gradient(160deg, rgba(255, 255, 255, 0.44) 0%, rgba(255, 255, 255, 0.24) 55%, rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.16) 100%)`);
      root.style.setProperty('--glass-surface', 'rgba(255, 255, 255, 0.74)');
      root.style.setProperty('--glass-surface-strong', 'rgba(255, 255, 255, 0.94)');
      root.style.setProperty('--glass-pill', 'rgba(255, 255, 255, 0.84)');
      root.style.setProperty('--text-primary', fontPrimaryLight);
      root.style.setProperty('--text-secondary', fontSecondaryLight);
      root.style.setProperty('--text-muted', fontMutedLight);
      root.style.setProperty('--text-accent', darkShade);
    }

    // Sync input pickers and dot indicators
    const desktopInput = document.getElementById('desktopCustomColorInput');
    const mobileInput = document.getElementById('mobileCustomColorInput');
    const dot = document.getElementById('activeColorDot');
    const readout = document.getElementById('activeHexReadout');

    if (desktopInput) desktopInput.value = layoutState.primaryHex;
    if (mobileInput) mobileInput.value = layoutState.primaryHex;
    if (dot) dot.style.backgroundColor = layoutState.primaryHex;
    if (readout) readout.textContent = `Active Palette: ${layoutState.primaryHex}`;

    // Highlight active preset button
    document.querySelectorAll('.color-Preset-btn, .color-preset-btn').forEach(btn => {
      const btnColor = btn.getAttribute('data-theme-color');
      if (btnColor && btnColor.toUpperCase() === layoutState.primaryHex) {
        btn.classList.add('ring-2', 'ring-offset-1', 'scale-110');
      } else {
        btn.classList.remove('ring-2', 'ring-offset-1', 'scale-110');
      }
    });

    window.dispatchEvent(new CustomEvent('portal:theme-color-changed', { detail: { hex: layoutState.primaryHex, rgb } }));
  }

  // --- 2. THEME SWITCHER (DARK / LIGHT) ---
  function applyTheme(mode) {
    layoutState.theme = mode === 'dark' ? 'dark' : 'light';
    localStorage.setItem('portal_theme', layoutState.theme);

    const isDark = layoutState.theme === 'dark';
    document.body.classList.toggle('theme-dark', isDark);
    document.documentElement.classList.toggle('dark', isDark);

    // Sync buttons classes (white active background on selected mode)
    const activeClass = 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm transition cursor-pointer';
    const inactiveClass = 'w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition cursor-pointer';

    const themeLightBtn = document.getElementById('themeLightBtn');
    const themeDarkBtn = document.getElementById('themeDarkBtn');
    if (themeLightBtn) themeLightBtn.className = isDark ? inactiveClass : activeClass;
    if (themeDarkBtn) themeDarkBtn.className = isDark ? activeClass : inactiveClass;

    const mobileThemeLightBtn = document.getElementById('mobileThemeLightBtn');
    const mobileThemeDarkBtn = document.getElementById('mobileThemeDarkBtn');
    if (mobileThemeLightBtn) mobileThemeLightBtn.className = isDark ? inactiveClass : activeClass;
    if (mobileThemeDarkBtn) mobileThemeDarkBtn.className = isDark ? activeClass : inactiveClass;

    // Reapply color palette to recalculate mesh for light or dark mode
    applyCustomColorPalette(layoutState.primaryHex);

    // Global theme change notification
    window.dispatchEvent(new CustomEvent('portal:theme-mode-changed', { detail: { theme: layoutState.theme, isDark } }));
  }

  // --- 3. SAMSUNG EDGE PULL HANDLE & MOBILE SIDEBAR DRAWER ---
  function updateHamburgerIcon(isOpen) {
    const line1 = document.getElementById('hamburgerLine1');
    const line2 = document.getElementById('hamburgerLine2');
    const line3 = document.getElementById('hamburgerLine3');
    const btn = document.getElementById('mobileMenuBtn');

    if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    if (line1 && line2 && line3) {
      if (isOpen) {
        line1.setAttribute('d', 'M6 18L18 6');
        line2.style.opacity = '0';
        line3.setAttribute('d', 'M6 6l12 12');
      } else {
        line1.setAttribute('d', 'M4 6.5h16');
        line2.style.opacity = '1';
        line3.setAttribute('d', 'M4 17.5h11');
      }
    }
  }

  function openMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const handle = document.getElementById('samsungEdgeHandle');

    if (sidebar) {
      sidebar.classList.remove('samsung-menu-closed');
      sidebar.classList.add('samsung-menu-open');
    }
    if (backdrop) {
      backdrop.classList.remove('hidden');
      requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
      });
    }
    if (handle) {
      handle.classList.add('opacity-0', 'pointer-events-none');
    }
    updateHamburgerIcon(true);
  }

  function closeMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const handle = document.getElementById('samsungEdgeHandle');

    document.body.classList.remove('mobile-sidebar-active');

    if (sidebar) {
      sidebar.classList.remove('samsung-menu-open');
      sidebar.classList.add('samsung-menu-closed');
    }
    if (backdrop) {
      backdrop.classList.remove('opacity-100');
      backdrop.classList.add('opacity-0');
      setTimeout(() => backdrop.classList.add('hidden'), 300);
    }
    if (handle) {
      handle.classList.remove('opacity-0', 'pointer-events-none');
    }
    updateHamburgerIcon(false);
  }

  window.openMobileSidebar = openMobileSidebar;
  window.closeMobileSidebar = closeMobileSidebar;

  function toggleMobileExpandedNames() {
    const sidebar = document.getElementById('mainSidebar');
    if (!sidebar) return;

    layoutState.mobileExpandedNames = !layoutState.mobileExpandedNames;
    localStorage.setItem('portal_sidebar_mobile_expanded', layoutState.mobileExpandedNames);

    const icon = document.getElementById('expandSidebarNamesIcon');
    if (layoutState.mobileExpandedNames) {
      sidebar.classList.remove('samsung-icon-only');
      sidebar.classList.add('samsung-expanded-names');
      if (icon) icon.classList.add('rotate-180');
    } else {
      sidebar.classList.remove('samsung-expanded-names');
      sidebar.classList.add('samsung-icon-only');
      if (icon) icon.classList.remove('rotate-180');
    }
  }

  // --- 4. DESKTOP 3-STATE SIDEBAR CONTROLS ---
  function syncDesktopSidebarMode() {
    const sidebar = document.getElementById('mainSidebar');
    const toggleIcon = document.getElementById('desktopSidebarToggleIcon');
    const toggleBtn = document.getElementById('desktopSidebarToggleBtn');
    const hideBtn = document.getElementById('desktopHideSidebarBtn');

    if (!sidebar) return;

    if (layoutState.desktopSidebarMode === 'expanded') {
      sidebar.classList.remove('lg:hidden');
      sidebar.classList.add('desktop-expanded');
      if (toggleIcon) toggleIcon.classList.add('rotate-180');
      if (toggleBtn) {
        toggleBtn.classList.add('bg-[var(--color-primary)]', 'text-white');
        toggleBtn.title = 'Click to Show Only Icons';
      }
      if (hideBtn) {
        hideBtn.classList.remove('bg-[var(--color-alert)]', 'text-white');
        hideBtn.title = 'Hide Side Menu Completely';
      }
    } else if (layoutState.desktopSidebarMode === 'hidden') {
      sidebar.classList.remove('desktop-expanded');
      sidebar.classList.add('lg:hidden');
      if (toggleIcon) toggleIcon.classList.remove('rotate-180');
      if (toggleBtn) {
        toggleBtn.classList.remove('bg-[var(--color-primary)]', 'text-white');
        toggleBtn.title = 'Open Side Menu with Text';
      }
      if (hideBtn) {
        hideBtn.classList.add('bg-[var(--color-alert)]', 'text-white');
        hideBtn.title = 'Restore Side Menu';
      }
    } else {
      // Default: 'compact' (Icons Only Dock)
      sidebar.classList.remove('lg:hidden', 'desktop-expanded');
      if (toggleIcon) toggleIcon.classList.remove('rotate-180');
      if (toggleBtn) {
        toggleBtn.classList.remove('bg-[var(--color-primary)]', 'text-white');
        toggleBtn.title = 'Open Side Menu with Text';
      }
      if (hideBtn) {
        hideBtn.classList.remove('bg-[var(--color-alert)]', 'text-white');
        hideBtn.title = 'Hide Side Menu Completely';
      }
    }

    localStorage.setItem('portal_sidebar_desktop_mode', layoutState.desktopSidebarMode);
  }

  // --- 5. UNIVERSAL TOAST NOTIFICATIONS ---
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

  // --- 6. INITIALIZATION OF LAYOUT ENGINE ---
  function initDashboardLayout() {
    // 1. Apply persisted theme & colors
    applyTheme(layoutState.theme);
    applyCustomColorPalette(layoutState.primaryHex);
    syncDesktopSidebarMode();

    // 2. Mobile Edge Handle & Backdrop Listeners
    const handle = document.getElementById('samsungEdgeHandle');
    if (handle) handle.addEventListener('click', openMobileSidebar);

    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) backdrop.addEventListener('click', closeMobileSidebar);

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', () => {
        const sidebar = document.getElementById('mainSidebar');
        if (sidebar && sidebar.classList.contains('samsung-menu-open')) {
          closeMobileSidebar();
        } else {
          openMobileSidebar();
        }
      });
    }

    const expandSidebarNamesBtn = document.getElementById('expandSidebarNamesBtn');
    if (expandSidebarNamesBtn) expandSidebarNamesBtn.addEventListener('click', toggleMobileExpandedNames);

    const closeMobileSidebarBtn = document.getElementById('closeMobileSidebarBtn');
    if (closeMobileSidebarBtn) closeMobileSidebarBtn.addEventListener('click', closeMobileSidebar);

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const sidebar = document.getElementById('mainSidebar');
        if (sidebar && sidebar.classList.contains('samsung-menu-open')) {
          closeMobileSidebar();
        }
      }
    });

    // Close mobile drawer when clicking a navigation link inside the sidebar
    const navLinks = document.querySelectorAll('#mainSidebar a');
    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
          closeMobileSidebar();
        }
      });
    });

    // 3. Desktop Sidebar Toggle Buttons
    const desktopToggleBtn = document.getElementById('desktopSidebarToggleBtn');
    if (desktopToggleBtn) {
      desktopToggleBtn.addEventListener('click', () => {
        layoutState.desktopSidebarMode = (layoutState.desktopSidebarMode === 'expanded') ? 'compact' : 'expanded';
        syncDesktopSidebarMode();
        showPortalToast(layoutState.desktopSidebarMode === 'expanded' ? 'Sidebar: Expanded' : 'Sidebar: Compact Icons');
      });
    }

    const desktopHideBtn = document.getElementById('desktopHideSidebarBtn');
    if (desktopHideBtn) {
      desktopHideBtn.addEventListener('click', () => {
        layoutState.desktopSidebarMode = (layoutState.desktopSidebarMode === 'hidden') ? 'compact' : 'hidden';
        syncDesktopSidebarMode();
        showPortalToast(layoutState.desktopSidebarMode === 'hidden' ? 'Sidebar: Hidden' : 'Sidebar: Restored');
      });
    }

    // 4. Color Studio Popover & Pickers
    const colorPickerToggleBtn = document.getElementById('colorPickerToggleBtn') || document.getElementById('openColorStudioBtn');
    const colorPickerPopover = document.getElementById('colorPickerPopover') || document.getElementById('colorStudioPopover');
    const notificationsBtn = document.getElementById('notificationsBtn');
    const mobileNotifBtn = document.getElementById('mobileNotifBtn');
    const notificationsPopover = document.getElementById('notificationsPopover');

    if (colorPickerToggleBtn && colorPickerPopover) {
      colorPickerToggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        colorPickerPopover.classList.toggle('hidden');
        if (notificationsPopover) notificationsPopover.classList.add('hidden');
      });
    }

    if (notificationsBtn && notificationsPopover) {
      notificationsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        notificationsPopover.classList.toggle('hidden');
        if (colorPickerPopover) colorPickerPopover.classList.add('hidden');
      });
    }

    if (mobileNotifBtn && notificationsPopover) {
      mobileNotifBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        notificationsPopover.classList.toggle('hidden');
        if (colorPickerPopover) colorPickerPopover.classList.add('hidden');
      });
    }

    document.addEventListener('click', (e) => {
      if (colorPickerPopover && !colorPickerPopover.contains(e.target) && e.target !== colorPickerToggleBtn && !colorPickerToggleBtn.contains(e.target)) {
        colorPickerPopover.classList.add('hidden');
      }
      if (notificationsPopover && !notificationsPopover.contains(e.target) && e.target !== notificationsBtn && !notificationsBtn.contains(e.target) && (!mobileNotifBtn || !mobileNotifBtn.contains(e.target))) {
        notificationsPopover.classList.add('hidden');
      }
    });

    // Color Pickers
    const desktopInput = document.getElementById('desktopCustomColorInput');
    if (desktopInput) {
      desktopInput.addEventListener('input', (e) => applyCustomColorPalette(e.target.value));
      desktopInput.addEventListener('change', (e) => applyCustomColorPalette(e.target.value));
    }

    const mobileInput = document.getElementById('mobileCustomColorInput');
    if (mobileInput) {
      mobileInput.addEventListener('input', (e) => applyCustomColorPalette(e.target.value));
      mobileInput.addEventListener('change', (e) => applyCustomColorPalette(e.target.value));
    }

    document.querySelectorAll('.color-Preset-btn, .color-preset-btn').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const hex = btn.getAttribute('data-theme-color');
        if (hex) {
          applyCustomColorPalette(hex);
          showPortalToast(`Applied ${hex} Theme Palette`);
        }
      });
    });

    // 5. Theme Switch Buttons
    const themeLightBtn = document.getElementById('themeLightBtn');
    const themeDarkBtn = document.getElementById('themeDarkBtn');
    if (themeLightBtn) {
      themeLightBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        applyTheme('light');
        showPortalToast('Switched to Glass Light Mode');
      });
    }
    if (themeDarkBtn) {
      themeDarkBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        applyTheme('dark');
        showPortalToast('Switched to Dark Glass Mode');
      });
    }

    const mobileThemeLightBtn = document.getElementById('mobileThemeLightBtn');
    const mobileThemeDarkBtn = document.getElementById('mobileThemeDarkBtn');
    if (mobileThemeLightBtn) {
      mobileThemeLightBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        applyTheme('light');
        showPortalToast('Switched to Glass Light Mode');
      });
    }
    if (mobileThemeDarkBtn) {
      mobileThemeDarkBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        applyTheme('dark');
        showPortalToast('Switched to Dark Glass Mode');
      });
    }
  }

  // Auto-init on DOMContentLoaded or immediate if already loaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardLayout);
  } else {
    initDashboardLayout();
  }

  // Export public API
  window.applyCustomColorPalette = applyCustomColorPalette;
  window.applyTheme = applyTheme;
  window.showPortalToast = showPortalToast;
  window.showToast = showPortalToast; // Compatibility alias
  window.openMobileSidebar = openMobileSidebar;
  window.closeMobileSidebar = closeMobileSidebar;
})(window);
