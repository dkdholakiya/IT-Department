<?php
$page_title = 'Engineering Dept CE & IT — Smart Management Dashboard';
$page_css = ['assets/css/dashboard.css'];
include dirname(__DIR__) . '/partials/dashboard-head.php';

$metrics = $dashboardData['metrics'] ?? [];
$chartData = $dashboardData['chartData'] ?? [];
$roleContext = $dashboardData['roleContext'] ?? [];
$moduleSummaries = $dashboardData['moduleSummaries'] ?? ($metrics['moduleSummaries'] ?? []);
$activeRole = $dashboardData['activeRole'] ?? ($currentUser['role'] ?? 'super_admin');
$currentDay = $dashboardData['currentDay'] ?? 'MON';
$facultyList = $dashboardData['facultyList'] ?? [];
$canCheck = class_exists('\Core\Auth');

$userCan = function(string $perm) use ($currentUser, $canCheck): bool {
    if (!$canCheck) return true;
    return \Core\Auth::can($perm, $currentUser);
};
?>

  <!-- MAIN WRAPPER: Fixed 100vh on Desktop, Smooth Scroll on Mobile -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-5 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-3.5 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <?php
    $active_page = 'dashboard';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Smart Dashboard';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: FIXED TOP NAVBAR + SCROLLABLE BENTO GRID WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-3 lg:gap-3.5 h-full">

      <!-- FIXED / STICKY TOP NAVBAR (Permanently fixed in place, does NOT scroll) -->
      <header class="shrink-0 z-30">
        <?php
        $nav_title = 'Engineering Dept CE & IT';
        $nav_subtitle = 'CE & IT Academic Portal';
        $nav_search_placeholder = 'Search metrics, faculty, schedules, modules...';
        include dirname(__DIR__) . '/partials/navbar.php';
        ?>
      </header>

      <!-- SCROLLABLE BENTO GRID WORKSPACE (Cards scroll smoothly beneath the fixed navbar) -->
      <div id="dashboardWorkspace" class="flex-1 min-h-0 min-w-0 overflow-y-auto overflow-x-hidden pr-0.5 custom-scroll pb-4">
        <main id="bentoGrid" class="dashboard-sortable-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3.5 transition-all duration-300">

        <!-- ================= CARD 1: LIVE GREENHOUSE CAMERA FEED (PERMANENTLY PINNED & LOCKED IN SLOT 1) ================= -->
        <section
          id="cardHero"
          data-id="cardHero"
          data-category="growth zones"
          data-search="camera greenhouse lettuce zone cam 01 02 live feed video optical canopy vision"
          class="locked-card dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] relative overflow-hidden glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-lg"
        >
          <!-- Camera Zone Background Image -->
          <img
            id="heroCameraImg"
            src="<?php echo function_exists('v_asset') ? v_asset('assets/images/camera-hero.webp') : 'assets/images/camera-hero.webp'; ?>"
            alt="Live Smart Greenhouse Hydroponic Lettuce Feed"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
          />

          <!-- Subtle dark gradient vignette for readable controls & text -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-black/50 pointer-events-none"></div>

          <!-- Top Header Overlay: Live Indicator + Pinned Badge + Zone Switcher -->
          <div class="relative z-10 flex items-center justify-between gap-1.5 shrink-0">
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md border border-white/20 text-white text-[10px] font-bold tracking-wider uppercase shadow-sm">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
              <span>Live Feed</span>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="locked-badge" title="This first card is permanently fixed in place and cannot be moved">
                <svg class="w-2.5 h-2.5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                </svg>
                <span>Fixed Slot</span>
              </span>

              <button id="switchCamZone1Btn" type="button" class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-md text-[#0A0909] text-[11px] font-bold shadow-sm border border-white flex items-center gap-1.5 transition">
                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-primary)]"></span>
                <span>Cam 01</span>
              </button>
              <button id="switchCamZone2Btn" type="button" class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-[11px] font-semibold hover:bg-black/80 border border-white/10 transition">
                Cam 02
              </button>
            </div>
          </div>

          <!-- Bottom Overlay: Active Zone & Optical AI Sensor Metadata -->
          <div class="relative z-10 flex items-end justify-between gap-2 pt-2 text-white shrink-0">
            <div class="min-w-0">
              <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary-light)] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span id="activeZoneHeaderLabel" class="text-xs font-bold text-white truncate drop-shadow-sm">Zone A • Hydroponic Bay</span>
              </div>
              <p class="text-[10px] text-white/75 truncate mt-0.5">Lettuce Canopy Vision • 30 FPS Stream</p>
            </div>
            <button type="button" data-modal-open="harvest" class="px-2.5 py-1 rounded-lg bg-white/20 hover:bg-white/30 backdrop-blur-md text-white text-[10px] font-bold tracking-wide transition border border-white/20 flex items-center gap-1 shrink-0" title="Inspect Optical Telemetry">
              <span>View</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </section>

        <!-- ================= CARD 2: ACADEMIC WORKLOAD & TIMETABLE FULFILLMENT GAUGE (MOVEABLE) ================= -->
        <?php if ($userCan('workload.view') || $userCan('timetable.view')): ?>
        <section
          id="cardHarvest"
          data-id="cardHarvest"
          data-category="academics workload"
          data-search="harvest workload fulfillment timetable readiness academic 96% good allocation"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <!-- Card Header -->
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)] leading-tight">Workload Fulfillment</h2>
                <p class="text-[10px] text-[var(--text-muted)] leading-none mt-0.5">Department Academic Allotment</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="harvest" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Full Workload Analytics">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Semi-Circular Radial SVG Gauge -->
          <div class="relative flex flex-col items-center justify-center my-auto py-1">
            <svg class="w-[185px] h-[98px] overflow-visible" viewBox="0 0 200 115">
              <defs>
                <linearGradient id="academicGaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop id="gradStop1" offset="0%" stop-color="var(--color-primary-light, #A78BFA)" />
                  <stop id="gradStop2" offset="50%" stop-color="var(--color-primary, #7C3AED)" />
                  <stop id="gradStop3" offset="100%" stop-color="var(--color-primary-dark, #5B21B6)" />
                </linearGradient>
              </defs>
              <path
                d="M 18 102 A 82 82 0 0 1 182 102"
                fill="none"
                stroke="var(--glass-border)"
                stroke-width="7"
                stroke-linecap="round"
              />
              <path
                id="harvestGaugePill"
                d="M 28 88 A 82 82 0 0 1 172 88"
                fill="none"
                stroke="url(#academicGaugeGrad)"
                stroke-width="16"
                stroke-linecap="round"
              />
            </svg>

            <div class="-mt-11 text-center">
              <div id="harvestPercentValue" class="text-[26px] font-bold tracking-tight text-[var(--text-primary)] leading-none">
                <?php echo $metrics['allotmentPercent'] ?? 96; ?>%
              </div>
              <div id="harvestStatusLabel" class="text-[11px] font-semibold text-[var(--color-primary)] mt-1">Optimal Allotment</div>
            </div>
          </div>

          <!-- Bottom Metric Pills -->
          <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[var(--glass-border)] shrink-0">
            <div class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
              <span class="text-[10px] text-[var(--text-muted)] font-medium">Weekly Total</span>
              <span class="text-[11px] font-bold text-[var(--text-primary)]"><?php echo $metrics['totalWeeklyHours'] ?? 826; ?> Hrs</span>
            </div>
            <div class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between">
              <span class="text-[10px] text-[var(--text-muted)] font-medium">Staff Synced</span>
              <span class="text-[11px] font-bold text-[var(--color-primary)]"><?php echo $metrics['totalSyncedFaculties'] ?? 51; ?> / <?php echo $metrics['activeFaculty'] ?? 52; ?></span>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 3: TODAY'S TIME-SLOT SCHEDULE ACTIVITY (16 PILL BARS - MOVEABLE) ================= -->
        <?php if ($userCan('timetable.view') || $userCan('dashboard.view')): ?>
        <section
          id="cardWaterFlow"
          data-id="cardWaterFlow"
          data-category="academics timetable"
          data-search="daily slot activity timetable lectures sessions periods"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Daily Slot Activity</h2>
                <p class="text-[10px] text-[var(--text-muted)]">16 Academic Period Shifts</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="water" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Time Slots">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="mt-1 flex items-end justify-between relative shrink-0">
            <div>
              <div id="waterFlowValue" class="text-[22px] xl:text-[25px] font-bold tracking-tight text-[var(--text-primary)] leading-none">
                <?php echo $metrics['todayTotalLectures'] ?? 160; ?> Slots
              </div>
              <p class="text-[10px] text-[var(--text-muted)] mt-0.5">Active Academic Periods Today</p>
            </div>
            <div id="waterSubTag" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-semibold text-[var(--text-secondary)] shadow-sm cursor-pointer hover:border-[var(--color-primary)] transition" title="Click to toggle Shift label">
              <?php echo $currentDay; ?> Shift
            </div>
          </div>

          <!-- 16 Interactive Pill Bars -->
          <div id="waterFlowBarsContainer" class="mt-2 flex-1 min-h-[52px] max-h-[72px] flex items-stretch justify-between gap-1.5 pt-0.5" role="slider" aria-label="Slot Activity Bars">
            <!-- Populated dynamically via JS -->
          </div>

          <div class="flex items-center justify-between text-[9px] text-[var(--text-muted)] font-medium pt-1 shrink-0">
            <span>07:30 Morning</span>
            <span>12:00 Recess</span>
            <span>06:00 Evening</span>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 4: HOURLY CLASSROOM & LAB LOAD (34 BARS - MOVEABLE) ================= -->
        <?php if ($userCan('timetable.view') || $userCan('dashboard.view')): ?>
        <section
          id="cardPower"
          data-id="cardPower"
          data-category="academics load"
          data-search="hourly classroom load lecture theory practical lab power 34 bars"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Hourly Classroom Load</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Theory vs Practical Density</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="power" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Hourly Load Breakdown">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="mt-1 flex flex-wrap items-center justify-between gap-1 shrink-0">
            <div class="flex items-baseline gap-1">
              <span id="powerTotalValue" class="text-[22px] xl:text-[25px] font-bold tracking-tight text-[var(--text-primary)] leading-none">
                <?php echo $metrics['todayTotalLectures'] ?? 160; ?>
              </span>
              <span class="text-[11px] font-semibold text-[var(--text-secondary)]">Classes</span>
            </div>
            <div class="flex items-center gap-1 flex-wrap">
              <button id="filterPowerLightingBtn" type="button" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-medium text-[var(--text-secondary)] hover:border-[var(--color-primary)] transition">
                Theory: <strong id="powerLightingVal" class="font-bold text-[var(--text-primary)]"><?php echo round(($metrics['todayTotalLectures'] ?? 160) * 0.58); ?></strong>
              </button>
              <button id="filterPowerOtherBtn" type="button" class="px-2 py-0.5 rounded-full drawer-card text-[10px] font-medium text-[var(--text-secondary)] hover:border-[var(--color-alert)] transition">
                Lab: <strong id="powerOtherVal" class="font-bold text-[var(--text-primary)]"><?php echo round(($metrics['todayTotalLectures'] ?? 160) * 0.42); ?></strong>
              </button>
            </div>
          </div>

          <!-- 34-Bar Histogram -->
          <div class="mt-1.5 flex-1 min-h-0 flex flex-col justify-end">
            <div id="powerChartBars" class="flex-1 min-h-[44px] max-h-[64px] flex items-end justify-between gap-[2px]">
              <!-- Populated by JS -->
            </div>
            <div class="flex items-center justify-between text-[9.5px] font-medium text-[var(--text-muted)] mt-1 px-0.5 shrink-0">
              <span>07:00</span>
              <span>08:00</span>
              <span>09:00</span>
              <span>10:00</span>
              <span>11:00</span>
              <span>12:00</span>
              <span>01:00</span>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 5: CORE ACADEMIC MICRO-KPIS (2x2 CLUSTER - MOVEABLE) ================= -->
        <?php if ($userCan('dashboard.view') || $userCan('timetable.view')): ?>
        <section
          id="cardMicroKpis"
          data-id="cardMicroKpis"
          data-category="academics kpi faculty workload"
          data-search="core kpi faculty staff teaching workload divisions audit compliance"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0 mb-1.5">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
              </div>
              <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Academic Metrics Hub</h2>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="setpoints" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Academic Thresholds">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- 2x2 Clean Micro-Tiles Grid -->
          <div class="grid grid-cols-2 grid-rows-2 gap-2 flex-1">
            <!-- Tile 1: Total Faculty -->
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex flex-col justify-between hover:border-[var(--color-primary)] transition cursor-pointer" onclick="window.location.href='<?php echo htmlspecialchars($baseUrl); ?>faculty'">
              <div class="flex items-center justify-between">
                <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase tracking-wider">Faculty Staff</span>
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
              </div>
              <div class="mt-1">
                <span class="text-[20px] font-bold text-[var(--text-primary)] leading-none"><?php echo $metrics['totalFaculty'] ?? 54; ?></span>
                <p class="text-[9.5px] text-[var(--text-secondary)] mt-0.5"><?php echo $metrics['activeFaculty'] ?? 52; ?> Active Members</p>
              </div>
            </div>

            <!-- Tile 2: Teaching Workload -->
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex flex-col justify-between hover:border-[var(--color-primary)] transition cursor-pointer" onclick="window.location.href='<?php echo htmlspecialchars($baseUrl); ?>workload-summary'">
              <div class="flex items-center justify-between">
                <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase tracking-wider">Avg Workload</span>
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary-light)]"></span>
              </div>
              <div class="mt-1">
                <span class="text-[20px] font-bold text-[var(--text-primary)] leading-none"><?php echo $metrics['avgWorkloadPerStaff'] ?? 16.4; ?></span>
                <span class="text-[10px] font-semibold text-[var(--text-muted)]">Hrs/Wk</span>
                <p class="text-[9.5px] text-[var(--text-secondary)] mt-0.5">100% Target Met</p>
              </div>
            </div>

            <!-- Tile 3: Student Divisions -->
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex flex-col justify-between hover:border-[var(--color-primary)] transition cursor-pointer" onclick="window.location.href='<?php echo htmlspecialchars($baseUrl); ?>student-timetable'">
              <div class="flex items-center justify-between">
                <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase tracking-wider">Divisions</span>
                <span class="w-2 h-2 rounded-full bg-[var(--color-primary-dark)]"></span>
              </div>
              <div class="mt-1">
                <span class="text-[20px] font-bold text-[var(--text-primary)] leading-none"><?php echo $metrics['activeStudentDivisions'] ?? 33; ?></span>
                <span class="text-[10px] font-semibold text-[var(--text-muted)]">Classes</span>
                <p class="text-[9.5px] text-[var(--text-secondary)] mt-0.5">CE, IT, Dip, B.Tech</p>
              </div>
            </div>

            <!-- Tile 4: Audit Compliance -->
            <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex flex-col justify-between hover:border-[var(--color-primary)] transition cursor-pointer" onclick="window.location.href='<?php echo htmlspecialchars($baseUrl); ?>report'">
              <div class="flex items-center justify-between">
                <span class="text-[10px] text-[var(--text-muted)] font-semibold uppercase tracking-wider">Compliance</span>
                <span class="w-2 h-2 rounded-full bg-[var(--text-accent)]"></span>
              </div>
              <div class="mt-1">
                <span class="text-[20px] font-bold text-[var(--color-primary)] leading-none"><?php echo $metrics['auditCompliancePercent'] ?? 100; ?>%</span>
                <p class="text-[9.5px] text-[var(--text-secondary)] mt-0.5"><?php echo $metrics['zeroStudentAlerts'] ?? 0; ?> Zero-Student Flags</p>
              </div>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 6: WEEKLY WORKLOAD & LECTURE DENSITY TREND (56 BARS - MOVEABLE) ================= -->
        <?php if ($userCan('timetable.view') || $userCan('dashboard.view')): ?>
        <section
          id="cardGrowthRate"
          data-id="cardGrowthRate"
          data-category="academics workload"
          data-search="weekly workload trend density histogram monday tuesday wednesday thursday friday saturday"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                  <polyline points="16 7 22 7 22 13"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Weekly Workload Trend</h2>
                <p class="text-[10px] text-[var(--text-muted)]">6-Day Lecture Density Histogram</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="harvest" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Weekly Trend">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-baseline justify-between mt-1 shrink-0">
            <div id="growthRateValue" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">
              <?php echo round(($metrics['totalWeeklyHours'] ?? 826) / 6); ?>
              <span class="text-xs font-semibold text-[var(--text-secondary)]">Avg/Day</span>
            </div>
            <span id="growthHoverLabel" class="text-[10.5px] font-semibold text-[var(--text-accent)]">Peak Load: TUE–THU</span>
          </div>

          <!-- 56-Bar High-Density Histogram -->
          <div class="mt-2 flex-1 min-h-0 flex flex-col justify-end">
            <div id="growthChartBars" class="flex-1 min-h-[64px] max-h-[85px] flex items-end justify-between gap-[2px]">
              <!-- Populated by JS -->
            </div>
            <div class="flex items-center justify-between text-[9.5px] font-medium text-[var(--text-muted)] mt-1.5 shrink-0">
              <button type="button" data-growth-day="0" class="growth-day-btn hover:text-[var(--text-primary)]">MON</button>
              <button type="button" data-growth-day="1" class="growth-day-btn hover:text-[var(--text-primary)]">TUE</button>
              <button type="button" data-growth-day="2" class="growth-day-btn font-bold text-[var(--color-primary)]">WED</button>
              <button type="button" data-growth-day="3" class="growth-day-btn font-bold text-[var(--color-primary)]">THU</button>
              <button type="button" data-growth-day="4" class="growth-day-btn hover:text-[var(--text-primary)]">FRI</button>
              <button type="button" data-growth-day="5" class="growth-day-btn hover:text-[var(--text-primary)]">SAT</button>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 7: LAB & ROOM OCCUPANCY STEPPED CHART (MOVEABLE) ================= -->
        <?php if ($userCan('dashboard.view') || $userCan('timetable.view')): ?>
        <section
          id="cardAirCo2"
          data-id="cardAirCo2"
          data-category="academics rooms"
          data-search="room lab occupancy classrooms stepped area co2 air 95%"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Lab & Room Occupancy</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Campus Facility Utilization</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="co2" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Room Schedule">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-baseline justify-between mt-1 shrink-0">
            <div class="flex items-baseline gap-1.5">
              <span id="co2PpmValue" class="text-[24px] xl:text-[28px] font-bold tracking-tight text-[var(--text-primary)] leading-none">95</span>
              <span class="text-xs font-semibold text-[var(--text-secondary)]">% Peak</span>
            </div>
            <span class="text-[10.5px] font-semibold text-[var(--text-muted)]">FF-24, FF-25, TF-05</span>
          </div>

          <!-- Stepped Area Chart -->
          <div class="mt-2 flex-1 min-h-[75px] max-h-[105px] flex items-stretch gap-2">
            <div class="flex flex-col justify-between text-[9.5px] font-medium text-[var(--text-muted)] py-0.5 select-none shrink-0">
              <span>100%</span>
              <span>75%</span>
              <span>50%</span>
              <span>25%</span>
            </div>

            <div id="co2StepsContainer" class="flex-1 grid grid-cols-8 items-end pt-1 relative">
              <!-- Populated by JS -->
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 8: ROLE-SPECIFIC DYNAMIC ACADEMIC CARD (MOVEABLE) ================= -->
        <section
          id="cardRoleContext"
          data-id="cardRoleContext"
          data-category="academics role schedule faculty"
          data-search="role dynamic card faculty today schedule hod governance super admin tpa"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <!-- Dynamic Role Card Header -->
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div id="roleCardIconBadge" class="w-7 h-7 rounded-full icon-badge flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <div>
                <h2 id="roleCardTitle" class="text-[13px] font-semibold text-[var(--text-primary)] truncate">
                  <?php echo htmlspecialchars($roleContext['title'] ?? 'Role Context'); ?>
                </h2>
                <p id="roleCardSubtitle" class="text-[10px] text-[var(--text-muted)] truncate">
                  <?php echo htmlspecialchars($roleContext['subtitle'] ?? 'Personalized Academic Telemetry'); ?>
                </p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <a href="<?php echo htmlspecialchars($baseUrl); ?>timetable" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Go to Timetable Module">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Dynamic Role Card Body Container (Hydrated by JS & PHP) -->
          <div id="roleCardBody" class="flex-1 my-2 min-h-0 flex flex-col justify-between overflow-y-auto">
            <!-- Will be populated by JS based on current role selection -->
            <div class="p-3 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-xs">
              <div class="flex items-center justify-between mb-2">
                <span class="font-bold text-[var(--text-primary)]">Loading Role Context...</span>
              </div>
            </div>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0">
            <span class="text-[var(--text-muted)] font-medium">Quick Access</span>
            <a id="roleCardActionBtn" href="<?php echo htmlspecialchars($baseUrl); ?>timetable" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>View Full Module</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>

        <!-- ================= CARD 9: ACADEMIC CALENDAR & SEMESTER TIMELINE (MOVEABLE) ================= -->
        <?php if ($userCan('calendar.view')): 
          $calSummary = $moduleSummaries['calendar'] ?? [];
          $nextMeetingName = is_array($calSummary['nextMeeting'] ?? null) ? ($calSummary['nextMeeting']['name'] ?? 'Weekly Department Review') : ($calSummary['nextMeeting'] ?? 'Weekly Department Review');
          $nextMeetingSchedule = is_array($calSummary['nextMeeting'] ?? null) ? (($calSummary['nextMeeting']['day'] ?? 'Every Friday') . ' • ' . ($calSummary['nextMeeting']['time'] ?? '09:30 AM')) : ($calSummary['meetingSchedule'] ?? 'Every Friday • 09:30 AM');
          $termWeeksElapsed = $calSummary['termWeeksElapsed'] ?? ($calSummary['elapsedWeeks'] ?? 18);
          $termWeeksTotal = $calSummary['termWeeksTotal'] ?? ($calSummary['totalWeeks'] ?? 24);
          $termProgress = $calSummary['termPercent'] ?? ($calSummary['semesterProgress'] ?? 75);
          $termTitle = $calSummary['termName'] ?? ($calSummary['currentTerm'] ?? 'ODD Semester 2026-27');
        ?>
        <section
          id="cardCalendar"
          data-id="cardCalendar"
          data-category="calendar academic events"
          data-search="academic calendar semester timeline holidays term odd 2026-27 review meeting"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Academic Calendar</h2>
                <p class="text-[10px] text-[var(--text-muted)]"><?php echo htmlspecialchars($termTitle); ?></p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="calendar" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Academic Calendar">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Semester Timeline Progress -->
          <div class="my-2 p-2.5 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] shrink-0">
            <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-primary)] mb-1">
              <span>Semester Progress</span>
              <span class="text-[var(--color-primary)] font-bold">Week <?php echo $termWeeksElapsed; ?> / <?php echo $termWeeksTotal; ?> (<?php echo $termProgress; ?>%)</span>
            </div>
            <div class="timeline-track-bg">
              <div class="timeline-track-fill" style="width: <?php echo $termProgress; ?>%;"></div>
            </div>
            <div class="flex items-center justify-between text-[9.5px] text-[var(--text-muted)] mt-1.5">
              <span>Start</span>
              <span class="font-medium text-[var(--text-secondary)]">Mid-Sem Exams</span>
              <span>Term End</span>
            </div>
          </div>

          <!-- Upcoming Holidays & Meetings -->
          <div class="space-y-1.5 flex-1 min-h-0 overflow-y-auto pr-0.5">
            <div class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs">
              <div class="min-w-0 pr-1">
                <span class="font-bold text-[var(--text-primary)] truncate block"><?php echo htmlspecialchars($nextMeetingName); ?></span>
                <span class="text-[10px] text-[var(--text-muted)]"><?php echo htmlspecialchars($nextMeetingSchedule); ?></span>
              </div>
              <span class="px-2 py-0.5 rounded-full text-[9px] font-bold theme-tag-pill shrink-0">RECURRING</span>
            </div>

            <?php foreach (array_slice($calSummary['upcomingHolidays'] ?? [], 0, 2) as $hol): ?>
            <div class="px-2.5 py-1 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs">
              <span class="font-medium text-[var(--text-secondary)] truncate"><?php echo htmlspecialchars($hol['name']); ?></span>
              <span class="text-[10px] font-semibold text-[var(--text-muted)] shrink-0"><?php echo htmlspecialchars($hol['date']); ?></span>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium"><?php echo $calSummary['totalHolidays'] ?? 21; ?> Holidays Listed</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>calendar" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>View Full Calendar</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 10: STUDENT DIVISIONS & BATCHES (MOVEABLE) ================= -->
        <?php if ($userCan('student_timetable.view')): 
          $divSummary = $moduleSummaries['studentDivisions'] ?? [];
        ?>
        <section
          id="cardDivisions"
          data-id="cardDivisions"
          data-category="timetable student divisions"
          data-search="student divisions timetable btech diploma mtech classes batches shifts"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Student Divisions</h2>
                <p class="text-[10px] text-[var(--text-muted)]"><?php echo $divSummary['totalDivisions'] ?? 33; ?> Class Divisions &bull; <?php echo $divSummary['totalLabBatches'] ?? 66; ?> Batches</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="divisions" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Student Divisions">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Program Distribution Bars -->
          <div class="my-2 space-y-2 shrink-0">
            <div>
              <div class="flex justify-between text-[11px] font-medium text-[var(--text-primary)] mb-0.5">
                <span>B.Tech Engineering (18 Divs)</span>
                <span class="font-bold text-[var(--color-primary)]">55%</span>
              </div>
              <div class="mini-h-bar-track"><div class="mini-h-bar-fill bg-[var(--color-primary)]" style="width: 55%;"></div></div>
            </div>
            <div>
              <div class="flex justify-between text-[11px] font-medium text-[var(--text-primary)] mb-0.5">
                <span>Diploma Engineering (10 Divs)</span>
                <span class="font-bold text-[var(--color-primary-dark)]">30%</span>
              </div>
              <div class="mini-h-bar-track"><div class="mini-h-bar-fill bg-[var(--color-primary-dark)]" style="width: 30%;"></div></div>
            </div>
            <div>
              <div class="flex justify-between text-[11px] font-medium text-[var(--text-primary)] mb-0.5">
                <span>M.Tech Postgraduate (5 Divs)</span>
                <span class="font-bold text-[var(--color-primary-light)]">15%</span>
              </div>
              <div class="mini-h-bar-track"><div class="mini-h-bar-fill bg-[var(--color-primary-light)]" style="width: 15%;"></div></div>
            </div>
          </div>

          <!-- Shifts Breakdown -->
          <div class="grid grid-cols-2 gap-2 shrink-0">
            <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-center">
              <span class="text-[9.5px] text-[var(--text-muted)] uppercase font-semibold">Morning Shift</span>
              <p class="text-sm font-bold text-[var(--text-primary)]"><?php echo $divSummary['morningShiftCount'] ?? 18; ?> Divisions</p>
            </div>
            <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-center">
              <span class="text-[9.5px] text-[var(--text-muted)] uppercase font-semibold">Noon Shift</span>
              <p class="text-sm font-bold text-[var(--text-primary)]"><?php echo $divSummary['afternoonShiftCount'] ?? 15; ?> Divisions</p>
            </div>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium">Excel Cache Synced</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>student-timetable" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Open Divisions Grid</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 11: CTL ACTIVITY & MENTORSHIP PULSE (MOVEABLE) ================= -->
        <?php if ($userCan('ctlactivity.view')): 
          $ctlSummary = $moduleSummaries['ctlActivity'] ?? [];
        ?>
        <section
          id="cardCtlActivity"
          data-id="cardCtlActivity"
          data-category="ctl activity mentorship submissions"
          data-search="ctl activity pulse mentorship submissions coordinators approval rate"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">CTL Activity Pulse</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Teaching Compliance & Mentorship</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="ctlactivity" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect CTL Activity">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Radial Donut Gauge & Metrics -->
          <div class="flex items-center justify-between my-auto py-1">
            <div class="relative w-24 h-24 flex items-center justify-center shrink-0">
              <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
                <path class="radial-donut-bg" fill="none" stroke-width="3.5" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                <path id="ctlDonutVal" class="radial-donut-val" fill="none" stroke-dasharray="<?php echo $ctlSummary['approvalRate'] ?? 94.1; ?>, 100" stroke-width="3.5" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none select-none">
                <span class="text-base font-extrabold text-[var(--text-primary)] leading-none tracking-tight"><?php echo $ctlSummary['approvalRate'] ?? 94.1; ?>%</span>
                <span class="text-[9.5px] font-semibold text-[var(--text-muted)] mt-1 uppercase tracking-wide">Approved</span>
              </div>
            </div>

            <div class="space-y-1.5 flex-1 pl-3 min-w-0">
              <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">Verified</span>
                <span class="font-bold text-[var(--color-primary)]"><?php echo $ctlSummary['submissionsApproved'] ?? 48; ?> / 51</span>
              </div>
              <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">Under Review</span>
                <span class="font-bold text-[var(--color-primary-light)]"><?php echo $ctlSummary['submissionsPending'] ?? 3; ?> Pending</span>
              </div>
            </div>
          </div>

          <!-- CC Authority Contacts Alert -->
          <div class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-[10.5px] shrink-0">
            <span class="text-[var(--text-muted)]">CC Authorities:</span>
            <span class="font-semibold text-[var(--text-primary)]">HOD + 3 Incharge Leads</span>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium">Auto-Sync Alert</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>ctlactivity" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Open CTL Module</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 12: CTL CLOUD DRIVE SCANNER (MOVEABLE) ================= -->
        <?php if ($userCan('ctldrive.view')): 
          $driveSummary = $moduleSummaries['driveCloud'] ?? [];
        ?>
        <section
          id="cardCtlDrive"
          data-id="cardCtlDrive"
          data-category="ctldrive google drive cloud audit scanner"
          data-search="ctl drive scanner google drive cloud sync folders audit level 3"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">CTL Drive Scanner</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Curriculum Cloud Depth Audit</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="ctldrive" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Drive Scanner">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Sync Progress Bar & Cloud Status -->
          <div class="my-2 p-2.5 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] shrink-0">
            <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-primary)] mb-1">
              <span>Drive Folders Synced</span>
              <span class="text-[var(--color-primary)] font-bold"><?php echo $driveSummary['syncedFoldersCount'] ?? 51; ?> / 51 Folders (100%)</span>
            </div>
            <div class="w-full h-2 rounded-full theme-progress-track">
              <div class="h-full bg-[var(--color-primary)] rounded-full transition-all duration-500" style="width: 100%;"></div>
            </div>
            <div class="flex items-center justify-between text-[9.5px] text-[var(--text-muted)] mt-1.5 font-medium">
              <span>Level 3 Heuristic Compliant</span>
              <span class="text-[var(--color-primary)] font-semibold">GIS OAuth 2.0 Active</span>
            </div>
          </div>

          <!-- Master Folders Links -->
          <div class="space-y-1.5 flex-1 min-h-0 overflow-y-auto pr-0.5">
            <?php foreach ($driveSummary['masterFolders'] ?? [] as $f): ?>
            <a href="<?php echo htmlspecialchars($f['link']); ?>" target="_blank" rel="noopener" class="px-2.5 py-1.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs hover:border-[var(--color-primary)] transition group">
              <span class="font-medium text-[var(--text-primary)] truncate group-hover:text-[var(--color-primary)]"><?php echo htmlspecialchars($f['name']); ?></span>
              <svg class="w-3.5 h-3.5 text-[var(--text-muted)] group-hover:text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <?php endforeach; ?>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium">51 Scanned Folders</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>ctldrive" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Open Scanner</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 13: ZERO STUDENT INCIDENT GUARD (MOVEABLE) ================= -->
        <?php if ($userCan('zero_student.view')): 
          $zeroSummary = $moduleSummaries['zeroStudent'] ?? [];
        ?>
        <section
          id="cardZeroStudent"
          data-id="cardZeroStudent"
          data-category="zero student attendance incident guard"
          data-search="zero student incident attendance safeguard compliance 75% gtu"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Zero Student Guard</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Attendance Incident Monitoring</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="zero_student" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Zero Student Incidents">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Shield Status Badge & Compliance Rate -->
          <div class="my-auto py-2 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full theme-tag-pill font-bold text-xs mb-2">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
              <span>100% NORMAL COMPLIANCE</span>
            </div>
            <div class="text-[26px] font-bold text-[var(--text-primary)] leading-tight">
              <?php echo $zeroSummary['openIncidents'] ?? 0; ?> Open Incidents
            </div>
            <p class="text-[11px] text-[var(--text-muted)] mt-1">GTU 75% Attendance Threshold Guard Active</p>
          </div>

          <!-- Historical Stats Pill -->
          <div class="grid grid-cols-2 gap-2 shrink-0">
            <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-center">
              <span class="text-[9.5px] text-[var(--text-muted)] uppercase font-semibold">Resolved Cases</span>
              <p class="text-sm font-bold text-[var(--text-primary)]"><?php echo $zeroSummary['resolvedIncidents'] ?? 12; ?> Handled</p>
            </div>
            <div class="p-2 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] text-center">
              <span class="text-[9.5px] text-[var(--text-muted)] uppercase font-semibold">Alert Level</span>
              <p class="text-sm font-bold text-[var(--color-primary)]">Normal (0 Flags)</p>
            </div>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium">Batch Import Enabled</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>zero-student" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>View Incident Log</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 14: DEPARTMENT WORKLOAD REPORTS (MOVEABLE) ================= -->
        <?php if ($userCan('report.view')): 
          $repSummary = $moduleSummaries['reportAnalytics'] ?? [];
        ?>
        <section
          id="cardReports"
          data-id="cardReports"
          data-category="reports workload analytics export"
          data-search="reports workload teaching activity logs excel pdf sheetjs export"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Workload Reports</h2>
                <p class="text-[10px] text-[var(--text-muted)]">Activity Analytics & Export Engine</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="reports" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Reports Analytics">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Activity Distribution Breakdown Bars -->
          <div class="my-2 space-y-1.5 shrink-0">
            <?php foreach ($repSummary['breakdown'] ?? [] as $b): ?>
            <div>
              <div class="flex justify-between text-[10.5px] font-medium text-[var(--text-primary)] mb-0.5">
                <span><?php echo htmlspecialchars($b['category']); ?></span>
                <span class="font-bold text-[var(--text-secondary)]"><?php echo $b['percent']; ?>%</span>
              </div>
              <div class="mini-h-bar-track">
                <div class="mini-h-bar-fill bg-[var(--color-primary)]" style="width: <?php echo $b['percent']; ?>%;"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Supported Export Types -->
          <div class="flex items-center justify-between gap-1 text-[10px] shrink-0">
            <span class="px-2 py-1 rounded-lg bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] font-semibold text-[var(--text-secondary)]">SheetJS XLSX</span>
            <span class="px-2 py-1 rounded-lg bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] font-semibold text-[var(--text-secondary)]">PDF Export</span>
            <span class="px-2 py-1 rounded-lg bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] font-semibold text-[var(--text-secondary)]">CSV Table</span>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium"><?php echo $repSummary['totalReports'] ?? 0; ?> Logged Reports</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>report" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Open Reports Hub</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 15: SYLLABUS COVERAGE & EXAM PAPERS (MOVEABLE) ================= -->
        <?php if ($userCan('syllabus.view')): 
          $sylSummary = $moduleSummaries['syllabus'] ?? [];
        ?>
        <section
          id="cardSyllabus"
          data-id="cardSyllabus"
          data-category="syllabus curriculum gtu exam papers"
          data-search="syllabus curriculum schemes exam question papers sem 1 to 8 gtu gmiu"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Syllabus & Past Papers</h2>
                <p class="text-[10px] text-[var(--text-muted)]">GTU Curriculum & Question Banks</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="syllabus" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Syllabus & Papers">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- 8 Semester Coverage Grid -->
          <div class="my-2 p-2.5 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] shrink-0">
            <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-primary)] mb-1.5">
              <span>Curriculum Coverage</span>
              <span class="text-[var(--color-primary)] font-bold">Sem 1 to Sem 8</span>
            </div>
            <div class="grid grid-cols-4 gap-1.5 text-center text-[10px] font-bold">
              <span class="py-1 rounded-lg bg-[var(--color-primary)]/15 text-[var(--color-primary)]">Sem 1-2</span>
              <span class="py-1 rounded-lg bg-[var(--color-primary)]/15 text-[var(--color-primary)]">Sem 3-4</span>
              <span class="py-1 rounded-lg bg-[var(--color-primary)]/15 text-[var(--color-primary)]">Sem 5-6</span>
              <span class="py-1 rounded-lg bg-[var(--color-primary)]/15 text-[var(--color-primary)]">Sem 7-8</span>
            </div>
            <p class="text-[9.5px] text-[var(--text-muted)] text-center mt-1.5">UG CE, UG IT, Diploma CE/IT, M.Tech</p>
          </div>

          <!-- Question Papers Bank & Scraper Status -->
          <div class="p-2.5 rounded-xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] flex items-center justify-between text-xs shrink-0">
            <div>
              <span class="font-bold text-[var(--text-primary)]">GTU Question Bank</span>
              <p class="text-[10px] text-[var(--text-muted)]">Winter & Summer Examination Papers</p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold theme-tag-pill">LIVE</span>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium">NEP 2020 Aligned</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>syllabus" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Browse Syllabus</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 16: FACULTY MATRIX & CABIN ALLOCATION (MOVEABLE) ================= -->
        <?php if ($userCan('faculty.list') || $userCan('faculty.profile')): 
          $facMatrix = $moduleSummaries['facultyMatrix'] ?? [];
        ?>
        <section
          id="cardFacultyMatrix"
          data-id="cardFacultyMatrix"
          data-category="faculty staff cabin allocation matrix"
          data-search="faculty staff cabins seating rooms ff-38 ff-01 ff-37 gf-37 matrix directory"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Faculty & Cabins</h2>
                <p class="text-[10px] text-[var(--text-muted)]">54 Department Members Matrix</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <button type="button" data-modal-open="faculty_matrix" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Inspect Faculty Matrix">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- IT vs CE Distribution -->
          <div class="my-2 p-2.5 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] shrink-0">
            <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-primary)] mb-1">
              <span>IT Dept (<?php echo $facMatrix['itCount'] ?? 26; ?>)</span>
              <span>CE Dept (<?php echo $facMatrix['ceCount'] ?? 28; ?>)</span>
            </div>
            <div class="w-full h-2 rounded-full theme-progress-track flex">
              <div class="h-full bg-[var(--color-primary)]" style="width: <?php echo $facMatrix['itPercent'] ?? 48.1; ?>%;"></div>
              <div class="h-full bg-[var(--color-primary-light)]" style="width: <?php echo $facMatrix['cePercent'] ?? 51.9; ?>%;"></div>
            </div>
            <div class="flex items-center justify-between text-[9.5px] text-[var(--text-muted)] mt-1 font-medium">
              <span><?php echo $facMatrix['itPercent'] ?? 48.1; ?>% Information Tech</span>
              <span><?php echo $facMatrix['cePercent'] ?? 51.9; ?>% Computer Engg</span>
            </div>
          </div>

          <!-- Cabin Seating Pills -->
          <div class="space-y-1 flex-1 min-h-0 overflow-y-auto pr-0.5">
            <div class="grid grid-cols-2 gap-1.5">
              <?php foreach (array_slice($facMatrix['topCabins'] ?? [], 0, 4) as $cab): ?>
              <div class="cabin-pill">
                <span class="truncate"><?php echo htmlspecialchars($cab['room']); ?></span>
                <span class="font-bold text-[var(--color-primary)]"><?php echo $cab['count']; ?> Staff</span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Bottom Action Link -->
          <div class="pt-2 border-t border-[var(--glass-border)] flex items-center justify-between text-[11px] shrink-0 mt-1">
            <span class="text-[var(--text-muted)] font-medium"><?php echo $facMatrix['activeRate'] ?? 96.3; ?>% Active Logins</span>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>faculty" class="font-bold text-[var(--color-primary)] hover:underline flex items-center gap-1">
              <span>Directory Roster</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </section>
        <?php endif; ?>

        <!-- ================= CARD 17: DEPARTMENT ROSTER & ACADEMIC ACTION HUB (MOVEABLE) ================= -->
        <section
          id="cardDepartmentHub"
          data-id="cardDepartmentHub"
          data-category="academics shortcuts"
          data-search="department roster hub information technology computer engineering shortcuts timetable faculty"
          class="dashboard-card col-span-12 md:col-span-6 lg:col-span-4 min-h-[290px] glass-card p-3.5 sm:p-4 flex flex-col justify-between group shadow-sm"
        >
          <div class="flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-full icon-badge flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </div>
              <div>
                <h2 class="text-[13px] font-semibold text-[var(--text-primary)]">Department Hub</h2>
                <p class="text-[10px] text-[var(--text-muted)]">CE & IT Academic Action Shortcuts</p>
              </div>
            </div>

            <div class="flex items-center gap-1.5">
              <span class="card-drag-handle p-1 rounded-lg" title="Drag to move card">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                  <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                  <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                </svg>
              </span>
              <a href="<?php echo htmlspecialchars($baseUrl); ?>faculty" class="w-7 h-7 rounded-full action-arrow-btn flex items-center justify-center" title="Open Faculty Directory">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Department Distribution Breakdown -->
          <div class="my-2 p-3 rounded-2xl bg-[var(--icon-circle-bg)] border border-[var(--glass-border)] shrink-0">
            <div class="flex items-center justify-between text-xs font-semibold text-[var(--text-primary)] mb-1.5">
              <span>IT Department (26)</span>
              <span>CE Department (28)</span>
            </div>
            <div class="w-full h-2 rounded-full theme-progress-track flex">
              <div class="h-full bg-[var(--color-primary)] transition-all duration-500" style="width: 48%;"></div>
              <div class="h-full bg-[var(--color-primary-light)] transition-all duration-500" style="width: 52%;"></div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-[var(--text-muted)] mt-1.5 font-medium">
              <span>48% Information Tech</span>
              <span>52% Computer Engg</span>
            </div>
          </div>

          <!-- Academic Fast Action Grid -->
          <div class="grid grid-cols-2 gap-2 text-xs">
            <a href="<?php echo htmlspecialchars($baseUrl); ?>timetable" class="px-3 py-2 rounded-xl glass-pill font-semibold text-[var(--text-primary)] hover:border-[var(--color-primary)] transition flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)]"></span>
              <span class="truncate">Timetable</span>
            </a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>faculty" class="px-3 py-2 rounded-xl glass-pill font-semibold text-[var(--text-primary)] hover:border-[var(--color-primary)] transition flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary-light)]"></span>
              <span class="truncate">Faculty Roster</span>
            </a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>student-timetable" class="px-3 py-2 rounded-xl glass-pill font-semibold text-[var(--text-primary)] hover:border-[var(--color-primary)] transition flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary-dark)]"></span>
              <span class="truncate">Divisions</span>
            </a>
            <a href="<?php echo htmlspecialchars($baseUrl); ?>workload-summary" class="px-3 py-2 rounded-xl glass-pill font-semibold text-[var(--text-primary)] hover:border-[var(--color-primary)] transition flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-[var(--text-accent)]"></span>
              <span class="truncate">Workload Audit</span>
            </a>
          </div>
        </section>

      </main>
      </div><!-- /#dashboardWorkspace -->
    </div><!-- /right column -->
  </div><!-- /main wrapper -->

  <!-- ================= INTERACTIVE MODALS & DRAWERS ================= -->

  <!-- UNIVERSAL MODAL CONTAINER -->
  <div id="modalOverlay" class="fixed inset-0 bg-black/45 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div id="modalBox" class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-lg p-5 sm:p-6 shadow-2xl border border-white/80 relative max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
        <div class="flex items-center gap-2.5">
          <div id="modalIconBadge" class="w-9 h-9 rounded-full bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center font-bold">✨</div>
          <div>
            <h3 id="modalTitle" class="text-base font-bold text-[var(--text-primary)]">Module Details</h3>
            <p id="modalSubtitle" class="text-xs text-[var(--text-muted)]">Real-time academic telemetry & controls</p>
          </div>
        </div>
        <button id="closeModalBtn" type="button" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-sm font-bold hover:scale-105 transition" aria-label="Close Modal">✕</button>
      </div>
      <div id="modalBody" class="space-y-4 text-sm">
        <!-- Populated dynamically by JS -->
      </div>
    </div>
  </div>

  <!-- PASS SERVER METRICS TO CLIENT JS -->
  <script>
    window.DASHBOARD_DATA = <?php echo json_encode($dashboardData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  </script>

  <!-- ================= APPLICATION JAVASCRIPT ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/vendor/sortable/sortable.min.js') : 'assets/vendor/sortable/sortable.min.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/dashboard.js') : 'assets/js/dashboard.js'; ?>"></script>
</body>
</html>
