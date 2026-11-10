/**
 * Department of CE & IT — Smart Management Dashboard & Portal
 * Universal Bootstrap 5 Toast Notification System Engine
 *
 * Placed in Top-Right Corner with Auto-Stacking, Bootstrap 5 Markup,
 * Color Coding (Green=Right/Success, Red=Wrong/Error, Dynamic Theme, etc.),
 * URL Notice Auto-Detection (login, logout, auth, notices), and Auto-Dismissal.
 */

(function (window, document) {
  'use strict';

  // --- SVG ICONS FOR STATUS BADGES ---
  const ICONS = {
    success: `<svg viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>`,
    error: `<svg viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>`,
    warning: `<svg viewBox="0 0 16 16"><path d="M7.938 2.016a.13.13 0 0 1 .125 0l6.857 11.856c.04.068.04.15 0 .218a.13.13 0 0 1-.125.07H1.205a.13.13 0 0 1-.125-.07.26.26 0 0 1 0-.218L7.937 2.016zM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>`,
    info: `<svg viewBox="0 0 16 16"><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>`,
    theme: `<svg viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>`
  };

  // --- HIGH-DEFINITION EMBEDDED DESKTOP LOGO DATA URI (100% BULLETPROOF ZERO-FAIL FALLBACK) ---
  const DEFAULT_LOGO_DATA_URI = 'data:image/png;base64,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';

  // --- ESCAPE HTML HELPER ---
  function escapeHtml(str) {
    if (typeof str !== 'string') return String(str ?? '');
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // --- GET OR CREATE TOAST CONTAINER ---
  function getOrCreateContainer() {
    let container = document.getElementById('portalToastContainer');
    if (!container) {
      const root = document.createElement('div');
      root.setAttribute('aria-live', 'polite');
      root.setAttribute('aria-atomic', 'true');
      root.className = 'position-relative toast-portal-root';
      root.innerHTML = `
        <div id="portalToastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 999999;" data-portal-toast-container></div>
      `;
      document.body.appendChild(root);
      container = document.getElementById('portalToastContainer');
    }
    return container;
  }

  // --- RESOLVE DESKTOP PORTAL LOGO URL ---
  function getPortalLogoUrl() {
    if (window.PORTAL_LOGO_URL) return window.PORTAL_LOGO_URL;

    // 1. Try to find the crisp favicon link tag in DOM
    const favLink = document.querySelector('link[rel="icon"][sizes="64x64"]') ||
                    document.querySelector('link[rel="icon"][type="image/svg+xml"]') ||
                    document.querySelector('link[rel="shortcut icon"]') ||
                    document.querySelector('link[rel="icon"]');
    if (favLink && favLink.href) {
      return favLink.href;
    }

    // 2. Resolve relative to base URL or current root
    const base = window.APP_BASE_URL || window.portalBaseUrl || '';
    if (base) {
      const cleanBase = base.endsWith('/') ? base : base + '/';
      return cleanBase + 'assets/images/favicon-64x64.png';
    }

    // 3. Fall back to global or embedded default logo
    return window.PORTAL_DEFAULT_LOGO || DEFAULT_LOGO_DATA_URI;
  }

  // --- RESOLVE TYPE, TITLE & ACCENT ---
  function resolveToastConfig(message, typeOrOptions) {
    let opts = {};
    if (typeof typeOrOptions === 'string') {
      opts.type = typeOrOptions;
    } else if (typeof typeOrOptions === 'boolean') {
      opts.type = typeOrOptions ? 'success' : 'error';
    } else if (typeOrOptions && typeof typeOrOptions === 'object') {
      opts = { ...typeOrOptions };
    }

    let type = (opts.type || '').toLowerCase().trim();
    const rawMsg = String(message || opts.message || '');

    // Auto-detect type if not explicitly provided or if "default"
    if (!type || type === 'default') {
      const lower = rawMsg.toLowerCase();
      if (rawMsg.includes('✓') || lower.includes('success') || lower.includes('verified') || lower.includes('completed') || lower.includes('dispatched') || lower.includes('saved')) {
        type = 'success';
      } else if (lower.includes('error') || lower.includes('wrong') || lower.includes('fail') || lower.includes('denied') || lower.includes('invalid') || lower.includes('cannot') || lower.includes('deactivated')) {
        type = 'error';
      } else if (lower.includes('warning') || lower.includes('caution') || lower.includes('wait') || lower.includes('expired')) {
        type = 'warning';
      } else {
        type = 'theme';
      }
    }

    // Aliases
    if (type === 'wright' || type === 'right' || type === 'ok') type = 'success';
    if (type === 'wrong' || type === 'danger' || type === 'alert_danger') type = 'error';
    if (type === 'warn' || type === 'alert') type = 'warning';

    // Titles
    let title = opts.title;
    if (!title) {
      switch (type) {
        case 'success': title = 'Success'; break;
        case 'error':   title = 'Error'; break;
        case 'warning': title = 'Notice'; break;
        case 'info':    title = 'Information'; break;
        case 'theme':   title = 'Notification'; break;
        default:        title = 'Notification'; break;
      }
    }

    // Icons
    let icon = opts.icon;
    if (!icon) {
      if (type.startsWith('#')) {
        icon = ICONS.theme;
      } else {
        icon = ICONS[type] || ICONS.theme;
      }
    }

    // Resolve desktop brand logo (Always guarantee crisp icon, fallback to embedded data URI)
    let logo = opts.logo;
    if (!logo) {
      logo = getPortalLogoUrl();
    }

    return {
      message: rawMsg,
      type: type,
      title: title,
      icon: icon,
      logo: logo,
      time: opts.time || 'just now',
      duration: typeof opts.duration === 'number' ? opts.duration : 4000,
      customColor: type.startsWith('#') ? type : (opts.color || null)
    };
  }

  // --- CORE SHOW FUNCTION ---
  function show(message, typeOrOptions) {
    if (!document.body) {
      window.addEventListener('DOMContentLoaded', () => show(message, typeOrOptions), { once: true });
      return;
    }

    const config = resolveToastConfig(message, typeOrOptions);
    const container = getOrCreateContainer();

    // Create Toast Element following Bootstrap 5 Toast markup
    const toastEl = document.createElement('div');
    const typeClass = config.customColor ? 'toast-custom' : `toast-${config.type}`;
    toastEl.className = `toast toast-portal toast-${config.type} ${typeClass}`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.setAttribute('data-bs-autohide', 'true');

    if (config.customColor) {
      toastEl.style.setProperty('--toast-accent-color', config.customColor);
      toastEl.style.setProperty('--toast-badge-bg', config.customColor + '22');
      toastEl.style.borderColor = config.customColor + '44';
    }

    const logoSrc = config.logo || DEFAULT_LOGO_DATA_URI;
    const logoHtml = `
      <img src="${escapeHtml(logoSrc)}" class="rounded me-2 toast-brand-logo" alt="Desktop Logo" width="22" height="22" onerror="this.onerror=null; this.src='${DEFAULT_LOGO_DATA_URI}';">
    `;

    toastEl.innerHTML = `
      <div class="toast-header">
        ${logoHtml}
        <strong class="me-auto toast-title">
          <span class="toast-status-badge">${config.icon}</span>
          <span>${escapeHtml(config.title)}</span>
        </strong>
        <small class="text-body-secondary toast-time">${escapeHtml(config.time)}</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        ${escapeHtml(config.message)}
      </div>
    `;

    // Append to container (stacks automatically top to bottom)
    container.appendChild(toastEl);

    // Trigger smooth entry transition
    requestAnimationFrame(() => {
      toastEl.classList.add('show');
    });

    // Dismiss Handler
    let dismissed = false;
    function dismissToast() {
      if (dismissed) return;
      dismissed = true;
      toastEl.classList.add('toast-dismissing');
      setTimeout(() => {
        try {
          if (toastEl.parentNode) {
            toastEl.parentNode.removeChild(toastEl);
          }
        } catch (e) {}
      }, 260);
    }

    // Wire close button
    const closeBtn = toastEl.querySelector('.btn-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dismissToast();
      });
    }

    // Auto-dismiss timer with pause on hover
    let dismissTimer = null;
    let remaining = config.duration;
    let startTime = Date.now();

    function startTimer(ms) {
      clearTimeout(dismissTimer);
      if (ms > 0) {
        dismissTimer = setTimeout(dismissToast, ms);
      }
    }

    if (config.duration > 0) {
      startTimer(config.duration);

      toastEl.addEventListener('mouseenter', () => {
        clearTimeout(dismissTimer);
        remaining -= Date.now() - startTime;
      });

      toastEl.addEventListener('mouseleave', () => {
        startTime = Date.now();
        startTimer(Math.max(1200, remaining));
      });
    }

    // If Bootstrap 5 is present, also attach Bootstrap Toast instance
    if (window.bootstrap && window.bootstrap.Toast) {
      try {
        const bs = window.bootstrap.Toast.getOrCreateInstance(toastEl, {
          autohide: false // handled by our timer for pause-on-hover & resilience
        });
        bs.show();
      } catch (err) {}
    }

    return toastEl;
  }

  // --- DISMISS ALL TOASTS ---
  function dismissAll() {
    const container = document.getElementById('portalToastContainer');
    if (!container) return;
    const toasts = container.querySelectorAll('.toast.toast-portal');
    toasts.forEach(t => {
      t.classList.add('toast-dismissing');
      setTimeout(() => { if (t.parentNode) t.parentNode.removeChild(t); }, 200);
    });
  }

  // --- CONVENIENCE METHODS ---
  const PortalToast = {
    show: show,
    success: (msg, title) => show(msg, { type: 'success', title: title || 'Success' }),
    error: (msg, title) => show(msg, { type: 'error', title: title || 'Error' }),
    warning: (msg, title) => show(msg, { type: 'warning', title: title || 'Notice' }),
    info: (msg, title) => show(msg, { type: 'info', title: title || 'Information' }),
    theme: (msg, title) => show(msg, { type: 'theme', title: title || 'Notification' }),
    dismissAll: dismissAll
  };

  // Global namespace exports
  window.PortalToast = PortalToast;
  window.showToast = show;
  window.showPortalToast = show;

  // --- THEME COLOR SYNC ---
  window.addEventListener('portal:theme-color-changed', (e) => {
    const hex = e.detail?.hex;
    if (!hex) return;
    const themeToasts = document.querySelectorAll('.toast.toast-portal.toast-theme');
    themeToasts.forEach(t => {
      t.style.setProperty('--toast-accent-color', hex);
    });
  });

  // --- AUTOMATIC URL NOTICE DETECTOR ---
  function checkUrlNotices() {
    try {
      const params = new URLSearchParams(window.location.search);
      let triggered = false;

      // 1. Logout notice
      if (params.get('notice') === 'logged_out' || params.get('logged_out') === '1') {
        PortalToast.info('You have been logged out successfully.', 'Logged Out');
        triggered = true;
      }
      // 1b. Successful login notice
      else if (params.get('notice') === 'login_success' || params.get('login') === 'success') {
        PortalToast.success('Login successful! Welcome to the portal.', 'Login Successful');
        triggered = true;
      }
      // 2. Account deactivated notice
      else if (params.get('notice') === 'account_deactivated') {
        PortalToast.error('Your faculty account has been deactivated. Access denied.', 'Access Denied');
        triggered = true;
      }
      // 3. Auth required notice
      else if (params.get('notice') === 'auth_required' || params.get('auth') === 'required') {
        PortalToast.warning('Please log in with your credentials to access this page.', 'Login Required');
        triggered = true;
      }
      // 4. Session expired notice
      else if (params.get('notice') === 'session_expired') {
        PortalToast.warning('Your session has expired. Please log in again.', 'Session Expired');
        triggered = true;
      }
      // 5. Generic error param
      else if (params.has('error')) {
        PortalToast.error(params.get('error'), 'Error');
        triggered = true;
      }
      // 6. Generic success param
      else if (params.has('success')) {
        PortalToast.success(params.get('success'), 'Success');
        triggered = true;
      }
      // 7. Generic message param
      else if (params.has('msg') || params.has('message')) {
        PortalToast.theme(params.get('msg') || params.get('message'), 'Notification');
        triggered = true;
      }

      // If a notice was triggered from query string, cleanly remove query param so refresh doesn't replay it
      if (triggered && window.history && window.history.replaceState) {
        params.delete('notice');
        params.delete('logged_out');
        params.delete('auth');
        params.delete('error');
        params.delete('success');
        params.delete('msg');
        params.delete('message');
        const newSearch = params.toString();
        const newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '') + window.location.hash;
        window.history.replaceState(null, '', newUrl);
      }
    } catch (e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkUrlNotices);
  } else {
    setTimeout(checkUrlNotices, 80);
  }

})(window, document);

