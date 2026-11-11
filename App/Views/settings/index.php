<?php
/**
 * Department Settings & Role-Based Access Control (RBAC) Hub
 * Department of CE & IT — Smart Management Dashboard
 */
$page_title = 'Settings & Roles — CE & IT Department';
include dirname(__DIR__) . '/partials/dashboard-head.php';

$baseUrl = function_exists('base_url') ? base_url('') : './';
?>

  <!-- MAIN WRAPPER: Responsive Top Spacing (Snug 6-8px Gap below Mobile Fixed Navbar, 100vh on Desktop) -->
  <div class="max-w-[1600px] w-full mx-auto px-3 sm:px-5 lg:px-6 pt-[72px] sm:pt-[76px] lg:pt-3.5 pb-6 sm:pb-8 lg:pb-3.5 min-h-screen lg:h-screen lg:max-h-screen flex flex-col lg:flex-row gap-2.5 sm:gap-3 lg:gap-4 overflow-x-hidden lg:overflow-hidden select-none">

    <!-- Shared Samsung Edge Shortcut Sidebar -->
    <?php
    $active_page = 'settings';
    $sidebar_mode = 'nav';
    $sidebar_active_label = 'Settings & Roles';
    include dirname(__DIR__) . '/partials/sidebar.php';
    ?>

    <!-- ================= RIGHT COLUMN: TOP NAVBAR + SETTINGS WORKSPACE ================= -->
    <div class="flex-1 min-h-0 min-w-0 flex flex-col gap-2.5 sm:gap-3 lg:gap-3">

      <!-- ================= TOP NAVIGATION BAR (SHARED PARTIAL) ================= -->
      <?php
      $nav_title = 'Department Settings';
      $nav_subtitle = 'CE & IT Department • Role & Permission Management';
      $nav_search_placeholder = 'Search permissions, roles, modules, or faculty...';
      include dirname(__DIR__) . '/partials/navbar.php';
      ?>

      <!-- ================= MAIN WORKSPACE: GLASS SETTINGS & RBAC CANVAS ================= -->
      <main class="flex-1 min-h-0 min-w-0 glass-card p-3 sm:p-5 flex flex-col justify-between overflow-hidden relative transition-all duration-300">

        <!-- 1. TOP HEADER & NAVIGATION TABS BAR -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-black/5 dark:border-white/10 shrink-0">
          
          <!-- Left: Tab Navigation Buttons -->
          <div class="inline-flex items-center p-1 rounded-2xl glass-pill border border-white/80 dark:border-white/10 shrink-0 self-start md:self-auto gap-1">
            <button
              type="button"
              id="tabOverviewBtn"
              data-tab="overview"
              class="settings-tab-btn active px-3 sm:px-4 py-1.5 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer bg-[var(--color-primary)] text-white shadow-sm"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
              </svg>
              <span>Settings Hub</span>
            </button>

            <button
              type="button"
              id="tabRolesBtn"
              data-tab="roles"
              class="settings-tab-btn px-3 sm:px-4 py-1.5 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
              <span>Roles & Permissions</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-white/20 dark:bg-white/10 text-current ml-0.5">4 Roles</span>
            </button>

            <button
              type="button"
              id="tabUsersBtn"
              data-tab="users"
              class="settings-tab-btn px-3 sm:px-4 py-1.5 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
              <span>Staff Roles</span>
              <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-white/20 dark:bg-white/10 text-current ml-0.5"><?php echo count($users); ?></span>
            </button>
          </div>

          <!-- Right: Active Role Status Pill & Sync Status -->
          <div class="flex items-center gap-2 self-end md:self-auto">
            <span class="text-[11px] text-[var(--text-muted)] hidden sm:inline">Active Session:</span>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full glass-pill border border-white/80 dark:border-white/10 text-xs font-bold text-[var(--text-primary)]">
              <span class="w-2 h-2 rounded-full bg-[var(--color-primary)] animate-pulse"></span>
              <span><?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?></span>
              <span class="px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider bg-[var(--color-primary)]/15 text-[var(--color-primary)]">
                <?php
                  $currRole = \Core\Auth::userRole();
                  if ($currRole === 'super_admin') echo 'Super Admin (Dev)';
                  elseif ($currRole === 'hod') echo 'HOD';
                  elseif ($currRole === 'sub_admin') echo 'Sub Admin';
                  else echo 'Faculty';
                ?>
              </span>
            </div>
          </div>
        </div>

        <!-- ================= TAB 1: OVERVIEW & BENTO CARDS HUB ================= -->
        <div id="sectionOverview" class="tab-section flex-1 min-h-0 overflow-y-auto no-scrollbar py-3.5 space-y-4">

          <!-- Bento Grid of Settings Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">

            <!-- ================= HERO CARD 1: ROLE & PERMISSION MANAGEMENT (MAIN MODULE) ================= -->
            <div class="md:col-span-2 glass-card p-5 sm:p-6 rounded-3xl relative overflow-hidden flex flex-col justify-between border-2 border-[var(--color-primary)]/40 shadow-xl group hover:border-[var(--color-primary)] transition-all duration-300">
              <!-- Ambient decorative glow in background -->
              <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-[var(--color-primary)]/15 blur-3xl pointer-events-none group-hover:bg-[var(--color-primary)]/25 transition-all"></div>
              
              <div>
                <!-- Top Badge Pill -->
                <div class="flex items-center justify-between gap-2 mb-3">
                  <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[var(--color-primary)] text-white shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Role & Permission Governance</span>
                  </div>
                  <span class="text-xs font-bold text-[var(--color-primary)]">Model-Wise Control</span>
                </div>

                <!-- Hero Title & Subtitle -->
                <h2 class="text-xl sm:text-2xl font-black text-[var(--text-primary)] tracking-tight">Role & Permission Management</h2>
                <p class="text-xs sm:text-[13px] text-[var(--text-muted)] mt-1.5 leading-relaxed max-w-xl">
                  Configure granular model-wise access rights across the entire portal. Control user list view, profile inspection, add/edit rights, 1-click active status, view sign-in credentials, and dispatching mail credentials.
                </p>

                <!-- Quick Role Chips Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4">
                  <div class="p-2.5 rounded-2xl glass-pill border border-purple-500/30 flex flex-col">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-600 dark:text-purple-400">👑 Super Admin</span>
                    <strong class="text-xs text-[var(--text-primary)] mt-0.5">Developer Role</strong>
                    <span class="text-[10px] text-[var(--text-muted)] mt-0.5">Full Master Access (*)</span>
                  </div>

                  <div class="p-2.5 rounded-2xl glass-pill border border-blue-500/30 flex flex-col">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">🏛️ HOD</span>
                    <strong class="text-xs text-[var(--text-primary)] mt-0.5">Dept Head</strong>
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold mt-0.5">Strictly No Delete</span>
                  </div>

                  <div class="p-2.5 rounded-2xl glass-pill border border-amber-500/30 flex flex-col">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">🛡️ Sub Admin</span>
                    <strong class="text-xs text-[var(--text-primary)] mt-0.5">Observer</strong>
                    <span class="text-[10px] text-[var(--text-muted)] mt-0.5">Universal View-Only</span>
                  </div>

                  <div class="p-2.5 rounded-2xl glass-pill border border-emerald-500/30 flex flex-col">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">👨‍🏫 Faculty</span>
                    <strong class="text-xs text-[var(--text-primary)] mt-0.5">Teaching Staff</strong>
                    <span class="text-[10px] text-[var(--text-muted)] mt-0.5">6 Academic Pages</span>
                  </div>
                </div>
              </div>

              <!-- Bottom CTA Action Row -->
              <div class="mt-6 pt-4 border-t border-black/5 dark:border-white/10 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3 text-xs text-[var(--text-muted)]">
                  <span><strong><?php echo $modulesCount; ?></strong> Modules</span>
                  <span>•</span>
                  <span><strong><?php echo $totalPermissions; ?></strong> Granular Permissions</span>
                  <span>•</span>
                  <span class="text-emerald-600 dark:text-emerald-400 font-bold">100% Protected</span>
                </div>

                <button
                  type="button"
                  id="openRoleMatrixFromHeroBtn"
                  class="px-5 py-2.5 rounded-2xl bg-[var(--color-primary)] text-white text-xs font-bold hover:brightness-105 active:scale-95 transition flex items-center gap-2 shadow-lg cursor-pointer"
                >
                  <span>Configure Roles & Permissions</span>
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- ================= CARD 2: THEME COLOR STUDIO & APPEARANCE ================= -->
            <div class="glass-card p-5 rounded-3xl flex flex-col justify-between border border-black/5 dark:border-white/10 shadow-lg relative overflow-hidden">
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--color-primary)]">Personalization</span>
                  <svg class="w-4 h-4 text-[var(--color-primary)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/>
                  </svg>
                </div>

                <h3 class="text-base font-bold text-[var(--text-primary)]">Theme Studio</h3>
                <p class="text-xs text-[var(--text-muted)] mt-1">Live department accent color palette</p>

                <!-- Color Palette Swatches -->
                <div class="grid grid-cols-3 gap-2 mt-4">
                  <button type="button" data-apply-color="#10B981" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#10B981] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Emerald</span>
                  </button>
                  <button type="button" data-apply-color="#729C51" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#729C51] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Green</span>
                  </button>
                  <button type="button" data-apply-color="#0284C7" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#0284C7] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Ocean</span>
                  </button>
                  <button type="button" data-apply-color="#7C3AED" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#7C3AED] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Violet</span>
                  </button>
                  <button type="button" data-apply-color="#E11D48" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#E11D48] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Rose</span>
                  </button>
                  <button type="button" data-apply-color="#D97706" class="color-select-btn p-2 rounded-xl glass-pill hover:scale-105 transition flex items-center gap-2 text-[11px] font-bold">
                    <span class="w-4 h-4 rounded-full bg-[#D97706] shrink-0 shadow-sm flex items-center justify-center"></span>
                    <span>Amber</span>
                  </button>
                </div>

                <!-- Custom Hex Color Picker Input -->
                <div class="mt-4 p-2.5 rounded-2xl glass-pill flex items-center justify-between border border-black/5 dark:border-white/10">
                  <span class="text-xs font-bold text-[var(--text-primary)]">Custom Palette</span>
                  <input
                    id="themeCustomPicker"
                    type="color"
                    value="#10B981"
                    class="w-7 h-7 rounded-lg cursor-pointer border-0 bg-transparent p-0"
                    title="Select Custom Brand Color"
                  />
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-black/5 dark:border-white/10 flex items-center justify-between text-xs text-[var(--text-muted)]">
                <span>Font: <strong>PT Serif</strong></span>
                <span class="text-[var(--color-primary)] font-bold">Live CSS Sync</span>
              </div>
            </div>

          </div>

          <!-- ================= SECOND ROW: SECURITY POLICY & RECENT ROLES AUDIT ================= -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">

            <!-- Security & Session Card -->
            <div class="glass-card p-5 rounded-3xl border border-black/5 dark:border-white/10 shadow-lg flex flex-col justify-between">
              <div>
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Security Guard</span>
                <h3 class="text-base font-bold text-[var(--text-primary)] mt-1">Session & Authentication</h3>
                <p class="text-xs text-[var(--text-muted)] mt-1">Autonomous session lifetime and OTP enforcement</p>

                <div class="space-y-2 mt-4 text-xs">
                  <div class="flex items-center justify-between p-2.5 rounded-xl glass-pill">
                    <span>Session Lifetime</span>
                    <strong class="text-[var(--color-primary)]">24 Hours (86,400s)</strong>
                  </div>
                  <div class="flex items-center justify-between p-2.5 rounded-xl glass-pill">
                    <span>Account Deactivation Guard</span>
                    <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Active</strong>
                  </div>
                  <div class="flex items-center justify-between p-2.5 rounded-xl glass-pill">
                    <span>Controller Guard</span>
                    <strong class="text-emerald-600 dark:text-emerald-400 font-bold">Enforced (403 Block)</strong>
                  </div>
                </div>
              </div>

              <div class="mt-4 text-[11px] text-[var(--text-muted)]">
                Protected against unauthorized direct URL route access.
              </div>
            </div>

            <!-- Role Distribution Summary Card -->
            <div class="md:col-span-2 glass-card p-5 rounded-3xl border border-black/5 dark:border-white/10 shadow-lg flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between">
                  <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-[var(--color-primary)]">Access Governance</span>
                    <h3 class="text-base font-bold text-[var(--text-primary)] mt-0.5">Department Role Distribution</h3>
                  </div>
                  <button type="button" id="jumpToStaffRolesBtn" class="text-xs font-bold text-[var(--color-primary)] hover:underline cursor-pointer">
                    Manage Staff Bindings →
                  </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-center">
                  <div class="p-3 rounded-2xl glass-pill border border-purple-500/20">
                    <span class="block text-2xl font-black text-purple-600 dark:text-purple-400"><?php echo $roleUserCounts['super_admin']; ?></span>
                    <span class="text-[11px] font-bold text-[var(--text-primary)]">Super Admin</span>
                    <span class="block text-[10px] text-[var(--text-muted)]">Dev Master</span>
                  </div>

                  <div class="p-3 rounded-2xl glass-pill border border-blue-500/20">
                    <span class="block text-2xl font-black text-blue-600 dark:text-blue-400"><?php echo $roleUserCounts['hod']; ?></span>
                    <span class="text-[11px] font-bold text-[var(--text-primary)]">HOD</span>
                    <span class="block text-[10px] text-amber-500 font-bold">No-Delete</span>
                  </div>

                  <div class="p-3 rounded-2xl glass-pill border border-amber-500/20">
                    <span class="block text-2xl font-black text-amber-600 dark:text-amber-400"><?php echo $roleUserCounts['sub_admin']; ?></span>
                    <span class="text-[11px] font-bold text-[var(--text-primary)]">Sub Admin</span>
                    <span class="block text-[10px] text-[var(--text-muted)]">View-Only</span>
                  </div>

                  <div class="p-3 rounded-2xl glass-pill border border-emerald-500/20">
                    <span class="block text-2xl font-black text-emerald-600 dark:text-emerald-400"><?php echo $roleUserCounts['faculty']; ?></span>
                    <span class="text-[11px] font-bold text-[var(--text-primary)]">Faculty Staff</span>
                    <span class="block text-[10px] text-[var(--text-muted)]">Academic Access</span>
                  </div>
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-black/5 dark:border-white/10 flex items-center justify-between text-xs text-[var(--text-muted)]">
                <span>Total Department Accounts: <strong><?php echo count($users); ?> Members</strong></span>
                <span>All roles synchronized with <code>users.json</code></span>
              </div>
            </div>

          </div>

        </div>


        <!-- ================= TAB 2: INTERACTIVE ROLE & PERMISSION MATRIX ================= -->
        <div id="sectionRoles" class="tab-section hidden flex-1 min-h-0 flex flex-col justify-between overflow-hidden py-2">
          
          <!-- Top Role Selector Tabs & Action Toolbar -->
          <div class="shrink-0 space-y-2.5 pb-2.5 border-b border-black/5 dark:border-white/10">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2.5">
              
              <!-- Role Tabs Capsule: Continuous horizontal scrollable pill bar -->
              <div class="flex items-center gap-1.5 min-w-0 max-w-full overflow-x-auto no-scrollbar py-0.5">
                <div id="roleSelectorPills" class="inline-flex items-center p-1 rounded-2xl glass-pill border border-white/80 dark:border-white/10 shrink-0 gap-1 font-sans">
                  <!-- Dynamically rendered by settings.js with concise labels & compact badges -->
                  <button
                    type="button"
                    data-role="super_admin"
                    class="role-pill-btn active px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer bg-[var(--color-primary)] text-white shadow-sm"
                  >
                    <span>👑 Super Admin</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-white/20">Master</span>
                  </button>

                  <button
                    type="button"
                    data-role="hod"
                    class="role-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
                  >
                    <span>🏛️ HOD</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-amber-500/20 text-amber-600 dark:text-amber-400 font-bold">No Delete</span>
                  </button>

                  <button
                    type="button"
                    data-role="sub_admin"
                    class="role-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
                  >
                    <span>🛡️ Sub Admin</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-black/10 dark:bg-white/10">View Only</span>
                  </button>

                  <button
                    type="button"
                    data-role="faculty"
                    class="role-pill-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer text-[var(--text-muted)] hover:text-[var(--text-primary)]"
                  >
                    <span>👨‍🏫 Faculty</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] bg-black/10 dark:bg-white/10">Academic</span>
                  </button>
                </div>

                <?php if (!empty($canCreateRole)): ?>
                <button
                  type="button"
                  id="openCreateRoleModalBtn"
                  class="h-8 px-3 rounded-xl bg-gradient-to-r from-emerald-500/15 to-teal-500/15 hover:from-emerald-500/25 hover:to-teal-500/25 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-sm shrink-0 whitespace-nowrap active:scale-95 font-sans"
                  title="Create a new custom security role"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                  </svg>
                  <span>Add Role</span>
                </button>
                <?php endif; ?>
              </div>

              <!-- Action Toolbar: Search + Reset + Save Buttons (Strictly single line, perfectly aligned) -->
              <div class="flex items-center gap-2 self-end lg:self-auto shrink-0 flex-nowrap font-sans">
                <!-- Search Filter Input -->
                <div class="relative w-40 sm:w-48">
                  <input
                    type="text"
                    id="permSearchInput"
                    placeholder="Search permissions..."
                    class="w-full pl-8 pr-3 py-1.5 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-xs text-[var(--text-primary)] placeholder-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition"
                  />
                  <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-[var(--text-muted)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                  </svg>
                </div>

                <!-- Reset to Defaults Button -->
                <?php if (!empty($canResetDefaults)): ?>
                <button
                  type="button"
                  id="resetRoleDefaultsBtn"
                  class="h-8 px-3 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition flex items-center gap-1.5 cursor-pointer shrink-0"
                  title="Reset role back to recommended system defaults"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                  </svg>
                  <span class="hidden sm:inline">Reset</span>
                </button>
                <?php endif; ?>

                <!-- Save Changes Button -->
                <?php if (!empty($canEditRoles)): ?>
                <button
                  type="button"
                  id="saveRolePermissionsBtn"
                  class="h-8 px-3.5 rounded-xl bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-dark,var(--color-primary))] hover:brightness-110 active:scale-95 text-white text-xs font-bold shadow-md shadow-[var(--color-primary)]/20 flex items-center gap-1.5 transition cursor-pointer shrink-0 whitespace-nowrap"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                  </svg>
                  <span>Save Permissions</span>
                </button>
                <?php endif; ?>
              </div>

            </div>

            <!-- Role Context Banner / Notice -->
            <div id="roleContextBanner" class="p-3 rounded-2xl glass-pill border border-[var(--color-primary)]/30 flex items-center justify-between text-xs flex-wrap gap-2">
              <div class="flex items-center gap-2">
                <span id="roleBannerIcon" class="text-base">👑</span>
                <div>
                  <strong id="roleBannerTitle" class="text-[var(--text-primary)] font-bold">Super Admin (Developer)</strong>
                  <p id="roleBannerDesc" class="text-[var(--text-muted)] text-[11px]">Unrestricted master developer role with complete access across every module, action, and setting.</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <?php if (!empty($canDeleteRole)): ?>
                <button
                  type="button"
                  id="deleteCurrentRoleBtn"
                  class="hidden h-7 px-2.5 rounded-lg bg-rose-500/15 hover:bg-rose-500/25 text-rose-600 dark:text-rose-400 text-[11px] font-bold border border-rose-500/30 transition items-center gap-1 cursor-pointer"
                  title="Delete this custom security role"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                  </svg>
                  <span>Delete Role</span>
                </button>
                <?php endif; ?>
                <span id="roleActiveCountBadge" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[var(--color-primary)]/15 text-[var(--color-primary)] whitespace-nowrap">
                  All 42 Permissions Active
                </span>
              </div>
            </div>

          </div>

          <!-- Middle: Scrollable Module Accordions & Granular Permission Switches -->
          <div id="modulePermissionsContainer" class="flex-1 min-h-0 overflow-y-auto no-scrollbar py-3 space-y-3.5">
            <!-- Dynamic module cards injected by assets/js/settings.js -->
            <div class="p-8 text-center text-[var(--text-muted)] text-xs">
              <span class="inline-block w-5 h-5 border-2 border-[var(--color-primary)] border-t-transparent rounded-full animate-spin mb-2"></span>
              <p>Loading permissions matrix...</p>
            </div>
          </div>

          <!-- Bottom: Floating Quick Summary Bar -->
          <div class="shrink-0 pt-2.5 border-t border-black/5 dark:border-white/10 flex items-center justify-between text-xs text-[var(--text-muted)]">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>Changes take effect immediately across all sessions</span>
            </div>
            <div class="flex items-center gap-2">
              <span id="permSelectedRatio">0 of 42 enabled</span>
            </div>
          </div>

        </div>


        <!-- ================= TAB 3: STAFF ROLE BINDINGS & USER ASSIGNMENT ================= -->
        <div id="sectionUsers" class="tab-section hidden flex-1 min-h-0 flex flex-col justify-between overflow-hidden py-2">
          
          <!-- Top Filter Bar -->
          <div class="shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-black/5 dark:border-white/10">
            <div>
              <h3 class="text-sm font-bold text-[var(--text-primary)]">Staff Security Role Assignment</h3>
              <p class="text-[11px] text-[var(--text-muted)]">Assign any faculty member to Super Admin, HOD, Sub Admin, or Faculty</p>
            </div>

            <!-- Search Filter -->
            <div class="relative w-full sm:w-64">
              <input
                type="text"
                id="staffRoleSearchInput"
                placeholder="Search staff by name, empId, dept..."
                class="w-full pl-8 pr-3 py-1.5 rounded-xl glass-pill border border-white/80 dark:border-white/10 text-xs text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
              />
              <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-[var(--text-muted)] pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
              </svg>
            </div>
          </div>

          <!-- Scrollable Staff Users Table -->
          <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar py-2">
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse text-xs">
                <thead>
                  <tr class="border-b border-black/5 dark:border-white/10 text-[var(--text-muted)] text-[10.5px] font-extrabold uppercase tracking-wider">
                    <th class="py-2.5 px-3">Faculty / Staff Member</th>
                    <th class="py-2.5 px-3">Department</th>
                    <th class="py-2.5 px-3">Designation</th>
                    <th class="py-2.5 px-3">Assigned Security Role</th>
                    <th class="py-2.5 px-3 text-right">Instant Action</th>
                  </tr>
                </thead>
                <tbody id="staffRoleTableBody" class="divide-y divide-black/5 dark:divide-white/5">
                  <!-- Dynamically rendered by assets/js/settings.js -->
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bottom Summary Bar -->
          <div class="shrink-0 pt-2.5 border-t border-black/5 dark:border-white/10 flex items-center justify-between text-xs text-[var(--text-muted)]">
            <span>Total Staff Accounts: <strong id="staffRoleTableCount"><?php echo count($users); ?></strong></span>
            <span>Role changes update <code>config/users.json</code> and live session</span>
          </div>

        </div>

      </main>
    </div>
  </div>

  <!-- ================= MODAL: CREATE NEW SECURITY ROLE ================= -->
  <div id="createRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md hidden font-sans" style="display: none;">
    <div class="glass-card w-full max-w-lg p-6 sm:p-7 rounded-3xl border border-white/60 dark:border-white/10 shadow-2xl relative overflow-hidden animate-fadeIn bg-white/90 dark:bg-[#121218]/95 backdrop-blur-2xl">
      <!-- Ambient top accent gradient bar -->
      <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[var(--color-primary)] via-purple-500 to-teal-400"></div>

      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-3.5 border-b border-black/5 dark:border-white/10 mb-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[var(--color-primary)]/20 to-[var(--color-primary)]/40 text-[var(--color-primary)] flex items-center justify-center font-bold text-lg shrink-0 shadow-inner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-[var(--text-primary)] tracking-tight">Create Security Role</h3>
            <p class="text-xs text-[var(--text-muted)]">Configure a new department role with custom access boundaries</p>
          </div>
        </div>
        <button
          type="button"
          id="closeCreateRoleModalBtn"
          class="w-8 h-8 rounded-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-[var(--text-muted)] hover:text-[var(--text-primary)] flex items-center justify-center transition cursor-pointer"
          title="Close dialog"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form id="createRoleForm" class="space-y-4">
        <!-- Field 1: Display Name -->
        <div>
          <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
            Role Display Name <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            id="newRoleName"
            required
            maxlength="50"
            placeholder="e.g. Lab Assistant, Exam Coordinator, Visiting Faculty"
            class="w-full px-3.5 py-2.5 rounded-xl bg-white/70 dark:bg-white/[0.05] border border-black/10 dark:border-white/10 text-xs sm:text-[13px] text-[var(--text-primary)] placeholder-[var(--text-muted)]/60 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition"
          />
        </div>

        <!-- Field 2 & 3: Role Key & Color Swatches -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          <div>
            <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
              Role Key (Slug) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-mono text-[var(--text-muted)]">@</span>
              <input
                type="text"
                id="newRoleKey"
                required
                maxlength="32"
                placeholder="lab_assistant"
                class="w-full pl-7 pr-3 py-2.5 rounded-xl bg-white/70 dark:bg-white/[0.05] border border-black/10 dark:border-white/10 text-xs font-mono text-[var(--text-primary)] placeholder-[var(--text-muted)]/60 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition"
              />
            </div>
            <span class="text-[10px] text-[var(--text-muted)] mt-1 block">Lowercase alphanumeric & underscores</span>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
              Badge Accent Color
            </label>
            <!-- Interactive Color Swatches -->
            <div class="flex items-center gap-1.5 flex-wrap pt-1" id="roleColorSwatches">
              <button type="button" data-color="teal" data-hex="#14b8a6" class="color-swatch-btn active w-6 h-6 rounded-full bg-teal-500 ring-2 ring-offset-2 ring-teal-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Teal">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
              </button>
              <button type="button" data-color="emerald" data-hex="#10b981" class="color-swatch-btn w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Emerald"></button>
              <button type="button" data-color="blue" data-hex="#3b82f6" class="color-swatch-btn w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Blue"></button>
              <button type="button" data-color="purple" data-hex="#8b5cf6" class="color-swatch-btn w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Purple"></button>
              <button type="button" data-color="amber" data-hex="#f59e0b" class="color-swatch-btn w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Amber"></button>
              <button type="button" data-color="rose" data-hex="#f43f5e" class="color-swatch-btn w-6 h-6 rounded-full bg-rose-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Rose"></button>
              <button type="button" data-color="indigo" data-hex="#6366f1" class="color-swatch-btn w-6 h-6 rounded-full bg-indigo-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Indigo"></button>
              <button type="button" data-color="cyan" data-hex="#06b6d4" class="color-swatch-btn w-6 h-6 rounded-full bg-cyan-500 flex items-center justify-center transition hover:scale-110 cursor-pointer" title="Cyan"></button>
            </div>
            <input type="hidden" id="newRoleColor" value="teal" />
          </div>
        </div>

        <!-- Field 4: Description -->
        <div>
          <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
            Role Scope & Description
          </label>
          <textarea
            id="newRoleDesc"
            rows="2"
            maxlength="200"
            placeholder="Describe the department responsibilities and access scope of this role..."
            class="w-full px-3.5 py-2 rounded-xl bg-white/70 dark:bg-white/[0.05] border border-black/10 dark:border-white/10 text-xs text-[var(--text-primary)] placeholder-[var(--text-muted)]/60 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent transition"
          ></textarea>
        </div>

        <!-- Field 5: Starter Permissions Preset Tiles -->
        <div>
          <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
            Starter Permissions Preset
          </label>
          <div class="grid grid-cols-3 gap-2" id="presetCardsContainer">
            <button
              type="button"
              data-preset="faculty"
              class="preset-card active p-2.5 rounded-xl border-2 border-[var(--color-primary)] bg-[var(--color-primary)]/10 text-left transition flex flex-col justify-between cursor-pointer"
            >
              <span class="text-sm">👨‍🏫</span>
              <strong class="text-xs text-[var(--text-primary)] mt-1">Faculty Defaults</strong>
              <span class="text-[10px] text-[var(--text-muted)] mt-0.5">6 Academic Pages</span>
            </button>
            <button
              type="button"
              data-preset="sub_admin"
              class="preset-card p-2.5 rounded-xl border border-black/10 dark:border-white/10 hover:border-black/25 dark:hover:border-white/25 text-left transition flex flex-col justify-between cursor-pointer"
            >
              <span class="text-sm">🛡️</span>
              <strong class="text-xs text-[var(--text-primary)] mt-1">Sub Admin</strong>
              <span class="text-[10px] text-[var(--text-muted)] mt-0.5">Universal View</span>
            </button>
            <button
              type="button"
              data-preset=""
              class="preset-card p-2.5 rounded-xl border border-black/10 dark:border-white/10 hover:border-black/25 dark:hover:border-white/25 text-left transition flex flex-col justify-between cursor-pointer"
            >
              <span class="text-sm">⚙️</span>
              <strong class="text-xs text-[var(--text-primary)] mt-1">Start Blank</strong>
              <span class="text-[10px] text-[var(--text-muted)] mt-0.5">Configure Custom</span>
            </button>
          </div>
          <input type="hidden" id="newRoleCloneFrom" value="faculty" />
        </div>

        <!-- Live Pill Preview Box -->
        <div class="p-3 rounded-2xl bg-black/[0.03] dark:bg-white/[0.03] border border-black/5 dark:border-white/10 flex items-center justify-between">
          <div>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[var(--text-muted)] block">Live Pill Preview</span>
            <span class="text-[11px] text-[var(--text-muted)]">Real-time matrix appearance</span>
          </div>
          <div id="rolePreviewPill" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-teal-500 text-white shadow-sm">
            <span>⭐ <span id="previewRoleName">New Role</span></span>
            <span id="previewRoleBadge" class="px-1.5 py-0.2 rounded-full text-[9px] bg-white/20">Custom</span>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-3 border-t border-black/5 dark:border-white/10 flex items-center justify-end gap-2.5">
          <button
            type="button"
            id="cancelCreateRoleBtn"
            class="px-4 py-2 rounded-xl glass-pill text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="submit"
            id="submitCreateRoleBtn"
            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary-dark,var(--color-primary))] hover:brightness-110 active:scale-95 text-white text-xs font-bold shadow-md shadow-[var(--color-primary)]/25 transition flex items-center gap-1.5 cursor-pointer"
          >
            <span>Create Role</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ================= MODAL: DELETE CUSTOM ROLE ================= -->
  <div id="deleteRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md hidden font-sans" style="display: none;">
    <div class="glass-card w-full max-w-md p-6 sm:p-7 rounded-3xl border border-rose-500/30 shadow-2xl relative overflow-hidden animate-fadeIn bg-white/90 dark:bg-[#121218]/95 backdrop-blur-2xl">
      <!-- Ambient top accent gradient bar -->
      <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-rose-500 to-amber-500"></div>

      <div class="flex items-center gap-3 pb-3.5 border-b border-black/5 dark:border-white/10 mb-4">
        <div class="w-10 h-10 rounded-2xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg shrink-0 shadow-inner">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-base font-extrabold text-[var(--text-primary)] tracking-tight">Delete Security Role</h3>
          <p class="text-xs text-[var(--text-muted)]">Permanent removal from role configuration</p>
        </div>
      </div>

      <div class="space-y-4 text-xs text-[var(--text-primary)]">
        <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300">
          <p class="leading-relaxed">
            Are you sure you want to permanently delete role <strong id="deleteRoleTargetName" class="font-black text-rose-600 dark:text-rose-400"></strong>?
          </p>
        </div>

        <p class="text-[11px] text-[var(--text-muted)] leading-relaxed">
          All custom permission records for this role will be wiped. Staff members assigned to this role will be safely migrated to the fallback role below.
        </p>

        <div>
          <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">
            Migrate Assigned Staff Members To:
          </label>
          <select
            id="deleteRoleReassignSelect"
            class="w-full px-3.5 py-2.5 rounded-xl bg-white/70 dark:bg-white/[0.05] border border-black/10 dark:border-white/10 text-xs font-bold text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] transition cursor-pointer"
          >
            <option value="faculty" selected>Faculty Member (Standard Academic Staff)</option>
            <option value="sub_admin">Sub Admin (Observer)</option>
          </select>
        </div>
      </div>

      <div class="mt-6 pt-3.5 border-t border-black/5 dark:border-white/10 flex items-center justify-end gap-2.5">
        <button
          type="button"
          id="cancelDeleteRoleBtn"
          class="px-4 py-2 rounded-xl glass-pill text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text-primary)] hover:bg-black/5 dark:hover:bg-white/10 transition cursor-pointer"
        >
          Cancel
        </button>
        <button
          type="button"
          id="confirmDeleteRoleBtn"
          class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:brightness-110 active:scale-95 text-white text-xs font-bold shadow-md shadow-rose-500/30 transition flex items-center gap-1.5 cursor-pointer"
        >
          <span>Confirm & Delete</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Shared Toast Notification Container -->
  <?php include dirname(__DIR__) . '/partials/toast.php'; ?>

  <!-- Core JavaScript & Settings Data Handler -->
  <script>
    window.portalBaseUrl = "<?php echo htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>";
    window.initialActiveTab = "<?php echo htmlspecialchars($activeTab); ?>";
    window.rolesConfig = <?php echo json_encode($rolesConfig); ?>;
    window.departmentUsers = <?php echo json_encode($users); ?>;
    window.currentUserRole = "<?php echo \Core\Auth::userRole(); ?>";
    window.canCreateRole = <?php echo !empty($canCreateRole) ? 'true' : 'false'; ?>;
    window.canDeleteRole = <?php echo !empty($canDeleteRole) ? 'true' : 'false'; ?>;
    window.canEditRoles = <?php echo !empty($canEditRoles) ? 'true' : 'false'; ?>;
    window.canAssignRoles = <?php echo !empty($canAssignRoles) ? 'true' : 'false'; ?>;
    window.canResetDefaults = <?php echo !empty($canResetDefaults) ? 'true' : 'false'; ?>;
  </script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/sidebar.js') : 'assets/js/sidebar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/navbar.js') : 'assets/js/navbar.js'; ?>"></script>
  <script src="<?php echo function_exists('v_asset') ? v_asset('assets/js/settings.js') : 'assets/js/settings.js'; ?>"></script>
</body>
</html>

