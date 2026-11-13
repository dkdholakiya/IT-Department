/**
 * Realistic Winter Snowfall Canvas Engine & Controller
 * Department of CE & IT Portal
 *
 * Features:
 * - Ultra-smooth 60 FPS HTML5 Canvas snowfall simulation
 * - Realistic 3D depth-of-field layers (background, midground, foreground)
 * - Organic sinusoidal horizontal swaying and dynamic gentle breeze wind
 * - High-DPI / Retina display auto-scaling
 * - Non-blocking pointer-events: none (zero interference with underlying clicks/forms/buttons)
 * - Enabled by default with persistent user preference in localStorage
 * - Seamless desktop and mobile navbar toggle button integration
 * - Background tab battery and CPU saver (auto-pauses on visibilitychange)
 */

(function () {
  'use strict';

  // State keys
  const STORAGE_KEY = 'portal_snowfall_enabled';

  // Read initial preference (DEFAULT: ON)
  let isEnabled = (function () {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored === null ? true : stored === 'true';
    } catch (e) {
      return true;
    }
  })();

  let canvas = null;
  let ctx = null;
  let animFrameId = null;
  let flakes = [];
  let dpr = 1;
  let width = 0;
  let height = 0;
  let globalWindAngle = 0;

  // Configuration
  const CONFIG = {
    // Dynamically scaled by viewport size
    desktopFlakeCount: 120,
    mobileFlakeCount: 50,
    // Physics
    minSpeedY: 0.55,
    maxSpeedY: 1.85,
    windAmplitude: 0.45,
    windSpeed: 0.007,
    // Rendering
    zIndex: 99999
  };

  /**
   * Helper to dispatch portal toasts
   */
  function notify(msg, type = 'info') {
    if (typeof window.showPortalToast === 'function') {
      window.showPortalToast(msg, type);
    } else if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      window.PortalToast.show(msg, type);
    }
  }

  /**
   * Snowflake Particle Generator
   */
  function createFlake(customY = null) {
    // Depth: 0.25 (distant, tiny, slow) to 1.0 (foreground, large, faster)
    const depth = 0.25 + Math.random() * 0.75;
    const baseRadius = 1.0 + Math.random() * 2.6;
    const radius = baseRadius * (0.6 + depth * 0.6);

    // Speed scales with simulated depth
    const speedY = (CONFIG.minSpeedY + Math.random() * (CONFIG.maxSpeedY - CONFIG.minSpeedY)) * depth * 1.35;

    // Horizontal sway parameters
    const swaySpeed = 0.012 + Math.random() * 0.024;
    const swayAmplitude = (0.6 + Math.random() * 1.6) * depth;
    const swayAngle = Math.random() * Math.PI * 2;

    // Opacity based on depth layer (distant flakes are softer)
    const opacity = 0.25 + depth * 0.65;

    return {
      x: Math.random() * (width || window.innerWidth),
      y: customY !== null ? customY : Math.random() * (height || window.innerHeight),
      radius: Math.max(0.8, radius),
      depth: depth,
      speedY: Math.max(0.4, speedY),
      swaySpeed: swaySpeed,
      swayAmplitude: swayAmplitude,
      swayAngle: swayAngle,
      opacity: Math.min(0.92, opacity),
      // Radial glow for foreground flakes
      hasGlow: depth > 0.8 && radius > 2.5
    };
  }

  /**
   * Initialize or Resize Canvas to fit viewport with Retina scaling
   */
  function resizeCanvas() {
    if (!canvas) return;

    dpr = window.devicePixelRatio || 1;
    width = window.innerWidth;
    height = window.innerHeight;

    canvas.width = Math.floor(width * dpr);
    canvas.height = Math.floor(height * dpr);

    if (ctx) {
      ctx.setTransform(1, 0, 0, 1, 0, 0);
      ctx.scale(dpr, dpr);
    }

    // Determine target flake count based on screen width
    const targetCount = width < 768 ? CONFIG.mobileFlakeCount : CONFIG.desktopFlakeCount;

    if (flakes.length === 0) {
      for (let i = 0; i < targetCount; i++) {
        flakes.push(createFlake());
      }
    } else if (flakes.length < targetCount) {
      while (flakes.length < targetCount) {
        flakes.push(createFlake(-10));
      }
    } else if (flakes.length > targetCount) {
      flakes.length = targetCount;
    }
  }

  /**
   * Create and Mount Canvas Overlay in Front of Design
   */
  function mountCanvas() {
    if (canvas) return;

    canvas = document.createElement('canvas');
    canvas.id = 'portal-snowfall-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    canvas.tabIndex = -1;

    // Critical: pointer-events: none ensures ZERO interference with underlying clicks/forms
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100vw';
    canvas.style.height = '100vh';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = CONFIG.zIndex.toString();
    canvas.style.userSelect = 'none';
    canvas.style.display = 'block';

    ctx = canvas.getContext('2d', { alpha: true });
    document.body.appendChild(canvas);

    resizeCanvas();
  }

  /**
   * Main 60 FPS Render & Physics Loop
   */
  function renderLoop() {
    if (!isEnabled || !ctx || !canvas) return;

    ctx.clearRect(0, 0, width, height);

    // Global gentle breeze calculation
    globalWindAngle += CONFIG.windSpeed;
    const globalWind = Math.sin(globalWindAngle) * CONFIG.windAmplitude;

    // Check if dark theme is active for subtle color tinting
    const isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('theme-dark');

    for (let i = 0; i < flakes.length; i++) {
      const f = flakes[i];

      // Update sway and position
      f.swayAngle += f.swaySpeed;
      const horizontalSway = Math.sin(f.swayAngle) * f.swayAmplitude;

      f.x += horizontalSway + (globalWind * f.depth);
      f.y += f.speedY;

      // Wrap around edges seamlessly
      if (f.y > height + 10) {
        f.y = -10;
        f.x = Math.random() * width;
      }
      if (f.x > width + 15) {
        f.x = -10;
      } else if (f.x < -15) {
        f.x = width + 10;
      }

      // Draw flake
      ctx.beginPath();
      ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2);

      if (f.hasGlow) {
        // Soft radial glow for authentic winter bokeh depth
        const grad = ctx.createRadialGradient(f.x, f.y, 0, f.x, f.y, f.radius * 1.8);
        if (isDark) {
          grad.addColorStop(0, `rgba(255, 255, 255, ${f.opacity})`);
          grad.addColorStop(0.5, `rgba(224, 242, 254, ${f.opacity * 0.7})`);
          grad.addColorStop(1, 'rgba(224, 242, 254, 0)');
        } else {
          grad.addColorStop(0, `rgba(240, 249, 255, ${f.opacity})`);
          grad.addColorStop(0.6, `rgba(186, 230, 253, ${f.opacity * 0.6})`);
          grad.addColorStop(1, 'rgba(186, 230, 253, 0)');
        }
        ctx.fillStyle = grad;
        ctx.fill();
      } else {
        // Crisp clean snow particle
        if (isDark) {
          ctx.fillStyle = `rgba(255, 255, 255, ${f.opacity})`;
        } else {
          ctx.fillStyle = `rgba(220, 240, 255, ${Math.min(0.9, f.opacity + 0.1)})`;
        }
        ctx.fill();
      }
    }

    animFrameId = requestAnimationFrame(renderLoop);
  }

  /**
   * Start Snowfall Animation
   */
  function startSnowfall() {
    isEnabled = true;
    try {
      localStorage.setItem(STORAGE_KEY, 'true');
    } catch (e) {}

    mountCanvas();
    updateUIButtons(true);

    if (!animFrameId) {
      animFrameId = requestAnimationFrame(renderLoop);
    }
  }

  /**
   * Stop Snowfall Animation and Clear Canvas
   */
  function stopSnowfall() {
    isEnabled = false;
    try {
      localStorage.setItem(STORAGE_KEY, 'false');
    } catch (e) {}

    if (animFrameId) {
      cancelAnimationFrame(animFrameId);
      animFrameId = null;
    }

    if (ctx && canvas) {
      ctx.clearRect(0, 0, width, height);
    }

    updateUIButtons(false);
  }

  /**
   * Toggle Snowfall State
   */
  function toggleSnowfall() {
    if (isEnabled) {
      stopSnowfall();
      notify('❄️ Winter Snowfall Effect Paused');
    } else {
      startSnowfall();
      notify('❄️ Winter Snowfall Effect Enabled');
    }
  }

  /**
   * Synchronize UI Button Appearance (Desktop & Mobile)
   */
  function updateUIButtons(active) {
    const desktopBtn = document.getElementById('snowfallToggleBtn');
    const mobileBtn = document.getElementById('mobileSnowfallToggleBtn');
    const desktopDot = document.getElementById('snowfallActiveDot');
    const mobileDot = document.getElementById('mobileSnowfallActiveDot');
    const desktopIcon = document.getElementById('snowfallIcon');

    // Update active dots
    if (desktopDot) {
      desktopDot.style.opacity = active ? '1' : '0';
      desktopDot.style.transform = active ? 'scale(1)' : 'scale(0)';
    }
    if (mobileDot) {
      mobileDot.style.opacity = active ? '1' : '0';
      mobileDot.style.transform = active ? 'scale(1)' : 'scale(0)';
    }

    // Update button titles and classes
    const titleText = active ? 'Winter Snowfall: Active (Click to Pause)' : 'Winter Snowfall: Paused (Click to Enable)';
    if (desktopBtn) {
      desktopBtn.title = titleText;
      desktopBtn.setAttribute('aria-pressed', active ? 'true' : 'false');
      if (active) {
        desktopBtn.classList.add('text-sky-500', 'dark:text-sky-300');
      } else {
        desktopBtn.classList.remove('text-sky-500', 'dark:text-sky-300');
      }
    }

    if (mobileBtn) {
      mobileBtn.title = titleText;
      mobileBtn.setAttribute('aria-pressed', active ? 'true' : 'false');
      if (active) {
        mobileBtn.classList.add('text-sky-500', 'dark:text-sky-300');
      } else {
        mobileBtn.classList.remove('text-sky-500', 'dark:text-sky-300');
      }
    }

    // Gentle micro-rotation animation on icon toggle
    if (desktopIcon) {
      desktopIcon.classList.add('rotate-45');
      setTimeout(() => desktopIcon.classList.remove('rotate-45'), 260);
    }
  }

  /**
   * Bind Event Listeners to Navbar Buttons
   */
  function bindButtons() {
    const desktopBtn = document.getElementById('snowfallToggleBtn');
    const mobileBtn = document.getElementById('mobileSnowfallToggleBtn');

    if (desktopBtn && !desktopBtn.__snowfallBound) {
      desktopBtn.__snowfallBound = true;
      desktopBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleSnowfall();
      });
    }

    if (mobileBtn && !mobileBtn.__snowfallBound) {
      mobileBtn.__snowfallBound = true;
      mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleSnowfall();
      });
    }

    updateUIButtons(isEnabled);
  }

  /**
   * Setup Window and Visibility Event Handlers
   */
  function setupEvents() {
    let resizeTimer = null;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(resizeCanvas, 120);
    });

    // Auto pause when tab is hidden to conserve power and CPU
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        if (animFrameId) {
          cancelAnimationFrame(animFrameId);
          animFrameId = null;
        }
      } else if (isEnabled) {
        if (!animFrameId) {
          animFrameId = requestAnimationFrame(renderLoop);
        }
      }
    });

    // Handle theme change events if dispatched
    window.addEventListener('portal:theme-mode-changed', () => {
      // Flakes adapt dynamically on next frame based on DOM class
    });
  }

  /**
   * Engine Bootstrap
   */
  function init() {
    setupEvents();
    bindButtons();

    // Start immediately if enabled (DEFAULT: ON)
    if (isEnabled) {
      mountCanvas();
      if (!animFrameId) {
        animFrameId = requestAnimationFrame(renderLoop);
      }
    }
  }

  // Public Interface on window.PortalSnowfall
  window.PortalSnowfall = {
    start: startSnowfall,
    stop: stopSnowfall,
    toggle: toggleSnowfall,
    isEnabled: () => isEnabled,
    updateUI: () => updateUIButtons(isEnabled),
    bindButtons: bindButtons
  };

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

