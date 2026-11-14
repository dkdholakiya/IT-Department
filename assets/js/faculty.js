/**
 * Faculty List Management Controller — CE & IT Department
 * Smart Management Dashboard Theme Integration
 *
 * Capabilities:
 * - Reactive Local Storage State (`portal_faculty_list`) seeded from `facultyData.js`
 * - Department Filter Tabs (All / IT / CE) with live dynamic count badges
 * - Status Filter (All / Active / Inactive)
 * - Live Instant Search with highlight
 * - Pagination (10 / 20 / 50 / All) with page switching
 * - Unified Single Action Button with floating menu (View, Edit, Delete, Toggle Active/Inactive)
 * - View Details Modal with full profile and drive link
 * - Add / Edit Faculty Modal with form validation
 * - Delete Confirmation Modal
 * - Instant Active / Inactive Status Toggle with toast notification
 * - Lazy-loaded SheetJS Excel Export
 * - Universal Toast Notifications
 */

(function () {
  'use strict';

  // --- STATE ---
  const STORAGE_KEY = 'portal_faculty_list_v3';
  let facultyList = [];
  let currentDept = 'all';
  let currentStatus = 'all';
  let searchQuery = '';
  let currentPage = 1;
  let pageSize = 20;
  let activeMenuEl = null;
  let activeMenuBtn = null;

  // Selected faculty member for edit/delete
  let targetFacultyId = null;

  // Helper to reliably get source faculty data from global scope or window
  function getRawFacultyData() {
    if (typeof facultyData !== 'undefined' && Array.isArray(facultyData) && facultyData.length > 0) {
      return facultyData;
    }
    if (typeof window !== 'undefined' && typeof window.facultyData !== 'undefined' && Array.isArray(window.facultyData) && window.facultyData.length > 0) {
      return window.facultyData;
    }
    return [];
  }

  // Department Matching Helper
  function isDeptMatch(memberDept, filterDept) {
    if (filterDept === 'all') return true;
    if (!memberDept) return true;
    const d = memberDept.toLowerCase().trim();
    if (d === 'both' || d.includes('both') || d.includes('it & ce') || d.includes('ce & it') || d.includes('it/ce') || d.includes('ce/it')) {
      return true;
    }
    if (filterDept === 'Information Technology') {
      return d === 'information technology' || d.includes('information technology') || d === 'it';
    }
    if (filterDept === 'Computer Engineering') {
      return d === 'computer engineering' || d.includes('computer engineering') || d === 'ce';
    }
    return d === filterDept.toLowerCase();
  }

  // Avatar Gradient Selector
  function getAvatarStyle(member) {
    const dept = (member.department || '').toLowerCase();
    if (member.id === 'dc' || member.id === 'sw' || member.id === 'eu') {
      return 'av-dept-hod';
    }
    if (dept.includes('computer') || dept === 'ce') {
      return 'av-dept-ce';
    }
    if (dept.includes('both') || dept.includes('it & ce') || dept.includes('ce & it')) {
      return 'av-dept-both';
    }
    return 'av-dept-it';
  }

  // Load Initial Data
  function loadFacultyData() {
    // 1. Authoritative Server Data (from PHP / config/users.json)
    if (typeof window !== 'undefined' && window.serverFacultyList && Array.isArray(window.serverFacultyList) && window.serverFacultyList.length > 0) {
      facultyList = window.serverFacultyList.map(m => ({
        ...m,
        status: m.status || 'active',
        setting: m.setting || 'FF-38'
      }));
      saveFacultyData();
      return;
    }

    const raw = getRawFacultyData();

    try {
      // Clear legacy empty storage keys if present
      localStorage.removeItem('portal_faculty_list_v2');

      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        const parsed = JSON.parse(saved);
        if (Array.isArray(parsed) && parsed.length > 0) {
          facultyList = parsed;
          return;
        }
      }
    } catch (e) {
      console.warn('Failed to parse saved faculty list, loading defaults:', e);
    }

    // Initialize from raw facultyData
    if (raw && raw.length > 0) {
      facultyList = raw.map(m => ({
        ...m,
        status: m.status || 'active',
        setting: m.setting || 'FF-38'
      }));
    } else {
      facultyList = [];
    }
    saveFacultyData();
  }

  // Save to Local Storage
  function saveFacultyData() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(facultyList));
    } catch (e) {
      console.error('Storage quota exceeded or storage blocked:', e);
    }
  }

  // Toast Notification (Delegates to Universal Top-Right Bootstrap 5 Toast Engine)
  function showToast(message, isAlert = false) {
    if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      return window.PortalToast.show(message, isAlert ? 'error' : 'success');
    }
    if (typeof window.showPortalToast === 'function') {
      return window.showPortalToast(message, isAlert ? 'error' : 'success');
    }
  }

  // Calculate Counts
  function updateCounts() {
    const totalCount = facultyList.length;
    const itCount = facultyList.filter(m => isDeptMatch(m.department, 'Information Technology')).length;
    const ceCount = facultyList.filter(m => isDeptMatch(m.department, 'Computer Engineering')).length;

    const allBtn = document.getElementById('deptFilterAll');
    const itBtn = document.getElementById('deptFilterIt');
    const ceBtn = document.getElementById('deptFilterCe');
    const totalBadge = document.getElementById('totalFacultyBadge');

    if (allBtn) allBtn.innerHTML = `<span class="hidden md:inline">All Departments</span><span class="md:hidden">All</span> <span class="ml-1 px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/15 text-[10px] font-bold">${totalCount}</span>`;
    if (itBtn) itBtn.innerHTML = `<span class="hidden md:inline">Information Technology</span><span class="md:hidden">IT</span> <span class="ml-1 px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/15 text-[10px] font-bold">${itCount}</span>`;
    if (ceBtn) ceBtn.innerHTML = `<span class="hidden md:inline">Computer Engineering</span><span class="md:hidden">CE</span> <span class="ml-1 px-1.5 py-0.2 rounded-full bg-black/10 dark:bg-white/15 text-[10px] font-bold">${ceCount}</span>`;
    if (totalBadge) totalBadge.textContent = `${totalCount} Faculty Members`;
  }

  // Get Filtered Data
  function getFilteredList() {
    const q = searchQuery.toLowerCase().trim();

    // Fixed Top HODs
    const fixedHodIds = ['dc', 'sw', 'eu'];

    return facultyList.filter(m => {
      // Dept filter
      if (!isDeptMatch(m.department, currentDept)) return false;

      // Status filter
      if (currentStatus !== 'all') {
        const memStatus = m.status || 'active';
        if (memStatus !== currentStatus) return false;
      }

      // Search Query
      if (q) {
        const nameMatch = (m.name || '').toLowerCase().includes(q);
        const initMatch = (m.initials || '').toLowerCase().includes(q);
        const empMatch = (m.empId || '').toLowerCase().includes(q);
        const desgMatch = (m.designation || '').toLowerCase().includes(q);
        const emailMatch = (m.email || '').toLowerCase().includes(q);
        const roomMatch = (m.setting || '').toLowerCase().includes(q);
        const phoneMatch = (m.phone || '').toLowerCase().includes(q);
        if (!nameMatch && !initMatch && !empMatch && !desgMatch && !emailMatch && !roomMatch && !phoneMatch) {
          return false;
        }
      }

      return true;
    }).sort((a, b) => {
      // Keep HODs at the top if search is empty
      if (!q) {
        const aHod = fixedHodIds.indexOf(a.id);
        const bHod = fixedHodIds.indexOf(b.id);
        if (aHod !== -1 && bHod !== -1) return aHod - bHod;
        if (aHod !== -1) return -1;
        if (bHod !== -1) return 1;
      }
      return 0;
    });
  }

  // Render Table
  function renderTable() {
    closeActionMenu();
    updateCounts();

    const tbody = document.getElementById('facultyTableBody');
    const tableInfo = document.getElementById('tableInfoText');
    const paginationContainer = document.getElementById('paginationControls');
    if (!tbody) return;

    const filtered = getFilteredList();
    const totalRecords = filtered.length;

    // Handle Page Bounds
    const maxPage = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalRecords / pageSize));
    if (currentPage > maxPage) currentPage = maxPage;

    // Slice for pagination
    let paginatedData = filtered;
    let startIdx = 0;
    let endIdx = totalRecords;

    if (pageSize !== 'all') {
      startIdx = (currentPage - 1) * pageSize;
      endIdx = Math.min(startIdx + pageSize, totalRecords);
      paginatedData = filtered.slice(startIdx, endIdx);
    }

    // Update Summary Text
    if (tableInfo) {
      if (totalRecords === 0) {
        tableInfo.textContent = 'Showing 0 records';
      } else {
        tableInfo.textContent = `Showing ${startIdx + 1}–${endIdx} of ${totalRecords} faculty members`;
      }
    }

    // Render Empty State
    if (totalRecords === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="8" class="py-12 text-center text-sm font-medium text-[var(--text-muted)]">
            <div class="flex flex-col items-center justify-center gap-2">
              <svg class="w-8 h-8 text-[var(--text-muted)] opacity-60" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <span>No faculty members match the selected filters.</span>
              <button type="button" id="clearFiltersBtn" class="mt-1 px-3 py-1 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white hover:opacity-90">
                Clear Filters
              </button>
            </div>
          </td>
        </tr>
      `;
      const clrBtn = document.getElementById('clearFiltersBtn');
      if (clrBtn) {
        clrBtn.addEventListener('click', () => {
          currentDept = 'all';
          currentStatus = 'all';
          searchQuery = '';
          const searchInp = document.getElementById('facultySearchInput');
          if (searchInp) searchInp.value = '';
          updateActiveFilterPills();
          renderTable();
        });
      }
      renderPagination(0, 1);
      return;
    }

    // Build Table Rows
    let rowsHtml = '';
    paginatedData.forEach((member, i) => {
      const globalIndex = startIdx + i + 1;
      const avatarStyle = getAvatarStyle(member);
      const isInactive = member.status === 'inactive';
      const cleanPhone = (member.phone || '').replace(/\s+/g, '');

      // Department Badge Class
      let deptClass = 'badge-it';
      let deptLabel = member.department || 'Information Technology';
      const dLower = deptLabel.toLowerCase();
      if (dLower.includes('computer') || dLower === 'ce') {
        deptClass = 'badge-ce';
        deptLabel = 'Computer Eng.';
      } else if (dLower.includes('both') || dLower.includes('it & ce') || dLower.includes('ce & it')) {
        deptClass = 'badge-both';
        deptLabel = 'IT & CE';
      } else {
        deptLabel = 'Info. Tech.';
      }

      rowsHtml += `
        <tr class="${isInactive ? 'row-inactive' : ''}" data-faculty-id="${member.id}">
          <!-- Sr. No (hidden on mobile <640px) -->
          <td class="font-mono text-xs text-[var(--text-muted)] text-center w-10 sm:w-12 select-none hidden sm:table-cell">${globalIndex}</td>

          <!-- Faculty Name & Avatar -->
          <td>
            <div class="flex items-center gap-2.5 sm:gap-3">
              <div class="fac-avatar ${avatarStyle}">
                ${member.initials || 'FC'}
              </div>
              <div class="min-w-0 flex-1">
                <div class="font-bold text-xs sm:text-sm text-[var(--text-primary)] leading-tight truncate">
                  <span class="truncate">${member.name}</span>
                </div>
                <div class="flex items-center gap-1.5 mt-0.5 text-[11px] sm:text-xs text-[var(--text-muted)] flex-wrap">
                  <span class="font-mono font-semibold text-[10px] sm:text-[11px] px-1.5 py-0.2 rounded bg-black/5 dark:bg-white/10 text-[var(--text-secondary)]">
                    ${member.initials || 'FC'}
                  </span>
                  ${member.username ? `<span class="font-mono text-[10.5px] text-[var(--text-muted)]">@${member.username}</span>` : ''}
                  <span class="md:hidden font-medium text-[var(--text-primary)] truncate">• ${member.designation || 'Faculty'}</span>
                  ${member.setting ? `<span class="truncate hidden xs:inline">📍 ${member.setting}</span>` : ''}
                </div>
                <!-- Mobile Inline Quick Contacts (visible on <640px when Contact column is hidden) -->
                <div class="sm:hidden flex items-center gap-2.5 mt-1">
                  ${member.email ? `
                    <a href="mailto:${member.email}" class="text-[var(--color-primary)] hover:underline flex items-center gap-1 text-[10.5px] font-medium" title="${member.email}">
                      <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                      </svg>
                      <span>Email</span>
                    </a>
                  ` : ''}
                  ${member.phone && member.phone !== '---' ? `
                    <a href="tel:${cleanPhone}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-[10.5px] flex items-center gap-1" title="Call +91 ${member.phone}">
                      <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                      </svg>
                      <span>Call</span>
                    </a>
                  ` : ''}
                </div>
              </div>
            </div>
          </td>

          <!-- Department -->
          <td>
            <span class="fac-dept-badge ${deptClass}">
              <span class="w-1.5 h-1.5 rounded-full bg-current opacity-80"></span>
              ${deptLabel}
            </span>
          </td>

          <!-- Designation (hidden on small screens, shown in name column) -->
          <td class="hidden md:table-cell">
            <span class="text-xs font-semibold text-[var(--text-primary)] block">
              ${member.designation || 'Faculty Member'}
            </span>
          </td>

          <!-- Employee ID (hidden on tablet/mobile, shown in modal/card) -->
          <td class="hidden lg:table-cell">
            <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded-md bg-black/5 dark:bg-white/10 text-[var(--text-secondary)]">
              ${member.empId || '---'}
            </span>
          </td>

          <!-- Contact Details (hidden on mobile <640px, shown on sm+) -->
          <td class="hidden sm:table-cell">
            <div class="flex flex-col gap-0.5 text-xs">
              ${member.email ? `
                <a href="mailto:${member.email}" class="text-[var(--color-primary)] hover:underline truncate max-w-[190px] font-medium flex items-center gap-1" title="${member.email}">
                  <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                  </svg>
                  ${member.email}
                </a>
              ` : '<span class="text-[var(--text-muted)]">---</span>'}
              ${member.phone && member.phone !== '---' ? `
                <a href="tel:${cleanPhone}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-[11px] flex items-center gap-1" title="Call +91 ${member.phone}">
                  <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                  +91 ${member.phone}
                </a>
              ` : ''}
            </div>
          </td>

          <!-- Status Pill (1-Click Interactive Toggle) -->
          <td>
            <button
              type="button"
              class="fac-status-pill ${isInactive ? 'status-inactive' : 'status-active'} cursor-pointer hover:opacity-90 active:scale-95 transition"
              data-id="${member.id}"
              data-action="toggle-status"
              title="Click to ${isInactive ? 'Activate' : 'Deactivate'} account"
            >
              <span class="status-dot ${isInactive ? 'status-dot-inactive' : 'status-dot-active'}"></span>
              ${isInactive ? 'Inactive' : 'Active'}
            </button>
          </td>

          <!-- Clean Plus (+) Action Button -->
          <td class="action-cell">
            <div class="flex items-center justify-end">
              <button
                type="button"
                class="action-plus-btn fac-action-icon-btn"
                data-id="${member.id}"
                aria-label="Actions for ${member.name}"
                title="Actions"
              >
                <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
              </button>
            </div>
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = rowsHtml;

    // Attach Event Listeners to Plus Action Buttons & Status Pills
    const plusBtns = tbody.querySelectorAll('.action-plus-btn');
    plusBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const id = btn.getAttribute('data-id');
        openActionMenu(btn, id);
      });
    });

    const statusBtns = tbody.querySelectorAll('[data-action="toggle-status"]');
    statusBtns.forEach(sbtn => {
      sbtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const id = sbtn.getAttribute('data-id');
        toggleFacultyStatus(id);
      });
    });

    renderPagination(totalRecords, maxPage);
  }

  // Render Pagination Controls
  function renderPagination(totalRecords, maxPage) {
    const container = document.getElementById('paginationControls');
    if (!container) return;

    if (totalRecords === 0 || pageSize === 'all' || maxPage <= 1) {
      container.innerHTML = '';
      return;
    }

    let pagesHtml = '';

    // Prev Button
    pagesHtml += `
      <button type="button" class="fac-page-btn" id="prevPageBtn" ${currentPage === 1 ? 'disabled' : ''} aria-label="Previous Page" title="Previous Page">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/>
        </svg>
      </button>
    `;

    // Responsive window of pages around current (1 on mobile, 2 on tablet/desktop)
    const windowSize = (typeof window !== 'undefined' && window.innerWidth < 640) ? 1 : 2;
    const startPage = Math.max(1, currentPage - windowSize);
    const endPage = Math.min(maxPage, currentPage + windowSize);

    if (startPage > 1) {
      pagesHtml += `<button type="button" class="fac-page-btn" data-page="1">1</button>`;
      if (startPage > 2) pagesHtml += `<span class="px-0.5 text-[var(--text-muted)] text-[11px]">…</span>`;
    }

    for (let p = startPage; p <= endPage; p++) {
      pagesHtml += `
        <button type="button" class="fac-page-btn ${p === currentPage ? 'active' : ''}" data-page="${p}">
          ${p}
        </button>
      `;
    }

    if (endPage < maxPage) {
      if (endPage < maxPage - 1) pagesHtml += `<span class="px-1 text-[var(--text-muted)]">…</span>`;
      pagesHtml += `<button type="button" class="fac-page-btn" data-page="${maxPage}">${maxPage}</button>`;
    }

    // Next Button
    pagesHtml += `
      <button type="button" class="fac-page-btn" id="nextPageBtn" ${currentPage === maxPage ? 'disabled' : ''} aria-label="Next Page" title="Next Page">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
        </svg>
      </button>
    `;

    container.innerHTML = pagesHtml;

    // Events
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
          currentPage--;
          renderTable();
        }
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        if (currentPage < maxPage) {
          currentPage++;
          renderTable();
        }
      });
    }

    const pageBtns = container.querySelectorAll('.fac-page-btn[data-page]');
    pageBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        currentPage = parseInt(btn.getAttribute('data-page'), 10);
        renderTable();
      });
    });
  }

  // --- FLOATING HORIZONTAL ACTION ICON MENU ---
  function closeActionMenu() {
    if (activeMenuEl && activeMenuEl.parentNode) {
      activeMenuEl.parentNode.removeChild(activeMenuEl);
    }
    activeMenuEl = null;
    if (activeMenuBtn) {
      activeMenuBtn.classList.remove('is-active');
      activeMenuBtn = null;
    }
  }

  function openActionMenu(btn, id) {
    if (activeMenuBtn === btn) {
      closeActionMenu();
      return;
    }

    closeActionMenu();

    function hasFacPerm(perm) {
      if (window.isSuperAdmin) return true;
      if (Array.isArray(window.userPermissions)) {
        if (window.userPermissions.includes('*')) return true;
        return window.userPermissions.includes(perm);
      }
      return false;
    }

    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const isInactive = member.status === 'inactive';
    activeMenuBtn = btn;
    btn.classList.add('is-active');

    const menu = document.createElement('div');
    menu.className = 'action-icon-menu';
    menu.id = 'floatingActionMenu';

    let menuButtonsHtml = '';

    // 1. Key (View Credentials) - strictly permission controlled
    if (hasFacPerm('faculty.view_credentials')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-cred-key" data-action="show-credentials" data-id="${member.id}" title="View Sign-In Credentials">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
          </svg>
        </button>
      `;
    }

    // 2. Mail (Email Credentials) - strictly permission controlled
    if (hasFacPerm('faculty.send_credentials')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-cred-mail" data-action="email-credentials" data-id="${member.id}" title="Send Credentials via Email">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
          </svg>
        </button>
      `;
    }

    // 3. Eye (View Details) - profile view
    if (hasFacPerm('faculty.profile') || hasFacPerm('faculty.list')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-action-view" data-action="view" data-id="${member.id}" title="View Faculty Profile">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      `;
    }

    // 4. Edit (Edit Faculty) - edit permission
    if (hasFacPerm('faculty.edit')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-action-edit" data-action="edit" data-id="${member.id}" title="Edit Faculty Profile">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
            <path d="m15 5 4 4"/>
          </svg>
        </button>
      `;
    }

    // 5. Toggle Status (Active / Inactive) - toggle permission
    if (hasFacPerm('faculty.toggle_status')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-action-status ${isInactive ? 'is-inactive' : ''}" data-action="toggle-status" data-id="${member.id}" title="${isInactive ? 'Mark as Active' : 'Mark as Inactive'}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
          </svg>
        </button>
      `;
    }

    // 6. Delete Faculty - strictly blocked for HOD & Sub Admin
    if (hasFacPerm('faculty.delete')) {
      menuButtonsHtml += `
        <button type="button" class="fac-action-icon-btn btn-action-delete" data-action="delete" data-id="${member.id}" title="Delete Faculty Profile">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
          </svg>
        </button>
      `;
    }

    // 7. Close Menu (X)
    menuButtonsHtml += `
      <button type="button" class="fac-action-icon-btn btn-action-close" data-action="close" data-id="${member.id}" title="Close Actions">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    `;

    menu.innerHTML = menuButtonsHtml;

    document.body.appendChild(menu);
    activeMenuEl = menu;

    // Position horizontally next to the button
    const rect = btn.getBoundingClientRect();
    const menuWidth = 275;
    const menuHeight = 42;

    let left = rect.right - menuWidth;
    let top = rect.top + (rect.height - menuHeight) / 2;

    if (left < 10) left = 10;
    if (left + menuWidth > window.innerWidth - 10) {
      left = window.innerWidth - menuWidth - 10;
    }
    if (top < 10) top = 10;
    if (top + menuHeight > window.innerHeight - 10) {
      top = window.innerHeight - menuHeight - 10;
    }

    menu.style.left = `${left}px`;
    menu.style.top = `${top}px`;

    // Action clicks
    menu.querySelectorAll('[data-action]').forEach(item => {
      item.addEventListener('click', (e) => {
        e.stopPropagation();
        const action = item.getAttribute('data-action');
        const mId = item.getAttribute('data-id');
        closeActionMenu();

        if (action === 'close') {
          return;
        } else if (action === 'view') {
          showViewModal(mId);
        } else if (action === 'edit') {
          showEditModal(mId);
        } else if (action === 'show-credentials') {
          showCredentialsModal(mId);
        } else if (action === 'email-credentials') {
          sendFacultyCredentials(mId);
        } else if (action === 'toggle-status') {
          toggleFacultyStatus(mId);
        } else if (action === 'delete') {
          showDeleteModal(mId);
        }
      });
    });
  }

  // --- ACTIONS IMPLEMENTATION ---

  // 1. Toggle Active / Inactive (Persisted to backend users.json)
  async function toggleFacultyStatus(id) {
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const originalStatus = member.status || 'active';
    const targetStatus = (originalStatus === 'inactive') ? 'active' : 'inactive';

    // Optimistic UI feedback
    member.status = targetStatus;
    renderTable();

    try {
      const baseUrl = (typeof window !== 'undefined' && window.portalBaseUrl) ? window.portalBaseUrl : '';
      const endpoint = baseUrl.replace(/\/+$/, '') + '/api/faculty/toggle-status';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });

      const data = await response.json();
      if (data && data.success) {
        member.status = data.status || targetStatus;
        saveFacultyData();
        renderTable();

        const statusLabel = (member.status === 'active') ? 'Active' : 'Inactive';
        showToast(`✓ ${member.name} is now marked as ${statusLabel}`);

        // Refresh view modal if currently open for this member
        const viewModal = document.getElementById('viewFacultyModal');
        if (viewModal && !viewModal.classList.contains('hidden') && targetFacultyId === id) {
          showViewModal(id);
        }
      } else {
        throw new Error((data && data.error) ? data.error : 'Failed to update status on server.');
      }
    } catch (err) {
      // Revert optimistic change on failure
      member.status = originalStatus;
      renderTable();
      showToast('Status update failed: ' + err.message, true);
    }
  }

  // Helper: Copy Text to Clipboard with Visual Toast Feedback
  function copyTextToClipboard(text, successMsg = 'Copied to clipboard!') {
    if (!text || text === '---') return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(() => {
        showToast(successMsg);
      }).catch(() => {
        fallbackCopyText(text, successMsg);
      });
    } else {
      fallbackCopyText(text, successMsg);
    }
  }

  function fallbackCopyText(text, successMsg) {
    const t = document.createElement('textarea');
    t.value = text;
    t.style.position = 'fixed';
    t.style.opacity = '0';
    document.body.appendChild(t);
    t.select();
    try {
      document.execCommand('copy');
      showToast(successMsg);
    } catch (e) {
      showToast('Could not copy automatically.', true);
    }
    document.body.removeChild(t);
  }

  // 2. View Modal (Display all details in a balanced 2-column layout - No desktop scroll)
  function showViewModal(id) {
    targetFacultyId = id;
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const modal = document.getElementById('viewFacultyModal');
    const body = document.getElementById('viewModalBody');
    if (!modal || !body) return;

    const avatarStyle = getAvatarStyle(member);
    const isInactive = member.status === 'inactive';
    const isSuperAdmin = member.role === 'super_admin';
    const cleanPhone = (member.phone || '').replace(/\s+/g, '');
    const passwordVal = member.password || cleanPhone || '12345678';
    const usernameVal = member.username || (member.email ? member.email.split('@')[0] : member.id);

    body.innerHTML = `
      <!-- Header Profile Banner -->
      <div class="flex flex-col sm:flex-row items-center gap-3.5 pb-3 border-b border-black/10 dark:border-white/10">
        <div class="fac-avatar ${avatarStyle} w-14 h-14 sm:w-16 sm:h-16 text-xl rounded-2xl shadow-md shrink-0">
          ${member.initials || 'FC'}
        </div>
        <div class="text-center sm:text-left flex-1 min-w-0">
          <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
            <h4 class="text-base sm:text-lg font-bold text-[var(--text-primary)] font-serif leading-tight truncate">${member.name}</h4>
            <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded-lg bg-black/5 dark:bg-white/10 text-[var(--text-secondary)]">
              ${member.empId || '#---'}
            </span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${isSuperAdmin ? 'bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-purple-500/20' : 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20'}">
              ${isSuperAdmin ? 'Super Administrator' : 'Staff Faculty'}
            </span>
          </div>
          <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-1 text-xs">
            <span class="font-semibold text-[var(--color-primary)]">${member.designation || 'Faculty Member'}</span>
            <span class="text-[var(--text-muted)]">•</span>
            <span class="fac-dept-badge badge-it">${member.department || 'Information Technology'}</span>
            <span class="text-[var(--text-muted)]">•</span>
            <span class="fac-status-pill ${isInactive ? 'status-inactive' : 'status-active'}">
              <span class="status-dot ${isInactive ? 'status-dot-inactive' : 'status-dot-active'}"></span>
              ${isInactive ? 'Inactive' : 'Active'}
            </span>
          </div>
        </div>
      </div>

      <!-- 2-Column Responsive Body -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 py-3 text-xs">
        <!-- Left: Contact & Credentials Card -->
        <div class="p-3 rounded-2xl bg-black/[0.02] dark:bg-white/[0.03] border border-black/5 dark:border-white/10 flex flex-col gap-2.5">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--color-primary)] flex items-center gap-1.5 pb-1 border-b border-black/5 dark:border-white/5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            <span>Contact & Portal Sign-In</span>
          </div>

          <!-- Email -->
          <div class="flex items-center justify-between gap-1">
            <div class="min-w-0">
              <span class="text-[10px] font-bold text-[var(--text-muted)] block">Email Address</span>
              ${member.email ? `
                <a href="mailto:${member.email}" class="text-[var(--color-primary)] hover:underline font-medium text-xs truncate block" title="${member.email}">
                  ${member.email}
                </a>
              ` : '<span class="text-[var(--text-muted)] text-xs">No email provided</span>'}
            </div>
            ${member.email ? `
              <button type="button" class="copy-field-btn p-1.5 rounded-lg glass-pill text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" data-copy="${member.email}" title="Copy Email">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
              </button>
            ` : ''}
          </div>

          <!-- Phone -->
          <div class="flex items-center justify-between gap-1">
            <div class="min-w-0">
              <span class="text-[10px] font-bold text-[var(--text-muted)] block">Mobile Number</span>
              ${member.phone && member.phone !== '---' ? `
                <a href="tel:${cleanPhone}" class="text-[var(--text-primary)] hover:underline font-mono text-xs font-semibold block">
                  +91 ${member.phone}
                </a>
              ` : '<span class="text-[var(--text-muted)] text-xs">---</span>'}
            </div>
            ${member.phone && member.phone !== '---' ? `
              <button type="button" class="copy-field-btn p-1.5 rounded-lg glass-pill text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" data-copy="${member.phone}" title="Copy Phone">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
              </button>
            ` : ''}
          </div>

          <!-- Username -->
          <div class="flex items-center justify-between gap-1">
            <div class="min-w-0">
              <span class="text-[10px] font-bold text-[var(--text-muted)] block">Portal Username</span>
              <span class="font-mono text-xs font-bold text-[var(--text-primary)] block">@${usernameVal}</span>
            </div>
            <button type="button" class="copy-field-btn p-1.5 rounded-lg glass-pill text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" data-copy="${usernameVal}" title="Copy Username">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
            </button>
          </div>

          <!-- Password -->
          <div class="flex items-center justify-between gap-1">
            <div class="min-w-0 flex-1">
              <span class="text-[10px] font-bold text-[var(--text-muted)] block">Login Password</span>
              <span id="viewPwdText" class="font-mono text-xs font-bold text-[var(--text-primary)] tracking-widest block">••••••••</span>
            </div>
            <div class="flex items-center gap-1">
              <button type="button" id="viewPwdToggleBtn" class="px-2 py-1 rounded-lg text-[10px] font-bold glass-pill text-[var(--color-primary)] hover:bg-black/5 dark:hover:bg-white/10" title="Show or hide password">
                Show
              </button>
              <button type="button" class="copy-field-btn p-1.5 rounded-lg glass-pill text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" data-copy="${passwordVal}" title="Copy Password">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Academic & Campus Allocation Card -->
        <div class="p-3 rounded-2xl bg-black/[0.02] dark:bg-white/[0.03] border border-black/5 dark:border-white/10 flex flex-col gap-2.5">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--color-primary)] flex items-center gap-1.5 pb-1 border-b border-black/5 dark:border-white/5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557c0 1.25.75 2 2 2h6.5c1.25 0 2-.75 2-2v-3.675" />
            </svg>
            <span>Campus & Academic Allocation</span>
          </div>

          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Employee ID</span>
            <span class="font-mono text-xs font-bold text-[var(--text-primary)] block">${member.empId || '---'}</span>
          </div>

          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Cabin / Sitting Room</span>
            <span class="text-xs font-bold text-[var(--text-primary)] block">📍 ${member.setting || 'FF-37'}</span>
          </div>

          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Department Division</span>
            <span class="text-xs font-semibold text-[var(--text-primary)] block">${member.department || 'Information Technology'}</span>
          </div>

          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Semester / Master Folder</span>
            <span class="text-xs font-medium text-[var(--text-primary)] block">${member.semClass || '---'}</span>
          </div>
        </div>
      </div>

      <!-- Master Drive Repository (if assigned) -->
      ${member.driveLink ? `
        <div class="mb-3 p-2.5 rounded-2xl bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/20 flex items-center justify-between gap-2 text-xs">
          <div class="flex items-center gap-2 min-w-0">
            <svg class="w-4 h-4 text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
            </svg>
            <span class="font-bold text-[var(--text-primary)] truncate">Master Google Drive Connected</span>
          </div>
          <a href="${member.driveLink}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl bg-[var(--color-primary)] text-white font-bold text-[11px] hover:opacity-90 transition shrink-0 flex items-center gap-1">
            <span>Open Drive</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </a>
        </div>
      ` : ''}

      <!-- Bottom Executive Action Toolbar -->
      <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between flex-wrap gap-2 text-xs">
        <!-- Left: Quick Credential Utilities -->
        <div class="flex items-center gap-2">
          <button
            type="button"
            id="viewModalCredsBtn"
            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 btn-cred-key"
            title="View Sign-In Credentials"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
            <span>Credentials</span>
          </button>

          <button
            type="button"
            id="viewModalMailCredsBtn"
            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 btn-cred-mail"
            title="Send Credentials via Email"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            <span>Send Email</span>
          </button>
        </div>

        <!-- Right: Status Toggle + Edit Profile -->
        <div class="flex items-center gap-2">
          <button
            type="button"
            id="viewModalToggleStatusBtn"
            class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 ${isInactive ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' : 'bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-400'}"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            <span>${isInactive ? 'Activate' : 'Deactivate'}</span>
          </button>

          <button
            type="button"
            id="viewModalEditBtn"
            class="px-4 py-2 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 shadow-sm transition flex items-center gap-1.5"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
              <path d="m15 5 4 4"/>
            </svg>
            <span>Edit Profile</span>
          </button>
        </div>
      </div>
    `;

    // Event: Toggle Password
    const pwdBtn = document.getElementById('viewPwdToggleBtn');
    const pwdText = document.getElementById('viewPwdText');
    if (pwdBtn && pwdText) {
      let revealed = false;
      pwdBtn.addEventListener('click', () => {
        revealed = !revealed;
        pwdText.textContent = revealed ? passwordVal : '••••••••';
        pwdBtn.textContent = revealed ? 'Hide' : 'Show';
      });
    }

    // Event: Copy buttons
    body.querySelectorAll('.copy-field-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const text = btn.getAttribute('data-copy');
        copyTextToClipboard(text, 'Copied to clipboard!');
      });
    });

    // Event: View Credentials Modal Trigger
    const credsBtn = document.getElementById('viewModalCredsBtn');
    if (credsBtn) {
      credsBtn.addEventListener('click', () => {
        showCredentialsModal(id);
      });
    }

    // Event: Send Credentials Email Trigger
    const mailCredsBtn = document.getElementById('viewModalMailCredsBtn');
    if (mailCredsBtn) {
      mailCredsBtn.addEventListener('click', () => {
        sendFacultyCredentials(id, mailCredsBtn);
      });
    }

    // Event: Toggle Status
    const toggleBtn = document.getElementById('viewModalToggleStatusBtn');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        toggleFacultyStatus(id);
      });
    }

    // Event: Edit Profile
    const editBtn = document.getElementById('viewModalEditBtn');
    if (editBtn) {
      editBtn.addEventListener('click', () => {
        closeAllModals();
        showEditModal(id);
      });
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // 2b. Show Credentials Modal
  function showCredentialsModal(id) {
    targetFacultyId = id;
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const modal = document.getElementById('credentialsModal');
    const body = document.getElementById('credentialsModalBody');
    if (!modal || !body) return;

    const avatarStyle = getAvatarStyle(member);
    const cleanPhone = (member.phone || '').replace(/\s+/g, '');
    const passwordVal = member.password || cleanPhone || '12345678';
    const usernameVal = member.username || (member.email ? member.email.split('@')[0] : member.id);
    const isInactive = member.status === 'inactive';

    body.innerHTML = `
      <!-- Member Overview -->
      <div class="flex items-center gap-3 pb-3 mb-3 border-b border-black/10 dark:border-white/10">
        <div class="fac-avatar ${avatarStyle} w-11 h-11 text-base rounded-2xl shadow-sm shrink-0">
          ${member.initials || 'FC'}
        </div>
        <div class="min-w-0 flex-1">
          <h4 class="text-sm font-bold text-[var(--text-primary)] truncate">${member.name}</h4>
          <div class="flex items-center gap-1.5 text-xs text-[var(--text-muted)] mt-0.5">
            <span>${member.designation || 'Faculty Member'}</span>
            <span>•</span>
            <span class="fac-status-pill ${isInactive ? 'status-inactive' : 'status-active'} text-[10px] py-0 px-2">
              <span class="status-dot ${isInactive ? 'status-dot-inactive' : 'status-dot-active'}"></span>
              ${isInactive ? 'Inactive' : 'Active'}
            </span>
          </div>
        </div>
      </div>

      <!-- Confidential Credentials Cards -->
      <div class="space-y-2.5 text-xs">
        <!-- Username Card -->
        <div class="p-3 rounded-2xl bg-black/[0.03] dark:bg-white/[0.05] border border-black/5 dark:border-white/10 flex items-center justify-between gap-2">
          <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Portal Sign-In Username</span>
            <span class="font-mono text-sm font-bold text-[var(--text-primary)] mt-0.5 block">${usernameVal}</span>
          </div>
          <button
            type="button"
            class="copy-cred-btn px-2.5 py-1.5 rounded-xl glass-pill font-bold text-xs text-[var(--color-primary)] hover:bg-black/5 dark:hover:bg-white/10 flex items-center gap-1.5 transition"
            data-copy="${usernameVal}"
            title="Copy Username"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
            <span>Copy</span>
          </button>
        </div>

        <!-- Password Card -->
        <div class="p-3 rounded-2xl bg-black/[0.03] dark:bg-white/[0.05] border border-black/5 dark:border-white/10 flex items-center justify-between gap-2">
          <div class="min-w-0 flex-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Sign-In Password</span>
            <span id="credPasswordText" class="font-mono text-sm font-bold text-[var(--text-primary)] tracking-widest mt-0.5 block">••••••••</span>
          </div>
          <div class="flex items-center gap-1.5 shrink-0">
            <button
              type="button"
              id="credPwdToggleBtn"
              class="px-2.5 py-1.5 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] transition"
              title="Reveal or hide password"
            >
              Show
            </button>
            <button
              type="button"
              class="copy-cred-btn px-2.5 py-1.5 rounded-xl glass-pill font-bold text-xs text-[var(--color-primary)] hover:bg-black/5 dark:hover:bg-white/10 flex items-center gap-1.5 transition"
              data-copy="${passwordVal}"
              title="Copy Password"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
              <span>Copy</span>
            </button>
          </div>
        </div>

        <!-- Email & Phone Info Card -->
        <div class="p-3 rounded-2xl bg-black/[0.02] dark:bg-white/[0.03] border border-black/5 dark:border-white/10 grid grid-cols-2 gap-2 text-xs">
          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Registered Email</span>
            <span class="font-medium text-[var(--text-primary)] truncate block">${member.email || 'None'}</span>
          </div>
          <div>
            <span class="text-[10px] font-bold text-[var(--text-muted)] block">Mobile Number</span>
            <span class="font-mono font-medium text-[var(--text-primary)] block">${member.phone ? '+91 ' + member.phone : '---'}</span>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="mt-4 pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between gap-2 text-xs">
        <button
          type="button"
          class="close-modal-trigger px-4 py-2 rounded-xl glass-pill font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] transition"
        >
          Close
        </button>

        <button
          type="button"
          id="credModalSendBtn"
          class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center gap-1.5 shadow-md active:scale-95 transition"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
          </svg>
          <span>Send Credentials to Email</span>
        </button>
      </div>
    `;

    // Event: Toggle password
    const pwdBtn = document.getElementById('credPwdToggleBtn');
    const pwdText = document.getElementById('credPasswordText');
    if (pwdBtn && pwdText) {
      let revealed = false;
      pwdBtn.addEventListener('click', () => {
        revealed = !revealed;
        pwdText.textContent = revealed ? passwordVal : '••••••••';
        pwdBtn.textContent = revealed ? 'Hide' : 'Show';
      });
    }

    // Event: Copy buttons
    body.querySelectorAll('.copy-cred-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const text = btn.getAttribute('data-copy');
        copyTextToClipboard(text, 'Copied credentials to clipboard!');
      });
    });

    // Event: Send Credentials Button
    const sendBtn = document.getElementById('credModalSendBtn');
    if (sendBtn) {
      sendBtn.addEventListener('click', () => {
        sendFacultyCredentials(id, sendBtn);
      });
    }

    // Attach close trigger inside modal body
    body.querySelectorAll('.close-modal-trigger').forEach(btn => {
      btn.addEventListener('click', closeAllModals);
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // 2c. Send Faculty Credentials via Email
  async function sendFacultyCredentials(id, triggerBtn = null) {
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    if (!member.email || !member.email.includes('@')) {
      showToast(`Cannot send: ${member.name} has no valid email address registered.`, true);
      return;
    }

    let originalBtnHtml = '';
    if (triggerBtn) {
      originalBtnHtml = triggerBtn.innerHTML;
      triggerBtn.disabled = true;
      triggerBtn.innerHTML = `
        <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Sending...</span>
      `;
    }

    showToast(`Sending credentials to ${member.email}...`);

    try {
      const baseUrl = (typeof window !== 'undefined' && window.portalBaseUrl) ? window.portalBaseUrl : '';
      const endpoint = baseUrl.replace(/\/+$/, '') + '/api/faculty/send-credentials';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });

      const data = await response.json();
      if (data && data.success) {
        showToast(data.message || `✓ Credentials successfully sent to ${member.email}!`);
        if (triggerBtn) {
          triggerBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            <span>Sent Successfully!</span>
          `;
          setTimeout(() => {
            if (triggerBtn) {
              triggerBtn.disabled = false;
              triggerBtn.innerHTML = originalBtnHtml;
            }
          }, 3000);
        }
      } else {
        throw new Error((data && data.error) ? data.error : 'Failed to send credentials email.');
      }
    } catch (err) {
      showToast(`Email error: ${err.message}`, true);
      if (triggerBtn) {
        triggerBtn.disabled = false;
        triggerBtn.innerHTML = originalBtnHtml;
      }
    }
  }

  // 3. Edit / Add Modal (Supports all users.json & facultyData.js fields)
  function showEditModal(id = null) {
    targetFacultyId = id;
    const modal = document.getElementById('facultyFormModal');
    const modalTitle = document.getElementById('formModalTitle');
    const form = document.getElementById('facultyForm');
    if (!modal || !form) return;

    if (id) {
      const member = facultyList.find(m => m.id === id);
      if (!member) return;
      if (modalTitle) modalTitle.textContent = 'Edit Faculty Profile';

      document.getElementById('inputName').value = member.name || '';
      document.getElementById('inputInitials').value = member.initials || '';
      document.getElementById('inputDesignation').value = member.designation || '';
      document.getElementById('inputDepartment').value = member.department || 'Information Technology';
      document.getElementById('inputEmpId').value = member.empId || '';
      document.getElementById('inputSetting').value = member.setting || 'FF-38';
      document.getElementById('inputEmail').value = member.email || '';
      document.getElementById('inputPhone').value = member.phone || '';
      document.getElementById('inputStatus').value = member.status || 'active';

      if (document.getElementById('inputUsername')) {
        document.getElementById('inputUsername').value = member.username || (member.email ? member.email.split('@')[0] : member.id);
      }
      if (document.getElementById('inputRole')) {
        let r = (member.role || 'faculty').toLowerCase();
        if (r === 'staff') r = 'faculty';
        document.getElementById('inputRole').value = r;
      }
      if (document.getElementById('inputPassword')) {
        document.getElementById('inputPassword').value = member.password || '';
      }
      if (document.getElementById('inputSemClass')) {
        document.getElementById('inputSemClass').value = member.semClass || '';
      }
      if (document.getElementById('inputDriveLink')) {
        document.getElementById('inputDriveLink').value = member.driveLink || '';
      }
    } else {
      if (modalTitle) modalTitle.textContent = 'Add New Faculty Member';
      form.reset();
      document.getElementById('inputDepartment').value = currentDept === 'all' ? 'Information Technology' : currentDept;
      document.getElementById('inputStatus').value = 'active';
      document.getElementById('inputSetting').value = 'FF-38';
      if (document.getElementById('inputRole')) document.getElementById('inputRole').value = 'faculty';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // Save Form Handler (Persisted to backend users.json)
  async function handleFormSubmit(e) {
    e.preventDefault();

    const name = document.getElementById('inputName').value.trim();
    const initials = document.getElementById('inputInitials').value.trim() || name.split(' ').map(w => w[0]).join('').substring(0, 3).toUpperCase();
    const designation = document.getElementById('inputDesignation').value.trim() || 'Faculty Member';
    const department = document.getElementById('inputDepartment').value;
    const empId = document.getElementById('inputEmpId').value.trim() || '#---';
    const setting = document.getElementById('inputSetting').value.trim() || 'FF-38';
    const email = document.getElementById('inputEmail').value.trim();
    const phone = document.getElementById('inputPhone').value.trim();
    const status = document.getElementById('inputStatus').value;

    const username = (document.getElementById('inputUsername')?.value || '').trim();
    const role = (document.getElementById('inputRole')?.value || 'faculty').trim();
    const password = (document.getElementById('inputPassword')?.value || '').trim();
    const semClass = (document.getElementById('inputSemClass')?.value || '').trim();
    const driveLink = (document.getElementById('inputDriveLink')?.value || '').trim();

    if (!name) {
      showToast('Faculty name is required.', true);
      return;
    }

    const payload = {
      id: targetFacultyId || '',
      name,
      initials,
      designation,
      department,
      empId,
      setting,
      email,
      phone,
      status,
      username,
      role,
      password,
      semClass,
      driveLink
    };

    try {
      const baseUrl = (typeof window !== 'undefined' && window.portalBaseUrl) ? window.portalBaseUrl : '';
      const endpoint = baseUrl.replace(/\/+$/, '') + '/api/faculty/save';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await response.json();
      if (data && data.success) {
        if (targetFacultyId) {
          const idx = facultyList.findIndex(m => m.id === targetFacultyId);
          if (idx !== -1) {
            facultyList[idx] = { ...facultyList[idx], ...data.faculty };
          }
        } else {
          facultyList.unshift(data.faculty);
        }

        saveFacultyData();
        closeAllModals();
        renderTable();
        showToast(data.message || `Saved profile for ${name}`);
      } else {
        throw new Error((data && data.error) ? data.error : 'Failed to save faculty record.');
      }
    } catch (err) {
      showToast('Save failed: ' + err.message, true);
    }
  }

  // 4. Delete Modal (Persisted to backend users.json)
  function showDeleteModal(id) {
    targetFacultyId = id;
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const modal = document.getElementById('deleteConfirmModal');
    const deleteNameSpan = document.getElementById('deleteFacultyName');
    if (!modal) return;

    if (deleteNameSpan) {
      deleteNameSpan.textContent = `${member.name} (${member.empId || member.initials})`;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  async function confirmDelete() {
    if (!targetFacultyId) return;

    const member = facultyList.find(m => m.id === targetFacultyId);
    const memberName = member ? member.name : 'Faculty member';

    try {
      const baseUrl = (typeof window !== 'undefined' && window.portalBaseUrl) ? window.portalBaseUrl : '';
      const endpoint = baseUrl.replace(/\/+$/, '') + '/api/faculty/delete';

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: targetFacultyId })
      });

      const data = await response.json();
      if (data && data.success) {
        facultyList = facultyList.filter(m => m.id !== targetFacultyId);
        saveFacultyData();
        closeAllModals();
        renderTable();
        showToast(data.message || `Deleted ${memberName}`, true);
        targetFacultyId = null;
      } else {
        throw new Error((data && data.error) ? data.error : 'Failed to delete faculty member.');
      }
    } catch (err) {
      showToast('Delete failed: ' + err.message, true);
    }
  }

  // Close All Modals
  function closeAllModals() {
    ['viewFacultyModal', 'credentialsModal', 'facultyFormModal', 'deleteConfirmModal'].forEach(id => {
      const modal = document.getElementById(id);
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }
    });
    targetFacultyId = null;
  }

  // Update UI Pills State
  function updateActiveFilterPills() {
    // Dept buttons
    ['deptFilterAll', 'deptFilterIt', 'deptFilterCe'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.classList.remove('active');
    });

    if (currentDept === 'Information Technology') {
      const b = document.getElementById('deptFilterIt');
      if (b) b.classList.add('active');
    } else if (currentDept === 'Computer Engineering') {
      const b = document.getElementById('deptFilterCe');
      if (b) b.classList.add('active');
    } else {
      const b = document.getElementById('deptFilterAll');
      if (b) b.classList.add('active');
    }

    // Status buttons
    ['statusFilterAll', 'statusFilterActive', 'statusFilterInactive'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.classList.remove('active');
    });

    if (currentStatus === 'active') {
      const b = document.getElementById('statusFilterActive');
      if (b) b.classList.add('active');
    } else if (currentStatus === 'inactive') {
      const b = document.getElementById('statusFilterInactive');
      if (b) b.classList.add('active');
    } else {
      const b = document.getElementById('statusFilterAll');
      if (b) b.classList.add('active');
    }
  }

  // Export to Excel (Lazy-loads SheetJS)
  function exportFacultyExcel() {
    const listToExport = getFilteredList();
    if (!listToExport || listToExport.length === 0) {
      alert('No faculty records found for the current selection to export.');
      return;
    }

    const runExport = () => {
      const rows = listToExport.map((m, idx) => ({
        'Sr. No.': idx + 1,
        'Faculty Name': m.name || '',
        'Initials': m.initials || '',
        'Designation': m.designation || '',
        'Department': m.department || 'Information Technology',
        'Employee ID': (m.empId || '').replace('#', ''),
        'Email Address': m.email || '',
        'Mobile Number': m.phone ? `+91 ${m.phone}` : '',
        'Cabin Setting': m.setting || '',
        'Status': (m.status || 'active').toUpperCase()
      }));

      const worksheet = XLSX.utils.json_to_sheet(rows);
      worksheet['!cols'] = [
        { wch: 8 },
        { wch: 28 },
        { wch: 10 },
        { wch: 24 },
        { wch: 26 },
        { wch: 14 },
        { wch: 32 },
        { wch: 18 },
        { wch: 14 },
        { wch: 12 }
      ];

      const workbook = XLSX.utils.book_new();
      let sheetLabel = 'All Faculty';
      if (currentDept === 'Information Technology') sheetLabel = 'IT Faculty';
      if (currentDept === 'Computer Engineering') sheetLabel = 'CE Faculty';

      XLSX.utils.book_append_sheet(workbook, worksheet, sheetLabel);

      const dateStr = new Date().toISOString().split('T')[0];
      const fileName = `Faculty_List_${sheetLabel.replace(/\s+/g, '_')}_${dateStr}.xlsx`;

      XLSX.writeFile(workbook, fileName);
      showToast(`Exported ${rows.length} faculty records to Excel!`);
    };

    if (typeof window.XLSX === 'undefined') {
      const script = document.createElement('script');
      script.src = 'assets/vendor/xlsx/xlsx.full.min.js';
      script.onload = runExport;
      document.body.appendChild(script);
    } else {
      runExport();
    }
  }

  // --- INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    loadFacultyData();

    // 1. Department Filters
    const deptAllBtn = document.getElementById('deptFilterAll');
    const deptItBtn = document.getElementById('deptFilterIt');
    const deptCeBtn = document.getElementById('deptFilterCe');

    if (deptAllBtn) {
      deptAllBtn.addEventListener('click', () => {
        currentDept = 'all';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    if (deptItBtn) {
      deptItBtn.addEventListener('click', () => {
        currentDept = 'Information Technology';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    if (deptCeBtn) {
      deptCeBtn.addEventListener('click', () => {
        currentDept = 'Computer Engineering';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    // 2. Status Filters
    const statusAllBtn = document.getElementById('statusFilterAll');
    const statusActiveBtn = document.getElementById('statusFilterActive');
    const statusInactiveBtn = document.getElementById('statusFilterInactive');

    if (statusAllBtn) {
      statusAllBtn.addEventListener('click', () => {
        currentStatus = 'all';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    if (statusActiveBtn) {
      statusActiveBtn.addEventListener('click', () => {
        currentStatus = 'active';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    if (statusInactiveBtn) {
      statusInactiveBtn.addEventListener('click', () => {
        currentStatus = 'inactive';
        currentPage = 1;
        updateActiveFilterPills();
        renderTable();
      });
    }

    // 3. Search Input with Debounce
    const searchInp = document.getElementById('facultySearchInput');
    if (searchInp) {
      let debounceTimer;
      searchInp.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          searchQuery = e.target.value;
          currentPage = 1;
          renderTable();
        }, 180);
      });
    }

    // 3b. Synchronized Top Navbar Search Listener
    window.addEventListener('portal:search', (e) => {
      searchQuery = e.detail?.query || '';
      if (searchInp) searchInp.value = searchQuery;
      currentPage = 1;
      renderTable();
    });

    // 4. Page Size Selector
    const pageSizeSelect = document.getElementById('pageSizeSelect');
    if (pageSizeSelect) {
      pageSizeSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        pageSize = val === 'all' ? 'all' : parseInt(val, 10);
        currentPage = 1;
        renderTable();
      });
    }

    // 5. Add Faculty Button
    const addBtn = document.getElementById('addFacultyBtn');
    if (addBtn) {
      addBtn.addEventListener('click', () => {
        showEditModal(null);
      });
    }

    // 6. Export Excel Button
    const exportBtn = document.getElementById('exportExcelBtn');
    if (exportBtn) {
      exportBtn.addEventListener('click', exportFacultyExcel);
    }

    // 7. Reset Defaults Button
    const resetBtn = document.getElementById('resetFacultyDefaultsBtn');
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (confirm('Reset faculty directory back to initial defaults (110 faculty records)? Any custom edits will be restored.')) {
          localStorage.removeItem(STORAGE_KEY);
          loadFacultyData();
          currentPage = 1;
          renderTable();
          showToast('Faculty directory reset to defaults');
        }
      });
    }

    // 8. Form Submit
    const facultyForm = document.getElementById('facultyForm');
    if (facultyForm) {
      facultyForm.addEventListener('submit', handleFormSubmit);
    }

    // 9. Confirm Delete Button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
      confirmDeleteBtn.addEventListener('click', confirmDelete);
    }

    // 10. Close Modals Buttons & Outside Backdrop Click to Auto-Close
    document.querySelectorAll('.close-modal-trigger').forEach(btn => {
      btn.addEventListener('click', closeAllModals);
    });

    ['viewFacultyModal', 'credentialsModal', 'facultyFormModal', 'deleteConfirmModal'].forEach(modalId => {
      const modalEl = document.getElementById(modalId);
      if (modalEl) {
        modalEl.addEventListener('click', (e) => {
          // If the user clicked directly on the outer backdrop overlay, close modal
          if (e.target === modalEl) {
            closeAllModals();
          }
        });
      }
    });

    // Dismiss on Escape Key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAllModals();
        closeActionMenu();
      }
    });

    // Dismiss floating action menu on outside click
    document.addEventListener('click', (e) => {
      if (activeMenuEl && !activeMenuEl.contains(e.target)) {
        closeActionMenu();
      }
    });

    window.addEventListener('scroll', closeActionMenu, { passive: true });
    window.addEventListener('resize', closeActionMenu);

    // Initial Render
    updateActiveFilterPills();
    renderTable();
  });

})();

