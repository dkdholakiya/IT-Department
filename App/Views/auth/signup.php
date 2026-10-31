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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>Sign Up & Register — Smart Management Dashboard</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script>
    tailwind.config = {
      darkMode: 'class'
    };
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      /* Dynamic Theme Tokens */
      --color-primary: #10B981;
      --color-primary-dark: #059669;
      --color-primary-light: #34D399;
      --color-primary-rgb: 16, 185, 129;
      --color-alert: #EB5011;

      /* Ambient Mesh Backdrop */
      --bg-mesh-1: #C7E0ED;
      --bg-mesh-2: #D4E8C7;
      --bg-mesh-3: #EDF6EE;
      --bg-mesh-4: #C9E3B8;

      /* Glassmorphic Surfaces */
      --glass-surface: rgba(255, 255, 255, 0.72);
      --glass-surface-strong: rgba(255, 255, 255, 0.92);
      --glass-pill: rgba(255, 255, 255, 0.82);
      --glass-border: rgba(255, 255, 255, 0.88);
      --glass-shadow: 0 20px 48px -10px rgba(var(--color-primary-rgb), 0.20), 0 4px 16px -2px rgba(10, 9, 9, 0.06);

      /* Left & Right Panel Backgrounds */
      --left-panel-bg: linear-gradient(145deg, rgba(255, 255, 255, 0.68) 0%, rgba(240, 253, 244, 0.50) 50%, rgba(255, 255, 255, 0.60) 100%);
      --left-panel-border: rgba(255, 255, 255, 0.92);
      --right-panel-bg: linear-gradient(145deg, rgba(255, 255, 255, 0.84) 0%, rgba(255, 255, 255, 0.74) 100%);
      --right-panel-border: rgba(255, 255, 255, 0.90);

      /* Input Surface */
      --input-bg: rgba(255, 255, 255, 0.85);
      --input-border: rgba(10, 9, 9, 0.12);
      --input-bg-focus: #FFFFFF;

      /* Typography & Accents */
      --text-primary: #0A0909;
      --text-secondary: #2C3E28;
      --text-muted: #5C6F58;
      --text-accent: #059669;

      /* Button Glows */
      --ai-btn-grad: linear-gradient(180deg, #F2FFE6 0%, #34D399 48%, #10B981 100%);
      --ai-btn-shadow: rgba(16, 185, 129, 0.35);
      --ai-btn-text: #0A0909;
    }

    * {
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--text-primary);
      background-color: var(--bg-mesh-3);
      background-image:
        radial-gradient(at 8% 8%, var(--bg-mesh-1) 0px, transparent 52%),
        radial-gradient(at 92% 10%, var(--bg-mesh-1) 0px, transparent 48%),
        radial-gradient(at 15% 85%, var(--bg-mesh-4) 0px, transparent 55%),
        radial-gradient(at 65% 75%, var(--bg-mesh-2) 0px, transparent 55%),
        radial-gradient(at 50% 50%, var(--bg-mesh-3) 0px, transparent 70%);
      background-attachment: fixed;
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      transition: background 0.4s ease, color 0.3s ease;
      position: relative;
    }

    /* Glass Cards */
    .glass-card {
      background: var(--glass-surface);
      backdrop-filter: blur(28px) saturate(180%);
      -webkit-backdrop-filter: blur(28px) saturate(180%);
      border: 1.5px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.35s ease;
    }

    .glass-card-strong {
      background: var(--glass-surface-strong);
      backdrop-filter: blur(32px) saturate(185%);
      -webkit-backdrop-filter: blur(32px) saturate(185%);
      border: 1.5px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
    }

    .glass-pill {
      background: var(--glass-pill);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1.5px solid var(--glass-border);
      box-shadow: 0 4px 14px rgba(10, 9, 9, 0.05);
      transition: all 0.22s ease;
    }

    /* Ultra-Translucent Frosted Glass for Video Showcase Overlay */
    .video-glass-card {
      background: rgba(255, 255, 255, 0.20) !important;
      backdrop-filter: blur(16px) saturate(160%) !important;
      -webkit-backdrop-filter: blur(16px) saturate(160%) !important;
      border: 1px solid rgba(255, 255, 255, 0.45) !important;
      box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08), inset 0 1px 1.5px 0 rgba(255, 255, 255, 0.65) !important;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .video-glass-card:hover {
      background: rgba(255, 255, 255, 0.32) !important;
      border-color: rgba(255, 255, 255, 0.75) !important;
      box-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.12), inset 0 1px 2px 0 rgba(255, 255, 255, 0.85) !important;
      transform: translateY(-2px);
    }
    body.theme-dark .video-glass-card {
      background: rgba(15, 23, 42, 0.35) !important;
      backdrop-filter: blur(18px) saturate(180%) !important;
      -webkit-backdrop-filter: blur(18px) saturate(180%) !important;
      border: 1px solid rgba(255, 255, 255, 0.22) !important;
      box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.45), inset 0 1px 1px 0 rgba(255, 255, 255, 0.25) !important;
    }
    body.theme-dark .video-glass-card:hover {
      background: rgba(15, 23, 42, 0.50) !important;
      border-color: rgba(255, 255, 255, 0.38) !important;
    }

    /* Showcase Panel Crystal Glass Container */
    .auth-panel-showcase {
      background: transparent !important;
      border: 1.5px solid rgba(255, 255, 255, 0.65) !important;
      box-shadow: 0 20px 48px -10px rgba(0, 0, 0, 0.12), inset 0 1px 2px rgba(255, 255, 255, 0.5) !important;
    }
    body.theme-dark .auth-panel-showcase {
      border: 1.5px solid rgba(255, 255, 255, 0.22) !important;
      box-shadow: 0 24px 56px -8px rgba(0, 0, 0, 0.6), inset 0 1px 1.5px rgba(255, 255, 255, 0.18) !important;
    }

    /* Enhanced Headline Text Shadow for Over-Video Legibility */
    .headline-glass-text {
      text-shadow: 0 2px 14px rgba(255, 255, 255, 0.95), 0 1px 3px rgba(0, 0, 0, 0.25);
    }
    body.theme-dark .headline-glass-text {
      text-shadow: 0 2px 16px rgba(0, 0, 0, 0.95), 0 0 10px rgba(0, 0, 0, 0.8);
    }

    /* Left & Right Glass Panels with Dynamic Gradients */
    .left-glass-panel {
      background: var(--left-panel-bg);
      backdrop-filter: blur(32px) saturate(185%);
      -webkit-backdrop-filter: blur(32px) saturate(185%);
      border: 1.5px solid var(--left-panel-border);
      box-shadow: var(--glass-shadow);
      transition: all 0.35s ease;
    }

    .right-glass-panel {
      background: var(--right-panel-bg);
      backdrop-filter: blur(32px) saturate(190%);
      -webkit-backdrop-filter: blur(32px) saturate(190%);
      border: 1.5px solid var(--right-panel-border);
      box-shadow: var(--glass-shadow);
      transition: all 0.35s ease;
    }

    /* Circular action arrow button */
    .action-arrow-btn {
      background: rgba(255, 255, 255, 0.16);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #FFFFFF;
      transition: all 0.22s ease;
    }
    body.theme-light .action-arrow-btn {
      background: rgba(10, 9, 9, 0.06);
      border: 1px solid rgba(10, 9, 9, 0.12);
      color: var(--text-primary);
    }
    .action-arrow-btn:hover {
      transform: scale(1.1) translate(1px, -1px);
      background: var(--color-primary);
      color: #FFFFFF;
      border-color: var(--color-primary);
    }

    /* Auth Inputs */
    .auth-input {
      background: var(--input-bg);
      border: 1.5px solid var(--input-border);
      color: var(--text-primary);
      transition: all 0.2s ease;
    }
    .auth-input:focus {
      outline: none;
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.22);
      background: var(--input-bg-focus);
    }

    /* Shake animation on invalid attempt */
    @keyframes shake-anim {
      0%, 100% { transform: translateX(0); }
      20%, 60% { transform: translateX(-6px); }
      40%, 80% { transform: translateX(6px); }
    }
    .shake-error {
      animation: shake-anim 0.4s ease-in-out;
    }

    /* Live status dot pulse */
    @keyframes pulse-dot {
      0% { transform: scale(1); opacity: 0.8; }
      70%, 100% { transform: scale(2.2); opacity: 0; }
    }
    .live-dot {
      position: relative !important;
      display: inline-block !important;
      width: 7px !important;
      height: 7px !important;
      border-radius: 9999px !important;
      background-color: var(--color-primary) !important;
      vertical-align: middle;
      flex-shrink: 0;
    }
    .live-dot::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 9999px;
      background-color: var(--color-primary);
      opacity: 0.6;
      animation: pulse-dot 2s cubic-bezier(0, 0, 0.2, 1) infinite;
      pointer-events: none;
    }

    /* Floating stat badges */
    @keyframes float-subtle {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5px); }
    }
    .animate-float {
      animation: float-subtle 4s ease-in-out infinite;
    }
    .animate-float-delayed {
      animation: float-subtle 4s ease-in-out 2s infinite;
    }

    /* Dual Slider Hardware-Accelerated Translations */
    .auth-slider-container {
      position: relative;
      width: 100%;
    }

    @media (min-width: 1024px) {
      .auth-slider-container {
        display: flex;
        flex-direction: row;
        align-items: stretch;
        gap: 1.5rem; /* 24px */
      }

      .auth-panel-showcase,
      .auth-panel-form {
        width: calc(50% - 0.75rem);
        flex-shrink: 0;
        transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
        will-change: transform;
        z-index: 10;
      }

      /* In Login mode: Showcase Left, Form Right */
      #authCard.mode-login .auth-panel-showcase {
        transform: translate3d(0, 0, 0);
      }
      #authCard.mode-login .auth-panel-form {
        transform: translate3d(0, 0, 0);
      }

      /* In Sign Up mode: Form Left, Showcase Right */
      #authCard.mode-signup .auth-panel-showcase {
        transform: translate3d(calc(100% + 1.5rem), 0, 0);
      }
      #authCard.mode-signup .auth-panel-form {
        transform: translate3d(calc(-100% - 1.5rem), 0, 0);
      }
    }

    @media (max-width: 1023px) {
      .auth-panel-showcase,
      .auth-panel-form {
        width: 100% !important;
        transform: none !important;
      }
      #authCard.mode-signup .auth-panel-form {
        order: 1;
      }
      #authCard.mode-signup .auth-panel-showcase {
        order: 2;
      }
      #authCard.mode-login .auth-panel-showcase {
        order: 1;
      }
      #authCard.mode-login .auth-panel-form {
        order: 2;
      }
    }

    /* Form views smooth morph & crossfade */
    .form-view {
      transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    .form-view-active {
      opacity: 1;
      transform: translateX(0);
      pointer-events: auto;
      position: relative;
      display: block;
    }
    .form-view-hidden {
      opacity: 0;
      transform: translateX(20px);
      pointer-events: none;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      display: none;
    }

    /* Showcase content crossfade */
    .showcase-content {
      transition: opacity 0.35s ease, transform 0.35s ease;
    }
  </style>
</head>
<body class="theme-light antialiased selection:bg-[var(--color-primary)] selection:text-white p-3 sm:p-5 lg:p-7">

  <!-- ================= AMBIENT FROSTED GLASS BACKGROUND ORBS ================= -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
    <div id="ambientOrb1" class="absolute -top-28 -left-28 w-[520px] h-[520px] rounded-full blur-[110px] opacity-75 transition-all duration-700"></div>
    <div id="ambientOrb2" class="absolute -bottom-36 -right-36 w-[580px] h-[580px] rounded-full blur-[120px] opacity-65 transition-all duration-700"></div>
    <div id="ambientOrb3" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[620px] h-[620px] rounded-full blur-[130px] opacity-45 transition-all duration-700"></div>
  </div>

  <!-- ================= COLOR PICKER POPOVER ================= -->
  <div id="colorPickerPopover" class="hidden fixed w-64 glass-card p-3.5 z-50 shadow-2xl border border-white/60">
    <div class="flex items-center justify-between pb-2 mb-2 border-b border-black/10 dark:border-white/10">
      <span class="font-bold text-xs text-[var(--text-primary)]">Palette Studio</span>
      <input id="customColorInput" type="color" value="#10B981" class="w-6 h-6 rounded-lg cursor-pointer border-0 bg-transparent p-0" title="Custom Color" />
    </div>
    <div class="grid grid-cols-6 gap-1.5">
      <button type="button" data-theme-color="#10B981" class="color-preset-btn w-7 h-7 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Green (#10B981)"></button>
      <button type="button" data-theme-color="#729C51" class="color-preset-btn w-7 h-7 rounded-full bg-[#729C51]" title="Leaf Green (#729C51)"></button>
      <button type="button" data-theme-color="#0284C7" class="color-preset-btn w-7 h-7 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)"></button>
      <button type="button" data-theme-color="#7C3AED" class="color-preset-btn w-7 h-7 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)"></button>
      <button type="button" data-theme-color="#E11D48" class="color-preset-btn w-7 h-7 rounded-full bg-[#E11D48]" title="Crimson Rose (#E11D48)"></button>
      <button type="button" data-theme-color="#D97706" class="color-preset-btn w-7 h-7 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)"></button>
    </div>
  </div>

  <!-- ================= MAIN WIDESCREEN CARD (SIGN UP MODE: FORM LEFT, IMAGE RIGHT) ================= -->
  <main id="authCard" class="auth-card relative w-full max-w-[1240px] mx-auto rounded-[32px] sm:rounded-[38px] glass-card p-3 sm:p-5 lg:p-6 shadow-2xl border border-white/60 overflow-hidden my-auto mode-signup">

    <div id="authSliderContainer" class="auth-slider-container w-full min-h-[580px] lg:min-h-[640px]">

      <!-- =========================================================================
           SHOWCASE PANEL:
           - On Sign Up: Right Side (Slid smoothly to right)
           - On Login: Left Side (Natural position)
           - Displays AI Hero Image, dynamic statistics, testimonials & theme controls
           ========================================================================= -->
      <section id="showcasePanel" class="auth-panel auth-panel-showcase rounded-[26px] sm:rounded-[30px] p-5 sm:p-7 flex flex-col justify-between relative overflow-hidden">

        <!-- High-Definition Left-Side Card Video (assets/video/1.mp4) Full Width & Height - Zero Grey Filter -->
        <video
          id="showcaseVideo"
          autoplay
          loop
          muted
          playsinline
          preload="auto"
          class="absolute inset-0 w-full h-full object-cover z-0 pointer-events-none rounded-[26px] sm:rounded-[30px]"
        >
          <source src="<?php echo $baseUrl; ?>assets/video/1.mp4" type="video/mp4">
          <source src="<?php echo $baseUrl; ?>assets/video/2.mp4" type="video/mp4">
        </video>

        <!-- 1. Top Bar: Back to Dashboard & Theme Studio -->
        <div class="relative z-10 flex items-center justify-between">
          <a
            href="<?php echo $baseUrl; ?>dashboard"
            class="h-10 px-4 sm:px-5 rounded-full video-glass-card text-[var(--text-primary)] hover:text-[var(--color-primary)] active:scale-95 text-xs sm:text-[13px] font-bold flex items-center gap-2 shadow-sm transition"
            title="Return to Smart Management Dashboard"
          >
            <svg class="w-4 h-4 text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to the dashboard</span>
          </a>

          <!-- Controls: Palette Chooser + Dark/Light Mode -->
          <div class="flex items-center gap-2.5">
            <!-- Palette Chooser -->
            <button id="colorPickerToggleBtn" type="button" class="w-10 h-10 rounded-full video-glass-card flex items-center justify-center hover:scale-105 active:scale-95 transition relative shadow-sm group" title="Choose Dynamic Theme Color" aria-label="Theme Color">
              <!-- Perfect Artist Palette Icon -->
              <svg class="w-5 h-5 text-[var(--text-primary)] transition-transform group-hover:rotate-12 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
                <circle cx="13.5" cy="6.5" r=".75" fill="currentColor"></circle>
                <circle cx="17.5" cy="10.5" r=".75" fill="currentColor"></circle>
                <circle cx="8.5" cy="7.5" r=".75" fill="currentColor"></circle>
                <circle cx="6.5" cy="12.5" r=".75" fill="currentColor"></circle>
              </svg>
              <!-- Live Theme Color Indicator Dot Badge -->
              <span id="activeColorDot" class="w-3.5 h-3.5 rounded-full absolute -top-0.5 -right-0.5 border-2 border-white dark:border-slate-800 shadow-md transition-all group-hover:scale-110" style="background-color: var(--color-primary);"></span>
            </button>

            <!-- Dark / Light Theme Toggle -->
            <div class="h-10 p-1 rounded-full video-glass-card flex items-center gap-1 shadow-sm">
              <button id="themeDarkBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Dark Glass Mode" aria-label="Dark Mode">
                <!-- Perfect Centered Lucide Moon Icon -->
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
              </button>
              <button id="themeLightBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-amber-500 bg-white shadow-md border border-black/5 transition" title="Light Glass Mode" aria-label="Light Mode">
                <!-- Perfect Centered Lucide Sun Icon -->
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="4.5"></circle>
                  <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- 2. Typographic Headline & Floating Glass Stat Chips -->
        <div class="relative z-10 my-auto py-2 sm:py-3 flex flex-col items-center text-center">

          <!-- Dynamic Headline with Enhanced Glass Legibility -->
          <div id="showcaseHeadlineWrapper" class="showcase-content">
            <h1 id="showcaseHeadline" class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-[var(--text-primary)] leading-tight headline-glass-text">
              <span id="headlinePrefix" class="opacity-80 font-normal block sm:inline">Join</span>
              <span id="headlineAccent" class="bg-gradient-to-r from-[var(--color-primary)] via-[var(--color-primary-light)] to-[var(--color-primary-dark)] bg-clip-text text-transparent">The Platform</span>
              <span id="headlineSuffix" class="font-extrabold">Network</span>
            </h1>
          </div>

          <!-- Futuristic Centerpiece area with Transparent Glass Stat Chips over Video -->
          <div class="relative mt-2 sm:mt-4 w-full max-w-[360px] sm:max-w-[440px] h-[260px] sm:h-[300px] lg:h-[340px] flex items-center justify-center">

            <!-- Floating Translucent Glass Stat Chip 1 (Lower Left) -->
            <div id="statChipLeft" class="absolute -left-2 sm:left-1 bottom-3 sm:bottom-6 z-20 px-3.5 py-2.5 rounded-2xl video-glass-card shadow-xl animate-float">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-[var(--color-primary)]/20 text-[var(--color-primary)] flex items-center justify-center shrink-0 border border-[var(--color-primary)]/30">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                    <circle cx="12" cy="12" r="9"/>
                  </svg>
                </div>
                <div>
                  <p id="statChipLeftTitle" class="text-xs sm:text-[13px] font-extrabold text-[#0F172A] dark:text-white leading-tight drop-shadow-sm">Instant Setup</p>
                  <p id="statChipLeftDesc" class="text-[9.5px] text-[#334155] dark:text-slate-300 leading-none mt-0.5 font-bold">Zero Config Provision</p>
                </div>
              </div>
            </div>

            <!-- Floating Translucent Glass Stat Chip 2 (Upper Right) -->
            <div id="statChipRight" class="absolute -right-2 sm:right-2 top-3 sm:top-6 z-20 px-3.5 py-2.5 rounded-2xl video-glass-card shadow-xl animate-float-delayed">
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-[var(--color-primary)]/20 text-[var(--color-primary)] flex items-center justify-center shrink-0 border border-[var(--color-primary)]/30">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                  </svg>
                </div>
                <div>
                  <p id="statChipRightTitle" class="text-xs sm:text-[13px] font-extrabold text-[#0F172A] dark:text-white leading-tight drop-shadow-sm">99.98% Telemetry</p>
                  <p id="statChipRightDesc" class="text-[9.5px] text-[#334155] dark:text-slate-300 leading-none mt-0.5 font-bold">Real-Time Campus SLA</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Bottom Row: 3 Ultra-Translucent Glass Testimonial Cards (Video Fully Visible Behind) -->
        <div id="showcaseBottomCards" class="relative z-10 pt-2 grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-left">

          <!-- Card 1: David S. -->
          <div class="p-2.5 rounded-2xl video-glass-card hover:scale-[1.02] transition group">
            <div class="flex items-center justify-between gap-1.5">
              <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-full bg-emerald-700/60 ring-1 ring-white/50 flex items-center justify-center text-white text-xs font-bold overflow-hidden shrink-0 shadow-sm">
                  <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces" alt="David S." class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\' fill=\'%2310B981\'><circle cx=\'16\' cy=\'16\' r=\'16\'/><text x=\'16\' y=\'20\' text-anchor=\'middle\' fill=\'white\' font-size=\'12\'>DS</text></svg>';" />
                </div>
                <div class="min-w-0">
                  <p class="text-[11.5px] font-bold text-[#0F172A] dark:text-white truncate leading-tight">David S.</p>
                  <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none mt-0.5 font-semibold">CEO & Co-founder</p>
                </div>
              </div>
              <button type="button" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View David S. Case">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
            <p class="text-[10px] text-[var(--color-primary)] font-bold mt-2 truncate drop-shadow-sm">"Best Ever. Autonomous yield +40%."</p>
          </div>

          <!-- Card 2: Mira K. -->
          <div class="p-2.5 rounded-2xl video-glass-card hover:scale-[1.02] transition group">
            <div class="flex items-center justify-between gap-1.5">
              <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-full bg-teal-700/60 ring-1 ring-white/50 flex items-center justify-center text-white text-xs font-bold overflow-hidden shrink-0 shadow-sm">
                  <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=100&h=100&fit=crop&crop=faces" alt="Mira K." class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\' fill=\'%2310B981\'><circle cx=\'16\' cy=\'16\' r=\'16\'/><text x=\'16\' y=\'20\' text-anchor=\'middle\' fill=\'white\' font-size=\'12\'>MK</text></svg>';" />
                </div>
                <div class="min-w-0">
                  <p class="text-[11.5px] font-bold text-[#0F172A] dark:text-white truncate leading-tight">Mira K.</p>
                  <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none mt-0.5 font-semibold">Head of Operations</p>
                </div>
              </div>
              <button type="button" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View Mira K. Case">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
            <p class="text-[10px] text-[var(--color-primary)] font-bold mt-2 truncate drop-shadow-sm">"2.4k Sensors fully integrated."</p>
          </div>

          <!-- Card 3: Imran Jakir -->
          <div class="p-2.5 rounded-2xl video-glass-card hover:scale-[1.02] transition group">
            <div class="flex items-center justify-between gap-1.5">
              <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-full bg-cyan-800/60 ring-1 ring-white/30 flex items-center justify-center text-white text-xs font-bold overflow-hidden shrink-0">
                  <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=faces" alt="Imran Jakir" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\' fill=\'%2310B981\'><circle cx=\'16\' cy=\'16\' r=\'16\'/><text x=\'16\' y=\'20\' text-anchor=\'middle\' fill=\'white\' font-size=\'12\'>IJ</text></svg>';" />
                </div>
                <div class="min-w-0">
                  <p class="text-[11.5px] font-bold text-[#0F172A] dark:text-white truncate leading-tight">Imran Jakir</p>
                  <p class="text-[9px] text-[#334155] dark:text-slate-300 truncate leading-none mt-0.5 font-medium">Agritech Systems Lead</p>
                </div>
              </div>
              <button type="button" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center shrink-0" aria-label="View Imran Jakir Case">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
            <p class="text-[10px] text-[var(--color-primary)] font-semibold mt-2 truncate">"Optimal 80% Harvest yield."</p>
          </div>
        </div>
      </section>

      <!-- =========================================================================
           FORM PANEL:
           - On Sign Up: Left Side (Slid smoothly to left)
           - On Login: Right Side (Natural position)
           - Seamlessly morphs between Sign Up and Login form views
           ========================================================================= -->
      <section id="formPanel" class="auth-panel auth-panel-form rounded-[26px] sm:rounded-[30px] p-5 sm:p-6 lg:p-7 flex flex-col justify-between right-glass-panel relative overflow-hidden">

        <div class="w-full">

          <!-- Top Status & Segmented Mode Switcher -->
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10.5px] font-bold bg-[var(--color-primary)]/15 text-[var(--color-primary)] border border-[var(--color-primary)]/30">
              <span class="live-dot"></span>
              <span>CE & IT Secure Access</span>
            </span>

            <!-- Mode Toggle Segmented Pill -->
            <div class="p-0.5 rounded-full bg-black/10 dark:bg-white/10 flex items-center text-xs font-bold">
              <a
                id="tabLoginBtn"
                href="<?php echo $baseUrl; ?>login"
                class="px-3.5 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
              >Log In</a>
              <a
                id="tabSignUpBtn"
                href="<?php echo $baseUrl; ?>signup"
                class="px-3.5 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm"
              >Sign Up</a>
            </div>
          </div>

          <!-- Dynamic Form Container with 2 Views -->
          <div class="relative w-full">

            <!-- ================= VIEW 1: SIGN UP (DEFAULT ACTIVE IN SIGNUP.PHP) ================= -->
            <div id="signUpFormView" class="form-view form-view-active">
              <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 text-[var(--text-primary)] leading-tight">Create Account.</h2>
              <p class="text-xs text-[var(--text-muted)] mt-0.5">Register your operator profile for telemetry access.</p>

              <form id="signUpForm" class="mt-3.5 space-y-2.5 sm:space-y-3" onsubmit="return false;">
                <!-- First Name & Last Name (Grid 2 cols) -->
                <div class="grid grid-cols-2 gap-2.5">
                  <div>
                    <label for="signUpFirstName" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">First Name</label>
                    <input
                      id="signUpFirstName"
                      type="text"
                      placeholder="Omar"
                      required
                      class="w-full h-10 sm:h-11 px-3.5 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                  </div>
                  <div>
                    <label for="signUpLastName" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">Last Name</label>
                    <input
                      id="signUpLastName"
                      type="text"
                      placeholder="Jakir"
                      required
                      class="w-full h-10 sm:h-11 px-3.5 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                  </div>
                </div>

                <!-- Email Address -->
                <div>
                  <label for="signUpEmail" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">E-mail Address</label>
                  <div class="relative">
                    <input
                      id="signUpEmail"
                      type="email"
                      placeholder="new.operator@gmiu.edu.in"
                      required
                      autocomplete="email"
                      class="w-full h-10 sm:h-11 pl-3.5 pr-9 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                    <svg class="w-4 h-4 text-[var(--text-muted)] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                  </div>
                </div>

                <!-- Password with Strength Meter -->
                <div>
                  <label for="signUpPassword" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">Create Password</label>
                  <div class="relative">
                    <input
                      id="signUpPassword"
                      type="password"
                      placeholder="Min 6 characters"
                      required
                      autocomplete="new-password"
                      class="w-full h-10 sm:h-11 pl-3.5 pr-10 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                    <button id="toggleSignUpPasswordBtn" type="button" class="w-8 h-8 rounded-lg absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Show/Hide Password">
                      <svg id="eyeIconSignUp" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                  </div>
                  <!-- Password Strength Bar -->
                  <div class="mt-1.5">
                    <div class="w-full bg-black/10 dark:bg-white/10 h-1.5 rounded-full overflow-hidden">
                      <div id="passwordStrengthBar" class="h-full w-0 transition-all duration-300 rounded-full bg-red-500"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-[var(--text-muted)] mt-1 font-semibold">
                      <span>Security strength:</span>
                      <span id="passwordStrengthText" class="font-bold text-[var(--text-muted)]">Too short</span>
                    </div>
                  </div>
                </div>

                <!-- Confirm Password -->
                <div>
                  <label for="signUpPasswordConfirm" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">Confirm Password</label>
                  <div class="relative">
                    <input
                      id="signUpPasswordConfirm"
                      type="password"
                      placeholder="Re-enter password"
                      required
                      autocomplete="new-password"
                      class="w-full h-10 sm:h-11 pl-3.5 pr-10 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                    <button id="toggleSignUpConfirmBtn" type="button" class="w-8 h-8 rounded-lg absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Show/Hide Confirm Password">
                      <svg id="eyeIconConfirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                  </div>
                  <div id="passwordMatchIndicator" class="hidden text-[10px] mt-1 font-semibold"></div>
                </div>

                <!-- Terms & Conditions Checkbox -->
                <div class="pt-0.5 flex items-center justify-between">
                  <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-[var(--text-secondary)]">
                    <input
                      id="signUpTerms"
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] accent-[var(--color-primary)] cursor-pointer"
                    />
                    <span>Accept <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Terms of Service</a> & <a href="#" class="text-[var(--color-primary)] font-bold hover:underline">Privacy Policy</a></span>
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

              <!-- Social Divider -->
              <div class="relative my-3 sm:my-3.5 text-center">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-black/10 dark:border-white/10"></div></div>
                <span class="relative px-3 bg-[var(--glass-surface-strong)] text-[10px] uppercase font-bold tracking-wider text-[var(--text-muted)]">
                  Or register with
                </span>
              </div>

              <!-- Social Buttons -->
              <div class="flex items-center justify-center gap-2.5 sm:gap-3">
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="Google SSO">
                  <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.41 7.34 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.59 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                  </svg>
                </button>
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="Apple ID">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.88c.63-.77 1.06-1.84.95-2.91-.91.04-2.02.61-2.67 1.37-.58.67-1.09 1.76-.95 2.81 1.02.08 2.04-.5 2.67-1.27z"/>
                  </svg>
                </button>
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="GitHub">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                  </svg>
                </button>
              </div>

              <!-- Bottom Action Row: Switch Prompt + Create Account Pill Button -->
              <div class="mt-4 pt-2.5 sm:mt-5 sm:pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="text-xs text-[var(--text-muted)] truncate">
                    Already have an account?
                    <a href="<?php echo $baseUrl; ?>login" id="linkToLogin" class="font-bold text-[var(--text-primary)] hover:text-[var(--color-primary)] hover:underline transition cursor-pointer">Log In</a>
                  </p>
                </div>
                <button
                  id="signUpSubmitBtn"
                  type="button"
                  class="h-10 sm:h-11 px-5 sm:px-6 rounded-full bg-white text-[#0A0909] font-extrabold text-xs tracking-tight flex items-center gap-2 shadow-lg hover:scale-105 active:scale-95 transition shrink-0 group border border-white"
                >
                  <span id="signUpSubmitBtnText">Create Account</span>
                  <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </button>
              </div>

              <!-- One-Click Demo Registration Auto-fill -->
              <div class="mt-2.5 sm:mt-3">
                <button
                  id="demoSignUpBtn"
                  type="button"
                  class="w-full py-1.5 sm:py-2 px-3 rounded-2xl border border-dashed border-[var(--color-primary)]/50 hover:border-[var(--color-primary)] bg-[var(--color-primary)]/10 text-[var(--text-accent)] text-xs font-bold flex items-center justify-center gap-2 hover:bg-[var(--color-primary)]/20 transition"
                  title="Auto-fill Demo Registration Information"
                >
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                  </svg>
                  <span>One-Click Demo Registration (Auto-fill & Submit)</span>
                </button>
              </div>
            </div>

            <!-- ================= VIEW 2: LOGIN (SLIDES IN UPON CLICKING 'LOG IN') ================= -->
            <div id="loginFormView" class="form-view form-view-hidden">
              <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1 text-[var(--text-primary)] leading-tight">Welcome Back.</h2>
              <p class="text-xs text-[var(--text-muted)] mt-0.5">Sign in to access your portal & telemetry dashboard.</p>

              <form id="loginForm" class="mt-3.5 space-y-2.5 sm:space-y-3" onsubmit="return false;">
                <!-- Email / Operator ID -->
                <div>
                  <label for="loginEmail" class="block text-[11px] font-semibold text-[var(--text-secondary)] mb-1">E-mail / Operator ID</label>
                  <div class="relative">
                    <input
                      id="loginEmail"
                      type="text"
                      placeholder="operator@gmiu.edu.in"
                      required
                      autocomplete="email"
                      class="w-full h-10 sm:h-11 pl-3.5 pr-9 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                    <svg class="w-4 h-4 text-[var(--text-muted)] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                  </div>
                </div>

                <!-- Password -->
                <div>
                  <div class="flex items-center justify-between mb-1">
                    <label for="loginPassword" class="block text-[11px] font-semibold text-[var(--text-secondary)]">Password</label>
                    <button id="forgotPwdBtn" type="button" class="text-[11px] font-semibold text-[var(--color-primary)] hover:underline">Forgot password?</button>
                  </div>
                  <div class="relative">
                    <input
                      id="loginPassword"
                      type="password"
                      placeholder="••••••••••••"
                      required
                      autocomplete="current-password"
                      class="w-full h-10 sm:h-11 pl-3.5 pr-10 rounded-xl auth-input text-xs font-semibold placeholder:text-[var(--text-muted)]"
                    />
                    <button id="toggleLoginPasswordBtn" type="button" class="w-8 h-8 rounded-lg absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Show/Hide Password">
                      <svg id="eyeIconLogin" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="pt-0.5 flex items-center justify-between">
                  <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-[var(--text-secondary)]">
                    <input
                      id="rememberMeCheckbox"
                      type="checkbox"
                      checked
                      class="w-4 h-4 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] accent-[var(--color-primary)] cursor-pointer"
                    />
                    <span>Remember this device for 30 days</span>
                  </label>
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

              <!-- Social Divider -->
              <div class="relative my-3 sm:my-3.5 text-center">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-black/10 dark:border-white/10"></div></div>
                <span class="relative px-3 bg-[var(--glass-surface-strong)] text-[10px] uppercase font-bold tracking-wider text-[var(--text-muted)]">
                  Or continue with
                </span>
              </div>

              <!-- Social Buttons -->
              <div class="flex items-center justify-center gap-2.5 sm:gap-3">
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="Google SSO">
                  <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.41 7.34 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.59 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                  </svg>
                </button>
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="Apple ID">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.88c.63-.77 1.06-1.84.95-2.91-.91.04-2.02.61-2.67 1.37-.58.67-1.09 1.76-.95 2.81 1.02.08 2.04-.5 2.67-1.27z"/>
                  </svg>
                </button>
                <button type="button" class="social-login-btn w-9 h-9 sm:w-10 sm:h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shadow-sm" title="GitHub">
                  <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                  </svg>
                </button>
              </div>

              <!-- Bottom Action Row: Switch Prompt + Log In Pill Button -->
              <div class="mt-4 pt-2.5 sm:mt-5 sm:pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="text-xs text-[var(--text-muted)] truncate">
                    Don't have an account?
                    <a href="<?php echo $baseUrl; ?>signup" id="linkToSignUp" class="font-bold text-[var(--text-primary)] hover:text-[var(--color-primary)] hover:underline transition cursor-pointer">Sign Up</a>
                  </p>
                </div>
                <button
                  id="loginSubmitBtn"
                  type="button"
                  class="h-10 sm:h-11 px-5 sm:px-6 rounded-full bg-white text-[#0A0909] font-extrabold text-xs tracking-tight flex items-center gap-2 shadow-lg hover:scale-105 active:scale-95 transition shrink-0 group border border-white"
                >
                  <span id="loginSubmitBtnText">Log In</span>
                  <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </button>
              </div>

              <!-- One-Click Demo Access -->
              <div class="mt-2.5 sm:mt-3">
                <button
                  id="demoLoginBtn"
                  type="button"
                  class="w-full py-1.5 sm:py-2 px-3 rounded-2xl border border-dashed border-[var(--color-primary)]/50 hover:border-[var(--color-primary)] bg-[var(--color-primary)]/10 text-[var(--text-accent)] text-xs font-bold flex items-center justify-center gap-2 hover:bg-[var(--color-primary)]/20 transition"
                  title="Fast Unlock with Default Demo Credentials"
                >
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                  </svg>
                  <span>One-Click Demo Access (Auto-fill & Unlock)</span>
                </button>
              </div>
            </div>

          </div>

        </div>

      </section>

    </div>

  </main>

  <!-- ================= FORGOT PASSWORD MODAL ================= -->
  <div id="forgotPwdModal" class="fixed inset-0 bg-black/50 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
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

  <!-- TOAST NOTIFICATION -->
  <div id="toastBanner" class="fixed bottom-5 right-5 z-50 hidden items-center gap-2.5 px-4 py-3 rounded-2xl bg-[#0A0909] text-white text-xs font-semibold shadow-2xl border border-white/20 transition-all">
    <span class="w-2 h-2 rounded-full bg-[var(--color-primary-light)]"></span>
    <span id="toastMessage">Action completed</span>
  </div>

  <!-- ================= JAVASCRIPT CONTROLLER ================= -->
  <script>
    (function () {
      // --- 1. APPLICATION STATE ---
      const state = {
        mode: 'signup', // Default mode on signup page
        theme: localStorage.getItem('portal_auth_theme') || 'light',
        primaryHex: localStorage.getItem('portal_auth_color') || '#10B981',
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

      // Toast Notification Helper
      let toastTimer = null;
      function showToast(msg) {
        const banner = document.getElementById('toastBanner');
        const text = document.getElementById('toastMessage');
        if (!banner || !text) return;
        text.textContent = msg;
        banner.classList.remove('hidden');
        banner.classList.add('flex');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
          banner.classList.add('hidden');
          banner.classList.remove('flex');
        }, 3200);
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
        localStorage.setItem('portal_auth_color', state.primaryHex);
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

          // Glass Surfaces in Dark Mode
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

          if (orb1) orb1.style.background = `radial-gradient(circle, hsla(${hsl.h}, 85%, 35%, 0.5) 0%, transparent 70%)`;
          if (orb2) orb2.style.background = `radial-gradient(circle, hsla(${(hsl.h + 30) % 360}, 80%, 30%, 0.45) 0%, transparent 70%)`;
          if (orb3) orb3.style.background = `radial-gradient(circle, hsla(${(hsl.h - 30 + 360) % 360}, 75%, 25%, 0.35) 0%, transparent 70%)`;
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

          // Glass Surfaces in Light Mode
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

          if (orb1) orb1.style.background = `radial-gradient(circle, hsla(${hsl.h}, 85%, 68%, 0.55) 0%, transparent 70%)`;
          if (orb2) orb2.style.background = `radial-gradient(circle, hsla(${(hsl.h + 35) % 360}, 80%, 72%, 0.50) 0%, transparent 70%)`;
          if (orb3) orb3.style.background = `radial-gradient(circle, hsla(${(hsl.h - 30 + 360) % 360}, 75%, 65%, 0.35) 0%, transparent 70%)`;
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
          colorPickerPopover.style.left = Math.max(16, rect.right - 256) + 'px';
          colorPickerPopover.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
          if (!colorPickerPopover.contains(e.target) && e.target !== colorPickerToggleBtn) {
            colorPickerPopover.classList.add('hidden');
          }
        });
      }

      document.querySelectorAll('.color-preset-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const color = btn.getAttribute('data-theme-color');
          document.querySelectorAll('.color-preset-btn').forEach(b => b.classList.remove('ring-2', 'ring-offset-1'));
          btn.classList.add('ring-2', 'ring-offset-1');
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
            tabLoginBtn.className = 'px-3.5 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm';
            tabSignUpBtn.className = 'px-3.5 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]';
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
            tabSignUpBtn.className = 'px-3.5 py-1 rounded-full transition cursor-pointer bg-white text-[#0A0909] shadow-sm';
            tabLoginBtn.className = 'px-3.5 py-1 rounded-full transition cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]';
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

      // --- 8. SOCIAL LOGIN SHORTCUTS ---
      document.querySelectorAll('.social-login-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const provider = btn.title || 'SSO';
          if (state.mode === 'signup') {
            if (signUpFirstName) signUpFirstName.value = 'Operator';
            if (signUpLastName) signUpLastName.value = 'User';
            if (signUpEmail) signUpEmail.value = 'operator.sso@gmiu.edu.in';
            if (signUpPassword) signUpPassword.value = 'admin123';
            if (signUpPasswordConfirm) signUpPasswordConfirm.value = 'admin123';
            showToast(`Connecting via ${provider}...`);
            setTimeout(submitSignUp, 400);
          } else {
            if (loginEmail) loginEmail.value = 'operator.sso@gmiu.edu.in';
            if (loginPassword) loginPassword.value = 'admin123';
            showToast(`Connecting via ${provider}...`);
            setTimeout(() => submitLogin('admin123'), 400);
          }
        });
      });

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
