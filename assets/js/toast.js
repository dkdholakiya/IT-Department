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
    const cleanBase = base ? (base.endsWith('/') ? base : base + '/') : '';
    return cleanBase + 'assets/images/favicon-64x64.png';
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

    // Resolve desktop brand logo
    let logo = opts.logo;
    if (logo === undefined) {
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

    const logoHtml = config.logo ? `
      <img src="${escapeHtml(config.logo)}" class="rounded me-2 toast-brand-logo" alt="Portal Logo" width="22" height="22" onerror="this.style.display='none'">
    ` : '';

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

