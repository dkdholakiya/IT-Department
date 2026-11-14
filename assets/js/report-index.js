/**
 * Report Management Directory Script — CE & IT Department
 * Unified Management & Live JSON Registry Viewer
 */

(function () {
  'use strict';

  // --- STATE ---
  let records = Array.isArray(window.initialReportRecords) ? [...window.initialReportRecords] : [];
  let filteredRecords = [];
  let activeDept = 'all';
  let selectedCategory = '';
  let selectedFaculty = '';
  let selectedDate = '';
  let searchQuery = '';
  let currentPage = 1;
  let pageSize = 20;
  let recordToDeleteId = null;

  // --- DOM ELEMENTS ---
  const tableBody = document.getElementById('reportTableBody');
  const tableInfoText = document.getElementById('tableInfoText');
  const paginationControls = document.getElementById('paginationControls');
  const pageSizeSelect = document.getElementById('pageSizeSelect');

  const kpiTotal = document.getElementById('kpiTotal');
  const kpiIt = document.getElementById('kpiIt');
  const kpiCe = document.getElementById('kpiCe');
  const kpiMy = document.getElementById('kpiMy');
  const totalLogsBadge = document.getElementById('totalLogsBadge');

  // Upper Side Search
  const reportSearchInput = document.getElementById('reportSearchInput');
  const clearSearchBtn = document.getElementById('clearSearchBtn');

  // Side Canvas Drawer Elements
  const activeFilterBadge = document.getElementById('activeFilterBadge');
  const offcanvasActiveBadge = document.getElementById('offcanvasActiveBadge');
  const activeDeptText = document.getElementById('activeDeptText');
  const deptFilterAll = document.getElementById('deptFilterAll');
  const deptFilterIt = document.getElementById('deptFilterIt');
  const deptFilterCe = document.getElementById('deptFilterCe');
  const categoryFilterSelect = document.getElementById('categoryFilterSelect');
  const facultyFilterSelect = document.getElementById('facultyFilterSelect');
  const dateFilterInput = document.getElementById('dateFilterInput');
  const resetFiltersBtn = document.getElementById('resetFiltersBtn');
  const applyFiltersBtn = document.getElementById('applyFiltersBtn');

  // Action buttons
  const refreshRecordsBtn = document.getElementById('refreshRecordsBtn');
  const exportExcelBtn = document.getElementById('exportExcelBtn');

  // Modals
  const viewReportModal = document.getElementById('viewReportModal');
  const closeDetailModalBtn = document.getElementById('closeDetailModalBtn');
  const closeDetailModalBottomBtn = document.getElementById('closeDetailModalBottomBtn');
  const detailModalBody = document.getElementById('detailModalBody');
  const modalReportId = document.getElementById('modalReportId');

  const deleteReportModal = document.getElementById('deleteReportModal');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  const deleteTargetTitle = document.getElementById('deleteTargetTitle');

  // --- INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    // Standard non-admin faculty default filter to self
    if (!window.canViewAllFaculty && window.loggedInUser && window.loggedInUser.email) {
      selectedFaculty = (window.loggedInUser.email || '').toLowerCase().trim();
    }

    initFlatpickrDateFilter();
    populateFacultyFilter();
    setupEventListeners();
    applyFilters();

    // If initial records was empty, fetch from API
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

    if (!window.canViewAllFaculty && window.loggedInUser) {
      const u = window.loggedInUser;
      const opt = document.createElement('option');
      opt.value = (u.email || '').toLowerCase().trim();
      opt.textContent = `${u.name || 'Faculty Member'} (${u.initials || 'ME'})`;
      opt.selected = true;
      facultyFilterSelect.appendChild(opt);
      facultyFilterSelect.disabled = true;
      facultyFilterSelect.classList.add('opacity-75', 'cursor-not-allowed');
      return;
    }

    // Admin: Populate all faculty from shared facultyData or records
    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = 'All Faculty Members';
    facultyFilterSelect.appendChild(defaultOpt);

    const facultyMap = new Map();
    if (typeof facultyData !== 'undefined' && Array.isArray(facultyData)) {
      facultyData.forEach(f => {
        if (f.email && f.name) {
          facultyMap.set(f.email.toLowerCase().trim(), f.name + (f.initials ? ` (${f.initials})` : ''));
        }
      });
    }

    // Also include any faculty present in existing records
    records.forEach(r => {
      const email = (r.faculty?.email || r.facultyEmail || '').toLowerCase().trim();
      const name = r.faculty?.name || r.facultySearch || r.facultyName || '';
      if (email && name && !facultyMap.has(email)) {
        facultyMap.set(email, name);
      }
    });

    facultyMap.forEach((name, email) => {
      const opt = document.createElement('option');
      opt.value = email;
      opt.textContent = name;
      facultyFilterSelect.appendChild(opt);
    });
  }

  // --- EVENT LISTENERS ---
  function setupEventListeners() {
    // Upper search input
    if (reportSearchInput) {
      reportSearchInput.addEventListener('input', (e) => {
        searchQuery = (e.target.value || '').toLowerCase().trim();
        if (clearSearchBtn) {
          if (searchQuery.length > 0) {
            clearSearchBtn.classList.remove('hidden');
          } else {
            clearSearchBtn.classList.add('hidden');
          }
        }
        currentPage = 1;
        applyFilters();
      });
    }

    if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', () => {
        if (reportSearchInput) {
          reportSearchInput.value = '';
          searchQuery = '';
        }
        clearSearchBtn.classList.add('hidden');
        currentPage = 1;
        applyFilters();
      });
    }

    // Dept filters in side canvas
    if (deptFilterAll) {
      deptFilterAll.addEventListener('click', () => {
        setDeptFilter('all');
      });
    }
    if (deptFilterIt) {
      deptFilterIt.addEventListener('click', () => {
        setDeptFilter('Information Technology');
      });
    }
    if (deptFilterCe) {
      deptFilterCe.addEventListener('click', () => {
        setDeptFilter('Computer Engineering');
      });
    }

    // Category filter in side canvas
    if (categoryFilterSelect) {
      categoryFilterSelect.addEventListener('change', (e) => {
        selectedCategory = e.target.value;
        currentPage = 1;
        applyFilters();
      });
    }

    // Faculty filter in side canvas
    if (facultyFilterSelect) {
      facultyFilterSelect.addEventListener('change', (e) => {
        selectedFaculty = (e.target.value || '').toLowerCase().trim();
        currentPage = 1;
        applyFilters();
      });
    }

    // Reset filters button (Drawer Footer)
    if (resetFiltersBtn) {
      resetFiltersBtn.addEventListener('click', () => {
        activeDept = 'all';
        selectedCategory = '';
        selectedDate = '';
        searchQuery = '';
        if (reportSearchInput) {
          reportSearchInput.value = '';
          if (clearSearchBtn) clearSearchBtn.classList.add('hidden');
        }
        if (categoryFilterSelect) categoryFilterSelect.value = '';
        if (dateFilterInput && dateFilterInput._flatpickr) {
          dateFilterInput._flatpickr.clear();
        }
        if (window.canViewAllFaculty) {
          selectedFaculty = '';
          if (facultyFilterSelect) facultyFilterSelect.value = '';
        }
        updateDeptButtonUI();
        currentPage = 1;
        applyFilters();
      });
    }

    // Apply filters button in drawer
    if (applyFiltersBtn) {
      applyFiltersBtn.addEventListener('click', () => {
        currentPage = 1;
        applyFilters();
      });
    }

    // Refresh records
    if (refreshRecordsBtn) {
      refreshRecordsBtn.addEventListener('click', () => {
        fetchRecordsFromServer();
      });
    }

    // Export Excel
    if (exportExcelBtn) {
      exportExcelBtn.addEventListener('click', exportToExcel);
    }

    // Page size select
    if (pageSizeSelect) {
      pageSizeSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        pageSize = (val === 'all') ? 999999 : (parseInt(val, 10) || 20);
        currentPage = 1;
        renderTable();
      });
    }

    // Detail Modal close buttons
    if (closeDetailModalBtn) closeDetailModalBtn.addEventListener('click', closeDetailModal);
    if (closeDetailModalBottomBtn) closeDetailModalBottomBtn.addEventListener('click', closeDetailModal);
    if (viewReportModal) {
      viewReportModal.addEventListener('click', (e) => {
        if (e.target === viewReportModal) closeDetailModal();
      });
    }

    // Delete Modal buttons
    if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', executeDeleteRecord);
    if (deleteReportModal) {
      deleteReportModal.addEventListener('click', (e) => {
        if (e.target === deleteReportModal) closeDeleteModal();
      });
    }
  }

  function setDeptFilter(dept) {
    activeDept = dept;
    updateDeptButtonUI();
    currentPage = 1;
    applyFilters();
  }

  function updateDeptButtonUI() {
    [deptFilterAll, deptFilterIt, deptFilterCe].forEach(btn => {
      if (btn) btn.classList.remove('active');
    });

    if (activeDept === 'Information Technology' && deptFilterIt) {
      deptFilterIt.classList.add('active');
      if (activeDeptText) activeDeptText.textContent = 'Information Technology';
    } else if (activeDept === 'Computer Engineering' && deptFilterCe) {
      deptFilterCe.classList.add('active');
      if (activeDeptText) activeDeptText.textContent = 'Computer Engineering';
    } else if (deptFilterAll) {
      deptFilterAll.classList.add('active');
      if (activeDeptText) activeDeptText.textContent = 'All Departments';
    }
  }

  // --- FILTERING & RECALCULATION ---
  function applyFilters() {
    const hasActiveFilters = (activeDept !== 'all') ||
      (selectedCategory !== '') ||
      (window.canViewAllFaculty && selectedFaculty !== '') ||
      (selectedDate !== '');

    if (activeFilterBadge) {
      if (hasActiveFilters) {
        activeFilterBadge.classList.remove('hidden');
      } else {
        activeFilterBadge.classList.add('hidden');
      }
    }

    if (offcanvasActiveBadge) {
      if (hasActiveFilters) {
        offcanvasActiveBadge.classList.remove('hidden');
      } else {
        offcanvasActiveBadge.classList.add('hidden');
      }
    }

    filteredRecords = records.filter(r => {
      // 1. Department Filter
      const dept = r.facultyDept || r.faculty?.department || '';
      if (activeDept !== 'all') {
        if (!dept.toLowerCase().includes(activeDept.toLowerCase())) {
          return false;
        }
      }

      // 2. Category Filter
      if (selectedCategory !== '') {
        const type = (r.reportType || '').toLowerCase();
        if (!type.includes(selectedCategory.toLowerCase())) {
          return false;
        }
      }

      // 3. Faculty Filter
      if (selectedFaculty !== '') {
        const email = (r.faculty?.email || r.facultyEmail || r.createdBy?.email || '').toLowerCase().trim();
        if (email !== selectedFaculty) {
          return false;
        }
      }

      // 4. Date Filter
      if (selectedDate !== '') {
        const actDate = (r.activityDate || '').trim();
        if (actDate !== selectedDate) {
          return false;
        }
      }

      // 5. Keyword Search
      if (searchQuery !== '') {
        const id = (r.id || '').toLowerCase();
        const title = (r.reportTitle || '').toLowerCase();
        const facName = (r.faculty?.name || r.facultySearch || r.facultyName || '').toLowerCase();
        const venue = (r.venue || '').toLowerCase();
        const obj = (r.briefObjective || '').toLowerCase();
        const cat = (r.reportTypeLabel || r.reportType || '').toLowerCase();
        const placeAct = (r.placementActType || '').toLowerCase();
        const plmAct = (r.plmActivity || r.customPlmActivity || r.rlmActivity || '').toLowerCase();

        const match = id.includes(searchQuery) ||
          title.includes(searchQuery) ||
          facName.includes(searchQuery) ||
          venue.includes(searchQuery) ||
          obj.includes(searchQuery) ||
          cat.includes(searchQuery) ||
          placeAct.includes(searchQuery) ||
          plmAct.includes(searchQuery);

        if (!match) return false;
      }

      return true;
    });

    updateKPICards();
    renderTable();
  }

  // --- UPDATE KPI CARDS ---
  function updateKPICards() {
    const total = records.length;
    let itCount = 0;
    let ceCount = 0;
    let myCount = 0;

    const myEmail = (window.loggedInUser?.email || '').toLowerCase().trim();

    records.forEach(r => {
      const dept = (r.facultyDept || r.faculty?.department || '').toLowerCase();
      if (dept.includes('information') || dept.includes('it')) itCount++;
      if (dept.includes('computer') || dept.includes('ce')) ceCount++;

      const rEmail = (r.faculty?.email || r.facultyEmail || r.createdBy?.email || '').toLowerCase().trim();
      if (myEmail && rEmail === myEmail) myCount++;
    });

    if (kpiTotal) kpiTotal.textContent = total;
    if (kpiIt) kpiIt.textContent = itCount;
    if (kpiCe) kpiCe.textContent = ceCount;
    if (kpiMy) kpiMy.textContent = myCount;
    if (totalLogsBadge) totalLogsBadge.textContent = `${filteredRecords.length} of ${total} records`;
  }

  // --- TABLE RENDERING & PAGINATION ---
  function renderTable() {
    if (!tableBody) return;
    tableBody.innerHTML = '';

    const total = filteredRecords.length;
    const totalPages = Math.ceil(total / pageSize) || 1;
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = (currentPage - 1) * pageSize;
    const endIdx = Math.min(startIdx + pageSize, total);
    const pageRecords = filteredRecords.slice(startIdx, endIdx);

    if (pageRecords.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center py-12">
            <div class="flex flex-col items-center justify-center gap-2">
              <div class="w-12 h-12 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-center text-[var(--text-muted)]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/>
                </svg>
              </div>
              <div class="text-xs font-bold text-[var(--text-primary)]">No Report Records Found</div>
              <div class="text-[11px] text-[var(--text-muted)]">Try adjusting your filters or search query, or click "+ Create Report" to submit one.</div>
            </div>
          </td>
        </tr>
      `;
      if (tableInfoText) tableInfoText.textContent = `Showing 0 of 0 entries`;
      renderPagination(0, 1);
      return;
    }

    pageRecords.forEach((r, idx) => {
      const globalIndex = startIdx + idx + 1;
      const tr = document.createElement('tr');
      tr.className = 'border-b border-black/5 dark:border-white/5 hover:bg-black/5 dark:hover:bg-white/5 transition';

      const facultyName = r.faculty?.name || r.facultySearch || r.facultyName || 'Faculty Member';
      const facultyDept = r.facultyDept || r.faculty?.department || 'IT';
      const isCE = facultyDept.toLowerCase().includes('computer') || facultyDept.toLowerCase().includes('ce');

      const isMyRecord = checkOwnership(r);
      const canEdit = window.canEditReport && (window.canViewAllFaculty || isMyRecord);
      const canDelete = window.canDeleteReport && (window.canViewAllFaculty || isMyRecord);

      const typeLabel = r.reportTypeLabel || formatReportType(r.reportType);
      const dateFormatted = formatDateDisplay(r.activityDate);
      const timeFormatted = formatTimeRange(r.startTime, r.endTime);

      tr.innerHTML = `
        <!-- Index # -->
        <td class="py-2.5 px-3 text-center text-[var(--text-muted)] font-bold text-xs select-none w-12">
          ${globalIndex}
        </td>

        <!-- ID -->
        <td class="py-2.5 px-3 whitespace-nowrap">
          <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-black/5 dark:bg-white/10 text-[10.5px] font-mono font-semibold text-[var(--text-primary)]">
            ${escapeHtml(r.id || '—')}
          </span>
        </td>

        <!-- Report Title & Objective -->
        <td class="py-2.5 px-3 max-w-[280px]">
          <div class="font-bold text-xs text-[var(--text-primary)] truncate" title="${escapeHtml(r.reportTitle || '')}">
            ${escapeHtml(r.reportTitle || 'Untitled Activity')}
          </div>
          <div class="text-[10px] text-[var(--text-muted)] truncate mt-0.5" title="${escapeHtml(r.briefObjective || '')}">
            ${escapeHtml(r.briefObjective || 'No objective provided')}
          </div>
        </td>

        <!-- Category & Dept -->
        <td class="py-2.5 px-3 whitespace-nowrap">
          <div class="inline-flex items-center gap-1.5">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
              ${escapeHtml(typeLabel)}
            </span>
            <span class="px-1.5 py-0.5 rounded-md text-[9.5px] font-bold ${isCE ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400'}">
              ${isCE ? 'CE' : 'IT'}
            </span>
          </div>
        </td>

        <!-- Faculty In-Charge -->
        <td class="py-2.5 px-3 whitespace-nowrap">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] font-bold text-[10px] flex items-center justify-center shrink-0">
              ${escapeHtml((r.faculty?.empId || r.facultyEmpId || facultyName.slice(0, 2)).toUpperCase())}
            </div>
            <div class="min-w-0">
              <div class="font-semibold text-xs text-[var(--text-primary)] truncate">${escapeHtml(facultyName)}</div>
              <div class="text-[10px] text-[var(--text-muted)] truncate">${escapeHtml(r.faculty?.designation || r.facultyDesignation || 'Faculty')}</div>
            </div>
          </div>
        </td>

        <!-- Date & Timing -->
        <td class="py-2.5 px-3 whitespace-nowrap">
          <div class="font-semibold text-xs text-[var(--text-primary)]">${escapeHtml(dateFormatted)}</div>
          <div class="text-[10px] text-[var(--text-muted)]">${escapeHtml(timeFormatted)}</div>
        </td>

        <!-- Status -->
        <td class="py-2.5 px-3 whitespace-nowrap">
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/15 text-green-600 dark:text-green-400">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            ${escapeHtml(r.status || 'Submitted')}
          </span>
        </td>

        <!-- Actions -->
        <td class="py-2.5 px-3 text-right whitespace-nowrap">
          <div class="inline-flex items-center gap-1">
            <!-- View Detail Button -->
            <button
              type="button"
              class="w-7 h-7 rounded-lg bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 text-[var(--text-primary)] flex items-center justify-center transition cursor-pointer"
              title="View Report Details"
              onclick="window.openReportDetail('${r.id}')"
            >
              <i class="bi bi-eye"></i>
            </button>

            <!-- Edit Button -->
            ${canEdit ? `
              <a
                href="${(window.baseUrl || '')}report-create?id=${encodeURIComponent(r.id)}"
                class="w-7 h-7 rounded-lg bg-black/5 dark:bg-white/5 hover:bg-black/10 dark:hover:bg-white/10 text-[var(--text-primary)] flex items-center justify-center transition cursor-pointer text-decoration-none"
                title="Edit Report"
              >
                <i class="bi bi-pencil"></i>
              </a>
            ` : ''}

            <!-- Delete Button -->
            ${canDelete ? `
              <button
                type="button"
                class="w-7 h-7 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 flex items-center justify-center transition cursor-pointer"
                title="Delete Report"
                onclick="window.promptDeleteReport('${r.id}', '${escapeHtml(r.reportTitle || '')}')"
              >
                <i class="bi bi-trash3"></i>
              </button>
            ` : ''}
          </div>
        </td>
      `;

      tableBody.appendChild(tr);
    });

    if (tableInfoText) {
      tableInfoText.textContent = `Showing ${startIdx + 1} to ${endIdx} of ${total} entries`;
    }

    renderPagination(totalPages, currentPage);
  }

  // --- PAGINATION CONTROLS (Identical to Zero Student / Faculty Style) ---
  function renderPagination(totalPages, current) {
    if (!paginationControls) return;
    paginationControls.innerHTML = '';

    if (totalPages < 1) totalPages = 1;

    let html = `
      <button
        type="button"
        class="zs-page-btn"
        ${current <= 1 ? 'disabled' : ''}
        onclick="window.goToReportPage(${current - 1})"
        title="Previous Page"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
      </button>
    `;

    for (let p = 1; p <= totalPages; p++) {
      if (totalPages > 7) {
        if (p > 2 && p < totalPages - 1 && Math.abs(p - current) > 1) {
          if (p === 3 || p === totalPages - 2) {
            html += `<span class="px-1 text-xs text-[var(--text-muted)] select-none">...</span>`;
          }
          continue;
        }
      }

      html += `
        <button
          type="button"
          class="zs-page-btn ${p === current ? 'active' : ''}"
          onclick="window.goToReportPage(${p})"
        >
          ${p}
        </button>
      `;
    }

    html += `
      <button
        type="button"
        class="zs-page-btn"
        ${current >= totalPages ? 'disabled' : ''}
        onclick="window.goToReportPage(${current + 1})"
        title="Next Page"
      >
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </button>
    `;

    paginationControls.innerHTML = html;
  }

  // Global page jump helper
  window.goToReportPage = function (p) {
    const total = filteredRecords.length;
    const totalPages = pageSize >= 999999 ? 1 : (Math.ceil(total / pageSize) || 1);
    if (p >= 1 && p <= totalPages) {
      currentPage = p;
      renderTable();
    }
  };

  // --- FETCH DATA FROM SERVER ---
  function fetchRecordsFromServer() {
    if (refreshRecordsBtn) {
      refreshRecordsBtn.classList.add('animate-spin');
    }

    fetch((window.baseUrl || '') + 'api/report-records')
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.records)) {
          records = data.records;
          populateFacultyFilter();
          applyFilters();
        }
      })
      .catch(err => {
        console.error('Failed to fetch report records:', err);
      })
      .finally(() => {
        if (refreshRecordsBtn) {
          refreshRecordsBtn.classList.remove('animate-spin');
        }
      });
  }

  // --- DETAIL MODAL HELPERS ---
  const programmeDisplayNames = {
    progDiploma: 'Diploma',
    progDiplomaPremium: 'Diploma (Premium)',
    progBTech: 'B.Tech',
    progBTechPLM: 'B.Tech (PLM)',
    progMTech: 'M.Tech',
    progBCA: 'BCA',
    progMCA: 'MCA',
    progOther: 'Other'
  };

  function formatProgrammeBadges(programmes) {
    if (!programmes) return '<span class="text-[var(--text-muted)] font-normal">—</span>';
    const list = Array.isArray(programmes) ? programmes : [programmes];
    if (list.length === 0) return '<span class="text-[var(--text-muted)] font-normal">—</span>';
    return list.map(p => {
      const name = programmeDisplayNames[p] || p;
      return `<span class="inline-block px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">${escapeHtml(name)}</span>`;
    }).join(' ');
  }

  function formatDeadlineDisplay(val) {
    if (!val) return '—';
    try {
      const d = new Date(val);
      if (isNaN(d.getTime())) return val;
      return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' +
             d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    } catch (e) {
      return val;
    }
  }

  function renderYesNoBadge(val, yesText = 'Yes', noText = 'No') {
    const isYes = String(val).trim().toLowerCase() === 'yes';
    if (isYes) {
      return `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-green-500/15 text-green-600 dark:text-green-400 border border-green-500/20"><i class="bi bi-check-circle-fill text-[10px]"></i> ${escapeHtml(yesText)}</span>`;
    }
    return `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-black/5 dark:bg-white/10 text-[var(--text-muted)] border border-black/5 dark:border-white/10"><i class="bi bi-dash-circle text-[10px]"></i> ${escapeHtml(noText)}</span>`;
  }

  function renderDynamicActivityDetails(record) {
    const type = record.reportType || '';
    const details = [];

    // Training & Placement (RLM / PLM)
    if (type === 'training_placement' || record.placementActType) {
      if (record.placementActType) {
        let catLabel = record.placementActType;
        if (record.placementActType === 'RLM') catLabel = 'RLM (Recruitment Level Module)';
        else if (record.placementActType === 'PLM') catLabel = 'PLM (Placement Level Module)';
        else if (record.placementActType === 'PREMIUM') catLabel = 'PREMIUM (Placement Level Module)';
        details.push({ 
          label: 'Placement Category', 
          val: catLabel 
        });
      }
      const act = record.placementActType === 'RLM' 
        ? (record.rlmActivity || record.customRlmActivity) 
        : (record.plmActivity || record.customPlmActivity);
      if (act) details.push({ label: 'Selected Activity Track', val: act });
      if (record.placementSubType || record.customPlacementSubType) {
        details.push({ label: 'Activity Sub-Type', val: record.customPlacementSubType || record.placementSubType });
      }
      if (record.placementSpeaker) details.push({ label: 'Speaker / Resource Person Details', val: record.placementSpeaker });
      if (record.placementOutcomes) details.push({ label: 'Key Takeaways & Outcomes', val: record.placementOutcomes });
    }

    // Departmental Activities
    if (type === 'departmental') {
      const sub = record.customDepartmentalSubType || record.departmentalSubType;
      if (sub) details.push({ label: 'Departmental Sub-Type', val: sub });
      if (record.departmentalSpeaker) details.push({ label: 'Speaker / Resource Person Details', val: record.departmentalSpeaker });
      if (record.departmentalOutcomes) details.push({ label: 'Key Takeaways & Outcomes', val: record.departmentalOutcomes });
    }

    // Startup & Innovation
    if (type === 'startup_innovation') {
      if (record.startupName) details.push({ label: 'Startup / Project Name', val: record.startupName });
      if (record.startupStage) details.push({ label: 'Stage of Development', val: record.startupStage });
      if (record.startupTeam) details.push({ label: 'Founding Team Members', val: record.startupTeam });
      if (record.startupProblem) details.push({ label: 'Problem Statement & Proposed Solution', val: record.startupProblem });
    }

    // Research & Publications
    if (type === 'research_publications') {
      if (record.researchPaperTitle) details.push({ label: 'Research Paper Title', val: record.researchPaperTitle });
      if (record.researchAuthors) details.push({ label: 'Author(s)', val: record.researchAuthors });
      if (record.researchJournal) details.push({ label: 'Journal / Conference Details', val: record.researchJournal });
      if (record.researchPubDate) details.push({ label: 'Publication Date', val: record.researchPubDate });
    }

    // International Collaborations
    if (type === 'international_collab') {
      if (record.intCollaboratingOrg) details.push({ label: 'Collaborating Organization / University', val: record.intCollaboratingOrg });
      const collabType = record.customIntCollaborationType || record.intCollaborationType;
      if (collabType) details.push({ label: 'Collaboration Type', val: collabType });
      if (record.intDescription) details.push({ label: 'Scope & Key Deliverables', val: record.intDescription });
    }

    // Central University Events
    if (type === 'central_events') {
      const cat = record.customCentralCategory || record.centralCategory;
      if (cat) details.push({ label: 'Central Event Category', val: cat });
      if (record.centralHighlights) details.push({ label: 'Key Highlights & Department Role', val: record.centralHighlights });
    }

    if (details.length === 0 && record.activityDetailsSummary) {
      return `
        <div class="text-xs text-[var(--text-primary)] leading-relaxed bg-black/5 dark:bg-white/5 p-3 rounded-xl border border-black/5 dark:border-white/10">
          ${escapeHtml(record.activityDetailsSummary)}
        </div>
      `;
    }

    if (details.length === 0) {
      return `<div class="text-xs text-[var(--text-muted)] italic py-2">Standard Academic Activity (No specialized dynamic sub-fields recorded)</div>`;
    }

    return `
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
        ${details.map(d => `
          <div class="p-2.5 rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 ${d.label.includes('Outcomes') || d.label.includes('Problem') || d.label.includes('Scope') || d.label.includes('Highlights') || d.label.includes('Paper Title') ? 'sm:col-span-2' : ''}">
            <span class="text-[9.5px] text-[var(--text-muted)] block font-extrabold uppercase tracking-wider">${escapeHtml(d.label)}:</span>
            <span class="font-bold text-[var(--text-primary)] mt-0.5 block leading-relaxed">${escapeHtml(d.val)}</span>
          </div>
        `).join('')}
      </div>
    `;
  }

  // --- DETAIL MODAL ---
  window.openReportDetail = function (id) {
    const record = records.find(r => r.id === id);
    if (!record || !detailModalBody) return;

    if (modalReportId) modalReportId.textContent = `#${record.id}`;

    const facultyName = record.faculty?.name || record.facultySearch || record.facultyName || 'Faculty Member';
    const facultyEmail = record.faculty?.email || record.facultyEmail || '—';
    const facultyEmpId = record.faculty?.empId || record.facultyEmpId || '—';
    const facultyDesg = record.faculty?.designation || record.facultyDesignation || '—';
    const facultyDept = record.facultyDept || record.faculty?.department || 'Information Technology';
    const facultyPhone = record.faculty?.phone || record.facultyPhone || '—';

    const semList = record.semester || (Array.isArray(record.semesters) ? record.semesters.join(', ') : 'None');
    const ccList = Array.isArray(record.ccEmails) && record.ccEmails.length > 0 ? record.ccEmails.join(', ') : 'None';

    const photoMethodLabel = record.photoMethod === 'zip' 
      ? 'ZIP File Upload' 
      : (record.photoMethod === 'email' ? 'Submit via Email Reply' : 'Google Drive Folder Link');

    detailModalBody.innerHTML = `
      <div class="space-y-4">
        <!-- 1. Header Snapshot Banner -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
          <div>
            <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-[var(--color-primary)]">Activity Title</span>
            <h3 class="text-sm sm:text-base font-bold text-[var(--text-primary)] mt-0.5 leading-snug">${escapeHtml(record.reportTitle || 'Untitled Activity')}</h3>
            <div class="text-[10.5px] text-[var(--text-muted)] mt-0.5 flex items-center gap-1.5 flex-wrap">
              <span class="font-semibold">${escapeHtml(record.academicYear || '')}</span>
              <span>•</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.reportTypeLabel || formatReportType(record.reportType))}</span>
              ${record.customReportType ? `<span class="italic text-[var(--text-muted)]">(${escapeHtml(record.customReportType)})</span>` : ''}
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-green-500/15 text-green-600 dark:text-green-400 border border-green-500/20">
              <i class="bi bi-check2-circle me-1"></i>${escapeHtml(record.status || 'Submitted')}
            </span>
            <span class="px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20">
              <i class="bi bi-building me-1"></i>${escapeHtml(facultyDept)}
            </span>
          </div>
        </div>

        <!-- Review Deadline Alert Banner (if enabled) -->
        ${(record.enableDeadline && record.deadlineVal) ? `
          <div class="p-3 rounded-2xl bg-gradient-to-r from-amber-500/15 to-orange-500/10 border border-amber-500/30 flex items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <i class="bi bi-alarm-fill text-base"></i>
              </div>
              <div>
                <span class="text-[9.5px] font-extrabold uppercase tracking-wider text-amber-700 dark:text-amber-300 block">Report Review Deadline</span>
                <span class="text-xs font-bold text-[var(--text-primary)]">${escapeHtml(formatDeadlineDisplay(record.deadlineVal))}</span>
              </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/25 text-amber-700 dark:text-amber-300 border border-amber-500/30">Target Deadline</span>
          </div>
        ` : ''}

        <!-- 2. Section 1: Faculty Information -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2.5 flex items-center gap-1.5">
            <i class="bi bi-person-badge text-[var(--color-primary)]"></i>
            <span>Faculty Profile (Requested By)</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Faculty Name:</span>
              <span class="font-bold text-[var(--text-primary)]">${escapeHtml(facultyName)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Employee ID:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(facultyEmpId)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Designation:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(facultyDesg)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Email Address:</span>
              <span class="font-semibold text-[var(--text-primary)] font-mono">${escapeHtml(facultyEmail)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Mobile:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(facultyPhone)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Department:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(facultyDept)}</span>
            </div>
          </div>
        </div>

        <!-- 3. Section 2: Basic Event Logistics Information -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2.5 flex items-center gap-1.5">
            <i class="bi bi-calendar-event text-[var(--color-primary)]"></i>
            <span>Basic Event & Logistics Information</span>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Activity Date:</span>
              <span class="font-bold text-[var(--text-primary)]">${escapeHtml(formatDateDisplay(record.activityDate))}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Timing:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(formatTimeRange(record.startTime, record.endTime))}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Venue / Room:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.venue || '—')}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Participants:</span>
              <span class="font-bold text-[var(--text-primary)]">${escapeHtml(record.participantsCount || '0')} attendees</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Semesters:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(semList)}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Division / Class:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.divisionClass || '—')}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Batch:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.batch || '—')}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Academic Year:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.academicYear || '—')}</span>
            </div>
            <div class="col-span-2">
              <span class="text-[10px] text-[var(--text-muted)] block mb-1">Target Programme(s):</span>
              <div class="flex flex-wrap gap-1">${formatProgrammeBadges(record.programmes)}</div>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block mb-1">Website Publication:</span>
              ${renderYesNoBadge(record.publishWebsite, 'Yes, Publish', 'No')}
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block mb-1">Press Note Required:</span>
              ${renderYesNoBadge(record.pressNote, 'Yes, Required', 'No')}
            </div>
          </div>

          <div class="mt-3 pt-2.5 border-t border-black/5 dark:border-white/5 text-xs">
            <span class="text-[10px] text-[var(--text-muted)] block">Brief Objective:</span>
            <p class="text-[var(--text-primary)] mt-0.5 leading-relaxed bg-black/[0.02] dark:bg-white/[0.02] p-2.5 rounded-xl border border-black/5 dark:border-white/5">${escapeHtml(record.briefObjective || '—')}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-2.5 text-xs">
            ${record.coordinators ? `
              <div class="p-2.5 rounded-xl bg-black/[0.02] dark:bg-white/[0.02] border border-black/5 dark:border-white/5">
                <span class="text-[9.5px] text-[var(--text-muted)] block font-extrabold uppercase tracking-wider">Faculty Coordinator(s):</span>
                <span class="font-semibold text-[var(--text-primary)] mt-0.5 block">${escapeHtml(record.coordinators)}</span>
              </div>
            ` : ''}
            ${record.studentCoordinator ? `
              <div class="p-2.5 rounded-xl bg-black/[0.02] dark:bg-white/[0.02] border border-black/5 dark:border-white/5">
                <span class="text-[9.5px] text-[var(--text-muted)] block font-extrabold uppercase tracking-wider">Student Coordinator(s):</span>
                <span class="font-semibold text-[var(--text-primary)] mt-0.5 block">${escapeHtml(record.studentCoordinator)}</span>
              </div>
            ` : ''}
          </div>
        </div>

        <!-- 4. Section 3: Dynamic Category Specific Details -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2.5 flex items-center justify-between">
            <div class="flex items-center gap-1.5">
              <i class="bi bi-puzzle text-[var(--color-primary)]"></i>
              <span>Category Specific Details</span>
            </div>
            <span class="text-[10.5px] font-bold text-[var(--color-primary)]">${escapeHtml(record.reportTypeLabel || formatReportType(record.reportType))}</span>
          </div>
          ${renderDynamicActivityDetails(record)}
        </div>

        <!-- 5. Section 4: Media, Photos & Attachments -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2.5 flex items-center gap-1.5">
            <i class="bi bi-camera text-[var(--color-primary)]"></i>
            <span>Activity Photos & Supporting Media</span>
          </div>
          <div class="text-xs space-y-2">
            <div class="flex items-center gap-2">
              <span class="text-[10px] text-[var(--text-muted)]">Submission Method:</span>
              <span class="font-bold text-[var(--text-primary)]">${escapeHtml(photoMethodLabel)}</span>
            </div>
            ${record.driveLink ? `
              <div class="p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                  <i class="bi bi-google text-blue-600 dark:text-blue-400"></i>
                  <span class="font-semibold text-[var(--text-primary)] truncate">${escapeHtml(record.driveLink)}</span>
                </div>
                <a href="${escapeHtml(record.driveLink)}" target="_blank" rel="noopener noreferrer" class="px-3 py-1 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-bold text-[11px] shrink-0 inline-flex items-center gap-1">
                  Open Drive Folder <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                </a>
              </div>
            ` : ''}
            ${record.largeZipCheckbox ? `
              <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-sm shrink-0"></i>
                <span>Large ZIP file (>5 MB) selected. Photos will be attached directly in the email reply to administration.</span>
              </div>
            ` : ''}
            ${(!record.driveLink && !record.largeZipCheckbox) ? `
              <div class="text-[11px] text-[var(--text-muted)] italic">
                Photos handled via standard email attachment submission.
              </div>
            ` : ''}
          </div>
        </div>

        <!-- 6. Section 5: Email Distribution & Submission Audit -->
        <div class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10">
          <div class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] mb-2.5 flex items-center gap-1.5">
            <i class="bi bi-envelope-paper text-[var(--color-primary)]"></i>
            <span>Email Notification & Submission Audit</span>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Submission Timestamp:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.createdAt ? new Date(record.createdAt).toLocaleString() : '—')}</span>
            </div>
            <div>
              <span class="text-[10px] text-[var(--text-muted)] block">Submitted By User:</span>
              <span class="font-semibold text-[var(--text-primary)]">${escapeHtml(record.createdBy?.name || facultyName)} (${escapeHtml(record.createdBy?.email || facultyEmail)})</span>
            </div>
            ${record.refSubject ? `
              <div class="sm:col-span-2">
                <span class="text-[10px] text-[var(--text-muted)] block">Reference Subject:</span>
                <span class="font-semibold text-[var(--text-primary)] font-mono text-[11px]">${escapeHtml(record.refSubject)}</span>
              </div>
            ` : ''}
            <div class="sm:col-span-2">
              <span class="text-[10px] text-[var(--text-muted)] block">CC Email Recipients:</span>
              <span class="font-semibold text-[var(--text-primary)] font-mono text-[11px]">${escapeHtml(ccList)}</span>
            </div>
          </div>

          ${record.refMessage ? `
            <div class="mt-3">
              <details class="group rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 overflow-hidden">
                <summary class="px-3 py-2 text-xs font-bold text-[var(--text-primary)] cursor-pointer select-none flex items-center justify-between hover:bg-black/5 dark:hover:bg-white/5">
                  <span class="flex items-center gap-1.5"><i class="bi bi-file-earmark-text"></i> Full Formatted Report Text Preview</span>
                  <i class="bi bi-chevron-down transition-transform group-open:rotate-180 text-xs text-[var(--text-muted)]"></i>
                </summary>
                <div class="p-3 border-t border-black/5 dark:border-white/10 bg-black/[0.02] dark:bg-white/[0.02]">
                  <pre class="text-[10.5px] font-mono leading-relaxed text-[var(--text-primary)] whitespace-pre-wrap max-h-64 overflow-y-auto">${escapeHtml(record.refMessage)}</pre>
                </div>
              </details>
            </div>
          ` : ''}
        </div>
      </div>
    `;

    if (viewReportModal) {
      viewReportModal.classList.remove('hidden');
      viewReportModal.classList.add('flex');
    }
  };

  function closeDetailModal() {
    if (viewReportModal) {
      viewReportModal.classList.add('hidden');
      viewReportModal.classList.remove('flex');
    }
  }

  // --- DELETE MODAL & ACTION ---
  window.promptDeleteReport = function (id, title) {
    recordToDeleteId = id;
    if (deleteTargetTitle) {
      deleteTargetTitle.textContent = title ? `"${title}" (#${id})` : `#${id}`;
    }
    if (deleteReportModal) {
      deleteReportModal.classList.remove('hidden');
      deleteReportModal.classList.add('flex');
    }
  };

  function closeDeleteModal() {
    recordToDeleteId = null;
    if (deleteReportModal) {
      deleteReportModal.classList.add('hidden');
      deleteReportModal.classList.remove('flex');
    }
  }

  async function executeDeleteRecord() {
    if (!recordToDeleteId) return;

    if (confirmDeleteBtn) {
      confirmDeleteBtn.disabled = true;
      confirmDeleteBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Deleting...`;
    }

    try {
      // Primary: standard REST DELETE
      let res = await fetch((window.baseUrl || '') + `api/report-records?id=${encodeURIComponent(recordToDeleteId)}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      // Secondary Fallback: If server or firewall blocked DELETE method, fallback to POST with action: 'delete'
      if (!res.ok && (res.status === 403 || res.status === 405)) {
        res = await fetch((window.baseUrl || '') + 'api/report-records', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ action: 'delete', id: recordToDeleteId })
        });
      }

      const contentType = res.headers.get('content-type') || '';
      let data = {};
      if (contentType.includes('application/json')) {
        data = await res.json();
      } else {
        const text = await res.text();
        try { data = JSON.parse(text); } catch (_) { data = { error: text.slice(0, 100) }; }
      }

      if (res.ok && data.success) {
        records = records.filter(r => r.id !== recordToDeleteId);
        closeDeleteModal();
        applyFilters();
      } else {
        alert('Failed to delete report: ' + (data.error || `HTTP ${res.status} Error`));
      }
    } catch (err) {
      console.error('Delete error:', err);
      alert('Network connection error while deleting record.');
    } finally {
      if (confirmDeleteBtn) {
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.textContent = 'Yes, Delete Report';
      }
    }
  }

  // --- EXCEL EXPORT ---
  function exportToExcel() {
    if (typeof XLSX === 'undefined') {
      alert('Excel export library (XLSX) is still loading. Please try again.');
      return;
    }

    const exportRows = filteredRecords.map((r, idx) => ({
      '#': idx + 1,
      'Report ID': r.id || '',
      'Title': r.reportTitle || '',
      'Category': r.reportTypeLabel || r.reportType || '',
      'Department': r.facultyDept || r.faculty?.department || '',
      'Faculty Name': r.faculty?.name || r.facultySearch || r.facultyName || '',
      'Faculty Email': r.faculty?.email || r.facultyEmail || '',
      'Employee ID': r.faculty?.empId || r.facultyEmpId || '',
      'Activity Date': r.activityDate || '',
      'Start Time': r.startTime || '',
      'End Time': r.endTime || '',
      'Venue': r.venue || '',
      'Semesters': r.semester || (Array.isArray(r.semesters) ? r.semesters.join(', ') : ''),
      'Division': r.divisionClass || '',
      'Participants': r.participantsCount || '',
      'Brief Objective': r.briefObjective || '',
      'Drive Link': r.driveLink || '',
      'Status': r.status || 'Submitted',
      'Created At': r.createdAt || ''
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportRows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Workload Reports');

    const fileName = `Workload_Reports_${new Date().toISOString().slice(0, 10)}.xlsx`;
    XLSX.writeFile(workbook, fileName);
  }

  // --- HELPER UTILITIES ---
  function checkOwnership(record) {
    if (!window.loggedInUser) return false;
    const myEmail = (window.loggedInUser.email || '').toLowerCase().trim();
    const myId = (window.loggedInUser.id || '').trim();
    const myInit = (window.loggedInUser.initials || '').toUpperCase().trim();

    const createdByEmail = (record.createdBy?.email || '').toLowerCase().trim();
    const createdById = (record.createdBy?.id || '').trim();
    const createdByInit = (record.createdBy?.initials || '').toUpperCase().trim();

    if (myEmail && createdByEmail === myEmail) return true;
    if (myId && createdById === myId) return true;
    if (myInit && createdByInit === myInit) return true;

    const facEmail = (record.faculty?.email || record.facultyEmail || '').toLowerCase().trim();
    const facEmpId = (record.faculty?.empId || record.facultyEmpId || '').toLowerCase().trim();
    const facInit = (record.faculty?.initials || record.facultyInitials || '').toUpperCase().trim();

    if (myEmail && facEmail === myEmail) return true;
    if (myInit && facInit === myInit) return true;
    if (window.loggedInUser.empId && facEmpId === window.loggedInUser.empId.toLowerCase().trim()) return true;

    return false;
  }

  function formatReportType(type) {
    if (!type) return 'Activity Report';
    return type
      .replace(/_/g, ' ')
      .replace(/\b\w/g, c => c.toUpperCase());
  }

  function formatDateDisplay(d) {
    if (!d) return '—';
    try {
      const parts = d.split('-');
      if (parts.length === 3) {
        const year = parts[0];
        const month = parts[1];
        const day = parts[2];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const mIdx = parseInt(month, 10) - 1;
        if (mIdx >= 0 && mIdx < 12) {
          return `${day}-${months[mIdx]}-${year}`;
        }
      }
    } catch (e) { }
    return d;
  }

  function formatTimeRange(start, end) {
    if (!start && !end) return 'Time not set';
    return `${start || '—'} - ${end || '—'}`;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

})();
