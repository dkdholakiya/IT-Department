<?php
$page_title = 'Drive Folder Scanner — CE & IT Department';
$page_css = ['assets/css/ctldrive.css'];
$extra_head = '<script src="' . (function_exists('v_asset') ? v_asset('assets/vendor/xlsx/xlsx.full.min.js') : 'assets/vendor/xlsx/xlsx.full.min.js') . '"></script>' .
              '<script>' .
              'window._gapiLoadedQueue = false;' .
              'window._gisLoadedQueue = false;' .
              'function gapiLoaded() { if (window._initGapi) { window._initGapi(); } else { window._gapiLoadedQueue = true; } }' .
              'function gisLoaded() { if (window._initGis) { window._initGis(); } else { window._gisLoadedQueue = true; } }' .
              '</script>';
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Calendar, CTL Activity & Faculty (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'ctldrive';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Drive Folder Scanner';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + CTL DRIVE WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Drive Folder Scanner';
      $nav_subtitle = 'CE & IT Department • Google Drive Folder Scanner & Excel Reporter';
      $nav_search_placeholder = 'Search scanned folders, faculty, files...';
      $nav_back_url = function_exists('base_url') ? base_url('/dashboard') : './dashboard';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: BENTO GLASS SCANNER WORKSPACE ================= -->
      <main class="flex-1 min-h-0 min-w-0 grid grid-cols-1 lg:grid-cols-12 gap-3.5 lg:gap-4 transition-all duration-300">

        <!-- ================= 1. LEFT COLUMN: CONTROLS & CONFIGURATION (5 COLS ON DESKTOP) ================= -->
        <section class="lg:col-span-5 glass-card p-3.5 sm:p-5 flex flex-col gap-3.5 overflow-y-auto ctldrive-custom-scroll relative">

          <!-- Hidden Connection Status Hooks for JS compatibility -->
          <div id="connection-status-badge" class="hidden" aria-hidden="true">
            <span id="connection-status-dot"></span>
            <span id="connection-status-text"></span>
          </div>

          <!-- Section 1: Connection Controls -->
          <div class="p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between gap-3 shrink-0">
            <div class="min-w-0">
              <span class="text-xs font-bold text-[var(--text-primary)] block">Drive Authorization</span>
              <span class="text-[11px] text-[var(--text-muted)] block">Google OAuth 2.0 (24h persistent session)</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button 
                id="auth-btn" 
                type="button" 
                onclick="handleAuthClick()"
                class="h-8 px-3.5 rounded-xl bg-[var(--color-primary)] hover:brightness-105 active:scale-95 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition cursor-pointer"
              >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span>Authorize & Connect</span>
              </button>
              <button 
                id="signout-btn" 
                type="button" 
                onclick="handleSignoutClick()" 
                style="display: none;"
                class="h-8 px-3 rounded-xl glass-pill text-xs font-bold text-rose-500 hover:bg-rose-500 hover:text-white transition flex items-center gap-1.5 cursor-pointer"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <polyline points="16 17 21 12 16 7" />
                  <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                <span>Sign Out</span>
              </button>
            </div>
          </div>

          <!-- Section 2: Target Directory & Presets (Visible when connected) -->
          <div id="scan-container" class="flex flex-col gap-3" style="display: none;">
            
            <!-- Department Tabs & Modal Trigger -->
            <div class="flex flex-col gap-2">
              <div class="flex items-center justify-between gap-2 flex-wrap">
                <span class="text-xs font-bold text-[var(--text-primary)]">Presets & Department:</span>
                <div class="flex items-center gap-2">
                  <?php if (!class_exists('Core\Auth') || \Core\Auth::can('ctldrive.view_links')): ?>
                  <button 
                    type="button" 
                    class="h-7 px-2.5 rounded-xl glass-pill text-[11px] font-bold text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1 cursor-pointer" 
                    onclick="openDriveLinksModal()" 
                    title="View popup box with all Drive links from facultyData.js"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                      <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    <span>facultyData.js Links</span>
                  </button>
                  <?php endif; ?>

                  <!-- Department Selector Segment -->
                  <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0">
                    <button 
                      type="button" 
                      class="px-2.5 py-0.5 rounded-full text-[11px] font-bold transition bg-[var(--color-primary)] text-white shadow-sm cursor-pointer" 
                      id="tab-dept-it" 
                      onclick="switchDeptTab('IT')"
                    >
                      IT Dept
                    </button>
                    <button 
                      type="button" 
                      class="px-2.5 py-0.5 rounded-full text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] transition cursor-pointer" 
                      id="tab-dept-ce" 
                      onclick="switchDeptTab('CE')"
                    >
                      CE Dept
                    </button>
                  </div>
                </div>
              </div>

              <!-- Presets Grid Container -->
              <div id="dept-presets-container" class="dept-presets-list ctldrive-custom-scroll">
                <!-- Dynamically populated via JS -->
              </div>
            </div>

            <!-- Target Folder Link / ID Input Bar -->
            <div class="space-y-1.5 pt-1">
              <label for="folder-id-input" class="text-xs font-bold text-[var(--text-primary)] flex items-center justify-between">
                <span>Target Folder Link or ID:</span>
                <span class="text-[10px] text-[var(--text-muted)] font-mono">1m2_s4hPYSs4L6LmnNfzS17Geor7Eknh5</span>
              </label>
              <div class="flex items-center gap-2">
                <div class="relative flex-1 min-w-0">
                  <input 
                    type="text" 
                    id="folder-id-input" 
                    placeholder="Enter parent folder ID or Google Drive URL..." 
                    value="1m2_s4hPYSs4L6LmnNfzS17Geor7Eknh5"
                    class="w-full h-9 pl-8 pr-3 rounded-xl glass-pill text-xs font-mono text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition"
                  >
                  <svg class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                  </svg>
                </div>
                <?php if (!class_exists('Core\Auth') || \Core\Auth::can('ctldrive.scan')): ?>
                <button 
                  id="scan-btn" 
                  type="button"
                  onclick="startScan()" 
                  class="h-9 px-4 rounded-xl bg-[var(--color-primary)] hover:brightness-105 active:scale-95 text-white font-bold text-xs shadow-sm flex items-center gap-1.5 transition shrink-0 cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                  </svg>
                  <span>Scan Folders</span>
                </button>
                <?php endif; ?>
              </div>
            </div>

          </div>

          <!-- Section 3: Email Excel Report (revealed on scan or expandable) -->
          <?php if (!class_exists('Core\Auth') || \Core\Auth::can('ctldrive.send_email')): ?>
          <div id="email-section" class="p-3.5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 space-y-3" style="display: none;">
            <div class="flex items-center justify-between gap-2">
              <span class="text-xs font-bold text-[var(--text-primary)] flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
                <span>Email Excel Report</span>
              </span>
              <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Report Ready</span>
            </div>

            <!-- Faculty Search Autocomplete -->
            <div class="space-y-1">
              <label for="facultySearch" class="text-[11px] font-semibold text-[var(--text-muted)]">Faculty Coordinator <span class="text-rose-500">*</span></label>
              <div class="search-select-wrap relative">
                <input 
                  type="text" 
                  id="facultySearch" 
                  placeholder="Type to search faculty name..." 
                  autocomplete="off"
                  class="w-full h-8 pl-8 pr-3 rounded-xl glass-pill text-xs text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition"
                >
                <input type="hidden" id="preparedBy" name="preparedBy" required>
                <svg class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8"/>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <div class="search-dropdown-list ctldrive-custom-scroll" id="facultyDropdownList"></div>
              </div>
              <div id="facultyError" class="text-[10px] text-rose-500 font-semibold hidden">Please select a faculty member from the dropdown.</div>
            </div>

            <!-- Auto-filled Contact Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div class="space-y-1">
                <label for="facultyEmail" class="text-[10px] font-semibold text-[var(--text-muted)]">Email Address</label>
                <input type="text" id="facultyEmail" placeholder="Auto-filled..." readonly class="w-full h-7 px-2.5 rounded-lg glass-pill text-[11px] text-[var(--text-muted)] bg-black/5 dark:bg-white/5 cursor-not-allowed">
              </div>
              <div class="space-y-1">
                <label for="facultyPhone" class="text-[10px] font-semibold text-[var(--text-muted)]">Mobile Number</label>
                <input type="text" id="facultyPhone" placeholder="Auto-filled..." readonly class="w-full h-7 px-2.5 rounded-lg glass-pill text-[11px] text-[var(--text-muted)] bg-black/5 dark:bg-white/5 cursor-not-allowed">
              </div>
            </div>

            <!-- CC Emails Multi-select -->
            <div class="space-y-1">
              <label for="ccSearch" class="text-[10px] font-semibold text-[var(--text-muted)]">CC Emails (Multi-select)</label>
              <div class="cc-select-wrap p-2 rounded-xl glass-pill border border-black/5 dark:border-white/10 min-h-[38px] flex flex-col justify-center">
                <div class="cc-tags-container" id="ccTagsContainer"></div>
                <input 
                  type="text" 
                  id="ccSearch" 
                  placeholder="Type or click to add CC emails..." 
                  autocomplete="off"
                  class="w-full bg-transparent text-xs text-[var(--text-primary)] focus:outline-none placeholder:text-[var(--text-muted)] mt-0.5"
                >
                <div class="search-dropdown-list ctldrive-custom-scroll" id="ccDropdownList"></div>
              </div>
            </div>

            <!-- Send Action Button -->
            <button 
              type="button" 
              id="sendBtn"
              class="w-full h-8 rounded-xl bg-[var(--color-primary)] hover:brightness-105 active:scale-95 text-white font-bold text-xs shadow-sm flex items-center justify-center gap-1.5 transition cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M22 2L11 13" />
                <path d="M22 2L15 22l-4-9-9-4 20-7z" />
              </svg>
              <span>Send Report via Email</span>
            </button>
          </div>
          <?php endif; ?>

          <!-- Section 4: Live Console Logs -->
          <div class="space-y-1.5 mt-auto pt-2">
            <div class="flex items-center justify-between text-xs font-bold text-[var(--text-primary)]">
              <span>Console Logs</span>
              <button type="button" onclick="openScanConsoleModal()" class="text-[10.5px] text-[var(--color-primary)] hover:underline font-semibold cursor-pointer">Expand Modal ↗</button>
            </div>
            <div id="inline-console-wrapper">
              <div id="status" class="status ctldrive-custom-scroll">Please authorize to begin.</div>
            </div>
          </div>

        </section>

        <!-- ================= 2. RIGHT COLUMN: SCAN RESULTS & DOWNLOAD (7 COLS ON DESKTOP) ================= -->
        <section class="lg:col-span-7 glass-card p-3.5 sm:p-5 flex flex-col justify-between overflow-hidden relative">

          <!-- Results Header & Actions -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base sm:text-lg font-bold font-serif tracking-tight text-[var(--text-primary)] leading-none">
                  Scan Results & Directory Tree
                </h3>
              </div>
              <p class="text-[11px] sm:text-xs text-[var(--text-muted)] mt-1 hidden sm:block">
                Recursive directory inspection with empty/filled subfolder classification.
              </p>
            </div>

            <!-- Download Excel Report Button -->
            <?php if (!class_exists('Core\Auth') || \Core\Auth::can('ctldrive.download')): ?>
            <button 
              id="download-btn" 
              type="button"
              onclick="downloadExcelReport()" 
              style="display: none;"
              class="h-8 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs shadow-md flex items-center gap-1.5 transition shrink-0 cursor-pointer"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
              </svg>
              <span>Download Excel Report (.xlsx)</span>
            </button>
            <?php endif; ?>
          </div>

          <!-- Live 4-Card Summary Counters -->
          <div id="summary-card" class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-3 shrink-0" style="display: none;">
            <div class="p-2.5 rounded-2xl glass-pill border border-amber-500/20 text-center">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Folders Scanned</span>
              <span class="text-lg font-extrabold text-[var(--text-primary)] font-mono block mt-0.5" id="total-folders-val">0</span>
            </div>
            <div class="p-2.5 rounded-2xl glass-pill border border-blue-500/20 text-center">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Total Documents</span>
              <span class="text-lg font-extrabold text-[var(--text-primary)] font-mono block mt-0.5" id="total-docs-val">0</span>
            </div>
            <div class="p-2.5 rounded-2xl glass-pill border border-emerald-500/20 text-center">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Non-Empty</span>
              <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 font-mono block mt-0.5" id="non-empty-folders-val">0</span>
            </div>
            <div class="p-2.5 rounded-2xl glass-pill border border-rose-500/20 text-center">
              <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block">Empty Folders</span>
              <span class="text-lg font-extrabold text-rose-600 dark:text-rose-400 font-mono block mt-0.5" id="empty-folders-val">0</span>
            </div>
          </div>

          <!-- Results Scroll Container -->
          <div class="flex-1 overflow-y-auto ctldrive-custom-scroll pr-1 space-y-4">

            <!-- Empty State Graphic (Shown before scan) -->
            <div id="results-empty-state" class="h-full flex flex-col items-center justify-center text-center p-6 text-[var(--text-muted)]">
              <div class="w-16 h-16 rounded-3xl glass-pill flex items-center justify-center text-[var(--color-primary)] mb-3 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                  <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                  <line x1="12" y1="11" x2="12" y2="17"/>
                  <line x1="9" y1="14" x2="15" y2="14"/>
                </svg>
              </div>
              <h4 class="font-bold text-sm text-[var(--text-primary)]">Ready to Scan Google Drive</h4>
              <p class="text-xs max-w-sm mt-1">Connect your Google account and select a department preset or paste a Parent Folder ID to start scanning.</p>
            </div>

            <!-- Empty Folders Category -->
            <div class="space-y-2">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <h4 class="font-bold text-xs uppercase tracking-wider text-rose-600 dark:text-rose-400">
                  Empty Subfolders (Action Required)
                </h4>
              </div>
              <div id="empty-folders" class="space-y-2.5">
                <!-- Dynamically populated -->
              </div>
            </div>

            <!-- Non-Empty Folders Category -->
            <div class="space-y-2 pt-2">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                  Non-Empty Folders (Files Uploaded)
                </h4>
              </div>
              <div id="non-empty-folders" class="space-y-2.5">
                <!-- Dynamically populated -->
              </div>
            </div>

          </div>

        </section>

      </main>

    </div><!-- /Right Column -->

  </div><!-- /Main Wrapper -->

  <!-- ================= 3. POPUP MODAL 1: LIVE RECURSIVE SCANNER CONSOLE ================= -->
  <div id="scanConsoleModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md hidden items-center justify-center p-3 sm:p-6">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-4xl lg:max-w-5xl h-[82vh] sm:h-[86vh] p-4 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 flex flex-col">
      <div class="flex items-center justify-between pb-3.5 border-b border-black/10 dark:border-white/10 shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
          <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
          <svg class="w-5 h-5 spin-icon text-[var(--color-primary)] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/>
          </svg>
          <div>
            <h3 class="text-base sm:text-lg font-bold text-[var(--text-primary)] leading-tight truncate">
              Live Google Drive Recursive Scanner
            </h3>
            <p class="text-[11px] text-[var(--text-muted)] hidden sm:block">Real-time inspection of folders and files</p>
          </div>
        </div>
        <button type="button" onclick="closeScanConsoleModal()" class="w-9 h-9 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] text-xl font-bold cursor-pointer transition" title="Minimize to Page">
          &times;
        </button>
      </div>

      <div id="scan-console-modal-body" class="flex-1 min-h-0 my-3 flex flex-col overflow-hidden">
        <!-- #status element attaches here during active scanning -->
      </div>

      <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between gap-3 shrink-0">
        <span id="scan-console-modal-hint" class="text-xs text-[var(--text-muted)] font-medium truncate">⚡ Real-time line-by-line scanning in progress...</span>
        <button type="button" onclick="closeScanConsoleModal()" class="px-4 py-2 rounded-xl bg-[var(--color-primary)] hover:brightness-105 active:scale-95 text-white text-xs font-bold shadow-sm transition cursor-pointer">
          Minimize to Page
        </button>
      </div>
    </div>
  </div>

  <!-- ================= 4. POPUP MODAL 2: FACULTY DRIVE LINKS BROWSER ================= -->
  <div id="driveLinksModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-md hidden items-center justify-center p-3 sm:p-5">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-3xl lg:max-w-4xl p-4 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 flex flex-col max-h-[85vh]">
      <!-- Header -->
      <div class="flex items-center justify-between pb-3 border-b border-black/10 dark:border-white/10 shrink-0">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-sm sm:text-base font-bold text-[var(--text-primary)]">Faculty & Department Drive Links</h3>
            <p class="text-[11px] text-[var(--text-muted)]">Single-click to populate & scan Google Drive links from facultyData.js</p>
          </div>
        </div>
        <button type="button" onclick="closeDriveLinksModal()" class="w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          &times;
        </button>
      </div>

      <!-- Search & Department Filter Bar -->
      <div class="flex flex-col sm:flex-row items-center gap-2.5 my-3 shrink-0">
        <div class="relative flex-1 w-full">
          <input 
            type="text" 
            id="modalDriveSearch" 
            placeholder="Search faculty name, department, or initials..." 
            autocomplete="off" 
            oninput="filterModalDriveLinks()"
            class="w-full h-8 pl-8 pr-3 rounded-xl glass-pill text-xs text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition"
          >
          <svg class="w-3.5 h-3.5 text-[var(--text-muted)] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </div>
        <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0 self-start sm:self-auto">
          <button type="button" class="modal-pill px-2.5 py-0.5 rounded-full text-xs font-bold transition bg-[var(--color-primary)] text-white cursor-pointer" id="modal-pill-all" onclick="filterModalByDept('ALL')">All</button>
          <button type="button" class="modal-pill px-2.5 py-0.5 rounded-full text-xs font-bold transition text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer" id="modal-pill-it" onclick="filterModalByDept('IT')">IT Dept</button>
          <button type="button" class="modal-pill px-2.5 py-0.5 rounded-full text-xs font-bold transition text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer" id="modal-pill-ce" onclick="filterModalByDept('CE')">CE Dept</button>
        </div>
      </div>

      <!-- Links List -->
      <div id="modalDriveLinksList" class="flex-1 overflow-y-auto ctldrive-custom-scroll space-y-2 pr-1">
        <!-- Dynamically rendered list from facultyData.js -->
      </div>

      <!-- Footer -->
      <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between gap-2 shrink-0">
        <span class="text-xs text-[var(--text-muted)]">Click any folder link to start 1-Click Scan.</span>
        <button type="button" onclick="closeDriveLinksModal()" class="px-4 py-1.5 rounded-xl glass-pill text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] cursor-pointer">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Config variable for base url -->
  <script>
    window.APP_BASE_URL = '<?php echo function_exists("base_url") ? base_url("/") : "./"; ?>';
  </script>

  <!-- ================= APPLICATION SCRIPTS ================= -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/ctldrive.js') : 'assets/js/ctldrive.js'; ?>"></script>

  <!-- Google Drive Client APIs -->
  <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
  <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
</body>
</html>
