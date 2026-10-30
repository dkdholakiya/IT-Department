<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>AI Greenhouse — Smart Management Dashboard</title>
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
      /* Dynamic Theme Tokens (Updated live by the Color Chooser!) */
      --color-primary: #10B981;
      --color-primary-dark: #059669;
      --color-primary-light: #34D399;
      --color-primary-rgb: 16, 185, 129;
      --color-alert: #EB5011;

      /* Dynamic Ambient Mesh Backdrop */
      --bg-mesh-1: #C7E0ED;
      --bg-mesh-2: #D4E8C7;
      --bg-mesh-3: #F2F7F0;
      --bg-mesh-4: #C9E3B8;

      /* Glassmorphic Surfaces */
      --glass-surface: rgba(255, 255, 255, 0.74);
      --glass-surface-strong: rgba(255, 255, 255, 0.94);
      --glass-pill: rgba(255, 255, 255, 0.84);
      --glass-border: rgba(255, 255, 255, 0.92);
      --glass-shadow: 0 10px 28px -6px rgba(var(--color-primary-rgb), 0.14), 0 2px 8px -1px rgba(10, 9, 9, 0.04);
      --drawer-bg: linear-gradient(160deg, rgba(255, 255, 255, 0.44) 0%, rgba(255, 255, 255, 0.24) 55%, rgba(var(--color-primary-rgb), 0.16) 100%);
      --drawer-item-bg: rgba(255, 255, 255, 0.48);
      --side-menu-pill-glass: linear-gradient(145deg, rgba(255, 255, 255, 0.52) 0%, rgba(255, 255, 255, 0.28) 58%, rgba(var(--color-primary-rgb), 0.15) 100%);
      --side-menu-btn-glass: rgba(255, 255, 255, 0.45);
      --side-menu-border: rgba(255, 255, 255, 0.78);
      --side-menu-shadow: 0 12px 32px -4px rgba(var(--color-primary-rgb), 0.20), 0 4px 14px rgba(10, 9, 9, 0.06), inset 0 1px 1.5px rgba(255, 255, 255, 0.88);

      /* Dynamic Font & Icon Colors (Adapt to Chosen Theme Color!) */
      --text-primary: #0A0909;
      --text-secondary: #2C3E28;
      --text-muted: #5C6F58;
      --text-accent: #059669;

      /* Chart & Control Tokens */
      --bar-inactive: rgba(var(--color-primary-rgb), 0.18);
      --bar-mid: rgba(var(--color-primary-rgb), 0.46);
      --bar-active: #10B981;
      --bar-peak: #EB5011;
      --sidebar-active-bg: #10B981;
      --sidebar-active-text: #FFFFFF;
      --icon-circle-bg: rgba(255, 255, 255, 0.94);
      --ai-btn-grad: linear-gradient(180deg, #F2FFE6 0%, #99FF55 48%, #6CE620 100%);
      --ai-btn-shadow: rgba(108, 230, 32, 0.35);
      --ai-btn-text: #0A0909;
    }

    /* Dark Glass Mode */
    body.theme-dark {
      --bg-mesh-1: #0D1F18;
      --bg-mesh-2: #122616;
      --bg-mesh-3: #090D0A;
      --bg-mesh-4: #162E1B;
      --glass-surface: rgba(16, 24, 18, 0.78);
      --glass-surface-strong: rgba(20, 30, 23, 0.95);
      --glass-pill: rgba(24, 36, 27, 0.88);
      --glass-border: rgba(var(--color-primary-rgb), 0.32);
      --glass-shadow: 0 12px 32px -4px rgba(0, 0, 0, 0.55);
      --drawer-bg: linear-gradient(160deg, rgba(22, 34, 26, 0.62) 0%, rgba(14, 22, 17, 0.44) 60%, rgba(var(--color-primary-rgb), 0.18) 100%);
      --drawer-item-bg: rgba(28, 42, 32, 0.54);
      --side-menu-pill-glass: linear-gradient(145deg, rgba(24, 38, 28, 0.64) 0%, rgba(15, 25, 18, 0.46) 60%, rgba(var(--color-primary-rgb), 0.16) 100%);
      --side-menu-btn-glass: rgba(255, 255, 255, 0.10);
      --side-menu-border: rgba(var(--color-primary-rgb), 0.38);
      --side-menu-shadow: 0 14px 34px -4px rgba(0, 0, 0, 0.55), inset 0 1px 1px rgba(255, 255, 255, 0.14);
      --text-primary: #F4FBF2;
      --text-secondary: #C8DEC4;
      --text-muted: #8FA98B;
      --text-accent: var(--color-primary-light);
      --bar-inactive: rgba(255, 255, 255, 0.13);
      --bar-mid: rgba(var(--color-primary-rgb), 0.48);
      --icon-circle-bg: rgba(28, 42, 31, 0.95);
    }

    * {
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      font-family: 'Gilroy', 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--text-primary);
      background-color: var(--bg-mesh-3);
      background-image:
        radial-gradient(at 6% 6%, var(--bg-mesh-1) 0px, transparent 52%),
        radial-gradient(at 94% 8%, var(--bg-mesh-1) 0px, transparent 48%),
        radial-gradient(at 12% 88%, var(--bg-mesh-4) 0px, transparent 55%),
        radial-gradient(at 62% 80%, var(--bg-mesh-2) 0px, transparent 55%),
        radial-gradient(at 50% 42%, var(--bg-mesh-3) 0px, transparent 70%);
      background-attachment: fixed;
      margin: 0;
      overflow-x: hidden;
      transition: background 0.3s ease, color 0.25s ease;
    }

    /* Glassmorphic Card Surface */
    .glass-card {
      background: var(--glass-surface);
      backdrop-filter: blur(22px) saturate(160%);
      -webkit-backdrop-filter: blur(22px) saturate(160%);
      border: 1.5px solid var(--glass-border);
      box-shadow: var(--glass-shadow);
      border-radius: 22px;
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.25s ease, background 0.3s ease;
    }

    .glass-card:hover {
      box-shadow: 0 14px 32px -6px rgba(var(--color-primary-rgb), 0.18), 0 4px 12px -2px rgba(10, 9, 9, 0.05);
    }

    /* Floating Harvest AI Glass Widget inside Hero */
    .harvest-glass-modal {
      background: var(--glass-surface);
      backdrop-filter: blur(18px) saturate(180%);
      -webkit-backdrop-filter: blur(18px) saturate(180%);
      border: 1.5px solid var(--glass-border);
      box-shadow: 0 18px 40px rgba(10, 9, 9, 0.16), inset 0 1px 1px rgba(255, 255, 255, 0.85);
    }

    /* Glass Pill Containers (Navbar & Desktop Sidebar) */
    .glass-pill {
      background: var(--glass-pill);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      border: 1.5px solid var(--glass-border);
      box-shadow: 0 4px 14px rgba(10, 9, 9, 0.04);
    }

    /* ==========================================================================
       SAMSUNG EDGE SHORTCUT CAPSULE SIDE MENU (Mobile < 1024px & Desktop Dock)
       - Default on Mobile: Sleek Icon-Only Capsule (76px wide, like 12341.png)
       - Expand Button Near Close (✕) Icon: Smoothly expands to Full Sidebar (266px)
         showing all Side Menu Names without truncation!
       - Top Navbar hides behind the Sidebar & Backdrop when Sidebar is open!
       ========================================================================== */
    @media (max-width: 1023px) {
      /* Hide top navbar smoothly behind the sidebar when mobile sidebar is open */
      #topNavbar {
        transition: opacity 0.32s cubic-bezier(0.22, 1, 0.36, 1), transform 0.32s cubic-bezier(0.22, 1, 0.36, 1), filter 0.32s ease;
      }
      body.mobile-sidebar-active #topNavbar {
        opacity: 0;
        transform: translateY(-10px);
        filter: blur(6px);
        pointer-events: none;
      }

      #mainSidebar {
        background: var(--drawer-bg);
        backdrop-filter: blur(32px) saturate(195%);
        -webkit-backdrop-filter: blur(32px) saturate(195%);
        border: 1.5px solid var(--glass-border);
        border-radius: 38px;
        box-shadow:
          0 26px 64px -8px rgba(10, 9, 9, 0.36),
          0 0 0 1px rgba(255, 255, 255, 0.32) inset,
          0 8px 26px rgba(var(--color-primary-rgb), 0.24);
        transform-origin: left center;
        transition:
          transform 0.46s cubic-bezier(0.22, 1, 0.36, 1),
          opacity 0.34s cubic-bezier(0.22, 1, 0.36, 1),
          filter 0.34s cubic-bezier(0.22, 1, 0.36, 1),
          width 0.38s cubic-bezier(0.22, 1, 0.36, 1),
          padding 0.35s cubic-bezier(0.22, 1, 0.36, 1);
      }

      #mainSidebar.samsung-menu-closed {
        transform: translate3d(-125%, 0, 0) scale(0.86);
        opacity: 0;
        filter: blur(8px);
        pointer-events: none;
      }

      #mainSidebar.samsung-menu-open {
        transform: translate3d(0, 0, 0) scale(1);
        opacity: 1;
        filter: blur(0px);
        pointer-events: auto;
      }

      /* Staggered smooth entrance for shortcut items inside the Samsung capsule */
      #mainSidebar .samsung-shortcut-item {
        transition:
          transform 0.42s cubic-bezier(0.22, 1, 0.36, 1),
          opacity 0.32s ease,
          background-color 0.22s ease,
          box-shadow 0.22s ease;
        transition-delay: calc(var(--stagger, 0) * 30ms);
      }

      #mainSidebar.samsung-menu-closed .samsung-shortcut-item {
        transform: translateX(-16px) scale(0.9);
        opacity: 0;
      }

      #mainSidebar.samsung-menu-open .samsung-shortcut-item {
        transform: translateX(0) scale(1);
        opacity: 1;
      }

      /* DEFAULT MOBILE MODE: Icon-Only Frosted Glassmorphic Capsule Pills (72px wide) */
      #mainSidebar.samsung-icon-only {
        width: 72px !important;
        padding: 10px 8px !important;
      }
      #mainSidebar.samsung-icon-only #upperSideMenuHeader {
        width: 52px !important;
        height: 52px !important;
        padding: 6px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        justify-content: center !important;
        align-items: center !important;
        border-radius: 9999px !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
      }
      #mainSidebar.samsung-icon-only #expandSidebarNamesBtn {
        width: 40px !important;
        height: 40px !important;
      }
      #mainSidebar.samsung-icon-only .expanded-only {
        display: none !important;
      }
      #mainSidebar.samsung-icon-only #sideMenuNavPill {
        width: 52px !important;
        padding: 6px !important;
        border-radius: 9999px !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
        margin-left: auto !important;
        margin-right: auto !important;
        align-items: center !important;
      }
      #mainSidebar.samsung-icon-only .sidebar-nav-btn {
        justify-content: center !important;
        padding: 0 !important;
        width: 40px !important;
        height: 40px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        border-radius: 9999px !important;
      }
      #mainSidebar.samsung-icon-only #bottomCapsuleDock {
        flex-direction: column !important;
        padding: 6px !important;
        border-radius: 9999px !important;
        width: 52px !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
        margin-left: auto !important;
        margin-right: auto !important;
        gap: 8px !important;
      }

      /* EXPANDED MOBILE MODE: Full Frosted Glassmorphic Sidebar with Side Menu Names (278px wide) */
      #mainSidebar.samsung-expanded-names {
        width: 278px !important;
        max-width: calc(100vw - 24px) !important;
        padding: 12px !important;
      }
      #mainSidebar.samsung-expanded-names #upperSideMenuHeader {
        width: 100% !important;
        height: auto !important;
        padding: 7px 10px !important;
        border-radius: 9999px !important;
        justify-content: space-between !important;
        align-items: center !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
      }
      #mainSidebar.samsung-expanded-names #expandSidebarNamesBtn {
        width: 34px !important;
        height: 34px !important;
        background: var(--color-primary) !important;
        color: #FFFFFF !important;
      }
      #mainSidebar.samsung-expanded-names #sideMenuNavPill {
        width: 100% !important;
        padding: 8px !important;
        border-radius: 26px !important;
        align-items: stretch !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
      }
      #mainSidebar.samsung-expanded-names .sidebar-nav-btn {
        width: 100% !important;
        height: 44px !important;
        padding: 4px 14px 4px 4px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
      }
      #mainSidebar.samsung-expanded-names .sidebar-nav-btn .samsung-squircle {
        width: 36px !important;
        height: 36px !important;
        border-radius: 9999px !important;
        flex-shrink: 0 !important;
      }
      #mainSidebar.samsung-expanded-names .sidebar-nav-label-wrap {
        display: block !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
      }
      #mainSidebar.samsung-expanded-names #bottomCapsuleDock {
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 8px !important;
        border-radius: 24px !important;
        width: 100% !important;
        background: var(--side-menu-pill-glass) !important;
        backdrop-filter: blur(26px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
        border: 1.5px solid var(--side-menu-border) !important;
        box-shadow: var(--side-menu-shadow) !important;
        gap: 6px !important;
      }
      #mainSidebar.samsung-expanded-names #openSettingsBtn,
      #mainSidebar.samsung-expanded-names #openProfileBtn {
        width: 100% !important;
        height: 44px !important;
        justify-content: flex-start !important;
        padding: 4px 14px 4px 4px !important;
        border-radius: 9999px !important;
        gap: 10px !important;
      }
      #mainSidebar.samsung-expanded-names .expanded-only {
        display: block !important;
      }
      #mainSidebar.samsung-expanded-names .expanded-flex {
        display: flex !important;
      }
      #mainSidebar.samsung-expanded-names .expanded-grid {
        display: grid !important;
      }
    }

    /* True Frosted Glassmorphism Classes for Side Menu Pills & Icon Buttons */
    .side-menu-glass {
      background: var(--side-menu-pill-glass) !important;
      backdrop-filter: blur(26px) saturate(190%) !important;
      -webkit-backdrop-filter: blur(26px) saturate(190%) !important;
      border: 1.5px solid var(--side-menu-border) !important;
      box-shadow: var(--side-menu-shadow) !important;
    }

    .side-menu-icon-glass {
      background: var(--side-menu-btn-glass);
      backdrop-filter: blur(14px) saturate(165%);
      -webkit-backdrop-filter: blur(14px) saturate(165%);
      border: 1px solid var(--side-menu-border);
      box-shadow: 0 2px 8px rgba(10, 9, 9, 0.05), inset 0 1px 1px rgba(255, 255, 255, 0.75);
    }

    /* Desktop Expandable Side Menu Names Support */
    @media (min-width: 1024px) {
      #mainSidebar {
        transition: width 0.38s cubic-bezier(0.22, 1, 0.36, 1), transform 0.38s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease;
      }
      #mainSidebar.desktop-expanded {
        width: 256px !important;
      }
      #mainSidebar.desktop-expanded #sideMenuNavPill {
        border-radius: 26px !important;
        padding: 8px !important;
        width: 100% !important;
        align-items: stretch !important;
      }
      #mainSidebar.desktop-expanded .sidebar-nav-btn {
        width: 100% !important;
        height: 44px !important;
        justify-content: flex-start !important;
        padding: 4px 14px 4px 4px !important;
        border-radius: 9999px !important;
        gap: 10px !important;
      }
      #mainSidebar.desktop-expanded .sidebar-nav-btn .samsung-squircle {
        width: 36px !important;
        height: 36px !important;
        border-radius: 9999px !important;
        flex-shrink: 0 !important;
      }
      #mainSidebar.desktop-expanded .sidebar-nav-label-wrap {
        display: block !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
      }
      #mainSidebar.desktop-expanded #upperDesktopMenuHeader {
        display: flex !important;
      }
      #mainSidebar.desktop-expanded #bottomCapsuleDock {
        width: 100% !important;
        border-radius: 24px !important;
        padding: 8px !important;
        align-items: stretch !important;
      }
      #mainSidebar.desktop-expanded #openSettingsBtn,
      #mainSidebar.desktop-expanded #openProfileBtn {
        width: 100% !important;
        height: 44px !important;
        justify-content: flex-start !important;
        padding: 4px 14px 4px 4px !important;
        border-radius: 9999px !important;
        gap: 10px !important;
      }
      #mainSidebar.desktop-expanded .desktop-expanded-label {
        display: block !important;
      }
    }

    /* Samsung Squircle Icon Badge */
    .samsung-squircle {
      width: 38px;
      height: 38px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: transform 0.22s cubic-bezier(0.22, 1, 0.36, 1), background-color 0.22s ease, color 0.22s ease, box-shadow 0.22s ease;
    }

    .sidebar-nav-btn:hover .samsung-squircle {
      transform: scale(1.07);
    }

    /* Samsung Dotted Divider (matches 12341.png) */
    .samsung-dotted-divider {
      border-top: 2px dotted rgba(var(--color-primary-rgb), 0.45);
      margin: 4px 8px;
    }

    .drawer-card {
      background: var(--drawer-item-bg);
      border: 1px solid var(--glass-border);
      box-shadow: 0 2px 10px rgba(10, 9, 9, 0.03);
    }

    .icon-badge {
      background: var(--icon-circle-bg);
      border: 1px solid var(--glass-border);
      box-shadow: 0 2px 6px rgba(10, 9, 9, 0.04);
      color: var(--text-primary);
      transition: all 0.2s ease;
    }

    .action-arrow-btn {
      background: var(--icon-circle-bg);
      border: 1px solid var(--glass-border);
      box-shadow: 0 2px 6px rgba(10, 9, 9, 0.04);
      color: var(--text-primary);
      transition: all 0.2s ease;
    }

    .action-arrow-btn:hover {
      transform: translate(1px, -1px) scale(1.05);
      background: var(--sidebar-active-bg);
      color: #FFFFFF;
      border-color: var(--sidebar-active-bg);
    }

    /* Dynamic AI Assistant Button */
    .ai-assistant-btn {
      background: var(--ai-btn-grad);
      color: var(--ai-btn-text);
      border: 1.5px solid rgba(255, 255, 255, 0.92);
      box-shadow: 0 6px 18px var(--ai-btn-shadow), inset 0 1px 2px rgba(255, 255, 255, 0.85);
      transition: all 0.22s ease;
    }

    .ai-assistant-btn:hover {
      transform: translateY(-1px);
      filter: brightness(1.04);
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 4px;
      height: 4px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(var(--color-primary-rgb), 0.35);
      border-radius: 9999px;
    }

    /* Card Search Highlight */
    .card-dimmed {
      opacity: 0.22;
      filter: grayscale(65%);
      transform: scale(0.985);
      pointer-events: none;
    }

    /* Bar Hover Transitions */
    .chart-bar {
      transition: height 0.32s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.25s ease, transform 0.15s ease, opacity 0.2s ease;
    }
    .chart-bar:hover {
      transform: scaleY(1.06);
      filter: brightness(1.08);
    }

    /* Pulse dot for live telemetry */
    @keyframes pulse-ring {
      0% { transform: scale(0.85); opacity: 0.9; }
      70% { transform: scale(1.9); opacity: 0; }
      100% { transform: scale(1.9); opacity: 0; }
    }
    .live-dot::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 9999px;
      background-color: var(--color-primary);
      animation: pulse-ring 2s infinite;
    }
  </style>
</head>
<body class="antialiased selection:bg-[var(--color-primary)] selection:text-white lg:h-screen lg:w-screen lg:overflow-hidden">

  <!-- MOBILE / TABLET SAMSUNG EDGE PULL-HANDLE TAB (Tap left edge to smoothly open Samsung Shortcut Menu!) -->
  <button
    id="samsungEdgeHandle"
    type="button"
    class="fixed left-0 top-[36%] z-30 lg:hidden w-2.5 h-20 rounded-r-full bg-[var(--color-primary)]/75 backdrop-blur-md border border-l-0 border-white/70 shadow-lg flex items-center justify-center transition-all duration-300 hover:w-4 active:scale-95"
    title="Open Samsung Edge Side Menu"
    aria-label="Open Side Menu Handle"
  >
    <span class="w-0.5 h-8 rounded-full bg-white/90"></span>
  </button>

  <!-- MOBILE / TABLET SIDEBAR BACKDROP OVERLAY (Covers Navbar z-30 when Sidebar z-50 is open!) -->
  <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 backdrop-blur-[5px] z-40 hidden lg:hidden transition-opacity duration-300 opacity-0" aria-hidden="true"></div>

  <!-- MAIN WRAPPER: Fixed 100vh x 100vw on Desktop (Zero Vertical/Horizontal Scroll), Smooth Scroll with Fixed Top Navbar on Mobile -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-16 sm:pt-20 lg:pt-3.5 pb-5 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col gap-3 lg:gap-3 overflow-x-hidden lg:overflow-hidden">

    <!-- ================= FIXED TOP NAVIGATION BAR (Pinned at z-30; Hides behind Sidebar when Sidebar is Open on Mobile) ================= -->
    <header id="topNavbar" class="fixed lg:static top-0 left-0 right-0 z-30 px-3 sm:px-5 lg:px-0 py-2.5 lg:py-0 bg-[var(--bg-mesh-3)]/85 lg:bg-transparent backdrop-blur-xl lg:backdrop-blur-none border-b border-white/50 lg:border-none shrink-0">
      <div class="max-w-[1600px] mx-auto flex items-center justify-between gap-2 sm:gap-3 w-full">

        <!-- Left: Hamburger Menu (Mobile/Tablet) + Brand Logo + Desktop Sidebar Toggle Button -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
          <!-- Mobile / Tablet Left Samsung Shortcut Menu Toggle (Always Hamburger Icon; Navbar hides behind Sidebar when open) -->
          <button id="mobileMenuBtn" type="button" class="lg:hidden w-10 h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition-all duration-300 shrink-0" aria-label="Open Navigation Sidebar" title="Open Samsung Shortcut Side Menu">
            <svg id="mobileMenuHamburgerIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
              <path id="hamburgerLine1" stroke-linecap="round" stroke-linejoin="round" d="M4 6.5h16" />
              <path id="hamburgerLine2" stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
              <path id="hamburgerLine3" stroke-linecap="round" stroke-linejoin="round" d="M4 17.5h11" />
            </svg>
          </button>

          <!-- Brand Logo Pill -->
          <button id="brandHomeBtn" type="button" class="flex items-center gap-2.5 group text-left focus:outline-none min-w-0" title="Reset to Full Greenhouse View">
            <div class="w-10 h-10 lg:w-10 lg:h-10 rounded-full glass-pill flex items-center justify-center shadow-sm group-hover:scale-105 transition shrink-0">
              <svg class="w-5 h-5 sm:w-5 sm:h-5 text-[var(--color-primary)]" viewBox="0 0 28 28" fill="none">
                <path d="M5 13.5L14 6L23 13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7.5 11.5V22H20.5V11.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 22H24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M11.5 22V16.5C11.5 15.67 12.17 15 13 15H15C15.83 15 16.5 15.67 16.5 16.5V22" stroke="currentColor" stroke-width="1.6"/>
                <path d="M17.5 7.5C19.5 5 22.8 5.2 23.2 5.2C23.2 5.2 23.5 8.5 21 10.5C19.2 11.8 17.2 10.8 17.5 7.5Z" fill="currentColor" stroke="currentColor" stroke-width="1.2"/>
              </svg>
            </div>
            <div class="min-w-0">
              <span class="text-[15px] sm:text-[17px] lg:text-[18px] font-bold tracking-tight text-[var(--text-primary)] block leading-tight truncate">AI Greenhouse</span>
              <span class="text-[10px] font-medium text-[var(--text-muted)] flex items-center gap-1.5 truncate">
                <span class="relative inline-flex w-1.5 h-1.5 rounded-full bg-[var(--color-primary)] live-dot shrink-0"></span>
                <span id="activeZoneHeaderLabel" class="truncate">Zone A • Hydroponic Bay</span>
              </span>
            </div>
          </button>

          <!-- Desktop Two-Icon Sidebar Control Pill (1: Open/Collapse Menu Text, 2: Close/Hide Menu Completely) -->
          <div id="desktopSidebarControlGroup" class="hidden lg:flex items-center gap-1 p-1 rounded-full glass-pill shrink-0">
            <!-- Icon 1: Menu Open with Text (1st click = Show Icons + Text; 2nd click = Icons Only) -->
            <button
              id="desktopSidebarToggleBtn"
              type="button"
              class="w-8 h-8 rounded-full side-menu-icon-glass flex items-center justify-center text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition shrink-0"
              title="Open Menu with Text / Collapse to Icons Only"
              aria-label="Toggle Menu Text Labels"
            >
              <svg id="desktopSidebarToggleIcon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
              </svg>
              <span id="desktopSidebarToggleText" class="sr-only">Hide Sidebar</span>
            </button>

            <!-- Icon 2: Menu Close / Hide Completely (Click = Fully Hide Side Menu) -->
            <button
              id="desktopHideSidebarBtn"
              type="button"
              class="w-8 h-8 rounded-full side-menu-icon-glass flex items-center justify-center text-[var(--text-primary)] hover:bg-[var(--color-alert)] hover:text-white transition shrink-0"
              title="Hide Side Menu Completely"
              aria-label="Hide Side Menu Completely"
            >
              <svg id="desktopHideSidebarIcon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Center (Desktop/Tablet >= 768px): Global Search Input + Voice Command Button -->
        <div class="hidden md:flex items-center gap-2 flex-1 max-w-xs lg:max-w-md mx-auto justify-center">
          <div class="relative flex-1 max-w-[230px] lg:max-w-[280px]">
            <svg class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/>
            </svg>
            <input
              id="dashboardSearchInput"
              type="search"
              placeholder="Search..."
              aria-label="Search greenhouse metrics"
              class="w-full h-10 pl-10 pr-7 rounded-full glass-pill text-xs lg:text-sm font-medium text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition"
            />
            <button id="clearSearchBtn" type="button" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)]" aria-label="Clear search">✕</button>
          </div>

          <!-- Circular Microphone Button -->
          <button id="voiceCommandBtn" type="button" class="w-10 h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] hover:scale-105 active:scale-95 transition shrink-0" title="Voice Assistant Command" aria-label="Voice Command">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3M8 22h8"/>
            </svg>
          </button>
        </div>

        <!-- Right: Desktop Controls (md+) + Mobile Single Right-Side User Icon Button (< md) -->
        <div class="flex items-center gap-2 shrink-0">

          <!-- DESKTOP / TABLET RIGHT BAR -->
          <div class="hidden md:flex items-center gap-2">
            <!-- Portal Link Button -->
            <button id="portalBackLink" type="button" class="h-10 px-3.5 rounded-full glass-pill flex items-center gap-1.5 text-xs font-semibold text-[var(--text-primary)] hover:bg-white/90 transition shrink-0" title="Return to Main Portal">
              <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
              <span>Portal</span>
            </button>

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
              <button id="colorPickerToggleBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 transition relative" title="Choose Theme & Font Color Combination" aria-label="Choose Theme Color">
                <span id="activeColorDot" class="w-4 h-4 rounded-full border-2 border-white shadow-sm" style="background-color: var(--color-primary);"></span>
              </button>

              <!-- Notifications Bell -->
              <button id="notificationsBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-primary)] hover:bg-black/5 transition relative" title="System Alerts & Telemetry Notifications" aria-label="Notifications">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                <span id="notifBadge" class="w-2 h-2 rounded-full bg-[var(--color-alert)] absolute top-1.5 right-1.5 ring-2 ring-white"></span>
              </button>
            </div>

            <!-- Theme Switcher Pill (Dark Moon / Light Sun) -->
            <div class="h-10 px-1.5 rounded-full glass-pill flex items-center gap-0.5">
              <button id="themeDarkBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" title="Dark Glass Theme" aria-label="Dark Mode">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                </svg>
              </button>
              <button id="themeLightBtn" type="button" class="w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm transition" title="Light Glass Theme" aria-label="Light Glass Mode">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="4"/>
                  <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- MOBILE RIGHT-SIDE USER ICON BUTTON (Clicking opens the downside menu panel) -->
          <button
            id="mobileUserMenuBtn"
            type="button"
            class="md:hidden h-10 pl-1.5 pr-2.5 rounded-full glass-pill flex items-center gap-1.5 text-[var(--text-primary)] hover:scale-105 active:scale-95 transition relative"
            aria-label="Open User & Quick Actions Menu"
            title="Open Quick Actions & User Menu"
          >
            <img
              src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA=="
              alt="User Menu"
              class="w-7 h-7 rounded-full object-cover ring-1 ring-[var(--color-primary)]"
            />
            <svg id="mobileUserMenuChevron" class="w-3.5 h-3.5 text-[var(--text-secondary)] transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
            </svg>
            <span class="w-2 h-2 rounded-full bg-[var(--color-alert)] absolute top-1 right-1 ring-1 ring-white"></span>
          </button>
        </div>
      </div>

      <!-- MOBILE DOWNSIDE COLLAPSIBLE MENU PANEL (Opens when clicking the right-side User Icon on Mobile!) -->
      <div id="mobileSearchContainer" class="hidden md:hidden mt-2.5 pt-2.5 border-t border-black/10 space-y-2.5">
        <!-- Row A: Search Input + Voice Button -->
        <div class="flex items-center gap-2">
          <div class="relative flex-1 min-w-0">
            <svg class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/>
            </svg>
            <input
              id="mobileSearchInput"
              type="search"
              placeholder="Search greenhouse metrics..."
              aria-label="Mobile search metrics"
              class="w-full h-10 pl-9 pr-3 rounded-full glass-pill text-xs font-medium text-[var(--text-primary)] placeholder:text-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
            />
          </div>
          <button id="mobileVoiceBtn" type="button" class="w-10 h-10 rounded-full glass-pill flex items-center justify-center text-[var(--text-primary)] shrink-0" aria-label="Mobile Voice Search" title="Voice Assistant">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3"/>
            </svg>
          </button>
        </div>

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

      <!-- DESKTOP DYNAMIC COLOR PICKER POPOVER -->
      <div id="colorPickerPopover" class="hidden fixed sm:absolute right-3 sm:right-20 top-16 w-72 glass-card p-4 z-50 shadow-2xl border border-white/80">
        <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-black/10">
          <div>
            <span class="font-bold text-xs text-[var(--text-primary)] block">Theme & Font Color Studio</span>
            <span class="text-[10px] text-[var(--text-muted)]">Transforms UI, charts & typography</span>
          </div>
          <input id="desktopCustomColorInput" type="color" value="#10B981" class="w-8 h-8 rounded-lg cursor-pointer border border-black/10 bg-transparent p-0.5" title="Pick Custom Color" />
        </div>
        <div class="grid grid-cols-6 gap-2 mb-2">
          <button type="button" data-theme-color="#10B981" class="color-Preset-btn w-8 h-8 rounded-full bg-[#10B981] ring-2 ring-offset-1 ring-[#10B981]" title="Emerald Default (#10B981)" aria-label="Emerald"></button>
          <button type="button" data-theme-color="#729C51" class="color-Preset-btn w-8 h-8 rounded-full bg-[#729C51]" title="Leaf Green (#729C51)" aria-label="Leaf Green"></button>
          <button type="button" data-theme-color="#0284C7" class="color-Preset-btn w-8 h-8 rounded-full bg-[#0284C7]" title="Ocean Blue (#0284C7)" aria-label="Ocean Blue"></button>
          <button type="button" data-theme-color="#7C3AED" class="color-Preset-btn w-8 h-8 rounded-full bg-[#7C3AED]" title="Royal Violet (#7C3AED)" aria-label="Royal Violet"></button>
          <button type="button" data-theme-color="#E11D48" class="color-Preset-btn w-8 h-8 rounded-full bg-[#E11D48]" title="Sunset Rose (#E11D48)" aria-label="Sunset Rose"></button>
          <button type="button" data-theme-color="#D97706" class="color-Preset-btn w-8 h-8 rounded-full bg-[#D97706]" title="Amber Gold (#D97706)" aria-label="Amber Gold"></button>
        </div>
        <p id="activeHexReadout" class="text-[11px] font-semibold text-[var(--text-secondary)] text-center mt-2">Active Palette: #10B981</p>
      </div>

      <!-- Notifications Dropdown Popover -->
      <div id="notificationsPopover" class="hidden fixed sm:absolute right-3 sm:right-6 top-16 w-[calc(100vw-24px)] max-w-sm glass-card p-4 z-50 shadow-2xl border border-white/80">
        <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-black/10">
          <div class="flex items-center gap-2">
            <span class="font-bold text-sm text-[var(--text-primary)]">Greenhouse Telemetry Alerts</span>
            <span id="notifCountPill" class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-[var(--color-alert)]/15 text-[var(--color-alert)]">3 New</span>
          </div>
          <button id="markAllReadBtn" type="button" class="text-xs font-semibold text-[var(--color-primary)] hover:underline">Mark read</button>
        </div>
        <div id="notifList" class="space-y-2.5 max-h-64 overflow-y-auto text-xs">
          <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
            <span class="w-2 h-2 rounded-full bg-[var(--color-alert)] mt-1 shrink-0"></span>
            <div>
              <p class="font-semibold text-[var(--text-primary)]">Peak Lighting Draw Detected (10:00)</p>
              <p class="text-[var(--text-muted)] mt-0.5">Power consumption reached 144 kWh (Lighting: 112 kWh). AI suggests 8% dimming.</p>
            </div>
          </div>
          <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] mt-1 shrink-0"></span>
            <div>
              <p class="font-semibold text-[var(--text-primary)]">Harvest AI: 80% Good Growth Reached</p>
              <p class="text-[var(--text-muted)] mt-0.5">Zone A hydroponic lettuce biomass index hit 0.70 on July 23 peak window.</p>
            </div>
          </div>
          <div class="p-2.5 rounded-xl drawer-card flex items-start gap-2.5">
            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] mt-1 shrink-0"></span>
            <div>
              <p class="font-semibold text-[var(--text-primary)]">Air CO₂ Enrichment Optimal (900 ppm)</p>
              <p class="text-[var(--text-muted)] mt-0.5">Stepped dosing stabilized at 16:47 for maximum photosynthetic uptake.</p>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Active Filter Banner -->
    <div id="activeFilterBanner" class="hidden items-center justify-between px-4 py-1.5 rounded-2xl glass-pill text-xs font-medium shrink-0">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
        <span id="activeFilterText">Showing all greenhouse telemetry modules</span>
      </div>
      <button id="resetFilterBtn" type="button" class="font-bold text-[var(--color-primary)] hover:underline">Show All Widgets</button>
    </div>

    <!-- ================= MAIN WORKSPACE (SAMSUNG SHORTCUT SIDEBAR + 100vh BENTO GRID) ================= -->
    <div class="flex-1 min-h-0 flex items-stretch gap-3.5 lg:gap-4 relative">

      <!-- =========================================================================
           LEFT SIDE MENU: FLOATING SAMSUNG EDGE SHORTCUT CAPSULE ON MOBILE (<1024px)
           - Default State: samsung-icon-only (76px wide, icons only like 12341.png)
           - Expand Button Near Close (✕) Icon: Expands to samsung-expanded-names (268px)
             showing full Side Menu Names without truncation!
           ========================================================================= -->
      <aside
        id="mainSidebar"
        aria-label="Dashboard Side Menu"
        class="samsung-menu-closed samsung-icon-only fixed lg:static inset-y-0 my-auto h-fit max-h-[calc(100dvh-20px)] lg:max-h-none lg:h-full left-[10px] z-50 w-[72px] lg:w-[56px] p-2.5 lg:py-2 lg:px-0 flex flex-col justify-between items-center gap-2.5 lg:gap-3 shrink-0 overflow-y-auto lg:overflow-visible select-none"
      >
        <!-- 1. UPPER CONTROL HEADER PILL (Mobile Only: Expand-Full-Sidebar Icon » RIGHT NEAR Single Close ✕ Icon) -->
        <div id="upperSideMenuHeader" class="samsung-shortcut-item side-menu-glass flex lg:hidden items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-full w-full" style="--stagger: 0;">
          <!-- Left Brand & Active Menu Title (Visible when Expanded with Side Menu Names) -->
          <div class="expanded-only expanded-flex items-center gap-2 min-w-0 flex-1">
            <div class="w-7 h-7 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center shadow-sm shrink-0">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/>
                <rect x="14" y="14" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <span class="text-[9px] font-extrabold uppercase tracking-wider text-[var(--color-primary)] block leading-none">Side Menu</span>
              <p id="upperSideMenuActiveLabel" class="font-bold text-[11.5px] text-[var(--text-primary)] leading-tight truncate mt-0.5">Dashboard Overview</p>
            </div>
          </div>

          <!-- Single Expand / Collapse Full Menu Button in Responsive Top Pill (Close ✕ icon removed as requested) -->
          <div class="flex items-center justify-center shrink-0">
            <button
              id="expandSidebarNamesBtn"
              type="button"
              class="w-9 h-9 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center hover:brightness-105 transition shadow-sm shrink-0"
              aria-label="Toggle Full Sidebar with Side Menu Names"
              title="Open Full Menu with Text / Collapse to Icons"
            >
              <svg id="expandSidebarNamesIcon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Upper Desktop Header (Visible when desktop sidebar is expanded to show names) -->
        <div id="upperDesktopMenuHeader" class="side-menu-glass hidden items-center justify-between px-3 py-2 w-full rounded-2xl">
          <div class="min-w-0">
            <span class="text-[9px] font-extrabold uppercase tracking-wider text-[var(--color-primary)] block">Side Menu</span>
            <span id="desktopUpperActiveName" class="text-xs font-bold text-[var(--text-primary)] truncate block">Dashboard Overview</span>
          </div>
          <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
        </div>

        <!-- 2. MAIN WHITE VERTICAL SIDE MENU PILL (1:1 Match with 11968.png: 6 Circular Side Menu Icons in a Frosted White Vertical Pill) -->
        <div class="my-auto flex flex-col items-center w-full">
          <nav id="sideMenuNavPill" class="desktop-pill-nav side-menu-glass rounded-full p-1.5 flex flex-col items-center gap-1.5 transition-all duration-300">

            <!-- Shortcut 1: Dashboard Overview (Active = Solid Green Circle) -->
            <button type="button" data-nav="all" data-menu-title="Dashboard Overview" style="--stagger: 1;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition bg-[var(--sidebar-active-bg)] text-white shadow-sm" title="Dashboard Overview">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10.5Z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 14c1.5-2 4.5-2 6 0"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Dashboard Overview</span>
              </span>
            </button>

            <!-- Shortcut 2: Water & Irrigation -->
            <button type="button" data-nav="irrigation" data-menu-title="Water & Irrigation" style="--stagger: 2;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition" title="Water & Irrigation">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Water & Irrigation</span>
              </span>
            </button>

            <!-- Shortcut 3: Harvest & Growth Rate -->
            <button type="button" data-nav="growth" data-menu-title="Harvest & Growth Rate" style="--stagger: 3;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition" title="Harvest & Growth Rate">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Harvest & Growth Rate</span>
              </span>
            </button>

            <!-- Shortcut 4: Climate & EC Setpoints -->
            <button type="button" data-nav="controls" data-menu-title="Climate & EC Setpoints" style="--stagger: 4;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition" title="Climate & EC Setpoints">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="2" x2="6" y1="14" y2="14"/><line x1="10" x2="14" y1="8" y2="8"/><line x1="18" x2="22" y1="16" y2="16"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Climate & EC Setpoints</span>
              </span>
            </button>

            <!-- Shortcut 5: Energy & CO2 Analytics -->
            <button type="button" data-nav="energy" data-menu-title="Energy & CO₂ Analytics" style="--stagger: 5;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition" title="Energy & CO₂ Analytics">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                  <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M9 14h6M9 18h6M9 10h2"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Energy & CO₂ Analytics</span>
              </span>
            </button>

            <!-- Shortcut 6: Switch Greenhouse Zone -->
            <button type="button" data-nav="zones" data-menu-title="Switch Greenhouse Zone" style="--stagger: 6;" class="samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition" title="Switch Greenhouse Camera Zones">
              <span class="samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                  <line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/>
                </svg>
              </span>
              <span class="sidebar-nav-label-wrap expanded-only lg:hidden min-w-0 flex-1 pr-2">
                <span class="sidebar-nav-label font-bold text-[12.5px] tracking-tight block leading-tight whitespace-nowrap">Switch Greenhouse Zone</span>
              </span>
            </button>
          </nav>

          <!-- Hidden auxiliary controls preserved for DOM integrity -->
          <div class="hidden" aria-hidden="true">
            <button id="drawerAiAssistantBtn" type="button">Open AI Greenhouse Assistant</button>
            <div id="drawerThemeCard">
              <input id="drawerCustomColorInput" type="color" value="#10B981" />
              <button type="button" id="drawerLightBtn">Glass Light</button>
              <button type="button" id="drawerDarkBtn">Dark Glass</button>
            </div>
            <button id="toggleCapsuleLayoutBtn" type="button" aria-label="Toggle Capsule Layout"></button>
            <button id="closeMobileSidebarBtn" type="button" aria-label="Close Side Menu"><span class="sr-only">Hide </span>✕</button>
          </div>
        </div>

        <!-- 3. BOTTOM WHITE VERTICAL PILL SET AT THE BOTTOM NEAR SIDE MENU (1. Setting Icon on Top, 2. One User Icon in White Directly Under Setting Icon — 1:1 Match with 11968.png) -->
        <div id="bottomCapsuleDock" class="samsung-shortcut-item side-menu-glass rounded-full p-1.5 flex flex-col items-center justify-center gap-2 transition-all duration-300" style="--stagger: 7;">
          <!-- 1. Setting Icon (Top of bottom white capsule, under the side menu icons) -->
          <button id="openSettingsBtn" type="button" class="w-10 h-10 rounded-full side-menu-icon-glass text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white flex items-center justify-center gap-2.5 transition shrink-0" title="Theme Color & Dashboard Settings" aria-label="Settings">
            <span class="w-9 h-9 rounded-full flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.1" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0-2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
              </svg>
            </span>
            <span class="expanded-only desktop-expanded-label hidden text-xs font-bold text-[var(--text-primary)] whitespace-nowrap pr-2">Settings & Theme</span>
          </button>

          <!-- 2. One User Icon in White (Directly under the Setting icon at the bottom of the side menu) -->
          <button id="openProfileBtn" type="button" class="w-10 h-10 rounded-full side-menu-icon-glass text-[var(--text-primary)] flex items-center justify-center gap-2.5 focus:outline-none group hover:ring-2 hover:ring-[var(--color-primary)] transition shrink-0" title="Operator Profile: Imran Jakir" aria-label="User Profile">
            <span class="w-9 h-9 rounded-full bg-white text-[#0A0909] border border-black/10 shadow-sm flex items-center justify-center overflow-hidden ring-2 ring-white shrink-0 relative">
              <img
                src="data:image/webp;base64,UklGRoADAABXRUJQVlA4IHQDAABQEwCdASpIAEgAPk0eikQioaEa28UAKATEsYBbQcmm2JIfYeZH4TL9JZ4SCKj80FL5PkxxSUoHsWj/akvbPVMgC2UCoSYtTnPzu6eNKwl6JJQVjfpfTiQb736NIgoPlkD7s4CKzca9Q8en5mOD9Mi3W9iHyIkhYapixdNXQE5VMncuCPFVGa/xGNb+A5v1JTIzpIM8RA1y6DpNPdhspwXL4GGAAP79elCCa5mpT5tfH+3pn5r9kKncStykW9LpNOVg3Z744eXfA79u1AR9E9L9b7Hu2+iK2nt7piU52Ol8VJUJSMtR+/74ZJ+BuQH0usp0gUAaabq21xYnMYEVB1C/WQVzhqfeKXWj+7K20eBSOqgeazhy7hJ4FqLkDMlosit692wRUqmiO/veeTwgvryJOCros8BjaHQMhAcsuAKvhNOwXnTCksVRPvWA1duJR2m/JuDYcPykBTdTAdpYgK8EI8aivUqra+7zwr0uiI/IeXDsi4B2xr0wMjQ2CkSni5TBwPs4jZ9MY0S4Yb2PuqFXAnIjA5z4rXNu16LUZAKzkiI1ZqUJPyb2w+Y1kWOUBrp/w2vJNVQLKHa4OXMbFKDSOkw/lWZPvIAJv84Lurm4uwuQ6SnsGWV6BzPNpLwsPRyfNAN7jB9q9ZVh1/XJGKEhPtXPg7JkbPCwVAM1mGZ2Zi3Srf6aryNWXuhKys4tPq6fKV9aGVoM/hhw0e+AhwdudgJMZce5EgZQ0M04iJtd30ez4j4f1iyepx+C39HRytHp1vyzTDqSagmY//TN/hqvefyz6tccSg1LHP1jbTYkJfuui9VZrby176zog/JP1V6Z/46FNBGx1bHqJheRESb1YUZp1pnUTOD5ek+kBZFqHZykmwVr/j7RpGG2YRi2GuJyXfnGDZ9WgDuUOeRBRuBoGVdl5wq0vK6nigwGCJGl2l69HGcEtxQMraHQ9Mjetnl75dNRbCeriN4Zvo7MLbeC6n7WJCze/v5Je8tYFXX16krLyk/YJc0W6xqiPk2aU6WjzY5u8ts5q6EgQInUaFMRDfEgdnWHvkAxc4ZVKrTbXROac2nmY2q5dx9P6UPUMIemzorSX4h8x6kIsbgSR62N793MdzUfKkyisCi78OmhpOmXMK4DFTHzvECGPSEwBubP8ZDtqW8//Me2mDvQ+NK8aQvAAA=="
                alt="Imran Jakir Profile"
                class="w-full h-full object-cover rounded-full"
              />
            </span>
            <div class="expanded-only desktop-expanded-label hidden text-left min-w-0 pr-2">
              <p class="text-[11.5px] font-bold text-[var(--text-primary)] leading-tight whitespace-nowrap">Imran Jakir</p>
              <p class="text-[9.5px] text-[var(--text-accent)] font-semibold whitespace-nowrap">Lead Agronomist</p>
            </div>
          </button>
        </div>
      </aside>

      <!-- ================= BENTO GRID DASHBOARD (Fits 100% inside 100vh on Desktop!) ================= -->
      <main id="bentoGrid" class="flex-1 min-h-0 min-w-0 grid grid-cols-1 lg:grid-cols-12 lg:grid-rows-[1.32fr_1fr] gap-3.5 lg:gap-3.5 transition-all duration-300">

        <!-- 1. HERO GREENHOUSE VISUAL + FLOATING HARVEST AI WIDGET (Spans 8 cols, Row 1 on Desktop) -->
        <section
          id="cardHero"
          data-category="growth zones"
          data-search="harvest ai growth camera greenhouse lettuce zone 80% good growth"
          class="lg:col-span-8 glass-card relative overflow-hidden min-h-[270px] sm:min-h-[320px] lg:min-h-0 p-3.5 flex flex-col items-center justify-center group"
        >
          <img
            id="heroCameraImg"
            src="data:image/webp;base64,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"
            alt="Live Smart Greenhouse Hydroponic Lettuce Feed"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.015]"
          />

          <!-- Camera Zone Switcher Bar -->
          <div class="relative sm:absolute sm:top-3 sm:left-3 z-10 flex items-center gap-1.5 mb-2.5 sm:mb-0 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button id="switchCamZone1Btn" type="button" class="px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
              <span>Cam 01: Main Bay</span>
            </button>
            <button id="switchCamZone2Btn" type="button" class="px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 transition">
              Cam 02: North Wing
            </button>
          </div>

          <!-- CENTER FLOATING GLASSMORPHISM CARD: HARVEST AI -->
          <div class="relative z-10 w-[225px] sm:w-[240px] rounded-[22px] harvest-glass-modal p-3.5 sm:p-4 transition-transform duration-300 hover:scale-[1.02] my-auto">
            <div class="flex items-center justify-between mb-1">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/>
                  </svg>
                </div>
                <h2 class="text-[13px] font-semibold tracking-tight text-[var(--text-primary)]">Harvest AI</h2>
              </div>
              <button type="button" data-modal-open="harvest" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Harvest AI Readiness" aria-label="Expand Harvest AI">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>

            <!-- Semi-Circular Radial Gauge (Dynamic Theme Gradient) -->
            <div class="relative flex flex-col items-center justify-center pt-1">
              <svg class="w-[180px] h-[98px] overflow-visible" viewBox="0 0 200 115">
                <defs>
                  <linearGradient id="cropCanopyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop id="gradStop1" offset="0%" stop-color="#9BE15D" />
                    <stop id="gradStop2" offset="50%" stop-color="#10B981" />
                    <stop id="gradStop3" offset="100%" stop-color="#3E6B22" />
                  </linearGradient>
                  <pattern id="cropArcPattern" patternUnits="userSpaceOnUse" width="200" height="120">
                    <image id="svgArcImg" href="" x="0" y="-40" width="220" height="160" preserveAspectRatio="xMidYMid slice" />
                  </pattern>
                </defs>
                <path
                  d="M 18 102 A 82 82 0 0 1 182 102"
                  fill="none"
                  stroke="rgba(148, 163, 150, 0.42)"
                  stroke-width="6"
                  stroke-linecap="round"
                />
                <path
                  id="harvestGaugePill"
                  d="M 108 21 A 82 82 0 0 1 154 42"
                  fill="none"
                  stroke="url(#cropCanopyGrad)"
                  stroke-width="18"
                  stroke-linecap="round"
                />
              </svg>

              <div class="-mt-11 text-center">
                <div id="harvestPercentValue" class="text-[26px] font-bold tracking-tight text-[var(--text-primary)] leading-none">80%</div>
                <div id="harvestStatusLabel" class="text-[11px] font-medium text-[var(--text-muted)] mt-1">Good Growth</div>
              </div>
            </div>
          </div>
        </section>

        <!-- 2. RIGHT COLUMN TOP & MIDDLE STACK (Spans 4 cols, Row 1 on Desktop: Water Flow + Power Consumption) -->
        <div class="lg:col-span-4 min-h-0 min-w-0 grid grid-rows-2 gap-3.5">

          <!-- 2A. WATER FLOW CARD -->
          <section
            id="cardWaterFlow"
            data-category="irrigation"
            data-search="water flow irrigation liters soem 6.750 L 3.541"
            class="glass-card p-3.5 sm:p-4 flex flex-col justify-between min-h-0"
          >
            <div class="flex items-center justify-between shrink-0">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                  </svg>
                </div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Water Flow</h2>
              </div>
              <button type="button" data-modal-open="water" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Configure Irrigation Flow" aria-label="Configure Water Flow">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>

            <div class="mt-1 flex items-end justify-between relative shrink-0">
              <div id="waterFlowValue" class="text-[22px] xl:text-[25px] font-bold tracking-tight text-[var(--text-primary)] leading-none">6.750 L</div>
              <div id="waterSubTag" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-semibold text-[var(--text-secondary)] shadow-sm mx-auto cursor-pointer" title="Click to toggle Zone / Soem label">
                Soem 3.541
              </div>
            </div>

            <div id="waterFlowBarsContainer" class="mt-2 flex-1 min-h-[42px] max-h-[64px] flex items-stretch justify-between gap-1.5 pt-0.5" role="slider" aria-label="Water Flow Level Bars">
              <!-- Populated dynamically via JS -->
            </div>
          </section>

          <!-- 2B. POWER CONSUMPTION CARD -->
          <section
            id="cardPower"
            data-category="energy"
            data-search="power consumption energy kwh lighting 144 112 20"
            class="glass-card p-3.5 sm:p-4 flex flex-col justify-between min-h-0"
          >
            <div class="flex items-center justify-between shrink-0">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                  </svg>
                </div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Power Consumption</h2>
              </div>
              <button type="button" data-modal-open="power" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Hourly Power Breakdown" aria-label="Expand Power Consumption">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>

            <div class="mt-1 flex flex-wrap items-center justify-between gap-1 shrink-0">
              <div class="flex items-baseline gap-1">
                <span id="powerTotalValue" class="text-[22px] xl:text-[25px] font-bold tracking-tight text-[var(--text-primary)] leading-none">144</span>
                <span class="text-[11px] font-semibold text-[var(--text-secondary)]">kWh</span>
              </div>
              <div class="flex items-center gap-1 flex-wrap">
                <button id="filterPowerLightingBtn" type="button" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-medium text-[var(--text-secondary)] hover:border-[var(--color-primary)] transition">
                  Lighting: <strong id="powerLightingVal" class="font-bold text-[var(--text-primary)]">112</strong> <span class="text-[9px]">kWh</span>
                </button>
                <button id="filterPowerOtherBtn" type="button" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-medium text-[var(--text-secondary)] hover:border-[var(--color-alert)] transition">
                  Other: <strong id="powerOtherVal" class="font-bold text-[var(--text-primary)]">20</strong> <span class="text-[9px]">kWh</span>
                </button>
              </div>
            </div>

            <div class="mt-1.5 flex-1 min-h-0 flex flex-col justify-end">
              <div id="powerChartBars" class="flex-1 min-h-[38px] max-h-[56px] flex items-end justify-between gap-[2px]">
                <!-- Populated by JS -->
              </div>
              <div class="flex items-center justify-between text-[9.5px] font-medium text-[var(--text-muted)] mt-1 px-0.5 shrink-0">
                <span>07:00</span>
                <span>08:00</span>
                <span>09:00</span>
                <span>10:00</span>
                <span>11:00</span>
                <span>12:00</span>
                <span>01:00</span>
              </div>
            </div>
          </section>
        </div>

        <!-- 3. BOTTOM LEFT: 2x2 MICRO-KPI GRID (Spans 4 cols, Row 2 on Desktop) -->
        <div class="lg:col-span-4 min-h-0 min-w-0 grid grid-cols-2 grid-rows-2 gap-3">

          <!-- 3A. Indoor Climate (23 °C) -->
          <section
            id="cardClimate"
            data-category="controls"
            data-search="indoor climate temperature 23 c"
            class="glass-card p-3 sm:p-3.5 flex flex-col justify-between min-h-[104px] lg:min-h-0 cursor-pointer group"
            data-modal-open="setpoints"
          >
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/>
                </svg>
              </div>
              <h2 class="text-[12.5px] font-semibold text-[var(--text-primary)] leading-snug truncate">Indoor Climate</h2>
            </div>
            <div class="flex items-end justify-between mt-2">
              <div class="flex items-baseline gap-1">
                <span id="kpiTempVal" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">23</span>
                <span class="text-xs font-semibold text-[var(--text-secondary)]">°C</span>
              </div>
              <button type="button" data-modal-open="setpoints" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center" aria-label="Adjust Indoor Climate">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </section>

          <!-- 3B. Air Moisture (68 %) -->
          <section
            id="cardMoisture"
            data-category="irrigation controls"
            data-search="air moisture humidity 68 %"
            class="glass-card p-3 sm:p-3.5 flex flex-col justify-between min-h-[104px] lg:min-h-0 cursor-pointer group"
            data-modal-open="setpoints"
          >
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                  <path stroke-linecap="round" d="M9.5 14.5c1.5 1.5 3.5 1.5 5 0"/>
                </svg>
              </div>
              <h2 class="text-[12.5px] font-semibold text-[var(--text-primary)] leading-snug truncate">Air Moisture</h2>
            </div>
            <div class="flex items-end justify-between mt-2">
              <div class="flex items-baseline gap-1">
                <span id="kpiMoistureVal" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">68</span>
                <span class="text-xs font-bold text-[var(--text-secondary)]">%</span>
              </div>
              <button type="button" data-modal-open="setpoints" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center" aria-label="Adjust Air Moisture">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </section>

          <!-- 3C. Light Level (15 H) -->
          <section
            id="cardLightLevel"
            data-category="controls energy"
            data-search="light level photoperiod 15 h hours"
            class="glass-card p-3 sm:p-3.5 flex flex-col justify-between min-h-[104px] lg:min-h-0 cursor-pointer group"
            data-modal-open="setpoints"
          >
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 18h6M10 22h4M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>
                </svg>
              </div>
              <h2 class="text-[12.5px] font-semibold text-[var(--text-primary)] leading-snug truncate">Light Level</h2>
            </div>
            <div class="flex items-end justify-between mt-2">
              <div class="flex items-baseline gap-1">
                <span id="kpiLightVal" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">15</span>
                <span class="text-xs font-bold text-[var(--text-secondary)]">H</span>
              </div>
              <button type="button" data-modal-open="setpoints" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center" aria-label="Adjust Light Photoperiod">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </section>

          <!-- 3D. EC Level (2.1 mS) -->
          <section
            id="cardEcLevel"
            data-category="controls irrigation"
            data-search="ec level electrical conductivity nutrients 2.1 ms"
            class="glass-card p-3 sm:p-3.5 flex flex-col justify-between min-h-[104px] lg:min-h-0 cursor-pointer group"
            data-modal-open="setpoints"
          >
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 20h10M10 20c5.5-2.5.8-6.4 3-10"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2z"/>
                </svg>
              </div>
              <h2 class="text-[12.5px] font-semibold text-[var(--text-primary)] leading-snug truncate">EC Level</h2>
            </div>
            <div class="flex items-end justify-between mt-2">
              <div class="flex items-baseline gap-1">
                <span id="kpiEcVal" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">2.1</span>
                <span class="text-xs font-semibold text-[var(--text-secondary)]">mS</span>
              </div>
              <button type="button" data-modal-open="setpoints" class="w-6 h-6 rounded-full action-arrow-btn flex items-center justify-center" aria-label="Adjust Nutrient EC Level">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </section>
        </div>

        <!-- 4. BOTTOM CENTER: GROWTH RATE CARD (Spans 4 cols, Row 2 on Desktop) -->
        <section
          id="cardGrowthRate"
          data-category="growth"
          data-search="growth rate 0.70 july 20 21 22 23 24 25 26"
          class="lg:col-span-4 glass-card p-3.5 sm:p-4 flex flex-col justify-between min-h-0 min-w-0"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                  <polyline points="16 7 22 7 22 13"/>
                </svg>
              </div>
              <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Growth Rate</h2>
            </div>
            <button type="button" data-modal-open="harvest" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Daily Growth Histogram" aria-label="Expand Growth Rate">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
              </svg>
            </button>
          </div>

          <div class="flex items-baseline justify-between mt-1 shrink-0">
            <div id="growthRateValue" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">0.70</div>
            <span id="growthHoverLabel" class="text-[10.5px] font-semibold text-[var(--text-accent)]">Peak: July 22–23</span>
          </div>

          <!-- 56-Bar High-Density Histogram (July 20 to July 26) -->
          <div class="mt-2 flex-1 min-h-0 flex flex-col justify-end">
            <div id="growthChartBars" class="flex-1 min-h-[70px] max-h-[105px] flex items-end justify-between gap-[2px]">
              <!-- Populated by JS -->
            </div>
            <div class="flex items-center justify-between text-[9.5px] font-medium text-[var(--text-muted)] mt-1.5 shrink-0">
              <button type="button" data-growth-day="0" class="growth-day-btn hover:text-[var(--text-primary)]">July 20</button>
              <button type="button" data-growth-day="1" class="growth-day-btn hover:text-[var(--text-primary)]">July 21</button>
              <button type="button" data-growth-day="2" class="growth-day-btn font-bold text-[var(--color-primary)]">July 22</button>
              <button type="button" data-growth-day="3" class="growth-day-btn font-bold text-[var(--color-primary)]">July 23</button>
              <button type="button" data-growth-day="4" class="growth-day-btn hover:text-[var(--text-primary)]">July 24</button>
              <button type="button" data-growth-day="5" class="growth-day-btn hover:text-[var(--text-primary)]">July 25</button>
              <button type="button" data-growth-day="6" class="growth-day-btn hover:text-[var(--text-primary)]">July 26</button>
            </div>
          </div>
        </section>

        <!-- 5. BOTTOM RIGHT: AIR CO2 CARD (Spans 4 cols, Row 2 on Desktop) -->
        <section
          id="cardAirCo2"
          data-category="energy controls"
          data-search="air co2 carbon dioxide 900 ppm 16:47"
          class="lg:col-span-4 glass-card p-3.5 sm:p-4 flex flex-col justify-between min-h-0 min-w-0"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="10" r="3"/>
                  <path stroke-linecap="round" d="M6 18h12M8 21h8"/>
                </svg>
              </div>
              <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Air CO₂</h2>
            </div>
            <button type="button" data-modal-open="co2" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Configure CO₂ Enrichment" aria-label="Expand Air CO2">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
              </svg>
            </button>
          </div>

          <div class="flex items-baseline gap-1.5 mt-1 shrink-0">
            <span id="co2PpmValue" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">900</span>
            <span class="text-xs font-semibold text-[var(--text-secondary)]">ppm</span>
          </div>

          <!-- Stepped Area Chart -->
          <div class="mt-2 flex-1 min-h-[85px] max-h-[120px] flex items-stretch gap-2">
            <div class="flex flex-col justify-between text-[9.5px] font-medium text-[var(--text-muted)] py-0.5 select-none shrink-0">
              <span>900</span>
              <span>800</span>
              <span>600</span>
              <span>400</span>
            </div>

            <div id="co2StepsContainer" class="flex-1 grid grid-cols-8 items-end pt-1 relative">
              <!-- Populated by JS -->
            </div>
          </div>
        </section>

      </main>
    </div>
  </div>

  <!-- ================= INTERACTIVE MODALS & DRAWERS ================= -->

  <!-- UNIVERSAL MODAL CONTAINER -->
  <div id="modalOverlay" class="fixed inset-0 bg-black/45 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div id="modalBox" class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-lg p-5 sm:p-6 shadow-2xl border border-white/80 relative max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
        <div class="flex items-center gap-2.5">
          <div id="modalIconBadge" class="w-9 h-9 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center font-bold">✨</div>
          <div>
            <h3 id="modalTitle" class="text-base font-bold text-[var(--text-primary)]">Module Details</h3>
            <p id="modalSubtitle" class="text-xs text-[var(--text-muted)]">Real-time AI telemetry & controls</p>
          </div>
        </div>
        <button id="closeModalBtn" type="button" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-sm font-bold hover:scale-105 transition" aria-label="Close Modal">✕</button>
      </div>
      <div id="modalBody" class="space-y-4 text-sm">
        <!-- Populated dynamically by JS -->
      </div>
    </div>
  </div>

  <!-- VOICE COMMAND LISTENING OVERLAY -->
  <div id="voiceModal" class="fixed inset-0 bg-black/50 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-sm p-6 text-center shadow-2xl">
      <div class="w-16 h-16 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center mx-auto mb-3 relative">
        <span class="absolute inset-0 rounded-full bg-[var(--color-primary)]/25 animate-ping"></span>
        <svg class="w-8 h-8 relative" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3M8 22h8"/>
        </svg>
      </div>
      <h3 class="text-base font-bold text-[var(--text-primary)]">AI Voice Command Active</h3>
      <p id="voiceTranscriptText" class="text-xs text-[var(--text-muted)] mt-1">Select a quick voice command below to execute:</p>
      <div class="mt-4 flex flex-col gap-2 text-xs font-semibold">
        <button type="button" data-voice-cmd="optimize-power" class="px-3.5 py-2.5 rounded-xl bg-[var(--color-primary)]/15 text-[var(--text-accent)] hover:bg-[var(--color-primary)] hover:text-white transition">
          "Optimize 10:00 peak power consumption"
        </button>
        <button type="button" data-voice-cmd="boost-water" class="px-3.5 py-2.5 rounded-xl bg-[var(--color-primary)]/15 text-[var(--text-accent)] hover:bg-[var(--color-primary)] hover:text-white transition">
          "Increase Water Flow to 7.500 L"
        </button>
        <button type="button" data-voice-cmd="cool-climate" class="px-3.5 py-2.5 rounded-xl bg-[var(--color-primary)]/15 text-[var(--text-accent)] hover:bg-[var(--color-primary)] hover:text-white transition">
          "Set Indoor Climate to 21 °C & 72% Moisture"
        </button>
      </div>
      <button id="closeVoiceModalBtn" type="button" class="mt-4 px-5 py-2 rounded-full glass-pill text-xs font-bold">Cancel</button>
    </div>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div id="toastBanner" class="fixed bottom-5 right-5 z-50 hidden items-center gap-2.5 px-4 py-3 rounded-2xl bg-[#0A0909] text-white text-xs font-semibold shadow-2xl border border-white/15 transition-all">
    <span class="w-2 h-2 rounded-full bg-[var(--color-primary-light)]"></span>
    <span id="toastMessage">Action completed</span>
  </div>

  <!-- ================= APPLICATION JAVASCRIPT ================= -->
  <script>
    (function () {
      // --- 1. CENTRAL GREENHOUSE STATE ---
      const state = {
        theme: 'light', // 'light' | 'dark'
        primaryHex: '#10B981',
        desktopSidebarMode: 'compact', // 'compact' | 'expanded' | 'hidden'
        sidebarOpenMobile: false,
        samsungExpandedNames: false, // DEFAULT on Mobile = false (Icon-Only Samsung Shortcut Capsule); true = Full Sidebar with Side Menu Names!
        mobileUserTrayOpen: false,
        activeNav: 'all',
        cameraZone: 1,
        zone1Src: '',
        harvestPercent: 80,
        harvestStatus: 'Good Growth',
        waterActiveBars: 8,
        waterSubLabelMode: 'soem',
        powerTotal: 144,
        powerLighting: 112,
        powerOther: 20,
        powerFilter: 'all',
        climateTemp: 23,
        airMoisture: 68,
        lightHours: 15,
        ecLevel: 2.1,
        growthRate: 0.70,
        growthHighlightDays: [2, 3],
        co2Ppm: 900,
        co2ActiveStep: 7,
        co2Steps: [
          { ppm: 620, time: '09:30', h: 36 },
          { ppm: 710, time: '10:45', h: 49 },
          { ppm: 785, time: '12:00', h: 61 },
          { ppm: 880, time: '13:15', h: 83 },
          { ppm: 850, time: '14:20', h: 76 },
          { ppm: 885, time: '15:30', h: 85 },
          { ppm: 835, time: '16:10', h: 71 },
          { ppm: 900, time: '16:47', h: 90 }
        ]
      };

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
        }, 3000);
      }

      // --- 2. DYNAMIC COLOR COMBINATION & FONT COLOR ENGINE ---
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
        const rgb = hexToRgb(hexColor);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        const root = document.documentElement;

        const lightShade = hslToHex(hsl.h, Math.min(95, hsl.s + 20), Math.min(82, hsl.l + 24));
        const darkShade = hslToHex(hsl.h, Math.min(90, hsl.s + 10), Math.max(18, hsl.l - 18));
        const complementaryPeak = hslToHex((hsl.h + 215) % 360, 85, 52);

        // Font colors tinted with the user's chosen hue!
        const fontPrimaryLight = hslToHex(hsl.h, Math.min(65, hsl.s + 15), 14);
        const fontSecondaryLight = hslToHex(hsl.h, Math.min(50, hsl.s), 28);
        const fontMutedLight = hslToHex(hsl.h, Math.min(35, hsl.s), 44);

        const fontPrimaryDark = hslToHex(hsl.h, Math.min(70, hsl.s), 95);
        const fontSecondaryDark = hslToHex(hsl.h, Math.min(55, hsl.s), 80);
        const fontMutedDark = hslToHex(hsl.h, Math.min(40, hsl.s), 64);

        root.style.setProperty('--color-primary', state.primaryHex);
        root.style.setProperty('--color-primary-dark', darkShade);
        root.style.setProperty('--color-primary-light', lightShade);
        root.style.setProperty('--color-primary-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
        root.style.setProperty('--bar-active', state.primaryHex);
        root.style.setProperty('--sidebar-active-bg', state.primaryHex);
        const isDefaultGreen = state.primaryHex === '#10B981' || state.primaryHex === '#729C51';
        root.style.setProperty('--bar-peak', isDefaultGreen ? '#EB5011' : complementaryPeak);
        root.style.setProperty('--color-alert', isDefaultGreen ? '#EB5011' : complementaryPeak);

        const aiTop = hslToHex(hsl.h, 95, 92);
        const aiMid = hslToHex(hsl.h, 92, 64);
        const aiBot = hslToHex(hsl.h, 85, 46);
        root.style.setProperty('--ai-btn-grad', `linear-gradient(180deg, ${aiTop} 0%, ${aiMid} 48%, ${aiBot} 100%)`);
        root.style.setProperty('--ai-btn-shadow', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.38)`);

        if (state.theme === 'dark') {
          root.style.setProperty('--bg-mesh-1', hslToHex((hsl.h + 30) % 360, 45, 10));
          root.style.setProperty('--bg-mesh-2', hslToHex(hsl.h, 50, 12));
          root.style.setProperty('--bg-mesh-3', hslToHex(hsl.h, 35, 5));
          root.style.setProperty('--bg-mesh-4', hslToHex(hsl.h, 55, 14));
          root.style.setProperty('--drawer-bg', `linear-gradient(160deg, rgba(22, 34, 26, 0.62) 0%, rgba(14, 22, 17, 0.44) 60%, rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.18) 100%)`);
          root.style.setProperty('--text-primary', fontPrimaryDark);
          root.style.setProperty('--text-secondary', fontSecondaryDark);
          root.style.setProperty('--text-muted', fontMutedDark);
          root.style.setProperty('--text-accent', lightShade);
        } else {
          root.style.setProperty('--bg-mesh-1', hslToHex((hsl.h + 45) % 360, 52, 86));
          root.style.setProperty('--bg-mesh-2', hslToHex(hsl.h, 48, 85));
          root.style.setProperty('--bg-mesh-3', hslToHex(hsl.h, 38, 95));
          root.style.setProperty('--bg-mesh-4', hslToHex(hsl.h, 55, 82));
          root.style.setProperty('--drawer-bg', `linear-gradient(160deg, rgba(255, 255, 255, 0.44) 0%, rgba(255, 255, 255, 0.24) 55%, rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.16) 100%)`);
          root.style.setProperty('--text-primary', fontPrimaryLight);
          root.style.setProperty('--text-secondary', fontSecondaryLight);
          root.style.setProperty('--text-muted', fontMutedLight);
          root.style.setProperty('--text-accent', darkShade);
        }

        const stop1 = document.getElementById('gradStop1');
        const stop2 = document.getElementById('gradStop2');
        const stop3 = document.getElementById('gradStop3');
        if (stop1) stop1.setAttribute('stop-color', lightShade);
        if (stop2) stop2.setAttribute('stop-color', state.primaryHex);
        if (stop3) stop3.setAttribute('stop-color', darkShade);

        ['desktopCustomColorInput', 'mobileCustomColorInput', 'drawerCustomColorInput'].forEach((id) => {
          const inp = document.getElementById(id);
          if (inp && inp.value.toUpperCase() !== state.primaryHex) inp.value = state.primaryHex;
        });
        const readout = document.getElementById('activeHexReadout');
        if (readout) readout.textContent = `Active Palette & Font: ${state.primaryHex}`;

        renderWaterFlowBars();
        renderPowerBars();
        renderGrowthBars();
        renderCo2Chart();
      }

      ['desktopCustomColorInput', 'mobileCustomColorInput', 'drawerCustomColorInput'].forEach((id) => {
        const inp = document.getElementById(id);
        if (inp) {
          inp.addEventListener('input', (e) => {
            applyCustomColorPalette(e.target.value);
          });
        }
      });

      document.querySelectorAll('.color-Preset-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const hex = btn.getAttribute('data-theme-color');
          document.querySelectorAll('.color-Preset-btn').forEach(b => b.classList.remove('ring-2', 'ring-offset-1'));
          btn.classList.add('ring-2', 'ring-offset-1');
          applyCustomColorPalette(hex);
          showToast(`Applied ${hex} Theme & Font Color Combination`);
        });
      });

      const colorPickerToggleBtn = document.getElementById('colorPickerToggleBtn');
      const colorPickerPopover = document.getElementById('colorPickerPopover');
      if (colorPickerToggleBtn && colorPickerPopover) {
        colorPickerToggleBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          colorPickerPopover.classList.toggle('hidden');
          const notifPop = document.getElementById('notificationsPopover');
          if (notifPop) notifPop.classList.add('hidden');
        });
      }

      // --- 3. RENDER WATER FLOW BARS (16 Pill Bars) ---
      function renderWaterFlowBars() {
        const container = document.getElementById('waterFlowBarsContainer');
        const valEl = document.getElementById('waterFlowValue');
        if (!container || !valEl) return;

        const liters = ((state.waterActiveBars / 8) * 6.75).toFixed(3);
        valEl.textContent = `${liters} L`;

        const totalBars = 16;
        const inactiveCount = totalBars - state.waterActiveBars;
        container.innerHTML = '';

        for (let i = 0; i < totalBars; i++) {
          const isGreen = i >= inactiveCount;
          const bar = document.createElement('button');
          bar.type = 'button';
          bar.className = 'flex-1 h-full flex items-center justify-center group focus:outline-none';
          bar.title = `Set water flow to ${totalBars - i} active bars (${(((totalBars - i) / 8) * 6.75).toFixed(3)} L)`;

          const pill = document.createElement('span');
          pill.className = 'w-[5px] sm:w-[6px] h-full rounded-full transition-all duration-200 group-hover:scale-y-105';
          pill.style.backgroundColor = isGreen ? 'var(--bar-active)' : 'var(--bar-inactive)';

          bar.appendChild(pill);
          bar.addEventListener('click', () => {
            state.waterActiveBars = totalBars - i;
            renderWaterFlowBars();
            showToast(`Water Flow updated to ${(((state.waterActiveBars) / 8) * 6.75).toFixed(3)} L`);
          });
          container.appendChild(bar);
        }
      }

      const waterSubTag = document.getElementById('waterSubTag');
      if (waterSubTag) {
        waterSubTag.addEventListener('click', () => {
          state.waterSubLabelMode = state.waterSubLabelMode === 'soem' ? 'zone' : 'soem';
          waterSubTag.textContent = state.waterSubLabelMode === 'soem' ? 'Soem 3.541' : 'Zone A: 3.541 L';
          showToast(`Sub-flow label switched to "${waterSubTag.textContent}"`);
        });
      }

      // --- 4. RENDER POWER CONSUMPTION BARS (34 Bars) ---
      const powerBarData = [
        { h: 48, type: 'mid', time: '07:00' },
        { h: 68, type: 'mid', time: '07:10' },
        { h: 55, type: 'mid', time: '07:20' },
        { h: 86, type: 'mid', time: '07:30' },
        { h: 74, type: 'mid', time: '07:40' },
        { h: 62, type: 'mid', time: '07:50' },
        { h: 66, type: 'mid', time: '08:00' },
        { h: 50, type: 'mid', time: '08:10' },
        { h: 52, type: 'low', time: '08:20' },
        { h: 52, type: 'low', time: '08:35' },
        { h: 52, type: 'low', time: '08:50' },
        { h: 52, type: 'low', time: '09:00' },
        { h: 52, type: 'low', time: '09:15' },
        { h: 52, type: 'low', time: '09:30' },
        { h: 38, type: 'peak', time: '09:45' },
        { h: 65, type: 'peak', time: '09:55' },
        { h: 58, type: 'peak', time: '10:00' },
        { h: 82, type: 'peak', time: '10:10' },
        { h: 94, type: 'peak', time: '10:20' },
        { h: 72, type: 'peak', time: '10:30' },
        { h: 44, type: 'peak', time: '10:40' },
        { h: 52, type: 'low', time: '10:50' },
        { h: 52, type: 'low', time: '11:00' },
        { h: 52, type: 'low', time: '11:15' },
        { h: 52, type: 'low', time: '11:30' },
        { h: 52, type: 'low', time: '11:45' },
        { h: 52, type: 'low', time: '11:55' },
        { h: 42, type: 'green', time: '12:05' },
        { h: 70, type: 'green', time: '12:15' },
        { h: 92, type: 'green', time: '12:25' },
        { h: 66, type: 'green', time: '12:35' },
        { h: 96, type: 'green', time: '12:45' },
        { h: 78, type: 'green', time: '12:55' },
        { h: 52, type: 'green', time: '01:00' }
      ];

      function renderPowerBars() {
        const container = document.getElementById('powerChartBars');
        if (!container) return;
        container.innerHTML = '';

        powerBarData.forEach((item) => {
          const bar = document.createElement('div');
          bar.className = 'chart-bar flex-1 rounded-full cursor-pointer';
          bar.style.height = `${item.h}%`;

          let color = 'var(--bar-inactive)';
          if (item.type === 'mid') color = 'var(--bar-mid)';
          if (item.type === 'peak') color = 'var(--bar-peak)';
          if (item.type === 'green') color = 'var(--bar-active)';

          if (state.powerFilter === 'lighting' && item.type === 'low') {
            bar.style.opacity = '0.3';
          } else if (state.powerFilter === 'other' && item.type !== 'peak') {
            bar.style.opacity = '0.25';
          }

          bar.style.backgroundColor = color;
          bar.title = `${item.time} — ${(item.h * 1.6).toFixed(1)} kW`;
          bar.addEventListener('click', () => {
            showToast(`Power @ ${item.time}: ${(item.h * 1.6).toFixed(1)} kW`);
          });
          container.appendChild(bar);
        });
      }

      const filterLightingBtn = document.getElementById('filterPowerLightingBtn');
      const filterOtherBtn = document.getElementById('filterPowerOtherBtn');
      if (filterLightingBtn) {
        filterLightingBtn.addEventListener('click', () => {
          state.powerFilter = state.powerFilter === 'lighting' ? 'all' : 'lighting';
          renderPowerBars();
          showToast(state.powerFilter === 'lighting' ? 'Highlighting Lighting Power (112 kWh)' : 'Showing Total Power Consumption');
        });
      }
      if (filterOtherBtn) {
        filterOtherBtn.addEventListener('click', () => {
          state.powerFilter = state.powerFilter === 'other' ? 'all' : 'other';
          renderPowerBars();
          showToast(state.powerFilter === 'other' ? 'Highlighting Peak HVAC/Other Load (20 kWh)' : 'Showing Total Power Consumption');
        });
      }

      // --- 5. RENDER GROWTH RATE 56-BAR HISTOGRAM (July 20 - July 26) ---
      const growthHeights = [
        38, 45, 58, 72, 60, 48, 36, 42,
        54, 65, 84, 70, 52, 40, 32, 46,
        38, 50, 44, 58, 62, 78, 86, 75,
        82, 92, 85, 98, 90, 80, 55, 62,
        48, 68, 82, 76, 68, 56, 45, 52,
        60, 44, 38, 55, 62, 50, 42, 35,
        40, 48, 42, 56, 64, 58, 50, 36
      ];
      const dayNames = ['July 20', 'July 21', 'July 22', 'July 23', 'July 24', 'July 25', 'July 26'];

      function renderGrowthBars() {
        const container = document.getElementById('growthChartBars');
        if (!container) return;
        container.innerHTML = '';

        growthHeights.forEach((h, idx) => {
          const dayIdx = Math.floor(idx / 8);
          const isHighlighted = (state.growthHighlightDays.includes(dayIdx) && idx >= 20 && idx <= 30) ||
            (state.growthHighlightDays.length === 1 && state.growthHighlightDays[0] === dayIdx);

          const bar = document.createElement('div');
          bar.className = 'chart-bar flex-1 rounded-t-full cursor-pointer';
          bar.style.height = `${h}%`;
          bar.style.backgroundColor = isHighlighted ? 'var(--bar-active)' : 'var(--bar-mid)';
          bar.style.opacity = isHighlighted ? '1' : '0.45';

          const rateVal = (0.45 + (h / 100) * 0.35).toFixed(2);
          bar.title = `${dayNames[dayIdx]} — Growth Rate: ${rateVal}`;

          bar.addEventListener('mouseenter', () => {
            const label = document.getElementById('growthHoverLabel');
            const mainVal = document.getElementById('growthRateValue');
            if (label) label.textContent = `${dayNames[dayIdx]} • ${rateVal} idx`;
            if (mainVal) mainVal.textContent = rateVal;
          });
          bar.addEventListener('mouseleave', () => {
            const label = document.getElementById('growthHoverLabel');
            const mainVal = document.getElementById('growthRateValue');
            if (label) label.textContent = 'Peak: July 22–23';
            if (mainVal) mainVal.textContent = state.growthRate.toFixed(2);
          });
          container.appendChild(bar);
        });
      }

      document.querySelectorAll('.growth-day-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          const day = parseInt(btn.getAttribute('data-growth-day'), 10);
          state.growthHighlightDays = [day];
          document.querySelectorAll('.growth-day-btn').forEach(b => b.classList.remove('font-bold', 'text-[var(--color-primary)]'));
          btn.classList.add('font-bold', 'text-[var(--color-primary)]');
          renderGrowthBars();
          showToast(`Highlighting Growth Rate for ${dayNames[day]}`);
        });
      });

      // --- 6. RENDER AIR CO2 STEPPED AREA CHART ---
      function renderCo2Chart() {
        const container = document.getElementById('co2StepsContainer');
        const ppmEl = document.getElementById('co2PpmValue');
        if (!container || !ppmEl) return;

        const rgb = hexToRgb(state.primaryHex);
        container.innerHTML = '';
        state.co2Steps.forEach((step, idx) => {
          const col = document.createElement('div');
          col.className = 'h-full flex flex-col justify-end cursor-pointer group relative';

          const box = document.createElement('div');
          box.className = 'w-full transition-all duration-300 relative overflow-hidden';
          box.style.height = `${step.h}%`;
          box.style.borderTop = `2.5px solid var(--bar-active)`;
          box.style.borderLeft = idx > 0 ? `1px solid rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.25)` : 'none';
          box.style.background = `repeating-linear-gradient(
            -45deg,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.18),
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.18) 4px,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.06) 4px,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.06) 8px
          )`;

          if (idx === state.co2ActiveStep) {
            const badge = document.createElement('span');
            badge.className = 'absolute top-1.5 left-1/2 -translate-x-1/2 text-[9px] font-bold text-[var(--text-primary)] bg-white/85 px-1 py-0.5 rounded shadow-sm';
            badge.textContent = step.time;
            box.appendChild(badge);
          }

          col.appendChild(box);
          col.title = `${step.time} — ${step.ppm} ppm CO₂`;
          col.addEventListener('click', () => {
            state.co2ActiveStep = idx;
            state.co2Ppm = step.ppm;
            ppmEl.textContent = step.ppm;
            renderCo2Chart();
            showToast(`Air CO₂ @ ${step.time}: ${step.ppm} ppm`);
          });
          container.appendChild(col);
        });
      }

      // --- 7. SAMSUNG EDGE SHORTCUT SIDE MENU (DEFAULT ICON-ONLY + EXPAND NEAR CLOSE ICON + HIDE NAVBAR BEHIND SIDEBAR) ---
      const mainSidebar = document.getElementById('mainSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');
      const portalBackLink = document.getElementById('portalBackLink');
      if (portalBackLink) {
        portalBackLink.addEventListener('click', (e) => {
          e.preventDefault();
          showToast('Returning to Smart Management Portal...');
        });
      }
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const samsungEdgeHandle = document.getElementById('samsungEdgeHandle');
      const closeMobileSidebarBtn = document.getElementById('closeMobileSidebarBtn');
      const expandSidebarNamesBtn = document.getElementById('expandSidebarNamesBtn');
      const expandSidebarNamesIcon = document.getElementById('expandSidebarNamesIcon');
      const toggleCapsuleLayoutBtn = document.getElementById('toggleCapsuleLayoutBtn');
      const desktopSidebarToggleBtn = document.getElementById('desktopSidebarToggleBtn');
      const desktopSidebarToggleText = document.getElementById('desktopSidebarToggleText');
      const desktopSidebarToggleIcon = document.getElementById('desktopSidebarToggleIcon');

      const mobileUserMenuBtn = document.getElementById('mobileUserMenuBtn');
      const mobileUserMenuChevron = document.getElementById('mobileUserMenuChevron');
      const mobileSearchContainer = document.getElementById('mobileSearchContainer');

      function applyMobileSidebarMode(expandedWithNames) {
        state.samsungExpandedNames = expandedWithNames;
        if (!mainSidebar) return;
        if (expandedWithNames) {
          mainSidebar.classList.remove('samsung-icon-only');
          mainSidebar.classList.add('samsung-expanded-names');
          if (expandSidebarNamesIcon) expandSidebarNamesIcon.classList.add('rotate-180');
          if (expandSidebarNamesBtn) expandSidebarNamesBtn.title = 'Collapse to Icon-Only Sidebar';
        } else {
          mainSidebar.classList.remove('samsung-expanded-names');
          mainSidebar.classList.add('samsung-icon-only');
          if (expandSidebarNamesIcon) expandSidebarNamesIcon.classList.remove('rotate-180');
          if (expandSidebarNamesBtn) expandSidebarNamesBtn.title = 'Show Full Sidebar with Side Menu Names';
        }
      }

      function openMobileSidebar() {
        state.sidebarOpenMobile = true;
        // Hide navbar behind sidebar & backdrop when sidebar is open
        document.body.classList.add('mobile-sidebar-active');
        // Always start in default Icon-Only mode when freshly opened (user can tap the icon near Close ✕ to expand full names)
        mainSidebar.classList.remove('samsung-menu-closed');
        mainSidebar.classList.add('samsung-menu-open');
        if (samsungEdgeHandle) samsungEdgeHandle.classList.add('-translate-x-full', 'opacity-0');
        sidebarBackdrop.classList.remove('hidden');
        requestAnimationFrame(() => {
          sidebarBackdrop.classList.remove('opacity-0');
        });
      }

      function closeMobileSidebar() {
        state.sidebarOpenMobile = false;
        document.body.classList.remove('mobile-sidebar-active');
        mainSidebar.classList.remove('samsung-menu-open');
        mainSidebar.classList.add('samsung-menu-closed');
        if (samsungEdgeHandle) samsungEdgeHandle.classList.remove('-translate-x-full', 'opacity-0');
        sidebarBackdrop.classList.add('opacity-0');
        setTimeout(() => sidebarBackdrop.classList.add('hidden'), 320);
      }

      if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
          if (state.sidebarOpenMobile) closeMobileSidebar();
          else openMobileSidebar();
        });
      }
      if (samsungEdgeHandle) {
        samsungEdgeHandle.addEventListener('click', openMobileSidebar);
      }
      if (closeMobileSidebarBtn) closeMobileSidebarBtn.addEventListener('click', closeMobileSidebar);
      if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeMobileSidebar);

      // Icon Near Close (✕) Button & Bottom 3x3 Grid Button: Toggle between Default Icon-Only Capsule & Full Sidebar with Side Menu Names!
      function toggleMobileFullSidebarNames() {
        applyMobileSidebarMode(!state.samsungExpandedNames);
        showToast(state.samsungExpandedNames ? 'Full Sidebar with Side Menu Names' : 'Icon-Only Shortcut Sidebar');
      }

      if (expandSidebarNamesBtn) {
        expandSidebarNamesBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          toggleMobileFullSidebarNames();
        });
      }
      if (toggleCapsuleLayoutBtn) {
        toggleCapsuleLayoutBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          toggleMobileFullSidebarNames();
        });
      }

      // Mobile Right-Side User Icon Toggle -> Opens/Closes the downside tray with all actions!
      if (mobileUserMenuBtn && mobileSearchContainer) {
        mobileUserMenuBtn.addEventListener('click', () => {
          state.mobileUserTrayOpen = !state.mobileUserTrayOpen;
          mobileSearchContainer.classList.toggle('hidden', !state.mobileUserTrayOpen);
          if (mobileUserMenuChevron) {
            mobileUserMenuChevron.classList.toggle('rotate-180', state.mobileUserTrayOpen);
          }
        });
      }

      const desktopHideSidebarBtn = document.getElementById('desktopHideSidebarBtn');
      const desktopHideSidebarIcon = document.getElementById('desktopHideSidebarIcon');

      function syncDesktopSidebarControls() {
        if (!mainSidebar) return;
        if (state.desktopSidebarMode === 'expanded') {
          mainSidebar.classList.remove('lg:hidden');
          mainSidebar.classList.add('desktop-expanded');
          if (desktopSidebarToggleIcon) desktopSidebarToggleIcon.classList.add('rotate-180');
          if (desktopSidebarToggleBtn) {
            desktopSidebarToggleBtn.classList.add('bg-[var(--color-primary)]', 'text-white');
            desktopSidebarToggleBtn.title = 'Click to Show Only Icons';
          }
          if (desktopHideSidebarBtn) {
            desktopHideSidebarBtn.classList.remove('bg-[var(--color-alert)]', 'text-white');
            desktopHideSidebarBtn.title = 'Hide Side Menu Completely';
          }
        } else if (state.desktopSidebarMode === 'hidden') {
          mainSidebar.classList.remove('desktop-expanded');
          mainSidebar.classList.add('lg:hidden');
          if (desktopSidebarToggleIcon) desktopSidebarToggleIcon.classList.remove('rotate-180');
          if (desktopSidebarToggleBtn) {
            desktopSidebarToggleBtn.classList.remove('bg-[var(--color-primary)]', 'text-white');
            desktopSidebarToggleBtn.title = 'Open Side Menu with Text';
          }
          if (desktopHideSidebarBtn) {
            desktopHideSidebarBtn.classList.add('bg-[var(--color-alert)]', 'text-white');
            desktopHideSidebarBtn.title = 'Restore Side Menu';
          }
        } else {
          // Default 'compact' (Icons Only)
          mainSidebar.classList.remove('lg:hidden', 'desktop-expanded');
          if (desktopSidebarToggleIcon) desktopSidebarToggleIcon.classList.remove('rotate-180');
          if (desktopSidebarToggleBtn) {
            desktopSidebarToggleBtn.classList.remove('bg-[var(--color-primary)]', 'text-white');
            desktopSidebarToggleBtn.title = 'Open Side Menu with Text';
          }
          if (desktopHideSidebarBtn) {
            desktopHideSidebarBtn.classList.remove('bg-[var(--color-alert)]', 'text-white');
            desktopHideSidebarBtn.title = 'Hide Side Menu Completely';
          }
        }
      }

      // Desktop Icon 1 (Open/Expand Menu): 1st click = Show Icons + Text; 2nd click = As-It-Is Icons Only
      if (desktopSidebarToggleBtn) {
        desktopSidebarToggleBtn.addEventListener('click', () => {
          if (state.desktopSidebarMode === 'expanded') {
            state.desktopSidebarMode = 'compact';
            syncDesktopSidebarControls();
            showToast('Side Menu: Icons Only');
          } else {
            state.desktopSidebarMode = 'expanded';
            syncDesktopSidebarControls();
            showToast('Side Menu Open with Full Text');
          }
        });
      }

      // Desktop Icon 2 (Close/Hide Menu): Fully hides the side menu (clicking again restores Icon-Only menu)
      if (desktopHideSidebarBtn) {
        desktopHideSidebarBtn.addEventListener('click', () => {
          if (state.desktopSidebarMode === 'hidden') {
            state.desktopSidebarMode = 'compact';
            syncDesktopSidebarControls();
            showToast('Side Menu Restored');
          } else {
            state.desktopSidebarMode = 'hidden';
            syncDesktopSidebarControls();
            showToast('Side Menu Hidden Completely');
          }
        });
      }

      // Sidebar Navigation Filtering + Upper Side Menu Name Update
      const navButtons = document.querySelectorAll('.sidebar-nav-btn');
      const activeFilterBanner = document.getElementById('activeFilterBanner');
      const activeFilterText = document.getElementById('activeFilterText');
      const resetFilterBtn = document.getElementById('resetFilterBtn');
      const upperSideMenuActiveLabel = document.getElementById('upperSideMenuActiveLabel');
      const desktopUpperActiveName = document.getElementById('desktopUpperActiveName');

      function setSidebarCategory(category) {
        state.activeNav = category;
        navButtons.forEach((btn) => {
          const isMatch = btn.getAttribute('data-nav') === category;
          const squircle = btn.querySelector('.samsung-squircle');

          if (isMatch) {
            btn.className = 'samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold transition bg-[var(--sidebar-active-bg)] text-white shadow-sm';
            if (squircle) squircle.className = 'samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-white shrink-0';

            const menuTitle = btn.getAttribute('data-menu-title') || 'Dashboard Overview';
            if (upperSideMenuActiveLabel) upperSideMenuActiveLabel.textContent = menuTitle;
            if (desktopUpperActiveName) desktopUpperActiveName.textContent = menuTitle;
          } else {
            btn.className = 'samsung-shortcut-item sidebar-nav-btn w-10 h-10 rounded-full flex items-center justify-center gap-2.5 text-left text-xs font-semibold text-[var(--text-primary)] bg-transparent hover:bg-black/5 transition';
            if (squircle) squircle.className = 'samsung-squircle w-9 h-9 rounded-full flex items-center justify-center text-current shrink-0';
          }
        });

        if (category === 'zones') {
          toggleCameraZone();
        }

        const cards = document.querySelectorAll('[data-category]');
        cards.forEach((card) => {
          const cats = card.getAttribute('data-category') || '';
          if (category === 'all' || cats.includes(category)) {
            card.classList.remove('card-dimmed');
          } else {
            card.classList.add('card-dimmed');
          }
        });

        if (category === 'all') {
          activeFilterBanner.classList.add('hidden');
          activeFilterBanner.classList.remove('flex');
        } else {
          const labels = {
            irrigation: 'Water Flow, Irrigation & Air Moisture Modules',
            growth: 'Harvest AI Readiness & 7-Day Growth Rate Histogram',
            controls: 'Indoor Climate, Moisture, Photoperiod & EC Setpoints',
            energy: 'Power Consumption & Air CO₂ Enrichment Analytics',
            zones: 'Greenhouse Camera Feeds & Spatial Zone Telemetry'
          };
          activeFilterText.textContent = `Focused View: ${labels[category] || category}`;
          activeFilterBanner.classList.remove('hidden');
          activeFilterBanner.classList.add('flex');
        }

        if (window.innerWidth < 1024) closeMobileSidebar();
      }

      navButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const nav = btn.getAttribute('data-nav');
          setSidebarCategory(nav);
        });
      });

      if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', () => setSidebarCategory('all'));
      }
      const brandHomeBtn = document.getElementById('brandHomeBtn');
      if (brandHomeBtn) {
        brandHomeBtn.addEventListener('click', () => setSidebarCategory('all'));
      }

      // --- 8. CAMERA ZONE SWITCHER ---
      const heroCameraImg = document.getElementById('heroCameraImg');
      const switchCamZone1Btn = document.getElementById('switchCamZone1Btn');
      const switchCamZone2Btn = document.getElementById('switchCamZone2Btn');
      const activeZoneHeaderLabel = document.getElementById('activeZoneHeaderLabel');

      function setCameraZone(zoneNum) {
        state.cameraZone = zoneNum;
        if (!heroCameraImg) return;
        if (zoneNum === 1) {
          heroCameraImg.src = state.zone1Src;
          if (activeZoneHeaderLabel) activeZoneHeaderLabel.textContent = 'Zone A • Hydroponic Bay';
          if (switchCamZone1Btn) switchCamZone1Btn.className = 'px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5';
          if (switchCamZone2Btn) switchCamZone2Btn.className = 'px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 transition';
          showToast('Switched to Cam 01: Main Hydroponic Lettuce Bay');
        } else {
          heroCameraImg.src = "data:image/webp;base64,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";
          if (activeZoneHeaderLabel) activeZoneHeaderLabel.textContent = 'Zone B • North Lettuce Wing';
          if (switchCamZone2Btn) switchCamZone2Btn.className = 'px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5';
          if (switchCamZone1Btn) switchCamZone1Btn.className = 'px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 transition';
          showToast('Switched to Cam 02: North Lettuce Wing');
        }
      }

      function toggleCameraZone() {
        setCameraZone(state.cameraZone === 1 ? 2 : 1);
      }

      if (switchCamZone1Btn) switchCamZone1Btn.addEventListener('click', () => setCameraZone(1));
      if (switchCamZone2Btn) switchCamZone2Btn.addEventListener('click', () => setCameraZone(2));

      // --- 9. LIVE SEARCH FILTERING ---
      const searchInput = document.getElementById('dashboardSearchInput');
      const mobileSearchInput = document.getElementById('mobileSearchInput');
      const clearSearchBtn = document.getElementById('clearSearchBtn');

      function handleSearch(query) {
        const q = query.trim().toLowerCase();
        if (clearSearchBtn) {
          clearSearchBtn.classList.toggle('hidden', q.length === 0);
        }
        const cards = document.querySelectorAll('[data-search]');
        cards.forEach((card) => {
          const text = (card.getAttribute('data-search') || '').toLowerCase();
          if (!q || text.includes(q)) {
            card.classList.remove('card-dimmed');
          } else {
            card.classList.add('card-dimmed');
          }
        });
      }

      if (searchInput) {
        searchInput.addEventListener('input', (e) => {
          if (mobileSearchInput) mobileSearchInput.value = e.target.value;
          handleSearch(e.target.value);
        });
      }
      if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', (e) => {
          if (searchInput) searchInput.value = e.target.value;
          handleSearch(e.target.value);
        });
      }
      if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
          searchInput.value = '';
          if (mobileSearchInput) mobileSearchInput.value = '';
          handleSearch('');
        });
      }

      // --- 10. THEME SWITCHER (Light Glass / Dark Glass) ---
      const themeLightBtn = document.getElementById('themeLightBtn');
      const themeDarkBtn = document.getElementById('themeDarkBtn');
      const mobileThemeLightBtn = document.getElementById('mobileThemeLightBtn');
      const mobileThemeDarkBtn = document.getElementById('mobileThemeDarkBtn');
      const drawerLightBtn = document.getElementById('drawerLightBtn');
      const drawerDarkBtn = document.getElementById('drawerDarkBtn');

      function applyTheme(mode) {
        state.theme = mode === 'dark' ? 'dark' : 'light';
        document.body.classList.toggle('theme-dark', state.theme === 'dark');
        document.documentElement.classList.toggle('dark', state.theme === 'dark');

        const activeClass = 'w-8 h-8 rounded-full flex items-center justify-center bg-white text-[#0A0909] shadow-sm transition';
        const inactiveClass = 'w-8 h-8 rounded-full flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition';
        if (themeLightBtn) themeLightBtn.className = state.theme === 'light' ? activeClass : inactiveClass;
        if (themeDarkBtn) themeDarkBtn.className = state.theme === 'dark' ? activeClass : inactiveClass;

        if (mobileThemeLightBtn) mobileThemeLightBtn.className = state.theme === 'light' ? activeClass : inactiveClass;
        if (mobileThemeDarkBtn) mobileThemeDarkBtn.className = state.theme === 'dark' ? activeClass : inactiveClass;

        if (drawerLightBtn) drawerLightBtn.className = state.theme === 'light'
          ? 'py-1 px-1.5 rounded-xl bg-white/80 text-[10px] font-bold text-[#0A0909] text-center ring-1 ring-[var(--color-primary)]'
          : 'py-1 px-1.5 rounded-xl bg-black/5 text-[10px] font-semibold text-[var(--text-secondary)] text-center';
        if (drawerDarkBtn) drawerDarkBtn.className = state.theme === 'dark'
          ? 'py-1 px-1.5 rounded-xl bg-white/80 text-[10px] font-bold text-[#0A0909] text-center ring-1 ring-[var(--color-primary)]'
          : 'py-1 px-1.5 rounded-xl bg-black/5 text-[10px] font-semibold text-[var(--text-secondary)] text-center';

        applyCustomColorPalette(state.primaryHex);
      }

      if (themeLightBtn) themeLightBtn.addEventListener('click', () => { applyTheme('light'); showToast('Switched to Glass Light Mode'); });
      if (themeDarkBtn) themeDarkBtn.addEventListener('click', () => { applyTheme('dark'); showToast('Switched to Dark Glass Mode'); });
      if (mobileThemeLightBtn) mobileThemeLightBtn.addEventListener('click', () => { applyTheme('light'); showToast('Switched to Glass Light Mode'); });
      if (mobileThemeDarkBtn) mobileThemeDarkBtn.addEventListener('click', () => { applyTheme('dark'); showToast('Switched to Dark Glass Mode'); });
      if (drawerLightBtn) drawerLightBtn.addEventListener('click', () => { applyTheme('light'); showToast('Switched to Glass Light Mode'); });
      if (drawerDarkBtn) drawerDarkBtn.addEventListener('click', () => { applyTheme('dark'); showToast('Switched to Dark Glass Mode'); });

      // --- 11. NOTIFICATIONS POPOVER ---
      const notificationsBtn = document.getElementById('notificationsBtn');
      const mobileNotifBtn = document.getElementById('mobileNotifBtn');
      const notificationsPopover = document.getElementById('notificationsPopover');
      const markAllReadBtn = document.getElementById('markAllReadBtn');
      const notifBadge = document.getElementById('notifBadge');
      const notifCountPill = document.getElementById('notifCountPill');

      function toggleNotifications(e) {
        e.stopPropagation();
        if (notificationsPopover) notificationsPopover.classList.toggle('hidden');
        if (colorPickerPopover) colorPickerPopover.classList.add('hidden');
      }

      if (notificationsBtn) notificationsBtn.addEventListener('click', toggleNotifications);
      if (mobileNotifBtn) mobileNotifBtn.addEventListener('click', toggleNotifications);

      document.addEventListener('click', (e) => {
        if (notificationsPopover && !notificationsPopover.contains(e.target) && e.target !== notificationsBtn && e.target !== mobileNotifBtn) {
          notificationsPopover.classList.add('hidden');
        }
        if (colorPickerPopover && !colorPickerPopover.contains(e.target) && e.target !== colorPickerToggleBtn) {
          colorPickerPopover.classList.add('hidden');
        }
      });

      if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', () => {
          if (notifBadge) notifBadge.classList.add('hidden');
          if (notifCountPill) {
            notifCountPill.textContent = '0 Unread';
            notifCountPill.className = 'px-2 py-0.5 text-[11px] font-bold rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)]';
          }
          showToast('All greenhouse alerts marked as read');
        });
      }

      // --- 12. VOICE COMMAND OVERLAY ---
      const voiceCommandBtn = document.getElementById('voiceCommandBtn');
      const mobileVoiceBtn = document.getElementById('mobileVoiceBtn');
      const voiceModal = document.getElementById('voiceModal');
      const closeVoiceModalBtn = document.getElementById('closeVoiceModalBtn');

      function openVoiceModal() {
        if (voiceModal) voiceModal.classList.remove('hidden');
      }
      function closeVoiceModal() {
        if (voiceModal) voiceModal.classList.add('hidden');
      }

      if (voiceCommandBtn) voiceCommandBtn.addEventListener('click', openVoiceModal);
      if (mobileVoiceBtn) mobileVoiceBtn.addEventListener('click', openVoiceModal);
      if (closeVoiceModalBtn) closeVoiceModalBtn.addEventListener('click', closeVoiceModal);

      document.querySelectorAll('[data-voice-cmd]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const cmd = btn.getAttribute('data-voice-cmd');
          if (cmd === 'optimize-power') {
            state.powerTotal = 132;
            state.powerLighting = 104;
            document.getElementById('powerTotalValue').textContent = '132';
            document.getElementById('powerLightingVal').textContent = '104';
            showToast('AI Voice: Shaved 10:00 lighting peak (-12 kWh saved)');
          } else if (cmd === 'boost-water') {
            state.waterActiveBars = 9;
            renderWaterFlowBars();
            showToast('AI Voice: Water Flow increased to 7.594 L');
          } else if (cmd === 'cool-climate') {
            state.climateTemp = 21;
            state.airMoisture = 72;
            document.getElementById('kpiTempVal').textContent = '21';
            document.getElementById('kpiMoistureVal').textContent = '72';
            showToast('AI Voice: Set Indoor Climate to 21 °C & 72% Moisture');
          }
          closeVoiceModal();
        });
      });

      // --- 13. INTERACTIVE MODALS ---
      const modalOverlay = document.getElementById('modalOverlay');
      const modalTitle = document.getElementById('modalTitle');
      const modalSubtitle = document.getElementById('modalSubtitle');
      const modalBody = document.getElementById('modalBody');
      const closeModalBtn = document.getElementById('closeModalBtn');

      function openModal(type) {
        if (!modalOverlay || !modalBody) return;
        modalOverlay.classList.remove('hidden');

        if (type === 'ai-assistant') {
          modalTitle.textContent = 'AI Greenhouse Agronomist Assistant';
          modalSubtitle.textContent = 'Real-time recommendations & 1-click autonomous actions';
          modalBody.innerHTML = `
            <div class="p-3.5 rounded-2xl bg-[var(--color-primary)]/15 border border-[var(--color-primary)]/30 text-xs leading-relaxed">
              <strong class="text-[var(--text-accent)] block mb-1">🌱 Live AI Diagnosis (Zone A Hydroponics):</strong>
              Crop readiness is at <strong>${state.harvestPercent}% (${state.harvestStatus})</strong> with a peak growth velocity of <strong>${state.growthRate}</strong> recorded on July 22–23. Midday power peaked at <strong>10:00 (${state.powerTotal} kWh)</strong>.
            </div>
            <div class="space-y-2">
              <p class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">1-Click AI Optimizations</p>
              <button id="aiOptPowerBtn" type="button" class="w-full p-3 rounded-xl glass-pill flex items-center justify-between text-left hover:border-[var(--color-primary)] transition">
                <div>
                  <p class="font-bold text-xs">Balance 10:00 AM Grow Light Peak</p>
                  <p class="text-[11px] text-[var(--text-muted)]">Dim LED banks by 10% during peak solar radiation (Saves 12 kWh)</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-[var(--color-primary)] text-white text-[11px] font-bold">Apply</span>
              </button>
              <button id="aiOptNutrientBtn" type="button" class="w-full p-3 rounded-xl glass-pill flex items-center justify-between text-left hover:border-[var(--color-primary)] transition">
                <div>
                  <p class="font-bold text-xs">Boost Late-Stage Lettuce Biomass</p>
                  <p class="text-[11px] text-[var(--text-muted)]">Tune EC to 2.3 mS & CO₂ to 920 ppm for 88% Harvest Readiness</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-[var(--color-primary)] text-white text-[11px] font-bold">Apply</span>
              </button>
            </div>
          `;
          document.getElementById('aiOptPowerBtn').addEventListener('click', () => {
            state.powerTotal = 132;
            state.powerLighting = 104;
            document.getElementById('powerTotalValue').textContent = '132';
            document.getElementById('powerLightingVal').textContent = '104';
            showToast('Applied AI Power Optimization: 132 kWh');
            modalOverlay.classList.add('hidden');
          });
          document.getElementById('aiOptNutrientBtn').addEventListener('click', () => {
            state.ecLevel = 2.3;
            state.harvestPercent = 88;
            state.harvestStatus = 'Optimal Yield';
            document.getElementById('kpiEcVal').textContent = '2.3';
            document.getElementById('harvestPercentValue').textContent = '88%';
            document.getElementById('harvestStatusLabel').textContent = 'Optimal Yield';
            showToast('Applied AI Biomass Boost: Harvest AI now at 88%');
            modalOverlay.classList.add('hidden');
          });
        } else if (type === 'setpoints') {
          modalTitle.textContent = 'Environmental & Nutrient Setpoint Controls';
          modalSubtitle.textContent = 'Adjust live greenhouse targets for Climate, Moisture, Light & EC';
          modalBody.innerHTML = `
            <div class="space-y-4">
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Indoor Climate (°C)</span>
                  <span id="sliderTempLabel" class="text-[var(--color-primary)]">${state.climateTemp} °C</span>
                </div>
                <input id="sliderTemp" type="range" min="16" max="32" step="1" value="${state.climateTemp}" class="w-full accent-[var(--color-primary)]" />
              </div>
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Air Moisture (%)</span>
                  <span id="sliderMoistureLabel" class="text-[var(--color-primary)]">${state.airMoisture} %</span>
                </div>
                <input id="sliderMoisture" type="range" min="40" max="90" step="1" value="${state.airMoisture}" class="w-full accent-[var(--color-primary)]" />
              </div>
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Photoperiod Light Level (Hours)</span>
                  <span id="sliderLightLabel" class="text-[var(--color-primary)]">${state.lightHours} H</span>
                </div>
                <input id="sliderLight" type="range" min="8" max="20" step="1" value="${state.lightHours}" class="w-full accent-[var(--color-primary)]" />
              </div>
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Nutrient EC Level (mS/cm)</span>
                  <span id="sliderEcLabel" class="text-[var(--color-primary)]">${state.ecLevel} mS</span>
                </div>
                <input id="sliderEc" type="range" min="1.0" max="3.5" step="0.1" value="${state.ecLevel}" class="w-full accent-[var(--color-primary)]" />
              </div>
              <button id="saveSetpointsBtn" type="button" class="w-full py-2.5 rounded-full bg-[var(--color-primary)] text-white font-bold text-xs shadow-md hover:opacity-95 transition">
                Apply Setpoints to Greenhouse
              </button>
            </div>
          `;
          const sTemp = document.getElementById('sliderTemp');
          const sMoist = document.getElementById('sliderMoisture');
          const sLight = document.getElementById('sliderLight');
          const sEc = document.getElementById('sliderEc');

          sTemp.addEventListener('input', (e) => { document.getElementById('sliderTempLabel').textContent = `${e.target.value} °C`; });
          sMoist.addEventListener('input', (e) => { document.getElementById('sliderMoistureLabel').textContent = `${e.target.value} %`; });
          sLight.addEventListener('input', (e) => { document.getElementById('sliderLightLabel').textContent = `${e.target.value} H`; });
          sEc.addEventListener('input', (e) => { document.getElementById('sliderEcLabel').textContent = `${parseFloat(e.target.value).toFixed(1)} mS`; });

          document.getElementById('saveSetpointsBtn').addEventListener('click', () => {
            state.climateTemp = parseInt(sTemp.value, 10);
            state.airMoisture = parseInt(sMoist.value, 10);
            state.lightHours = parseInt(sLight.value, 10);
            state.ecLevel = parseFloat(sEc.value).toFixed(1);
            document.getElementById('kpiTempVal').textContent = state.climateTemp;
            document.getElementById('kpiMoistureVal').textContent = state.airMoisture;
            document.getElementById('kpiLightVal').textContent = state.lightHours;
            document.getElementById('kpiEcVal').textContent = state.ecLevel;
            modalOverlay.classList.add('hidden');
            showToast('Updated Indoor Climate, Moisture, Light & EC setpoints');
          });
        } else if (type === 'harvest') {
          modalTitle.textContent = 'Harvest AI & Crop Growth Analytics';
          modalSubtitle.textContent = 'Computer vision canopy scan & 7-day biomass velocity';
          modalBody.innerHTML = `
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-2xl glass-pill">
                  <p class="text-[11px] text-[var(--text-muted)]">Harvest Readiness</p>
                  <p class="text-xl font-bold text-[var(--color-primary)] mt-0.5">${state.harvestPercent}% (${state.harvestStatus})</p>
                </div>
                <div class="p-3 rounded-2xl glass-pill">
                  <p class="text-[11px] text-[var(--text-muted)]">Growth Rate Index</p>
                  <p class="text-xl font-bold text-[var(--text-primary)] mt-0.5">${state.growthRate} <span class="text-xs font-normal">g/m²/h</span></p>
                </div>
              </div>
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Simulate Crop Maturity (%)</span>
                  <span id="simHarvestLabel" class="text-[var(--color-primary)]">${state.harvestPercent}%</span>
                </div>
                <input id="simHarvestSlider" type="range" min="45" max="100" value="${state.harvestPercent}" class="w-full accent-[var(--color-primary)]" />
              </div>
              <div class="flex gap-2">
                <button id="modalCam1Btn" type="button" class="flex-1 py-2 rounded-xl glass-pill text-xs font-bold">View Cam 01 (Main Bay)</button>
                <button id="modalCam2Btn" type="button" class="flex-1 py-2 rounded-xl glass-pill text-xs font-bold">View Cam 02 (North Wing)</button>
              </div>
            </div>
          `;
          const simSlider = document.getElementById('simHarvestSlider');
          simSlider.addEventListener('input', (e) => {
            const val = parseInt(e.target.value, 10);
            state.harvestPercent = val;
            state.harvestStatus = val >= 85 ? 'Ready to Harvest' : val >= 70 ? 'Good Growth' : 'Vegetative Stage';
            document.getElementById('simHarvestLabel').textContent = `${val}%`;
            document.getElementById('harvestPercentValue').textContent = `${val}%`;
            document.getElementById('harvestStatusLabel').textContent = state.harvestStatus;
          });
          document.getElementById('modalCam1Btn').addEventListener('click', () => { setCameraZone(1); modalOverlay.classList.add('hidden'); });
          document.getElementById('modalCam2Btn').addEventListener('click', () => { setCameraZone(2); modalOverlay.classList.add('hidden'); });
        } else if (type === 'water') {
          modalTitle.textContent = 'Water Flow & Hydroponic Irrigation';
          modalSubtitle.textContent = 'Closed-loop nutrient recirculation telemetry';
          modalBody.innerHTML = `
            <div class="space-y-4">
              <p class="text-xs text-[var(--text-secondary)]">Click any bar directly on the Water Flow widget or use the slider below to adjust active manifold valves (16 total channels):</p>
              <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                  <span>Active Valve Channels (${state.waterActiveBars} / 16)</span>
                  <span id="modalWaterLabel" class="text-[var(--color-primary)]">${((state.waterActiveBars / 8) * 6.75).toFixed(3)} L</span>
                </div>
                <input id="modalWaterSlider" type="range" min="1" max="16" value="${state.waterActiveBars}" class="w-full accent-[var(--color-primary)]" />
              </div>
            </div>
          `;
          document.getElementById('modalWaterSlider').addEventListener('input', (e) => {
            state.waterActiveBars = parseInt(e.target.value, 10);
            renderWaterFlowBars();
            document.getElementById('modalWaterLabel').textContent = `${((state.waterActiveBars / 8) * 6.75).toFixed(3)} L`;
          });
        } else if (type === 'power') {
          modalTitle.textContent = 'Power Consumption & Load Breakdown';
          modalSubtitle.textContent = 'Hourly energy profile (07:00 – 01:00)';
          modalBody.innerHTML = `
            <div class="space-y-3 text-xs">
              <div class="grid grid-cols-3 gap-2 text-center">
                <div class="p-2.5 rounded-xl glass-pill">
                  <p class="text-[var(--text-muted)]">Headline Total</p>
                  <p class="text-base font-bold mt-0.5">${state.powerTotal} kWh</p>
                </div>
                <div class="p-2.5 rounded-xl glass-pill">
                  <p class="text-[var(--text-muted)]">LED Lighting</p>
                  <p class="text-base font-bold text-[var(--color-primary)] mt-0.5">${state.powerLighting} kWh</p>
                </div>
                <div class="p-2.5 rounded-xl glass-pill">
                  <p class="text-[var(--text-muted)]">HVAC & Pumps</p>
                  <p class="text-base font-bold text-[var(--color-alert)] mt-0.5">${state.powerOther} kWh</p>
                </div>
              </div>
              <p class="text-[var(--text-muted)] leading-relaxed">
                Peak draw occurs between <strong>09:45 and 10:40</strong> when overhead grow lights and dehumidifiers run concurrently.
              </p>
            </div>
          `;
        } else if (type === 'co2') {
          modalTitle.textContent = 'Air CO₂ Enrichment Schedule';
          modalSubtitle.textContent = 'Stepped photosynthetic carbon dosing (400 – 900 ppm)';
          modalBody.innerHTML = `
            <div class="space-y-3 text-xs">
              <p class="text-[var(--text-secondary)]">Select a stepped timestamp to inspect or set current CO₂ concentration:</p>
              <div class="grid grid-cols-4 gap-2">
                ${state.co2Steps.map((s, idx) => `
                  <button type="button" data-co2-step="${idx}" class="p-2 rounded-xl glass-pill text-center hover:border-[var(--color-primary)] ${idx === state.co2ActiveStep ? 'ring-2 ring-[var(--color-primary)] font-bold' : ''}">
                    <span class="block text-[10px] text-[var(--text-muted)]">${s.time}</span>
                    <span class="text-xs font-bold">${s.ppm} ppm</span>
                  </button>
                `).join('')}
              </div>
            </div>
          `;
          modalBody.querySelectorAll('[data-co2-step]').forEach((b) => {
            b.addEventListener('click', () => {
              const idx = parseInt(b.getAttribute('data-co2-step'), 10);
              state.co2ActiveStep = idx;
              state.co2Ppm = state.co2Steps[idx].ppm;
              document.getElementById('co2PpmValue').textContent = state.co2Ppm;
              renderCo2Chart();
              modalOverlay.classList.add('hidden');
              showToast(`CO₂ readout set to ${state.co2Ppm} ppm (${state.co2Steps[idx].time})`);
            });
          });
        } else if (type === 'settings') {
          modalTitle.textContent = 'Theme Color Studio & Settings';
          modalSubtitle.textContent = 'Customize primary brand color, chart palette & font colors';
          modalBody.innerHTML = `
            <div class="space-y-4 text-xs">
              <div class="p-3.5 rounded-2xl glass-pill flex items-center justify-between">
                <div>
                  <p class="font-bold text-sm text-[var(--text-primary)]">Custom Theme & Font Color</p>
                  <p class="text-[11px] text-[var(--text-muted)]">Pick any color to re-theme the entire dashboard</p>
                </div>
                <input id="modalCustomColorPicker" type="color" value="${state.primaryHex}" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent" />
              </div>
              <div>
                <p class="font-bold mb-2">Switch Appearance Mode</p>
                <div class="grid grid-cols-2 gap-2">
                  <button type="button" id="setModeLight" class="py-2.5 px-3 rounded-xl glass-pill font-bold">Glass Light Mode</button>
                  <button type="button" id="setModeDark" class="py-2.5 px-3 rounded-xl glass-pill font-bold">Dark Glass Mode</button>
                </div>
              </div>
            </div>
          `;
          document.getElementById('modalCustomColorPicker').addEventListener('input', (e) => {
            applyCustomColorPalette(e.target.value);
          });
          document.getElementById('setModeLight').addEventListener('click', () => { applyTheme('light'); modalOverlay.classList.add('hidden'); });
          document.getElementById('setModeDark').addEventListener('click', () => { applyTheme('dark'); modalOverlay.classList.add('hidden'); });
        } else if (type === 'profile') {
          modalTitle.textContent = 'Imran Jakir — Lead AI Product Designer';
          modalSubtitle.textContent = 'AI Greenhouse Management System • Operator Profile';
          modalBody.innerHTML = `
            <div class="flex items-center gap-4">
              <img src="${document.querySelector('#openProfileBtn img').src}" alt="Imran Jakir" class="w-14 h-14 rounded-full object-cover ring-2 ring-[var(--color-primary)]" />
              <div class="text-xs">
                <p class="font-bold text-sm">Imran Jakir</p>
                <p class="text-[var(--text-muted)]">AI Product Experience Designer • Agritech Systems</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] font-bold text-[10px]">Active Shift • Zone A & B</span>
              </div>
            </div>
          `;
        }
      }

      if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => modalOverlay.classList.add('hidden'));
      }
      if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
          if (e.target === modalOverlay) modalOverlay.classList.add('hidden');
        });
      }

      document.querySelectorAll('[data-modal-open]').forEach((el) => {
        el.addEventListener('click', (e) => {
          e.stopPropagation();
          openModal(el.getAttribute('data-modal-open'));
        });
      });

      ['aiAssistantBtn', 'mobileAiAssistantBtn', 'drawerAiAssistantBtn'].forEach((id) => {
        const btn = document.getElementById(id);
        if (btn) {
          btn.addEventListener('click', () => {
            if (window.innerWidth < 1024) closeMobileSidebar();
            openModal('ai-assistant');
          });
        }
      });

      const openSettingsBtn = document.getElementById('openSettingsBtn');
      if (openSettingsBtn) openSettingsBtn.addEventListener('click', () => openModal('settings'));

      const openProfileBtn = document.getElementById('openProfileBtn');
      if (openProfileBtn) openProfileBtn.addEventListener('click', () => openModal('profile'));

      // --- 14. INITIAL RENDER ---
      const heroEl = document.getElementById('heroCameraImg');
      if (heroEl) {
        state.zone1Src = heroEl.src;
      }
      applyMobileSidebarMode(false); // Default to Icon-Only Samsung Shortcut mode on mobile
      applyCustomColorPalette('#10B981');
    })();
  </script>
</body>
</html>