<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class SettingController extends Controller
{
    private function getRolesFilePath(): string
    {
        return defined('CONFIG_PATH')
            ? CONFIG_PATH . '/roles.json'
            : (defined('BASE_PATH') ? BASE_PATH . '/config/roles.json' : dirname(__DIR__, 2) . '/config/roles.json');
    }

    private function getUsersFilePath(): string
    {
        return defined('CONFIG_PATH')
            ? CONFIG_PATH . '/users.json'
            : (defined('BASE_PATH') ? BASE_PATH . '/config/users.json' : dirname(__DIR__, 2) . '/config/users.json');
    }

    private function getFacultyDataJsPath(): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        return $base . '/assets/js/facultyData.js';
    }

    /**
     * Load roles configuration from config/roles.json
     */
    private function loadRolesConfig(): array
    {
        $file = $this->getRolesFilePath();
        if (!file_exists($file)) {
            return Auth::getRolesConfig();
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : Auth::getRolesConfig();
    }

    /**
     * Save roles configuration to config/roles.json
     */
    private function saveRolesConfig(array $config): bool
    {
        $file = $this->getRolesFilePath();
        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return (file_put_contents($file, $json, LOCK_EX) !== false);
    }

    /**
     * Load all department users from config/users.json
     */
    private function loadUsers(): array
    {
        $file = $this->getUsersFilePath();
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Save users to config/users.json and sync assets/js/facultyData.js
     */
    private function saveUsers(array $users): bool
    {
        $file = $this->getUsersFilePath();
        $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $ok = (file_put_contents($file, $json, LOCK_EX) !== false);

        // Synchronize client-side facultyData.js
        $jsFile = $this->getFacultyDataJsPath();
        if (file_exists(dirname($jsFile))) {
            $jsContent = "// Shared Faculty Members Data Array for CE & IT Department\n";
            $jsContent .= "const facultyData = " . $json . ";\n";
            @file_put_contents($jsFile, $jsContent, LOCK_EX);
        }

        return $ok;
    }

    /**
     * Render the Settings Hub page with Bento Cards
     * (Role & Permission Management Hero Card, Theme Studio, and Security Guard)
     */
    public function index(): void
    {
        Auth::requirePermission('settings.view', 'Settings Hub');

        $rolesConfig = $this->loadRolesConfig();
        $users = $this->loadUsers();

        // Calculate role metrics
        $rolesCount = count($rolesConfig['roles'] ?? []);
        $modulesCount = count($rolesConfig['modules'] ?? []);
        $totalPermissions = count(Auth::allPermissionIds());

        // Count users per role
        $roleUserCounts = [
            'super_admin' => 0,
            'hod'         => 0,
            'sub_admin'   => 0,
            'faculty'     => 0
        ];

        foreach ($users as $u) {
            $r = Auth::normalizeRole($u['role'] ?? 'faculty');
            if (isset($roleUserCounts[$r])) {
                $roleUserCounts[$r]++;
            } else {
                $roleUserCounts['faculty']++;
            }
        }

        $activeTab = $_GET['tab'] ?? 'overview';

        $this->view('settings.index', [
            'currentUser'       => Auth::user(),
            'rolesConfig'       => $rolesConfig,
            'users'             => $users,
            'rolesCount'        => $rolesCount,
            'modulesCount'      => $modulesCount,
            'totalPermissions'  => $totalPermissions,
            'roleUserCounts'    => $roleUserCounts,
            'activeTab'         => $activeTab,
            'baseUrl'           => function_exists('base_url') ? base_url('') : '/',
            'canManageRoles'    => Auth::can('settings.roles'),
            'canEditRoles'      => Auth::can('settings.edit_roles'),
            'canCreateRole'     => Auth::can('settings.create_role'),
            'canDeleteRole'     => (Auth::can('settings.delete_role') && !Auth::isHod()),
            'canAssignRoles'    => Auth::can('settings.assign_user'),
            'canResetDefaults'  => Auth::can('settings.reset_defaults')
        ]);
    }

    /**
     * Direct shortcut to Role & Permission Management
     */
    public function roles(): void
    {
        Auth::requirePermission('settings.roles', 'Role & Permission Management');
        $_GET['tab'] = 'roles';
        $this->index();
    }

    /**
     * API: Get all roles, granular permissions schema, and department users
     * Endpoint: GET /api/settings/roles
     */
    public function apiGetRoles(): void
    {
        Auth::requirePermission('settings.roles', 'Role & Permission API');

        $rolesConfig = $this->loadRolesConfig();
        $users = $this->loadUsers();

        // Sanitize users array (omit raw passwords from API listing)
        $safeUsers = array_map(function($u) {
            $copy = $u;
            unset($copy['password']);
            $copy['normalized_role'] = Auth::normalizeRole($copy['role'] ?? 'faculty');
            return $copy;
        }, $users);

        $this->json([
            'success'     => true,
            'modules'     => $rolesConfig['modules'] ?? [],
            'roles'       => $rolesConfig['roles'] ?? [],
            'users'       => $safeUsers,
            'currentUser' => Auth::user(),
            'userPermissions' => Auth::userPermissions()
        ]);
    }

    /**
     * API: Save updated permissions for a specific role
     * Endpoint: POST /api/settings/roles/save
     */
    public function apiSaveRolePermissions(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('settings.edit_roles', 'Save Role Permissions');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $roleKey = Auth::normalizeRole($input['roleKey'] ?? '');
        if (empty($roleKey)) {
            $this->json(['success' => false, 'error' => 'A valid role identifier is required.'], 400);
            return;
        }

        $rolesConfig = $this->loadRolesConfig();

        if (!isset($rolesConfig['roles'][$roleKey])) {
            $this->json(['success' => false, 'error' => "Role '{$roleKey}' does not exist."], 404);
            return;
        }

        // 1. Super Admin is the master developer role: cannot be restricted
        if ($roleKey === 'super_admin') {
            $rolesConfig['roles']['super_admin']['permissions'] = ['*'];
            $this->saveRolesConfig($rolesConfig);
            $this->json([
                'success' => true,
                'message' => 'Super Admin (Developer) role retains unrestricted full master access (*).',
                'role'    => $rolesConfig['roles']['super_admin']
            ]);
            return;
        }

        $incomingPerms = $input['permissions'] ?? [];
        if (!is_array($incomingPerms)) {
            $incomingPerms = [];
        }

        // Valid permission filter: only accept defined permission IDs
        $allValidPerms = Auth::allPermissionIds();
        $filteredPerms = array_values(array_intersect($incomingPerms, $allValidPerms));

        // 2. Strict HOD Rule: HOD CANNOT DELETE ANYTHING across the website
        if ($roleKey === 'hod') {
            $deletePermissions = [];
            foreach ($rolesConfig['modules'] ?? [] as $m) {
                foreach ($m['permissions'] ?? [] as $p) {
                    if (($p['type'] ?? '') === 'delete') {
                        $deletePermissions[] = $p['id'];
                    }
                }
            }
            // Strip out any delete permissions
            $filteredPerms = array_values(array_diff($filteredPerms, $deletePermissions));
        }

        // 3. Strict Sub Admin Rule: Sub Admin is strictly View-Only
        if ($roleKey === 'sub_admin') {
            $disallowedTypes = ['create', 'edit', 'delete', 'toggle', 'send', 'upload', 'sync'];
            $disallowedPerms = [];
            foreach ($rolesConfig['modules'] ?? [] as $m) {
                foreach ($m['permissions'] ?? [] as $p) {
                    if (in_array($p['type'] ?? '', $disallowedTypes, true)) {
                        $disallowedPerms[] = $p['id'];
                    }
                }
            }
            $filteredPerms = array_values(array_diff($filteredPerms, $disallowedPerms));
        }

        // Update permissions array
        $rolesConfig['roles'][$roleKey]['permissions'] = $filteredPerms;
        $this->saveRolesConfig($rolesConfig);

        $roleName = $rolesConfig['roles'][$roleKey]['name'] ?? ucfirst($roleKey);

        $this->json([
            'success'     => true,
            'message'     => "Granular permissions for {$roleName} updated successfully (" . count($filteredPerms) . " active).",
            'roleKey'     => $roleKey,
            'permissions' => $filteredPerms
        ]);
    }

    /**
     * API: Reassign a user's security role
     * Endpoint: POST /api/settings/roles/assign-user
     */
    public function apiAssignUserRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('settings.assign_user', 'Assign User Role');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $userId = trim($input['userId'] ?? '');
        $newRole = Auth::normalizeRole($input['newRole'] ?? '');

        if (empty($userId) || empty($newRole)) {
            $this->json(['success' => false, 'error' => 'User ID and valid role are required.'], 400);
            return;
        }

        $rolesConfig = $this->loadRolesConfig();
        if (!isset($rolesConfig['roles'][$newRole])) {
            $this->json(['success' => false, 'error' => "Role '{$newRole}' is not recognized."], 400);
            return;
        }

        // Protect primary administrative system accounts from losing super_admin
        $primaryAdmins = ['adminit', 'admince'];
        if (in_array(strtolower($userId), $primaryAdmins, true) && $newRole !== 'super_admin') {
            $this->json(['success' => false, 'error' => "Cannot demote primary Department Admin account '{$userId}'."], 403);
            return;
        }

        $users = $this->loadUsers();
        $found = false;
        $userName = $userId;

        foreach ($users as &$u) {
            if (strcasecmp($u['id'] ?? '', $userId) === 0) {
                $u['role'] = $newRole;
                $userName = $u['name'] ?? $userId;
                $found = true;
                break;
            }
        }
        unset($u);

        if (!$found) {
            $this->json(['success' => false, 'error' => "User account '{$userId}' not found."], 404);
            return;
        }

        $this->saveUsers($users);

        // If the current active user updated their own role, synchronize their session
        if (strcasecmp(Auth::id() ?? '', $userId) === 0) {
            if (isset($_SESSION['user'])) {
                $_SESSION['user']['role'] = $newRole;
            }
        }

        $roleName = $rolesConfig['roles'][$newRole]['name'] ?? ucfirst($newRole);

        $this->json([
            'success' => true,
            'message' => "{$userName} is now assigned to the '{$roleName}' role.",
            'userId'  => $userId,
            'newRole' => $newRole
        ]);
    }

    /**
     * API: Restore all role permission presets to system defaults
     * Endpoint: POST /api/settings/roles/reset-defaults
     */
    public function apiResetDefaults(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('settings.reset_defaults', 'Reset System Defaults');

        $rolesConfig = $this->loadRolesConfig();

        // 1. Super Admin: full access
        $rolesConfig['roles']['super_admin']['permissions'] = ['*'];

        // 2. HOD: all permissions except delete
        $allPerms = Auth::allPermissionIds();
        $deletePerms = [];
        foreach ($rolesConfig['modules'] ?? [] as $m) {
            foreach ($m['permissions'] ?? [] as $p) {
                if (($p['type'] ?? '') === 'delete') {
                    $deletePerms[] = $p['id'];
                }
            }
        }
        $rolesConfig['roles']['hod']['permissions'] = array_values(array_diff($allPerms, $deletePerms));

        // 3. Sub Admin: universal view-only
        $viewPerms = [];
        foreach ($rolesConfig['modules'] ?? [] as $m) {
            foreach ($m['permissions'] ?? [] as $p) {
                if (in_array($p['type'] ?? '', ['view', 'export'], true)) {
                    $viewPerms[] = $p['id'];
                }
            }
        }
        $rolesConfig['roles']['sub_admin']['permissions'] = array_values($viewPerms);

        // 4. Faculty: default academic pages
        $rolesConfig['roles']['faculty']['permissions'] = [
            'dashboard.view',
            'dashboard.cameras',
            'calendar.view',
            'calendar.print',
            'calendar.copy',
            'timetable.view',
            'student_timetable.view',
            'student_timetable.print',
            'report.view',
            'report.create',
            'report.edit',
            'report.delete',
            'report.submit',
            'report.draft',
            'report.preview',
            'report.print',
            'report.reset',
            'syllabus.view',
            'syllabus.export_all',
            'syllabus.export_subject',
            'syllabus.export_exam'
        ];

        $this->saveRolesConfig($rolesConfig);

        $this->json([
            'success' => true,
            'message' => 'All roles have been restored to recommended system default permissions.'
        ]);
    }

    /**
     * API: Create a new custom security role
     * Endpoint: POST /api/settings/roles/create
     */
    public function apiCreateRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('settings.create_role', 'Create New Role');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        if (empty($name)) {
            $this->json(['success' => false, 'error' => 'Role name is required.'], 400);
            return;
        }

        if (strlen($name) > 50) {
            $this->json(['success' => false, 'error' => 'Role name must be 50 characters or fewer.'], 400);
            return;
        }

        // Generate or sanitize role key
        $keyInput = trim($input['key'] ?? '');
        if (empty($keyInput)) {
            $key = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($name)));
            $key = trim($key, '_');
        } else {
            $key = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $keyInput));
        }

        if (empty($key) || strlen($key) < 2 || strlen($key) > 32) {
            $this->json(['success' => false, 'error' => 'Role key must be 2-32 alphanumeric characters.'], 400);
            return;
        }

        $rolesConfig = $this->loadRolesConfig();

        if (isset($rolesConfig['roles'][$key])) {
            $this->json(['success' => false, 'error' => "A role with key '{$key}' already exists."], 409);
            return;
        }

        $badge = trim($input['badge'] ?? '') ?: $name;
        $badgeColor = trim($input['badge_color'] ?? 'teal');
        $validColors = ['purple', 'blue', 'amber', 'emerald', 'teal', 'rose', 'cyan', 'indigo'];
        if (!in_array($badgeColor, $validColors, true)) {
            $badgeColor = 'teal';
        }

        $description = trim($input['description'] ?? '');
        if (empty($description)) {
            $description = "Custom security role for {$name}.";
        }

        // Clone permissions from a base preset if requested
        $cloneFrom = trim($input['clone_from'] ?? '');
        $initialPermissions = [];
        if (!empty($cloneFrom) && isset($rolesConfig['roles'][$cloneFrom]['permissions'])) {
            $initialPermissions = $rolesConfig['roles'][$cloneFrom]['permissions'];
            // If cloned from super_admin (*), expand or convert to all permissions
            if (in_array('*', $initialPermissions, true)) {
                $initialPermissions = Auth::allPermissionIds();
            }
        } elseif (isset($input['permissions']) && is_array($input['permissions'])) {
            $allValid = Auth::allPermissionIds();
            $initialPermissions = array_values(array_intersect($input['permissions'], $allValid));
        }

        $newRole = [
            'key'          => $key,
            'name'         => $name,
            'badge'        => $badge,
            'badge_color'  => $badgeColor,
            'description'  => $description,
            'is_system'    => false,
            'allow_delete' => true,
            'permissions'  => $initialPermissions
        ];

        $rolesConfig['roles'][$key] = $newRole;
        $this->saveRolesConfig($rolesConfig);

        $this->json([
            'success' => true,
            'message' => "Role '{$name}' created successfully.",
            'roleKey' => $key,
            'role'    => $newRole
        ], 201);
    }

    /**
     * API: Delete a custom security role
     * Endpoint: POST /api/settings/roles/delete
     */
    public function apiDeleteRole(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('settings.delete_role', 'Delete Custom Role');

        // Extra server-side check: HOD CANNOT DELETE ANYTHING
        if (Auth::isHod()) {
            $this->json([
                'success' => false,
                'error'   => 'Access Denied: Head of Department (HOD) is strictly not permitted to delete roles.'
            ], 403);
            return;
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $roleKey = strtolower(trim($input['roleKey'] ?? ''));
        if (empty($roleKey)) {
            $this->json(['success' => false, 'error' => 'Role key is required.'], 400);
            return;
        }

        $rolesConfig = $this->loadRolesConfig();

        if (!isset($rolesConfig['roles'][$roleKey])) {
            $this->json(['success' => false, 'error' => "Role '{$roleKey}' does not exist."], 404);
            return;
        }

        // System-protected roles cannot be deleted
        $systemRoles = ['super_admin', 'hod', 'sub_admin', 'faculty'];
        if (in_array($roleKey, $systemRoles, true) || !empty($rolesConfig['roles'][$roleKey]['is_system'])) {
            $this->json([
                'success' => false,
                'error'   => "System core role '{$roleKey}' is permanently protected and cannot be deleted."
            ], 400);
            return;
        }

        $roleName = $rolesConfig['roles'][$roleKey]['name'] ?? $roleKey;

        // Reassign any users currently assigned to this role
        $reassignTo = Auth::normalizeRole($input['reassignTo'] ?? 'faculty');
        if (!isset($rolesConfig['roles'][$reassignTo])) {
            $reassignTo = 'faculty';
        }

        $users = $this->loadUsers();
        $reassignedCount = 0;
        foreach ($users as &$u) {
            if (Auth::normalizeRole($u['role'] ?? '') === $roleKey) {
                $u['role'] = $reassignTo;
                $reassignedCount++;
            }
        }
        unset($u);

        if ($reassignedCount > 0) {
            $this->saveUsers($users);
        }

        // Delete from roles config
        unset($rolesConfig['roles'][$roleKey]);
        $this->saveRolesConfig($rolesConfig);

        $msg = "Role '{$roleName}' deleted successfully.";
        if ($reassignedCount > 0) {
            $msg .= " {$reassignedCount} assigned staff member(s) were reassigned to " . ($rolesConfig['roles'][$reassignTo]['name'] ?? $reassignTo) . ".";
        }

        $this->json([
            'success'         => true,
            'message'         => $msg,
            'roleKey'         => $roleKey,
            'reassignedCount' => $reassignedCount
        ]);
    }
}

