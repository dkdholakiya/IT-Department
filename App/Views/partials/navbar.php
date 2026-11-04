<?php
/**
 * Shared Top Navigation Bar Partial
 * Department of CE & IT — Smart Management Dashboard & Academic Portal
 *
 * Supported Variables:
 * @var string|null $nav_title              Brand / Header Title (defaults to 'AI Greenhouse')
 * @var string|null $nav_subtitle           Sub-header readout (defaults to 'Zone A • Hydroponic Bay')
 * @var string|null $nav_back_url           URL for Portal Back button (defaults to base_url('/'))
 * @var string|null $nav_search_placeholder Search input placeholder text
 */

$baseUrl = function_exists('base_url') ? base_url('/') : './';
$navTitle = $nav_title ?? 'AI Greenhouse';
$navSubtitle = $nav_subtitle ?? 'Zone A • Hydroponic Bay';
$navBackUrl = $nav_back_url ?? $baseUrl;
$navSearchPlaceholder = $nav_search_placeholder ?? 'Search metrics, events, modules...';
?>

<!-- ================= FIXED TOP NAVIGATION BAR (Pinned at z-40 on Mobile; Relative in Flow on Desktop) ================= -->
<header id="topNavbar" class="fixed lg:relative top-0 left-0 right-0 z-40 px-3 sm:px-5 lg:px-0 py-2.5 lg:py-0 bg-[var(--bg-mesh-3)]/85 lg:bg-transparent backdrop-blur-xl lg:backdrop-blur-none border-b border-white/50 lg:border-none shrink-0">
  <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-2 sm:gap-3 w-full relative">

    <!-- Left: Hamburger Menu (Mobile/Tablet) + Brand Logo + Desktop Sidebar Toggle Button -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
      <!-- Mobile / Tablet Left Samsung Shortcut Menu Toggle -->
      <button id="mobileMenuBtn" type="button" class="lg:hidden w-10 h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition-all duration-300 shrink-0" aria-label="Open Navigation Sidebar" title="Open Samsung Shortcut Side Menu">
        <svg id="mobileMenuHamburgerIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
          <path id="hamburgerLine1" stroke-linecap="round" stroke-linejoin="round" d="M4 6.5h16" />
          <path id="hamburgerLine2" stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
          <path id="hamburgerLine3" stroke-linecap="round" stroke-linejoin="round" d="M4 17.5h11" />
        </svg>
      </button>

      <!-- Brand Logo & Navigation Capsule (Unified Glass Pill, Normal Home Icon, 100% Fluid Responsive) -->
      <div class="glass-pill rounded-full px-2.5 sm:px-3.5 py-1.5 sm:py-2 flex items-center gap-2 sm:gap-3 min-w-0 shrink-0">
        <!-- Brand Link: Normal Home Icon + Title & Subtitle -->
        <a href="<?php echo htmlspecialchars($baseUrl); ?>dashboard" id="brandHomeBtn" class="flex items-center gap-2 sm:gap-2.5 group text-left focus:outline-none min-w-0" title="Return to Main Dashboard">
          <!-- Normal Home Icon (Prismatic Theme Color, No White Background Disc) -->
          <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[var(--color-primary)] group-hover:scale-110 group-hover:bg-[var(--color-primary)]/10 transition shrink-0">
            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
            </svg>
          </div>
          <div class="min-w-0 max-w-[125px] xs:max-w-[170px] sm:max-w-none">
            <span class="text-[13.5px] sm:text-[15.5px] lg:text-[17px] font-bold tracking-tight text-[var(--text-primary)] block leading-tight truncate"><?php echo htmlspecialchars($navTitle); ?></span>
            <span class="text-[9.5px] sm:text-[10.5px] font-medium text-[var(--text-muted)] flex items-center gap-1.5 truncate">
              <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-[var(--color-primary)] live-dot shrink-0"></span>
              <span id="activeZoneHeaderLabel" class="truncate"><?php echo htmlspecialchars($navSubtitle); ?></span>
            </span>
          </div>
        </a>

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



    <!-- Right: Desktop Controls (md+) + Mobile Single Right-Side User Icon Button (< md) -->
    <div class="flex items-center gap-2 shrink-0">

      <!-- DESKTOP / TABLET RIGHT BAR -->
      <div class="hidden md:flex items-center gap-2">
        <!-- Portal Link Button -->
        <a href="<?php echo htmlspecialchars($navBackUrl); ?>" id="portalBackLink" class="h-10 px-3.5 rounded-full glass-pill flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)] hover:bg-white/90 transition shrink-0" title="Return to Main Portal">
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

        <!-- Utility Pill: Dynamic Color Chooser + Notification Bell -->
        <div class="h-10 px-1.5 rounded-full glass-pill flex items-center gap-1 relative">
          <!-- Dynamic Theme Color Picker Button -->
          <button id="colorPickerToggleBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 transition relative cursor-pointer" title="Choose Theme & Font Color Combination" aria-label="Choose Theme Color">
            <span id="activeColorDot" class="w-4 h-4 rounded-full border-2 border-white shadow-sm pointer-events-none" style="background-color: var(--color-primary);"></span>
          </button>

          <!-- Notifications Bell -->
          <button id="notificationsBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 transition relative cursor-pointer" title="System Alerts & Telemetry Notifications" aria-label="Notifications">
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>
            <span id="notifBadge" class="w-2 h-2 rounded-full bg-[var(--color-alert)] absolute top-1.5 right-1.5 ring-2 ring-white pointer-events-none"></span>
          </button>
        </div>

        <!-- Theme Switcher Pill (Dark Moon / Light Sun) -->
        <div class="h-10 px-1.5 rounded-full glass-pill flex items-center gap-0.5">
          <button id="themeDarkBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition cursor-pointer" title="Dark Glass Theme" aria-label="Dark Mode">
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
            </svg>
          </button>
          <button id="themeLightBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm transition cursor-pointer" title="Light Glass Theme" aria-label="Light Glass Mode">
            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="4"/>
              <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- MOBILE RIGHT-SIDE USER ICON BUTTON (Clicking opens downside menu panel) -->
      <button
        id="mobileUserMenuBtn"
        type="button"
        class="md:hidden h-10 pl-1.5 pr-2.5 rounded-full glass-pill flex items-center gap-1.5 text-[var(--text-primary)] hover:scale-105 active:scale-95 transition relative cursor-pointer"
        aria-label="Open User & Quick Actions Menu"
        title="Open Quick Actions & User Menu"
      >
        <img
          src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA=="
          alt="User Menu"
          class="w-7 h-7 rounded-full object-cover ring-1 ring-[var(--color-primary)] pointer-events-none"
        />
        <svg id="mobileUserMenuChevron" class="w-3.5 h-3.5 text-[var(--text-secondary)] transition-transform duration-200 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
        </svg>
        <span class="w-2 h-2 rounded-full bg-[var(--color-alert)] absolute top-1 right-1 ring-1 ring-white pointer-events-none"></span>
      </button>
    </div>

    <!-- DESKTOP DYNAMIC COLOR PICKER POPOVER (Positioned cleanly under the Color Dot Button) -->
    <div id="colorPickerPopover" class="hidden fixed md:absolute right-3 sm:right-6 md:right-16 lg:right-20 top-16 md:top-12 w-72 glass-card p-4 z-50 shadow-2xl border border-white/80">
      <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-black/10">
        <div>
          <span class="font-bold text-xs text-[var(--text-primary)] block">Theme & Font Color Studio</span>
          <span class="text-[10px] text-[var(--text-muted)]">Transforms UI, charts & typography</span>
        </div>
        <input id="desktopCustomColorInput" type="color" value="#10B981" class="w-8 h-8 rounded-lg cursor-pointer border border-black/10 bg-transparent p-0.5" title="Pick Custom Color" />
      </div>
      <div class="grid grid-cols-6 gap-2 mb-2">
        <button type="button" data-theme-color="#10B981" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981] cursor-pointer" title="Emerald Default (#10B981)" aria-label="Emerald"></button>
        <button type="button" data-theme-color="#729C51" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#729C51] cursor-pointer" title="Leaf Green (#729C51)" aria-label="Leaf Green"></button>
        <button type="button" data-theme-color="#0284C7" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#0284C7] cursor-pointer" title="Ocean Blue (#0284C7)" aria-label="Ocean Blue"></button>
        <button type="button" data-theme-color="#7C3AED" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#7C3AED] cursor-pointer" title="Royal Violet (#7C3AED)" aria-label="Royal Violet"></button>
        <button type="button" data-theme-color="#E11D48" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#E11D48] cursor-pointer" title="Sunset Rose (#E11D48)" aria-label="Sunset Rose"></button>
        <button type="button" data-theme-color="#D97706" class="color-Preset-btn color-preset-btn w-8 h-8 rounded-full bg-[#D97706] cursor-pointer" title="Amber Gold (#D97706)" aria-label="Amber Gold"></button>
      </div>
      <p id="activeHexReadout" class="text-[11px] font-semibold text-[var(--text-secondary)] text-center mt-2">Active Palette: #10B981</p>
    </div>

    <!-- Notifications Dropdown Popover -->
    <div id="notificationsPopover" class="hidden fixed md:absolute right-3 sm:right-6 md:right-2 lg:right-4 top-16 md:top-12 w-[calc(100vw-24px)] max-w-sm glass-card p-4 z-50 shadow-2xl border border-white/80">
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

      <button id="mobileNotifBtn" type="button" class="h-10 px-3 rounded-full glass-pill flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)] relative" aria-label="Mobile Notifications">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
        </svg>
        <span>Alerts</span>
        <span class="w-2 h-2 rounded-full bg-[var(--color-alert)]"></span>
      </button>

      <div class="h-10 px-1 rounded-full glass-pill flex items-center gap-0.5">
        <button id="mobileThemeDarkBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)]" aria-label="Mobile Dark Mode">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
          </svg>
        </button>
        <button id="mobileThemeLightBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm" aria-label="Mobile Light Mode">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="4"/>
            <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Row C: Mobile Theme Color Chooser Bar -->
    <div class="p-2.5 rounded-2xl glass-pill flex items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <label for="mobileCustomColorInput" class="text-xs font-bold text-[var(--text-primary)] cursor-pointer flex items-center gap-1.5">
          <span>🎨 Theme Color:</span>
        </label>
        <input id="mobileCustomColorInput" type="color" value="#10B981" class="w-7 h-7 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Pick any custom theme & font color" />
      </div>
      <div class="flex items-center gap-1.5">
        <button type="button" data-theme-color="#10B981" class="color-Preset-btn w-6 h-6 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Default (#10B981)" aria-label="Emerald Default Theme"></button>
        <button type="button" data-theme-color="#729C51" class="color-Preset-btn w-6 h-6 rounded-full bg-[#729C51]" title="Nature Green (#729C51)" aria-label="Nature Green Theme"></button>
        <button type="button" data-theme-color="#0284C7" class="color-Preset-btn w-6 h-6 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)" aria-label="Ocean Blue Theme"></button>
        <button type="button" data-theme-color="#7C3AED" class="color-Preset-btn w-6 h-6 rounded-full bg-[#7C3AED]" title="Royal Purple (#7C3AED)" aria-label="Royal Purple Theme"></button>
        <button type="button" data-theme-color="#E11D48" class="color-Preset-btn w-6 h-6 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)" aria-label="Crimson Rose Theme"></button>
        <button type="button" data-theme-color="#0D9488" class="color-Preset-btn w-6 h-6 rounded-full bg-[#0D9488]" title="Teal Lagoon (#0D9488)" aria-label="Teal Lagoon Theme"></button>
      </div>
    </div>
  </div>
</header>

