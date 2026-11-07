/**
 * Shared Samsung Edge Shortcut Sidebar Controller
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Controls:
 * - Mobile pull handle & hamburger button drawer toggle
 * - Smooth backdrop dismiss & Escape key dismissal
 * - Mobile capsule expand/collapse full menu names
 * - Desktop 3-mode sidebar transitions (compact dock, expanded text, hidden)
 */

(function () {
  'use strict';

  // --- STATE ---
  const sidebarState = {
    isOpenMobile: false,
    mobileExpandedNames: localStorage.getItem('portal_sidebar_mobile_expanded') === 'true',
    desktopSidebarMode: localStorage.getItem('portal_sidebar_desktop_mode') || 'compact'
  };

  // --- HAMBURGER ICON ANIMATION (Hamburger <-> Cross) ---
  function updateHamburgerIcon(isOpen) {
    const btn = document.getElementById('mobileMenuBtn');
    const line1 = document.getElementById('hamburgerLine1');
    const line2 = document.getElementById('hamburgerLine2');
    const line3 = document.getElementById('hamburgerLine3');

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

  // --- MOBILE SIDEBAR DRAWER CONTROLLER ---
  function openMobileSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const handle = document.getElementById('samsungEdgeHandle');

    sidebarState.isOpenMobile = true;
    document.body.classList.add('mobile-sidebar-active');

    // Close any open popovers or trays in navbar
    const mobileSearchContainer = document.getElementById('mobileSearchContainer');
    if (mobileSearchContainer) mobileSearchContainer.classList.add('hidden');
    const mobileUserMenuChevron = document.getElementById('mobileUserMenuChevron');
    if (mobileUserMenuChevron) mobileUserMenuChevron.classList.remove('rotate-180');
    document.getElementById('colorPickerPopover')?.classList.add('hidden');
    document.getElementById('notificationsPopover')?.classList.add('hidden');

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

    sidebarState.isOpenMobile = false;
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

  // Expose globally so other modules can trigger drawer actions
  window.openMobileSidebar = openMobileSidebar;
  window.closeMobileSidebar = closeMobileSidebar;

  // Toggle between mobile icon-only capsule and full readable sidebar
  function toggleMobileExpandedNames() {
    const sidebar = document.getElementById('mainSidebar');
    if (!sidebar) return;

    sidebarState.mobileExpandedNames = !sidebarState.mobileExpandedNames;
    localStorage.setItem('portal_sidebar_mobile_expanded', sidebarState.mobileExpandedNames);

    const icon = document.getElementById('expandSidebarNamesIcon');
    if (sidebarState.mobileExpandedNames) {
      sidebar.classList.remove('samsung-icon-only');
      sidebar.classList.add('samsung-expanded-names');
      if (icon) icon.classList.add('rotate-180');
      if (window.showPortalToast) window.showPortalToast('Full Sidebar with Menu Names');
    } else {
      sidebar.classList.remove('samsung-expanded-names');
      sidebar.classList.add('samsung-icon-only');
      if (icon) icon.classList.remove('rotate-180');
      if (window.showPortalToast) window.showPortalToast('Icon-Only Shortcut Capsule');
    }
  }

  // --- DESKTOP 3-MODE CONTROLLER ---
  function syncDesktopSidebarMode() {
    const sidebar = document.getElementById('mainSidebar');
    const toggleIcon = document.getElementById('desktopSidebarToggleIcon');
    const toggleBtn = document.getElementById('desktopSidebarToggleBtn');
    const hideBtn = document.getElementById('desktopHideSidebarBtn');

    if (!sidebar) return;

    if (sidebarState.desktopSidebarMode === 'expanded') {
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
    } else if (sidebarState.desktopSidebarMode === 'hidden') {
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

    localStorage.setItem('portal_sidebar_desktop_mode', sidebarState.desktopSidebarMode);
  }

  // --- INITIALIZATION ---
  function initSidebar() {
    const sidebar = document.getElementById('mainSidebar');
    if (!sidebar) return;

    // Apply persisted mobile capsule state
    if (sidebarState.mobileExpandedNames) {
      sidebar.classList.remove('samsung-icon-only');
      sidebar.classList.add('samsung-expanded-names');
      const icon = document.getElementById('expandSidebarNamesIcon');
      if (icon) icon.classList.add('rotate-180');
    }

    // Apply persisted desktop mode
    syncDesktopSidebarMode();

    // Mobile pull handle click
    const handle = document.getElementById('samsungEdgeHandle');
    if (handle) {
      handle.addEventListener('click', openMobileSidebar);
    }

    // Mobile backdrop click
    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) {
      backdrop.addEventListener('click', closeMobileSidebar);
    }

    // Mobile hamburger menu button toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', () => {
        if (sidebarState.isOpenMobile || sidebar.classList.contains('samsung-menu-open')) {
          closeMobileSidebar();
        } else {
          openMobileSidebar();
        }
      });
    }

    // Mobile expand full names button inside capsule
    const expandSidebarNamesBtn = document.getElementById('expandSidebarNamesBtn');
    if (expandSidebarNamesBtn) {
      expandSidebarNamesBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMobileExpandedNames();
      });
    }

    // Desktop mode toggle button
    const desktopToggleBtn = document.getElementById('desktopSidebarToggleBtn');
    if (desktopToggleBtn) {
      desktopToggleBtn.addEventListener('click', () => {
        sidebarState.desktopSidebarMode = (sidebarState.desktopSidebarMode === 'expanded') ? 'compact' : 'expanded';
        syncDesktopSidebarMode();
        if (window.showPortalToast) {
          window.showPortalToast(sidebarState.desktopSidebarMode === 'expanded' ? 'Sidebar: Expanded' : 'Sidebar: Compact Icons');
        }
      });
    }

    // Desktop hide button
    const desktopHideBtn = document.getElementById('desktopHideSidebarBtn');
    if (desktopHideBtn) {
      desktopHideBtn.addEventListener('click', () => {
        sidebarState.desktopSidebarMode = (sidebarState.desktopSidebarMode === 'hidden') ? 'compact' : 'hidden';
        syncDesktopSidebarMode();
        if (window.showPortalToast) {
          window.showPortalToast(sidebarState.desktopSidebarMode === 'hidden' ? 'Sidebar: Hidden' : 'Sidebar: Restored');
        }
      });
    }

    // Auto-close mobile drawer when tapping a navigation link inside sidebar
    const navLinks = sidebar.querySelectorAll('a');
    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
          closeMobileSidebar();
        }
      });
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && sidebar.classList.contains('samsung-menu-open')) {
        closeMobileSidebar();
      }
    });

    // Close mobile sidebar if screen is resized to desktop viewport
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024 && (sidebarState.isOpenMobile || document.body.classList.contains('mobile-sidebar-active'))) {
        closeMobileSidebar();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
  } else {
    initSidebar();
  }
})();

