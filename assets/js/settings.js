/**
 * Department Settings & Role-Based Access Control (RBAC) Client Controller
 * CE & IT Department Portal — High Performance Glassmorphic Interactivity
 */

(function () {
  'use strict';

  // Base state and configuration
  let rolesConfig = window.rolesConfig || { modules: [], roles: {} };
  let departmentUsers = window.departmentUsers || [];
  let currentActiveTab = window.initialActiveTab === 'roles' ? 'roles' : 'overview';
  let activeRoleKey = 'super_admin';

  // Working permissions set for the active role (allows toggling before saving)
  let workingPermissions = new Set();

  // Helper: Reliable API endpoint resolution
  function getApiUrl(endpoint) {
    const cleanEndpoint = endpoint.replace(/^\/+/, '');
    let base = window.portalBaseUrl;
    if (!base || base === '/' || base === './') {
      const m = window.location.pathname.match(/^(\/[^\/]+)/);
      if (m && (m[1].toLowerCase() === '/it' || m[1].toLowerCase() === '/portal')) {
        base = m[1] + '/';
      } else {
        base = '/';
      }
    }
    return base.replace(/\/+$/, '') + '/' + cleanEndpoint;
  }

  // Badge styles by permission type
  const typeBadgeStyles = {
    view: 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20',
    create: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
    edit: 'bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-purple-500/20',
    delete: 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 font-bold',
    toggle: 'bg-teal-500/15 text-teal-600 dark:text-teal-400 border border-teal-500/20',
    send: 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20',
    sync: 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20',
    upload: 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20',
    export: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20'
  };

  /**
   * Helper: Show Toast Notification using Universal Portal Toast Engine
   */
  function showToast(message, type = 'success') {
    const isError = (type === true || type === 'error' || type === 'danger');
    const toastType = isError ? 'error' : (type === false || type === 'success' ? 'success' : type);

    if (typeof window.showPortalToast === 'function') {
      window.showPortalToast(message, toastType);
      return;
    }
    if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      window.PortalToast.show(message, toastType);
      return;
    }
    if (typeof window.showToast === 'function' && window.showToast !== showToast) {
      window.showToast(message, toastType);
      return;
    }

    // Fallback: console
    console.log(`[${toastType.toUpperCase()}] ${message}`);
  }

  /**
   * Initialize on DOM Ready
   */
  document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initThemeStudio();
    initRoleMatrix();
    initStaffRoles();
    initCreateRoleModal();
    initDeleteRoleModal();

    // Check if initial tab was requested via query parameter
    if (window.initialActiveTab === 'roles') {
      switchTab('roles');
    }
  });

  /**
   * 1. Tab Navigation System
   */
  function initTabs() {
    const tabBtns = document.querySelectorAll('.settings-tab-btn');
    tabBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        const tab = btn.getAttribute('data-tab');
        switchTab(tab);
      });
    });

    const heroBtn = document.getElementById('openRoleMatrixFromHeroBtn');
    if (heroBtn) {
      heroBtn.addEventListener('click', () => switchTab('roles'));
    }

    const jumpStaffBtn = document.getElementById('jumpToStaffRolesBtn');
    if (jumpStaffBtn) {
      jumpStaffBtn.addEventListener('click', () => switchTab('users'));
    }
  }

  function switchTab(tabName) {
    currentActiveTab = tabName;

    // Update buttons
    document.querySelectorAll('.settings-tab-btn').forEach((btn) => {
      const match = btn.getAttribute('data-tab') === tabName;
      btn.classList.toggle('active', match);
      if (match) {
        btn.classList.add('bg-[var(--color-primary)]', 'text-white', 'shadow-sm');
        btn.classList.remove('text-[var(--text-muted)]');
      } else {
        btn.classList.remove('bg-[var(--color-primary)]', 'text-white', 'shadow-sm');
        btn.classList.add('text-[var(--text-muted)]');
      }
    });

    // Toggle content sections
    const sOverview = document.getElementById('sectionOverview');
    const sRoles = document.getElementById('sectionRoles');
    const sUsers = document.getElementById('sectionUsers');

    if (sOverview) sOverview.classList.toggle('hidden', tabName !== 'overview');
    if (sRoles) sRoles.classList.toggle('hidden', tabName !== 'roles');
    if (sUsers) sUsers.classList.toggle('hidden', tabName !== 'users');

    if (tabName === 'roles') {
      renderRoleSelectorPills();
      loadRoleIntoMatrix(activeRoleKey);
    } else if (tabName === 'users') {
      renderStaffRolesTable();
    }
  }

  /**
   * 2. Theme Studio (Live Palette & Custom Color)
   */
  function initThemeStudio() {
    const currentThemeColor = (localStorage.getItem('portal_theme_color') || '#10B981').toUpperCase();

    // Palette Swatch Buttons
    document.querySelectorAll('[data-apply-color]').forEach((btn) => {
      const color = btn.getAttribute('data-apply-color');
      const label = btn.querySelector('span:last-child')?.textContent || color;

      // Mark active if matching saved color
      updateSwatchActiveState(btn, color.toUpperCase() === currentThemeColor);

      btn.addEventListener('click', () => {
        applyBrandColor(color, label);
      });
    });

    // Custom Color Picker
    const customPicker = document.getElementById('themeCustomPicker');
    if (customPicker) {
      customPicker.value = currentThemeColor;
      customPicker.addEventListener('input', (e) => {
        applyBrandColor(e.target.value, 'Custom Palette');
      });
    }
  }

  function updateSwatchActiveState(btn, isActive) {
    if (!btn) return;
    const dot = btn.querySelector('span:first-child');
    if (dot) {
      dot.classList.add('flex', 'items-center', 'justify-center');
    }
    if (isActive) {
      btn.classList.add('ring-2', 'ring-[var(--color-primary)]', 'border-[var(--color-primary)]', 'shadow-md', 'bg-[var(--color-primary)]/10');
      btn.classList.remove('border-transparent');
      if (dot) {
        dot.innerHTML = `<svg class="w-2.5 h-2.5 text-white block pointer-events-none" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;
      }
    } else {
      btn.classList.remove('ring-2', 'ring-[var(--color-primary)]', 'border-[var(--color-primary)]', 'shadow-md', 'bg-[var(--color-primary)]/10');
      if (dot) {
        dot.innerHTML = '';
      }
    }
  }

  function applyBrandColor(color, label = '') {
    if (!color) return;

    // 1. Delegate to Navbar / Global Theme Controller if available
    if (typeof window.applyCustomColorPalette === 'function') {
      window.applyCustomColorPalette(color);
    } else {
      document.documentElement.style.setProperty('--color-primary', color);
      document.documentElement.style.setProperty('--sidebar-active-bg', color);
      localStorage.setItem('portal_theme_color', color);
    }

    // 2. Update visual indicators on all palette buttons
    document.querySelectorAll('[data-apply-color]').forEach((btn) => {
      const btnColor = btn.getAttribute('data-apply-color');
      updateSwatchActiveState(btn, btnColor.toUpperCase() === color.toUpperCase());
    });

    const customPicker = document.getElementById('themeCustomPicker');
    if (customPicker) {
      customPicker.value = color;
    }

    showToast(`Theme accent updated to ${label || color}`, 'success');
  }

  /**
   * 3. Role & Permission Management Matrix
   */
  function initRoleMatrix() {
    // Search filter input
    const searchInput = document.getElementById('permSearchInput');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        filterPermissionCards(e.target.value.toLowerCase().trim());
      });
    }

    // Reset Defaults Button
    const resetBtn = document.getElementById('resetRoleDefaultsBtn');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (confirm(`Reset ${activeRoleKey.toUpperCase()} permissions back to recommended system defaults?`)) {
          resetRoleToPresetDefaults(activeRoleKey);
        }
      });
    }

    // Save Permissions Button
    const saveBtn = document.getElementById('saveRolePermissionsBtn');
    if (saveBtn) {
      saveBtn.addEventListener('click', () => {
        saveCurrentRolePermissions();
      });
    }

    // Initialize modals
    initCreateRoleModal();
    initDeleteRoleModal();

    // Render initial role pills and load active role
    renderRoleSelectorPills();
    selectRole('super_admin');
  }

  /**
   * Render dynamic role switcher pills (including newly created custom roles)
   */
  function renderRoleSelectorPills() {
    const container = document.getElementById('roleSelectorPills');
    if (!container) return;

    const roles = rolesConfig.roles || {};
    const roleKeys = Object.keys(roles);

    if (!roleKeys.includes(activeRoleKey)) {
      activeRoleKey = roleKeys[0] || 'super_admin';
    }

    const iconMap = {
      super_admin: '👑',
      hod: '🏛️',
      sub_admin: '🛡️',
      faculty: '👨‍🏫'
    };

    const conciseNames = {
      super_admin: 'Super Admin',
      hod: 'HOD',
      sub_admin: 'Sub Admin',
      faculty: 'Faculty'
    };

    const conciseBadges = {
      super_admin: 'Master',
      hod: 'No Delete',
      sub_admin: 'View Only',
      faculty: 'Academic'
    };

    let html = '';
    roleKeys.forEach((key) => {
      const roleDef = roles[key] || {};
      const isActive = key === activeRoleKey;
      const icon = iconMap[key] || '⭐';
      const name = conciseNames[key] || roleDef.name || key;
      const badge = conciseBadges[key] || roleDef.badge || (roleDef.is_system ? 'Core' : 'Custom');
      const isCustom = !roleDef.is_system;
      const canDelete = isCustom && (window.canDeleteRole !== false);

      const deleteIconHtml = canDelete ? `
        <span
          class="delete-role-trigger ml-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold text-rose-300 hover:text-white hover:bg-rose-500 transition cursor-pointer"
          data-role="${key}"
          data-role-name="${escapeHtml(name)}"
          title="Delete custom role ${escapeHtml(name)}"
        >
          &times;
        </span>
      ` : '';

      html += `
        <button
          type="button"
          data-role="${key}"
          class="role-pill-btn ${isActive ? 'active bg-[var(--color-primary)] text-white shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text-primary)]'} px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap shrink-0"
        >
          <span>${icon} ${escapeHtml(name)}</span>
          <span class="px-1.5 py-0.5 rounded-full text-[9px] ${isActive ? 'bg-white/20 text-white' : (isCustom ? 'bg-purple-500/20 text-purple-600 dark:text-purple-400 font-bold' : 'bg-black/10 dark:bg-white/10 text-[var(--text-muted)]')}">${escapeHtml(badge)}</span>
          ${deleteIconHtml}
        </button>
      `;
    });

    container.innerHTML = html;

    // Attach click listeners to role pills
    container.querySelectorAll('.role-pill-btn').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        // If clicking the delete trigger inside the pill, don't trigger role select
        if (e.target.closest('.delete-role-trigger')) return;
        const role = btn.getAttribute('data-role');
        selectRole(role);
      });
    });

    // Attach click listeners to delete triggers
    container.querySelectorAll('.delete-role-trigger').forEach((delBtn) => {
      delBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const role = delBtn.getAttribute('data-role');
        const roleName = delBtn.getAttribute('data-role-name');
        openDeleteRoleModal(role, roleName);
      });
    });
  }

  function selectRole(roleKey) {
    activeRoleKey = roleKey;

    // Highlight active role pill
    document.querySelectorAll('.role-pill-btn').forEach((btn) => {
      const match = btn.getAttribute('data-role') === roleKey;
      btn.classList.toggle('active', match);
      if (match) {
        btn.classList.add('bg-[var(--color-primary)]', 'text-white', 'shadow-sm');
        btn.classList.remove('text-[var(--text-muted)]');
      } else {
        btn.classList.remove('bg-[var(--color-primary)]', 'text-white', 'shadow-sm');
        btn.classList.add('text-[var(--text-muted)]');
      }
    });

    loadRoleIntoMatrix(roleKey);
  }

  function loadRoleIntoMatrix(roleKey) {
    const roleDef = rolesConfig.roles[roleKey] || {};
    const rolePerms = roleDef.permissions || [];

    // Initialize working permissions
    if (roleKey === 'super_admin' || rolePerms.includes('*')) {
      workingPermissions = new Set(allAvailablePermissionIds());
    } else {
      workingPermissions = new Set(rolePerms);
    }

    // Update Context Banner & Delete button visibility
    updateRoleBanner(roleKey, roleDef);

    // Render Module Panels
    renderModulePermissions();
    updateRatioDisplay();
  }

  function updateRoleBanner(roleKey, roleDef) {
    const iconEl = document.getElementById('roleBannerIcon');
    const titleEl = document.getElementById('roleBannerTitle');
    const descEl = document.getElementById('roleBannerDesc');
    const countEl = document.getElementById('roleActiveCountBadge');
    const deleteBtn = document.getElementById('deleteCurrentRoleBtn');

    const icons = {
      super_admin: '👑',
      hod: '🏛️',
      sub_admin: '🛡️',
      faculty: '👨‍🏫'
    };

    if (iconEl) iconEl.textContent = icons[roleKey] || '⭐';
    if (titleEl) titleEl.textContent = roleDef.name || roleKey;
    if (descEl) descEl.textContent = roleDef.description || 'Custom department security role.';

    // Show Delete button ONLY for non-system custom roles, if user has delete permission
    if (deleteBtn) {
      const isSystem = (roleDef.is_system === true || ['super_admin', 'hod', 'sub_admin', 'faculty'].includes(roleKey));
      const canDelete = window.canDeleteRole && !isSystem;
      deleteBtn.classList.toggle('hidden', !canDelete);
      deleteBtn.classList.toggle('inline-flex', canDelete);
    }

    if (countEl) {
      if (roleKey === 'super_admin') {
        const total = allAvailablePermissionIds().length;
        countEl.textContent = `All ${total} Permissions Active (*)`;
        countEl.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-500/15 text-purple-600 dark:text-purple-400 whitespace-nowrap';
      } else if (roleKey === 'hod') {
        countEl.textContent = `${workingPermissions.size} Permissions (Delete Blocked)`;
        countEl.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/15 text-blue-600 dark:text-blue-400 whitespace-nowrap';
      } else if (roleKey === 'sub_admin') {
        countEl.textContent = `${workingPermissions.size} View-Only Permissions`;
        countEl.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 whitespace-nowrap';
      } else {
        countEl.textContent = `${workingPermissions.size} Active Permissions`;
        countEl.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 whitespace-nowrap';
      }
    }
  }

  function allAvailablePermissionIds() {
    const all = [];
    (rolesConfig.modules || []).forEach((m) => {
      (m.permissions || []).forEach((p) => {
        if (p.id) all.push(p.id);
      });
    });
    return all;
  }

  /**
   * Render all modules and granular switches
   */
  function renderModulePermissions() {
    const container = document.getElementById('modulePermissionsContainer');
    if (!container) return;

    const modules = rolesConfig.modules || [];
    if (!modules.length) {
      container.innerHTML = `<div class="p-4 text-center text-xs text-[var(--text-muted)]">No permission modules loaded.</div>`;
      return;
    }

    const isSuperAdmin = activeRoleKey === 'super_admin';
    const isHod = activeRoleKey === 'hod';
    const isSubAdmin = activeRoleKey === 'sub_admin';

    let html = '';

    modules.forEach((mod, modIdx) => {
      const perms = mod.permissions || [];
      const totalInMod = perms.length;
      const activeInMod = perms.filter((p) => workingPermissions.has(p.id)).length;
      const allActive = totalInMod > 0 && activeInMod === totalInMod;

      html += `
        <div class="module-card glass-card rounded-2xl border border-black/5 dark:border-white/10 overflow-hidden" data-module-id="${mod.id}">
          
          <!-- Module Header -->
          <div class="p-3.5 sm:p-4 bg-black/[0.02] dark:bg-white/[0.02] border-b border-black/5 dark:border-white/10 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
              <span class="w-8 h-8 rounded-xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center font-bold text-xs shrink-0">
                ${modIdx + 1}
              </span>
              <div>
                <h4 class="text-xs sm:text-sm font-black text-[var(--text-primary)] flex items-center gap-2">
                  <span>${escapeHtml(mod.name)}</span>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/5 dark:bg-white/10 text-[var(--text-muted)]">
                    <span class="mod-active-count">${activeInMod}</span>/${totalInMod} active
                  </span>
                </h4>
                <p class="text-[11px] text-[var(--text-muted)] line-clamp-1">${escapeHtml(mod.description || '')}</p>
              </div>
            </div>

            <!-- Quick Toggle All for this module -->
            ${
              isSuperAdmin
                ? `<span class="text-[11px] font-bold text-purple-600 dark:text-purple-400">Master Locked (*)</span>`
                : `<button
                    type="button"
                    class="toggle-all-module-btn px-2.5 py-1 rounded-lg glass-pill text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] transition"
                    data-module-id="${mod.id}"
                    data-target-state="${allActive ? 'off' : 'on'}"
                  >
                    ${allActive ? 'Deselect All' : 'Select All'}
                  </button>`
            }
          </div>

          <!-- Permissions Grid -->
          <div class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
      `;

      perms.forEach((p) => {
        const isChecked = workingPermissions.has(p.id) || isSuperAdmin;
        const isDeleteAction = p.type === 'delete';
        const isWriteAction = ['create', 'edit', 'delete', 'toggle', 'send', 'upload', 'sync'].includes(p.type);

        // HOD cannot have delete
        const lockedByHod = isHod && isDeleteAction;
        // Sub Admin cannot have write/action permissions
        const lockedBySubAdmin = isSubAdmin && isWriteAction;
        // Super Admin is locked full master
        const lockedBySuperAdmin = isSuperAdmin;

        const isLocked = lockedBySuperAdmin || lockedByHod || lockedBySubAdmin;
        const lockReason = lockedBySuperAdmin
          ? 'Master Super Admin Wildcard'
          : lockedByHod
          ? 'HOD Role Strictly Forbidden from Delete'
          : lockedBySubAdmin
          ? 'Sub Admin is Strictly View-Only'
          : '';

        const badgeClass = typeBadgeStyles[p.type] || 'bg-gray-500/15 text-gray-500 border border-gray-500/20';

        html += `
          <label
            class="perm-item-label relative p-3 rounded-xl border transition-all flex items-start gap-3 cursor-pointer select-none ${
              isChecked
                ? 'border-[var(--color-primary)]/40 bg-[var(--color-primary)]/[0.04]'
                : 'border-black/5 dark:border-white/10 hover:border-black/20 dark:hover:border-white/20'
            } ${isLocked ? 'opacity-80 cursor-not-allowed' : ''}"
            data-perm-id="${p.id}"
            data-perm-name="${escapeHtml((p.name || '').toLowerCase())}"
            data-perm-desc="${escapeHtml((p.description || '').toLowerCase())}"
            title="${lockReason}"
          >
            <!-- Checkbox Switch -->
            <input
              type="checkbox"
              class="perm-checkbox mt-0.5 rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] shrink-0 w-4 h-4 cursor-pointer"
              value="${p.id}"
              ${isChecked ? 'checked' : ''}
              ${isLocked ? 'disabled' : ''}
            />

            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-1 mb-1">
                <span class="text-xs font-bold text-[var(--text-primary)] truncate">${escapeHtml(p.name)}</span>
                <span class="text-[9.5px] uppercase tracking-wider font-extrabold px-1.5 py-0.2 rounded-full shrink-0 ${badgeClass}">
                  ${escapeHtml(p.type || 'action')}
                </span>
              </div>
              <p class="text-[10.5px] text-[var(--text-muted)] line-clamp-2 leading-relaxed">${escapeHtml(p.description || '')}</p>
              
              ${
                isLocked
                  ? `<span class="inline-flex items-center gap-1 mt-1 text-[9.5px] font-bold ${
                      lockedByHod
                        ? 'text-rose-600 dark:text-rose-400'
                        : lockedBySubAdmin
                        ? 'text-amber-600 dark:text-amber-400'
                        : 'text-purple-600 dark:text-purple-400'
                    }">🔒 ${lockReason}</span>`
                  : ''
              }
            </div>
          </label>
        `;
      });

      html += `
          </div>
        </div>
      `;
    });

    container.innerHTML = html;

    // Attach individual checkbox change listeners
    container.querySelectorAll('.perm-checkbox:not([disabled])').forEach((chk) => {
      chk.addEventListener('change', (e) => {
        const id = e.target.value;
        if (e.target.checked) {
          workingPermissions.add(id);
        } else {
          workingPermissions.delete(id);
        }
        updateRatioDisplay();
        updateModuleCountBadge(chk.closest('.module-card'));
      });
    });

    // Attach module "Select All / Deselect All" listeners
    container.querySelectorAll('.toggle-all-module-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const modId = btn.getAttribute('data-module-id');
        toggleModulePermissions(modId);
      });
    });
  }

  function toggleModulePermissions(modId) {
    const mod = (rolesConfig.modules || []).find((m) => m.id === modId);
    if (!mod) return;

    const perms = mod.permissions || [];
    const isHod = activeRoleKey === 'hod';
    const isSubAdmin = activeRoleKey === 'sub_admin';

    // Eligible perms for this role
    const eligiblePerms = perms.filter((p) => {
      if (isHod && p.type === 'delete') return false;
      if (isSubAdmin && ['create', 'edit', 'delete', 'toggle', 'send', 'upload', 'sync'].includes(p.type)) return false;
      return true;
    });

    const allCurrentlyEnabled = eligiblePerms.every((p) => workingPermissions.has(p.id));

    eligiblePerms.forEach((p) => {
      if (allCurrentlyEnabled) {
        workingPermissions.delete(p.id);
      } else {
        workingPermissions.add(p.id);
      }
    });

    renderModulePermissions();
    updateRatioDisplay();
  }

  function updateModuleCountBadge(card) {
    if (!card) return;
    const modId = card.getAttribute('data-module-id');
    const mod = (rolesConfig.modules || []).find((m) => m.id === modId);
    if (!mod) return;
    const activeCount = (mod.permissions || []).filter((p) => workingPermissions.has(p.id)).length;
    const countSpan = card.querySelector('.mod-active-count');
    if (countSpan) countSpan.textContent = activeCount;
  }

  function updateRatioDisplay() {
    const ratioEl = document.getElementById('permSelectedRatio');
    const countEl = document.getElementById('roleActiveCountBadge');
    const total = allAvailablePermissionIds().length;
    const active = workingPermissions.size;

    if (ratioEl) {
      ratioEl.textContent = `${active} of ${total} permissions enabled`;
    }

    if (countEl && activeRoleKey !== 'super_admin') {
      countEl.textContent = `${active} Active Permissions`;
    }
  }

  function filterPermissionCards(keyword) {
    document.querySelectorAll('.module-card').forEach((modCard) => {
      let anyItemVisible = false;
      modCard.querySelectorAll('.perm-item-label').forEach((label) => {
        const name = label.getAttribute('data-perm-name') || '';
        const desc = label.getAttribute('data-perm-desc') || '';
        const match = !keyword || name.includes(keyword) || desc.includes(keyword);
        label.classList.toggle('hidden', !match);
        if (match) anyItemVisible = true;
      });
      modCard.classList.toggle('hidden', !anyItemVisible);
    });
  }

  function resetRoleToPresetDefaults(roleKey) {
    const all = allAvailablePermissionIds();

    if (roleKey === 'super_admin') {
      workingPermissions = new Set(all);
    } else if (roleKey === 'hod') {
      const deletePerms = new Set();
      (rolesConfig.modules || []).forEach((m) => {
        (m.permissions || []).forEach((p) => {
          if (p.type === 'delete') deletePerms.add(p.id);
        });
      });
      workingPermissions = new Set(all.filter((id) => !deletePerms.has(id)));
    } else if (roleKey === 'sub_admin') {
      const viewPerms = [];
      (rolesConfig.modules || []).forEach((m) => {
        (m.permissions || []).forEach((p) => {
          if (p.type === 'view' || p.type === 'export') viewPerms.push(p.id);
        });
      });
      workingPermissions = new Set(viewPerms);
    } else if (roleKey === 'faculty') {
      workingPermissions = new Set([
        'dashboard.view',
        'dashboard.cameras',
        'calendar.view',
        'calendar.print',
        'calendar.copy',
        'timetable.view',
        'student_timetable.view',
        'student_timetable.print',
        'report.view',
        'report.submit',
        'report.draft',
        'report.preview',
        'report.print',
        'report.reset',
        'syllabus.view',
        'syllabus.export_all',
        'syllabus.export_subject',
        'syllabus.export_exam'
      ]);
    }

    renderModulePermissions();
    updateRatioDisplay();
    showToast(`Restored ${roleKey.toUpperCase()} to recommended default permissions.`, 'success');
  }

  /**
   * Save Active Role Permissions to Backend
   */
  async function saveCurrentRolePermissions() {
    const saveBtn = document.getElementById('saveRolePermissionsBtn');
    if (saveBtn) {
      saveBtn.disabled = true;
      saveBtn.innerHTML = `
        <span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        <span>Saving...</span>
      `;
    }

    try {
      const permsArray = Array.from(workingPermissions);
      const url = getApiUrl('api/settings/roles/save');

      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify({
          roleKey: activeRoleKey,
          permissions: permsArray
        })
      });

      const data = await res.json().catch(() => null);

      if (!res.ok || !data || !data.success) {
        const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to save permissions.`;
        throw new Error(errorMsg);
      }

      showToast(data.message || 'Permissions updated successfully!', 'success');
      if (rolesConfig.roles[activeRoleKey]) {
        rolesConfig.roles[activeRoleKey].permissions = data.permissions || permsArray;
      }
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = `
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
          <span>Save Permissions</span>
        `;
      }
    }
  }

  /**
   * 4. Modal: Create New Security Role
   */
  function initCreateRoleModal() {
    const openBtn = document.getElementById('openCreateRoleModalBtn');
    const modal = document.getElementById('createRoleModal');
    const closeBtn = document.getElementById('closeCreateRoleModalBtn');
    const cancelBtn = document.getElementById('cancelCreateRoleBtn');
    const form = document.getElementById('createRoleForm');
    const nameInput = document.getElementById('newRoleName');
    const keyInput = document.getElementById('newRoleKey');
    const colorInput = document.getElementById('newRoleColor');
    const cloneFromInput = document.getElementById('newRoleCloneFrom');
    const previewPill = document.getElementById('rolePreviewPill');
    const previewName = document.getElementById('previewRoleName');

    const swatchColorBgClasses = {
      teal: 'bg-teal-500',
      emerald: 'bg-emerald-500',
      blue: 'bg-blue-500',
      purple: 'bg-purple-500',
      amber: 'bg-amber-500',
      rose: 'bg-rose-500',
      indigo: 'bg-indigo-500',
      cyan: 'bg-cyan-500'
    };

    let keyManuallyChanged = false;

    // Interactive Color Swatches Wiring
    const swatchButtons = document.querySelectorAll('#roleColorSwatches .color-swatch-btn');
    swatchButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const color = btn.getAttribute('data-color') || 'teal';
        if (colorInput) colorInput.value = color;

        swatchButtons.forEach((b) => {
          b.classList.remove('ring-2', 'ring-offset-2', 'ring-teal-500', 'ring-emerald-500', 'ring-blue-500', 'ring-purple-500', 'ring-amber-500', 'ring-rose-500', 'ring-indigo-500', 'ring-cyan-500', 'active');
          b.innerHTML = '';
        });

        btn.classList.add('ring-2', 'ring-offset-2', `ring-${color}-500`, 'active');
        btn.innerHTML = `<svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;

        if (previewPill) {
          previewPill.className = `px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 ${swatchColorBgClasses[color] || 'bg-teal-500'} text-white shadow-sm`;
        }
      });
    });

    // Starter Preset Tiles Wiring
    const presetCards = document.querySelectorAll('#presetCardsContainer .preset-card');
    presetCards.forEach((card) => {
      card.addEventListener('click', () => {
        presetCards.forEach((c) => {
          c.classList.remove('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
          c.classList.add('border-black/10', 'dark:border-white/10');
        });

        card.classList.add('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
        card.classList.remove('border-black/10', 'dark:border-white/10');

        if (cloneFromInput) {
          cloneFromInput.value = card.getAttribute('data-preset') || '';
        }
      });
    });

    function resetModalForm() {
      keyManuallyChanged = false;
      if (form) form.reset();
      if (colorInput) colorInput.value = 'teal';
      if (cloneFromInput) cloneFromInput.value = 'faculty';
      if (previewName) previewName.textContent = 'New Role';

      // Reset swatch buttons
      swatchButtons.forEach((b, idx) => {
        b.classList.remove('ring-2', 'ring-offset-2', 'ring-teal-500', 'ring-emerald-500', 'ring-blue-500', 'ring-purple-500', 'ring-amber-500', 'ring-rose-500', 'ring-indigo-500', 'ring-cyan-500', 'active');
        b.innerHTML = '';
        if (idx === 0) {
          b.classList.add('ring-2', 'ring-offset-2', 'ring-teal-500', 'active');
          b.innerHTML = `<svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;
        }
      });

      // Reset preset cards
      presetCards.forEach((c, idx) => {
        c.classList.remove('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
        c.classList.add('border-black/10', 'dark:border-white/10');
        if (idx === 0) {
          c.classList.add('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
          c.classList.remove('border-black/10', 'dark:border-white/10');
        }
      });

      if (previewPill) {
        previewPill.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-teal-500 text-white shadow-sm';
      }
    }

    if (openBtn && modal) {
      openBtn.addEventListener('click', () => {
        resetModalForm();
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        if (nameInput) nameInput.focus();
      });
    }

    function closeModal() {
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
      }
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Auto-generate slug key and live preview while typing name
    if (nameInput) {
      nameInput.addEventListener('input', () => {
        const val = nameInput.value.trim();
        if (previewName) {
          previewName.textContent = val || 'New Role';
        }
        if (!keyManuallyChanged && keyInput) {
          keyInput.value = val
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
        }
      });
    }

    if (keyInput) {
      keyInput.addEventListener('input', () => {
        keyManuallyChanged = true;
      });
    }

    // Form submission
    if (form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('submitCreateRoleBtn');
        const name = (nameInput?.value || '').trim();
        const key = (keyInput?.value || '').trim();
        const badgeColor = colorInput?.value || 'teal';
        const description = (document.getElementById('newRoleDesc')?.value || '').trim();
        const cloneFrom = cloneFromInput?.value || '';

        if (!name || !key) {
          showToast('Role Name and Key are required.', 'error');
          return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = `
            <span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>Creating...</span>
          `;
        }

        try {
          const url = getApiUrl('api/settings/roles/create');
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json'
            },
            body: JSON.stringify({
              name,
              key,
              badge: name,
              badge_color: badgeColor,
              description,
              clone_from: cloneFrom
            })
          });

          const data = await res.json().catch(() => null);

          if (!res.ok || !data || !data.success) {
            const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to create role.`;
            throw new Error(errorMsg);
          }

          // Successfully created role
          showToast(data.message || `Role '${name}' created successfully!`, 'success');

          // Add to local state
          if (data.role) {
            rolesConfig.roles[data.roleKey] = data.role;
          }

          closeModal();

          // Refresh UI
          renderRoleSelectorPills();
          selectRole(data.roleKey);

          // Update Staff Roles table dropdown if open
          if (currentActiveTab === 'users') {
            renderStaffRolesTable();
          }
        } catch (err) {
          showToast(err.message, 'error');
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
              <span>Create Role</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
            `;
          }
        }
      });
    }
  }

  /**
   * 5. Modal: Delete Custom Security Role
   */
  function initDeleteRoleModal() {
    const deleteBtn = document.getElementById('deleteCurrentRoleBtn');
    const modal = document.getElementById('deleteRoleModal');
    const cancelBtn = document.getElementById('cancelDeleteRoleBtn');
    const confirmBtn = document.getElementById('confirmDeleteRoleBtn');
    const promptNameEl = document.getElementById('deleteRoleTargetName');
    const reassignSelect = document.getElementById('deleteRoleReassignSelect');

    function closeModal() {
      if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
      }
    }

    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (deleteBtn && modal) {
      deleteBtn.addEventListener('click', () => {
        const roleDef = rolesConfig.roles[activeRoleKey] || {};
        if (roleDef.is_system) {
          showToast('System core roles cannot be deleted.', 'error');
          return;
        }

        if (promptNameEl) {
          promptNameEl.textContent = `"${roleDef.name || activeRoleKey}"`;
        }

        // Populate reassign options with all remaining roles
        if (reassignSelect) {
          let opts = '';
          Object.keys(rolesConfig.roles).forEach((k) => {
            if (k !== activeRoleKey) {
              const r = rolesConfig.roles[k];
              opts += `<option value="${k}" ${k === 'faculty' ? 'selected' : ''}>${escapeHtml(r.name || k)}</option>`;
            }
          });
          reassignSelect.innerHTML = opts;
        }

        modal.classList.remove('hidden');
        modal.style.display = 'flex';
      });
    }

    if (confirmBtn) {
      confirmBtn.addEventListener('click', async () => {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
          <span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span>Deleting...</span>
        `;

        try {
          const reassignTo = reassignSelect ? reassignSelect.value : 'faculty';
          const targetKey = activeRoleKey;
          const url = getApiUrl('api/settings/roles/delete');

          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json'
            },
            body: JSON.stringify({
              roleKey: targetKey,
              reassignTo
            })
          });

          const data = await res.json().catch(() => null);

          if (!res.ok || !data || !data.success) {
            const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to delete role.`;
            throw new Error(errorMsg);
          }

          showToast(data.message || 'Role deleted successfully.', 'success');

          // Delete from local config
          delete rolesConfig.roles[targetKey];

          // Reassign locally in departmentUsers
          departmentUsers.forEach((u) => {
            if ((u.role || '').toLowerCase() === targetKey) {
              u.role = reassignTo;
            }
          });

          closeModal();

          // Switch active role back to faculty
          activeRoleKey = 'faculty';
          renderRoleSelectorPills();
          selectRole('faculty');

          if (currentActiveTab === 'users') {
            renderStaffRolesTable();
          }
        } catch (err) {
          showToast(err.message, 'error');
        } finally {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = `<span>Delete Role</span>`;
        }
      });
    }
  }

  /**
   * 6. Staff Role Bindings Table
   */
  function initStaffRoles() {
    const searchInput = document.getElementById('staffRoleSearchInput');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        filterStaffTable(query);
      });
    }
  }

  function renderStaffRolesTable() {
    const tbody = document.getElementById('staffRoleTableBody');
    if (!tbody) return;

    if (!departmentUsers.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-xs text-[var(--text-muted)]">No faculty members found.</td></tr>`;
      return;
    }

    const availableRoles = rolesConfig.roles || {};
    let html = '';

    departmentUsers.forEach((u) => {
      const userRole = (u.role || 'faculty').toLowerCase();
      const isSuperAdminPrimary = (u.id === 'adminit' || u.id === 'admince');

      html += `
        <tr class="staff-row hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition" data-user-id="${escapeHtml(u.id || '')}" data-user-name="${escapeHtml((u.name || '').toLowerCase())}" data-user-dept="${escapeHtml((u.department || '').toLowerCase())}" data-user-empid="${escapeHtml((u.empId || '').toLowerCase())}">
          <td class="py-2 px-3">
            <div class="flex items-center gap-2">
              <span class="w-7 h-7 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center font-bold text-[11px] shrink-0">
                ${escapeHtml(u.initials || u.name ? u.name.charAt(0) : '?')}
              </span>
              <div>
                <strong class="text-xs text-[var(--text-primary)] block">${escapeHtml(u.name || 'Unknown')}</strong>
                <span class="text-[10px] text-[var(--text-muted)]">${escapeHtml(u.email || u.id || '')}</span>
              </div>
            </div>
          </td>
          <td class="py-2 px-3 text-[11px] text-[var(--text-muted)]">${escapeHtml(u.department || 'CE & IT')}</td>
          <td class="py-2 px-3 text-[11px] text-[var(--text-muted)]">${escapeHtml(u.designation || 'Faculty Member')}</td>
          <td class="py-2 px-3">
            ${
              isSuperAdminPrimary
                ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-purple-500/15 text-purple-600 dark:text-purple-400">
                    👑 Super Admin (Primary Protected)
                  </span>`
                : `<select
                    class="staff-role-select w-full sm:w-48 px-2.5 py-1 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-xs font-bold text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] cursor-pointer"
                    data-user-id="${escapeHtml(u.id || '')}"
                    ${!window.canAssignRoles ? 'disabled' : ''}
                  >
                    ${renderRoleOptions(userRole, availableRoles)}
                  </select>`
            }
          </td>
          <td class="py-2 px-3 text-right">
            ${
              isSuperAdminPrimary
                ? `<span class="text-[10.5px] text-[var(--text-muted)]">Locked</span>`
                : `<button
                    type="button"
                    class="save-single-user-role-btn px-2.5 py-1 rounded-lg bg-[var(--color-primary)]/15 text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white text-[11px] font-bold transition cursor-pointer"
                    data-user-id="${escapeHtml(u.id || '')}"
                    ${!window.canAssignRoles ? 'disabled' : ''}
                  >
                    Save Role
                  </button>`
            }
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = html;

    // Attach save button events
    tbody.querySelectorAll('.save-single-user-role-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const userId = btn.getAttribute('data-user-id');
        const select = tbody.querySelector(`.staff-role-select[data-user-id="${userId}"]`);
        if (select) {
          saveUserRole(userId, select.value, btn);
        }
      });
    });

    // Also auto-save on select change
    tbody.querySelectorAll('.staff-role-select').forEach((sel) => {
      sel.addEventListener('change', () => {
        const userId = sel.getAttribute('data-user-id');
        const btn = tbody.querySelector(`.save-single-user-role-btn[data-user-id="${userId}"]`);
        saveUserRole(userId, sel.value, btn);
      });
    });

    const countEl = document.getElementById('staffRoleTableCount');
    if (countEl) countEl.textContent = departmentUsers.length;
  }

  function renderRoleOptions(currentRole, roles) {
    let html = '';
    const keys = Object.keys(roles);

    keys.forEach((key) => {
      const r = roles[key] || {};
      const selected = key === currentRole ? 'selected' : '';
      const name = r.name || key;
      html += `<option value="${key}" ${selected}>${escapeHtml(name)}</option>`;
    });

    return html;
  }

  async function saveUserRole(userId, newRole, btn) {
    if (!userId || !newRole) return;

    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Saving...';
    }

    try {
      const url = getApiUrl('api/settings/roles/assign-user');
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json'
        },
        body: JSON.stringify({ userId, newRole })
      });

      const data = await res.json().catch(() => null);

      if (!res.ok || !data || !data.success) {
        const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to update role.`;
        throw new Error(errorMsg);
      }

      showToast(data.message || 'User security role updated!', 'success');

      // Update in local array
      const user = departmentUsers.find((u) => u.id === userId);
      if (user) user.role = newRole;
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.textContent = 'Save Role';
      }
    }
  }

  function filterStaffTable(query) {
    document.querySelectorAll('.staff-row').forEach((row) => {
      const name = row.getAttribute('data-user-name') || '';
      const dept = row.getAttribute('data-user-dept') || '';
      const empid = row.getAttribute('data-user-empid') || '';
      const match = !query || name.includes(query) || dept.includes(query) || empid.includes(query);
      row.classList.toggle('hidden', !match);
    });
  }

  /**
   * Create Role Modal & Form Management
   */
  function initCreateRoleModal() {
    const openBtn = document.getElementById('openCreateRoleModalBtn');
    const modal = document.getElementById('createRoleModal');
    const closeBtn = document.getElementById('closeCreateRoleModalBtn');
    const cancelBtn = document.getElementById('cancelCreateRoleBtn');
    const form = document.getElementById('createRoleForm');
    const nameInput = document.getElementById('newRoleName');
    const keyInput = document.getElementById('newRoleKey');
    const colorInput = document.getElementById('newRoleColor');
    const previewName = document.getElementById('previewRoleName');
    const rolePreviewPill = document.getElementById('rolePreviewPill');

    if (!modal) return;

    function openModal() {
      modal.style.display = 'flex';
      modal.classList.remove('hidden');
      if (form) form.reset();
      if (previewName) previewName.textContent = 'New Role';
      if (keyInput) delete keyInput.dataset.manuallyEdited;
      if (nameInput) setTimeout(() => nameInput.focus(), 50);
    }

    function closeModal() {
      modal.style.display = 'none';
      modal.classList.add('hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Auto-generate key slug from name
    if (nameInput) {
      nameInput.addEventListener('input', () => {
        const val = nameInput.value.trim();
        if (previewName) previewName.textContent = val || 'New Role';
        if (keyInput && !keyInput.dataset.manuallyEdited) {
          keyInput.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
        }
      });
    }

    if (keyInput) {
      keyInput.addEventListener('input', () => {
        keyInput.dataset.manuallyEdited = 'true';
      });
    }

    // Color swatches
    const swatchContainer = document.getElementById('roleColorSwatches');
    if (swatchContainer) {
      swatchContainer.querySelectorAll('.color-swatch-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          swatchContainer.querySelectorAll('.color-swatch-btn').forEach(b => {
            b.classList.remove('active', 'ring-2', 'ring-offset-2');
            b.innerHTML = '';
          });
          btn.classList.add('active', 'ring-2', 'ring-offset-2');
          btn.innerHTML = `<svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>`;
          const colorName = btn.getAttribute('data-color') || 'teal';
          const hex = btn.getAttribute('data-hex') || '#14b8a6';
          if (colorInput) colorInput.value = colorName;
          if (rolePreviewPill) rolePreviewPill.style.backgroundColor = hex;
        });
      });
    }

    // Starter Presets
    const presetContainer = document.getElementById('presetCardsContainer');
    const cloneFromInput = document.getElementById('newRoleCloneFrom');
    if (presetContainer) {
      presetContainer.querySelectorAll('.preset-card').forEach((card) => {
        card.addEventListener('click', () => {
          presetContainer.querySelectorAll('.preset-card').forEach(c => {
            c.classList.remove('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
            c.classList.add('border-black/10', 'dark:border-white/10');
          });
          card.classList.add('active', 'border-2', 'border-[var(--color-primary)]', 'bg-[var(--color-primary)]/10');
          card.classList.remove('border-black/10', 'dark:border-white/10');
          const preset = card.getAttribute('data-preset') || '';
          if (cloneFromInput) cloneFromInput.value = preset;
        });
      });
    }

    // Form submission
    if (form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('submitCreateRoleBtn');
        const name = (nameInput ? nameInput.value : '').trim();
        const key = (keyInput ? keyInput.value : '').trim();
        const color = (colorInput ? colorInput.value : 'teal').trim();
        const desc = (document.getElementById('newRoleDesc') ? document.getElementById('newRoleDesc').value : '').trim();
        const cloneFrom = (cloneFromInput ? cloneFromInput.value : 'faculty').trim();

        if (!name || !key) {
          showToast('Role Name and Slug Key are required.', 'error');
          return;
        }

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = `<span>Creating...</span>`;
        }

        try {
          const url = getApiUrl('api/settings/roles/create');
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json'
            },
            body: JSON.stringify({
              name: name,
              key: key,
              badgeColor: color,
              description: desc,
              cloneFrom: cloneFrom
            })
          });

          const data = await res.json().catch(() => null);

          if (!res.ok || !data || !data.success) {
            const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to create role.`;
            throw new Error(errorMsg);
          }

          showToast(data.message || 'Security role created successfully!', 'success');
          closeModal();

          // Add to local rolesConfig
          if (!rolesConfig.roles) rolesConfig.roles = {};
          rolesConfig.roles[data.roleKey] = data.role;

          // Re-render role pills and select the new role
          renderRoleSelectorPills();
          selectRole(data.roleKey);
        } catch (err) {
          showToast(err.message, 'error');
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>Create Role</span> <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>`;
          }
        }
      });
    }
  }

  /**
   * Delete Role Modal & Deletion Management
   */
  let pendingDeleteRoleKey = null;

  function openDeleteRoleModal(roleKey, roleName) {
    pendingDeleteRoleKey = roleKey;
    const modal = document.getElementById('deleteRoleModal');
    const targetName = document.getElementById('deleteRoleTargetName');
    const reassignSelect = document.getElementById('deleteRoleReassignSelect');

    if (targetName) targetName.textContent = roleName || roleKey;

    if (reassignSelect) {
      let optHtml = '';
      const roles = rolesConfig.roles || {};
      Object.keys(roles).forEach((k) => {
        if (k !== roleKey) {
          const r = roles[k] || {};
          const sel = (k === 'faculty') ? 'selected' : '';
          optHtml += `<option value="${k}" ${sel}>${escapeHtml(r.name || k)}</option>`;
        }
      });
      reassignSelect.innerHTML = optHtml;
    }

    if (modal) {
      modal.style.display = 'flex';
      modal.classList.remove('hidden');
    }
  }

  function closeDeleteRoleModal() {
    pendingDeleteRoleKey = null;
    const modal = document.getElementById('deleteRoleModal');
    if (modal) {
      modal.style.display = 'none';
      modal.classList.add('hidden');
    }
  }

  function initDeleteRoleModal() {
    const modal = document.getElementById('deleteRoleModal');
    const cancelBtn = document.getElementById('cancelDeleteRoleBtn');
    const confirmBtn = document.getElementById('confirmDeleteRoleBtn');

    if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteRoleModal);

    if (confirmBtn) {
      confirmBtn.addEventListener('click', async () => {
        if (!pendingDeleteRoleKey) return;

        const reassignSelect = document.getElementById('deleteRoleReassignSelect');
        const reassignTo = reassignSelect ? reassignSelect.value : 'faculty';

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<span>Deleting...</span>`;

        try {
          const url = getApiUrl('api/settings/roles/delete');
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json'
            },
            body: JSON.stringify({
              roleKey: pendingDeleteRoleKey,
              reassignTo: reassignTo
            })
          });

          const data = await res.json().catch(() => null);

          if (!res.ok || !data || !data.success) {
            const errorMsg = (data && data.error) ? data.error : `HTTP ${res.status}: Failed to delete role.`;
            throw new Error(errorMsg);
          }

          showToast(data.message || 'Role deleted successfully.', 'success');
          const deletedKey = pendingDeleteRoleKey;
          closeDeleteRoleModal();

          if (rolesConfig.roles && rolesConfig.roles[deletedKey]) {
            delete rolesConfig.roles[deletedKey];
          }

          activeRoleKey = 'super_admin';
          renderRoleSelectorPills();
          selectRole('super_admin');
        } catch (err) {
          showToast(err.message, 'error');
        } finally {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = `<span>Confirm & Delete</span>`;
        }
      });
    }
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

})();
