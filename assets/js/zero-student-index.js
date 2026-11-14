/**
 * Zero Student Log Directory — CE & IT Department
 * Unified Management & Live JSON Registry Viewer
 */

(function () {
  'use strict';

  // --- STATE ---
  let records = Array.isArray(window.initialZeroRecords) ? [...window.initialZeroRecords] : [];
  let filteredRecords = [];
  let activeDept = 'all';
  let selectedFaculty = '';
  let selectedSem = '';
  let selectedDate = '';
  let searchQuery = '';
  let currentPage = 1;
  let pageSize = 20;
  let recordToDeleteId = null;

  // --- DOM ELEMENTS ---
  const tableBody = document.getElementById('recordsTableBody');
  const tableInfoText = document.getElementById('tableInfoText');
  const paginationControls = document.getElementById('paginationControls');
  const pageSizeSelect = document.getElementById('pageSizeSelect');

  const kpiTotal = document.getElementById('kpiTotal');
  const kpiIt = document.getElementById('kpiIt');
  const kpiCe = document.getElementById('kpiCe');
  const kpiToday = document.getElementById('kpiToday');
  const totalLogsBadge = document.getElementById('totalLogsBadge');

  const deptFilterAll = document.getElementById('deptFilterAll');
  const deptFilterIt = document.getElementById('deptFilterIt');
  const deptFilterCe = document.getElementById('deptFilterCe');
  const facultyFilterSelect = document.getElementById('facultyFilterSelect');
  const semFilterSelect = document.getElementById('semFilterSelect');
  const dateFilterInput = document.getElementById('dateFilterInput');
  const recordSearchInput = document.getElementById('recordSearchInput');
  const resetFiltersBtn = document.getElementById('resetFiltersBtn');
  const refreshRecordsBtn = document.getElementById('refreshRecordsBtn');
  const exportExcelBtn = document.getElementById('exportExcelBtn');

  // Modals
  const viewRecordModal = document.getElementById('viewRecordModal');
  const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
  const closeDetailModalBottomBtn = document.getElementById('closeDetailModalBottomBtn');
  const detailModalBody = document.getElementById('detailModalBody');
  const modalRecordId = document.getElementById('modalRecordId');

  const deleteRecordModal = document.getElementById('deleteRecordModal');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

  // --- INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.canViewAllFaculty && window.loggedInUser && window.loggedInUser.initials) {
      selectedFaculty = window.loggedInUser.initials.toUpperCase().trim();
    }
    initFlatpickrDateFilter();
    populateFacultyFilter();
    setupEventListeners();
    applyFilters();

    // If initial records was empty, attempt to fetch from API
    if (records.length === 0) {
      fetchRecordsFromServer();
    }
  });

  // --- DATE PICKER SETUP ---
  function initFlatpickrDateFilter() {
    if (typeof flatpickr !== 'undefined' && dateFilterInput) {
      flatpickr(dateFilterInput, {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd-M-Y',
        allowInput: true,
        onChange: (selectedDates, dateStr) => {
          selectedDate = dateStr || '';
          currentPage = 1;
          applyFilters();
        }
      });
    }
  }

  // --- POPULATE FACULTY FILTER DROPDOWN ---
  function populateFacultyFilter() {
    if (!facultyFilterSelect) return;
    facultyFilterSelect.innerHTML = '';

    // Non-admin faculty: Strictly populate ONLY the logged-in user and lock dropdown
    if (!window.canViewAllFaculty && window.loggedInUser) {
      const u = window.loggedInUser;
      const init = (u.initials || '').toUpperCase().trim();
      const opt = document.createElement('option');
      opt.value = init;
      opt.textContent = `${u.name || 'Faculty Member'} (${init})`;
      opt.selected = true;
      facultyFilterSelect.appendChild(opt);
      facultyFilterSelect.disabled = true;
      selectedFaculty = init;
      return;
    }

    // Build faculty map from facultyData if available, plus unique records (Admin Oversight Mode)
    const facultyMap = new Map();

    if (Array.isArray(window.facultyData)) {
      window.facultyData.forEach(f => {
        if (f.initials && f.name) {
          facultyMap.set(f.initials.toUpperCase(), {
            name: f.name,
            initials: f.initials.toUpperCase(),
            dept: f.department || ''
          });
        }
      });
    }

    // Also collect from records to guarantee zero missing entries
    records.forEach(r => {
      const init = (r.facultyInitials || '').toUpperCase().trim();
      if (init && !facultyMap.has(init)) {
        facultyMap.set(init, {
          name: r.facultyName || ('Prof. ' + init),
          initials: init,
          dept: r.department || ''
        });
      }
    });

    // Sort alphabetically by name
    const sortedFaculties = Array.from(facultyMap.values()).sort((a, b) =>
      a.name.localeCompare(b.name)
    );

    // Keep "All Faculty Members" option
    facultyFilterSelect.innerHTML = '<option value="">All Faculty Members</option>';
    sortedFaculties.forEach(f => {
      const opt = document.createElement('option');
      opt.value = f.initials;
      opt.textContent = `${f.name} (${f.initials})`;
      facultyFilterSelect.appendChild(opt);
    });
  }

  // --- EVENT LISTENERS ---
  function setupEventListeners() {
    // Department Segment Filter
    if (deptFilterAll) {
      deptFilterAll.addEventListener('click', () => setDepartmentFilter('all'));
    }
    if (deptFilterIt) {
      deptFilterIt.addEventListener('click', () => setDepartmentFilter('Information Technology'));
    }
    if (deptFilterCe) {
      deptFilterCe.addEventListener('click', () => setDepartmentFilter('Computer Engineering'));
    }

    // Faculty filter
    if (facultyFilterSelect) {
      facultyFilterSelect.addEventListener('change', (e) => {
        selectedFaculty = (e.target.value || '').toUpperCase().trim();
        currentPage = 1;
        applyFilters();
      });
    }

    // Semester filter
    if (semFilterSelect) {
      semFilterSelect.addEventListener('change', (e) => {
        selectedSem = e.target.value || '';
        currentPage = 1;
        applyFilters();
      });
    }

    // Search input (with debounce)
    let searchTimeout = null;
    if (recordSearchInput) {
      recordSearchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          searchQuery = (e.target.value || '').toLowerCase().trim();
          currentPage = 1;
          applyFilters();
        }, 150);
      });
    }

    // Top global navbar search sync (if available)
    const globalSearch = document.getElementById('navbarSearchInput');
    if (globalSearch && globalSearch !== recordSearchInput) {
      globalSearch.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          searchQuery = (e.target.value || '').toLowerCase().trim();
          if (recordSearchInput) recordSearchInput.value = e.target.value;
          currentPage = 1;
          applyFilters();
        }, 150);
      });
    }

    // Reset Filters button
    if (resetFiltersBtn) {
      resetFiltersBtn.addEventListener('click', resetAllFilters);
    }

    // Refresh from API
    if (refreshRecordsBtn) {
      refreshRecordsBtn.addEventListener('click', fetchRecordsFromServer);
    }

    // Export to Excel
    if (exportExcelBtn) {
      exportExcelBtn.addEventListener('click', exportToExcel);
    }

    // Page Size Selector
    if (pageSizeSelect) {
      pageSizeSelect.addEventListener('change', (e) => {
        pageSize = e.target.value === 'all' ? 999999 : parseInt(e.target.value, 10);
        currentPage = 1;
        renderTable();
      });
    }

    // Search bar clear button
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    if (recordSearchInput && clearSearchBtn) {
      recordSearchInput.addEventListener('input', () => {
        clearSearchBtn.classList.toggle('hidden', !recordSearchInput.value);
      });
      clearSearchBtn.addEventListener('click', () => {
        recordSearchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        searchQuery = '';
        currentPage = 1;
        applyFilters();
      });
    }

    // Offcanvas Standalone Fallback Controller
    const offcanvasEl = document.getElementById('offcanvasRight');
    const filterOffcanvasBtn = document.getElementById('filterOffcanvasBtn');

    function toggleCustomOffcanvas(show) {
      if (!offcanvasEl) return;
      let backdrop = document.getElementById('offcanvasBackdrop');
      if (show) {
        if (!backdrop) {
          backdrop = document.createElement('div');
          backdrop.id = 'offcanvasBackdrop';
          backdrop.className = 'offcanvas-backdrop fade';
          document.body.appendChild(backdrop);
          backdrop.addEventListener('click', () => toggleCustomOffcanvas(false));
        }
        offcanvasEl.classList.add('showing');
        setTimeout(() => {
          offcanvasEl.classList.remove('showing');
          offcanvasEl.classList.add('show');
          backdrop.classList.add('show');
        }, 10);
      } else {
        offcanvasEl.classList.add('hiding');
        if (backdrop) backdrop.classList.remove('show');
        setTimeout(() => {
          offcanvasEl.classList.remove('show', 'hiding');
          if (backdrop && backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
          }
        }, 300);
      }
    }

    if (filterOffcanvasBtn) {
      filterOffcanvasBtn.addEventListener('click', (e) => {
        if (typeof bootstrap === 'undefined' || !bootstrap.Offcanvas) {
          e.preventDefault();
          toggleCustomOffcanvas(!offcanvasEl.classList.contains('show'));
        }
      });
    }

    document.querySelectorAll('[data-bs-dismiss="offcanvas"]').forEach(btn => {
      btn.addEventListener('click', () => {
        if (typeof bootstrap === 'undefined' || !bootstrap.Offcanvas) {
          toggleCustomOffcanvas(false);
        }
      });
    });

    // Modal close listeners
    if (closeDetailModalBtn) closeDetailModalBtn.addEventListener('click', closeViewModal);
    if (closeDetailModalBottomBtn) closeDetailModalBottomBtn.addEventListener('click', closeViewModal);
    if (viewRecordModal) {
      viewRecordModal.addEventListener('click', (e) => {
        if (e.target === viewRecordModal) closeViewModal();
      });
    }

    // Delete modal listeners
    if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', executeDeleteRecord);
    if (deleteRecordModal) {
      deleteRecordModal.addEventListener('click', (e) => {
        if (e.target === deleteRecordModal) closeDeleteModal();
      });
    }

    // Escape key closes modals
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeViewModal();
        closeDeleteModal();
      }
    });
  }

  // --- DEPARTMENT SEGMENT HANDLER ---
  function setDepartmentFilter(dept) {
    activeDept = dept;
    [deptFilterAll, deptFilterIt, deptFilterCe].forEach(btn => {
      if (btn) btn.classList.remove('active');
    });

    if (dept === 'Information Technology' && deptFilterIt) {
      deptFilterIt.classList.add('active');
    } else if (dept === 'Computer Engineering' && deptFilterCe) {
      deptFilterCe.classList.add('active');
    } else if (deptFilterAll) {
      deptFilterAll.classList.add('active');
    }

    currentPage = 1;
    applyFilters();
  }

  // --- RESET ALL FILTERS ---
  function resetAllFilters() {
    activeDept = 'all';
    if (!window.canViewAllFaculty && window.loggedInUser && window.loggedInUser.initials) {
      selectedFaculty = window.loggedInUser.initials.toUpperCase().trim();
      if (facultyFilterSelect) facultyFilterSelect.value = selectedFaculty;
    } else {
      selectedFaculty = '';
      if (facultyFilterSelect) facultyFilterSelect.value = '';
    }
    selectedSem = '';
    selectedDate = '';
    searchQuery = '';
    currentPage = 1;

    [deptFilterAll, deptFilterIt, deptFilterCe].forEach(btn => {
      if (btn) btn.classList.remove('active');
    });
    if (deptFilterAll) deptFilterAll.classList.add('active');

    if (facultyFilterSelect) facultyFilterSelect.value = '';
    if (semFilterSelect) semFilterSelect.value = '';
    if (recordSearchInput) recordSearchInput.value = '';
    const globalSearch = document.getElementById('navbarSearchInput');
    if (globalSearch) globalSearch.value = '';

    if (dateFilterInput) {
      if (dateFilterInput._flatpickr) {
        dateFilterInput._flatpickr.clear();
      } else {
        dateFilterInput.value = '';
      }
    }

    applyFilters();
  }

  // --- FETCH RECORDS FROM SERVER ---
  async function fetchRecordsFromServer() {
    if (refreshRecordsBtn) {
      refreshRecordsBtn.classList.add('animate-spin');
    }

    try {
      const res = await fetch('api/zero-student-records.php');
      if (!res.ok) throw new Error('Failed to fetch records');
      const data = await res.json();
      if (data && Array.isArray(data.records)) {
        records = data.records;
        populateFacultyFilter();
        applyFilters();
        showToast('Records reloaded successfully!');
      }
    } catch (err) {
      console.warn('API fetch warning:', err);
      // Fallback: try root-relative route
      try {
        const res2 = await fetch('./api/zero-student-records');
        const data2 = await res2.json();
        if (data2 && Array.isArray(data2.records)) {
          records = data2.records;
          populateFacultyFilter();
          applyFilters();
        }
      } catch (err2) {
        console.error('Failed to reload records:', err2);
      }
    } finally {
      if (refreshRecordsBtn) {
        refreshRecordsBtn.classList.remove('animate-spin');
      }
    }
  }

  // --- APPLY FILTERS & UPDATE KPIS ---
  function applyFilters() {
    updateKPIs();

    filteredRecords = records.filter(r => {
      // 1. Department Filter
      if (activeDept !== 'all') {
        const rDept = (r.department || '').toLowerCase();
        const rBranch = (r.branch || '').toLowerCase();
        if (activeDept === 'Information Technology') {
          if (!rDept.includes('information') && !rBranch.includes('(it)') && !rBranch.includes('ict')) {
            return false;
          }
        } else if (activeDept === 'Computer Engineering') {
          if (!rDept.includes('computer') && !rBranch.includes('(ce)') && !rBranch.includes('cse')) {
            return false;
          }
        }
      }

      // 2. Faculty Filter (check initials or name)
      if (selectedFaculty) {
        const rInit = (r.facultyInitials || '').toUpperCase().trim();
        const rName = (r.facultyName || '').toUpperCase();
        if (rInit !== selectedFaculty && !rName.includes(selectedFaculty)) {
          return false;
        }
      }

      // 3. Semester Filter
      if (selectedSem) {
        if (String(r.semester || '').trim() !== String(selectedSem).trim()) {
          return false;
        }
      }

      // 4. Date Filter
      if (selectedDate) {
        const rDate = r.date || '';
        const rDateFmt = (r.dateFormatted || '').toLowerCase();
        if (!rDate.includes(selectedDate) && !rDateFmt.includes(selectedDate.toLowerCase())) {
          return false;
        }
      }

      // 5. Query Search Filter
      if (searchQuery) {
        const corpus = [
          r.subject || '',
          r.facultyInitials || '',
          r.facultyName || '',
          r.room || '',
          r.branch || '',
          r.semester || '',
          r.remarks || '',
          r.department || '',
          r.dateFormatted || ''
        ].join(' ').toLowerCase();

        if (!corpus.includes(searchQuery)) {
          return false;
        }
      }

      return true;
    });

    renderTable();
    updateFilterIndicators();
  }

  // --- UPDATE ACTIVE FILTER INDICATORS & BADGES ---
  function updateFilterIndicators() {
    const activeDeptText = document.getElementById('activeDeptText');
    if (activeDeptText) {
      activeDeptText.textContent =
        activeDept === 'Information Technology' ? 'Information Technology' :
        activeDept === 'Computer Engineering' ? 'Computer Engineering' :
        'All Departments';
    }

    const hasActiveFilters = (activeDept !== 'all') ||
      Boolean(selectedFaculty) ||
      Boolean(selectedSem) ||
      Boolean(selectedDate);

    const activeFilterBadge = document.getElementById('activeFilterBadge');
    if (activeFilterBadge) {
      activeFilterBadge.classList.toggle('hidden', !hasActiveFilters);
    }

    const offcanvasActiveBadge = document.getElementById('offcanvasActiveBadge');
    if (offcanvasActiveBadge) {
      offcanvasActiveBadge.classList.toggle('hidden', !hasActiveFilters);
    }
  }

  // --- UPDATE KPI COUNTERS ---
  function updateKPIs() {
    const total = records.length;
    let itCount = 0;
    let ceCount = 0;
    let todayCount = 0;

    // Calculate today's date formatted (YYYY-MM-DD)
    const today = new Date();
    const todayYMD = today.toISOString().split('T')[0];
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const todayFmt = `${String(today.getDate()).padStart(2, '0')}-${months[today.getMonth()]}-${today.getFullYear()}`.toLowerCase();

    records.forEach(r => {
      const dept = (r.department || '').toLowerCase();
      const branch = (r.branch || '').toLowerCase();

      if (dept.includes('computer') || branch.includes('(ce)') || branch.includes('cse')) {
        ceCount++;
      } else {
        itCount++;
      }

      const rDate = (r.date || '').trim();
      const rDateFmt = (r.dateFormatted || '').toLowerCase().trim();
      if (rDate === todayYMD || rDateFmt === todayFmt) {
        todayCount++;
      }
    });

    if (kpiTotal) kpiTotal.textContent = total;
    if (kpiIt) kpiIt.textContent = itCount;
    if (kpiCe) kpiCe.textContent = ceCount;
    if (kpiToday) kpiToday.textContent = todayCount;

    if (totalLogsBadge) {
      totalLogsBadge.textContent = `${total} Total Incidents`;
    }
  }

  // --- RENDER TABLE & PAGINATION ---
  function renderTable() {
    if (!tableBody) return;

    if (filteredRecords.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="10" class="py-12 text-center">
            <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto text-[var(--text-muted)]">
              <div class="w-12 h-12 rounded-2xl bg-black/5 dark:bg-white/10 flex items-center justify-center text-xl">
                📋
              </div>
              <div class="font-bold text-sm text-[var(--text-primary)]">No Zero Student Logs Found</div>
              <p class="text-xs">No attendance incident matches your active search or filter criteria.</p>
              <button type="button" onclick="window.resetZeroFilters()" class="mt-2 h-7 px-3 rounded-xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 transition cursor-pointer">
                Clear Filters
              </button>
            </div>
          </td>
        </tr>
      `;
      if (tableInfoText) tableInfoText.textContent = 'Showing 0 records';
      if (paginationControls) paginationControls.innerHTML = '';
      return;
    }

    // Pagination calculations
    const totalItems = filteredRecords.length;
    const totalPages = Math.ceil(totalItems / pageSize);
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = Math.min(startIndex + pageSize, totalItems);
    const pageRecords = filteredRecords.slice(startIndex, endIndex);

    // Build Table Rows
    const rowsHtml = pageRecords.map((r, i) => {
      const idx = startIndex + i + 1;
      const isCe = (r.department || '').toLowerCase().includes('computer') || (r.branch || '').toLowerCase().includes('(ce)');
      const deptBadgeClass = isCe ? 'zs-badge-dept-ce' : 'zs-badge-dept-it';
      const deptLabel = isCe ? 'CE' : 'IT';

      // Resolve faculty avatar
      const facultyInit = (r.facultyInitials || 'FA').toUpperCase().trim();
      const facultyName = r.facultyName || ('Prof. ' + facultyInit);

      // Format Date & Time display
      const dateDisplay = r.dateFormatted || r.date || '---';
      const timeIn = r.timeInFormatted || r.timeIn || '';
      const timeOut = r.timeOutFormatted || r.timeOut || '';
      const timeDisplay = (timeIn && timeOut) ? `${timeIn} – ${timeOut}` : (timeIn || timeOut || '---');

      const room = r.room || '---';
      const subject = r.subject || '---';
      const branch = r.branch || '---';
      const sem = r.semester ? `Sem ${r.semester}` : '---';
      const students = r.students || '0';
      const remarks = r.remarks || 'NO STUDENTS';
      const recordId = r.id || ('zs_' + idx);

      return `
        <tr class="hover:bg-black/5 dark:hover:bg-white/5 transition" data-id="${recordId}">
          <td class="text-center font-bold text-[var(--text-muted)] text-[11px]">${idx}</td>
          
          <td>
            <div class="flex flex-col">
              <span class="font-bold text-[var(--text-primary)]">${escapeHtml(dateDisplay)}</span>
              <span class="text-[10.5px] text-[var(--text-muted)]">${escapeHtml(timeDisplay)}</span>
            </div>
          </td>

          <td>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold ${deptBadgeClass}">
              <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
              <span>${deptLabel}</span>
            </span>
          </td>

          <td>
            <div class="flex items-center gap-1.5">
              <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-black/5 dark:bg-white/10 text-[var(--text-primary)] shrink-0">${escapeHtml(sem)}</span>
              <span class="text-xs text-[var(--text-muted)] truncate max-w-[140px]" title="${escapeHtml(branch)}">${escapeHtml(branch)}</span>
            </div>
          </td>

          <td>
            <span class="zs-badge-mono">${escapeHtml(room)}</span>
          </td>

          <td>
            <span class="px-2 py-0.5 rounded-lg text-xs font-bold bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/20">
              ${escapeHtml(subject)}
            </span>
          </td>

          <td>
            <div class="flex items-center gap-2">
              <div class="zs-faculty-avatar">${escapeHtml(facultyInit.substring(0, 3))}</div>
              <div class="flex flex-col min-w-0">
                <span class="font-bold text-xs text-[var(--text-primary)] truncate max-w-[150px]" title="${escapeHtml(facultyName)}">
                  ${escapeHtml(facultyName)}
                </span>
                <span class="text-[10px] font-mono text-[var(--text-muted)] font-semibold">${escapeHtml(facultyInit)}</span>
              </div>
            </div>
          </td>

          <td class="text-center">
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-rose-500/15 text-rose-600 dark:text-rose-400 font-bold text-xs">
              ${escapeHtml(students)}
            </span>
          </td>

          <td>
            <span class="text-xs font-medium text-[var(--text-primary)] truncate block max-w-[150px]" title="${escapeHtml(remarks)}">
              ${escapeHtml(remarks)}
            </span>
          </td>

          <td class="text-right pr-4">
            <div class="inline-flex items-center gap-1">
              <!-- View Detail Button -->
              <button
                type="button"
                class="view-record-btn w-7 h-7 rounded-lg glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--color-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition cursor-pointer"
                data-id="${escapeHtml(recordId)}"
                title="View full incident log card"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
              </button>

              ${(window.canDeleteZeroStudent !== false) ? `
              <!-- Delete Button -->
              <button
                type="button"
                class="delete-record-btn w-7 h-7 rounded-lg glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-500/10 transition cursor-pointer"
                data-id="${escapeHtml(recordId)}"
                title="Delete this incident log"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
              </button>
              ` : ''}
            </div>
          </td>
        </tr>
      `;
    }).join('');

    tableBody.innerHTML = rowsHtml;

    // Attach row button clicks
    tableBody.querySelectorAll('.view-record-btn').forEach(btn => {
      btn.addEventListener('click', () => openViewModal(btn.getAttribute('data-id')));
    });

    tableBody.querySelectorAll('.delete-record-btn').forEach(btn => {
      btn.addEventListener('click', () => openDeleteModal(btn.getAttribute('data-id')));
    });

    // Update bottom info text
    if (tableInfoText) {
      tableInfoText.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} incident logs`;
    }

    // Render pagination controls
    renderPagination(totalPages);
  }

  // --- RENDER PAGINATION BUTTONS ---
  function renderPagination(totalPages) {
    if (!paginationControls) return;

    if (totalPages <= 1) {
      paginationControls.innerHTML = '';
      return;
    }

    let html = `
      <button
        type="button"
        class="zs-page-btn"
        ${currentPage === 1 ? 'disabled' : ''}
        onclick="window.goToZeroPage(${currentPage - 1})"
        title="Previous Page"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
      </button>
    `;

    for (let p = 1; p <= totalPages; p++) {
      if (totalPages > 7) {
        if (p > 2 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
          if (p === 3 || p === totalPages - 2) {
            html += `<span class="px-1 text-xs text-[var(--text-muted)] select-none">...</span>`;
          }
          continue;
        }
      }

      html += `
        <button
          type="button"
          class="zs-page-btn ${p === currentPage ? 'active' : ''}"
          onclick="window.goToZeroPage(${p})"
        >
          ${p}
        </button>
      `;
    }

    html += `
      <button
        type="button"
        class="zs-page-btn"
        ${currentPage === totalPages ? 'disabled' : ''}
        onclick="window.goToZeroPage(${currentPage + 1})"
        title="Next Page"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </button>
    `;

    paginationControls.innerHTML = html;
  }

  // Global navigation helpers
  window.goToZeroPage = (page) => {
    currentPage = page;
    renderTable();
  };;
  window.resetZeroFilters = resetAllFilters;

  // --- MODAL: VIEW DETAILS ---
  function openViewModal(id) {
    const record = records.find(r => String(r.id) === String(id));
    if (!record || !viewRecordModal || !detailModalBody) return;

    if (modalRecordId) modalRecordId.textContent = `Record ID: #${record.id || 'N/A'}`;

    const isCe = (record.department || '').toLowerCase().includes('computer');
    const deptBadgeClass = isCe ? 'zs-badge-dept-ce' : 'zs-badge-dept-it';

    detailModalBody.innerHTML = `
      <!-- Top Overview Box -->
      <div class="p-3 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
          <div class="zs-faculty-avatar !w-9 !h-9 text-xs">${escapeHtml((record.facultyInitials || 'FA').substring(0, 3))}</div>
          <div>
            <div class="font-bold text-sm text-[var(--text-primary)]">${escapeHtml(record.facultyName || record.facultyInitials)}</div>
            <div class="text-[11px] text-[var(--text-muted)]">${escapeHtml(record.facultyEmail || 'No email provided')}</div>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-bold ${deptBadgeClass}">
          ${escapeHtml(record.department || 'CE/IT')}
        </span>
      </div>

      <!-- Details Grid -->
      <div class="grid grid-cols-2 gap-2.5 pt-1">
        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Session Date</div>
          <div class="font-bold text-xs text-[var(--text-primary)] mt-0.5">${escapeHtml(record.dateFormatted || record.date || '---')}</div>
        </div>

        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Session Timing</div>
          <div class="font-bold text-xs text-[var(--text-primary)] mt-0.5">${escapeHtml(record.timeInFormatted || record.timeIn)} – ${escapeHtml(record.timeOutFormatted || record.timeOut)}</div>
        </div>

        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Subject Code</div>
          <div class="font-bold text-xs text-amber-600 dark:text-amber-400 mt-0.5">${escapeHtml(record.subject || '---')}</div>
        </div>

        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Classroom / Lab</div>
          <div class="font-bold text-xs text-sky-600 dark:text-sky-400 font-mono mt-0.5">${escapeHtml(record.room || '---')}</div>
        </div>

        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Branch & Semester</div>
          <div class="font-bold text-xs text-[var(--text-primary)] mt-0.5">Sem ${escapeHtml(record.semester || '-')} • ${escapeHtml(record.branch || '---')}</div>
        </div>

        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Present Students</div>
          <div class="font-bold text-xs text-rose-500 mt-0.5">${escapeHtml(record.students || '0')} (Zero Attendance)</div>
        </div>
      </div>

      <!-- Remarks Box -->
      <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
        <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">Remarks / Justification</div>
        <div class="font-semibold text-xs text-[var(--text-primary)] mt-0.5">${escapeHtml(record.remarks || 'NO STUDENTS REPORTED')}</div>
      </div>

      <!-- CC Email Recipient List -->
      ${record.cc ? `
        <div class="p-2.5 rounded-xl border border-black/5 dark:border-white/10">
          <div class="text-[10px] uppercase font-bold text-[var(--text-muted)] tracking-wider">CC Notifications Dispatched</div>
          <div class="text-[11px] text-[var(--text-muted)] font-mono break-all mt-0.5">${escapeHtml(record.cc)}</div>
        </div>
      ` : ''}

      <!-- Record Timestamp -->
      <div class="flex items-center justify-between text-[10.5px] text-[var(--text-muted)] pt-1 px-1">
        <span>Logged At: ${record.createdAt ? new Date(record.createdAt).toLocaleString() : 'N/A'}</span>
        <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Verified Entry</span>
        </span>
      </div>
    `;

    viewRecordModal.classList.remove('hidden');
    viewRecordModal.classList.add('flex');
  }

  function closeViewModal() {
    if (!viewRecordModal) return;
    viewRecordModal.classList.add('hidden');
    viewRecordModal.classList.remove('flex');
  }

  // --- MODAL: DELETE RECORD ---
  function openDeleteModal(id) {
    recordToDeleteId = id;
    if (!deleteRecordModal) return;
    deleteRecordModal.classList.remove('hidden');
    deleteRecordModal.classList.add('flex');
  }

  function closeDeleteModal() {
    recordToDeleteId = null;
    if (!deleteRecordModal) return;
    deleteRecordModal.classList.add('hidden');
    deleteRecordModal.classList.remove('flex');
  }

  async function executeDeleteRecord() {
    if (!recordToDeleteId) return;

    if (confirmDeleteBtn) {
      confirmDeleteBtn.disabled = true;
      confirmDeleteBtn.textContent = 'Deleting...';
    }

    try {
      const res = await fetch('api/zero-student-records.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: recordToDeleteId })
      });

      if (!res.ok) {
        // Fallback endpoint
        await fetch('./api/zero-student-records', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'delete', id: recordToDeleteId })
        });
      }

      // Remove locally from state
      records = records.filter(r => String(r.id) !== String(recordToDeleteId));
      applyFilters();
      showToast('Incident record deleted from master registry.');
    } catch (err) {
      console.error('Delete error:', err);
      // Still remove locally to give user instant feedback
      records = records.filter(r => String(r.id) !== String(recordToDeleteId));
      applyFilters();
      showToast('Record deleted locally.', 'info');
    } finally {
      if (confirmDeleteBtn) {
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.textContent = 'Delete Record';
      }
      closeDeleteModal();
    }
  }

  // --- EXPORT TO EXCEL VIA SHEETJS ---
  function exportToExcel() {
    if (typeof XLSX === 'undefined') {
      showToast('Excel exporter library loading, please retry in a second.', 'error');
      return;
    }

    if (filteredRecords.length === 0) {
      showToast('No records available to export.', 'error');
      return;
    }

    const exportRows = filteredRecords.map((r, i) => ({
      'Sr No': i + 1,
      'Date': r.dateFormatted || r.date || '',
      'Time In': r.timeInFormatted || r.timeIn || '',
      'Time Out': r.timeOutFormatted || r.timeOut || '',
      'Department': r.department || '',
      'Semester': r.semester || '',
      'Branch': r.branch || '',
      'Room': r.room || '',
      'Subject': r.subject || '',
      'Faculty Initials': r.facultyInitials || '',
      'Faculty Name': r.facultyName || '',
      'Faculty Email': r.facultyEmail || '',
      'No. of Students': r.students || '0',
      'Remarks': r.remarks || '',
      'CC Emails': r.cc || '',
      'Created At': r.createdAt || ''
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportRows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Zero_Student_Logs');

    // Auto-size columns
    const colWidths = [
      { wch: 6 },  // Sr No
      { wch: 14 }, // Date
      { wch: 12 }, // Time In
      { wch: 12 }, // Time Out
      { wch: 22 }, // Department
      { wch: 10 }, // Semester
      { wch: 24 }, // Branch
      { wch: 10 }, // Room
      { wch: 10 }, // Subject
      { wch: 16 }, // Faculty Initials
      { wch: 24 }, // Faculty Name
      { wch: 24 }, // Faculty Email
      { wch: 14 }, // No. of Students
      { wch: 30 }, // Remarks
      { wch: 40 }, // CC Emails
      { wch: 24 }  // Created At
    ];
    worksheet['!cols'] = colWidths;

    const dateStr = new Date().toISOString().split('T')[0];
    XLSX.writeFile(workbook, `Zero_Student_Incidents_${dateStr}.xlsx`);
    showToast(`Exported ${exportRows.length} incident records to Excel!`);
  }

  // --- TOAST NOTIFICATION UTILITY (Delegates to Universal Top-Right Bootstrap 5 Toast Engine) ---
  function showToast(message, type = 'success') {
    if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      return window.PortalToast.show(message, type);
    }
    if (typeof window.showPortalToast === 'function') {
      return window.showPortalToast(message, type);
    }
  }

  // --- SANITIZE HTML ---
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
