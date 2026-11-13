/**
 * Dashboard Controller
 * Smart Management Dashboard — AI Greenhouse Bay & Telemetry Visualizations
 *
 * Handles:
 * - Water Flow & Hydroponic Irrigation (16 interactive pill bars)
 * - Power Consumption & Load Breakdown (34 hourly profile bars + filters)
 * - 7-Day Biomass Growth Rate (56-bar histogram + day highlights)
 * - Stepped Air CO2 enrichment area chart
 * - Camera Zone switching (Cam 01 vs Cam 02)
 * - Dashboard KPI setpoints & interactive telemetry modals
 * - Synchronization with 'portal:theme-color-changed' and 'portal:search'
 */

(function () {
  'use strict';

  // --- 1. DASHBOARD STATE ---
  const dashboardState = {
    cameraZone: 1,
    zone1Src: '',
    zone2Src: 'data:image/webp;base64,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',
    harvestPercent: 80,
    harvestStatus: 'Good Growth',
    waterActiveBars: 8,
    waterSubLabelMode: 'soem',
    powerTotal: 144,
    powerLighting: 112,
    powerOther: 20,
    powerFilter: 'all',
    climateTemp: 23,
    airMoisture: 68,
    lightHours: 15,
    ecLevel: 2.1,
    growthRate: 0.70,
    growthHighlightDays: [2, 3],
    co2Ppm: 900,
    co2ActiveStep: 7,
    co2Steps: [
      { ppm: 620, time: '09:30', h: 36 },
      { ppm: 710, time: '10:45', h: 49 },
      { ppm: 785, time: '12:00', h: 61 },
      { ppm: 880, time: '13:15', h: 83 },
      { ppm: 850, time: '14:20', h: 76 },
      { ppm: 885, time: '15:30', h: 85 },
      { ppm: 835, time: '16:10', h: 71 },
      { ppm: 900, time: '16:47', h: 90 }
    ]
  };

  // Helper Toast Notification (Delegates to Universal Top-Right Bootstrap 5 Toast Engine)
  function showToast(message, type) {
    if (window.PortalToast && typeof window.PortalToast.show === 'function') {
      return window.PortalToast.show(message, type);
    }
    if (typeof window.showPortalToast === 'function') {
      return window.showPortalToast(message, type);
    }
  }

  // --- 2. WATER FLOW TELEMETRY (16 PILL BARS) ---
  function renderWaterFlowBars() {
    const container = document.getElementById('waterFlowBarsContainer');
    const valEl = document.getElementById('waterFlowValue');
    if (!container || !valEl) return;

    const liters = ((dashboardState.waterActiveBars / 8) * 6.75).toFixed(3);
    valEl.textContent = `${liters} L`;

    const totalBars = 16;
    const inactiveCount = totalBars - dashboardState.waterActiveBars;
    container.innerHTML = '';

    for (let i = 0; i < totalBars; i++) {
      const isGreen = i >= inactiveCount;
      const bar = document.createElement('button');
      bar.type = 'button';
      bar.className = 'flex-1 h-full flex items-center justify-center group focus:outline-none';
      bar.title = `Set water flow to ${totalBars - i} active bars (${(((totalBars - i) / 8) * 6.75).toFixed(3)} L)`;

      const pill = document.createElement('span');
      pill.className = 'w-[5px] sm:w-[6px] h-full rounded-full transition-all duration-200 group-hover:scale-y-105';
      pill.style.backgroundColor = isGreen ? 'var(--bar-active)' : 'var(--bar-inactive)';

      bar.appendChild(pill);
      bar.addEventListener('click', () => {
        dashboardState.waterActiveBars = totalBars - i;
        renderWaterFlowBars();
        showToast(`Water Flow updated to ${(((dashboardState.waterActiveBars) / 8) * 6.75).toFixed(3)} L`);
      });
      container.appendChild(bar);
    }
  }

  const waterSubTag = document.getElementById('waterSubTag');
  if (waterSubTag) {
    waterSubTag.addEventListener('click', () => {
      dashboardState.waterSubLabelMode = dashboardState.waterSubLabelMode === 'soem' ? 'zone' : 'soem';
      waterSubTag.textContent = dashboardState.waterSubLabelMode === 'soem' ? 'Soem 3.541' : 'Zone A: 3.541 L';
      showToast(`Sub-flow label: "${waterSubTag.textContent}"`);
    });
  }

  // --- 3. POWER CONSUMPTION HOURLY BARS (34 BARS) ---
  const powerBarData = [
    { h: 48, type: 'mid', time: '07:00' },
    { h: 68, type: 'mid', time: '07:10' },
    { h: 55, type: 'mid', time: '07:20' },
    { h: 86, type: 'mid', time: '07:30' },
    { h: 74, type: 'mid', time: '07:40' },
    { h: 62, type: 'mid', time: '07:50' },
    { h: 66, type: 'mid', time: '08:00' },
    { h: 50, type: 'mid', time: '08:10' },
    { h: 52, type: 'low', time: '08:20' },
    { h: 52, type: 'low', time: '08:35' },
    { h: 52, type: 'low', time: '08:50' },
    { h: 52, type: 'low', time: '09:00' },
    { h: 52, type: 'low', time: '09:15' },
    { h: 52, type: 'low', time: '09:30' },
    { h: 38, type: 'peak', time: '09:45' },
    { h: 65, type: 'peak', time: '09:55' },
    { h: 58, type: 'peak', time: '10:00' },
    { h: 82, type: 'peak', time: '10:10' },
    { h: 94, type: 'peak', time: '10:20' },
    { h: 72, type: 'peak', time: '10:30' },
    { h: 44, type: 'peak', time: '10:40' },
    { h: 52, type: 'low', time: '10:50' },
    { h: 52, type: 'low', time: '11:00' },
    { h: 52, type: 'low', time: '11:15' },
    { h: 52, type: 'low', time: '11:30' },
    { h: 52, type: 'low', time: '11:45' },
    { h: 52, type: 'low', time: '11:55' },
    { h: 42, type: 'green', time: '12:05' },
    { h: 70, type: 'green', time: '12:15' },
    { h: 92, type: 'green', time: '12:25' },
    { h: 66, type: 'green', time: '12:35' },
    { h: 96, type: 'green', time: '12:45' },
    { h: 78, type: 'green', time: '12:55' },
    { h: 52, type: 'green', time: '01:00' }
  ];

  function renderPowerBars() {
    const container = document.getElementById('powerChartBars');
    if (!container) return;
    container.innerHTML = '';

    powerBarData.forEach((item) => {
      const bar = document.createElement('div');
      bar.className = 'chart-bar flex-1 rounded-full cursor-pointer';
      bar.style.height = `${item.h}%`;

      let color = 'var(--bar-inactive)';
      if (item.type === 'mid') color = 'var(--bar-mid)';
      if (item.type === 'peak') color = 'var(--bar-peak)';
      if (item.type === 'green') color = 'var(--bar-active)';

      if (dashboardState.powerFilter === 'lighting' && item.type === 'low') {
        bar.style.opacity = '0.3';
      } else if (dashboardState.powerFilter === 'other' && item.type !== 'peak') {
        bar.style.opacity = '0.25';
      }

      bar.style.backgroundColor = color;
      bar.title = `${item.time} — ${(item.h * 1.6).toFixed(1)} kW`;
      bar.addEventListener('click', () => {
        showToast(`Power @ ${item.time}: ${(item.h * 1.6).toFixed(1)} kW`);
      });
      container.appendChild(bar);
    });
  }

  const filterLightingBtn = document.getElementById('filterPowerLightingBtn');
  const filterOtherBtn = document.getElementById('filterPowerOtherBtn');
  if (filterLightingBtn) {
    filterLightingBtn.addEventListener('click', () => {
      dashboardState.powerFilter = dashboardState.powerFilter === 'lighting' ? 'all' : 'lighting';
      renderPowerBars();
      showToast(dashboardState.powerFilter === 'lighting' ? 'Highlighting Lighting Power (112 kWh)' : 'Showing Total Power Consumption');
    });
  }
  if (filterOtherBtn) {
    filterOtherBtn.addEventListener('click', () => {
      dashboardState.powerFilter = dashboardState.powerFilter === 'other' ? 'all' : 'other';
      renderPowerBars();
      showToast(dashboardState.powerFilter === 'other' ? 'Highlighting HVAC/Other Load (20 kWh)' : 'Showing Total Power Consumption');
    });
  }

  // --- 4. BIOMASS 7-DAY GROWTH HISTOGRAM (56 BARS) ---
  const growthHeights = [
    38, 45, 58, 72, 60, 48, 36, 42,
    54, 65, 84, 70, 52, 40, 32, 46,
    38, 50, 44, 58, 62, 78, 86, 75,
    82, 92, 85, 98, 90, 80, 55, 62,
    48, 68, 82, 76, 68, 56, 45, 52,
    60, 44, 38, 55, 62, 50, 42, 35,
    40, 48, 42, 56, 64, 58, 50, 36
  ];
  const dayNames = ['July 20', 'July 21', 'July 22', 'July 23', 'July 24', 'July 25', 'July 26'];

  function renderGrowthBars() {
    const container = document.getElementById('growthChartBars');
    if (!container) return;
    container.innerHTML = '';

    growthHeights.forEach((h, idx) => {
      const dayIdx = Math.floor(idx / 8);
      const isHighlighted = (dashboardState.growthHighlightDays.includes(dayIdx) && idx >= 20 && idx <= 30) ||
        (dashboardState.growthHighlightDays.length === 1 && dashboardState.growthHighlightDays[0] === dayIdx);

      const bar = document.createElement('div');
      bar.className = 'chart-bar flex-1 rounded-t-full cursor-pointer';
      bar.style.height = `${h}%`;
      bar.style.backgroundColor = isHighlighted ? 'var(--bar-active)' : 'var(--bar-mid)';
      bar.style.opacity = isHighlighted ? '1' : '0.45';

      const rateVal = (0.45 + (h / 100) * 0.35).toFixed(2);
      bar.title = `${dayNames[dayIdx]} — Growth Rate: ${rateVal}`;

      bar.addEventListener('mouseenter', () => {
        const label = document.getElementById('growthHoverLabel');
        const mainVal = document.getElementById('growthRateValue');
        if (label) label.textContent = `${dayNames[dayIdx]} • ${rateVal} idx`;
        if (mainVal) mainVal.textContent = rateVal;
      });
      bar.addEventListener('mouseleave', () => {
        const label = document.getElementById('growthHoverLabel');
        const mainVal = document.getElementById('growthRateValue');
        if (label) label.textContent = 'Peak: July 22–23';
        if (mainVal) mainVal.textContent = dashboardState.growthRate.toFixed(2);
      });
      container.appendChild(bar);
    });
  }

  document.querySelectorAll('.growth-day-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const day = parseInt(btn.getAttribute('data-growth-day'), 10);
      dashboardState.growthHighlightDays = [day];
      document.querySelectorAll('.growth-day-btn').forEach(b => b.classList.remove('font-bold', 'text-[var(--color-primary)]'));
      btn.classList.add('font-bold', 'text-[var(--color-primary)]');
      renderGrowthBars();
      showToast(`Highlighting Growth Rate for ${dayNames[day]}`);
    });
  });

  // --- 5. STEPPED AIR CO2 DOSING CHART ---
  function renderCo2Chart() {
    const container = document.getElementById('co2StepsContainer');
    const ppmEl = document.getElementById('co2PpmValue');
    if (!container || !ppmEl) return;

    container.innerHTML = '';
    dashboardState.co2Steps.forEach((step, idx) => {
      const col = document.createElement('div');
      col.className = 'h-full flex flex-col justify-end cursor-pointer group relative';

      const box = document.createElement('div');
      box.className = 'w-full transition-all duration-300 relative overflow-hidden';
      box.style.height = `${step.h}%`;
      box.style.borderTop = `2.5px solid var(--bar-active)`;
      box.style.borderLeft = idx > 0 ? `1px solid rgba(var(--color-primary-rgb), 0.25)` : 'none';
      box.style.background = `repeating-linear-gradient(
        -45deg,
        rgba(var(--color-primary-rgb), 0.18),
        rgba(var(--color-primary-rgb), 0.18) 4px,
        rgba(var(--color-primary-rgb), 0.06) 4px,
        rgba(var(--color-primary-rgb), 0.06) 8px
      )`;

      if (idx === dashboardState.co2ActiveStep) {
        const badge = document.createElement('span');
        badge.className = 'absolute top-1.5 left-1/2 -translate-x-1/2 text-[9px] font-bold text-[var(--text-primary)] bg-white/85 dark:bg-black/80 px-1 py-0.5 rounded shadow-sm';
        badge.textContent = step.time;
        box.appendChild(badge);
      }

      col.appendChild(box);
      col.title = `${step.time} — ${step.ppm} ppm CO₂`;
      col.addEventListener('click', () => {
        dashboardState.co2ActiveStep = idx;
        dashboardState.co2Ppm = step.ppm;
        ppmEl.textContent = step.ppm;
        renderCo2Chart();
        showToast(`Air CO₂ @ ${step.time}: ${step.ppm} ppm`);
      });
      container.appendChild(col);
    });
  }

  // --- 6. CAMERA ZONE SWITCHER ---
  const heroCameraImg = document.getElementById('heroCameraImg');
  const switchCamZone1Btn = document.getElementById('switchCamZone1Btn');
  const switchCamZone2Btn = document.getElementById('switchCamZone2Btn');
  const activeZoneHeaderLabel = document.getElementById('activeZoneHeaderLabel');

  function setCameraZone(zoneNum) {
    dashboardState.cameraZone = zoneNum;
    if (!heroCameraImg) return;
    if (zoneNum === 1) {
      heroCameraImg.src = dashboardState.zone1Src;
      if (activeZoneHeaderLabel) activeZoneHeaderLabel.textContent = 'Zone A • Hydroponic Bay';
      if (switchCamZone1Btn) switchCamZone1Btn.className = 'px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5';
      if (switchCamZone2Btn) switchCamZone2Btn.className = 'px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 transition';
      showToast('Switched to Cam 01: Main Hydroponic Bay');
    } else {
      heroCameraImg.src = dashboardState.zone2Src;
      if (activeZoneHeaderLabel) activeZoneHeaderLabel.textContent = 'Zone B • North Aeroponic Wing';
      if (switchCamZone1Btn) switchCamZone1Btn.className = 'px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 transition';
      if (switchCamZone2Btn) switchCamZone2Btn.className = 'px-3 py-1 rounded-full bg-white/90 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5';
      showToast('Switched to Cam 02: North Wing Aeroponics');
    }
  }

  if (switchCamZone1Btn) switchCamZone1Btn.addEventListener('click', () => setCameraZone(1));
  if (switchCamZone2Btn) switchCamZone2Btn.addEventListener('click', () => setCameraZone(2));

  // --- 7. FILTER BANNER & CARD SEARCH ---
  const activeFilterBanner = document.getElementById('activeFilterBanner');
  const activeFilterText = document.getElementById('activeFilterText');
  const resetFilterBtn = document.getElementById('resetFilterBtn');

  function setCardFilter(category, label) {
    const cards = document.querySelectorAll('[data-category]');
    cards.forEach((card) => {
      const cats = card.getAttribute('data-category') || '';
      if (category === 'all' || cats.includes(category)) {
        card.classList.remove('card-dimmed');
      } else {
        card.classList.add('card-dimmed');
      }
    });

    if (category === 'all') {
      if (activeFilterBanner) {
        activeFilterBanner.classList.add('hidden');
        activeFilterBanner.classList.remove('flex');
      }
    } else {
      if (activeFilterBanner && activeFilterText) {
        activeFilterText.textContent = label || `Focused View: ${category}`;
        activeFilterBanner.classList.remove('hidden');
        activeFilterBanner.classList.add('flex');
      }
    }
  }

  if (resetFilterBtn) {
    resetFilterBtn.addEventListener('click', () => setCardFilter('all'));
  }

  // Real-time search dispatch from navbar
  window.addEventListener('portal:search', (e) => {
    const query = (e.detail?.query || '').toLowerCase().trim();
    const cards = document.querySelectorAll('[data-search]');
    let matchCount = 0;

    cards.forEach((card) => {
      const text = (card.getAttribute('data-search') || '').toLowerCase();
      if (!query || text.includes(query)) {
        card.classList.remove('card-dimmed');
        matchCount++;
      } else {
        card.classList.add('card-dimmed');
      }
    });

    if (query) {
      if (activeFilterBanner && activeFilterText) {
        activeFilterText.textContent = `Search: "${query}" (${matchCount} matching modules)`;
        activeFilterBanner.classList.remove('hidden');
        activeFilterBanner.classList.add('flex');
      }
    } else {
      if (activeFilterBanner) {
        activeFilterBanner.classList.add('hidden');
        activeFilterBanner.classList.remove('flex');
      }
    }
  });

  // --- 8. MODAL SYSTEM ---
  const modalOverlay = document.getElementById('genericModalOverlay');
  const modalTitle = document.getElementById('genericModalTitle');
  const modalSubtitle = document.getElementById('genericModalSubtitle');
  const modalBody = document.getElementById('genericModalBody');
  const closeModalBtn = document.getElementById('closeGenericModalBtn');

  function openModal(type) {
    if (!modalOverlay || !modalTitle || !modalSubtitle || !modalBody) return;
    modalOverlay.classList.remove('hidden');

    if (type === 'ai-assistant') {
      modalTitle.textContent = 'Greenhouse Autonomous Copilot';
      modalSubtitle.textContent = 'Gemini Agritech Agent • Realtime Autonomous Recommendations';
      modalBody.innerHTML = `
        <div class="space-y-3.5 text-xs">
          <div class="p-3.5 rounded-2xl glass-pill border border-[var(--color-primary)]/30">
            <div class="flex items-center gap-2 text-[var(--color-primary)] font-bold mb-1">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
              <span>Autonomous Crop Health Analysis</span>
            </div>
            <p class="text-[var(--text-secondary)] leading-relaxed">
              Canopy thermal imaging confirms <strong>80% vegetative leaf area index</strong> in Hydroponic Bay A. Transpiration is optimal with EC at 2.1 mS/cm.
            </p>
          </div>
          <div class="space-y-2">
            <p class="font-bold text-[var(--text-primary)]">Recommended Automated Directives:</p>
            <button type="button" class="w-full text-left p-3 rounded-xl glass-pill hover:border-[var(--color-primary)] flex justify-between items-center transition" onclick="showToast('Optimizing Photoperiod lighting schedule'); document.getElementById('genericModalOverlay').classList.add('hidden');">
              <span>Reduce photoperiod by 30 mins during peak grid pricing</span>
              <span class="font-bold text-[var(--color-primary)]">Run</span>
            </button>
            <button type="button" class="w-full text-left p-3 rounded-xl glass-pill hover:border-[var(--color-primary)] flex justify-between items-center transition" onclick="showToast('Initiating closed-loop nutrient flush'); document.getElementById('genericModalOverlay').classList.add('hidden');">
              <span>Recirculate 6.75 L nutrient solution through filters</span>
              <span class="font-bold text-[var(--color-primary)]">Run</span>
            </button>
          </div>
        </div>
      `;
    } else if (type === 'setpoints') {
      modalTitle.textContent = 'Environmental & Nutrient Setpoint Controls';
      modalSubtitle.textContent = 'Adjust live greenhouse targets for Climate, Moisture, Light & EC';
      modalBody.innerHTML = `
        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Indoor Climate (°C)</span>
              <span id="sliderTempLabel" class="text-[var(--color-primary)]">${dashboardState.climateTemp} °C</span>
            </div>
            <input id="sliderTemp" type="range" min="16" max="32" step="1" value="${dashboardState.climateTemp}" class="w-full accent-[var(--color-primary)]" />
          </div>
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Air Moisture (%)</span>
              <span id="sliderMoistureLabel" class="text-[var(--color-primary)]">${dashboardState.airMoisture} %</span>
            </div>
            <input id="sliderMoisture" type="range" min="40" max="90" step="1" value="${dashboardState.airMoisture}" class="w-full accent-[var(--color-primary)]" />
          </div>
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Photoperiod Light Level (Hours)</span>
              <span id="sliderLightLabel" class="text-[var(--color-primary)]">${dashboardState.lightHours} H</span>
            </div>
            <input id="sliderLight" type="range" min="8" max="20" step="1" value="${dashboardState.lightHours}" class="w-full accent-[var(--color-primary)]" />
          </div>
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Nutrient EC Level (mS/cm)</span>
              <span id="sliderEcLabel" class="text-[var(--color-primary)]">${dashboardState.ecLevel} mS</span>
            </div>
            <input id="sliderEc" type="range" min="1.0" max="3.5" step="0.1" value="${dashboardState.ecLevel}" class="w-full accent-[var(--color-primary)]" />
          </div>
          <button id="saveSetpointsBtn" type="button" class="w-full py-2.5 rounded-full bg-[var(--color-primary)] text-white font-bold text-xs shadow-md hover:opacity-95 transition">
            Apply Setpoints to Greenhouse
          </button>
        </div>
      `;
      const sTemp = document.getElementById('sliderTemp');
      const sMoist = document.getElementById('sliderMoisture');
      const sLight = document.getElementById('sliderLight');
      const sEc = document.getElementById('sliderEc');

      sTemp.addEventListener('input', (e) => { document.getElementById('sliderTempLabel').textContent = `${e.target.value} °C`; });
      sMoist.addEventListener('input', (e) => { document.getElementById('sliderMoistureLabel').textContent = `${e.target.value} %`; });
      sLight.addEventListener('input', (e) => { document.getElementById('sliderLightLabel').textContent = `${e.target.value} H`; });
      sEc.addEventListener('input', (e) => { document.getElementById('sliderEcLabel').textContent = `${parseFloat(e.target.value).toFixed(1)} mS`; });

      document.getElementById('saveSetpointsBtn').addEventListener('click', () => {
        dashboardState.climateTemp = parseInt(sTemp.value, 10);
        dashboardState.airMoisture = parseInt(sMoist.value, 10);
        dashboardState.lightHours = parseInt(sLight.value, 10);
        dashboardState.ecLevel = parseFloat(sEc.value).toFixed(1);
        const kTemp = document.getElementById('kpiTempVal');
        const kMoist = document.getElementById('kpiMoistureVal');
        const kLight = document.getElementById('kpiLightVal');
        const kEc = document.getElementById('kpiEcVal');
        if (kTemp) kTemp.textContent = dashboardState.climateTemp;
        if (kMoist) kMoist.textContent = dashboardState.airMoisture;
        if (kLight) kLight.textContent = dashboardState.lightHours;
        if (kEc) kEc.textContent = dashboardState.ecLevel;
        modalOverlay.classList.add('hidden');
        showToast('Updated Indoor Climate, Moisture, Light & EC setpoints');
      });
    } else if (type === 'harvest') {
      modalTitle.textContent = 'Harvest AI & Crop Growth Analytics';
      modalSubtitle.textContent = 'Computer vision canopy scan & 7-day biomass velocity';
      modalBody.innerHTML = `
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-2xl glass-pill">
              <p class="text-[11px] text-[var(--text-muted)]">Harvest Readiness</p>
              <p class="text-xl font-bold text-[var(--color-primary)] mt-0.5">${dashboardState.harvestPercent}% (${dashboardState.harvestStatus})</p>
            </div>
            <div class="p-3 rounded-2xl glass-pill">
              <p class="text-[11px] text-[var(--text-muted)]">Growth Rate Index</p>
              <p class="text-xl font-bold text-[var(--text-primary)] mt-0.5">${dashboardState.growthRate} <span class="text-xs font-normal">g/m²/h</span></p>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Simulate Crop Maturity (%)</span>
              <span id="simHarvestLabel" class="text-[var(--color-primary)]">${dashboardState.harvestPercent}%</span>
            </div>
            <input id="simHarvestSlider" type="range" min="45" max="100" value="${dashboardState.harvestPercent}" class="w-full accent-[var(--color-primary)]" />
          </div>
          <div class="flex gap-2">
            <button id="modalCam1Btn" type="button" class="flex-1 py-2 rounded-xl glass-pill text-xs font-bold">View Cam 01 (Main Bay)</button>
            <button id="modalCam2Btn" type="button" class="flex-1 py-2 rounded-xl glass-pill text-xs font-bold">View Cam 02 (North Wing)</button>
          </div>
        </div>
      `;
      const simSlider = document.getElementById('simHarvestSlider');
      simSlider.addEventListener('input', (e) => {
        const val = parseInt(e.target.value, 10);
        dashboardState.harvestPercent = val;
        dashboardState.harvestStatus = val >= 85 ? 'Ready to Harvest' : val >= 70 ? 'Good Growth' : 'Vegetative Stage';
        document.getElementById('simHarvestLabel').textContent = `${val}%`;
        const hp = document.getElementById('harvestPercentValue');
        const hs = document.getElementById('harvestStatusLabel');
        if (hp) hp.textContent = `${val}%`;
        if (hs) hs.textContent = dashboardState.harvestStatus;
      });
      document.getElementById('modalCam1Btn').addEventListener('click', () => { setCameraZone(1); modalOverlay.classList.add('hidden'); });
      document.getElementById('modalCam2Btn').addEventListener('click', () => { setCameraZone(2); modalOverlay.classList.add('hidden'); });
    } else if (type === 'water') {
      modalTitle.textContent = 'Water Flow & Hydroponic Irrigation';
      modalSubtitle.textContent = 'Closed-loop nutrient recirculation telemetry';
      modalBody.innerHTML = `
        <div class="space-y-4">
          <p class="text-xs text-[var(--text-secondary)]">Click any bar directly on the Water Flow widget or use the slider below to adjust active manifold valves (16 channels):</p>
          <div>
            <div class="flex justify-between text-xs font-bold mb-1">
              <span>Active Valve Channels (${dashboardState.waterActiveBars} / 16)</span>
              <span id="modalWaterLabel" class="text-[var(--color-primary)]">${((dashboardState.waterActiveBars / 8) * 6.75).toFixed(3)} L</span>
            </div>
            <input id="modalWaterSlider" type="range" min="1" max="16" value="${dashboardState.waterActiveBars}" class="w-full accent-[var(--color-primary)]" />
          </div>
        </div>
      `;
      document.getElementById('modalWaterSlider').addEventListener('input', (e) => {
        dashboardState.waterActiveBars = parseInt(e.target.value, 10);
        renderWaterFlowBars();
        document.getElementById('modalWaterLabel').textContent = `${((dashboardState.waterActiveBars / 8) * 6.75).toFixed(3)} L`;
      });
    } else if (type === 'power') {
      modalTitle.textContent = 'Power Consumption & Load Breakdown';
      modalSubtitle.textContent = 'Hourly energy profile (07:00 – 01:00)';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-2 text-center">
            <div class="p-2.5 rounded-xl glass-pill">
              <p class="text-[var(--text-muted)]">Headline Total</p>
              <p class="text-base font-bold mt-0.5">${dashboardState.powerTotal} kWh</p>
            </div>
            <div class="p-2.5 rounded-xl glass-pill">
              <p class="text-[var(--text-muted)]">LED Lighting</p>
              <p class="text-base font-bold text-[var(--color-primary)] mt-0.5">${dashboardState.powerLighting} kWh</p>
            </div>
            <div class="p-2.5 rounded-xl glass-pill">
              <p class="text-[var(--text-muted)]">HVAC & Pumps</p>
              <p class="text-base font-bold text-[var(--color-alert)] mt-0.5">${dashboardState.powerOther} kWh</p>
            </div>
          </div>
          <p class="text-[var(--text-muted)] leading-relaxed">
            Peak draw occurs between <strong>09:45 and 10:40</strong> when overhead grow lights and dehumidifiers run concurrently.
          </p>
        </div>
      `;
    } else if (type === 'co2') {
      modalTitle.textContent = 'Air CO₂ Enrichment Schedule';
      modalSubtitle.textContent = 'Stepped photosynthetic carbon dosing (400 – 900 ppm)';
      modalBody.innerHTML = `
        <div class="space-y-3 text-xs">
          <p class="text-[var(--text-secondary)]">Select a stepped timestamp to inspect or set current CO₂ concentration:</p>
          <div class="grid grid-cols-4 gap-2">
            ${dashboardState.co2Steps.map((s, idx) => `
              <button type="button" data-co2-step="${idx}" class="p-2 rounded-xl glass-pill text-center hover:border-[var(--color-primary)] ${idx === dashboardState.co2ActiveStep ? 'ring-2 ring-[var(--color-primary)] font-bold' : ''}">
                <span class="block text-[10px] text-[var(--text-muted)]">${s.time}</span>
                <span class="text-xs font-bold">${s.ppm} ppm</span>
              </button>
            `).join('')}
          </div>
        </div>
      `;
      modalBody.querySelectorAll('[data-co2-step]').forEach((b) => {
        b.addEventListener('click', () => {
          const idx = parseInt(b.getAttribute('data-co2-step'), 10);
          dashboardState.co2ActiveStep = idx;
          dashboardState.co2Ppm = dashboardState.co2Steps[idx].ppm;
          const co2El = document.getElementById('co2PpmValue');
          if (co2El) co2El.textContent = dashboardState.co2Ppm;
          renderCo2Chart();
          modalOverlay.classList.add('hidden');
          showToast(`CO₂ readout set to ${dashboardState.co2Ppm} ppm (${dashboardState.co2Steps[idx].time})`);
        });
      });
    } else if (type === 'settings') {
      modalTitle.textContent = 'Theme Color Studio & Settings';
      modalSubtitle.textContent = 'Customize primary brand color, chart palette & appearance';
      modalBody.innerHTML = `
        <div class="space-y-4 text-xs">
          <div class="p-3.5 rounded-2xl glass-pill flex items-center justify-between">
            <div>
              <p class="font-bold text-sm text-[var(--text-primary)]">Custom Palette Picker</p>
              <p class="text-[11px] text-[var(--text-muted)]">Live accent updates across all cards</p>
            </div>
            <input id="modalCustomColorPicker" type="color" value="${localStorage.getItem('portal_theme_color') || '#7C3AED'}" class="w-10 h-10 rounded-xl cursor-pointer border-0 bg-transparent" />
          </div>
          <div>
            <p class="font-bold mb-2">Appearance Mode</p>
            <div class="grid grid-cols-2 gap-2">
              <button type="button" id="setModeLight" class="py-2.5 px-3 rounded-xl glass-pill font-bold hover:border-[var(--color-primary)]">Glass Light</button>
              <button type="button" id="setModeDark" class="py-2.5 px-3 rounded-xl glass-pill font-bold hover:border-[var(--color-primary)]">Dark Glass</button>
            </div>
          </div>
        </div>
      `;
      document.getElementById('modalCustomColorPicker').addEventListener('input', (e) => {
        if (typeof window.applyCustomColorPalette === 'function') {
          window.applyCustomColorPalette(e.target.value);
        }
      });
      document.getElementById('setModeLight').addEventListener('click', () => {
        if (typeof window.applyTheme === 'function') window.applyTheme('light');
        modalOverlay.classList.add('hidden');
      });
      document.getElementById('setModeDark').addEventListener('click', () => {
        if (typeof window.applyTheme === 'function') window.applyTheme('dark');
        modalOverlay.classList.add('hidden');
      });
    } else if (type === 'profile') {
      const uName = window.PORTAL_USER?.name || 'Faculty Member';
      const uRole = window.PORTAL_USER?.role === 'super_admin' ? 'Super Administrator' : (window.PORTAL_USER?.designation || 'Faculty Staff');
      const uEmail = window.PORTAL_USER?.email || '';
      const uDept = window.PORTAL_USER?.department || 'IT & CE Department';
      const uInitials = window.PORTAL_USER?.initials || uName.replace(/[^A-Za-z]/g, '').slice(0, 2).toUpperCase() || 'FM';

      modalTitle.textContent = `${uName} — Faculty Profile`;
      modalSubtitle.textContent = `CE & IT Academic Portal • ${uRole}`;
      modalBody.innerHTML = `
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg ring-2 ring-[var(--color-primary)] shrink-0 shadow-sm">
            ${uInitials}
          </div>
          <div class="text-xs">
            <p class="font-bold text-sm text-[var(--text-primary)]">${uName}</p>
            <p class="text-[var(--text-muted)] mt-0.5">${uRole} &bull; ${uDept}</p>
            ${uEmail ? `<p class="text-[var(--color-primary)] font-medium mt-1 font-mono">${uEmail}</p>` : ''}
            <div class="flex items-center gap-2 mt-2">
              <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold text-[10px]">Authenticated Session</span>
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

  document.querySelectorAll('[data-modal-open]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.stopPropagation();
      openModal(el.getAttribute('data-modal-open'));
    });
  });

  ['aiAssistantBtn', 'mobileAiAssistantBtn', 'drawerAiAssistantBtn'].forEach((id) => {
    const btn = document.getElementById(id);
    if (btn) {
      btn.addEventListener('click', () => {
        if (typeof window.closeMobileSidebar === 'function') window.closeMobileSidebar();
        openModal('ai-assistant');
      });
    }
  });

  const openSettingsBtn = document.getElementById('openSettingsBtn');
  if (openSettingsBtn) openSettingsBtn.addEventListener('click', () => openModal('settings'));

  const openProfileBtn = document.getElementById('openProfileBtn');
  if (openProfileBtn) openProfileBtn.addEventListener('click', () => openModal('profile'));

  // Voice command modal
  const voiceModal = document.getElementById('voiceModal');
  const closeVoiceModalBtn = document.getElementById('closeVoiceModalBtn');
  if (closeVoiceModalBtn && voiceModal) {
    closeVoiceModalBtn.addEventListener('click', () => voiceModal.classList.add('hidden'));
  }
  document.querySelectorAll('[data-voice-cmd]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const cmd = btn.getAttribute('data-voice-cmd');
      if (voiceModal) voiceModal.classList.add('hidden');
      if (cmd === 'optimize-power') {
        dashboardState.powerFilter = 'lighting';
        renderPowerBars();
        showToast('Voice Executed: Lighting optimization applied');
      } else if (cmd === 'boost-water') {
        dashboardState.waterActiveBars = 16;
        renderWaterFlowBars();
        showToast('Voice Executed: Water Flow boosted to 13.5 L');
      } else if (cmd === 'cool-climate') {
        dashboardState.climateTemp = 21;
        dashboardState.airMoisture = 72;
        const kt = document.getElementById('kpiTempVal');
        const km = document.getElementById('kpiMoistureVal');
        if (kt) kt.textContent = '21';
        if (km) km.textContent = '72';
        showToast('Voice Executed: Climate set to 21 °C & 72% Moisture');
      }
    });
  });

  // --- 9. THEME AND COLOR SYNCHRONIZATION ---
  window.addEventListener('portal:theme-color-changed', () => {
    renderWaterFlowBars();
    renderPowerBars();
    renderGrowthBars();
    renderCo2Chart();
  });

  window.addEventListener('portal:theme-mode-changed', () => {
    renderWaterFlowBars();
    renderPowerBars();
    renderGrowthBars();
    renderCo2Chart();
  });

  // --- 10. INITIALIZATION ---
  document.addEventListener('DOMContentLoaded', () => {
    const heroEl = document.getElementById('heroCameraImg');
    if (heroEl) {
      dashboardState.zone1Src = heroEl.src;
    }
    renderWaterFlowBars();
    renderPowerBars();
    renderGrowthBars();
    renderCo2Chart();
  });

  // Fallback immediate render if DOM is already parsed
  if (document.readyState !== 'loading') {
    const heroEl = document.getElementById('heroCameraImg');
    if (heroEl) {
      dashboardState.zone1Src = heroEl.src;
    }
    renderWaterFlowBars();
    renderPowerBars();
    renderGrowthBars();
    renderCo2Chart();
  }

})();

