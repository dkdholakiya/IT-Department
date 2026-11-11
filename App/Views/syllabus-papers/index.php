<?php
$page_title = 'Syllabus & Exam Papers — CE & IT Department';
$page_description = 'Department of CE & IT — Official GMIU Course Subject Codes, Credit Tables, and Previous Exam Question Papers.';
$page_css = [
    'assets/css/syllabus.css',
];
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Matches Dashboard, Timetable, CTL Activity & Faculty (1600px Max-Width, 100vh on Desktop, Zero Edge Bleed) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'syllabus-papers';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Syllabus & Exam Papers';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + SYLLABUS WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Syllabus & Exam Papers';
      $nav_subtitle = 'CE & IT Department • Official GMIU Subject Codes & Previous Exam Papers';
      $nav_search_placeholder = 'Search syllabus, course codes, papers...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS SYLLABUS DASHBOARD ================= -->
      <main class="syl-main-workspace flex-1 min-h-0 min-w-0 p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">
        
        <div id="mainSyllabusContent" class="flex-1 min-h-0 min-w-0 flex flex-col gap-3 overflow-y-auto syl-custom-scroll pr-1">

          <!-- 1. TOP TOOLBAR: TARGET DEPARTMENT SELECTOR & LIVE URL SCRAPER INPUT (ALL IN ONE ROW) -->
          <div class="glass-card p-2 sm:p-2.5 rounded-2xl border border-white/80 dark:border-white/10 shrink-0">
            <!-- Legacy Filter Hook (Hidden) -->
            <div id="deptFilter" class="hidden" aria-hidden="true"></div>

            <!-- Single Unified Row: Department Select + Scrape URL Input + Fetch Button -->
            <form id="scrapeForm" class="flex flex-col sm:flex-row items-center gap-2 w-full">
              <!-- Department Dropdown Selection -->
              <div class="flex items-center gap-1.5 w-full sm:w-auto shrink-0">
                <label for="deptSelect" class="text-[11px] font-bold text-[var(--text-muted)] flex items-center gap-1 shrink-0">
                  <svg class="w-3.5 h-3.5 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 6h16M7 12h10M10 18h4"/>
                  </svg>
                  <span>Dept:</span>
                </label>
                <div class="relative flex-1 sm:flex-initial min-w-0 w-full sm:w-[220px] md:w-[260px]">
                  <select id="deptSelect" class="w-full h-9 pl-3 pr-8 rounded-xl glass-pill syl-dept-select text-xs font-semibold text-[var(--text-primary)] border border-white/80 dark:border-white/10 focus:outline-none focus:ring-1 focus:ring-[var(--color-primary)] cursor-pointer truncate">
                    <optgroup label="── Under-Graduation (UG) ──">
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-engineering/student-corner" selected>
                        Computer Engineering (UG)
                      </option>
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-science-and-engineering/student-corner">
                        Computer Science &amp; Engineering (UG)
                      </option>
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-information-technology/student-corner">
                        Information Technology (UG)
                      </option>
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-information-technology/student-corner">
                        Info &amp; Communication Engineering (UG)
                      </option>
                    </optgroup>
                    <optgroup label="── Diploma ──">
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-computer-engineering/student-corner">
                        Computer Engineering (Diploma)
                      </option>
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-information-technology/student-corner">
                        Information Technology (Diploma)
                      </option>
                      <option value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/diploma-computer-science-and-engineering/student-corner">
                        Computer Science &amp; Engineering (Diploma)
                      </option>
                    </optgroup>
                  </select>
                  <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[var(--text-muted)]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9"/>
                  </svg>
                </div>
              </div>

              <!-- Scrape URL Input (Flex-1 fills remaining space) -->
              <div class="relative flex-1 min-w-0 w-full flex items-center">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-muted)] pointer-events-none">
                  <svg class="w-4 h-4 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                  </svg>
                </span>
                <input
                  type="url"
                  id="urlInput"
                  class="w-full h-9 pl-9 pr-8 rounded-xl glass-pill syl-url-input text-xs font-mono text-[var(--text-primary)] placeholder-[var(--text-muted)] border border-white/80 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                  placeholder="https://gmiu.edu.in/gmiu/website/faculty/..."
                  value="https://gmiu.edu.in/gmiu/website/faculty/faculty-of-engineering-amp-technology/under-graduation-computer-engineering/student-corner"
                  required
                  autocomplete="off"
                  spellcheck="false"
                />
                <button type="button" id="btnClear" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[var(--text-muted)] hover:text-[var(--text-primary)] text-base font-bold p-1 cursor-pointer leading-none" title="Clear input" aria-label="Clear input">&times;</button>
              </div>

              <!-- Fetch Syllabus Submit Button -->
              <button
                type="submit"
                id="btnSubmit"
                class="w-full sm:w-auto h-9 px-4 rounded-xl syl-submit-btn text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center justify-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                </svg>
                <span>Fetch Syllabus</span>
              </button>
            </form>
          </div>

          <!-- 2. LOADING STATE BOX -->
          <div id="loadingBox" class="syl-loading hidden p-6 flex flex-col items-center justify-center text-center gap-2.5 rounded-2xl glass-card border border-white/80 dark:border-white/10 shrink-0" role="status" aria-live="polite">
            <div class="syl-spinner" aria-hidden="true"></div>
            <div class="text-xs sm:text-sm font-bold text-[var(--text-primary)]">Fetching &amp; Parsing Syllabus Tables…</div>
            <div class="text-[11px] text-[var(--text-muted)] max-w-md">Connecting to GMIU, reading HTML DOM, extracting subject codes, credits, and examination paper archives.</div>
          </div>

          <!-- 3. ERROR ALERT BANNER -->
          <div id="alertBox" class="syl-alert hidden p-3.5 rounded-2xl glass-card border border-rose-500/30 bg-rose-500/10 text-rose-700 dark:text-rose-400 flex items-center gap-3 shrink-0" role="alert">
            <div class="text-xl shrink-0" aria-hidden="true">⚠️</div>
            <div>
              <div id="alertTitle" class="text-xs font-bold font-serif">Scan Error</div>
              <p id="alertMsg" class="text-[11px] opacity-90 mt-0.5">Unable to process the target URL.</p>
            </div>
          </div>

          <!-- 4. RESULTS SECTION (Stats, Semester Tabs, Tables) -->
          <section id="resultsSection" class="hidden flex flex-col gap-3">

            <!-- 4.1 Stats & Export Grid (4 Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-2.5 shrink-0">
              <!-- Semesters Found -->
              <div class="stat-card p-2.5 sm:p-3">
                <span class="stat-card-lbl block">Semesters Found</span>
                <div id="statSem" class="stat-card-val">0</div>
              </div>

              <!-- Total Subjects -->
              <div class="stat-card p-2.5 sm:p-3">
                <span class="stat-card-lbl block">Total Subjects</span>
                <div id="statSub" class="stat-card-val">0</div>
              </div>

              <!-- Total Credits -->
              <div class="stat-card p-2.5 sm:p-3">
                <span class="stat-card-lbl block">Total Credits</span>
                <div id="statCred" class="stat-card-val">0</div>
              </div>

              <!-- Export All Details Button -->
              <?php if (!class_exists('Core\Auth') || \Core\Auth::can('syllabus.export_all')): ?>
              <div class="stat-card p-2 sm:p-2.5 flex items-center justify-center">
                <button
                  type="button"
                  id="btnExportAll"
                  class="w-full h-full min-h-[46px] rounded-xl bg-[var(--color-primary)] text-white text-[11px] sm:text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                  title="Export All Details (Syllabus & Exam Papers) as CSV"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>Export All (CSV)</span>
                </button>
              </div>
              <?php endif; ?>
            </div>

            <!-- 4.2 Semester Tabs Navigation Pills -->
            <div class="syl-sem-tabs" id="semTabs" role="tablist"></div>

            <!-- 4.3 Subject Syllabus Data Table Card -->
            <div class="glass-card rounded-2xl border border-white/80 dark:border-white/10 flex flex-col overflow-hidden shrink-0">
              <div class="flex items-center justify-between p-3 sm:p-3.5 border-b border-black/5 dark:border-white/10 bg-black/[0.01] dark:bg-white/[0.01] flex-wrap gap-2">
                <div class="flex items-center gap-2">
                  <h3 id="activeSemTitle" class="text-xs sm:text-sm font-bold font-serif text-[var(--text-primary)]">Semester 1</h3>
                  <span id="activeCountBadge" class="px-2.5 py-0.5 rounded-full glass-pill text-[10px] sm:text-[11px] font-bold text-[var(--color-primary)] border border-white/80 dark:border-white/10">0 Subjects</span>
                </div>
                <?php if (!class_exists('Core\Auth') || \Core\Auth::can('syllabus.export_subject')): ?>
                <button
                  type="button"
                  id="btnExportSubjectTable"
                  class="h-7 px-3 rounded-xl glass-pill text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm cursor-pointer"
                  title="Export Subject Syllabus as CSV"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>Export CSV</span>
                </button>
                <?php endif; ?>
              </div>
              <div class="syl-table-scroll overflow-x-auto max-h-[380px]">
                <table class="syl-table" role="grid">
                  <thead>
                    <tr>
                      <th scope="col">Subject Code</th>
                      <th scope="col">Subject Title / Name</th>
                      <th scope="col">Short Name</th>
                      <th scope="col" class="text-center">L</th>
                      <th scope="col" class="text-center">T</th>
                      <th scope="col" class="text-center">P</th>
                      <th scope="col" class="text-center">Credit</th>
                      <th scope="col">Syllabus</th>
                    </tr>
                  </thead>
                  <tbody id="tableBody"></tbody>
                </table>
              </div>
            </div>

            <!-- 4.4 Previous Exam Papers Data Table Card -->
            <div class="syl-exam-section glass-card rounded-2xl border border-white/80 dark:border-white/10 flex flex-col overflow-hidden shrink-0">
              <div class="flex items-center justify-between p-3 sm:p-3.5 border-b border-black/5 dark:border-white/10 bg-black/[0.01] dark:bg-white/[0.01] flex-wrap gap-2">
                <div class="flex items-center gap-2">
                  <h3 class="text-xs sm:text-sm font-bold font-serif text-[var(--text-primary)]">Previous Exam Papers</h3>
                  <span id="examCountBadge" class="px-2.5 py-0.5 rounded-full glass-pill text-[10px] sm:text-[11px] font-bold text-[var(--color-primary)] border border-white/80 dark:border-white/10">0 Papers</span>
                </div>
                <?php if (!class_exists('Core\Auth') || \Core\Auth::can('syllabus.export_exam')): ?>
                <button
                  type="button"
                  id="btnExportExamTable"
                  class="h-7 px-3 rounded-xl glass-pill text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm cursor-pointer"
                  title="Export Previous Exam Papers as CSV"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                  </svg>
                  <span>Export CSV</span>
                </button>
                <?php endif; ?>
              </div>
              <div class="syl-table-scroll overflow-x-auto max-h-[340px]">
                <table class="syl-exam-table" role="grid">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 100px;">YEAR</th>
                      <th scope="col" style="width: 130px;">SESSION</th>
                      <th scope="col">EXAM PAPER TITLE</th>
                      <th scope="col" style="width: 160px;">DOWNLOAD</th>
                      <th scope="col" style="width: 110px; text-align: center;">SEM</th>
                    </tr>
                  </thead>
                  <tbody id="examTableBody"></tbody>
                </table>
              </div>
            </div>

          </section>

        </div>

        <!-- WORKSPACE FOOTER -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-3 mt-3 border-t border-black/5 dark:border-white/10 text-[11px] text-[var(--text-muted)] shrink-0">
          <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-primary)]"></span>
            <span>Department of Computer Engineering &amp; Information Technology</span>
          </div>
          <div class="font-mono text-[10px] opacity-75">Official GMIU Academic Portal</div>
        </div>

      </main>

    </div>

  </div>

  <!-- Shared Sidebar and Navbar Scripts -->
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>

  <!-- Syllabus Scraper and Data Handler Script -->
  <script>
  (function () {
      'use strict';

      // ─── DOM References ───
      const form         = document.getElementById('scrapeForm');
      const urlInput     = document.getElementById('urlInput');
      const btnClear     = document.getElementById('btnClear');
      const btnSubmit    = document.getElementById('btnSubmit');
      const loadingBox   = document.getElementById('loadingBox');
      const alertBox     = document.getElementById('alertBox');
      const alertTitle   = document.getElementById('alertTitle');
      const alertMsg     = document.getElementById('alertMsg');
      const resultsSection = document.getElementById('resultsSection');

      const statSem  = document.getElementById('statSem');
      const statSub  = document.getElementById('statSub');
      const statCred = document.getElementById('statCred');

      const semTabs          = document.getElementById('semTabs');
      const activeSemTitle   = document.getElementById('activeSemTitle');
      const activeCountBadge = document.getElementById('activeCountBadge');
      const tableBody        = document.getElementById('tableBody');

      const btnExportSubjectTable = document.getElementById('btnExportSubjectTable');
      const btnExportExamTable    = document.getElementById('btnExportExamTable');
      const btnExportAll          = document.getElementById('btnExportAll');

      let parsedData = null;
      let parsedExamPapers = null;
      let currentActiveSem = 'Semester 1';

      // ─── Default Fallback Exam Papers Dataset (Semesters 1 - 8) ───
      const defaultExamPapers = {
          "SEMESTER 1": [
              { year: "2026", session: "Summer", title: "BETCE11301 - Object-Oriented Programming - I", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE11301_S26.pdf", sem: "1" },
              { year: "2026", session: "Summer", title: "BETEE10301 - Basic Electrical and Electronics Engineering", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETEE10301_S26.pdf", sem: "1" },
              { year: "2026", session: "Summer", title: "BETME10301 - Engineering Graphics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETME10301_S26.pdf", sem: "1" },
              { year: "2026", session: "Summer", title: "BETXX10201 - Mathematics-I", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10201_S26.pdf", sem: "1" },
              { year: "2026", session: "Summer", title: "BETCE10201 - WordPress (Content Management System)", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10201_S26.pdf", sem: "1" },
              { year: "2026", session: "Summer", title: "BETXX10202 - Communication Skill", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10202_S26.pdf", sem: "1" },
              { year: "2025", session: "Winter", title: "BETCE11301 - Object-Oriented Programming - I", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE11301_W25.pdf", sem: "1" },
              { year: "2025", session: "Winter", title: "BETXX10201 - Mathematics-I", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10201_W25.pdf", sem: "1" }
          ],
          "SEMESTER 2": [
              { year: "2026", session: "Summer", title: "BETXX10205 - Physics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10205.pdf", sem: "2" },
              { year: "2026", session: "Summer", title: "BETEE10302 - Digital Electronics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETEE10302.pdf", sem: "2" },
              { year: "2026", session: "Summer", title: "BETCE12302 - Object Oriented Programming-II", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE12302.pdf", sem: "2" },
              { year: "2026", session: "Summer", title: "BETXX10207 - Technical Communication", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10207.pdf", sem: "2" },
              { year: "2025", session: "Winter", title: "BETEE10302 - Digital Electronics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETEE10302_W25.pdf", sem: "2" }
          ],
          "SEMESTER 3": [
              { year: "2026", session: "Summer", title: "BETCE10301 - Data Structures & Algorithms", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10301.pdf", sem: "3" },
              { year: "2026", session: "Summer", title: "BETCE10302 - Computer Organization & Architecture", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10302.pdf", sem: "3" },
              { year: "2026", session: "Summer", title: "BETCE10303 - Database Management Systems", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10303.pdf", sem: "3" },
              { year: "2026", session: "Summer", title: "BETXX10304 - Discrete Mathematics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETXX10304.pdf", sem: "3" }
          ],
          "SEMESTER 4": [
              { year: "2026", session: "Summer", title: "BETCE10401 - Operating Systems", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10401.pdf", sem: "4" },
              { year: "2026", session: "Summer", title: "BETCE10402 - Object Oriented Programming with Java", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10402.pdf", sem: "4" },
              { year: "2026", session: "Summer", title: "BETCE10403 - Computer Networks", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10403.pdf", sem: "4" },
              { year: "2026", session: "Summer", title: "BETCE10404 - Software Engineering", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10404.pdf", sem: "4" }
          ],
          "SEMESTER 5": [
              { year: "2026", session: "Summer", title: "BETCE10501 - Theory of Computation", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10501.pdf", sem: "5" },
              { year: "2026", session: "Summer", title: "BETCE10502 - Design & Analysis of Algorithms", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10502.pdf", sem: "5" },
              { year: "2026", session: "Summer", title: "BETCE10503 - Web Technology & Frameworks", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10503.pdf", sem: "5" }
          ],
          "SEMESTER 6": [
              { year: "2026", session: "Summer", title: "BETCE10601 - Artificial Intelligence & Machine Learning", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10601.pdf", sem: "6" },
              { year: "2026", session: "Summer", title: "BETCE10602 - Cloud Computing & DevOps", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10602.pdf", sem: "6" },
              { year: "2026", session: "Summer", title: "BETCE10603 - Mobile Application Development", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10603.pdf", sem: "6" }
          ],
          "SEMESTER 7": [
              { year: "2026", session: "Summer", title: "BETCE10701 - Big Data Analytics", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10701.pdf", sem: "7" },
              { year: "2026", session: "Summer", title: "BETCE10702 - Internet of Things (IoT)", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10702.pdf", sem: "7" },
              { year: "2026", session: "Summer", title: "BETCE10703 - Information & Network Security", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10703.pdf", sem: "7" }
          ],
          "SEMESTER 8": [
              { year: "2026", session: "Summer", title: "BETCE10801 - Industry Internship / Major Project Phase-II", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10801.pdf", sem: "8" },
              { year: "2026", session: "Summer", title: "BETCE10802 - Deep Learning & Neural Networks", link: "https://gmiu.edu.in/gmiu/website/assets/pdf/papers/BETCE10802.pdf", sem: "8" }
          ]
      };

      // ─── Clear Button ───
      btnClear.addEventListener('click', () => {
          urlInput.value = '';
          urlInput.focus();
      });

      // ─── Department Dropdown ───
      const deptSelect = document.getElementById('deptSelect');
      if (deptSelect) {
          deptSelect.addEventListener('change', () => {
              const url = deptSelect.value;
              if (!url) return;
              urlInput.value = url;
              form.dispatchEvent(new Event('submit', { bubbles: true }));
          });
      }

      // ─── Form Submit ───
      form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const url = urlInput.value.trim();
          if (!url) return;

          // Reset UI
          alertBox.classList.add('hidden');
          resultsSection.classList.add('hidden');
          loadingBox.classList.remove('hidden');
          btnSubmit.disabled = true;

          try {
              const res    = await fetch('scrape.php?url=' + encodeURIComponent(url));
              const result = await res.json();

              loadingBox.classList.add('hidden');
              btnSubmit.disabled = false;

              if (!result.success) {
                  showAlert('Syllabus Fetch Failed', result.error || 'Unknown server error occurred.');
                  return;
              }

              if (!result.data || Object.keys(result.data).length === 0) {
                  showAlert('No Syllabus Tables Found', 'No tables containing "SUBJECT CODE" were detected on the target page. Try a different URL.');
                  return;
              }

              parsedData = result.data;
              renderResults(result);

          } catch (err) {
              loadingBox.classList.add('hidden');
              btnSubmit.disabled = false;
              showAlert('Connection Error', 'Failed to reach the scrape API: ' + err.message);
          }
      });

      // ─── Render Results ───
      function renderResults(result) {
          const sems = Object.keys(parsedData);
          parsedExamPapers = result.examPapers || null;

          // Stats
          statSem.textContent  = sems.length;
          statSub.textContent  = result.totalSubjects || 0;

          let totalCredits = 0;
          sems.forEach(s => parsedData[s].forEach(row => {
              const v = parseFloat(row.credit);
              if (!isNaN(v)) totalCredits += v;
          }));
          statCred.textContent = totalCredits;

          // Semester Tabs
          semTabs.innerHTML = '';
          sems.forEach((sem, i) => {
              const btn = document.createElement('button');
              btn.type      = 'button';
              btn.className = 'syl-sem-tab' + (i === 0 ? ' active' : '');
              btn.textContent = sem;
              btn.setAttribute('role', 'tab');
              btn.setAttribute('aria-selected', i === 0 ? 'true' : 'false');
              btn.addEventListener('click', () => {
                  document.querySelectorAll('.syl-sem-tab').forEach(t => {
                      t.classList.remove('active');
                      t.setAttribute('aria-selected', 'false');
                  });
                  btn.classList.add('active');
                  btn.setAttribute('aria-selected', 'true');
                  currentActiveSem = sem;
                  renderTable(sem);
                  renderExamPapers(sem);
              });
              semTabs.appendChild(btn);
          });

          if (sems.length) {
              currentActiveSem = sems[0];
              renderTable(sems[0]);
              renderExamPapers(sems[0]);
          }

          resultsSection.classList.remove('hidden');
      }

      // ─── Render Subject Table ───
      function renderTable(semName, filterQuery = '') {
          activeSemTitle.textContent = semName;
          let rows = parsedData[semName] || [];

          if (filterQuery && filterQuery.trim() !== '') {
              const q = filterQuery.toLowerCase().trim();
              rows = rows.filter(r => 
                  (r.code && r.code.toLowerCase().includes(q)) ||
                  (r.name && r.name.toLowerCase().includes(q)) ||
                  (r.short && r.short.toLowerCase().includes(q))
              );
          }

          activeCountBadge.textContent = rows.length + (rows.length === 1 ? ' Subject' : ' Subjects');
          tableBody.innerHTML = '';

          if (!rows.length) {
              const tr = document.createElement('tr');
              tr.innerHTML = '<td colspan="8" style="text-align:center; color:var(--text-muted); padding:32px;">No subject records found matching the query.</td>';
              tableBody.appendChild(tr);
              return;
          }

          rows.forEach(row => {
              const tr = document.createElement('tr');
              const hasLink = row.link && row.link.trim() !== '';

              const codeHtml = hasLink 
                  ? '<a href="' + esc(row.link) + '" target="_blank" rel="noopener noreferrer" class="code-pill code-pill-link" title="Open Syllabus PDF">' + esc(row.code) + '</a>'
                  : '<span class="code-pill">' + esc(row.code) + '</span>';

              const downloadHtml = hasLink
                  ? '<a href="' + esc(row.link) + '" target="_blank" rel="noopener noreferrer" download class="syl-download-btn" title="Download Syllabus PDF">' +
                      '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">' +
                          '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>' +
                          '<polyline points="7 10 12 15 17 10"></polyline>' +
                          '<line x1="12" y1="15" x2="12" y2="3"></line>' +
                      '</svg>' +
                      '<span>Download</span>' +
                    '</a>'
                  : '<span class="syl-download-btn disabled" title="Syllabus file not available">' +
                      '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">' +
                          '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>' +
                          '<polyline points="7 10 12 15 17 10"></polyline>' +
                          '<line x1="12" y1="15" x2="12" y2="3"></line>' +
                      '</svg>' +
                      '<span>N/A</span>' +
                    '</span>';

              tr.innerHTML =
                  '<td>' + codeHtml + '</td>' +
                  '<td><span class="font-semibold text-[var(--text-primary)]">'  + esc(row.name) + '</span></td>' +
                  '<td><span class="text-xs font-mono text-[var(--text-muted)]">' + esc(row.short) + '</span></td>' +
                  '<td class="text-center font-mono text-xs">' + esc(row.l) + '</td>' +
                  '<td class="text-center font-mono text-xs">' + esc(row.t) + '</td>' +
                  '<td class="text-center font-mono text-xs">' + esc(row.p) + '</td>' +
                  '<td class="text-center"><span class="font-bold text-xs text-[var(--color-primary)]">' + esc(row.credit) + '</span></td>' +
                  '<td>' + downloadHtml + '</td>';
              tableBody.appendChild(tr);
          });
      }

      // ─── Render Previous Exam Papers ───
      function renderExamPapers(semName, filterQuery = '') {
          const examSection    = document.querySelector('.syl-exam-section');
          const examTableBody  = document.getElementById('examTableBody');
          const examCountBadge = document.getElementById('examCountBadge');
          if (!examTableBody) return;

          const semNum = semName.replace(/[^\d]/g, '') || '1';
          const semKey = "SEMESTER " + semNum;

          let rows = (parsedExamPapers && parsedExamPapers[semKey] && parsedExamPapers[semKey].length > 0)
              ? parsedExamPapers[semKey]
              : [];

          if (filterQuery && filterQuery.trim() !== '') {
              const q = filterQuery.toLowerCase().trim();
              rows = rows.filter(r => 
                  (r.title && r.title.toLowerCase().includes(q)) ||
                  (r.year && r.year.toLowerCase().includes(q)) ||
                  (r.session && r.session.toLowerCase().includes(q))
              );
          }

          if (!rows.length) {
              if (examSection) examSection.classList.add('hidden');
              return;
          }

          if (examSection) examSection.classList.remove('hidden');
          examCountBadge.textContent = rows.length + (rows.length === 1 ? ' Paper' : ' Papers');
          examTableBody.innerHTML = '';

          rows.forEach(row => {
              const tr = document.createElement('tr');
              const hasLink = row.link && row.link.trim() !== '';
              const sessionClass = (row.session || '').toLowerCase().includes('winter') ? 'session-pill session-winter' : 'session-pill session-summer';

              const docHtml = hasLink
                  ? '<a href="' + esc(row.link) + '" target="_blank" rel="noopener noreferrer" download class="syl-download-btn" title="Download Previous Exam Paper PDF">' +
                      '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">' +
                          '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>' +
                          '<polyline points="7 10 12 15 17 10"></polyline>' +
                          '<line x1="12" y1="15" x2="12" y2="3"></line>' +
                      '</svg>' +
                      '<span>Download</span>' +
                    '</a>'
                  : '<span class="syl-download-btn disabled" title="Paper document not available">' +
                      '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">' +
                          '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>' +
                          '<polyline points="7 10 12 15 17 10"></polyline>' +
                          '<line x1="12" y1="15" x2="12" y2="3"></line>' +
                      '</svg>' +
                      '<span>N/A</span>' +
                    '</span>';

              tr.innerHTML =
                  '<td><span class="year-pill">' + esc(row.year) + '</span></td>' +
                  '<td><span class="' + sessionClass + '">' + esc(row.session) + '</span></td>' +
                  '<td><span class="font-semibold text-[var(--text-primary)]">' + esc(row.title) + '</span></td>' +
                  '<td>' + docHtml + '</td>' +
                  '<td style="text-align: center;"><span class="sem-pill">Sem ' + esc(row.sem || semNum) + '</span></td>';
              examTableBody.appendChild(tr);
          });
      }

      // ─── Export CSV Functions ───
      function downloadCSV(filename, csvContent) {
          const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
          const url  = URL.createObjectURL(blob);
          const a    = document.createElement('a');
          a.href     = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
      }

      // 1. Export Subject Syllabus Table CSV (Table-wise)
      function exportSubjectCSV() {
          if (!parsedData) return;
          let csv = 'Semester,Subject Code,Subject Name,Short Name,L,T,P,Credit,Syllabus URL\n';
          Object.keys(parsedData).forEach(sem => {
              parsedData[sem].forEach(row => {
                  csv += [
                      '"' + sem + '"',
                      '"' + row.code + '"',
                      '"' + (row.name || '').replace(/"/g, '""') + '"',
                      '"' + (row.short || '-') + '"',
                      row.l, row.t, row.p, row.credit,
                      '"' + (row.link || '') + '"'
                  ].join(',') + '\n';
              });
          });
          downloadCSV('GMIU_Subject_Syllabus.csv', csv);
      }

      // 2. Export Previous Exam Papers Table CSV (Table-wise)
      function exportExamPapersCSV() {
          let csv = 'Semester,Year,Session,Exam Paper Title,PDF Download URL\n';
          const sourceData = parsedExamPapers || defaultExamPapers;
          Object.keys(sourceData).forEach(sem => {
              (sourceData[sem] || []).forEach(row => {
                  csv += [
                      '"' + (sem || 'Semester 1') + '"',
                      '"' + (row.year || '') + '"',
                      '"' + (row.session || '') + '"',
                      '"' + (row.title || '').replace(/"/g, '""') + '"',
                      '"' + (row.link || '') + '"'
                  ].join(',') + '\n';
              });
          });
          downloadCSV('GMIU_Previous_Exam_Papers.csv', csv);
      }

      // 3. Export All Details CSV (Combined Syllabus + Exam Papers)
      function exportAllDetailsCSV() {
          if (!parsedData) return;
          let csv = '=== SUBJECT SYLLABUS ===\n';
          csv += 'Semester,Subject Code,Subject Name,Short Name,L,T,P,Credit,Syllabus URL\n';
          Object.keys(parsedData).forEach(sem => {
              parsedData[sem].forEach(row => {
                  csv += [
                      '"' + sem + '"',
                      '"' + row.code + '"',
                      '"' + (row.name || '').replace(/"/g, '""') + '"',
                      '"' + (row.short || '-') + '"',
                      row.l, row.t, row.p, row.credit,
                      '"' + (row.link || '') + '"'
                  ].join(',') + '\n';
              });
          });

          csv += '\n=== PREVIOUS EXAM PAPERS ===\n';
          csv += 'Semester,Year,Session,Exam Paper Title,PDF Download URL\n';
          const examSource = parsedExamPapers || defaultExamPapers;
          Object.keys(examSource).forEach(sem => {
              (examSource[sem] || []).forEach(row => {
                  csv += [
                      '"' + sem + '"',
                      '"' + (row.year || '') + '"',
                      '"' + (row.session || '') + '"',
                      '"' + (row.title || '').replace(/"/g, '""') + '"',
                      '"' + (row.link || '') + '"'
                  ].join(',') + '\n';
              });
          });

          downloadCSV('GMIU_Syllabus_and_Exam_Papers_All_Details.csv', csv);
      }

      if (btnExportSubjectTable) btnExportSubjectTable.addEventListener('click', exportSubjectCSV);
      if (btnExportExamTable)    btnExportExamTable.addEventListener('click', exportExamPapersCSV);
      if (btnExportAll)          btnExportAll.addEventListener('click', exportAllDetailsCSV);

      // ─── Navbar Search Filter Integration ───
      const navSearch = document.getElementById('navSearchInput');
      if (navSearch) {
          navSearch.addEventListener('input', (e) => {
              const query = e.target.value;
              if (parsedData && currentActiveSem) {
                  renderTable(currentActiveSem, query);
                  renderExamPapers(currentActiveSem, query);
              }
          });
      }

      // ─── Helpers ───
      function showAlert(title, msg) {
          alertTitle.textContent = title;
          alertMsg.textContent   = msg;
          alertBox.classList.remove('hidden');
      }

      function esc(str) {
          if (str == null) return '';
          return String(str)
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;');
      }

  })();
  </script>

</body>
</html>