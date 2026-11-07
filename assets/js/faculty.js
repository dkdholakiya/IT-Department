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

  // Toast Notification
  function showToast(message, isAlert = false) {
    const toast = document.getElementById('toastBanner');
    const toastMsg = document.getElementById('toastMessage');
    const toastDot = document.getElementById('toastDot');
    if (!toast || !toastMsg) return;

    toastMsg.textContent = message;
    if (toastDot) {
      toastDot.className = isAlert 
        ? 'w-2 h-2 rounded-full bg-[var(--color-alert)]'
        : 'w-2 h-2 rounded-full bg-[var(--color-primary-light)]';
    }
    toast.classList.remove('hidden');
    toast.classList.add('flex');

    if (toast._timer) clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.classList.add('hidden');
      toast.classList.remove('flex');
    }, 3200);
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
                  ${member.name}
                </div>
                <div class="flex items-center gap-1.5 mt-0.5 text-[11px] sm:text-xs text-[var(--text-muted)] flex-wrap">
                  <span class="font-mono font-semibold text-[10px] sm:text-[11px] px-1.5 py-0.2 rounded bg-black/5 dark:bg-white/10 text-[var(--text-secondary)]">
                    ${member.initials || 'FC'}
                  </span>
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

          <!-- Status Pill -->
          <td>
            <span class="fac-status-pill ${isInactive ? 'status-inactive' : 'status-active'}">
              <span class="status-dot ${isInactive ? 'status-dot-inactive' : 'status-dot-active'}"></span>
              ${isInactive ? 'Inactive' : 'Active'}
            </span>
          </td>

          <!-- Single Action Dropdown Button (Sticky Right) -->
          <td class="action-cell">
            <button
              type="button"
              class="action-dropdown-btn"
              data-id="${member.id}"
              aria-label="Actions for ${member.name}"
              title="Open Actions Menu"
            >
              <span>Actions</span>
              <svg class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
              </svg>
            </button>
          </td>
        </tr>
      `;
    });

    tbody.innerHTML = rowsHtml;

    // Attach Event Listeners to Action Buttons
    const actionBtns = tbody.querySelectorAll('.action-dropdown-btn');
    actionBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const id = btn.getAttribute('data-id');
        openActionMenu(btn, id);
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

  // --- FLOATING ACTION MENU ---
  function closeActionMenu() {
    if (activeMenuEl && activeMenuEl.parentNode) {
      activeMenuEl.parentNode.removeChild(activeMenuEl);
    }
    activeMenuEl = null;
  }

  function openActionMenu(btn, id) {
    closeActionMenu();

    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const isInactive = member.status === 'inactive';

    const menu = document.createElement('div');
    menu.className = 'action-menu';
    menu.id = 'floatingActionMenu';

    menu.innerHTML = `
      <!-- View Option -->
      <button type="button" class="action-menu-item" data-action="view" data-id="${member.id}">
        <svg class="w-4 h-4 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        <span>View Details</span>
      </button>

      <!-- Edit Option -->
      <button type="button" class="action-menu-item" data-action="edit" data-id="${member.id}">
        <svg class="w-4 h-4 text-[#0284C7]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
          <path d="m15 5 4 4"/>
        </svg>
        <span>Edit Faculty</span>
      </button>

      <!-- Active / Inactive Toggle -->
      <button type="button" class="action-menu-item" data-action="toggle-status" data-id="${member.id}">
        <svg class="w-4 h-4 ${isInactive ? 'text-[#10B981]' : 'text-[#F59E0B]'}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        <span>${isInactive ? 'Mark as Active' : 'Mark as Inactive'}</span>
      </button>

      <div class="action-menu-divider"></div>

      <!-- Delete Option -->
      <button type="button" class="action-menu-item item-delete" data-action="delete" data-id="${member.id}">
        <svg class="w-4 h-4 text-[#EF4444]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
          <line x1="10" x2="10" y1="11" y2="17"/>
          <line x1="14" x2="14" y1="11" y2="17"/>
        </svg>
        <span class="text-[#EF4444]">Delete Faculty</span>
      </button>
    `;

    document.body.appendChild(menu);
    activeMenuEl = menu;

    // Position Menu Near Button with Multi-Edge Collision Detection
    const rect = btn.getBoundingClientRect();
    const menuWidth = 185;
    const menuHeight = menu.offsetHeight || 160;

    let left = rect.right - menuWidth;
    let top = rect.bottom + 6;

    // Viewport edge guards (horizontal & vertical)
    if (left < 10) left = 10;
    if (left + menuWidth > window.innerWidth - 10) {
      left = window.innerWidth - menuWidth - 10;
    }
    if (top + menuHeight > window.innerHeight - 10) {
      top = rect.top - menuHeight - 6;
    }
    if (top < 10) top = 10;

    menu.style.left = `${left}px`;
    menu.style.top = `${top}px`;

    // Action clicks
    menu.querySelectorAll('[data-action]').forEach(item => {
      item.addEventListener('click', (e) => {
        e.stopPropagation();
        const action = item.getAttribute('data-action');
        const mId = item.getAttribute('data-id');
        closeActionMenu();

        if (action === 'view') {
          showViewModal(mId);
        } else if (action === 'edit') {
          showEditModal(mId);
        } else if (action === 'toggle-status') {
          toggleFacultyStatus(mId);
        } else if (action === 'delete') {
          showDeleteModal(mId);
        }
      });
    });
  }

  // --- ACTIONS IMPLEMENTATION ---

  // 1. Toggle Active / Inactive
  function toggleFacultyStatus(id) {
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    member.status = member.status === 'inactive' ? 'active' : 'inactive';
    saveFacultyData();
    renderTable();

    const newStatusLabel = member.status === 'active' ? 'Active' : 'Inactive';
    showToast(`${member.name} is now marked as ${newStatusLabel}`);
  }

  // 2. View Modal
  function showViewModal(id) {
    const member = facultyList.find(m => m.id === id);
    if (!member) return;

    const modal = document.getElementById('viewFacultyModal');
    const body = document.getElementById('viewModalBody');
    if (!modal || !body) return;

    const avatarStyle = getAvatarStyle(member);
    const isInactive = member.status === 'inactive';
    const cleanPhone = (member.phone || '').replace(/\s+/g, '');

    body.innerHTML = `
      <div class="flex flex-col items-center text-center pb-3 border-b border-black/5 dark:border-white/10">
        <div class="fac-avatar ${avatarStyle} w-16 h-16 text-xl rounded-2xl shadow-lg mb-3">
          ${member.initials || 'FC'}
        </div>
        <h4 class="text-lg font-bold text-[var(--text-primary)] font-serif">${member.name}</h4>
        <p class="text-xs font-semibold text-[var(--color-primary)] mt-0.5">${member.designation || 'Faculty Member'}</p>
        <div class="mt-2 flex items-center gap-2">
          <span class="fac-dept-badge badge-it">${member.department || 'Information Technology'}</span>
          <span class="fac-status-pill ${isInactive ? 'status-inactive' : 'status-active'}">
            <span class="status-dot ${isInactive ? 'status-dot-inactive' : 'status-dot-active'}"></span>
            ${isInactive ? 'Inactive' : 'Active'}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 py-4 text-xs border-b border-black/5 dark:border-white/10">
        <div class="p-2.5 rounded-xl bg-black/[0.03] dark:bg-white/[0.05]">
          <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Employee ID</span>
          <span class="font-mono font-bold text-sm text-[var(--text-primary)] mt-0.5 block">${member.empId || '---'}</span>
        </div>
        <div class="p-2.5 rounded-xl bg-black/[0.03] dark:bg-white/[0.05]">
          <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Cabin / Room</span>
          <span class="font-bold text-sm text-[var(--text-primary)] mt-0.5 block">${member.setting || 'FF-38'}</span>
        </div>
      </div>

      <div class="space-y-2.5 py-3 text-xs">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Email Address</span>
          <a href="mailto:${member.email}" class="text-[var(--color-primary)] hover:underline font-medium flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            ${member.email || 'No email provided'}
          </a>
        </div>

        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Phone Number</span>
          <a href="tel:${cleanPhone}" class="text-[var(--text-primary)] hover:underline font-mono flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 shrink-0 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            ${member.phone ? `+91 ${member.phone}` : '---'}
          </a>
        </div>

        ${member.driveLink ? `
          <div class="pt-2">
            <a href="${member.driveLink}" target="_blank" rel="noopener noreferrer" class="w-full py-2 px-3 rounded-xl bg-[var(--color-primary)] text-white text-center font-bold flex items-center justify-center gap-2 hover:opacity-90 transition">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
              </svg>
              <span>Open Master Drive Repository</span>
            </a>
          </div>
        ` : ''}
      </div>
    `;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // 3. Edit / Add Modal
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
    } else {
      if (modalTitle) modalTitle.textContent = 'Add New Faculty Member';
      form.reset();
      document.getElementById('inputDepartment').value = currentDept === 'all' ? 'Information Technology' : currentDept;
      document.getElementById('inputStatus').value = 'active';
      document.getElementById('inputSetting').value = 'FF-38';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  // Save Form Handler
  function handleFormSubmit(e) {
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

    if (!name) {
      alert('Faculty name is required.');
      return;
    }

    if (targetFacultyId) {
      // Edit existing
      const member = facultyList.find(m => m.id === targetFacultyId);
      if (member) {
        member.name = name;
        member.initials = initials;
        member.designation = designation;
        member.department = department;
        member.empId = empId;
        member.setting = setting;
        member.email = email;
        member.phone = phone;
        member.status = status;
        showToast(`Updated profile for ${name}`);
      }
    } else {
      // Add new
      const newId = 'fac_' + Date.now();
      facultyList.unshift({
        id: newId,
        name,
        initials,
        designation,
        department,
        empId,
        setting,
        email,
        phone,
        status
      });
      showToast(`Added new faculty: ${name}`);
    }

    saveFacultyData();
    closeAllModals();
    renderTable();
  }

  // 4. Delete Modal
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

  function confirmDelete() {
    if (!targetFacultyId) return;

    const member = facultyList.find(m => m.id === targetFacultyId);
    const memberName = member ? member.name : 'Faculty member';

    facultyList = facultyList.filter(m => m.id !== targetFacultyId);
    saveFacultyData();
    closeAllModals();
    renderTable();
    showToast(`Deleted ${memberName}`, true);
    targetFacultyId = null;
  }

  // Close All Modals
  function closeAllModals() {
    ['viewFacultyModal', 'facultyFormModal', 'deleteConfirmModal'].forEach(id => {
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

    // 10. Close Modals Buttons
    document.querySelectorAll('.close-modal-trigger').forEach(btn => {
      btn.addEventListener('click', closeAllModals);
    });

    // Dismiss on Backdrop Click or Escape Key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAllModals();
        closeActionMenu();
      }
    });

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

