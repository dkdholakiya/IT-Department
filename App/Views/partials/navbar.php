<?php
/**
 * Shared Top Navigation Bar Partial
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Supported Variables:
 * @var string|null $nav_title              Brand / Header Title (defaults to 'Engineering Dept CE & IT')
 * @var string|null $nav_subtitle           Sub-header readout (defaults to 'CE & IT Academic Portal')
 * @var string|null $nav_back_url           URL for Portal Back button (defaults to base_url('/'))
 * @var string|null $nav_search_placeholder Search input placeholder text
 */

$baseUrl = function_exists('base_url') ? base_url('/') : './';
$defaultNavTitle = function_exists('get_portal_brand_name') ? get_portal_brand_name() : 'Engineering Dept CE & IT';
$navTitle = $nav_title ?? $defaultNavTitle;
$navSubtitle = $nav_subtitle ?? 'CE & IT Academic Portal';
$navBackUrl = $nav_back_url ?? $baseUrl;
$navSearchPlaceholder = $nav_search_placeholder ?? 'Search metrics, events, modules...';

$authUser = class_exists('Core\Auth') ? \Core\Auth::user() : null;
$authName = $authUser['name'] ?? 'Faculty Member';
$authRole = ($authUser['role'] ?? '') === 'super_admin' ? 'Super Admin' : ($authUser['designation'] ?? 'Staff');
if (!empty($authUser['initials'])) {
    $authInitials = strtoupper($authUser['initials']);
} else {
    $words = preg_split('/\s+/', trim($authName));
    $filtered = array_filter($words, function($w) {
        $clean = strtolower(rtrim($w, '.'));
        return !in_array($clean, ['prof', 'dr', 'mr', 'mrs', 'ms', 'er']);
    });
    if (empty($filtered)) $filtered = $words;
    $authInitials = '';
    foreach ($filtered as $w) {
        $c = preg_replace('/[^A-Za-z]/', '', $w);
        if (!empty($c)) {
            $authInitials .= strtoupper($c[0]);
        }
    }
    $authInitials = substr($authInitials, 0, 3);
    if (empty($authInitials)) $authInitials = 'FM';
}
$isLoggedIn = class_exists('Core\Auth') && \Core\Auth::isAuthenticated();
$sessionExpiresAt = $isLoggedIn && method_exists('Core\Auth', 'sessionExpiresAt') ? \Core\Auth::sessionExpiresAt() : null;
?>
<script>
  window.PORTAL_USER = <?php echo json_encode($authUser ?? null, JSON_UNESCAPED_UNICODE); ?>;
  window.PORTAL_SESSION_EXPIRES_AT = <?php echo json_encode($sessionExpiresAt); ?>;
  window.PORTAL_PERMISSIONS = <?php echo json_encode(class_exists('Core\Auth') ? \Core\Auth::userPermissions() : ['*'], JSON_UNESCAPED_UNICODE); ?>;
  window.canPermission = function(perm) {
    if (!window.PORTAL_PERMISSIONS) return true;
    if (window.PORTAL_PERMISSIONS.indexOf('*') !== -1) return true;
    return window.PORTAL_PERMISSIONS.indexOf(perm) !== -1;
  };
</script>

<!-- ================= FIXED TOP NAVIGATION BAR (Pinned at z-30 on Mobile; Relative in Flow on Desktop) ================= -->
<header id="topNavbar" class="fixed lg:relative top-0 left-0 right-0 z-30 lg:z-auto px-3 sm:px-5 lg:px-0 py-2.5 lg:py-0 bg-[var(--bg-mesh-3)]/85 lg:bg-transparent backdrop-blur-xl lg:backdrop-blur-none border-b border-white/50 lg:border-none shrink-0">
  <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-2 sm:gap-3 w-full relative">

    <!-- Left: Hamburger Menu (Mobile/Tablet) + Animated Brand Logo + Card 1: Separate Title Glass Card -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
      <!-- Mobile / Tablet Left Samsung Shortcut Menu Toggle -->
      <button id="mobileMenuBtn" type="button" class="lg:hidden w-10 h-10 rounded-full side-menu-glass flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition-all duration-300 shrink-0" aria-label="Open Navigation Sidebar" title="Open Samsung Shortcut Side Menu">
        <svg id="mobileMenuHamburgerIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
          <path id="hamburgerLine1" stroke-linecap="round" stroke-linejoin="round" d="M4 6.5h16" />
          <path id="hamburgerLine2" stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
          <path id="hamburgerLine3" stroke-linecap="round" stroke-linejoin="round" d="M4 17.5h11" />
        </svg>
      </button>

      <!-- Brand Logo Link (Outside on Left - Responsive Only with Identical Desktop Floating & Glowing AI Robot Animation) -->
      <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" id="mobileNavBrandLogo" class="md:hidden w-10 h-10 sm:w-11 sm:h-11 rounded-full side-menu-glass flex items-center justify-center shrink-0 shadow-sm transition-all duration-300 overflow-hidden cursor-pointer group" title="<?php echo htmlspecialchars($navTitle); ?>">
        <div class="sidebar-ai-float relative w-8 h-6 flex items-center justify-center shrink-0 pointer-events-none">
          <svg viewBox="0 0 46 32" class="sidebar-ai-robot w-7 h-5 overflow-visible" fill="none">
            <defs>
              <linearGradient id="navbarAiLogoRimGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="var(--color-primary-light, #9b3dff)" />
                <stop offset="100%" stop-color="var(--color-primary, #2ca9ff)" />
              </linearGradient>
            </defs>
            <!-- Outer white/pearl capsule shell -->
            <rect x="1" y="1" width="44" height="30" rx="15" fill="#ffffff" stroke="var(--glass-border-strong, rgba(124, 58, 237, 0.45))" stroke-width="1.2" />
            <!-- Dynamic colored rim matching theme primary -->
            <rect x="3.8" y="3.8" width="38.4" height="24.4" rx="12.2" fill="url(#navbarAiLogoRimGrad)" />
            <!-- Deep black glossy visor -->
            <rect x="6.6" y="6.6" width="32.8" height="18.8" rx="9.4" fill="#08080d" />
            <!-- Two white capsule eyes with lifelike blink -->
            <rect class="sidebar-ai-eye" x="14.2" y="10.8" width="5.0" height="10.4" rx="2.5" fill="#ffffff" />
            <rect class="sidebar-ai-eye" x="26.8" y="10.8" width="5.0" height="10.4" rx="2.5" fill="#ffffff" />
          </svg>
        </div>
      </a>

      <!-- Card 1: Navigation Title Glass Card (Separate Card with Glass Effect in Responsive!) -->
      <div class="side-menu-glass rounded-full h-11 sm:h-12 px-3.5 sm:px-4 flex items-center gap-2.5 sm:gap-3 min-w-0 shrink-0">
        <!-- Title & Subtitle -->
        <div class="min-w-0 max-w-[125px] xs:max-w-[170px] sm:max-w-none text-left">
          <span class="text-[13px] sm:text-[15.5px] lg:text-[17px] font-bold tracking-tight text-[var(--text-primary)] block leading-tight truncate"><?php echo htmlspecialchars($navTitle); ?></span>
          <span class="text-[9px] sm:text-[10.5px] font-medium text-[var(--text-muted)] flex items-center gap-1.5 truncate">
            <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-[var(--color-primary)] live-dot shrink-0"></span>
            <span id="activeZoneHeaderLabel" class="truncate"><?php echo htmlspecialchars($navSubtitle); ?></span>
          </span>
        </div>

        <!-- Desktop Two-Icon Sidebar Control Group -->
        <div id="desktopSidebarControlGroup" class="hidden lg:flex items-center gap-1 pl-2 sm:pl-2.5 border-l border-black/10 dark:border-white/15 shrink-0">
          <!-- Icon 1: Menu Open with Text (1st click = Show Icons + Text; 2nd click = Icons Only) -->
          <button
            id="desktopSidebarToggleBtn"
            type="button"
            class="w-7 h-7 rounded-full side-menu-icon-glass flex items-center justify-center text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition shrink-0"
            title="Open Menu with Text / Collapse to Icons Only"
            aria-label="Toggle Menu Text Labels"
          >
            <svg id="desktopSidebarToggleIcon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
            <span id="desktopSidebarToggleText" class="sr-only">Toggle Sidebar</span>
          </button>

          <!-- Icon 2: Menu Close / Hide Completely (Click = Fully Hide Side Menu) -->
          <button
            id="desktopHideSidebarBtn"
            type="button"
            class="w-7 h-7 rounded-full side-menu-icon-glass flex items-center justify-center text-[var(--text-primary)] hover:bg-[var(--color-alert)] hover:text-white transition shrink-0"
            title="Hide Side Menu Completely"
            aria-label="Hide Side Menu Completely"
          >
            <svg id="desktopHideSidebarIcon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>



    <!-- Right: Desktop Controls (md+) + Card 2: Separate Mobile Right-Side User Menu Glass Card (< md) -->
    <div class="flex items-center gap-2 shrink-0">

      <!-- DESKTOP / TABLET RIGHT BAR -->
      <div class="hidden md:flex items-center gap-2">
        <!-- Portal Link Button -->
        <a href="<?php echo htmlspecialchars($navBackUrl); ?>" id="portalBackLink" class="h-10 px-3.5 rounded-full side-menu-glass flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)] hover:scale-105 transition shrink-0" title="Return to Main Portal">
          <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          <span>Portal</span>
        </a>

        <!-- AI Assistant Button -->
        <button id="aiAssistantBtn" type="button" class="h-10 px-4 rounded-full ai-assistant-btn flex items-center gap-2 text-[13px] font-bold tracking-tight shrink-0">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 3v4M22 5h-4"/>
          </svg>
          <span>AI Assistant</span>
        </button>

        <!-- Card 1: Separate Notification Bell Card (In its own glass pill) -->
        <button
          id="notificationsBtn"
          type="button"
          class="w-10 h-10 rounded-full side-menu-glass flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition relative cursor-pointer shrink-0"
          title="System Alerts & Telemetry Notifications"
          aria-label="Notifications"
        >
          <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
          </svg>
          <span id="notifBadge" class="w-2 h-2 rounded-full bg-[var(--color-alert)] absolute top-2 right-2 ring-2 ring-white dark:ring-slate-900 pointer-events-none"></span>
        </button>

        <!-- Winter Snowfall Toggle Button (In its own glass pill next to notifications) -->
        <button
          id="snowfallToggleBtn"
          type="button"
          class="w-10 h-10 rounded-full side-menu-glass flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition relative cursor-pointer shrink-0"
          title="Toggle Winter Snowfall (Default: ON)"
          aria-label="Toggle Winter Snowfall"
        >
          <svg id="snowfallIcon" class="w-4 h-4 pointer-events-none transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M2 12h20M20 16l-4-4 4-4M4 8l4 4-4 4M16 4l-4 4-4-4M8 20l4-4 4 4"/>
          </svg>
          <span id="snowfallActiveDot" class="w-2 h-2 rounded-full bg-sky-400 absolute top-2 right-2 ring-2 ring-white dark:ring-slate-900 pointer-events-none transition-all duration-200 shadow-sm shadow-sky-400/50"></span>
        </button>

        <!-- Card 2: Combined Appearance Card (Color Studio + Theme Switcher in One Glass Pill) -->
        <div class="h-10 px-1.5 rounded-full side-menu-glass flex items-center gap-1 shrink-0">
          <!-- 1. Color Picker with Elegant Artist Palette Icon & Active Color Indicator -->
          <button
            id="colorPickerToggleBtn"
            type="button"
            class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition relative cursor-pointer shrink-0"
            title="Choose Theme & Font Color Combination"
            aria-label="Choose Theme Color"
          >
            <svg class="w-4 h-4 pointer-events-none text-[var(--text-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/>
              <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/>
              <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>
              <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/>
              <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>
            </svg>
            <span id="activeColorDot" class="w-2 h-2 rounded-full border border-white dark:border-slate-800 shadow-sm absolute bottom-1 right-1 pointer-events-none" style="background-color: var(--color-primary);"></span>
          </button>

          <!-- 2. Single Theme Toggle (Sleek Professional Thin-Line Moon / Sun Icon) -->
          <button
            id="themeToggleBtn"
            type="button"
            class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition cursor-pointer shrink-0"
            title="Switch to Dark Mode"
            aria-label="Toggle Light / Dark Theme"
          >
            <!-- Moon Icon: Shown when Light Theme is active (Delicate Professional 16px Lucide/Google SVG) -->
            <svg id="themeMoonIcon" class="w-4 h-4 pointer-events-none transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
            <!-- Sun Icon: Shown when Dark Theme is active (Delicate Professional 16px Lucide/Google SVG) -->
            <svg id="themeSunIcon" class="w-4 h-4 hidden pointer-events-none text-amber-400 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="4"/>
              <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
            </svg>
          </button>
        </div>

        <!-- Card 3: Combined User Profile & Quick Logout Pill (Desktop / Tablet) -->
        <?php if ($isLoggedIn): ?>
          <div
            id="navbarUserPill"
            class="h-10 pl-2 pr-1.5 rounded-full side-menu-glass flex items-center gap-2.5 shrink-0 select-none shadow-xs group"
            title="Signed in as <?php echo htmlspecialchars($authName . ' (' . $authRole . ')'); ?>"
          >
            <!-- Left Side: User Avatar, Name & Role Info -->
            <div class="flex items-center gap-2 min-w-0">
              <span class="w-7 h-7 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-[10.5px] shadow-xs shrink-0 ring-1 ring-white/30 dark:ring-white/10">
                <?php echo htmlspecialchars($authInitials); ?>
              </span>
              <div class="flex flex-col text-left leading-none pr-0.5 min-w-0">
                <span class="text-xs sm:text-[12.5px] font-bold text-[var(--text-primary)] max-w-[125px] md:max-w-[155px] lg:max-w-[195px] xl:max-w-[240px] truncate leading-tight"><?php echo htmlspecialchars($authName); ?></span>
                <span class="text-[9.5px] sm:text-[10px] font-bold text-[var(--color-primary)] mt-0.5 truncate leading-tight"><?php echo htmlspecialchars($authRole); ?></span>
              </div>
            </div>

            <!-- Subtle Vertical Divider -->
            <div class="w-[1px] h-4.5 bg-black/10 dark:bg-white/15 shrink-0"></div>

            <!-- Right Side: Logout Action Button (Icon Only) -->
            <a
              id="navbarLogoutBtn"
              href="<?php echo htmlspecialchars($baseUrl); ?>logout"
              class="w-7 h-7 rounded-full side-menu-icon-glass text-rose-500 hover:bg-rose-500 hover:text-white dark:hover:bg-rose-600 transition-all duration-200 flex items-center justify-center hover:scale-110 active:scale-95 shrink-0 shadow-xs cursor-pointer group/logout"
              title="Sign Out (<?php echo htmlspecialchars($authName); ?>)"
              aria-label="Logout"
            >
              <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/logout:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
            </a>
          </div>
        <?php else: ?>
          <a
            href="<?php echo htmlspecialchars($baseUrl); ?>login"
            class="h-10 px-3.5 rounded-full side-menu-glass flex items-center gap-1.5 text-xs font-bold text-[var(--color-primary)] hover:scale-105 transition shrink-0 shadow-xs"
            title="Sign In to Faculty Portal"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H3" />
            </svg>
            <span>Sign In</span>
          </a>
        <?php endif; ?>
      </div>

      <!-- Card 2: Separate Mobile Right-Side User Menu Glass Pill (< md) - Glass Effect as it is! -->
      <button
        id="mobileUserMenuBtn"
        type="button"
        class="md:hidden h-10 pl-1.5 pr-2.5 rounded-full side-menu-glass flex items-center gap-1.5 text-[var(--text-primary)] hover:scale-105 active:scale-95 transition relative cursor-pointer shrink-0"
        aria-label="Open User & Quick Actions Menu"
        title="Open Quick Actions & User Menu"
      >
        <img
          src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA=="
          alt="User Menu"
          class="w-7 h-7 rounded-full object-cover ring-1.5 ring-[var(--color-primary)] pointer-events-none"
        />
        <svg id="mobileUserMenuChevron" class="w-3.5 h-3.5 text-[var(--text-secondary)] transition-transform duration-200 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
        </svg>
        <span class="w-2.5 h-2.5 rounded-full bg-[var(--color-alert)] absolute top-1 right-1 ring-1 ring-white dark:ring-slate-900 pointer-events-none"></span>
      </button>
    </div>

    <!-- DYNAMIC COLOR PICKER POPOVER (Positioned cleanly under the Color Studio Button) -->
    <div id="colorPickerPopover" class="hidden fixed right-3 sm:right-6 md:right-4 lg:right-6 top-16 md:top-14 w-[calc(100vw-24px)] sm:w-72 max-w-sm glass-card p-4 z-50 shadow-2xl border border-white/80 dark:border-white/15">
      <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-black/10">
        <div>
          <span class="font-bold text-xs text-[var(--text-primary)] block">Theme & Font Color Studio</span>
          <span class="text-[10px] text-[var(--text-muted)]">Transforms UI, charts & typography</span>
        </div>
        <input id="desktopCustomColorInput" type="color" value="#7C3AED" class="w-8 h-8 rounded-lg cursor-pointer border border-black/10 bg-transparent p-0.5" title="Pick Custom Color" />
      </div>
      <div class="grid grid-cols-6 gap-2 mb-2">
        <button type="button" data-theme-color="#7C3AED" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#7C3AED] ring-2 ring-offset-1 ring-[#7C3AED] cursor-pointer" title="Royal Violet (#7C3AED)" aria-label="Royal Violet"></button>
        <button type="button" data-theme-color="#10B981" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#10B981] cursor-pointer" title="Emerald (#10B981)" aria-label="Emerald"></button>
        <button type="button" data-theme-color="#729C51" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#729C51] cursor-pointer" title="Leaf Green (#729C51)" aria-label="Leaf Green"></button>
        <button type="button" data-theme-color="#0284C7" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#0284C7] cursor-pointer" title="Ocean Blue (#0284C7)" aria-label="Ocean Blue"></button>
        <button type="button" data-theme-color="#E11D48" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#E11D48] cursor-pointer" title="Sunset Rose (#E11D48)" aria-label="Sunset Rose"></button>
        <button type="button" data-theme-color="#D97706" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#D97706] cursor-pointer" title="Amber Gold (#D97706)" aria-label="Amber Gold"></button>
      </div>
      <p id="activeHexReadout" class="text-[11px] font-semibold text-[var(--text-secondary)] text-center mt-2">Active Palette: #7C3AED</p>
    </div>

    <!-- Notifications Dropdown Popover -->
    <div id="notificationsPopover" class="hidden fixed md:absolute right-3 sm:right-6 md:right-16 lg:right-20 top-16 md:top-14 w-[calc(100vw-24px)] max-w-sm glass-card p-4 z-50 shadow-2xl border border-white/80 dark:border-white/15">
      <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-black/10">
        <div class="flex items-center gap-2">
          <span class="font-bold text-sm text-[var(--text-primary)]">System Alerts & Schedule Updates</span>
          <span id="notifCountPill" class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-[var(--color-alert)]/15 text-[var(--color-alert)]">3 New</span>
        </div>
        <button id="markAllReadBtn" type="button" class="text-xs font-semibold text-[var(--color-primary)] hover:underline cursor-pointer">Mark read</button>
      </div>
      <div id="notifList" class="space-y-2.5 max-h-64 overflow-y-auto text-xs">
        <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
          <span class="w-2 h-2 rounded-full bg-[var(--color-alert)] mt-1 shrink-0"></span>
          <div>
            <p class="font-semibold text-[var(--text-primary)]">Makar Sankranti Holiday Approaching</p>
            <p class="text-[var(--text-muted)] mt-0.5">Public Holiday on January 14, 2026. Department academic schedule paused.</p>
          </div>
        </div>
        <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
          <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] mt-1 shrink-0"></span>
          <div>
            <p class="font-semibold text-[var(--text-primary)]">Mid-Semester Exam Dates Announced</p>
            <p class="text-[var(--text-muted)] mt-0.5">Academic schedule synced with Google Sheets CTL repository.</p>
          </div>
        </div>
        <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
          <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] mt-1 shrink-0"></span>
          <div>
            <p class="font-semibold text-[var(--text-primary)]">Automated Backup Complete</p>
            <p class="text-[var(--text-muted)] mt-0.5">Faculty schedules & student timetables compiled successfully.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MOBILE DOWNSIDE COLLAPSIBLE MENU PANEL -->
  <div id="mobileSearchContainer" class="hidden md:hidden mt-2.5 pt-2.5 border-t border-black/10 space-y-2.5">
    <!-- Row B: AI Assistant + Alerts + Dark/Light Mode -->
    <div class="flex items-center justify-between gap-2">
      <button id="mobileAiAssistantBtn" type="button" class="flex-1 h-10 px-3.5 rounded-full ai-assistant-btn flex items-center justify-center gap-2 text-xs font-bold">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
        </svg>
        <span>AI Assistant</span>
      </button>

      <button id="mobileNotifBtn" type="button" class="h-10 px-3 rounded-full side-menu-glass flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)] relative" aria-label="Mobile Notifications">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
        </svg>
        <span>Alerts</span>
        <span class="w-2 h-2 rounded-full bg-[var(--color-alert)]"></span>
      </button>

      <!-- Mobile Snowfall Toggle Button -->
      <button
        id="mobileSnowfallToggleBtn"
        type="button"
        class="h-10 w-10 rounded-full side-menu-glass flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition cursor-pointer shrink-0 relative"
        aria-label="Toggle Winter Snowfall"
        title="Toggle Winter Snowfall"
      >
        <svg class="w-4 h-4 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2v20M2 12h20M20 16l-4-4 4-4M4 8l4 4-4 4M16 4l-4 4-4-4M8 20l4-4 4 4"/>
        </svg>
        <span id="mobileSnowfallActiveDot" class="w-2 h-2 rounded-full bg-sky-400 absolute top-2 right-2 ring-2 ring-white dark:ring-slate-900 pointer-events-none transition-all duration-200 shadow-sm shadow-sky-400/50"></span>
      </button>

      <!-- Mobile Single Theme Switcher Button -->
      <button
        id="mobileThemeToggleBtn"
        type="button"
        class="h-10 w-10 rounded-full side-menu-glass flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition cursor-pointer shrink-0"
        aria-label="Toggle Light / Dark Theme"
        title="Toggle Theme"
      >
        <svg id="mobileThemeMoonIcon" class="w-4 h-4 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <svg id="mobileThemeSunIcon" class="w-4 h-4 hidden pointer-events-none text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="4"/>
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
        </svg>
      </button>
    </div>

    <!-- Row C: Mobile Theme Color Chooser Bar -->
    <div class="p-2.5 rounded-2xl side-menu-glass flex items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <label for="mobileCustomColorInput" class="text-xs font-bold text-[var(--text-primary)] cursor-pointer flex items-center gap-1.5">
          <span>🎨 Theme Color:</span>
        </label>
        <input id="mobileCustomColorInput" type="color" value="#7C3AED" class="w-7 h-7 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Pick any custom theme & font color" />
      </div>
      <div class="flex items-center gap-1.5">
        <button type="button" data-theme-color="#7C3AED" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#7C3AED] ring-2 ring-offset-1 ring-[#7C3AED] cursor-pointer" title="Royal Violet Default (#7C3AED)" aria-label="Royal Violet Default Theme"></button>
        <button type="button" data-theme-color="#10B981" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#10B981] cursor-pointer" title="Emerald (#10B981)" aria-label="Emerald Theme"></button>
        <button type="button" data-theme-color="#729C51" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#729C51] cursor-pointer" title="Nature Green (#729C51)" aria-label="Nature Green Theme"></button>
        <button type="button" data-theme-color="#0284C7" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#0284C7] cursor-pointer" title="Ocean Blue (#0284C7)" aria-label="Ocean Blue Theme"></button>
        <button type="button" data-theme-color="#E11D48" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#E11D48] cursor-pointer" title="Crimson Rose (#E11D48)" aria-label="Crimson Rose Theme"></button>
        <button type="button" data-theme-color="#0D9488" class="color-preset-btn color-Preset-btn w-6 h-6 rounded-full bg-[#0D9488] cursor-pointer" title="Teal Lagoon (#0D9488)" aria-label="Teal Lagoon Theme"></button>
      </div>
    </div>

    <!-- Row D: Mobile User Session & Logout Action Bar -->
    <?php if ($isLoggedIn): ?>
      <div class="p-2.5 rounded-2xl side-menu-glass flex items-center justify-between gap-2.5 border border-black/5 dark:border-white/10 shadow-sm">
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
          <div class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs ring-1 ring-white/30 dark:ring-white/10">
            <?php echo htmlspecialchars($authInitials); ?>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-bold text-[var(--text-primary)] truncate leading-tight"><?php echo htmlspecialchars($authName); ?></p>
            <p class="text-[10.5px] font-semibold text-[var(--color-primary)] truncate mt-0.5"><?php echo htmlspecialchars($authRole); ?></p>
          </div>
        </div>
        <a
          id="mobileNavbarLogoutBtn"
          href="<?php echo htmlspecialchars($baseUrl); ?>logout"
          class="h-9 px-3.5 rounded-full side-menu-glass border border-rose-500/30 text-rose-500 hover:bg-rose-500 hover:text-white text-xs font-bold flex items-center gap-2 transition-all duration-200 active:scale-95 shrink-0 shadow-xs group"
          title="Sign out of session"
        >
          <span class="w-5 h-5 rounded-full bg-rose-500/10 group-hover:bg-transparent flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
          </span>
          <span>Logout</span>
        </a>
      </div>
    <?php endif; ?>
  </div>
</header>

<!-- Shared Top-Right Bootstrap 5 Toast Container -->
<?php if (file_exists(__DIR__ . '/toast.php')) include __DIR__ . '/toast.php'; ?>

<!-- Responsive Left-Bottom Hard Refresh & Cache Bust Button -->
<?php if (file_exists(__DIR__ . '/refresh-button.php')) include __DIR__ . '/refresh-button.php'; ?>

