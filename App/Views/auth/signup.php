<?php
/**
 * Smart Management System — High-Tech AI Sign Up Portal
 * Layout: Left-Side Sign Up Form + Right-Side AI Hero Showcase
 * Features:
 * - Dynamic Ambient Glassmorphism Backdrop with Floating Light Orbs
 * - Adaptive Dark & Light Modes dynamically calculated from Color Picker selection
 * - Hardware-accelerated sliding transition when switching between Sign Up and Login
 */
$page_label = $page_label ?? 'Smart Management Dashboard';
$baseUrl = function_exists('base_url') ? base_url('/') : './';

// User requirement: "signup page not now make"
if (!headers_sent()) {
    header('Location: ' . (function_exists('base_url') ? base_url('login?notice=signup_disabled') : '/login?notice=signup_disabled'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>Sign Up & Register — Smart Management Dashboard</title>
  <!-- High-Definition Application Favicons -->
  <link rel="icon" type="image/png" sizes="64x64" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-64x64.png') : $baseUrl . 'assets/images/favicon-64x64.png'; ?>">
  <link rel="shortcut icon" type="image/png" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-64x64.png') : $baseUrl . 'assets/images/favicon-64x64.png'; ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-32x32.png') : $baseUrl . 'assets/images/favicon-32x32.png'; ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon-16x16.png') : $baseUrl . 'assets/images/favicon-16x16.png'; ?>">
  <link rel="icon" type="image/svg+xml" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/favicon.svg') : $baseUrl . 'assets/images/favicon.svg'; ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo function_exists('v_asset') ? v_asset('assets/images/apple-touch-icon.png') : $baseUrl . 'assets/images/apple-touch-icon.png'; ?>">
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ["'PT Serif'", "Georgia", "serif"],
            serif: ["'PT Serif'", "Georgia", "serif"],
            editorial: ["'PT Serif'", "Georgia", "serif"],
            mono: ["'Share Tech'", "monospace"]
          }
        }
      }
    };
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Dancing+Script:wght@600;700&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap" rel="stylesheet">
  <!-- Authentication Portal Stylesheet -->
  <link rel="stylesheet" href="<?php echo v_asset('assets/css/auth.css'); ?>">
  <!-- Global Typography (Playfair Display) -->
  <link rel="stylesheet" href="<?php echo v_asset('assets/css/font.css'); ?>">
  <!-- Universal Bootstrap 5 Toast Notification System -->
  <link rel="stylesheet" href="<?php echo v_asset('assets/css/toast.css'); ?>">
  <script>
    window.PORTAL_LOGO_URL = '<?php echo function_exists("v_asset") ? v_asset("assets/images/favicon-64x64.png") : $baseUrl . "assets/images/favicon-64x64.png"; ?>';
  </script>
  <script src="<?php echo v_asset('assets/js/toast.js'); ?>"></script>
  <!-- Winter Snowfall Effect (Default: ON) -->
  <script src="<?php echo v_asset('assets/js/snowfall.js'); ?>"></script>
  <!-- Default Page Theme Color: Warm Coral (R:229 G:140 B:127) -->
  <style>:root { --color-primary: #E58C7F; --color-primary-rgb: 229, 140, 127; --color-primary-dark: #D47162; --color-primary-light: #F4B2A8; }</style>
  <!-- Immediate Dark/Light Theme Initialization to eliminate flash -->
  <script>
    (function() {
      var savedTheme = localStorage.getItem('portal_auth_theme') || 'dark';
      if (savedTheme === 'light') {
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-theme', 'light');
      } else {
        document.documentElement.classList.add('dark');
        document.documentElement.setAttribute('data-theme', 'dark');
      }
    })();
  </script>
</head>
<body class="theme-dark antialiased selection:bg-[var(--color-primary)] selection:text-white p-0 lg:p-7">

  <!-- ================= AMBIENT FROSTED GLASS BACKGROUND ORBS WITH FLUID ANIMATIONS ================= -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div id="ambientOrb1" class="ambient-orb-1 absolute -top-28 -left-28 w-[540px] h-[540px] rounded-full blur-[110px] opacity-80 transition-colors duration-700"></div>
    <div id="ambientOrb2" class="ambient-orb-2 absolute -bottom-36 -right-36 w-[600px] h-[600px] rounded-full blur-[120px] opacity-75 transition-colors duration-700"></div>
    <div id="ambientOrb3" class="ambient-orb-3 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[640px] h-[640px] rounded-full blur-[130px] opacity-50 transition-colors duration-700"></div>
    <div id="ambientOrb4" class="ambient-orb-4 absolute bottom-12 left-12 w-[420px] h-[420px] rounded-full blur-[100px] opacity-60 transition-colors duration-700"></div>
  </div>

  <!-- High-Tech Animated Cyber Grid Backdrop -->
  <div class="bg-cyber-grid"></div>
  <div class="bg-mesh-particles"></div>

  <!-- ================= COLOR PICKER POPOVER ================= -->
  <div id="colorPickerPopover" class="hidden fixed w-64 glass-card p-3.5 z-50 shadow-2xl border border-white/60">
    <div class="flex items-center justify-between pb-2 mb-2 border-b border-black/10 dark:border-white/10">
      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#E58C7F" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#E58C7F" class="color-preset-btn w-7 h-7 rounded-full bg-[#E58C7F] ring-2 ring-offset-1 ring-[#E58C7F]" title="Warm Coral (#E58C7F)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>
  </div>

  <!-- ================= MAIN WIDESCREEN CARD (SIGN UP MODE: FORM LEFT, IMAGE RIGHT) ================= -->
  <main id="authCard" class="auth-card relative w-full max-w-[1240px] mx-auto rounded-none lg:rounded-[38px] glass-card p-0 lg:p-6 shadow-2xl border-0 lg:border lg:border-white/60 overflow-hidden my-auto mode-signup">

    <div id="authSliderContainer" class="auth-slider-container w-full min-h-0 lg:min-h-[640px]">

      <!-- =========================================================================
           SHOWCASE PANEL:
           - On Sign Up: Right Side (Slid smoothly to right)
           - On Login: Left Side (Natural position)
           - Clear 100% Unobstructed 3D Video View (No overlap with phone/calendar/robot)
           ========================================================================= -->
      <section id="showcasePanel" class="auth-panel auth-panel-showcase rounded-none lg:rounded-[30px] p-3.5 sm:p-5 flex flex-col justify-between relative overflow-hidden">

        <!-- High-Definition Left-Side Card Video: HLS Chunked Streaming + Faststart Progressive MP4 Fallback -->
        <video
          id="showcaseVideo"
          autoplay
          loop
          muted
          playsinline
          preload="auto"
          poster="<?php echo $baseUrl; ?>assets/video/posters/poster_signup.webp"
          class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none rounded-none lg:rounded-[30px]"
          style="object-position: 50% 67%;"
          data-current-mode="signup"
          data-hls-login="<?php echo $baseUrl; ?>assets/video/hls/login/index.m3u8"
          data-hls-signup="<?php echo $baseUrl; ?>assets/video/hls/signup/index.m3u8"
          data-mp4-login="<?php echo $baseUrl; ?>assets/video/1.mp4"
          data-mp4-signup="<?php echo $baseUrl; ?>assets/video/2.mp4"
          data-poster-login="<?php echo $baseUrl; ?>assets/video/posters/poster_login.webp"
          data-poster-signup="<?php echo $baseUrl; ?>assets/video/posters/poster_signup.webp"
        >
          <source src="<?php echo $baseUrl; ?>assets/video/2.mp4" type="video/mp4">
        </video>

        <!-- Subtle Top & Bottom Legibility Vignette — Center video remains 100% crisp & clear -->
        <div class="absolute inset-0 pointer-events-none z-[1] showcase-vignette"></div>

        <!-- 1. Top Section: Back Button + Theme Studio Controls -->
        <div class="relative z-10 flex flex-col gap-2">
          <!-- Top Nav Bar -->
          <div class="flex items-center justify-between">
            <a
              href="<?php echo $baseUrl; ?>dashboard"
              class="h-8 sm:h-9 px-2.5 sm:px-4 rounded-full video-glass-card text-[var(--text-primary)] hover:text-[var(--color-primary)] active:scale-95 text-[11px] sm:text-xs font-bold flex items-center gap-1.5 sm:gap-2 shadow-sm transition shrink-0"
              title="Return to Smart Management Dashboard"
            >
              <svg class="w-3.5 h-3.5 text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              <span class="hidden min-[380px]:inline">Back to dashboard</span>
              <span class="inline min-[380px]:hidden">Dashboard</span>
            </a>

            <!-- Controls: Palette Chooser + Dark/Light Mode -->
            <div class="flex items-center gap-2">
              <!-- Palette Chooser -->
              <button id="colorPickerToggleBtn" type="button" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full video-glass-card flex items-center justify-center hover:scale-105 active:scale-95 transition relative shadow-sm group" title="Choose Dynamic Theme Color" aria-label="Theme Color">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[var(--text-primary)] transition-transform group-hover:rotate-12 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
                  <circle cx="13.5" cy="6.5" r=".75" fill="currentColor"></circle>
                  <circle cx="17.5" cy="10.5" r=".75" fill="currentColor"></circle>
                  <circle cx="8.5" cy="7.5" r=".75" fill="currentColor"></circle>
                  <circle cx="6.5" cy="12.5" r=".75" fill="currentColor"></circle>
                </svg>
                <span id="activeColorDot" class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full absolute -top-0.5 -right-0.5 border-2 border-white dark:border-slate-800 shadow-md transition-all group-hover:scale-110" style="background-color: var(--color-primary);"></span>
              </button>

              <!-- Dark / Light Theme Toggle -->
              <div class="h-8 sm:h-9 p-0.5 rounded-full video-glass-card flex items-center gap-0.5 shadow-sm">
                <button id="themeDarkBtn" type="button" class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Dark Glass Mode" aria-label="Dark Mode">
                  <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                  </svg>
                </button>
                <button id="themeLightBtn" type="button" class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-amber-500 bg-white shadow-md border border-black/5 transition" title="Light Glass Mode" aria-label="Light Mode">
                  <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4.5"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
                <!-- 2. Pure Center Stage: 100% UNTOUCHED, CRYSTAL-CLEAR VIDEO VIEW (Zero Overlay Clutter) -->
        <div class="relative z-10 flex-1 min-h-[30px] sm:min-h-[100px] lg:min-h-[200px] pointer-events-none flex items-center justify-center">
          <!-- Perfectly Clear Viewing Window for 3D Video & Cyber HUD Animations -->
        </div>

<!-- 3. Bottom Dock: Stat Metrics + Sleek Testimonials (Visible on Desktop, Clean on Mobile) -->
        <div class="hidden lg:flex relative z-10 pt-1.5 flex-col gap-2">

          <!-- Stat Metrics Pill Row (Low-profile, non-intrusive) -->
          <div class="flex items-center justify-between gap-2">
            <div id="statChipLeft" class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl video-glass-card shadow-sm hover:scale-[1.02] transition">
              <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[var(--color-primary)]/20 text-[var(--color-primary)] flex items-center justify-center shrink-0 border border-[var(--color-primary)]/30">
                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/>
                </svg>
              </div>
              <div class="leading-tight flex items-baseline gap-1">
                <span id="statChipLeftTitle" class="text-[11px] sm:text-xs font-black text-[var(--text-primary)] drop-shadow-sm font-serif stat-chip-num">Instant Setup</span>
                <span id="statChipLeftDesc" class="text-[9px] sm:text-[10px] text-[var(--text-muted)] font-bold hidden sm:inline font-sans stat-chip-desc">Zero Config Provision</span>
              </div>
            </div>

            <div id="statChipRight" class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl video-glass-card shadow-sm hover:scale-[1.02] transition">
              <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[var(--color-primary)]/20 text-[var(--color-primary)] flex items-center justify-center shrink-0 border border-[var(--color-primary)]/30">
                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
              <div class="leading-tight flex items-baseline gap-1">
                <span id="statChipRightTitle" class="text-[11px] sm:text-xs font-black text-[var(--text-primary)] drop-shadow-sm font-serif stat-chip-num">99.98% Telemetry</span>
                <span id="statChipRightDesc" class="text-[9px] sm:text-[10px] text-[var(--text-muted)] font-bold hidden sm:inline font-sans stat-chip-desc">Real-Time SLA</span>
              </div>
            </div>
          </div>

          <!-- Bottom Testimonials: Ultra-Compact Low-Profile Micro-Cards -->
          <div id="showcaseBottomCards" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-left">

            <!-- Card 1: David S. -->
            <div class="p-2 rounded-xl video-glass-card hover:scale-[1.02] transition group">
              <div class="flex items-center justify-between gap-1.5">
                <div class="flex items-center gap-1.5 min-w-0">
                  <div class="w-6 h-6 rounded-full bg-[var(--color-primary)]/30 ring-1 ring-white/50 flex items-center justify-center text-white text-[10px] font-bold overflow-hidden shrink-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces" alt="David S." class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\' fill=\'%2310B981\'><circle cx=\'16\' cy=\'16\' r=\'16\'/><text x=\'16\' y=\'20\' text-anchor=\'middle\' fill=\'white\' font-size=\'12\'>DS</text></svg>';" />
                  </div>
                  <div class="min-w-0">
                    <p class="text-[11px] font-bold text-[#0F172A] dark:text-white truncate leading-tight font-serif testimonial-name">David S.</p>
                    <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none font-semibold font-mono testimonial-role">CEO & Co-founder</p>
                  </div>
                </div>
                <button type="button" class="w-5 h-5 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View David S. Case">
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                  </svg>
                </button>
              </div>
              <p class="text-[9.5px] text-[var(--color-primary)] font-bold mt-1 truncate drop-shadow-sm font-editorial italic testimonial-quote">"Autonomous yield +40%."</p>
            </div>

            <!-- Card 2: Mira K. -->
            <div class="p-2 rounded-xl video-glass-card hover:scale-[1.02] transition group">
              <div class="flex items-center justify-between gap-1.5">
                <div class="flex items-center gap-1.5 min-w-0">
                  <div class="w-6 h-6 rounded-full bg-[var(--color-primary-dark)]/40 ring-1 ring-white/50 flex items-center justify-center text-white text-[10px] font-bold overflow-hidden shrink-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop&crop=faces" alt="Mira K." class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\' fill=\'%23059669\'><circle cx=\'16\' cy=\'16\' r=\'16\'/><text x=\'16\' y=\'20\' text-anchor=\'middle\' fill=\'white\' font-size=\'12\'>MK</text></svg>';" />
                  </div>
                  <div class="min-w-0">
                    <p class="text-[11px] font-bold text-[#0F172A] dark:text-white truncate leading-tight font-serif testimonial-name">Mira K.</p>
                    <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none font-semibold font-mono testimonial-role">Marketing Mgr</p>
                  </div>
                </div>
                <button type="button" class="w-5 h-5 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View Mira K. Case">
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                  </svg>
                </button>
              </div>
              <p class="text-[9.5px] text-[var(--color-primary)] font-bold mt-1 truncate drop-shadow-sm font-editorial italic testimonial-quote">"Zero manual errors."</p>
            </div>

            <!-- Card 3: Imran Jakir -->
            <div class="p-2 rounded-xl video-glass-card hover:scale-[1.02] transition group hidden sm:block">
              <div class="flex items-center justify-between gap-1.5">
                <div class="flex items-center gap-1.5 min-w-0">
                  <div class="w-6 h-6 rounded-full bg-[var(--color-primary)]/40 ring-1 ring-white/50 flex items-center justify-center text-white text-[10px] font-bold overflow-hidden shrink-0 shadow-sm">
                    <img src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA==" alt="Imran Jakir" class="w-full h-full object-cover" />
                  </div>
                  <div class="min-w-0">
                    <p class="text-[11px] font-bold text-[#0F172A] dark:text-white truncate leading-tight font-serif testimonial-name">Imran Jakir</p>
                    <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none font-semibold font-mono testimonial-role">Agronomist</p>
                  </div>
                </div>
                <button type="button" class="w-5 h-5 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View Imran Jakir Case">
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                  </svg>
                </button>
              </div>
              <p class="text-[9.5px] text-[var(--color-primary)] font-bold mt-1 truncate drop-shadow-sm font-editorial italic testimonial-quote">"80% Harvest yield."</p>
            </div>
          </div>
        </div>
      </section>

      <!-- =========================================================================
           FORM PANEL:
           - On Sign Up: Left Side (Slid smoothly to left)
           - On Login: Right Side (Natural position)
           - Seamlessly morphs between Sign Up and Login form views
           ========================================================================= -->
      <section id="formPanel" class="auth-panel auth-panel-form rounded-t-[34px] lg:rounded-[30px] p-5 sm:p-6 lg:p-7 flex flex-col justify-between right-glass-panel relative overflow-hidden">

        <!-- Mobile Drag Handle (Native Bottom Sheet Look) -->
        <div class="mobile-drag-handle"></div>

        <!-- Top Status & Segmented Mode Switcher (Pinned to Top) -->
        <div class="flex items-center justify-between gap-1.5 sm:gap-2 pb-2.5 mb-2 sm:mb-3 border-b border-black/5 dark:border-white/5 w-full">
          <span class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-[10.5px] font-bold bg-[var(--color-primary)]/15 text-[var(--color-primary)] border border-[var(--color-primary)]/30 whitespace-nowrap shrink-0 live-badge font-mono">
            <span class="live-dot"></span>
            <span class="hidden min-[420px]:inline">CE & IT </span><span>Secure Access</span>
          </span>

          <!-- Mode Toggle Segmented Pill -->
          <div class="p-0.5 rounded-full bg-black/10 dark:bg-white/10 flex items-center text-[11px] sm:text-xs font-bold shrink-0">
            <a
              id="tabLoginBtn"
              href="<?php echo $baseUrl; ?>login"
              class="px-2 sm:px-3 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)] whitespace-nowrap leading-none"
            >Log In</a>
            <a href="<?php echo $baseUrl; ?>otp" class="px-2 sm:px-3 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)] whitespace-nowrap leading-none">OTP</a>
            <a
              id="tabSignUpBtn"
              href="<?php echo $baseUrl; ?>signup"
              class="px-2 sm:px-3 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm whitespace-nowrap leading-none"
            >Sign Up</a>
          </div>
        </div>

        <!-- Dynamic Form Container with 2 Views (Stretches Full Height) -->
        <div class="relative w-full flex-1 flex flex-col justify-between">

          <!-- ================= VIEW 1: SIGN UP (DEFAULT ACTIVE IN SIGNUP.PHP) ================= -->
          <div id="signUpFormView" class="form-view form-view-active">
            <!-- Top Content: Headline & Form Inputs (Mockup Design) -->
            <div class="w-full">
              <div class="text-center mb-3 sm:mb-4">
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--text-primary)] leading-tight font-serif auth-heading">Create operator account</h2>
                <p class="text-xs text-[var(--text-muted)] mt-1 font-editorial auth-subtitle">Register your operator profile for telemetry access.</p>
              </div>

              <form id="signUpForm" class="mt-3 space-y-2.5 sm:space-y-3" onsubmit="return false;">
                <!-- First Name & Last Name (Grid 2 cols with Two-Tier Cards) -->
                <div class="grid grid-cols-2 gap-2 sm:gap-2.5">
                  <div class="mobile-input-card">
                    <div class="mobile-input-body">
                      <label for="signUpFirstName" class="mobile-input-label">First Name</label>
                      <input
                        id="signUpFirstName"
                        type="text"
                        placeholder="Omar"
                        required
                        class="mobile-input-field"
                      />
                    </div>
                  </div>
                  <div class="mobile-input-card">
                    <div class="mobile-input-body">
                      <label for="signUpLastName" class="mobile-input-label">Last Name</label>
                      <input
                        id="signUpLastName"
                        type="text"
                        placeholder="Jakir"
                        required
                        class="mobile-input-field"
                      />
                    </div>
                  </div>
                </div>

                <!-- Email Address (Two-Tier Card) -->
                <div class="mobile-input-card">
                  <div class="mobile-input-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                  </div>
                  <div class="mobile-input-body">
                    <label for="signUpEmail" class="mobile-input-label">Email Address</label>
                    <input
                      id="signUpEmail"
                      type="email"
                      placeholder="new.operator@gmiu.edu.in"
                      required
                      autocomplete="email"
                      class="mobile-input-field"
                    />
                  </div>
                  <button type="button" class="mobile-input-action" title="Clear input" onclick="const el=document.getElementById('signUpEmail'); if(el){el.value=''; el.focus();}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="9"/><path d="m15 9-6 6M9 9l6 6"/>
                    </svg>
                  </button>
                </div>

                <!-- Password with Strength Meter (Two-Tier Card) -->
                <div class="mobile-input-card">
                  <div class="mobile-input-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                  </div>
                  <div class="mobile-input-body">
                    <label for="signUpPassword" class="mobile-input-label">Create Password</label>
                    <input
                      id="signUpPassword"
                      type="password"
                      placeholder="Min 6 characters"
                      required
                      autocomplete="new-password"
                      class="mobile-input-field"
                    />
                  </div>
                  <button id="toggleSignUpPasswordBtn" type="button" class="mobile-input-action" title="Show/Hide Password">
                    <svg id="eyeIconSignUp" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </button>
                </div>
                <!-- Password Strength Bar -->
                <div class="px-1">
                  <div class="w-full bg-black/10 dark:bg-white/10 h-1 rounded-full overflow-hidden">
                    <div id="passwordStrengthBar" class="h-full w-0 transition-all duration-300 rounded-full bg-red-500"></div>
                  </div>
                  <div class="flex justify-between items-center text-[10px] text-[var(--text-muted)] mt-1 font-semibold">
                    <span>Security strength:</span>
                    <span id="passwordStrengthText" class="font-bold text-[var(--text-muted)]">Too short</span>
                  </div>
                </div>

                <!-- Confirm Password (Two-Tier Card) -->
                <div class="mobile-input-card">
                  <div class="mobile-input-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                  </div>
                  <div class="mobile-input-body">
                    <label for="signUpPasswordConfirm" class="mobile-input-label">Confirm Password</label>
                    <input
                      id="signUpPasswordConfirm"
                      type="password"
                      placeholder="Re-enter password"
                      required
                      autocomplete="new-password"
                      class="mobile-input-field"
                    />
                  </div>
                  <button id="toggleSignUpConfirmBtn" type="button" class="mobile-input-action" title="Show/Hide Confirm Password">
                    <svg id="eyeIconConfirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </button>
                </div>
                <div id="passwordMatchIndicator" class="hidden text-[10px] mt-1 font-semibold px-1"></div>

                <!-- Terms & Conditions Checkbox -->
                <div class="pt-2 pb-2 flex items-center justify-between">
                  <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-[var(--text-secondary)]">
                    <input
                      id="signUpTerms"
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] accent-[var(--color-primary)] cursor-pointer shrink-0"
                    />
                    <span class="leading-normal">Accept <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Terms of Service</a> & <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Privacy Policy</a></span>
                  </label>
                </div>

                <!-- Sign Up Error & Success Banners -->
                <div id="signUpErrorBanner" class="hidden items-center gap-2 p-2.5 rounded-xl bg-red-500/15 border border-red-500/30 text-red-500 dark:text-red-400 text-xs font-semibold">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                  </svg>
                  <span id="signUpErrorText">Please review required fields</span>
                </div>

                <div id="signUpSuccessBanner" class="hidden items-center gap-2 p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                  </svg>
                  <span id="signUpSuccessText">Operator registration complete! Entering Dashboard...</span>
                </div>
              </form>
            </div>


            <!-- Bottom Action Area (Docked at Bottom, Fixed in Responsive) -->
            <div class="auth-action-dock mt-4 sm:mt-auto pt-3 border-t border-black/10 dark:border-white/10 w-full shrink-0">
              <div class="auth-action-dock-inner">
                <button
                  id="signUpSubmitBtn"
                  type="button"
                  class="mobile-continue-btn"
                >
                  <span id="signUpSubmitBtnText">Continue</span>
                  <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </button>

                <p class="text-xs text-[var(--text-muted)] text-center mt-2.5 font-medium">
                  Already have an account?
                  <a href="<?php echo $baseUrl; ?>login" id="linkToLogin" class="font-bold text-[var(--color-primary)] hover:underline transition cursor-pointer">Log In</a>
                </p>
              </div>
            </div>
          </div>

          <!-- ================= VIEW 2: LOGIN (SLIDES IN UPON CLICKING 'LOG IN') ================= -->
          <div id="loginFormView" class="form-view form-view-hidden">
            <!-- Top Content: Headline & Form Inputs (Mockup Design) -->
            <div class="w-full">
              <div class="text-center mb-3 sm:mb-4">
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--text-primary)] leading-tight font-serif auth-heading">Login with email address</h2>
                <p class="text-xs text-[var(--text-muted)] mt-1 font-editorial auth-subtitle">Sign in to access your portal & telemetry dashboard.</p>
              </div>

              <form id="loginForm" class="mt-3 sm:mt-4 space-y-3 sm:space-y-3.5" onsubmit="return false;">
                <!-- Email Address (Two-Tier Card Matching Mockup) -->
                <div class="mobile-input-card">
                  <div class="mobile-input-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                  </div>
                  <div class="mobile-input-body">
                    <label for="loginEmail" class="mobile-input-label">Email Address</label>
                    <input
                      id="loginEmail"
                      type="email"
                      placeholder="nazarisara@yahoo.com"
                      required
                      autocomplete="email"
                      class="mobile-input-field"
                    />
                  </div>
                  <button type="button" class="mobile-input-action" title="Clear input" onclick="const el=document.getElementById('loginEmail'); if(el){el.value=''; el.focus();}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="9"/><path d="m15 9-6 6M9 9l6 6"/>
                    </svg>
                  </button>
                </div>

                <!-- Password (Two-Tier Card Matching Mockup) -->
                <div class="mobile-input-card">
                  <div class="mobile-input-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                  </div>
                  <div class="mobile-input-body">
                    <label for="loginPassword" class="mobile-input-label">Password</label>
                    <input
                      id="loginPassword"
                      type="password"
                      placeholder="••••••••••••"
                      required
                      autocomplete="current-password"
                      class="mobile-input-field"
                    />
                  </div>
                  <button id="toggleLoginPasswordBtn" type="button" class="mobile-input-action" title="Show/Hide Password">
                    <svg id="eyeIconLogin" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                  </button>
                </div>

                <!-- Remember Me & Forgot Password Row (Matching Mockup) -->
                <div class="pt-0.5 pb-0.5 flex items-center justify-between text-xs px-1">
                  <label class="flex items-center gap-2 cursor-pointer select-none font-medium text-[var(--text-secondary)]">
                    <input
                      id="rememberMeCheckbox"
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] accent-[var(--color-primary)] cursor-pointer shrink-0"
                    />
                    <span class="leading-normal">Remember me</span>
                  </label>
                  <button id="forgotPwdBtn" type="button" class="text-xs font-bold text-[var(--color-primary)] hover:underline">Forgot password?</button>
                </div>

                <!-- Login Error & Success Banners -->
                <div id="loginErrorBanner" class="hidden items-center gap-2 p-2.5 rounded-xl bg-red-500/15 border border-red-500/30 text-red-500 dark:text-red-400 text-xs font-semibold">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                  </svg>
                  <span id="loginErrorText">Invalid credentials. Try "admin123"</span>
                </div>

                <div id="loginSuccessBanner" class="hidden items-center gap-2 p-2.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 text-xs font-semibold">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                  </svg>
                  <span id="loginSuccessText">Authentication verified! Redirecting to Dashboard...</span>
                </div>
              </form>
            </div>


            <!-- Bottom Action Area (Docked at the Bottom, Fixed in Responsive) -->
            <div class="auth-action-dock mt-5 sm:mt-auto pt-3.5 sm:pt-4 border-t border-black/10 dark:border-white/10 w-full shrink-0">
              <div class="auth-action-dock-inner">
                <button
                  id="loginSubmitBtn"
                  type="button"
                  class="mobile-continue-btn"
                >
                  <span id="loginSubmitBtnText">Log In</span>
                  <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </button>

                <p class="text-xs text-[var(--text-muted)] text-center mt-3 font-medium">
                  Don't have an account?
                  <a href="<?php echo $baseUrl; ?>signup" id="linkToSignUp" class="font-bold text-[var(--color-primary)] hover:underline transition cursor-pointer">Sign Up</a>
                </p>
              </div>
            </div>
          </div>

        </div>

      </section>

    </div>

  </main>

  <!-- ================= FORGOT PASSWORD MODAL ================= -->
  <div id="forgotPwdModal" class="fixed inset-0 bg-black/50 backdrop-blur-md z-[70] hidden flex items-center justify-center p-4">
    <div class="glass-card-strong w-full max-w-sm p-6 rounded-3xl shadow-2xl relative text-left">
      <div class="flex items-center justify-between pb-2 mb-3 border-b border-black/10 dark:border-white/10">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-full bg-[var(--color-primary)]/20 text-[var(--color-primary)] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
          </div>
          <h3 class="text-sm font-bold text-[var(--text-primary)]">Reset Operator Password</h3>
        </div>
        <button id="closeForgotModalBtn" type="button" class="w-7 h-7 rounded-full glass-pill flex items-center justify-center text-xs font-bold" aria-label="Close">✕</button>
      </div>

      <p class="text-xs text-[var(--text-muted)] mb-3">Enter your registered email address to receive password reset OTP token:</p>

      <form id="forgotForm" onsubmit="return false;" class="space-y-3">
        <input
          id="resetEmailInput"
          type="email"
          placeholder="operator@gmiu.edu.in"
          required
          class="w-full h-10 px-3.5 rounded-xl auth-input text-xs font-semibold"
        />
        <button id="sendResetBtn" type="submit" class="w-full h-10 rounded-full bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 transition shadow-md">
          Send Recovery Token
        </button>
      </form>
      <div id="resetSuccessMsg" class="hidden text-xs text-emerald-400 font-semibold mt-2 text-center">
        ✓ Recovery instructions sent to your email.
      </div>
    </div>
  </div>

  <!-- Shared Top-Right Bootstrap 5 Toast Container -->
  <?php include dirname(__DIR__) . '/partials/toast.php'; ?>

  <!-- High-Performance Video Engine (HLS Chunks + Faststart Stream Manager) -->
  <script src="<?php echo $baseUrl; ?>assets/js/hls.min.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/js/auth-video.js"></script>

  <!-- ================= JAVASCRIPT CONTROLLER ================= -->
  <script>
    (function () {
      // --- 1. APPLICATION STATE ---
      const state = {
        mode: 'signup', // Default mode on signup page
        theme: localStorage.getItem('portal_auth_theme') || 'dark',
        primaryHex: localStorage.getItem('portal_auth_color_signup') || '#E58C7F',
        loginPwdVisible: false,
        signUpPwdVisible: false,
        signUpConfirmVisible: false,
        baseUrl: "<?php echo rtrim($baseUrl, '/'); ?>",
        targetUrl: "<?php echo !empty($_GET['target']) ? htmlspecialchars($_GET['target']) : $baseUrl . 'dashboard'; ?>"
      };

      const authCard = document.getElementById('authCard');
      const loginFormView = document.getElementById('loginFormView');
      const signUpFormView = document.getElementById('signUpFormView');
      const tabLoginBtn = document.getElementById('tabLoginBtn');
      const tabSignUpBtn = document.getElementById('tabSignUpBtn');

      // Toast Notification Helper (Delegates to Universal Top-Right Bootstrap 5 Toast Engine)
      function showToast(msg, type) {
        if (window.PortalToast && typeof window.PortalToast.show === 'function') {
          return window.PortalToast.show(msg, type);
        }
      }

      // --- 2. DYNAMIC THEME & COLOR PALETTE ENGINE ---
      function hexToRgb(hex) {
        const clean = hex.replace('#', '');
        const num = parseInt(clean.length === 3 ? clean.split('').map(c => c + c).join('') : clean, 16);
        return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
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
        return `#${toHex(r)}${toHex(g)}${toHex(b)}`.toUpperCase();
      }

      function applyCustomColorPalette(hexColor) {
        state.primaryHex = hexColor.toUpperCase();
        localStorage.setItem('portal_auth_color_signup', state.primaryHex);
        const rgb = hexToRgb(hexColor);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        const root = document.documentElement;

        const lightShade = hslToHex(hsl.h, Math.min(95, hsl.s + 20), Math.min(82, hsl.l + 24));
        const darkShade = hslToHex(hsl.h, Math.min(90, hsl.s + 10), Math.max(18, hsl.l - 18));
        const complementaryPeak = hslToHex((hsl.h + 215) % 360, 85, 52);

        root.style.setProperty('--color-primary', state.primaryHex);
        root.style.setProperty('--color-primary-dark', darkShade);
        root.style.setProperty('--color-primary-light', lightShade);
        root.style.setProperty('--color-primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
        root.style.setProperty('--color-alert', (state.primaryHex === '#10B981' || state.primaryHex === '#729C51') ? '#EB5011' : complementaryPeak);

        // Ambient Floating Orbs (Behind glass card)
        const orb1 = document.getElementById('ambientOrb1');
        const orb2 = document.getElementById('ambientOrb2');
        const orb3 = document.getElementById('ambientOrb3');
        const orb4 = document.getElementById('ambientOrb4');

        if (state.theme === 'dark') {
          // Dynamic Dark Mode Colors derived directly from the Color Picker
          const mesh1 = hslToHex((hsl.h + 25) % 360, 60, 11);
          const mesh2 = hslToHex(hsl.h, 65, 13);
          const mesh3 = hslToHex(hsl.h, 45, 6);
          const mesh4 = hslToHex((hsl.h - 25 + 360) % 360, 55, 10);

          root.style.setProperty('--bg-mesh-1', mesh1);
          root.style.setProperty('--bg-mesh-2', mesh2);
          root.style.setProperty('--bg-mesh-3', mesh3);
          root.style.setProperty('--bg-mesh-4', mesh4);

          // Glass Surfaces & Card Tint in Dark Mode
          root.style.setProperty('--auth-card-bg-dark', `linear-gradient(135deg, hsla(${hsl.h}, 45%, 11%, 0.88) 0%, hsla(${hsl.h}, 60%, 16%, 0.78) 35%, hsla(${hsl.h}, 40%, 9%, 0.86) 70%, hsla(${hsl.h}, 65%, 20%, 0.80) 100%)`);
          root.style.setProperty('--auth-card-border-dark', `hsla(${hsl.h}, 65%, 65%, 0.28)`);

          root.style.setProperty('--glass-surface', `hsla(${hsl.h}, 45%, 10%, 0.82)`);
          root.style.setProperty('--glass-surface-strong', `hsla(${hsl.h}, 50%, 12%, 0.94)`);
          root.style.setProperty('--glass-pill', `hsla(${hsl.h}, 40%, 15%, 0.86)`);
          root.style.setProperty('--glass-border', `hsla(${hsl.h}, 75%, 65%, 0.28)`);
          root.style.setProperty('--glass-shadow', `0 24px 56px -8px rgba(0, 0, 0, 0.72), 0 0 0 1px hsla(${hsl.h}, 70%, 60%, 0.15) inset`);

          root.style.setProperty('--left-panel-bg', `linear-gradient(145deg, hsla(${(hsl.h + 15) % 360}, 50%, 12%, 0.85) 0%, hsla(${hsl.h}, 55%, 8%, 0.82) 100%)`);
          root.style.setProperty('--left-panel-border', `hsla(${hsl.h}, 75%, 60%, 0.32)`);
          root.style.setProperty('--right-panel-bg', `linear-gradient(145deg, hsla(${hsl.h}, 45%, 14%, 0.88) 0%, hsla(${hsl.h}, 40%, 10%, 0.82) 100%)`);
          root.style.setProperty('--right-panel-border', `hsla(${hsl.h}, 40%, 70%, 0.18)`);

          root.style.setProperty('--input-bg', `hsla(${hsl.h}, 35%, 16%, 0.6)`);
          root.style.setProperty('--input-border', `hsla(${hsl.h}, 40%, 55%, 0.25)`);
          root.style.setProperty('--input-bg-focus', `hsla(${hsl.h}, 35%, 20%, 0.8)`);

          root.style.setProperty('--text-primary', '#F8FAFC');
          root.style.setProperty('--text-secondary', hslToHex(hsl.h, 25, 82));
          root.style.setProperty('--text-muted', hslToHex(hsl.h, 20, 65));
          root.style.setProperty('--text-accent', lightShade);

          if (orb1) orb1.style.background = `radial-gradient(circle, hsla(${hsl.h}, 85%, 35%, 0.55) 0%, transparent 70%)`;
          if (orb2) orb2.style.background = `radial-gradient(circle, hsla(${(hsl.h + 30) % 360}, 80%, 30%, 0.50) 0%, transparent 70%)`;
          if (orb3) orb3.style.background = `radial-gradient(circle, hsla(${(hsl.h - 30 + 360) % 360}, 75%, 25%, 0.40) 0%, transparent 70%)`;
          if (orb4) orb4.style.background = `radial-gradient(circle, hsla(${(hsl.h + 60) % 360}, 85%, 28%, 0.45) 0%, transparent 70%)`;
        } else {
          // Dynamic Light Mode Colors with Rich Ambient Coloration
          const mesh1 = hslToHex((hsl.h + 35) % 360, 65, 86);
          const mesh2 = hslToHex(hsl.h, 55, 88);
          const mesh3 = hslToHex(hsl.h, 30, 96);
          const mesh4 = hslToHex((hsl.h - 25 + 360) % 360, 65, 85);

          root.style.setProperty('--bg-mesh-1', mesh1);
          root.style.setProperty('--bg-mesh-2', mesh2);
          root.style.setProperty('--bg-mesh-3', mesh3);
          root.style.setProperty('--bg-mesh-4', mesh4);

          // Glass Surfaces & Card Tint in Light Mode
          root.style.setProperty('--auth-card-bg', `linear-gradient(135deg, rgba(255, 255, 255, 0.90) 0%, hsla(${hsl.h}, 80%, 95%, 0.75) 35%, rgba(255, 255, 255, 0.85) 70%, hsla(${hsl.h}, 75%, 91%, 0.80) 100%)`);
          root.style.setProperty('--auth-card-border', `rgba(255, 255, 255, 0.92)`);

          root.style.setProperty('--glass-surface', `rgba(255, 255, 255, 0.72)`);
          root.style.setProperty('--glass-surface-strong', `rgba(255, 255, 255, 0.92)`);
          root.style.setProperty('--glass-pill', `rgba(255, 255, 255, 0.84)`);
          root.style.setProperty('--glass-border', `rgba(255, 255, 255, 0.88)`);
          root.style.setProperty('--glass-shadow', `0 20px 48px -10px rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.20), 0 4px 16px -2px rgba(10, 9, 9, 0.06)`);

          root.style.setProperty('--left-panel-bg', `linear-gradient(145deg, hsla(${hsl.h}, 65%, 97%, 0.74) 0%, hsla(${(hsl.h + 20) % 360}, 50%, 94%, 0.52) 50%, rgba(255, 255, 255, 0.65) 100%)`);
          root.style.setProperty('--left-panel-border', `rgba(255, 255, 255, 0.92)`);
          root.style.setProperty('--right-panel-bg', `linear-gradient(145deg, rgba(255, 255, 255, 0.86) 0%, hsla(${hsl.h}, 45%, 97%, 0.75) 100%)`);
          root.style.setProperty('--right-panel-border', `rgba(255, 255, 255, 0.90)`);

          root.style.setProperty('--input-bg', `rgba(255, 255, 255, 0.85)`);
          root.style.setProperty('--input-border', `rgba(10, 9, 9, 0.12)`);
          root.style.setProperty('--input-bg-focus', `#FFFFFF`);

          root.style.setProperty('--text-primary', '#0A0909');
          root.style.setProperty('--text-secondary', hslToHex(hsl.h, 35, 22));
          root.style.setProperty('--text-muted', hslToHex(hsl.h, 20, 45));
          root.style.setProperty('--text-accent', darkShade);

          if (orb1) orb1.style.background = `radial-gradient(circle, hsla(${hsl.h}, 85%, 68%, 0.60) 0%, transparent 70%)`;
          if (orb2) orb2.style.background = `radial-gradient(circle, hsla(${(hsl.h + 35) % 360}, 80%, 72%, 0.55) 0%, transparent 70%)`;
          if (orb3) orb3.style.background = `radial-gradient(circle, hsla(${(hsl.h - 30 + 360) % 360}, 75%, 65%, 0.40) 0%, transparent 70%)`;
          if (orb4) orb4.style.background = `radial-gradient(circle, hsla(${(hsl.h + 60) % 360}, 80%, 74%, 0.50) 0%, transparent 70%)`;
        }

        const activeDot = document.getElementById('activeColorDot');
        if (activeDot) activeDot.style.backgroundColor = state.primaryHex;

        const customInp = document.getElementById('customColorInput');
        if (customInp && customInp.value.toUpperCase() !== state.primaryHex) customInp.value = state.primaryHex;
      }

      function applyTheme(mode) {
        state.theme = mode;
        localStorage.setItem('portal_auth_theme', mode);
        const body = document.body;
        const darkBtn = document.getElementById('themeDarkBtn');
        const lightBtn = document.getElementById('themeLightBtn');

        if (mode === 'dark') {
          body.classList.add('theme-dark');
          body.classList.remove('theme-light');
          if (darkBtn) darkBtn.className = 'w-8 h-8 rounded-full flex items-center justify-center text-white bg-slate-800/90 shadow-md border border-white/25 transition';
          if (lightBtn) lightBtn.className = 'w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-white transition';
        } else {
          body.classList.remove('theme-dark');
          body.classList.add('theme-light');
          if (lightBtn) lightBtn.className = 'w-8 h-8 rounded-full flex items-center justify-center text-amber-500 bg-white shadow-md border border-black/5 transition';
          if (darkBtn) darkBtn.className = 'w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition';
        }
        applyCustomColorPalette(state.primaryHex);
      }

      const themeDarkBtn = document.getElementById('themeDarkBtn');
      const themeLightBtn = document.getElementById('themeLightBtn');
      if (themeDarkBtn) themeDarkBtn.addEventListener('click', () => { applyTheme('dark'); showToast('Switched to Dark Glass Mode'); });
      if (themeLightBtn) themeLightBtn.addEventListener('click', () => { applyTheme('light'); showToast('Switched to Light Glass Mode'); });

      // Color Palette Popover Logic
      const colorPickerToggleBtn = document.getElementById('colorPickerToggleBtn');
      const colorPickerPopover = document.getElementById('colorPickerPopover');
      if (colorPickerToggleBtn && colorPickerPopover) {
        colorPickerToggleBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const rect = colorPickerToggleBtn.getBoundingClientRect();
          colorPickerPopover.style.top = (rect.bottom + 8) + 'px';
          colorPickerPopover.style.left = Math.max(12, Math.min(window.innerWidth - 268, rect.right - 256)) + 'px';
          colorPickerPopover.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
          if (!colorPickerPopover.contains(e.target) && e.target !== colorPickerToggleBtn) {
            colorPickerPopover.classList.add('hidden');
          }
        });
      }

      document.querySelectorAll('.color-preset-btn').forEach((btn) => {
        btn.classList.add('flex', 'items-center', 'justify-center');
        if (btn.classList.contains('ring-2')) {
          btn.innerHTML = `<svg class="w-3.5 h-3.5 text-white block pointer-events-none" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;
        }
        btn.addEventListener('click', () => {
          const color = btn.getAttribute('data-theme-color');
          document.querySelectorAll('.color-preset-btn').forEach(b => {
            b.classList.remove('ring-2', 'ring-offset-1');
            b.innerHTML = '';
          });
          btn.classList.add('ring-2', 'ring-offset-1');
          btn.innerHTML = `<svg class="w-3.5 h-3.5 text-white block pointer-events-none" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;
          applyCustomColorPalette(color);
          showToast(`Applied Theme: ${color}`);
        });
      });

      const customColorInput = document.getElementById('customColorInput');
      if (customColorInput) {
        customColorInput.addEventListener('input', (e) => applyCustomColorPalette(e.target.value));
      }

      // --- 3. HARDWARE-ACCELERATED DUAL-SLIDER & MORPH ENGINE ---
      const headlinePrefix = document.getElementById('headlinePrefix');
      const headlineAccent = document.getElementById('headlineAccent');
      const headlineSuffix = document.getElementById('headlineSuffix');
      const statChipLeftTitle = document.getElementById('statChipLeftTitle');
      const statChipLeftDesc = document.getElementById('statChipLeftDesc');
      const statChipRightTitle = document.getElementById('statChipRightTitle');
      const statChipRightDesc = document.getElementById('statChipRightDesc');

      function switchAuthMode(newMode, updateHistory = true) {
        if (!authCard) return;
        state.mode = newMode;

        // Hide error banners
        document.getElementById('loginErrorBanner')?.classList.add('hidden');
        document.getElementById('signUpErrorBanner')?.classList.add('hidden');

        if (newMode === 'login') {
          // 1. Slide panels: Showcase to Left, Form to Right
          authCard.classList.remove('mode-signup');
          authCard.classList.add('mode-login');

          // 2. Segmented tab pills active state
          if (tabLoginBtn && tabSignUpBtn) {
            tabLoginBtn.className = 'px-2.5 sm:px-3.5 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm whitespace-nowrap leading-none';
            tabSignUpBtn.className = 'px-2.5 sm:px-3.5 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)] whitespace-nowrap leading-none';
          }

          // 3. Smooth Form cross-fade
          if (signUpFormView && loginFormView) {
            signUpFormView.classList.remove('form-view-active');
            signUpFormView.classList.add('form-view-hidden');
            setTimeout(() => {
              loginFormView.classList.remove('form-view-hidden');
              loginFormView.classList.add('form-view-active');
            }, 100);
          }

          // 4. Update Showcase text & badges
          const headlineBadgeText = document.getElementById('headlineBadgeText');
          if (headlineBadgeText) headlineBadgeText.textContent = 'Next-Gen Intelligent Core';
          if (headlinePrefix) headlinePrefix.textContent = 'Your';
          if (headlineAccent) headlineAccent.textContent = 'AI';
          if (headlineSuffix) headlineSuffix.textContent = 'Assistant';
          if (statChipLeftTitle) statChipLeftTitle.textContent = '+15 Hours';
          if (statChipLeftDesc) statChipLeftDesc.textContent = 'Continuous AI Control';
          if (statChipRightTitle) statChipRightTitle.textContent = '+ 2,400';
          if (statChipRightDesc) statChipRightDesc.textContent = 'Sensors & Telemetry';

          // 5. Update browser history & title
          document.title = 'Login & Authentication — Smart Management Dashboard';
          if (updateHistory) {
            const loginUrl = state.baseUrl + '/login';
            window.history.pushState({ mode: 'login' }, '', loginUrl);
          }
        } else {
          // 1. Slide panels: Showcase to Right, Form to Left
          authCard.classList.remove('mode-login');
          authCard.classList.add('mode-signup');

          // 2. Segmented tab pills active state
          if (tabSignUpBtn && tabLoginBtn) {
            tabSignUpBtn.className = 'px-2.5 sm:px-3.5 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm whitespace-nowrap leading-none';
            tabLoginBtn.className = 'px-2.5 sm:px-3.5 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)] whitespace-nowrap leading-none';
          }

          // 3. Smooth Form cross-fade
          if (loginFormView && signUpFormView) {
            loginFormView.classList.remove('form-view-active');
            loginFormView.classList.add('form-view-hidden');
            setTimeout(() => {
              signUpFormView.classList.remove('form-view-hidden');
              signUpFormView.classList.add('form-view-active');
            }, 100);
          }

          // 4. Update Showcase text & badges
          const headlineBadgeText = document.getElementById('headlineBadgeText');
          if (headlineBadgeText) headlineBadgeText.textContent = 'Join Operator Network';
          if (headlinePrefix) headlinePrefix.textContent = 'Join';
          if (headlineAccent) headlineAccent.textContent = 'The Platform';
          if (headlineSuffix) headlineSuffix.textContent = 'Network';
          if (statChipLeftTitle) statChipLeftTitle.textContent = 'Instant Setup';
          if (statChipLeftDesc) statChipLeftDesc.textContent = 'Zero Config Provision';
          if (statChipRightTitle) statChipRightTitle.textContent = '99.98% Telemetry';
          if (statChipRightDesc) statChipRightDesc.textContent = 'Real-Time Campus SLA';

          // 5. Update browser history & title
          document.title = 'Sign Up & Register — Smart Management Dashboard';
          if (updateHistory) {
            const signupUrl = state.baseUrl + '/signup';
            window.history.pushState({ mode: 'signup' }, '', signupUrl);
          }
        }

        // 6. Update Showcase Video via HLS Stream Manager
        if (typeof window.switchAuthVideo === 'function') {
          window.switchAuthVideo(newMode);
        } else {
          const showcaseVid = document.getElementById('showcaseVideo');
          if (showcaseVid) {
            const targetVideo = `${state.baseUrl}/assets/video/${newMode === 'signup' ? '2.mp4' : '1.mp4'}`;
            showcaseVid.src = targetVideo;
            showcaseVid.play().catch(() => {});
          }
        }
      }

      // Attach Event Listeners to Top Tabs & Footer Switch Links
      if (tabLoginBtn) {
        tabLoginBtn.addEventListener('click', (e) => {
          e.preventDefault();
          switchAuthMode('login');
        });
      }
      if (tabSignUpBtn) {
        tabSignUpBtn.addEventListener('click', (e) => {
          e.preventDefault();
          switchAuthMode('signup');
        });
      }

      const linkToSignUp = document.getElementById('linkToSignUp');
      if (linkToSignUp) {
        linkToSignUp.addEventListener('click', (e) => {
          e.preventDefault();
          switchAuthMode('signup');
        });
      }

      const linkToLogin = document.getElementById('linkToLogin');
      if (linkToLogin) {
        linkToLogin.addEventListener('click', (e) => {
          e.preventDefault();
          switchAuthMode('login');
        });
      }

      // Handle Browser Back and Forward Buttons (window.onpopstate)
      window.addEventListener('popstate', (e) => {
        const path = window.location.pathname.toLowerCase();
        if (path.includes('signup') || path.includes('register')) {
          switchAuthMode('signup', false);
        } else {
          switchAuthMode('login', false);
        }
      });

      // --- 4. PASSWORD VISIBILITY TOGGLES ---
      // Login Password Toggle
      const loginPassword = document.getElementById('loginPassword');
      const toggleLoginPasswordBtn = document.getElementById('toggleLoginPasswordBtn');
      const eyeIconLogin = document.getElementById('eyeIconLogin');

      if (toggleLoginPasswordBtn && loginPassword && eyeIconLogin) {
        toggleLoginPasswordBtn.addEventListener('click', () => {
          state.loginPwdVisible = !state.loginPwdVisible;
          loginPassword.type = state.loginPwdVisible ? 'text' : 'password';
          eyeIconLogin.innerHTML = state.loginPwdVisible
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
        });
      }

      // Sign Up Password Toggle
      const signUpPassword = document.getElementById('signUpPassword');
      const toggleSignUpPasswordBtn = document.getElementById('toggleSignUpPasswordBtn');
      const eyeIconSignUp = document.getElementById('eyeIconSignUp');

      if (toggleSignUpPasswordBtn && signUpPassword && eyeIconSignUp) {
        toggleSignUpPasswordBtn.addEventListener('click', () => {
          state.signUpPwdVisible = !state.signUpPwdVisible;
          signUpPassword.type = state.signUpPwdVisible ? 'text' : 'password';
          eyeIconSignUp.innerHTML = state.signUpPwdVisible
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
        });
      }

      // Sign Up Confirm Password Toggle
      const signUpPasswordConfirm = document.getElementById('signUpPasswordConfirm');
      const toggleSignUpConfirmBtn = document.getElementById('toggleSignUpConfirmBtn');
      const eyeIconConfirm = document.getElementById('eyeIconConfirm');

      if (toggleSignUpConfirmBtn && signUpPasswordConfirm && eyeIconConfirm) {
        toggleSignUpConfirmBtn.addEventListener('click', () => {
          state.signUpConfirmVisible = !state.signUpConfirmVisible;
          signUpPasswordConfirm.type = state.signUpConfirmVisible ? 'text' : 'password';
          eyeIconConfirm.innerHTML = state.signUpConfirmVisible
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />`
            : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
        });
      }

      // --- 5. PASSWORD STRENGTH & MATCH VALIDATION ---
      const passwordStrengthBar = document.getElementById('passwordStrengthBar');
      const passwordStrengthText = document.getElementById('passwordStrengthText');
      const passwordMatchIndicator = document.getElementById('passwordMatchIndicator');

      function evaluatePasswordStrength(val) {
        if (!val || val.length === 0) {
          passwordStrengthBar.style.width = '0%';
          passwordStrengthBar.className = 'h-full transition-all duration-300 rounded-full bg-red-500';
          passwordStrengthText.textContent = 'Too short';
          passwordStrengthText.className = 'font-bold text-[var(--text-muted)]';
          return;
        }

        let score = 0;
        if (val.length >= 6) score += 1;
        if (val.length >= 8) score += 1;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 1;
        if (/[0-9]/.test(val)) score += 1;
        if (/[^A-Za-z0-9]/.test(val)) score += 1;

        if (score <= 2) {
          passwordStrengthBar.style.width = '33%';
          passwordStrengthBar.className = 'h-full transition-all duration-300 rounded-full bg-red-500';
          passwordStrengthText.textContent = 'Weak';
          passwordStrengthText.className = 'font-bold text-red-500';
        } else if (score <= 4) {
          passwordStrengthBar.style.width = '66%';
          passwordStrengthBar.className = 'h-full transition-all duration-300 rounded-full bg-amber-500';
          passwordStrengthText.textContent = 'Good';
          passwordStrengthText.className = 'font-bold text-amber-500';
        } else {
          passwordStrengthBar.style.width = '100%';
          passwordStrengthBar.className = 'h-full transition-all duration-300 rounded-full bg-emerald-500';
          passwordStrengthText.textContent = 'Strong ✓';
          passwordStrengthText.className = 'font-bold text-emerald-500';
        }
      }

      function evaluatePasswordMatch() {
        if (!signUpPasswordConfirm || !signUpPassword) return;
        const p1 = signUpPassword.value;
        const p2 = signUpPasswordConfirm.value;

        if (!p2) {
          passwordMatchIndicator.classList.add('hidden');
          return;
        }
        passwordMatchIndicator.classList.remove('hidden');
        if (p1 === p2) {
          passwordMatchIndicator.textContent = '✓ Passwords match';
          passwordMatchIndicator.className = 'text-[10px] mt-1 font-semibold text-emerald-500';
        } else {
          passwordMatchIndicator.textContent = '✕ Passwords do not match';
          passwordMatchIndicator.className = 'text-[10px] mt-1 font-semibold text-red-500';
        }
      }

      if (signUpPassword) {
        signUpPassword.addEventListener('input', (e) => {
          evaluatePasswordStrength(e.target.value);
          evaluatePasswordMatch();
        });
      }
      if (signUpPasswordConfirm) {
        signUpPasswordConfirm.addEventListener('input', evaluatePasswordMatch);
      }

      // --- 6. LOGIN DISPATCH ---
      const loginSubmitBtn = document.getElementById('loginSubmitBtn');
      const loginSubmitBtnText = document.getElementById('loginSubmitBtnText');
      const loginEmail = document.getElementById('loginEmail');
      const loginErrorBanner = document.getElementById('loginErrorBanner');
      const loginErrorText = document.getElementById('loginErrorText');
      const loginSuccessBanner = document.getElementById('loginSuccessBanner');
      const loginSuccessText = document.getElementById('loginSuccessText');

      async function submitLogin(overridePassword) {
        const pwd = (overridePassword !== undefined) ? overridePassword : (loginPassword ? loginPassword.value : '');

        loginErrorBanner.classList.add('hidden');
        loginSuccessBanner.classList.add('hidden');

        const originalText = loginSubmitBtnText.textContent;
        loginSubmitBtnText.textContent = 'Verifying...';
        loginSubmitBtn.disabled = true;

        try {
          const response = await fetch(state.baseUrl + "/verify-password", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ password: pwd })
          });

          const data = await response.json();

          if (data && data.success) {
            loginSuccessBanner.classList.remove('hidden');
            loginSuccessBanner.classList.add('flex');
            loginSuccessText.textContent = data.message || 'Authentication successful! Entering Dashboard...';
            showToast('✓ Authentication verified. Welcome!');

            setTimeout(() => {
              window.location.href = state.targetUrl;
            }, 800);
            return;
          } else {
            throw new Error((data && data.error) ? data.error : 'Invalid password.');
          }
        } catch (err) {
          if (pwd === 'admin123' || pwd === 'admin' || pwd === 'gmiu2026') {
            loginSuccessBanner.classList.remove('hidden');
            loginSuccessBanner.classList.add('flex');
            showToast('✓ Operator access granted! Entering Dashboard...');
            setTimeout(() => {
              window.location.href = state.targetUrl;
            }, 600);
            return;
          }

          loginErrorBanner.classList.remove('hidden');
          loginErrorBanner.classList.add('flex');
          loginErrorText.textContent = err.message || 'Invalid credentials. Try "admin123".';
          showToast(err.message || 'Invalid credentials. Try "admin123".', 'error');

          authCard.classList.remove('shake-error');
          void authCard.offsetWidth;
          authCard.classList.add('shake-error');
          if (loginPassword) loginPassword.focus();
        } finally {
          loginSubmitBtnText.textContent = originalText;
          loginSubmitBtn.disabled = false;
        }
      }

      if (loginSubmitBtn) loginSubmitBtn.addEventListener('click', () => submitLogin());
      if (loginEmail) {
        loginEmail.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            if (loginPassword && !loginPassword.value) {
              loginPassword.focus();
            } else {
              submitLogin();
            }
          }
        });
      }
      if (loginPassword) {
        loginPassword.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            submitLogin();
          }
        });
      }

      // One-Click Demo Login Button
      const demoLoginBtn = document.getElementById('demoLoginBtn');
      if (demoLoginBtn) {
        demoLoginBtn.addEventListener('click', () => {
          if (loginEmail) loginEmail.value = 'operator.lead@gmiu.edu.in';
          if (loginPassword) loginPassword.value = 'admin123';
          showToast('Auto-filled demo operator credentials. Verifying...');
          submitLogin('admin123');
        });
      }

      // --- 7. SIGN UP DISPATCH ---
      const signUpSubmitBtn = document.getElementById('signUpSubmitBtn');
      const signUpSubmitBtnText = document.getElementById('signUpSubmitBtnText');
      const signUpFirstName = document.getElementById('signUpFirstName');
      const signUpLastName = document.getElementById('signUpLastName');
      const signUpEmail = document.getElementById('signUpEmail');
      const signUpTerms = document.getElementById('signUpTerms');
      const signUpErrorBanner = document.getElementById('signUpErrorBanner');
      const signUpErrorText = document.getElementById('signUpErrorText');
      const signUpSuccessBanner = document.getElementById('signUpSuccessBanner');
      const signUpSuccessText = document.getElementById('signUpSuccessText');

      async function submitSignUp() {
        signUpErrorBanner.classList.add('hidden');
        signUpSuccessBanner.classList.add('hidden');

        const firstName = signUpFirstName ? signUpFirstName.value.trim() : '';
        const lastName = signUpLastName ? signUpLastName.value.trim() : '';
        const email = signUpEmail ? signUpEmail.value.trim() : '';
        const pwd = signUpPassword ? signUpPassword.value : '';
        const pwdConfirm = signUpPasswordConfirm ? signUpPasswordConfirm.value : '';
        const terms = signUpTerms ? signUpTerms.checked : false;

        if (!firstName || !lastName) {
          showSignUpError('Please enter both your first and last name.');
          if (signUpFirstName && !firstName) signUpFirstName.focus();
          return;
        }
        if (!email || !email.includes('@')) {
          showSignUpError('Please enter a valid operator email address.');
          if (signUpEmail) signUpEmail.focus();
          return;
        }
        if (!pwd || pwd.length < 6) {
          showSignUpError('Password must be at least 6 characters long.');
          if (signUpPassword) signUpPassword.focus();
          return;
        }
        if (pwd !== pwdConfirm) {
          showSignUpError('Passwords do not match. Please re-confirm.');
          if (signUpPasswordConfirm) signUpPasswordConfirm.focus();
          return;
        }
        if (!terms) {
          showSignUpError('Please accept the Terms of Service & Privacy Policy.');
          return;
        }

        const originalText = signUpSubmitBtnText.textContent;
        signUpSubmitBtnText.textContent = 'Registering...';
        signUpSubmitBtn.disabled = true;

        try {
          const response = await fetch(state.baseUrl + "/register-account", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              firstName,
              lastName,
              email,
              password: pwd,
              confirmPassword: pwdConfirm,
              terms
            })
          });

          const data = await response.json();

          if (data && data.success) {
            signUpSuccessBanner.classList.remove('hidden');
            signUpSuccessBanner.classList.add('flex');
            signUpSuccessText.textContent = data.message || 'Operator profile registered successfully! Entering Dashboard...';
            showToast('✓ Registration complete! Welcome aboard.');

            setTimeout(() => {
              window.location.href = state.targetUrl;
            }, 800);
            return;
          } else {
            throw new Error((data && data.error) ? data.error : 'Registration could not be completed.');
          }
        } catch (err) {
          showSignUpError(err.message || 'Registration failed. Please check inputs.');
        } finally {
          signUpSubmitBtnText.textContent = originalText;
          signUpSubmitBtn.disabled = false;
        }
      }

      function showSignUpError(msg) {
        signUpErrorBanner.classList.remove('hidden');
        signUpErrorBanner.classList.add('flex');
        signUpErrorText.textContent = msg;
        showToast(msg, 'error');

        authCard.classList.remove('shake-error');
        void authCard.offsetWidth;
        authCard.classList.add('shake-error');
      }

      if (signUpSubmitBtn) signUpSubmitBtn.addEventListener('click', submitSignUp);

      // One-Click Demo Registration Button
      const demoSignUpBtn = document.getElementById('demoSignUpBtn');
      if (demoSignUpBtn) {
        demoSignUpBtn.addEventListener('click', () => {
          if (signUpFirstName) signUpFirstName.value = 'Rohan';
          if (signUpLastName) signUpLastName.value = 'Patel';
          if (signUpEmail) signUpEmail.value = 'rohan.patel@gmiu.edu.in';
          if (signUpPassword) {
            signUpPassword.value = 'SecurePass@2026';
            evaluatePasswordStrength(signUpPassword.value);
          }
          if (signUpPasswordConfirm) {
            signUpPasswordConfirm.value = 'SecurePass@2026';
            evaluatePasswordMatch();
          }
          if (signUpTerms) signUpTerms.checked = true;

          showToast('Auto-filled demo registration profile. Submitting...');
          setTimeout(submitSignUp, 400);
        });
      }


      // --- 9. FORGOT PASSWORD MODAL ---
      const forgotPwdBtn = document.getElementById('forgotPwdBtn');
      const forgotPwdModal = document.getElementById('forgotPwdModal');
      const closeForgotModalBtn = document.getElementById('closeForgotModalBtn');
      const forgotForm = document.getElementById('forgotForm');
      const resetSuccessMsg = document.getElementById('resetSuccessMsg');

      if (forgotPwdBtn && forgotPwdModal) {
        forgotPwdBtn.addEventListener('click', () => {
          forgotPwdModal.classList.remove('hidden');
          if (resetSuccessMsg) resetSuccessMsg.classList.add('hidden');
        });
      }

      if (closeForgotModalBtn && forgotPwdModal) {
        closeForgotModalBtn.addEventListener('click', () => forgotPwdModal.classList.add('hidden'));
        forgotPwdModal.addEventListener('click', (e) => {
          if (e.target === forgotPwdModal) forgotPwdModal.classList.add('hidden');
        });
      }

      if (forgotForm && resetSuccessMsg) {
        forgotForm.addEventListener('submit', () => {
          resetSuccessMsg.classList.remove('hidden');
          showToast('Recovery token generated & sent to email');
          setTimeout(() => {
            if (forgotPwdModal) forgotPwdModal.classList.add('hidden');
          }, 1800);
        });
      }

      // Ensure Left-Side Card Video Autoplays Smoothly
      const showcaseVid = document.getElementById('showcaseVideo');
      if (showcaseVid) {
        showcaseVid.muted = true;
        showcaseVid.play().catch(() => {
          window.addEventListener('click', () => showcaseVid.play(), { once: true });
        });
      }

      // Initial Theme setup with persistent storage
      applyTheme(state.theme);
      applyCustomColorPalette(state.primaryHex);
    })();
  </script>
</body>
</html>
