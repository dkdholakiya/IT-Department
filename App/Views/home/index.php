<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CE & IT Department — Interactive 3D Portal | Made by DK Dholakiya</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= base_url('assets/css/font.css') ?>">
  <style>
    :root {
      --accent-start: #9b3dff;
      --accent-mid: #6e6bff;
      --accent-end: #2ca9ff;
      --cursor-color: #7c4dff;
      --cursor-bg: rgba(124, 77, 255, 0.08);
      --btn-active-bg: #111115;
    }

    body.theme-green {
      --accent-start: #4fa845;
      --accent-mid: #57b84c;
      --accent-end: #76d26b;
      --cursor-color: #55b34a;
      --cursor-bg: rgba(85, 179, 74, 0.16);
    }

    body.theme-angry {
      --accent-start: #ff4b1f;
      --accent-mid: #ff6a2b;
      --accent-end: #ff9045;
      --cursor-color: #ff5126;
      --cursor-bg: rgba(255, 81, 38, 0.14);
    }

    * {
      box-sizing: border-box;
      user-select: none;
      -webkit-user-select: none;
      cursor: none !important;
    }

    /* ── High-Contrast Neon Dark Theme (Default) + Seamless Light Theme Support ── */
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      font-family: 'PT Serif', Georgia, 'Times New Roman', serif;
      background: #09090f;
      color: #e8e8f4;
      transition: background-color 0.4s ease, color 0.4s ease;
    }

    body.light-mode {
      background: #ffffff;
      color: #16161a;
    }

    /* Fullscreen Background Canvas Wrapper */
    #tile-bg-wrapper {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
      background: #09090f;
      transition: background-color 0.4s ease;
    }

    body.light-mode #tile-bg-wrapper {
      background: #ffffff;
    }

    /* 2D Canvas for Flat-top Neon Hex Grid (Dark) or Frosted Cushion Hex Grid (Light) */
    #hex-canvas {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      display: block;
    }

    /* Central Radial Bloom — Purple/Magenta Core in Dark, Pastel AI Glow in Light */
    #hex-center-bloom {
      position: absolute;
      inset: 0;
      pointer-events: none;
      background:
        radial-gradient(ellipse 52% 46% at 50% 52%,
          rgba(180, 55, 255, 0.62) 0%,
          rgba(135, 45, 235, 0.32) 34%,
          transparent 68%),
        radial-gradient(ellipse 84% 70% at 50% 52%,
          rgba(80, 160, 255, 0.18) 0%,
          transparent 65%);
      animation: bloomPulse 4.8s ease-in-out infinite;
      transition: transform 0.4s ease, filter 0.5s ease, opacity 0.4s ease;
    }

    body.light-mode #hex-center-bloom {
      background:
        radial-gradient(ellipse 55% 45% at 50% 52%,
          rgba(168, 85, 247, 0.15) 0%,
          rgba(59, 130, 246, 0.08) 38%,
          transparent 68%);
      filter: blur(28px);
      opacity: 0.85;
      animation: none;
    }

    body.light-mode.theme-green #hex-center-bloom {
      background:
        radial-gradient(ellipse 55% 45% at 50% 52%,
          rgba(34, 197, 94, 0.18) 0%,
          rgba(16, 185, 129, 0.08) 38%,
          transparent 68%);
    }

    body.light-mode.theme-angry #hex-center-bloom {
      background:
        radial-gradient(ellipse 55% 45% at 50% 52%,
          rgba(249, 115, 22, 0.18) 0%,
          rgba(239, 68, 68, 0.08) 38%,
          transparent 68%);
    }

    body.theme-green #hex-center-bloom {
      background:
        radial-gradient(ellipse 52% 46% at 50% 52%,
          rgba(50, 210, 100, 0.55) 0%,
          rgba(40, 175, 80, 0.28) 34%,
          transparent 68%),
        radial-gradient(ellipse 84% 70% at 50% 52%,
          rgba(80, 225, 120, 0.14) 0%,
          transparent 65%);
    }

    body.theme-angry #hex-center-bloom {
      background:
        radial-gradient(ellipse 52% 46% at 50% 52%,
          rgba(255, 75, 20, 0.60) 0%,
          rgba(220, 50, 15, 0.30) 34%,
          transparent 68%),
        radial-gradient(ellipse 84% 70% at 50% 52%,
          rgba(255, 120, 60, 0.16) 0%,
          transparent 65%);
    }

    @keyframes bloomPulse {
      0%, 100% { opacity: 0.88; }
      50%       { opacity: 1.00; }
    }

    /* Edge Vignette (active in dark mode for cinematic focus on Orbii) */
    #hex-vignette {
      position: absolute;
      inset: 0;
      pointer-events: none;
      background:
        radial-gradient(ellipse 100% 40% at 50% 0%,   rgba(0,0,8,0.96) 0%, transparent 60%),
        radial-gradient(ellipse 100% 40% at 50% 100%, rgba(0,0,8,0.98) 0%, transparent 60%),
        radial-gradient(ellipse 28% 100% at 0% 50%,   rgba(0,0,8,0.75) 0%, transparent 65%),
        radial-gradient(ellipse 28% 100% at 100% 50%, rgba(0,0,8,0.75) 0%, transparent 65%);
      transition: opacity 0.4s ease;
    }

    body.light-mode #hex-vignette {
      opacity: 0;
    }

    /* Theme-Adaptive Brand Titles */
    .brand-title {
      color: #ffffff;
      text-shadow: 0 0 28px rgba(140, 80, 255, 0.55);
      transition: color 0.3s ease, text-shadow 0.3s ease;
    }
    body.light-mode .brand-title {
      color: #111115;
      text-shadow: none;
    }

    .brand-subtitle {
      color: #a77eff;
      transition: color 0.3s ease;
    }
    body.light-mode .brand-subtitle {
      color: #7c3dff;
    }

    /* Theme-Adaptive Action Buttons */
    #top-login-btn {
      border: 1px solid rgba(140, 90, 255, 0.45);
      background: rgba(255, 255, 255, 0.06);
      color: #d8daf0;
      backdrop-filter: blur(10px);
      transition: all 0.25s ease;
    }
    #top-login-btn:hover {
      border-color: rgba(140, 90, 255, 0.85);
      background: rgba(140, 90, 255, 0.16);
      color: #ffffff;
    }
    body.light-mode #top-login-btn {
      border: 1px solid rgba(200, 205, 220, 0.85);
      background: rgba(255, 255, 255, 0.85);
      color: #18181c;
    }
    body.light-mode #top-login-btn:hover {
      background: #ffffff;
      border-color: #a0a5b8;
    }

    /* Theme Mode Toggle Button */
    #theme-toggle-btn {
      border: 1px solid rgba(140, 90, 255, 0.4);
      background: rgba(255, 255, 255, 0.06);
      color: #d8daf0;
      backdrop-filter: blur(10px);
      cursor: pointer;
      transition: all 0.25s ease;
    }
    #theme-toggle-btn:hover {
      background: rgba(140, 90, 255, 0.2);
      border-color: rgba(140, 90, 255, 0.8);
      transform: scale(1.05);
    }
    body.light-mode #theme-toggle-btn {
      border: 1px solid rgba(200, 205, 220, 0.85);
      background: rgba(255, 255, 255, 0.85);
      color: #18181c;
    }
    body.light-mode #theme-toggle-btn:hover {
      background: #ffffff;
      border-color: #a0a5b8;
    }

    /* CTA Adopt / Dashboard Button */
    .adopt-btn {
      background: linear-gradient(135deg, #7c3dff 0%, #2ca9ff 100%);
      color: #ffffff;
      box-shadow: 0 4px 22px rgba(100, 50, 255, 0.45);
      transition: background 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                  transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                  box-shadow 0.28s ease;
    }

    .adopt-btn.hovered-green,
    .adopt-btn:hover {
      background: linear-gradient(135deg, #4fa845 0%, #76d26b 100%) !important;
      box-shadow: 0 10px 32px rgba(79, 168, 69, 0.52) !important;
      transform: translateY(-1px);
    }

    /* Bottom Signature Badge */
    .credit-badge-el, #credit-badge-el {
      font-size: 11px;
      color: rgba(190, 185, 230, 0.80);
      background: rgba(255, 255, 255, 0.055);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 5px 12px;
      border: 1px solid rgba(140, 80, 255, 0.25);
      box-shadow: 0 2px 18px rgba(100, 50, 255, 0.14);
      transition: all 0.3s ease;
    }
    body.light-mode .credit-badge-el,
    body.light-mode #credit-badge-el {
      color: #555562;
      background: rgba(255, 255, 255, 0.88);
      border: 1px solid rgba(210, 215, 230, 0.85);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    }

    /* Mobile Bottom Action Dock (Down side buttons) */
    .mobile-dock {
      background: rgba(13, 14, 24, 0.78);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(140, 90, 255, 0.32);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 24px rgba(124, 61, 255, 0.18);
      transition: all 0.3s ease;
    }
    body.light-mode .mobile-dock {
      background: rgba(255, 255, 255, 0.90);
      border: 1px solid rgba(215, 222, 240, 0.92);
      box-shadow: 0 8px 24px rgba(130, 140, 175, 0.18);
    }

    /* Mobile Login Button Style */
    .mobile-login-btn {
      border: 1px solid rgba(140, 90, 255, 0.45);
      background: rgba(255, 255, 255, 0.07);
      color: #e2e4ff;
      backdrop-filter: blur(10px);
      transition: all 0.25s ease;
    }
    .mobile-login-btn:hover {
      border-color: rgba(140, 90, 255, 0.85);
      background: rgba(140, 90, 255, 0.18);
      color: #ffffff;
    }
    body.light-mode .mobile-login-btn {
      border: 1px solid rgba(200, 205, 220, 0.85);
      background: rgba(246, 248, 254, 0.9);
      color: #18181c;
    }
    body.light-mode .mobile-login-btn:hover {
      background: #ffffff;
      border-color: #a0a5b8;
    }

    /* Disable custom cursor on touch devices to ensure native responsive touch */
    @media (hover: none) and (pointer: coarse) {
      #custom-cursor { display: none !important; }
    }

    /* Dynamic Gradient Text ("A little too curious.") */
    .curious-gradient-text {
      background: linear-gradient(90deg, var(--accent-start) 0%, var(--accent-mid) 48%, var(--accent-end) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      transition: filter 0.3s ease;
    }

    /* Custom AI / Interactive Cursor */
    #custom-cursor {
      position: fixed;
      top: 0;
      left: 0;
      width: 44px;
      height: 44px;
      border-radius: 9999px;
      border: 1.6px solid var(--cursor-color);
      background: var(--cursor-bg);
      pointer-events: none;
      z-index: 9999;
      transform: translate(-50%, -50%);
      transition: width 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                  height 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                  border-color 0.25s ease,
                  background-color 0.25s ease,
                  opacity 0.25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
    }

    #custom-cursor-dot {
      width: 5px;
      height: 5px;
      border-radius: 9999px;
      background: var(--cursor-color);
      transition: transform 0.2s ease, background-color 0.25s ease, opacity 0.2s ease;
    }

    #custom-cursor.cursor-cta-hover {
      width: 74px;
      height: 74px;
      border-color: rgba(85, 179, 74, 0.55);
      background: rgba(85, 179, 74, 0.14);
    }

    #custom-cursor.cursor-angry-poke {
      width: 34px;
      height: 34px;
      border-width: 2.2px;
      border-color: #ff4b1f;
      background: rgba(255, 75, 31, 0.22);
    }

    /* CTA Buttons */
    .adopt-btn {
      background-color: #0f0f13;
      color: #ffffff;
      transition: background-color 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                  transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                  box-shadow 0.28s ease;
    }

    .adopt-btn.hovered-green,
    .adopt-btn:hover {
      background-color: #4fa845 !important;
      box-shadow: 0 10px 28px rgba(79, 168, 69, 0.32);
      transform: translateY(-1px);
    }

    /* Navigation Pill */
    .nav-pill {
      padding: 8px 20px;
      border-radius: 9999px;
      font-size: 0.92rem;
      font-weight: 500;
      color: #25252b;
      border: 1px solid transparent;
      transition: all 0.22s ease;
      cursor: pointer;
      background: transparent;
    }

    .nav-pill.active {
      border-color: #52525b;
      font-weight: 600;
      color: #111115;
    }

    .nav-pill.nav-hovered,
    .nav-pill:hover {
      background: rgba(0, 0, 0, 0.04);
    }

    /* Bottom-right Neumorphic Card */
    .status-widget-card {
      background: linear-gradient(145deg, rgba(247, 247, 250, 0.92), rgba(235, 236, 242, 0.88));
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.85);
      box-shadow:
        12px 18px 38px rgba(180, 182, 195, 0.42),
        -8px -8px 24px rgba(255, 255, 255, 0.9),
        inset 0 1px 2px rgba(255, 255, 255, 0.95);
      border-radius: 26px;
      transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                  opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* Mood Swings Gradient Box */
    .mood-gradient-box {
      border-radius: 14px;
      background: linear-gradient(125deg,
        rgba(240, 210, 255, 0.9) 0%,
        rgba(148, 82, 255, 0.92) 38%,
        rgba(56, 169, 255, 0.9) 76%,
        rgba(225, 242, 255, 0.92) 100%);
      background-size: 160% 160%;
      animation: moodShimmer 5s ease infinite;
      transition: background 0.35s ease;
    }

    body.theme-green .mood-gradient-box {
      background: linear-gradient(125deg,
        rgba(225, 246, 222, 0.95) 0%,
        rgba(96, 186, 86, 0.88) 45%,
        rgba(150, 219, 140, 0.85) 78%,
        rgba(235, 250, 232, 0.95) 100%);
    }

    body.theme-angry .mood-gradient-box {
      background: linear-gradient(125deg,
        rgba(255, 228, 215, 0.95) 0%,
        rgba(255, 94, 43, 0.88) 45%,
        rgba(255, 152, 82, 0.85) 78%,
        rgba(255, 238, 228, 0.95) 100%);
    }

    @keyframes moodShimmer {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    /* Focus Level Gauge */
    .focus-well {
      background: linear-gradient(145deg, #ececf2, #f8f8fc);
      box-shadow:
        6px 8px 18px rgba(185, 188, 202, 0.45),
        -6px -6px 16px rgba(255, 255, 255, 0.95),
        inset 0 1px 2px rgba(255, 255, 255, 0.9);
    }

    /* Entrance / Page Reset Transition (00:27 in video) */
    .hero-transition-item {
      transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1),
                  opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }

    body.page-resetting .hero-left-col {
      opacity: 0;
      transform: translateX(-36px);
    }

    body.page-resetting .status-widget-card {
      opacity: 0;
      transform: translateY(42px) scale(0.95);
    }

    /* Eye Contact Bars */
    .eye-bar {
      width: 11px;
      border-radius: 9999px;
      transition: height 0.52s cubic-bezier(0.22, 1, 0.36, 1),
                  background 0.3s ease;
    }
  </style>
</head>
<body class="relative w-full h-full select-none overflow-hidden">

  <!-- Custom Circular AI / Interactive Cursor -->
  <div id="custom-cursor" aria-hidden="true">
    <div id="custom-cursor-dot"></div>
  </div>

  <!-- Neon AI Hexagonal Grid Background System (Canvas 2D + Glow Bloom + Vignette) -->
  <div id="tile-bg-wrapper" aria-hidden="true">
    <canvas id="hex-canvas"></canvas>
    <div id="hex-center-bloom"></div>
    <div id="hex-vignette"></div>
  </div>

  <!-- 3D Three.js Scene Container (3D Orbii Robot) -->
  <div id="canvas-container" class="fixed inset-0 w-full h-full z-[5]"></div>

  <!-- Top Navigation Header (Fully Responsive on All Screen Sizes) -->
  <header class="relative z-20 w-full max-w-[1440px] mx-auto px-4 sm:px-8 md:px-12 pt-4 sm:pt-6 md:pt-8 flex items-center justify-between">
    <!-- Brand Logo -->
    <button id="brand-logo" type="button" class="flex items-center gap-2 sm:gap-2.5 group focus:outline-none">
      <div class="relative w-8 h-6 sm:w-10 sm:h-7 flex items-center justify-center shrink-0">
        <svg viewBox="0 0 46 32" class="w-8 h-6 sm:w-10 sm:h-7 overflow-visible">
          <defs>
            <linearGradient id="logoRimGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop id="logoStop1" offset="0%" stop-color="#9b3dff" />
              <stop id="logoStop2" offset="100%" stop-color="#2ca9ff" />
            </linearGradient>
          </defs>
          <!-- Outer white shell -->
          <rect x="1" y="1" width="44" height="30" rx="15" fill="#f3f3f7" stroke="#e2e2ea" stroke-width="1" />
          <!-- Dynamic colored rim -->
          <rect x="4" y="4" width="38" height="24" rx="12" fill="url(#logoRimGrad)" />
          <!-- Black visor -->
          <rect x="6.8" y="6.8" width="32.4" height="18.4" rx="9.2" fill="#0d0d12" />
          <!-- Two white eyes -->
          <rect id="logoEyeL" x="14.5" y="11" width="4.8" height="9.6" rx="2.4" fill="#ffffff" />
          <rect id="logoEyeR" x="26.7" y="11" width="4.8" height="9.6" rx="2.4" fill="#ffffff" />
        </svg>
      </div>
      <div class="flex flex-col text-left">
        <span class="text-lg sm:text-xl md:text-[1.55rem] font-bold tracking-tight leading-none pt-serif-bold brand-title">Engineering</span>
        <span class="text-[9px] sm:text-[10px] md:text-[11px] font-bold tracking-wider uppercase pt-serif-regular mt-0.5 brand-subtitle">Department of IT & CE</span>
      </div>
    </button>

    <!-- Top-Right Action Buttons: Theme Toggle & Desktop Action Buttons -->
    <div class="flex items-center gap-2 sm:gap-2.5">
      <!-- Theme Switcher (Dark / Light) -->
      <button id="theme-toggle-btn" type="button" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition focus:outline-none" title="Switch Theme (Dark / Light)" aria-label="Toggle Theme">
        <!-- Sun Icon (shown in Dark mode) -->
        <svg id="theme-sun-icon" class="w-4 h-4 sm:w-5 sm:h-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1" x2="12" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
          <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/>
          <line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
          <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <!-- Moon Icon (shown in Light mode) -->
        <svg id="theme-moon-icon" class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>

      <!-- Desktop Header Buttons (Perfect on Desktop screens md+) -->
      <div class="hidden md:flex items-center gap-2.5">
        <a href="<?= base_url('login') ?>" id="top-login-btn" class="px-5 py-2 rounded-full text-sm font-semibold tracking-wide transition shadow-xs">
          Login
        </a>
        <a href="<?= base_url('dashboard') ?>" id="top-adopt-btn" class="adopt-btn inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold tracking-wide shadow-sm">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="9" rx="1"/>
            <rect x="14" y="3" width="7" height="5" rx="1"/>
            <rect x="14" y="12" width="7" height="9" rx="1"/>
            <rect x="3" y="16" width="7" height="5" rx="1"/>
          </svg>
          <span>Dashboard</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Interactive Adopt Modal / Notification Toast -->
  <div id="adopt-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/25 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-7 shadow-2xl border border-gray-100 text-center relative">
      <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-tr from-purple-600 to-sky-400 flex items-center justify-center text-white text-2xl shadow-md">
        🤖
      </div>
      <h2 id="modal-title" class="text-2xl font-bold text-[#141418] pt-serif-bold">Department of IT & CE — Engineering Portal</h2>
      <p id="modal-desc" class="mt-2 text-sm text-[#60606c] leading-relaxed">
        Welcome to the official IT Department portal featuring the interactive 3D robot companion. Access the management dashboard, academic timetables, and department reports. Made by DK Dholakiya.
      </p>
      <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
        <a href="<?= base_url('login') ?>" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-purple-600 to-sky-500 text-white text-sm font-semibold hover:opacity-90 transition shadow-md">
          Login / Sign In
        </a>
        <button id="close-modal-btn" type="button" class="px-5 py-2.5 rounded-full border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-100 transition">
          Close
        </button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      // ==========================================
      // 1. STATE & CONFIGURATION
      // ==========================================
      const VIDEO_DURATION = 31.833;
      let isAiDemoMode = false;
      let demoStartTime = performance.now();
      let currentVideoSec = 0;

      // Normalized Cursor Position (0..1 across viewport)
      const cursorState = {
        x: 0.28,
        y: 0.16,
        targetX: 0.28,
        targetY: 0.16,
        theme: 'default', // 'default' | 'green' | 'angry'
        activeSlide: 0,   // 0: Mood swings, 1: Focus level, 2: Eye contact
        hoverTarget: null // 'top-cta' | 'hero-cta' | 'nav-tricks' | 'angry-visor' | null
      };

      // Color Palettes for Smooth 3D Shader Interpolation
      const PALETTES = {
        default: {
          c1: new THREE.Color('#9a34ff'),
          c2: new THREE.Color('#27adff'),
          logo1: '#9b3dff',
          logo2: '#2ca9ff'
        },
        green: {
          c1: new THREE.Color('#47a43e'),
          c2: new THREE.Color('#6bd45f'),
          logo1: '#4fa845',
          logo2: '#70d666'
        },
        angry: {
          c1: new THREE.Color('#ff3b0f'),
          c2: new THREE.Color('#ff8533'),
          logo1: '#ff4b1f',
          logo2: '#ff8533'
        }
      };

      // Current interpolated shader colors & expressions
      const animState = {
        rimColor1: PALETTES.default.c1.clone(),
        rimColor2: PALETTES.default.c2.clone(),
        angryAmount: 0,       // 0 = normal cute eyes, 1 = slanted angry eyes
        targetReticle: 0,     // 0..1 opacity of orange target circle between eyes
        blinkAmount: 0,       // 0..1 eyelid blink
        entranceOffsetY: 0,   // Drops negative during 00:27 reset animation
        headRotX: 0,
        headRotY: 0,
        headRotZ: 0,
        eyeShiftX: 0,
        eyeShiftY: 0
      };

      // ==========================================
      // 2. DOM ELEMENTS
      // ==========================================
      const customCursor = document.getElementById('custom-cursor');
      const topAdoptBtn = document.getElementById('top-adopt-btn');
      const mobileAdoptBtn = document.getElementById('mobile-adopt-btn');
      const mobileLoginBtn = document.getElementById('mobile-login-btn');
      const heroAdoptBtn = document.getElementById('hero-adopt-btn');
      const navTricksBtn = document.getElementById('nav-tricks');
      const navPills = document.querySelectorAll('.nav-pill');
      const widgetCard = document.getElementById('widget-card');
      const widgetTitle = document.getElementById('widget-title');
      const slideMood = document.getElementById('slide-mood');
      const slideFocus = document.getElementById('slide-focus');
      const slideEye = document.getElementById('slide-eye');
      const slideDots = document.querySelectorAll('[data-slide-dot]');
      const eyeBars = document.querySelectorAll('.eye-bar');
      const focusRingCircle = document.getElementById('focus-ring-circle');
      const focusSemicircle = document.getElementById('focus-semicircle');
      const logoStop1 = document.getElementById('logoStop1');
      const logoStop2 = document.getElementById('logoStop2');
      const focusStop1 = document.getElementById('focusStop1');
      const focusStop2 = document.getElementById('focusStop2');
      const focusStop3 = document.getElementById('focusStop3');
                                          const adoptModal = document.getElementById('adopt-modal');
      const modalTitle = document.getElementById('modal-title');
      const modalDesc = document.getElementById('modal-desc');
      const closeModalBtn = document.getElementById('close-modal-btn');
      const brandLogo = document.getElementById('brand-logo');
      const heroSubtext = document.getElementById('hero-subtext');

      // ==========================================
      // 3. THREE.JS 3D SCENE (BACKGROUND HEX GRID + ORBII ROBOT)
      // ==========================================
      const container = document.getElementById('canvas-container');
      const scene = new THREE.Scene();

      const camera = new THREE.PerspectiveCamera(
        34,
        window.innerWidth / window.innerHeight,
        0.1,
        100
      );
      camera.position.set(0, 0, 13.5);

      const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
      renderer.setSize(window.innerWidth, window.innerHeight);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
      renderer.shadowMap.enabled = true;
      renderer.shadowMap.type = THREE.PCFSoftShadowMap;
      container.appendChild(renderer.domElement);

      // Tuned Studio Lighting — High contrast studio highlights for dark & light stage
      const ambientLight = new THREE.AmbientLight(0xffffff, 0.78);
      scene.add(ambientLight);

      const keyLight = new THREE.DirectionalLight(0xffffff, 0.82);
      keyLight.position.set(4, 7, 9);
      keyLight.castShadow = true;
      keyLight.shadow.mapSize.width = 1024;
      keyLight.shadow.mapSize.height = 1024;
      keyLight.shadow.camera.near = 0.5;
      keyLight.shadow.camera.far = 25;
      scene.add(keyLight);

      // Accent Rim Light (Violet/Purple behind the robot)
      const rimLight = new THREE.DirectionalLight(0x9b3dff, 0.45);
      rimLight.position.set(-5, 2, -6);
      scene.add(rimLight);

      // Accent Fill Light (Cyan from lower right)
      const fillLight = new THREE.DirectionalLight(0x2ca9ff, 0.32);
      fillLight.position.set(6, -3, 5);
      scene.add(fillLight);

      // ------------------------------------------
      // 3A. Neumorphic 3D Hexagonal Tile Background + Floating Mini Spheres
      // ------------------------------------------
      const bgGroup = new THREE.Group();
      bgGroup.position.set(0, 0.1, -3.2);
      bgGroup.visible = false; // Replaced by the stepped frosted 3D square tile background
      scene.add(bgGroup);

      // Hexagonal card background rendered via SVG pattern

      const hexMaterial = new THREE.MeshStandardMaterial({
        color: 0xf3f3f7,
        roughness: 0.72,
        metalness: 0.03
      });

      // Create rounded-looking cylinder hex tiles arranged in Honeycomb pattern
      const hexRadius = 0.92;
      const hexGapX = 1.72;
      const hexGapY = 1.5;
      const hexTiles = [];

      for (let row = -3; row <= 3; row++) {
        for (let col = -3; col <= 3; col++) {
          const dist = Math.hypot(col, row);
          if (dist > 3.2) continue;
          const hexGeo = new THREE.CylinderGeometry(hexRadius, hexRadius * 1.02, 0.24, 6);
          const hexMesh = new THREE.Mesh(hexGeo, hexMaterial.clone());
          hexMesh.rotation.x = Math.PI / 2;
          // Rotate 30 deg so flat/point orientation matches video honeycomb
          hexMesh.rotation.y = Math.PI / 6;
          const offsetX = (row % 2 !== 0) ? hexGapX * 0.5 : 0;
          const x = col * hexGapX + offsetX;
          const y = row * hexGapY;
          const zBase = -dist * 0.06;
          hexMesh.position.set(x, y, zBase);
          hexMesh.receiveShadow = true;
          bgGroup.add(hexMesh);
          hexTiles.push({ mesh: hexMesh, baseZ: zBase, phase: dist * 0.9 + col * 0.4 });
        }
      }

      // Small Floating White Spheres around the hex grid (exact match to video)
      const spherePositions = [
        { x: -3.2, y: 2.2, z: 0.35, r: 0.15 },
        { x: 2.7, y: 2.8, z: 0.25, r: 0.11 },
        { x: -2.6, y: -2.1, z: 0.4, r: 0.14 },
        { x: 3.1, y: -0.7, z: 0.3, r: 0.17 },
        { x: 2.4, y: -2.3, z: 0.3, r: 0.12 },
        { x: -3.8, y: -0.3, z: 0.2, r: 0.1 }
      ];
      const floatingDots = [];
      const dotMat = new THREE.MeshStandardMaterial({
        color: 0xf7f7fb,
        roughness: 0.35,
        metalness: 0.05
      });

      spherePositions.forEach((p, idx) => {
        const g = new THREE.SphereGeometry(p.r, 24, 24);
        const m = new THREE.Mesh(g, dotMat);
        m.position.set(p.x, p.y, p.z);
        bgGroup.add(m);
        floatingDots.push({ mesh: m, baseY: p.y, phase: idx * 1.3 });
      });

      // Soft Floor Shadow Disc directly below Orbii (adapts to dark and light mode)
      const shadowCanvas = document.createElement('canvas');
      shadowCanvas.width = 256;
      shadowCanvas.height = 256;
      const sCtx = shadowCanvas.getContext('2d');
      let shadowTex = null;

      function updateShadowTexture(isLight) {
        sCtx.clearRect(0, 0, 256, 256);
        const sGrad = sCtx.createRadialGradient(128, 128, 12, 128, 128, 116);
        if (isLight) {
          sGrad.addColorStop(0, 'rgba(110, 115, 145, 0.30)');
          sGrad.addColorStop(0.5, 'rgba(135, 140, 170, 0.12)');
          sGrad.addColorStop(1, 'rgba(135, 140, 170, 0)');
        } else {
          sGrad.addColorStop(0, 'rgba(120, 50, 220, 0.52)');
          sGrad.addColorStop(0.48, 'rgba(90, 40, 180, 0.20)');
          sGrad.addColorStop(1, 'rgba(60, 20, 120, 0)');
        }
        sCtx.fillStyle = sGrad;
        sCtx.fillRect(0, 0, 256, 256);
        if (shadowTex) shadowTex.needsUpdate = true;
      }

      updateShadowTexture(document.body.classList.contains('light-mode'));
      shadowTex = new THREE.CanvasTexture(shadowCanvas);
      const shadowPlane = new THREE.Mesh(
        new THREE.PlaneGeometry(3.8, 1.15),
        new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, depthWrite: false })
      );
      shadowPlane.position.set(0, -2.35, -0.5);
      scene.add(shadowPlane);

      // ------------------------------------------
      // 3B. 3D ORBII ROBOT HEAD
      // ------------------------------------------
      const robotRoot = new THREE.Group();
      scene.add(robotRoot);

      const robotHeadGroup = new THREE.Group();
      robotRoot.add(robotHeadGroup);

      // 1) Outer White Matte Spherical Shell (slightly wider ellipsoid like video)
      const headGeo = new THREE.SphereGeometry(1.95, 64, 64);
      headGeo.scale(1.13, 0.96, 1.02);
      const headMat = new THREE.MeshStandardMaterial({
        color: 0xf7f7fa,
        roughness: 0.42,
        metalness: 0.04
      });
      const headMesh = new THREE.Mesh(headGeo, headMat);
      robotHeadGroup.add(headMesh);

      // 2) Glowing Colored Visor Bezel / Rim (Custom Shader with smooth 2-color gradient)
      // Built as a curved rounded-stadium shell hugging the front surface of the sphere
      function createCurvedRoundedRectGeometry(width, height, radius, zOffset, sphereRx, sphereRy, sphereRz) {
        const shape = new THREE.Shape();
        const hw = width / 2;
        const hh = height / 2;
        const r = Math.min(radius, hw, hh);

        shape.moveTo(-hw + r, -hh);
        shape.lineTo(hw - r, -hh);
        shape.quadraticCurveTo(hw, -hh, hw, -hh + r);
        shape.lineTo(hw, hh - r);
        shape.quadraticCurveTo(hw, hh, hw - r, hh);
        shape.lineTo(-hw + r, hh);
        shape.quadraticCurveTo(-hw, hh, -hw, hh - r);
        shape.lineTo(-hw, -hh + r);
        shape.quadraticCurveTo(-hw, -hh, -hw + r, -hh);

        const geom = new THREE.ShapeGeometry(shape, 40);
        // Subdivide vertices onto a regular grid so projection onto the sphere is silky smooth
        const planeGeo = new THREE.PlaneGeometry(width, height, 64, 48);
        const pos = planeGeo.attributes.position;
        const uvs = planeGeo.attributes.uv;

        for (let i = 0; i < pos.count; i++) {
          let px = pos.getX(i);
          let py = pos.getY(i);

          // Clamp/project rectangular grid into a smooth stadium/squircle outline
          const ix = Math.max(0, Math.abs(px) - (hw - r));
          const iy = Math.max(0, Math.abs(py) - (hh - r));
          const cornerDist = Math.hypot(ix, iy);
          if (cornerDist > r) {
            const scale = r / cornerDist;
            px = Math.sign(px) * ((hw - r) + ix * scale);
            py = Math.sign(py) * ((hh - r) + iy * scale);
          }

          // Project (px, py) onto the front of the ellipsoid so it wraps seamlessly around Orbii
          const nx = px / sphereRx;
          const ny = py / sphereRy;
          const inside = Math.max(0.05, 1.0 - nx * nx - ny * ny);
          const pz = sphereRz * Math.sqrt(inside) + zOffset;

          pos.setXYZ(i, px, py, pz);
          uvs.setXY(i, (px + hw) / width, (py + hh) / height);
        }

        planeGeo.computeVertexNormals();
        return planeGeo;
      }

      const rimGeo = createCurvedRoundedRectGeometry(3.32, 2.02, 0.86, 0.045, 1.95 * 1.13, 1.95 * 0.96, 1.95 * 1.02);
      const rimShaderMat = new THREE.ShaderMaterial({
        uniforms: {
          uColor1: { value: PALETTES.default.c1.clone() },
          uColor2: { value: PALETTES.default.c2.clone() }
        },
        vertexShader: `
          varying vec2 vUv;
          varying vec3 vNormal;
          void main() {
            vUv = uv;
            vNormal = normalize(normalMatrix * normal);
            gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
          }
        `,
        fragmentShader: `
          uniform vec3 uColor1;
          uniform vec3 uColor2;
          varying vec2 vUv;
          varying vec3 vNormal;
          void main() {
            float t = clamp(vUv.x * 0.85 + (1.0 - vUv.y) * 0.25, 0.0, 1.0);
            vec3 baseCol = mix(uColor1, uColor2, t);
            // Subtle rim lighting for 3D plastic bezel depth
            float light = dot(vNormal, normalize(vec3(0.2, 0.5, 1.0))) * 0.15 + 0.92;
            gl_FragColor = vec4(baseCol * light, 1.0);
          }
        `
      });

      const rimMesh = new THREE.Mesh(rimGeo, rimShaderMat);
      robotHeadGroup.add(rimMesh);

      // 3) Glossy Black Visor Screen with Dynamic High-Res 2D Canvas Texture (Eyes + Angry Scowl + Reticle)
      const visorCanvas = document.createElement('canvas');
      visorCanvas.width = 1024;
      visorCanvas.height = 640;
      const vCtx = visorCanvas.getContext('2d');

      const visorTexture = new THREE.CanvasTexture(visorCanvas);
      visorTexture.anisotropy = 4;

      const visorGeo = createCurvedRoundedRectGeometry(3.02, 1.72, 0.74, 0.085, 1.95 * 1.13, 1.95 * 0.96, 1.95 * 1.02);
      const visorMat = new THREE.MeshBasicMaterial({
        map: visorTexture
      });
      const visorMesh = new THREE.Mesh(visorGeo, visorMat);
      robotHeadGroup.add(visorMesh);

      // Draw the Visor Screen (Glossy black background, subtle reflection, expressive white eyes, and angry reticle)
      function updateVisorCanvas() {
        const w = visorCanvas.width;
        const h = visorCanvas.height;
        vCtx.clearRect(0, 0, w, h);

        // Deep glossy black visor fill
        const bgGrad = vCtx.createLinearGradient(0, 0, w, h);
        bgGrad.addColorStop(0, '#14141b');
        bgGrad.addColorStop(0.35, '#09090d');
        bgGrad.addColorStop(1, '#050508');
        vCtx.fillStyle = bgGrad;
        vCtx.fillRect(0, 0, w, h);

        // Soft top-left studio glass reflection arc
        const glossGrad = vCtx.createRadialGradient(w * 0.22, h * 0.12, 10, w * 0.25, h * 0.18, w * 0.55);
        glossGrad.addColorStop(0, 'rgba(255, 255, 255, 0.09)');
        glossGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0.03)');
        glossGrad.addColorStop(1, 'rgba(255, 255, 255, 0)');
        vCtx.fillStyle = glossGrad;
        vCtx.fillRect(0, 0, w, h);

        // Eye positions (with parallax offset inside the visor)
        const shiftX = animState.eyeShiftX * 72;
        const shiftY = animState.eyeShiftY * 45;
        const eyeW = 144;
        const eyeH = 228 * (1 - animState.blinkAmount * 0.92);
        const eyeRadius = Math.min(eyeW / 2, eyeH / 2);

        const leftEyeX = w * 0.345 + shiftX;
        const rightEyeX = w * 0.655 + shiftX;
        const eyeCenterY = h * 0.49 - shiftY;

        // Helper to draw each pill eye and clip top slant when angry
        function drawEye(cx, cy, isLeft) {
          vCtx.save();
          vCtx.beginPath();
          vCtx.roundRect(cx - eyeW / 2, cy - eyeH / 2, eyeW, eyeH, eyeRadius);
          vCtx.fillStyle = '#ffffff';
          vCtx.shadowColor = 'rgba(255, 255, 255, 0.35)';
          vCtx.shadowBlur = 14;
          vCtx.fill();

          // If angryAmount > 0.01, carve a sharp diagonal angry upper eyelid mask (matches 00:16-00:17)
          if (animState.angryAmount > 0.01) {
            const a = animState.angryAmount;
            vCtx.globalCompositeOperation = 'source-atop';
            vCtx.fillStyle = '#08080c';
            vCtx.beginPath();
            const topY = cy - eyeH / 2 - 20;
            const innerDrop = topY + a * 125;
            const outerDrop = topY - a * 15;
            if (isLeft) {
              vCtx.moveTo(cx - eyeW, topY);
              vCtx.lineTo(cx + eyeW, topY);
              vCtx.lineTo(cx + eyeW, innerDrop);
              vCtx.lineTo(cx - eyeW, outerDrop);
            } else {
              vCtx.moveTo(cx - eyeW, topY);
              vCtx.lineTo(cx + eyeW, topY);
              vCtx.lineTo(cx + eyeW, outerDrop);
              vCtx.lineTo(cx - eyeW, innerDrop);
            }
            vCtx.closePath();
            vCtx.fill();
          }
          vCtx.restore();
        }

        drawEye(leftEyeX, eyeCenterY, true);
        drawEye(rightEyeX, eyeCenterY, false);

        // Draw Orange Target Reticle between the eyes when clicked/angry (exact match to 00:16)
        if (animState.targetReticle > 0.01) {
          const rx = (leftEyeX + rightEyeX) * 0.5;
          const ry = eyeCenterY + 26;
          vCtx.save();
          vCtx.globalAlpha = animState.targetReticle;
          vCtx.strokeStyle = '#ff5a1f';
          vCtx.lineWidth = 4.5;
          vCtx.beginPath();
          vCtx.arc(rx, ry, 34, 0, Math.PI * 2);
          vCtx.stroke();

          vCtx.fillStyle = '#ff5a1f';
          vCtx.beginPath();
          vCtx.arc(rx, ry, 7, 0, Math.PI * 2);
          vCtx.fill();
          vCtx.restore();
        }

        visorTexture.needsUpdate = true;
      }

      // ==========================================
      // 4. WIDGET & THEME SYNCHRONIZATION
      // ==========================================
      const SLIDE_TITLES = ['Mood swings', 'Focus level', 'Eye contact'];
      let lastSlideIndex = -1;

      function setWidgetSlide(index) {
        if (index === lastSlideIndex) return;
        lastSlideIndex = index;
        cursorState.activeSlide = index;

        if (widgetTitle) widgetTitle.textContent = SLIDE_TITLES[index];

        const slides = [slideMood, slideFocus, slideEye].filter(Boolean);
        slides.forEach((el, idx) => {
          if (idx === index) {
            el.classList.remove('opacity-0', 'scale-90', 'pointer-events-none');
            el.classList.add('opacity-100', 'scale-100');
          } else {
            el.classList.remove('opacity-100', 'scale-100');
            el.classList.add('opacity-0', 'scale-90', 'pointer-events-none');
          }
        });

        if (slideDots && slideDots.length) {
          slideDots.forEach((dot, idx) => {
            if (idx === index) {
              dot.className = 'w-4 h-1.5 rounded-full bg-[#18181c] transition-all duration-300';
            } else {
              dot.className = 'w-1.5 h-1.5 rounded-full bg-[#d0d0da] transition-all duration-300';
            }
          });
        }

        if (index === 2 && eyeBars && eyeBars.length) {
          eyeBars.forEach((bar) => {
            bar.style.height = '8%';
          });
          setTimeout(() => {
            eyeBars.forEach((bar) => {
              bar.style.height = bar.getAttribute('data-target-h');
            });
          }, 90);
        }

        if (index === 1 && focusRingCircle && focusSemicircle) {
          focusRingCircle.style.strokeDashoffset = '195';
          focusSemicircle.style.transform = 'rotate(-15deg) translateY(3px)';
          setTimeout(() => {
            focusRingCircle.style.strokeDashoffset = '58';
            focusSemicircle.style.transform = 'rotate(24deg) translateY(3px)';
          }, 90);
        }
      }

      function updateEyeBarColors(theme) {
        if (!eyeBars || !eyeBars.length) return;
        eyeBars.forEach((bar) => {
          const isPrimary = bar.getAttribute('data-tone') === 'primary';
          if (theme === 'green') {
            bar.style.background = isPrimary
              ? 'linear-gradient(180deg, #4da643 0%, #68c95c 100%)'
              : 'linear-gradient(180deg, #b8e6b2 0%, #d8f3d4 100%)';
          } else if (theme === 'angry') {
            bar.style.background = isPrimary
              ? 'linear-gradient(180deg, #e03c14 0%, #ff6b3d 100%)'
              : 'linear-gradient(180deg, #ffbfa8 0%, #ffe0d4 100%)';
          } else {
            bar.style.background = isPrimary
              ? 'linear-gradient(180deg, #8b3dff 0%, #b37aff 100%)'
              : 'linear-gradient(180deg, #4db8ff 0%, #b5e2ff 100%)';
          }
        });
      }

      function applyTheme(theme, hoverTarget) {
        cursorState.theme = theme;
        cursorState.hoverTarget = hoverTarget || null;

        document.body.classList.remove('theme-green', 'theme-angry');
        if (theme === 'green') document.body.classList.add('theme-green');
        if (theme === 'angry') document.body.classList.add('theme-angry');

        // Sync SVG Gradients (Top-left Logo & Focus Level Ring)
        const pal = PALETTES[theme] || PALETTES.default;
        if (logoStop1) logoStop1.setAttribute('stop-color', pal.logo1);
        if (logoStop2) logoStop2.setAttribute('stop-color', pal.logo2);

        if (focusStop1 && focusStop2 && focusStop3) {
          if (theme === 'green') {
            focusStop1.setAttribute('stop-color', '#47a43e');
            focusStop2.setAttribute('stop-color', '#6bd45f');
            focusStop3.setAttribute('stop-color', '#b5ebae');
          } else if (theme === 'angry') {
            focusStop1.setAttribute('stop-color', '#ff3b0f');
            focusStop2.setAttribute('stop-color', '#ff7b3d');
            focusStop3.setAttribute('stop-color', '#ffc299');
          } else {
            focusStop1.setAttribute('stop-color', '#8b3dff');
            focusStop2.setAttribute('stop-color', '#c86bfa');
            focusStop3.setAttribute('stop-color', '#38b6ff');
          }
        }

        updateEyeBarColors(theme);

        // Button & Cursor Hover Visual States
        if (topAdoptBtn) topAdoptBtn.classList.toggle('hovered-green', hoverTarget === 'top-cta');
        if (mobileAdoptBtn) mobileAdoptBtn.classList.toggle('hovered-green', hoverTarget === 'top-cta');
        if (heroAdoptBtn) heroAdoptBtn.classList.toggle('hovered-green', hoverTarget === 'hero-cta');
        if (navTricksBtn) navTricksBtn.classList.toggle('nav-hovered', hoverTarget === 'nav-tricks');

        if (customCursor) {
          customCursor.classList.toggle('cursor-cta-hover', hoverTarget === 'top-cta' || hoverTarget === 'hero-cta');
          customCursor.classList.toggle('cursor-angry-poke', hoverTarget === 'angry-visor');
        }
      }

      // ==========================================
      // 5. AI CURSOR CHOREOGRAPHER (EXACT 00:00 - 00:31 VIDEO TIMELINE)
      // ==========================================
      // Helper to get normalized viewport coordinates of any DOM element
      function getElemNormPos(el, offsetX = 0, offsetY = 0) {
        if (!el) return { x: 0.5, y: 0.5 };
        const rect = el.getBoundingClientRect();
        return {
          x: (rect.left + rect.width * (0.5 + offsetX)) / window.innerWidth,
          y: (rect.top + rect.height * (0.5 + offsetY)) / window.innerHeight
        };
      }

      function evaluateVideoTimeline(t) {
        // Compute responsive anchor positions dynamically so cursor always hits the exact UI elements on any screen size
        const logoPos = getElemNormPos(brandLogo, -0.15, 0.15);
        const topBtnPos = getElemNormPos(topAdoptBtn, 0.1, 0);
        const heroBtnPos = getElemNormPos(heroAdoptBtn, 0.08, 0.05);
        const tricksPos = getElemNormPos(navTricksBtn, 0, 0.1);
        const widgetPos = getElemNormPos(widgetCard, -0.38, 0.22);

        // Timeline Keyframes (t in seconds 0..31.833) matching every second of the uploaded video
        let pos = { x: 0.27, y: 0.17 };
        let theme = 'default';
        let hoverTarget = null;
        let slideIdx = 0;
        let isResetting = false;

        if (t < 1.0) {
          // 00:00: Cursor upper-left of Orbii, Mood swings card
          pos = { x: 0.27, y: 0.17 };
          slideIdx = 0;
        } else if (t < 2.4) {
          // 00:01 - 00:02: Moves over top-left "orbii" logo, switches to Focus level at 00:02
          pos = { x: logoPos.x, y: logoPos.y + (t > 1.7 ? 0.03 : 0) };
          slideIdx = t < 1.8 ? 0 : 1;
        } else if (t < 3.6) {
          // 00:03: Moves above Orbii, Focus level ring fills
          pos = { x: 0.36, y: 0.21 };
          slideIdx = 1;
        } else if (t < 4.7) {
          // 00:04: Sweeps right of Orbii
          pos = { x: 0.84, y: 0.36 };
          slideIdx = 1;
        } else if (t < 5.7) {
          // 00:05: Sweeps left above "I'm cute", widget enters Eye contact
          pos = { x: 0.16, y: 0.29 };
          slideIdx = 2;
        } else if (t < 6.7) {
          // 00:06: Moves upper-right of Orbii while Eye contact bars rise
          pos = { x: 0.71, y: 0.31 };
          slideIdx = 2;
        } else if (t < 8.8) {
          // 00:07 - 00:08: Hovers Top-Right "Adopt Me" button -> GREEN THEME!
          pos = { x: topBtnPos.x, y: topBtnPos.y };
          theme = 'green';
          hoverTarget = 'top-cta';
          slideIdx = t < 7.8 ? 2 : 0; // 00:07 Eye contact (green) -> 00:08 Mood swings (green)
        } else if (t < 10.0) {
          // 00:09: Leaves Top-Right button -> back to Purple/Blue theme
          pos = { x: 0.82, y: 0.22 };
          slideIdx = 0;
        } else if (t < 11.0) {
          // 00:10: Moves toward top nav ("My World" / "My Tricks")
          pos = { x: 0.44, y: 0.16 };
          slideIdx = 0;
        } else if (t < 12.6) {
          // 00:11 - 00:12: Hovers "My Tricks" -> Focus level slide
          pos = { x: tricksPos.x, y: tricksPos.y };
          hoverTarget = 'nav-tricks';
          slideIdx = 1;
        } else if (t < 13.8) {
          // 00:13: Hovers just above Orbii's top forehead
          pos = { x: 0.55, y: 0.31 };
          slideIdx = 1;
        } else if (t < 15.8) {
          // 00:14 - 00:15: Circles upper-left of Orbii -> Eye contact slide
          pos = t < 14.8 ? { x: 0.27, y: 0.36 } : { x: 0.39, y: 0.34 };
          slideIdx = 2;
        } else if (t < 17.85) {
          // 00:16 - 00:17: Clicks center of Orbii's visor -> ANGRY ORANGE/RED THEME!
          pos = { x: 0.50, y: 0.52 };
          theme = 'angry';
          hoverTarget = 'angry-visor';
          slideIdx = t < 16.85 ? 2 : 0; // 00:16 orange Eye contact -> 00:17 orange Mood swings
        } else if (t < 18.9) {
          // 00:18: Moves down below Orbii's chin -> restores Purple/Blue theme
          pos = { x: 0.49, y: 0.68 };
          slideIdx = 0;
        } else if (t < 20.0) {
          // 00:19: Moves to bottom-left corner of Mood swings card
          pos = { x: widgetPos.x, y: widgetPos.y };
          slideIdx = 0;
        } else if (t < 20.9) {
          // 00:20: Glides left below Orbii toward primary CTA button
          pos = { x: 0.44, y: 0.82 };
          slideIdx = 0;
        } else if (t < 26.75) {
          // 00:21 - 00:26: Hovers Left "Adopt Me ↗" CTA button -> GREEN THEME!
          pos = { x: heroBtnPos.x, y: heroBtnPos.y };
          theme = 'green';
          hoverTarget = 'hero-cta';
          slideIdx = t < 23.85 ? 1 : 2; // 00:21-00:23 Green Focus level -> 00:24-00:26 Green Eye contact
        } else if (t < 27.85) {
          // 00:27: Page Entrance / Transition Reset (Orbii dips down & rises back)
          pos = { x: 0.50, y: 0.55 };
          isResetting = true;
          slideIdx = 2;
        } else {
          // 00:28 - 00:31: Settles cleanly in default Purple/Blue state
          pos = { x: 0.50, y: 0.50 };
          slideIdx = t < 30.8 ? 2 : 0; // 00:28-00:30 Eye contact -> 00:31 Mood swings
        }

        cursorState.targetX = pos.x;
        cursorState.targetY = pos.y;
        applyTheme(theme, hoverTarget);
        setWidgetSlide(slideIdx);

        // Handle 00:27 Entrance Reset visual transition
        document.body.classList.toggle('page-resetting', isResetting);
        customCursor.style.opacity = (t >= 26.75) ? '0' : '1';
      }

      // ==========================================
      // 6. PC CURSOR REAL-TIME TRACKING & UI CONTROLS
      // ==========================================
      let manualAngryTimeout = null;
      let manualSlideInterval = null;
      let isHoveringCta = false;
      let isAngryLocked = false;

      function startAutoWidgetCycle() {
        if (manualSlideInterval) clearInterval(manualSlideInterval);
        manualSlideInterval = setInterval(() => {
          if (!isAiDemoMode) {
            setWidgetSlide((cursorState.activeSlide + 1) % 3);
          }
        }, 3200);
      }

      function setAiDemoMode(enabled) {
        isAiDemoMode = enabled;
        document.body.classList.remove('page-resetting');
        customCursor.style.opacity = '1';

        if (enabled) {
          demoStartTime = performance.now();
                              if (manualSlideInterval) {
            clearInterval(manualSlideInterval);
            manualSlideInterval = null;
          }
        } else {
                                        applyTheme('default', null);
          startAutoWidgetCycle();
        }
      }



      function triggerAngryPoke() {
        if (isAiDemoMode) {
          setAiDemoMode(false);
        }
        isAngryLocked = true;
        applyTheme('angry', 'angry-visor');
        if (manualAngryTimeout) clearTimeout(manualAngryTimeout);
        manualAngryTimeout = setTimeout(() => {
          isAngryLocked = false;
          if (!isAiDemoMode && !isHoveringCta) {
            applyTheme('default', null);
          }
        }, 2200);
      }



      // Real-time PC mouse cursor tracking: moving your PC mouse immediately drives the cursor and 3D AI model!
      window.addEventListener('pointermove', (e) => {
        const nx = e.clientX / window.innerWidth;
        const ny = e.clientY / window.innerHeight;

        // If Auto Demo was running and the user moves their PC mouse, switch to live PC cursor control automatically
        if (isAiDemoMode && (Math.abs(e.movementX) > 2 || Math.abs(e.movementY) > 2)) {
          setAiDemoMode(false);
        }

        cursorState.targetX = nx;
        cursorState.targetY = ny;
        // Snap custom cursor ring directly to PC mouse position with zero lag
        cursorState.x = nx;
        cursorState.y = ny;

        if (!isAiDemoMode) {
          

          // Check if PC cursor is right between Orbii's eyes (center forehead zone)
          const distToForehead = Math.hypot(nx - 0.5, ny - 0.49);
          if (!isHoveringCta && !isAngryLocked) {
            if (distToForehead < 0.055) {
              applyTheme('angry', 'angry-visor');
            } else if (cursorState.theme === 'angry') {
              applyTheme('default', null);
            }
          }
        }
      });

      // Hover listeners for Action buttons (Login & Dashboard on desktop & mobile)
      const topLoginBtn = document.getElementById('top-login-btn');
      [topAdoptBtn, topLoginBtn, heroAdoptBtn, mobileAdoptBtn, mobileLoginBtn].forEach((btn) => {
        if (!btn) return;
        btn.addEventListener('pointerenter', () => {
          isHoveringCta = true;
          isAngryLocked = false;
          if (!isAiDemoMode) applyTheme('green', 'top-cta');
        });
        btn.addEventListener('pointerleave', () => {
          isHoveringCta = false;
          if (!isAiDemoMode) applyTheme('default', null);
        });
      });

      const heroLoginBtn = document.getElementById('hero-login-btn');
      if (heroLoginBtn) {
        heroLoginBtn.addEventListener('pointerenter', () => {
          isHoveringCta = true;
          isAngryLocked = false;
          if (!isAiDemoMode) applyTheme('green', 'hero-cta');
        });
        heroLoginBtn.addEventListener('pointerleave', () => {
          isHoveringCta = false;
          if (!isAiDemoMode) applyTheme('default', null);
        });
      }

      if (closeModalBtn && adoptModal) {
        closeModalBtn.addEventListener('click', () => {
          adoptModal.classList.add('hidden');
          adoptModal.classList.remove('flex');
        });

        adoptModal.addEventListener('click', (e) => {
          if (e.target === adoptModal) {
            adoptModal.classList.add('hidden');
            adoptModal.classList.remove('flex');
          }
        });
      }

      // Clicking on the 3D robot visor triggers the 00:16 Angry orange-red mode!
      renderer.domElement.addEventListener('pointerdown', (e) => {
        const nx = e.clientX / window.innerWidth;
        const ny = e.clientY / window.innerHeight;
        if (Math.hypot(nx - 0.5, ny - 0.52) < 0.19) {
          triggerAngryPoke();
        }
      });

      // Navigation Pills interaction (if present)
      if (navPills && navPills.length) {
        navPills.forEach((pill) => {
          pill.addEventListener('click', (e) => {
            if (pill.tagName.toLowerCase() === 'a') return;
            navPills.forEach((p) => p.classList.remove('active'));
            pill.classList.add('active');
          });
        });
      }

      if (brandLogo) {
        brandLogo.addEventListener('click', () => {
          lastSlideIndex = -1;
          setAiDemoMode(true);
        });
      }

      // If widget card is present, cycle slides
      if (widgetCard) {
        widgetCard.addEventListener('click', () => {
          setWidgetSlide((cursorState.activeSlide + 1) % 3);
        });
      }

      if (slideDots && slideDots.length) {
        slideDots.forEach((dot) => {
          dot.addEventListener('click', (e) => {
            e.stopPropagation();
            const idx = parseInt(dot.getAttribute('data-slide-dot'), 10);
            setWidgetSlide(idx);
          });
        });
      }


      // ==========================================
      // NEON AI HEXAGONAL CANVAS ENGINE (DARK & LIGHT)
      // ==========================================
      let isLightMode = localStorage.getItem('portal_theme') === 'light';
      if (isLightMode) {
        document.body.classList.add('light-mode');
      }

      const hexCanvas = document.getElementById('hex-canvas');
      const hCtx = hexCanvas ? hexCanvas.getContext('2d') : null;
      let hW = 0, hH = 0;
      let bloomX = 0.5, bloomY = 0.52;

      function resizeHexCanvas() {
        if (!hexCanvas) return;
        hW = hexCanvas.width  = window.innerWidth;
        hH = hexCanvas.height = window.innerHeight;
      }
      window.addEventListener('resize', resizeHexCanvas);
      resizeHexCanvas();

      // Flat-top Hexagon Path
      function hexFlatPath(ctx, cx, cy, r) {
        ctx.beginPath();
        for (let i = 0; i < 6; i++) {
          const a = (Math.PI / 3) * i;
          const x = cx + r * Math.cos(a);
          const y = cy + r * Math.sin(a);
          i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        }
        ctx.closePath();
      }

      function drawHexGrid(elapsedSec) {
        if (!hCtx) return;
        hCtx.clearRect(0, 0, hW, hH);

        const currentLight = document.body.classList.contains('light-mode');
        const HEX_R = 28;
        const GAP = 2.6;
        const colW = HEX_R * 1.5;
        const rowH = HEX_R * Math.sqrt(3);
        const cols = Math.ceil(hW / colW) + 2;
        const rows = Math.ceil(hH / rowH) + 2;
        const r = HEX_R - GAP * 0.5;

        // Smoothly interpolate bloom center with subtle lag behind cursor
        bloomX += (cursorState.x - bloomX) * 0.06;
        bloomY += (cursorState.y * 0.98 - bloomY) * 0.06;
        const gcx = hW * bloomX;
        const gcy = hH * bloomY;
        const pulse = 0.88 + 0.12 * Math.sin(elapsedSec * 2.1);

        // Theme palette lookup
        const isGreen = cursorState.theme === 'green';
        const isAngry = cursorState.theme === 'angry';

        if (!currentLight) {
          // ── DARK MODE: Neon glowing lines on #09090f ──
          hCtx.fillStyle = '#09090f';
          hCtx.fillRect(0, 0, hW, hH);

          let coreR = 180, coreG = 55, coreB = 255;
          let midR = 80, midG = 160, midB = 255;
          if (isGreen) {
            coreR = 50; coreG = 210; coreB = 100;
            midR = 80; midG = 220; midB = 130;
          } else if (isAngry) {
            coreR = 255; coreG = 65; coreB = 20;
            midR = 255; midG = 115; midB = 50;
          }

          for (let col = -1; col < cols; col++) {
            for (let row = -1; row < rows; row++) {
              const cx = col * colW + HEX_R;
              const cy = row * rowH + (col % 2 === 0 ? 0 : rowH / 2) + rowH / 2;

              const dx = (cx - gcx) / (hW * 0.52);
              const dy = (cy - gcy) / (hH * 0.48);
              const dist = Math.sqrt(dx * dx + dy * dy);

              const coreF  = Math.max(0, 1.0 - dist / 0.32) * pulse;
              const midF   = Math.max(0, 1.0 - dist / 0.68) * (1.0 - coreF * 0.55);
              const outerF = Math.max(0, 1.0 - dist / 1.05) * 0.42;

              const sr = Math.round(70  + coreF * coreR + midF * midR * 0.5 + outerF * 30);
              const sg = Math.round(70  + coreF * coreG + midF * midG * 0.6 + outerF * 110);
              const sb = Math.round(200 + coreF * (coreB - 200) + midF * midB * 0.4 + outerF * 55);
              const sa = Math.min(1.0, 0.07 + coreF * 0.72 + midF * 0.44 + outerF * 0.28);

              // Subtle glowing fill inside central core
              const fillA = Math.max(0, (1.0 - dist / 0.28) * 0.07 * pulse);
              if (fillA > 0.004) {
                hexFlatPath(hCtx, cx, cy, r - 0.5);
                hCtx.fillStyle = `rgba(${coreR},${coreG},${coreB},${fillA.toFixed(3)})`;
                hCtx.fill();
              }

              hexFlatPath(hCtx, cx, cy, r);
              hCtx.strokeStyle = `rgba(${sr|0},${sg|0},${sb|0},${sa.toFixed(3)})`;
              hCtx.lineWidth   = 1.35;
              hCtx.stroke();
            }
          }
        } else {
          // ── LIGHT MODE: Clean White Canvas + Dynamic Interactive Cursor Glow ──
          hCtx.fillStyle = '#ffffff';
          hCtx.fillRect(0, 0, hW, hH);

          // Reactive cursor colors in light theme
          let coreR = 145, coreG = 55, coreB = 255; // Luminous Royal Violet
          let midR = 40, midG = 155, midB = 255;    // Vibrant Sky Cyan
          if (isGreen) {
            coreR = 35; coreG = 175; coreB = 75;    // Emerald Green
            midR = 75; midG = 205; midB = 120;
          } else if (isAngry) {
            coreR = 245; coreG = 65; coreB = 25;    // Fiery Amber/Coral
            midR = 255; midG = 135; midB = 55;
          }

          for (let col = -1; col < cols; col++) {
            for (let row = -1; row < rows; row++) {
              const cx = col * colW + HEX_R;
              const cy = row * rowH + (col % 2 === 0 ? 0 : rowH / 2) + rowH / 2;

              // Distance from the interactive cursor bloom center
              const dx = (cx - gcx) / (hW * 0.36);
              const dy = (cy - gcy) / (hH * 0.34);
              const dist = Math.sqrt(dx * dx + dy * dy);

              // Dynamic proximity factors: coreF (directly under/around cursor), midF (outer ripple)
              const coreF = Math.max(0, 1.0 - dist / 0.36) * pulse;
              const midF  = Math.max(0, 1.0 - dist / 0.72) * (1.0 - coreF * 0.55);

              // Base stroke for distant hexes: crisp, delicate periwinkle-gray
              // Near the cursor, they light up with vibrant, glowing electric violet/blue!
              const sr = Math.round(226 - coreF * (226 - coreR) - midF * (226 - midR));
              const sg = Math.round(232 - coreF * (232 - coreG) - midF * (232 - midG));
              const sb = Math.round(242 - coreF * (242 - coreB) - midF * (242 - midB));
              const sa = Math.min(1.0, 0.55 + coreF * 0.45 + midF * 0.25);
              const strokeWidth = 1.15 + coreF * 0.85;

              // Subtle glowing soft-violet fill directly inside hexagons near the cursor
              const fillA = Math.max(0, (1.0 - dist / 0.30) * 0.09 * pulse);
              if (fillA > 0.004) {
                hexFlatPath(hCtx, cx, cy, r - 0.5);
                hCtx.fillStyle = `rgba(${coreR},${coreG},${coreB},${fillA.toFixed(3)})`;
                hCtx.fill();
              }

              // Crisp hexagonal card stroke
              hexFlatPath(hCtx, cx, cy, r);
              hCtx.strokeStyle = `rgba(${sr|0},${sg|0},${sb|0},${sa.toFixed(3)})`;
              hCtx.lineWidth   = strokeWidth;
              hCtx.stroke();
            }
          }
        }
      }

      // Theme Switcher Event Listener
      const themeToggleBtn = document.getElementById('theme-toggle-btn');
      const sunIcon = document.getElementById('theme-sun-icon');
      const moonIcon = document.getElementById('theme-moon-icon');

      function updateThemeIcons(light) {
        if (!sunIcon || !moonIcon) return;
        if (light) {
          sunIcon.classList.add('hidden');
          moonIcon.classList.remove('hidden');
        } else {
          sunIcon.classList.remove('hidden');
          moonIcon.classList.add('hidden');
        }
      }
      updateThemeIcons(isLightMode);

      if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
          const currentlyLight = document.body.classList.toggle('light-mode');
          localStorage.setItem('portal_theme', currentlyLight ? 'light' : 'dark');
          updateThemeIcons(currentlyLight);
          updateShadowTexture(currentlyLight);

          // Update Three.js lights dynamically
          if (currentlyLight) {
            ambientLight.intensity = 0.90;
            keyLight.intensity = 0.60;
            rimLight.intensity = 0.05;
            fillLight.intensity = 0.35;
          } else {
            ambientLight.intensity = 0.78;
            keyLight.intensity = 0.82;
            rimLight.intensity = 0.45;
            fillLight.intensity = 0.32;
          }
        });
      }

      // ==========================================
      // 7. MAIN ANIMATION LOOP (60FPS 3D TRACKING + SHADER LERP)
      // ==========================================
      let nextBlinkTime = 2.8;

      function formatSec(sec) {
        const s = Math.floor(sec);
        return '00:' + (s < 10 ? '0' + s : s);
      }

      function animate(now) {
        requestAnimationFrame(animate);

        const elapsedSec = (now - demoStartTime) / 1000;

        if (isAiDemoMode) {
          currentVideoSec = elapsedSec % VIDEO_DURATION;
          evaluateVideoTimeline(currentVideoSec);
                  }

        // Smoothly interpolate normalized cursor position
        const lerpSpeed = isAiDemoMode ? 0.085 : 0.18;
        cursorState.x += (cursorState.targetX - cursorState.x) * lerpSpeed;
        cursorState.y += (cursorState.targetY - cursorState.y) * lerpSpeed;

        // Position the custom circular cursor in viewport pixels
        const px = cursorState.x * window.innerWidth;
        const py = cursorState.y * window.innerHeight;
        customCursor.style.left = `${px}px`;
        customCursor.style.top = `${py}px`;

        // Calculate 3D head rotation & eye parallax from cursor offset (-0.5 .. +0.5)
        const dx = cursorState.x - 0.5;
        const dy = cursorState.y - 0.5;

        // Only lock gaze to center during the 00:27 page reset; otherwise Orbii ALWAYS tracks your PC cursor!
        const lookCenter = (isAiDemoMode && currentVideoSec >= 27.0);
        const targetRotY = lookCenter ? 0 : THREE.MathUtils.clamp(dx * 1.32, -0.72, 0.72);
        const targetRotX = lookCenter ? 0 : THREE.MathUtils.clamp(dy * 0.95, -0.52, 0.52);
        const targetRotZ = lookCenter ? 0 : THREE.MathUtils.clamp(-dx * dy * 0.52, -0.22, 0.22);

        animState.headRotY += (targetRotY - animState.headRotY) * 0.14;
        animState.headRotX += (targetRotX - animState.headRotX) * 0.14;
        animState.headRotZ += (targetRotZ - animState.headRotZ) * 0.14;

        robotHeadGroup.rotation.y = animState.headRotY;
        robotHeadGroup.rotation.x = animState.headRotX;
        robotHeadGroup.rotation.z = animState.headRotZ;

        // Subtle 3D head translation toward the PC cursor for lifelike depth
        robotRoot.position.x += ((lookCenter ? 0 : dx * 0.38) - robotRoot.position.x) * 0.12;

        // Secondary eye shift inside visor following the PC cursor
        const targetEyeX = lookCenter ? 0 : THREE.MathUtils.clamp(dx * 1.55, -0.72, 0.72);
        const targetEyeY = lookCenter ? 0 : THREE.MathUtils.clamp(-dy * 1.45, -0.65, 0.65);
        animState.eyeShiftX += (targetEyeX - animState.eyeShiftX) * 0.2;
        animState.eyeShiftY += (targetEyeY - animState.eyeShiftY) * 0.2;

        // Entrance / Reset Animation at 00:27 (Orbii dips down below the frame and rises back up)
        const targetEntranceY = (isAiDemoMode && currentVideoSec >= 26.8 && currentVideoSec < 27.85) ? -3.35 : 0;
        animState.entranceOffsetY += (targetEntranceY - animState.entranceOffsetY) * 0.11;

        // Gentle floating idle bobbing
        const floatY = Math.sin(elapsedSec * 2.1) * 0.09;
        robotRoot.position.y = floatY + animState.entranceOffsetY;

        // Scale floor shadow with height
        const shadowScale = THREE.MathUtils.clamp(1.0 - (floatY + animState.entranceOffsetY) * 0.15, 0.5, 1.3);
        shadowPlane.scale.set(shadowScale, shadowScale, 1);

        // Animate background hex tiles & floating spheres subtly
        hexTiles.forEach((tile) => {
          tile.mesh.position.z = tile.baseZ + Math.sin(elapsedSec * 1.4 + tile.phase) * 0.04;
        });
        floatingDots.forEach((dot) => {
          dot.mesh.position.y = dot.baseY + Math.sin(elapsedSec * 1.8 + dot.phase) * 0.08;
        });
        bgGroup.rotation.y = animState.headRotY * 0.08;
        bgGroup.rotation.x = animState.headRotX * 0.08;

        // Subtle interactive depth parallax on the central neon glow bloom
        const bloomEl = document.getElementById('hex-center-bloom');
        if (bloomEl) {
          bloomEl.style.transform = `translate3d(${(cursorState.x - 0.5) * 22}px, ${(cursorState.y - 0.5) * 15}px, 0)`;
        }

        // Interpolate 3D Visor Rim Gradient Colors
        const activePalette = PALETTES[cursorState.theme] || PALETTES.default;
        animState.rimColor1.lerp(activePalette.c1, 0.1);
        animState.rimColor2.lerp(activePalette.c2, 0.1);
        rimShaderMat.uniforms.uColor1.value.copy(animState.rimColor1);
        rimShaderMat.uniforms.uColor2.value.copy(animState.rimColor2);

        // Interpolate Angry Eyelid Slant & Orange Target Reticle
        const targetAngry = cursorState.theme === 'angry' ? 1.0 : 0.0;
        const targetReticle = (cursorState.hoverTarget === 'angry-visor' && (!isAiDemoMode || currentVideoSec < 16.95)) ? 1.0 : 0.0;
        animState.angryAmount += (targetAngry - animState.angryAmount) * 0.18;
        animState.targetReticle += (targetReticle - animState.targetReticle) * 0.2;

        // Natural occasional eye blink (when not angry)
        if (elapsedSec > nextBlinkTime && animState.angryAmount < 0.1) {
          const blinkPhase = (elapsedSec - nextBlinkTime) / 0.18;
          if (blinkPhase <= 1.0) {
            animState.blinkAmount = Math.sin(blinkPhase * Math.PI);
          } else {
            animState.blinkAmount = 0;
            nextBlinkTime = elapsedSec + 3.2 + Math.random() * 2.5;
          }
        } else {
          animState.blinkAmount *= 0.8;
        }

        // Draw Neon Hex Canvas behind Three.js scene
        drawHexGrid(elapsedSec);

        // Redraw 2D Visor Canvas Texture & Render 3D Scene
        updateVisorCanvas();
        renderer.render(scene, camera);
      }

      // Responsive Resize Handler (optimizes camera distance & framing on mobile/tablet/desktop)
      function onWindowResize() {
        const aspect = window.innerWidth / window.innerHeight;
        camera.aspect = aspect;
        if (aspect < 0.85) {
          // Mobile portrait mode: pull camera back so Orbii robot & shadow stay beautifully framed
          camera.position.z = 13.5 + (0.85 - aspect) * 6.5;
        } else {
          camera.position.z = 13.5;
        }
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
      }
      window.addEventListener('resize', onWindowResize);
      onWindowResize();

      // Start Loop with Live PC Cursor Tracking enabled by default
      setWidgetSlide(0);
      updateEyeBarColors('default');
      startAutoWidgetCycle();
      requestAnimationFrame(animate);
    })();
  </script>
  <!-- Responsive Bottom Action Dock (Down side for Mobile & Tablet screens < md) -->
  <div class="md:hidden fixed bottom-3 sm:bottom-4 inset-x-0 z-30 flex flex-col items-center gap-2 px-4 pointer-events-none">
    <!-- Floating Action Dock -->
    <div class="mobile-dock pointer-events-auto flex items-center justify-between gap-2.5 w-full max-w-[340px] p-1.5 rounded-full">
      <a href="<?= base_url('login') ?>" id="mobile-login-btn" class="mobile-login-btn flex-1 text-center py-2 px-4 rounded-full text-xs sm:text-sm font-semibold tracking-wide transition">
        Login
      </a>
      <a href="<?= base_url('dashboard') ?>" id="mobile-adopt-btn" class="adopt-btn flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-4 rounded-full text-xs sm:text-sm font-semibold tracking-wide shadow-md">
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="9" rx="1"/>
          <rect x="14" y="3" width="7" height="5" rx="1"/>
          <rect x="14" y="12" width="7" height="9" rx="1"/>
          <rect x="3" y="16" width="7" height="5" rx="1"/>
        </svg>
        <span>Dashboard</span>
      </a>
    </div>

    <!-- Mobile Signature Badge -->
    <div class="credit-badge-el pointer-events-auto flex items-center gap-1.5 font-medium rounded-full">
      <span class="rounded-full" style="width:6px;height:6px;background:#7c3dff;box-shadow:0 0 8px rgba(124,61,255,0.95);display:inline-block"></span>
      <span>Made by <strong style="color:#a070ff;font-weight:700">DK Dholakiya</strong></span>
    </div>
  </div>

  <!-- Desktop Signature Badge (Fixed bottom-right on screens md+) -->
  <div id="credit-badge-el" class="credit-badge-el hidden md:flex fixed bottom-4 right-6 z-30 items-center gap-2 font-medium rounded-full">
    <span class="rounded-full" style="width:7px;height:7px;background:#7c3dff;box-shadow:0 0 10px rgba(124,61,255,0.95);display:inline-block"></span>
    <span>Made by <strong style="color:#a070ff;font-weight:700">DK Dholakiya</strong></span>
  </div>

</body>
</html>