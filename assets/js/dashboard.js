/**
 * Dashboard Controller
 * Smart Management Dashboard — CE & IT Department Academic Portal
 *
 * Handles:
 * - SortableJS Drag-and-Drop Card Grid Reordering (with Card 1 Permanently Pinned)
 * - Layout Order Persistence via localStorage and 1-Click Layout Reset
 * - 16 Time-Slot Academic Pill Bars (Daily Schedule Distribution)
 * - 34 Hourly Classroom & Lab Load Bars (Theory vs Lab Filters)
 * - 56-Bar Weekly Workload Density Histogram (Mon-Sat Days)
 * - Stepped Room & Lab Occupancy Chart
 * - Dynamic Role-Wise Card (Faculty Schedule / HOD Governance / Super Admin Status)
 * - Role View Switcher & Live API Sync
 * - Camera Zone switching (Cam 01 vs Cam 02)
 * - Synchronization with 'portal:theme-color-changed' and 'portal:theme-mode-changed'
 */

(function () {
  'use strict';

  // --- 1. DASHBOARD STATE & DATA HYDRATION ---
  const rawData = window.DASHBOARD_DATA || {};
  const metrics = rawData.metrics || {};
  const chartData = rawData.chartData || {};

  const dashboardState = {
    cameraZone: 1,
    zone1Src: '',
    zone2Src: 'data:image/webp;base64,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',
    currentDay: rawData.currentDay || 'MON',
    activeRole: rawData.activeRole || 'super_admin',
    timeSlots: chartData.timeSlots || [],
    slotActiveCounts: chartData.slotActiveCounts || [],
    hourlyProfileBars: chartData.hourlyProfileBars || [],
    weeklyTrendBars: chartData.weeklyTrendBars || [],
    occupancySteps: chartData.occupancySteps || [],
    roleContext: rawData.roleContext || {},
    moduleSummaries: rawData.moduleSummaries || (metrics.moduleSummaries || {}),
    facultyList: rawData.facultyList || [],
    powerFilter: 'all',
    selectedGrowthDay: 2
  };

  // Helper Toast Notification
  function showToast(message, type) {
    if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      return window.PortalToast.show(message, type);
    }
    if (typeof window.showPortalToast === 'function') {
      return window.showPortalToast(message, type);
    }
    console.log(`[Toast ${type || 'info'}]: ${message}`);
  }

  // --- 2. DRAG-AND-DROP REORDERABLE CARDS WITH SORTABLEJS ---
  const STORAGE_KEY_LAYOUT = 'portal_dashboard_layout_order';

  function initSortableGrid() {
    const gridEl = document.getElementById('bentoGrid');
    if (!gridEl || typeof Sortable === 'undefined') return;

    // A. Restore saved card sequence from localStorage
    restoreCardSequence(gridEl);

    // B. Initialize SortableJS
    new Sortable(gridEl, {
      animation: 250,
      handle: '.card-drag-handle, .glass-card',
      filter: '.locked-card, button, input, select, a, [data-modal-open]',
      preventOnFilter: false,
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen',
      dragClass: 'sortable-drag',
      onMove: function (evt) {
        // Strictly prevent moving onto or before the locked first card
        if (evt.related && evt.related.classList.contains('locked-card')) {
          return false;
        }
        return true;
      },
      onEnd: function () {
        saveCardSequence(gridEl);
        showToast('Dashboard card layout updated & saved', 'success');
      }
    });
  }

  function saveCardSequence(gridEl) {
    const cards = gridEl.querySelectorAll('.dashboard-card');
    const ids = [];
    cards.forEach((card) => {
      const id = card.getAttribute('data-id') || card.id;
      if (id) ids.push(id);
    });
    try {
      localStorage.setItem(STORAGE_KEY_LAYOUT, JSON.stringify(ids));
    } catch (e) { }
  }

  function restoreCardSequence(gridEl) {
    try {
      const raw = localStorage.getItem(STORAGE_KEY_LAYOUT);
      if (!raw) return;
      const ids = JSON.parse(raw);
      if (!Array.isArray(ids) || ids.length === 0) return;

      // Card 1 MUST ALWAYS stay at index 0
      const heroCard = document.getElementById('cardHero');
      if (heroCard && heroCard.parentNode === gridEl) {
        gridEl.insertBefore(heroCard, gridEl.firstElementChild);
      }

      ids.forEach((id) => {
        if (id === 'cardHero') return; // Skip locked hero
        const el = document.getElementById(id) || gridEl.querySelector(`[data-id="${id}"]`);
        if (el && el.parentNode === gridEl) {
          gridEl.appendChild(el);
        }
      });
    } catch (e) { }
  }

  function resetCardSequence() {
    try {
      localStorage.removeItem(STORAGE_KEY_LAYOUT);
      const gridEl = document.getElementById('bentoGrid');
      if (!gridEl) return;

      // Default card sequence (all 17 cards)
      const defaultOrder = [
        'cardHero',
        'cardHarvest',
        'cardWaterFlow',
        'cardPower',
        'cardMicroKpis',
        'cardGrowthRate',
        'cardAirCo2',
        'cardRoleContext',
        'cardCalendar',
        'cardDivisions',
        'cardCtlActivity',
        'cardCtlDrive',
        'cardZeroStudent',
        'cardReports',
        'cardSyllabus',
        'cardFacultyMatrix',
        'cardDepartmentHub'
      ];

      defaultOrder.forEach((id) => {
        const el = document.getElementById(id) || gridEl.querySelector(`[data-id="${id}"]`);
        if (el && el.parentNode === gridEl) {
          gridEl.appendChild(el);
        }
      });

      showToast('Card layout restored to default sequence', 'theme');
    } catch (e) { }
  }

  // --- 3. 16 TIME-SLOT PILL BARS (DAILY ACADEMIC SLOTS) ---
  function renderSlotPillBars() {
    const container = document.getElementById('waterFlowBarsContainer');
    if (!container) return;

    const slots = dashboardState.timeSlots;
    const activeCounts = dashboardState.slotActiveCounts;
    const totalBars = 16;
    container.innerHTML = '';

    for (let i = 0; i < totalBars; i++) {
      const slotName = slots[i] || `Period Slot ${i + 1}`;
      const count = activeCounts[i] !== undefined ? activeCounts[i] : (i % 2 === 0 ? 14 : 8);
      const isGreen = count > 0;

      const bar = document.createElement('button');
      bar.type = 'button';
      bar.className = 'flex-1 h-full flex items-center justify-center group focus:outline-none';
      bar.title = `${slotName}: ${count} active sessions today`;

      const pill = document.createElement('span');
      pill.className = 'w-[5px] sm:w-[6px] h-full rounded-full transition-all duration-200 group-hover:scale-y-110';
      pill.style.backgroundColor = isGreen ? 'var(--bar-active)' : 'var(--bar-inactive)';

      bar.appendChild(pill);
      bar.addEventListener('click', () => {
        showToast(`${slotName}: ${count} classes / labs active in this period`);
      });
      container.appendChild(bar);
    }
  }

  // --- 4. HOURLY CLASSROOM & LAB LOAD BARS (34 BARS) ---
  function renderHourlyLoadBars() {
    const container = document.getElementById('powerChartBars');
    if (!container) return;
    container.innerHTML = '';

    const barData = dashboardState.hourlyProfileBars;
    const filter = dashboardState.powerFilter; // 'all', 'theory', 'lab'

    barData.forEach((item) => {
      const bar = document.createElement('div');
      bar.className = 'chart-bar flex-1 rounded-full cursor-pointer';

      let displayHeight = item.h;
      if (filter === 'theory') {
        displayHeight = Math.max(15, Math.round(item.h * 0.58));
      } else if (filter === 'lab') {
        displayHeight = Math.max(15, Math.round(item.h * 0.42));
      }

      bar.style.height = `${displayHeight}%`;

      if (filter === 'theory') {
        bar.style.backgroundColor = 'var(--color-primary)';
      } else if (filter === 'lab') {
        bar.style.backgroundColor = 'var(--color-alert, #F59E0B)';
      } else {
        if (item.type === 'peak') {
          bar.style.backgroundColor = 'var(--color-alert, #F59E0B)';
        } else if (item.type === 'green') {
          bar.style.backgroundColor = 'var(--color-primary-dark)';
        } else if (item.type === 'mid') {
          bar.style.backgroundColor = 'var(--color-primary)';
        } else {
          bar.style.backgroundColor = 'var(--bar-inactive)';
        }
      }

      bar.title = `${item.time} — ${item.total} Total (${item.theory} Theory, ${item.lab} Labs)`;
      bar.addEventListener('click', () => {
        showToast(`${item.time}: ${item.total} lectures active (${item.theory} Theory, ${item.lab} Labs)`);
      });

      container.appendChild(bar);
    });
  }

  // --- 5. WEEKLY WORKLOAD DENSITY HISTOGRAM (56 BARS) ---
  function renderWeeklyTrendBars() {
    const container = document.getElementById('growthChartBars');
    if (!container) return;
    container.innerHTML = '';

    const trendData = dashboardState.weeklyTrendBars;
    const selDay = dashboardState.selectedGrowthDay; // 0=Mon, 1=Tue...

    trendData.forEach((item, idx) => {
      const bar = document.createElement('div');
      bar.className = 'chart-bar flex-1 rounded-t-sm cursor-pointer';
      bar.style.height = `${item.val}%`;

      const isCurrentDayMatch = item.dayIdx === selDay;
      if (isCurrentDayMatch) {
        bar.style.backgroundColor = 'var(--color-primary)';
        bar.style.opacity = '1';
      } else {
        bar.style.backgroundColor = 'var(--glass-border)';
        bar.style.opacity = '0.45';
      }

      bar.title = `${item.label}: ${item.val}% capacity`;
      bar.addEventListener('click', () => {
        dashboardState.selectedGrowthDay = item.dayIdx;
        renderWeeklyTrendBars();
        updateGrowthDayButtons(item.dayIdx);
        showToast(`${item.label}: Workload capacity ${item.val}%`);
      });

      container.appendChild(bar);
    });
  }

  function updateGrowthDayButtons(dayIdx) {
    document.querySelectorAll('.growth-day-btn').forEach((btn) => {
      const d = parseInt(btn.getAttribute('data-growth-day'), 10);
      if (d === dayIdx) {
        btn.classList.add('font-bold', 'text-[var(--color-primary)]');
        btn.classList.remove('hover:text-[var(--text-primary)]');
      } else {
        btn.classList.remove('font-bold', 'text-[var(--color-primary)]');
        btn.classList.add('hover:text-[var(--text-primary)]');
      }
    });
  }

  // --- 6. CAMPUS ROOM OCCUPANCY STEPPED AREA CHART ---
  function renderOccupancyChart() {
    const container = document.getElementById('co2StepsContainer');
    if (!container) return;
    container.innerHTML = '';

    const steps = dashboardState.occupancySteps;
    steps.forEach((step, idx) => {
      const col = document.createElement('div');
      col.className = 'flex flex-col justify-end items-center h-full group cursor-pointer';

      const block = document.createElement('div');
      block.className = 'w-full rounded-t-md transition-all duration-300 group-hover:brightness-110';
      block.style.height = `${step.h}%`;

      if (idx === steps.length - 1 || step.percent >= 90) {
        block.style.background = 'linear-gradient(180deg, var(--color-primary) 0%, var(--color-primary-dark) 100%)';
      } else {
        block.style.background = 'linear-gradient(180deg, var(--bar-mid) 0%, var(--bar-inactive) 100%)';
      }

      col.appendChild(block);

      const label = document.createElement('span');
      label.className = 'text-[9px] text-[var(--text-muted)] mt-1 select-none';
      label.textContent = step.time;
      col.appendChild(label);

      col.title = `${step.time} — ${step.percent}% Peak Occupancy (${step.rooms} active rooms)`;
      col.addEventListener('click', () => {
        showToast(`${step.time}: ${step.percent}% Lab & Room Occupancy (${step.rooms} Rooms Active)`);
      });

      container.appendChild(col);
    });
  }

  // --- 7. DYNAMIC ROLE-SPECIFIC ACADEMIC CARD RENDERER ---
  function renderRoleCard(roleData) {
    const titleEl = document.getElementById('roleCardTitle');
    const subtitleEl = document.getElementById('roleCardSubtitle');
    const bodyEl = document.getElementById('roleCardBody');
    if (!bodyEl) return;

    const role = roleData.role || dashboardState.activeRole;

    if (titleEl) titleEl.textContent = roleData.title || 'Academic Role Context';
    if (subtitleEl) subtitleEl.textContent = roleData.subtitle || 'Personalized Academic Telemetry';

    if (role === 'staff' || role === 'faculty') {
      const schedule = roleData.todaySchedule || [];
      const facName = roleData.facultyName || 'Faculty Member';
      const facInitials = roleData.facultyInitials || 'APG';
      const weeklyHrs = roleData.weeklyTotalHrs || 16;
      const nextClass = roleData.nextClass;

      let scheduleHtml = '';
      if (schedule.length > 0) {
        scheduleHtml = schedule.slice(0, 3).map((item, idx) => `
          <div class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs hover:border-[var(--color-primary)] transition">
            <div class="min-w-0 pr-2">
              <span class="font-bold text-[var(--text-primary)] truncate block">${item.class}</span>
              <span class="text-[10px] text-[var(--text-muted)]">${item.time} &bull; Room: <strong>${item.room || 'TBD'}</strong></span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold ${item.isLab ? 'theme-tag-pill' : 'theme-tag-pill'} shrink-0">
              ${item.isLab ? 'LAB' : 'THEORY'}
            </span>
          </div>
        `).join('');
      } else {
        scheduleHtml = `
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-center text-xs text-[var(--text-muted)]">
            <p class="font-semibold text-[var(--text-primary)]">No lectures scheduled today</p>
            <p class="text-[10px] mt-0.5">Academic office hours & student counseling</p>
          </div>
        `;
      }

      bodyEl.innerHTML = `
        <div class="space-y-2">
          <!-- Next Class Banner -->
          ${nextClass ? `
            <div class="px-2.5 py-1.5 rounded-xl bg-[var(--color-primary)]/10 border border-[var(--color-primary)]/30 flex items-center justify-between text-xs">
              <div class="flex items-center gap-1.5 min-w-0">
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-ping"></span>
                <span class="font-bold text-[var(--color-primary)] truncate">Next: ${nextClass.time}</span>
              </div>
              <span class="font-bold text-[var(--text-primary)] text-[11px]">${nextClass.room}</span>
            </div>
          ` : ''}

          <!-- Schedule List -->
          <div class="space-y-1.5 max-h-[145px] overflow-y-auto pr-0.5">
            ${scheduleHtml}
          </div>

          <!-- Faculty Profile Info (Automatic) -->
          <div class="pt-1.5 flex items-center justify-between text-[11px] border-t border-[var(--glass-border)]">
            <span class="text-[var(--text-muted)]">Faculty Profile:</span>
            <span class="font-bold text-[var(--color-primary)]">${facName} (${facInitials})</span>
          </div>
        </div>
      `;

    } else if (role === 'hod') {
      bodyEl.innerHTML = `
        <div class="space-y-2 text-xs">
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">IT Staff</span>
              <p class="text-lg font-bold text-[var(--text-primary)] mt-0.5">${roleData.deptFacultyCount || 26} Faculty</p>
            </div>
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">CE Staff</span>
              <p class="text-lg font-bold text-[var(--text-primary)] mt-0.5">${roleData.ceFacultyCount || 28} Faculty</p>
            </div>
          </div>
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Department Workload Compliance</span>
              <p class="text-xs font-bold text-[var(--color-primary)] mt-0.5">98.5% Timetable Allotment Met</p>
            </div>
            <span class="px-2 py-1 rounded-lg theme-tag-pill font-bold text-xs">NORMAL</span>
          </div>
        </div>
      `;
    } else if (role === 'sub_admin' || role === 'tpa_team') {
      bodyEl.innerHTML = `
        <div class="space-y-2 text-xs">
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Timetables Audited</span>
              <p class="text-lg font-bold text-[var(--text-primary)] mt-0.5">${roleData.verifiedTimetables || 51} / 51</p>
            </div>
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Zero-Student Cases</span>
              <p class="text-lg font-bold text-[var(--color-primary)] mt-0.5">${roleData.zeroStudentCases || 0} Open</p>
            </div>
          </div>
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
            <p class="text-[11px] font-semibold text-[var(--text-primary)]">TPA Workload Verification Active</p>
            <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Faculty schedules & student divisions comply with GTU norms.</p>
          </div>
        </div>
      `;
    } else if (roleData.isCustomRole || (role !== 'super_admin' && role !== 'hod' && role !== 'staff' && role !== 'faculty')) {
      // Future Custom Role Handler (Auto-adapts to any newly created role in config/roles.json)
      bodyEl.innerHTML = `
        <div class="space-y-2 text-xs">
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Active Role</span>
              <p class="text-sm font-bold text-[var(--text-primary)] mt-0.5">${roleData.title || role}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full theme-tag-pill font-bold text-[10px] uppercase">${roleData.badge || 'Custom Role'}</span>
          </div>
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Permissions Active</span>
              <p class="text-sm font-bold text-[var(--color-primary)] mt-0.5">${roleData.permissionsCount || 0} Modules Granted</p>
            </div>
            <span class="px-2 py-0.5 rounded-md theme-tag-pill text-[10px] font-bold">AUTHORIZED</span>
          </div>
          <p class="text-[10px] text-[var(--text-muted)] px-0.5">${roleData.subtitle || 'Custom departmental dashboard workspace.'}</p>
        </div>
      `;
    } else {
      // Super Admin View
      bodyEl.innerHTML = `
        <div class="space-y-2 text-xs">
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Total Accounts</span>
              <p class="text-lg font-bold text-[var(--text-primary)] mt-0.5">${roleData.totalAccounts || 54} Users</p>
            </div>
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Roles Configured</span>
              <p class="text-lg font-bold text-[var(--text-primary)] mt-0.5">${roleData.activeRolesCount || 5} Roles</p>
            </div>
          </div>
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase">Timetable Sync Engine</span>
              <p class="text-xs font-bold text-[var(--color-primary)] mt-0.5">51 Faculty Sheets Live & Synced</p>
            </div>
            <span class="px-2 py-0.5 rounded-md theme-tag-pill text-[10px] font-bold">ONLINE</span>
          </div>
        </div>
      `;
    }
  }

  // Fetch role metrics via API
  function fetchRoleMetrics(role, facultyId) {
    const url = new URL(`${window.location.origin}${window.location.pathname.replace(/\/dashboard.*$/, '')}/api/dashboard/metrics`);
    url.searchParams.set('role', role);
    if (facultyId) url.searchParams.set('faculty_id', facultyId);

    fetch(url.toString(), { credentials: 'same-origin' })
      .then(res => res.json())
      .then(res => {
        if (res && res.success && res.data) {
          dashboardState.activeRole = role;
          dashboardState.roleContext = res.data.roleContext || {};
          renderRoleCard(dashboardState.roleContext);
          showToast(`Switched view to ${role.toUpperCase()}`, 'theme');
        }
      })
      .catch(() => {
        // Fallback client update
        dashboardState.activeRole = role;
        renderRoleCard({ role: role, title: `${role.toUpperCase()} View`, subtitle: 'Academic Telemetry' });
      });
  }

  // --- 8. CAMERA FEED SWITCHER (CARD 1) ---
  const cam1Btn = document.getElementById('switchCamZone1Btn');
  const cam2Btn = document.getElementById('switchCamZone2Btn');
  const heroImg = document.getElementById('heroCameraImg');
  const heroLabel = document.getElementById('activeZoneHeaderLabel');

  if (cam1Btn && cam2Btn && heroImg) {
    cam1Btn.addEventListener('click', () => {
      heroImg.src = dashboardState.zone1Src || heroImg.src;
      cam1Btn.className = 'px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5 transition';
      cam2Btn.className = 'px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 border border-white/10 transition';
      if (heroLabel) heroLabel.textContent = 'Zone A • Hydroponic Bay';
      showToast('Switched to Camera 01 (Zone A Stream)');
    });

    cam2Btn.addEventListener('click', () => {
      heroImg.src = dashboardState.zone2Src;
      cam2Btn.className = 'px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5 transition';
      cam1Btn.className = 'px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 border border-white/10 transition';
      if (heroLabel) heroLabel.textContent = 'Zone B • Campus Canopy Vision';
      showToast('Switched to Camera 02 (Zone B Stream)');
    });
  }

  // --- 9. MODALS & SETTINGS ---
  const modalOverlay = document.getElementById('modalOverlay');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const modalTitle = document.getElementById('modalTitle');
  const modalSubtitle = document.getElementById('modalSubtitle');
  const modalBody = document.getElementById('modalBody');

  function openModal(type) {
    if (!modalOverlay || !modalBody) return;
    modalOverlay.classList.remove('hidden');

    if (type === 'harvest') {
      modalTitle.textContent = 'Department Workload Audit';
      modalSubtitle.textContent = 'CE & IT Faculty Allotment Breakdown';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Total weekly teaching load of 826 hours allotted across 51 faculty members. All classes adhere to GTU academic syllabus standards.</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Average Workload</span>
            <span class="font-bold text-[var(--color-primary)]">16.4 Hours / Week</span>
          </div>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Theory to Practical Ratio</span>
            <span class="font-bold text-[var(--text-primary)]">58% Theory / 42% Lab</span>
          </div>
        </div>
      `;
    } else if (type === 'water') {
      modalTitle.textContent = 'Time-Slot Schedule Breakdown';
      modalSubtitle.textContent = 'Daily Shift Sessions Distribution';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">16 standard period shifts active from 07:30 to 18:00 across Diploma, B.Tech, and M.Tech divisions.</p>
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold">Morning Shift</span>
              <p class="font-bold text-sm text-[var(--text-primary)] mt-0.5">07:30 to 12:00</p>
            </div>
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold">Afternoon Shift</span>
              <p class="font-bold text-sm text-[var(--text-primary)] mt-0.5">12:15 to 06:00</p>
            </div>
          </div>
        </div>
      `;
    } else if (type === 'setpoints') {
      modalTitle.textContent = 'Academic Standards & Thresholds';
      modalSubtitle.textContent = 'Department Operational Guidelines';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <p class="font-bold text-[var(--text-primary)]">Full Time Faculty Minimum Load</p>
              <p class="text-[10px] text-[var(--text-muted)]">Minimum 16 academic hours per week</p>
            </div>
            <span class="font-bold text-[var(--color-primary)]">COMPLIANT</span>
          </div>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <div>
              <p class="font-bold text-[var(--text-primary)]">Remedial Zero-Attendance Guard</p>
              <p class="text-[10px] text-[var(--text-muted)]">Requires instant student follow-up</p>
            </div>
            <span class="font-bold text-[var(--color-primary)]">ACTIVE</span>
          </div>
        </div>
      `;
    } else if (type === 'co2') {
      modalTitle.textContent = 'Campus Facility & Lab Capacity';
      modalSubtitle.textContent = 'Computer Labs & Classroom Utilization';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Monitored campus facilities include FF-24 (Software Lab), FF-25 (Network Lab), TF-05 (Cloud Lab), and GF-21 (IoT Bay).</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
            <span class="font-bold text-sm text-[var(--text-primary)]">Peak Lab Hours: 11:00 AM – 03:45 PM</span>
            <p class="text-[10px] text-[var(--text-muted)] mt-1">95% concurrent workstation utilization across department laboratories.</p>
          </div>
        </div>
      `;
    } else if (type === 'calendar') {
      modalTitle.textContent = 'Academic Calendar & Semester Schedule';
      modalSubtitle.textContent = 'ODD Semester 2026-27 Milestone Tracking';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
            <span class="font-bold text-sm text-[var(--text-primary)]">Semester Progress: Week 18 of 24 (75% Elapsed)</span>
            <p class="text-[10px] text-[var(--text-muted)] mt-1">GTU Term Schedule: 21 Public & Gazetted Holidays, Weekly Review Meetings on Fridays at 09:30 AM.</p>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold">Next Review Meeting</span>
              <p class="font-bold text-xs text-[var(--text-primary)] mt-0.5">Friday, 09:30 AM</p>
            </div>
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)]">
              <span class="text-[10px] text-[var(--text-muted)] font-semibold">Upcoming Holiday</span>
              <p class="font-bold text-xs text-[var(--color-primary)] mt-0.5">Winter Break (25 Dec)</p>
            </div>
          </div>
        </div>
      `;
    } else if (type === 'divisions') {
      modalTitle.textContent = 'Student Divisions & Batch Distribution';
      modalSubtitle.textContent = '33 Active Class Divisions & 66 Lab Batches';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Class schedules span 18 B.Tech divisions, 10 Diploma divisions, and 5 M.Tech postgraduate divisions across Morning (07:30) and Afternoon (12:15) shifts.</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Lab Practical Batches</span>
            <span class="font-bold text-[var(--color-primary)]">66 Active Batches</span>
          </div>
        </div>
      `;
    } else if (type === 'ctlactivity') {
      modalTitle.textContent = 'CTL Activity & Mentorship Pulse';
      modalSubtitle.textContent = 'Teaching Compliance & Verification Rates';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">51 faculty coordinators monitored for daily activity reporting and mentorship submissions. Automatic email notifications dispatched via SMTP to departmental CC authorities.</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Submission Approval Rate</span>
            <span class="font-bold text-[var(--color-primary)]">94.1% (48 Approved / 3 Pending)</span>
          </div>
        </div>
      `;
    } else if (type === 'ctldrive') {
      modalTitle.textContent = 'CTL Cloud Drive Scanner';
      modalSubtitle.textContent = 'Faculty Google Drive Folders & Curriculum Depth';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Automated scanner monitors all 51 faculty Google Drive folders to audit teaching material depth against GTU curriculum requirements (Level 3 Heuristic).</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Synced Cloud Health</span>
            <span class="font-bold text-[var(--color-primary)]">100% Compliant (51 / 51 Folders)</span>
          </div>
        </div>
      `;
    } else if (type === 'zero_student') {
      modalTitle.textContent = 'Zero Student Incident Log & Guard';
      modalSubtitle.textContent = 'GTU 75% Attendance Safeguard System';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Automated attendance audit detects lectures with zero or critically low attendance, enforcing faculty claims and immediate remedial counseling.</p>
          <div class="p-3 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
            <span class="font-semibold">Current Open Incidents</span>
            <span class="font-bold text-[var(--color-primary)]">0 Open (12 Resolved Historically)</span>
          </div>
        </div>
      `;
    } else if (type === 'reports') {
      modalTitle.textContent = 'Department Workload Reports & Exports';
      modalSubtitle.textContent = 'Daily Activity Tracking & Multi-Format Exports';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Full workload logs with 82% Lectures & Labs, 8% Workshops, 6% Seminars, and 4% Remedials. Supports instant client-side Excel (.xlsx), PDF, and CSV generation.</p>
        </div>
      `;
    } else if (type === 'syllabus') {
      modalTitle.textContent = 'Syllabus & Past Examination Question Papers';
      modalSubtitle.textContent = '8 Semester GTU Curriculum Schemes Archive';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Comprehensive syllabus archive covering Semesters 1 through 8 for UG CE, UG IT, Diploma CE, Diploma IT, and M.Tech CE, linked with live past Winter and Summer question papers.</p>
        </div>
      `;
    } else if (type === 'faculty_matrix') {
      modalTitle.textContent = 'Faculty Roster & Cabin Allocation Matrix';
      modalSubtitle.textContent = '54 Department Staff Members & Seating Map';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Department split of 26 IT faculty and 28 CE faculty across cabins FF-38 (26 staff), FF-01 (10 staff), FF-37 (6 staff), GF-37 (4 staff), and FF-24/25.</p>
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

  document.querySelectorAll('[data-modal-open]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.stopPropagation();
      openModal(el.getAttribute('data-modal-open'));
    });
  });


  // Reset Layout Handler
  window.resetDashboardLayout = resetCardSequence;
  const resetLayoutBtn = document.getElementById('resetLayoutBtn');
  if (resetLayoutBtn) {
    resetLayoutBtn.addEventListener('click', () => {
      resetCardSequence();
    });
  }

  // Filter Buttons for Hourly Load Chart
  const filterAllBtn = document.getElementById('filterPowerAllBtn');
  const filterTheoryBtn = document.getElementById('filterPowerLightingBtn');
  const filterLabBtn = document.getElementById('filterPowerOtherBtn');

  if (filterTheoryBtn) {
    filterTheoryBtn.addEventListener('click', () => {
      dashboardState.powerFilter = dashboardState.powerFilter === 'theory' ? 'all' : 'theory';
      renderHourlyLoadBars();
      showToast(`Filter: ${dashboardState.powerFilter.toUpperCase()} classes`);
    });
  }
  if (filterLabBtn) {
    filterLabBtn.addEventListener('click', () => {
      dashboardState.powerFilter = dashboardState.powerFilter === 'lab' ? 'all' : 'lab';
      renderHourlyLoadBars();
      showToast(`Filter: ${dashboardState.powerFilter.toUpperCase()} sessions`);
    });
  }

  // Growth Day Buttons
  document.querySelectorAll('.growth-day-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const d = parseInt(btn.getAttribute('data-growth-day'), 10);
      dashboardState.selectedGrowthDay = d;
      renderWeeklyTrendBars();
      updateGrowthDayButtons(d);
      const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      showToast(`Workload density for ${days[d] || 'Academic Day'}`);
    });
  });

  // --- 10. THEME & COLOR SYNCHRONIZATION ---
  function updateThemeVisuals(detail) {
    const rootStyle = getComputedStyle(document.documentElement);
    const primary = detail?.hex || rootStyle.getPropertyValue('--color-primary').trim() || '#7C3AED';
    const primaryLight = detail?.lightShade || rootStyle.getPropertyValue('--color-primary-light').trim() || '#A78BFA';
    const primaryDark = detail?.darkShade || rootStyle.getPropertyValue('--color-primary-dark').trim() || '#5B21B6';

    // 1. Update SVG radial gradient stops for Card 2 Workload gauge
    const s1 = document.getElementById('gradStop1');
    const s2 = document.getElementById('gradStop2');
    const s3 = document.getElementById('gradStop3');
    if (s1) s1.setAttribute('stop-color', primaryLight);
    if (s2) s2.setAttribute('stop-color', primary);
    if (s3) s3.setAttribute('stop-color', primaryDark);

    // 2. Update SVG radial donut arc strokes (Card 11 CTL activity)
    document.querySelectorAll('.radial-donut-val').forEach(path => {
      path.setAttribute('stroke', primary);
      path.setAttribute('fill', 'none');
    });
    document.querySelectorAll('.radial-donut-bg').forEach(path => {
      path.setAttribute('fill', 'none');
    });

    // 3. Update active growth day button highlight
    updateGrowthDayButtons(dashboardState.selectedGrowthDay);
  }

  window.addEventListener('portal:theme-color-changed', (e) => {
    updateThemeVisuals(e.detail);
    renderSlotPillBars();
    renderHourlyLoadBars();
    renderWeeklyTrendBars();
    renderOccupancyChart();
    renderRoleCard(dashboardState.roleContext);
  });

  window.addEventListener('portal:theme-mode-changed', () => {
    updateThemeVisuals();
    renderSlotPillBars();
    renderHourlyLoadBars();
    renderWeeklyTrendBars();
    renderOccupancyChart();
    renderRoleCard(dashboardState.roleContext);
  });

  // --- 11. INITIALIZATION ---
  function initDashboard() {
    if (heroImg) {
      dashboardState.zone1Src = heroImg.src;
    }
    initSortableGrid();
    updateThemeVisuals();
    renderSlotPillBars();
    renderHourlyLoadBars();
    renderWeeklyTrendBars();
    renderOccupancyChart();
    renderRoleCard(dashboardState.roleContext);
  }

  document.addEventListener('DOMContentLoaded', initDashboard);

  if (document.readyState !== 'loading') {
    initDashboard();
  }

})();
