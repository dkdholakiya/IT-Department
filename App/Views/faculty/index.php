<?php
$page_title = 'Faculty List — CE & IT Department';
$page_css = ['assets/css/faculty.css'];
include dirname(__DIR__) . '/partials/dashboard-head.php';
?>

  <!-- MAIN WRAPPER: Balanced Responsive Top Spacing (Snug 6-8px Gap below Mobile Fixed Navbar, 100vh on Desktop) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'faculty';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Faculty List';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + FACULTY WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Faculty List';
      $nav_subtitle = 'CE & IT Department • Directory & Management';
      $nav_search_placeholder = 'Search faculty by name, initials, ID, room...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS FACULTY DIRECTORY & TABLE ================= -->
      <main class="fac-main-workspace flex-1 min-h-0 min-w-0 glass-card p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">

        <!-- 1. TOP HEADER & CONTROLS TOOLBAR (Consolidated Single Row on Desktop) -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-2.5 pb-2.5 border-b border-black/5 dark:border-white/10 shrink-0">
          
          <!-- Hidden total badge for JS reactivity compatibility -->
          <span id="totalFacultyBadge" class="hidden" aria-hidden="true"></span>

          <!-- Left: Department Segment & Status Filter Segment (In place of removed title block) -->
          <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5 shrink-0 flex-wrap sm:flex-nowrap">
            <!-- Department Pills -->
            <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0">
              <button
                type="button"
                id="deptFilterAll"
                class="fac-seg-btn active"
              >
                <span class="hidden md:inline">All Departments</span>
                <span class="md:hidden">All</span>
              </button>
              <button
                type="button"
                id="deptFilterIt"
                class="fac-seg-btn"
              >
                <span class="hidden md:inline">Information Technology</span>
                <span class="md:hidden">IT</span>
              </button>
              <button
                type="button"
                id="deptFilterCe"
                class="fac-seg-btn"
              >
                <span class="hidden md:inline">Computer Engineering</span>
                <span class="md:hidden">CE</span>
              </button>
            </div>

            <!-- Status Filter Pills -->
            <div class="inline-flex items-center p-0.5 rounded-full glass-pill border border-white/80 dark:border-white/10 shrink-0">
              <button
                type="button"
                id="statusFilterAll"
                class="fac-seg-btn active"
                title="Show all records"
              >
                All
              </button>
              <button
                type="button"
                id="statusFilterActive"
                class="fac-seg-btn"
                title="Filter active faculty only"
              >
                Active
              </button>
              <button
                type="button"
                id="statusFilterInactive"
                class="fac-seg-btn"
                title="Filter inactive faculty only"
              >
                Inactive
              </button>
            </div>
          </div>

          <!-- Right: Search Input + Action Buttons (Reset Defaults, Export Excel, + Add Faculty) -->
          <div class="flex items-center gap-1.5 sm:gap-2 self-start xl:self-center flex-wrap shrink-0">
            <!-- Search Bar -->
            <div class="relative flex items-center h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill border border-white/80 dark:border-white/10 shadow-sm w-36 sm:w-44 lg:w-48 shrink-0 transition focus-within:ring-1 focus-within:ring-[var(--color-primary)]">
              <svg class="w-3.5 h-3.5 text-[var(--text-muted)] shrink-0 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <input
                type="text"
                id="facultySearchInput"
                class="fac-search-input w-full bg-transparent text-[11px] sm:text-xs font-semibold text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none"
                placeholder="Search faculty..."
                autocomplete="off"
              />
            </div>

            <!-- Reset Defaults Button -->
            <button
              type="button"
              id="resetFacultyDefaultsBtn"
              class="h-7 sm:h-8 px-2 sm:px-2.5 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 shrink-0 cursor-pointer"
              title="Reset back to default faculty list"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
              </svg>
              <span class="hidden md:inline">Reset Defaults</span>
            </button>

            <!-- Export Excel Button -->
            <button
              type="button"
              id="exportExcelBtn"
              class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl glass-pill text-[10.5px] sm:text-[11px] font-bold text-[var(--text-primary)] hover:bg-[var(--color-primary)] hover:text-white transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              title="Export filtered faculty list to Excel"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
              <span class="hidden xs:inline">Export</span><span class="hidden md:inline">&nbsp;Excel</span>
            </button>

            <!-- + Add Faculty Button -->
            <button
              type="button"
              id="addFacultyBtn"
              class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-xl bg-[var(--color-primary)] text-white text-[10.5px] sm:text-[11px] font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer"
              title="Add a new faculty member"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
              <span>Add Faculty</span>
            </button>
          </div>

        </div>

        <!-- 2. MAIN SCROLLABLE TABLE AREA -->
        <div class="fac-table-card flex-1 min-h-0 my-2">
          <div class="fac-table-wrapper rounded-2xl border border-black/5 dark:border-white/10">
            <table class="fac-table">
              <thead>
                <tr>
                  <th class="w-10 sm:w-12 text-center hidden sm:table-cell">#</th>
                  <th>Faculty Member</th>
                  <th>Department</th>
                  <th class="hidden md:table-cell">Designation</th>
                  <th class="hidden lg:table-cell">Employee ID</th>
                  <th class="hidden sm:table-cell">Contact Details</th>
                  <th>Status</th>
                  <th class="action-col-header text-right pr-3 sm:pr-4">Action</th>
                </tr>
              </thead>
              <tbody id="facultyTableBody">
                <!-- Dynamically rendered via assets/js/faculty.js -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- 3. BOTTOM PAGINATION & METRICS FOOTER -->
        <div class="pt-2.5 sm:pt-3 border-t border-black/5 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2.5 sm:gap-3 shrink-0">
          <!-- Summary Text & Page Size Selector -->
          <div class="flex items-center gap-3 text-xs text-[var(--text-muted)]">
            <span id="tableInfoText" class="font-semibold select-none">
              Loading faculty members...
            </span>

            <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-black/10 dark:border-white/10">
              <label for="pageSizeSelect" class="font-medium text-[11px] select-none">Rows per page:</label>
              <select
                id="pageSizeSelect"
                class="px-2 py-0.5 rounded-lg glass-pill font-bold text-xs text-[var(--text-primary)] border border-white/80 dark:border-white/10 focus:outline-none cursor-pointer"
              >
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="all">All</option>
              </select>
            </div>
          </div>

          <!-- Page Navigation Buttons -->
          <div id="paginationControls" class="flex items-center gap-1 shrink-0">
            <!-- Dynamically rendered via assets/js/faculty.js -->
          </div>
        </div>

      </main>

    </div><!-- /Workspace -->

  </div><!-- /Main Wrapper -->


  <!-- ================= POPUP MODALS CONTAINER ================= -->

  <!-- 1. VIEW FACULTY DETAILS MODAL -->
  <div id="viewFacultyModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-3xl lg:max-w-4xl p-5 sm:p-7 rounded-3xl relative shadow-2xl border border-white/80 max-h-[92vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-black/10 dark:border-white/10">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-2xl bg-[var(--color-primary)]/15 text-[var(--color-primary)] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-[var(--text-primary)]">Faculty Profile Details</h3>
            <p class="text-[11px] text-[var(--text-muted)]">Full mentor credentials & contact record</p>
          </div>
        </div>
        <button type="button" class="close-modal-trigger w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" aria-label="Close">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div id="viewModalBody">
        <!-- Injected dynamically via JS -->
      </div>
    </div>
  </div>


  <!-- 2. SHOW CREDENTIALS MODAL -->
  <div id="credentialsModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-xl p-5 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 max-h-[92vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-black/10 dark:border-white/10">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-2xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
            </svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-[var(--text-primary)]">Portal Sign-In Credentials</h3>
            <p class="text-[11px] text-[var(--text-muted)]">Confidential authentication credentials</p>
          </div>
        </div>
        <button type="button" class="close-modal-trigger w-8 h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" aria-label="Close">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div id="credentialsModalBody">
        <!-- Injected dynamically via JS -->
      </div>
    </div>
  </div>


  <!-- 3. ADD / EDIT FACULTY FORM MODAL -->
  <div id="facultyFormModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-md hidden items-center justify-center p-3 sm:p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-4xl lg:max-w-5xl p-4 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 max-h-[94vh] overflow-y-auto">
      <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-black/10 dark:border-white/10">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-2xl bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
          </div>
          <div>
            <h3 id="formModalTitle" class="text-sm sm:text-base font-bold text-[var(--text-primary)]">Configure Faculty Profile</h3>
            <p class="text-[10.5px] text-[var(--text-muted)]">Configure department role & credentials</p>
          </div>
        </div>
        <button type="button" class="close-modal-trigger w-7 h-7 sm:w-8 sm:h-8 rounded-full glass-pill flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-primary)] transition" aria-label="Close">
          <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form id="facultyForm" class="space-y-3 text-xs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3">
          <!-- Row 1: Full Name (2 cols) & Initials (1 col) -->
          <div class="sm:col-span-2 lg:col-span-2">
            <label for="inputName" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Full Name <span class="text-red-500">*</span></label>
            <input
              type="text"
              id="inputName"
              required
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-medium text-xs"
              placeholder="e.g. Prof. Tarjanee Vyas"
            />
          </div>

          <div>
            <label for="inputInitials" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Initials / Short Code</label>
            <input
              type="text"
              id="inputInitials"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono uppercase text-xs"
              placeholder="e.g. TMV"
            />
          </div>

          <!-- Row 2: Employee ID, Designation, Department -->
          <div>
            <label for="inputEmpId" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Employee ID</label>
            <input
              type="text"
              id="inputEmpId"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono text-xs"
              placeholder="e.g. #10395"
            />
          </div>

          <div>
            <label for="inputDesignation" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Designation / Role</label>
            <input
              type="text"
              id="inputDesignation"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-medium text-xs"
              placeholder="e.g. Sub Incharge HOD IT"
            />
          </div>

          <div>
            <label for="inputDepartment" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Department</label>
            <select
              id="inputDepartment"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-semibold text-xs cursor-pointer"
            >
              <option value="Information Technology">Information Technology</option>
              <option value="Computer Engineering">Computer Engineering</option>
              <option value=" IT & CE ">IT & CE (Both)</option>
            </select>
          </div>

          <!-- Row 3: Cabin / Room, Status, Security Role -->
          <div>
            <label for="inputSetting" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Cabin / Setting Room</label>
            <input
              type="text"
              id="inputSetting"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-medium text-xs"
              placeholder="e.g. FF-37"
            />
          </div>

          <div>
            <label for="inputStatus" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Status</label>
            <select
              id="inputStatus"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-semibold text-xs cursor-pointer"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div>
            <label for="inputRole" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Portal Security Role</label>
            <select
              id="inputRole"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-semibold text-xs cursor-pointer"
            >
              <option value="staff">Staff Member</option>
              <option value="super_admin">Super Administrator</option>
            </select>
          </div>

          <!-- Row 4: Email, Mobile, Username -->
          <div>
            <label for="inputEmail" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Email Address</label>
            <input
              type="email"
              id="inputEmail"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-medium text-xs"
              placeholder="e.g. tmvyas@gmiu.edu.in"
            />
          </div>

          <div>
            <label for="inputPhone" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Mobile Number</label>
            <input
              type="text"
              id="inputPhone"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono text-xs"
              placeholder="e.g. 81409 67331"
            />
          </div>

          <div>
            <label for="inputUsername" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Portal Username</label>
            <input
              type="text"
              id="inputUsername"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono lowercase text-xs"
              placeholder="e.g. tmvyas"
            />
          </div>

          <!-- Row 5: Password, Semester, Master Drive URL -->
          <div>
            <label for="inputPassword" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Login Password <span class="text-[10px] font-normal text-[var(--text-muted)]">(Default: Mobile)</span></label>
            <input
              type="text"
              id="inputPassword"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono text-xs"
              placeholder="Leave blank to use mobile"
            />
          </div>

          <div>
            <label for="inputSemClass" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Semester / Master Folder</label>
            <input
              type="text"
              id="inputSemClass"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-medium text-xs"
              placeholder="e.g. Diploma 5th Sem - B (IT)"
            />
          </div>

          <div>
            <label for="inputDriveLink" class="block font-bold text-[var(--text-primary)] mb-1 text-[11px]">Master Google Drive URL</label>
            <input
              type="url"
              id="inputDriveLink"
              class="w-full px-3 py-2 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] font-mono text-[10.5px]"
              placeholder="https://drive.google.com/..."
            />
          </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-2.5 border-t border-black/5 dark:border-white/10 flex items-center justify-end gap-2">
          <button
            type="button"
            class="close-modal-trigger px-4 py-2 rounded-xl glass-pill font-bold text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)] transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-5 py-2 rounded-xl bg-[var(--color-primary)] text-white font-bold text-xs shadow-md hover:brightness-105 active:scale-95 transition"
          >
            Save Faculty Profile
          </button>
        </div>
      </form>
    </div>
  </div>
    </div>
  </div>


  <!-- 3. DELETE CONFIRMATION MODAL -->
  <div id="deleteConfirmModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="glass-card bg-[var(--glass-surface-strong)] w-full max-w-sm p-5 sm:p-6 rounded-3xl relative shadow-2xl border border-white/80 text-center">
      <div class="w-12 h-12 rounded-2xl bg-red-500/15 text-red-500 flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
      </div>

      <h3 class="text-base font-bold text-[var(--text-primary)]">Delete Faculty Member</h3>
      <p class="text-xs text-[var(--text-muted)] mt-1.5 leading-relaxed">
        Are you sure you want to remove <strong id="deleteFacultyName" class="text-[var(--text-primary)]">this member</strong> from the active faculty records?
      </p>

      <div class="mt-5 flex items-center justify-center gap-2 text-xs font-bold">
        <button
          type="button"
          class="close-modal-trigger flex-1 py-2.5 rounded-xl glass-pill text-[var(--text-muted)] hover:text-[var(--text-primary)]"
        >
          Cancel
        </button>
        <button
          type="button"
          id="confirmDeleteBtn"
          class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white shadow-md active:scale-95 transition"
        >
          Yes, Delete
        </button>
      </div>
    </div>
  </div>


  <!-- ================= APPLICATION JAVASCRIPT ================= -->
  <script>
    window.serverFacultyList = <?php echo json_encode($facultyList ?? []); ?>;
    window.isSuperAdmin = <?php echo !empty($isSuperAdmin) ? 'true' : 'false'; ?>;
    window.portalBaseUrl = "<?php echo function_exists('base_url') ? base_url('') : '/'; ?>";
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/facultyData.js') : 'assets/js/facultyData.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/faculty.js') : 'assets/js/faculty.js'; ?>"></script>
</body>
</html>
