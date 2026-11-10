/**
 * Calendar Controller
 * Department of CE & IT — Academic Calendar & Event Matrix
 *
 * Handles:
 * - 7-column squircle grid calendar renderer
 * - Department switching (Information Technology vs Computer Engineering)
 * - Category filter checkboxes (Public holidays, Reserved holidays, Academic events)
 * - Real-time synchronized search (desktop & responsive mobile navbar via 'portal:search')
 * - Event Inspector (Day Overview card styled to match wireframe)
 * - Month navigation, Jump to Today, Print, and Copy to clipboard
 * - Keyboard shortcuts (ArrowLeft / ArrowRight)
 * - Theme & palette dynamic synchronization
 */

(function () {
  'use strict';

  // --- 1. STATE MANAGEMENT ---
  const today = new Date();
  let currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  let selectedDateStr = `${today.getFullYear()}-${mm}-${dd}`;

  let currentFilterPublic = true;
  let currentFilterReserved = true;
  let currentFilterCustom = true;
  let searchQuery = '';
  let currentDept = localStorage.getItem('portal_dept') === 'CE' ? 'Computer Engineering' : 'Information Technology';

  // --- 2. DOM REFERENCES ---
  const calendarDaysGrid = document.getElementById('calendarDaysGrid');
  const monthYearDisplay = document.getElementById('monthYearDisplay');
  const prevMonthBtn = document.getElementById('prev-month-btn');
  const nextMonthBtn = document.getElementById('next-month-btn');
  const todayJumpBtn = document.getElementById('todayJumpBtn');
  const selectedDateTitle = document.getElementById('selectedDateTitle');
  const selectedDateSubtitle = document.getElementById('selectedDateSubtitle');
  const selectedEventsList = document.getElementById('selectedEventsList');

  const filterPublic = document.getElementById('filter-public');
  const filterReserved = document.getElementById('filter-reserved');
  const filterCustom = document.getElementById('filter-custom');

  const itBtn = document.getElementById('dept-it-btn');
  const ceBtn = document.getElementById('dept-ce-btn');
  const printBtn = document.getElementById('printCalendarBtn');
  const copyBtn = document.getElementById('copyDateInfoBtn');
  const dashboardSearchInput = document.getElementById('dashboardSearchInput');
  const mobileSearchInput = document.getElementById('mobileSearchInput');

  // --- 3. HELPER FUNCTIONS ---
  function formatDateStr(year, month, day) {
    const m = String(month + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    return `${year}-${m}-${d}`;
  }

  function getHumanDate(dateString) {
    if (!dateString) return 'Select a Date';
    const parts = dateString.split('-');
    if (parts.length !== 3) return dateString;
    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    return d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  }

  function showToast(message) {
    if (typeof window.showPortalToast === 'function') {
      window.showPortalToast(message);
    }
  }

  // --- 4. DEPARTMENT THEME SWITCHER ---
  function setDepartment(isCe) {
    currentDept = isCe ? 'Computer Engineering' : 'Information Technology';
    localStorage.setItem('portal_dept', isCe ? 'CE' : 'IT');

    if (itBtn && ceBtn) {
      if (isCe) {
        itBtn.className = 'px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 text-[var(--text-muted)] hover:text-[var(--text-primary)]';
        ceBtn.className = 'px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 bg-[var(--color-primary)] text-white shadow-sm';
        document.title = 'Academic Calendar — CE Department';
      } else {
        ceBtn.className = 'px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 text-[var(--text-muted)] hover:text-[var(--text-primary)]';
        itBtn.className = 'px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold transition-all duration-200 bg-[var(--color-primary)] text-white shadow-sm';
        document.title = 'Academic Calendar — IT Department';
      }
    }

    renderCalendar();
    showEventDetails(selectedDateStr);
  }

  if (itBtn) itBtn.addEventListener('click', () => setDepartment(false));
  if (ceBtn) ceBtn.addEventListener('click', () => setDepartment(true));

  // --- 5. EVENT FILTERING ENGINE ---
  function getEventsForDate(dateStr) {
    const matches = [];
    const data = (typeof calendarData !== 'undefined') ? calendarData : (typeof window !== 'undefined' ? window.calendarData : null);
    if (!data) return matches;

    function matchesDept(dept) {
      if (!dept || dept === 'all' || dept === 'both') return true;
      if (Array.isArray(dept)) {
        return dept.includes(currentDept) || dept.includes('all') || dept.includes('both');
      }
      return dept === currentDept;
    }

    // 1. Public Holidays
    if (currentFilterPublic && data.publicHolidays) {
      data.publicHolidays.forEach((h) => {
        if (h.date === dateStr && matchesDept(h.department)) {
          matches.push({ ...h, type: 'public', badgeName: 'Public Holiday' });
        }
      });
    }

    // 2. Reserved Holidays
    if (currentFilterReserved && data.reservedHolidays) {
      data.reservedHolidays.forEach((h) => {
        if (h.date === dateStr && matchesDept(h.department)) {
          matches.push({ ...h, type: 'reserved', badgeName: 'Reserved Holiday' });
        }
      });
    }

    // 3. Academic & Custom Events
    if (currentFilterCustom) {
      if (data.customEvents) {
        data.customEvents.forEach((e) => {
          if (e.date === dateStr && matchesDept(e.department)) {
            matches.push({ ...e, type: 'custom', badgeName: e.category || 'Event' });
          }
        });
      }

      if (data.recurringEvents) {
        const parts = dateStr.split('-');
        const dateObj = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        const dayOfWeek = dateObj.getDay();

        data.recurringEvents.forEach((r) => {
          if (dayOfWeek === r.dayOfWeek && (!r.startDate || dateStr >= r.startDate) && matchesDept(r.department)) {
            matches.push({
              date: dateStr,
              name: r.name,
              type: 'custom',
              badgeName: r.category || 'Event',
              description: r.description,
              time: r.time,
              department: r.department
            });
          }
        });
      }
    }

    // Real-time search filter
    if (searchQuery.trim().length > 0) {
      const q = searchQuery.toLowerCase();
      return matches.filter((e) =>
        (e.name && e.name.toLowerCase().includes(q)) ||
        (e.description && e.description.toLowerCase().includes(q)) ||
        (e.badgeName && e.badgeName.toLowerCase().includes(q))
      );
    }

    return matches;
  }

  // --- 6. 7-COLUMN SQUIRCLE CALENDAR MATRIX ---
  function renderCalendar() {
    if (!calendarDaysGrid) return;

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];
    if (monthYearDisplay) {
      monthYearDisplay.textContent = `${monthNames[month]} ${year}`;
    }

    calendarDaysGrid.innerHTML = '';

    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();
    const prevMonthTotalDays = new Date(year, month, 0).getDate();

    // 1. Spacing filler cells from previous month
    for (let i = firstDayIndex - 1; i >= 0; i--) {
      const prevDay = prevMonthTotalDays - i;
      const filler = document.createElement('div');
      filler.className = 'cal-day-cell other-month';
      filler.innerHTML = `<span class="cal-date-number text-xs opacity-40">${prevDay}</span><div class="cal-dots-wrap"></div>`;
      calendarDaysGrid.appendChild(filler);
    }

    // 2. Active Days of Current Month
    const todayStr = formatDateStr(today.getFullYear(), today.getMonth(), today.getDate());

    for (let day = 1; day <= totalDays; day++) {
      const cellDateStr = formatDateStr(year, month, day);
      const cellEvents = getEventsForDate(cellDateStr);

      const cell = document.createElement('div');
      cell.className = 'cal-day-cell';
      cell.setAttribute('data-date', cellDateStr);

      if (cellDateStr === todayStr) {
        cell.classList.add('is-today');
      }
      if (cellDateStr === selectedDateStr) {
        cell.classList.add('is-selected');
      }

      // Date Number
      const numSpan = document.createElement('span');
      numSpan.className = 'cal-date-number';
      numSpan.textContent = day;
      cell.appendChild(numSpan);

      // Indicator Dots Container
      const dotsWrap = document.createElement('div');
      dotsWrap.className = 'cal-dots-wrap';

      if (cellEvents.length > 0) {
        const types = new Set(cellEvents.map((e) => e.type));
        types.forEach((t) => {
          const dot = document.createElement('span');
          dot.className = `cal-dot cal-dot-${t}`;
          dotsWrap.appendChild(dot);
        });
      }
      cell.appendChild(dotsWrap);

      // Click listener
      cell.addEventListener('click', () => {
        const prevSelected = calendarDaysGrid.querySelector('.cal-day-cell.is-selected');
        if (prevSelected) prevSelected.classList.remove('is-selected');

        cell.classList.add('is-selected');
        selectedDateStr = cellDateStr;
        showEventDetails(cellDateStr);

        // On mobile / tablet screens (<1024px), smooth scroll down to Day Overview if needed
        if (window.innerWidth < 1024) {
          const dayOverviewEl = document.getElementById('dayOverviewSection');
          if (dayOverviewEl) {
            dayOverviewEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
        }
      });

      calendarDaysGrid.appendChild(cell);
    }

    // 3. Spacing filler cells for trailing days to fill 35 or 42 grid slots
    const totalSlotsRendered = firstDayIndex + totalDays;
    const remainingSlots = (totalSlotsRendered > 35) ? (42 - totalSlotsRendered) : (35 - totalSlotsRendered);
    for (let nextDay = 1; nextDay <= remainingSlots; nextDay++) {
      const filler = document.createElement('div');
      filler.className = 'cal-day-cell other-month';
      filler.innerHTML = `<span class="cal-date-number text-xs opacity-40">${nextDay}</span><div class="cal-dots-wrap"></div>`;
      calendarDaysGrid.appendChild(filler);
    }
  }

  // --- 7. EVENT DETAILS INSPECTOR RENDERER ---
  function showEventDetails(dateStr) {
    if (!selectedDateTitle || !selectedDateSubtitle || !selectedEventsList) return;

    selectedDateTitle.textContent = getHumanDate(dateStr);
    const events = getEventsForDate(dateStr);
    selectedEventsList.innerHTML = '';

    if (events.length === 0) {
      selectedDateSubtitle.textContent = '0 Scheduled Items';
      selectedEventsList.innerHTML = `
        <div class="h-full flex flex-col items-center justify-center text-center p-6 text-[var(--text-muted)]">
          <div class="w-12 h-12 rounded-full glass-pill flex items-center justify-center text-xl mb-3 shadow-sm border border-white/80">
            ☕
          </div>
          <p class="font-bold text-sm text-[var(--text-primary)]">No Events Scheduled</p>
          <p class="text-xs mt-1">This day is free of official public or reserved departmental holidays.</p>
        </div>
      `;
      return;
    }

    selectedDateSubtitle.textContent = `${events.length} Scheduled Event${events.length > 1 ? 's' : ''}`;

    events.forEach((e) => {
      const item = document.createElement('div');
      item.className = 'event-detail-item space-y-2';

      // Department & Time tags
      let metaHtml = '';
      if (e.time || e.department) {
        metaHtml += `<div class="flex items-center gap-2.5 text-xs text-[var(--text-muted)] flex-wrap">`;
        if (e.time) {
          metaHtml += `
            <span class="inline-flex items-center gap-1.5 font-bold text-xs text-[var(--color-primary)]">
              <svg class="w-3.5 h-3.5 text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6h6"/></svg>
              ${e.time}
            </span>
          `;
        }
        if (e.department) {
          const deptLabel = Array.isArray(e.department) ? e.department.join(', ') : e.department;
          metaHtml += `
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-black/5 dark:bg-white/10 text-[10.5px] font-bold text-[var(--text-muted)]">
              ${deptLabel}
            </span>
          `;
        }
        metaHtml += `</div>`;
      }

      item.innerHTML = `
        <div class="flex items-start justify-between gap-2">
          <h4 class="font-bold text-sm sm:text-base text-[var(--color-primary-dark, #065F46)] dark:text-[var(--color-primary-light, #6EE7B7)] font-serif leading-tight">${e.name}</h4>
          <span class="cal-badge cal-badge-${e.type}">${e.badgeName}</span>
        </div>
        ${metaHtml}
        <p class="text-xs text-[var(--text-muted)] leading-relaxed">${e.description || 'Department holiday / academic activity.'}</p>
      `;

      selectedEventsList.appendChild(item);
    });
  }

  // --- 8. MONTH NAVIGATION EVENT HANDLERS ---
  function syncSelectionAfterMonthChange() {
    const parts = selectedDateStr.split('-');
    if (parseInt(parts[0], 10) === currentDate.getFullYear() && (parseInt(parts[1], 10) - 1) === currentDate.getMonth()) {
      showEventDetails(selectedDateStr);
    } else {
      if (selectedDateTitle) selectedDateTitle.textContent = 'Select a Date';
      if (selectedDateSubtitle) selectedDateSubtitle.textContent = 'Day Overview';
      if (selectedEventsList) {
        selectedEventsList.innerHTML = `
          <div class="h-full flex flex-col items-center justify-center text-center p-6 text-[var(--text-muted)]">
            <div class="w-12 h-12 rounded-full glass-pill flex items-center justify-center text-xl mb-3 shadow-sm border border-white/80">
              📅
            </div>
            <p class="font-bold text-sm text-[var(--text-primary)]">Select a Calendar Cell</p>
            <p class="text-xs mt-1">Click any day above to inspect scheduled holidays and activities.</p>
          </div>
        `;
      }
    }
  }

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
      syncSelectionAfterMonthChange();
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
      syncSelectionAfterMonthChange();
    });
  }

  if (todayJumpBtn) {
    todayJumpBtn.addEventListener('click', () => {
      currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
      selectedDateStr = formatDateStr(today.getFullYear(), today.getMonth(), today.getDate());
      renderCalendar();
      showEventDetails(selectedDateStr);
      showToast("Jumped to Today's Date");
    });
  }

  // Keyboard navigation (ArrowLeft & ArrowRight)
  document.addEventListener('keydown', (e) => {
    if (e?.target?.tagName === 'INPUT' || e?.target?.tagName === 'TEXTAREA') return;
    if (e.key === 'ArrowLeft') {
      currentDate.setMonth(currentDate.getMonth() - 1);
      renderCalendar();
      syncSelectionAfterMonthChange();
    } else if (e.key === 'ArrowRight') {
      currentDate.setMonth(currentDate.getMonth() + 1);
      renderCalendar();
      syncSelectionAfterMonthChange();
    }
  });

  // --- 9. FILTER CHECKBOX LISTENERS ---
  if (filterPublic) {
    filterPublic.addEventListener('change', (e) => {
      currentFilterPublic = e.target.checked;
      renderCalendar();
      showEventDetails(selectedDateStr);
    });
  }

  if (filterReserved) {
    filterReserved.addEventListener('change', (e) => {
      currentFilterReserved = e.target.checked;
      renderCalendar();
      showEventDetails(selectedDateStr);
    });
  }

  if (filterCustom) {
    filterCustom.addEventListener('change', (e) => {
      currentFilterCustom = e.target.checked;
      renderCalendar();
      showEventDetails(selectedDateStr);
    });
  }

  // --- 10. REAL-TIME SEARCH INTEGRATION (DESKTOP + RESPONSIVE MOBILE NAVBAR) ---
  window.addEventListener('portal:search', (e) => {
    searchQuery = e.detail?.query || '';
    renderCalendar();
    showEventDetails(selectedDateStr);
  });

  if (dashboardSearchInput) {
    dashboardSearchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      renderCalendar();
      showEventDetails(selectedDateStr);
    });
  }
  if (mobileSearchInput) {
    mobileSearchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      renderCalendar();
      showEventDetails(selectedDateStr);
    });
  }

  // --- 11. PRINT & COPY UTILITIES ---
  if (printBtn) {
    printBtn.addEventListener('click', () => window.print());
  }

  if (copyBtn) {
    copyBtn.addEventListener('click', () => {
      const events = getEventsForDate(selectedDateStr);
      const text = `${getHumanDate(selectedDateStr)}:\n` +
        (events.length ? events.map((e) => `• [${e.badgeName}] ${e.name}: ${e.description}`).join('\n') : 'No scheduled events.');
      navigator.clipboard.writeText(text).then(() => {
        showToast('Date details copied to clipboard!');
      });
    });
  }

  // --- 12. POPUP MODAL ENGINE (AI ASSISTANT / PROFILE / SETTINGS) ---
  const modalOverlay = document.getElementById('modalOverlay');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const modalTitle = document.getElementById('modalTitle');
  const modalSubtitle = document.getElementById('modalSubtitle');
  const modalBody = document.getElementById('modalBody');

  function openCalendarModal(type) {
    if (!modalOverlay || !modalTitle || !modalSubtitle || !modalBody) return;
    modalOverlay.classList.remove('hidden');

    if (type === 'ai-assistant') {
      modalTitle.textContent = 'AI Academic Calendar Assistant';
      modalSubtitle.textContent = 'Ask anything regarding holidays, academic exam schedules or events';
      modalBody.innerHTML = `
        <div class="space-y-3">
          <div class="p-3 rounded-2xl glass-pill text-xs flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
            <span>Active Year: <strong>Academic Year 2026</strong></span>
          </div>
          <textarea id="aiCalendarPrompt" rows="3" class="w-full p-3 rounded-2xl glass-input text-xs border border-white/60 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" placeholder="e.g. When are Diwali holidays? How many public holidays in October?"></textarea>
          <div class="flex justify-end gap-2">
            <button type="button" id="submitAiCalendarPrompt" class="px-4 py-2 rounded-xl bg-[var(--color-primary)] text-white font-bold text-xs shadow-md hover:brightness-105 transition">Ask Assistant</button>
          </div>
          <div id="aiCalendarOutput" class="hidden p-3 rounded-2xl glass-card text-xs"></div>
        </div>
      `;
      document.getElementById('submitAiCalendarPrompt')?.addEventListener('click', () => {
        const prompt = document.getElementById('aiCalendarPrompt')?.value || '';
        const output = document.getElementById('aiCalendarOutput');
        if (!prompt.trim() || !output) return;
        output.classList.remove('hidden');
        output.innerHTML = `
          <p class="font-bold text-[var(--color-primary)] mb-1">AI Response:</p>
          <p class="text-[var(--text-muted)]">Scanning 2026 academic calendar for "${prompt}"... (All 2026 public and reserved holidays are loaded in local data).</p>
        `;
      });
    } else if (type === 'profile') {
      const uName = window.PORTAL_USER?.name || 'Faculty Member';
      const uRole = window.PORTAL_USER?.role === 'super_admin' ? 'Super Administrator' : (window.PORTAL_USER?.designation || 'Faculty Staff');
      const uEmail = window.PORTAL_USER?.email || '';
      const uDept = window.PORTAL_USER?.department || 'IT & CE Department';
      const uInitials = window.PORTAL_USER?.initials || uName.replace(/[^A-Za-z]/g, '').slice(0, 2).toUpperCase() || 'FM';

      modalTitle.textContent = `${uName} — Faculty Profile`;
      modalSubtitle.textContent = `CE & IT Department Portal • ${uRole}`;
      modalBody.innerHTML = `
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg ring-2 ring-[var(--color-primary)] overflow-hidden shrink-0 shadow-sm">
            ${uInitials}
          </div>
          <div class="text-xs">
            <p class="font-bold text-sm text-[var(--text-primary)]">${uName}</p>
            <p class="text-[var(--text-muted)]">${uRole} &bull; ${uDept}</p>
            ${uEmail ? `<p class="text-[var(--color-primary)] font-medium mt-0.5 font-mono">${uEmail}</p>` : ''}
            <div class="flex items-center gap-2 mt-2">
              <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold text-[10px]">Active Shift • CE & IT</span>
              <a href="${typeof baseUrl !== 'undefined' ? baseUrl : './'}logout" class="inline-block px-2.5 py-0.5 rounded-full bg-rose-500/15 text-rose-500 hover:bg-rose-500 hover:text-white font-bold text-[10px] transition cursor-pointer">Sign Out</a>
            </div>
          </div>
        </div>
      `;
    }
  }

  if (closeModalBtn && modalOverlay) {
    closeModalBtn.addEventListener('click', () => modalOverlay.classList.add('hidden'));
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) modalOverlay.classList.add('hidden');
    });
  }

  ['aiAssistantBtn', 'mobileAiAssistantBtn', 'drawerAiAssistantBtn'].forEach((id) => {
    const btn = document.getElementById(id);
    if (btn) {
      btn.addEventListener('click', () => {
        if (typeof window.closeMobileSidebar === 'function') window.closeMobileSidebar();
        openCalendarModal('ai-assistant');
      });
    }
  });

  // Settings gear button opens color studio popover
  const openSettingsBtn = document.getElementById('openSettingsBtn');
  if (openSettingsBtn) {
    openSettingsBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const popover = document.getElementById('colorPickerPopover');
      if (popover) {
        popover.classList.toggle('hidden');
      }
    });
  }

  // Profile button opens profile modal
  const openProfileBtn = document.getElementById('openProfileBtn');
  if (openProfileBtn) {
    openProfileBtn.addEventListener('click', () => {
      if (typeof window.closeMobileSidebar === 'function') window.closeMobileSidebar();
      openCalendarModal('profile');
    });
  }

  // Re-tint calendar accents when theme changes
  window.addEventListener('portal:theme-color-changed', () => renderCalendar());
  window.addEventListener('portal:theme-mode-changed', () => renderCalendar());

  // --- 13. INITIAL INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    setDepartment(currentDept === 'Computer Engineering');
  });

  if (document.readyState !== 'loading') {
    setDepartment(currentDept === 'Computer Engineering');
  }

})();

