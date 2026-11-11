<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class FacultyController extends Controller
{
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
     * Load all faculty users from config/users.json
     */
    private function loadFacultyUsers(): array
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
     * Save faculty users to config/users.json and sync assets/js/facultyData.js
     */
    private function saveFacultyUsers(array $users): bool
    {
        $file = $this->getUsersFilePath();
        $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $ok = (file_put_contents($file, $json, LOCK_EX) !== false);

        // Also synchronize assets/js/facultyData.js for client-side compatibility
        $jsFile = $this->getFacultyDataJsPath();
        if (file_exists(dirname($jsFile))) {
            $jsContent = "// Shared Faculty Members Data Array for CE & IT Department\n";
            $jsContent .= "const facultyData = " . $json . ";\n";
            @file_put_contents($jsFile, $jsContent, LOCK_EX);
        }

        return $ok;
    }

    /**
     * Render the Faculty Directory page
     */
    public function index(): void
    {
        Auth::requirePermission('faculty.list', 'Faculty Directory');

        $facultyList = $this->loadFacultyUsers();

        $this->view('faculty.index', [
            'facultyList'     => $facultyList,
            'currentUser'     => Auth::user(),
            'isSuperAdmin'    => Auth::isSuperAdmin(),
            'userPermissions' => Auth::userPermissions()
        ]);
    }

    /**
     * API: Get all faculty records
     * Endpoint: GET /api/faculty
     */
    public function apiList(): void
    {
        Auth::requirePermission('faculty.list', 'Faculty Directory API');
        $faculty = $this->loadFacultyUsers();
        $this->json([
            'success' => true,
            'count'   => count($faculty),
            'data'    => $faculty
        ]);
    }

    /**
     * API: Create or update faculty member
     * Endpoint: POST /api/faculty/save
     */
    public function apiSave(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        if (empty($name)) {
            $this->json(['success' => false, 'error' => 'Faculty full name is required.'], 400);
            return;
        }

        $id = trim($input['id'] ?? '');
        $isNew = empty($id);
        if ($isNew) {
            $id = 'fac_' . time() . '_' . substr(md5(uniqid()), 0, 4);
        }

        $users = $this->loadFacultyUsers();
        $existingIndex = -1;
        foreach ($users as $idx => $u) {
            if (strcasecmp($u['id'] ?? '', $id) === 0) {
                $existingIndex = $idx;
                break;
            }
        }

        // Granular permission check: create vs edit
        if ($existingIndex !== -1) {
            Auth::requirePermission('faculty.edit', 'Edit Faculty Profile');
        } else {
            Auth::requirePermission('faculty.create', 'Create Faculty Member');
        }

        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $cleanPhone = preg_replace('/\D+/', '', $phone);

        // Derive username
        $username = trim($input['username'] ?? '');
        if (empty($username)) {
            if (!empty($email) && strpos($email, '@') !== false) {
                $username = strtolower(explode('@', $email)[0]);
            } else {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
            }
        }

        // Derive initials
        $initials = trim($input['initials'] ?? '');
        if (empty($initials)) {
            $words = explode(' ', $name);
            $init = '';
            foreach ($words as $w) {
                if (!empty($w)) $init .= $w[0];
            }
            $initials = strtoupper(substr($init, 0, 3));
        }

        // Password handling
        $password = trim($input['password'] ?? '');
        if (empty($password)) {
            if ($existingIndex !== -1 && !empty($users[$existingIndex]['password'])) {
                $password = $users[$existingIndex]['password'];
            } else {
                $password = !empty($cleanPhone) ? $cleanPhone : '12345678';
            }
        }

        $department  = trim($input['department'] ?? 'Information Technology');
        $designation = trim($input['designation'] ?? 'Faculty Member');
        $empId       = trim($input['empId'] ?? '#---');
        $setting     = trim($input['setting'] ?? 'FF-38');
        $role        = trim($input['role'] ?? ($existingIndex !== -1 ? ($users[$existingIndex]['role'] ?? 'staff') : 'staff'));
        $status      = trim($input['status'] ?? ($existingIndex !== -1 ? ($users[$existingIndex]['status'] ?? 'active') : 'active'));
        if (!in_array(strtolower($status), ['active', 'inactive'])) {
            $status = 'active';
        }

        // Avatar class
        $avatarClass = trim($input['avatarClass'] ?? '');
        if (empty($avatarClass)) {
            $deptLower = strtolower($department);
            if (in_array($id, ['dc', 'sw', 'eu'])) {
                $avatarClass = 'av-' . $id;
            } elseif (strpos($deptLower, 'computer') !== false) {
                $avatarClass = 'av-dept-ce';
            } elseif (strpos($deptLower, 'both') !== false || strpos($deptLower, '&') !== false) {
                $avatarClass = 'av-dept-both';
            } else {
                $avatarClass = 'av-dept-it';
            }
        }

        $record = [
            'id'          => $id,
            'username'    => $username,
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'clean_phone' => $cleanPhone,
            'password'    => $password,
            'role'        => $role,
            'designation' => $designation,
            'department'  => $department,
            'empId'       => $empId,
            'avatarClass' => $avatarClass,
            'initials'    => $initials,
            'setting'     => $setting,
            'link'        => trim($input['link'] ?? ''),
            'semClass'    => trim($input['semClass'] ?? ''),
            'driveLink'   => trim($input['driveLink'] ?? ''),
            'status'      => strtolower($status)
        ];

        if ($existingIndex !== -1) {
            $users[$existingIndex] = array_merge($users[$existingIndex], $record);
            $msg = "Profile for {$name} updated successfully.";
        } else {
            array_unshift($users, $record);
            $msg = "New faculty member {$name} added successfully.";
        }

        $this->saveFacultyUsers($users);

        $this->json([
            'success' => true,
            'message' => $msg,
            'faculty' => $record
        ]);
    }

    /**
     * API: 1-Click Toggle Active / Inactive status
     * Endpoint: POST /api/faculty/toggle-status
     */
    public function apiToggleStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('faculty.toggle_status', 'Toggle Faculty Status');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $id = trim($input['id'] ?? '');
        if (empty($id)) {
            $this->json(['success' => false, 'error' => 'Faculty ID is required.'], 400);
            return;
        }

        $users = $this->loadFacultyUsers();
        $found = false;
        $newStatus = 'active';
        $name = 'Faculty member';

        foreach ($users as &$u) {
            if (strcasecmp($u['id'] ?? '', $id) === 0) {
                $current = strtolower($u['status'] ?? 'active');
                $newStatus = ($current === 'inactive') ? 'active' : 'inactive';
                $u['status'] = $newStatus;
                $name = $u['name'] ?? $id;
                $found = true;
                break;
            }
        }
        unset($u);

        if (!$found) {
            $this->json(['success' => false, 'error' => "Faculty record '{$id}' not found."], 404);
            return;
        }

        $this->saveFacultyUsers($users);

        $statusLabel = ($newStatus === 'active') ? 'Active' : 'Inactive';

        $this->json([
            'success'   => true,
            'id'        => $id,
            'status'    => $newStatus,
            'message'   => "{$name} is now marked as {$statusLabel}."
        ]);
    }

    /**
     * API: Delete a faculty member
     * Endpoint: POST /api/faculty/delete
     */
    public function apiDelete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('faculty.delete', 'Delete Faculty Member');

        // Explicit defense-in-depth: HOD and Sub Admin are strictly restricted from deleting records
        if (Auth::isHod() || Auth::isSubAdmin()) {
            $this->json(['success' => false, 'error' => 'HOD and Sub Admin roles are strictly restricted from deleting records.'], 403);
            return;
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $id = trim($input['id'] ?? '');
        if (empty($id)) {
            $this->json(['success' => false, 'error' => 'Faculty ID is required.'], 400);
            return;
        }

        // Protect primary administrative system accounts from accidental deletion
        $protectedIds = ['adminit', 'admince', 'dc', 'dk'];
        if (in_array(strtolower($id), $protectedIds)) {
            $this->json(['success' => false, 'error' => 'Cannot delete a protected Department Administrator account.'], 403);
            return;
        }

        $users = $this->loadFacultyUsers();
        $initialCount = count($users);
        $deletedName = '';

        $filtered = [];
        foreach ($users as $u) {
            if (strcasecmp($u['id'] ?? '', $id) === 0) {
                $deletedName = $u['name'] ?? $id;
            } else {
                $filtered[] = $u;
            }
        }

        if (count($filtered) === $initialCount) {
            $this->json(['success' => false, 'error' => "Faculty record '{$id}' not found."], 404);
            return;
        }

        $this->saveFacultyUsers($filtered);

        $this->json([
            'success' => true,
            'message' => "Faculty member {$deletedName} removed successfully."
        ]);
    }

    /**
     * API: Send login credentials to a faculty member via Gmail SMTP email
     * Endpoint: POST /api/faculty/send-credentials
     */
    public function apiSendCredentials(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        Auth::requirePermission('faculty.send_credentials', 'Send Faculty Credentials');

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true) ?: $_POST;

        $id = trim($input['id'] ?? '');
        if (empty($id)) {
            $this->json(['success' => false, 'error' => 'Faculty ID is required.'], 400);
            return;
        }

        $users = $this->loadFacultyUsers();
        $targetUser = null;
        foreach ($users as $u) {
            if (strcasecmp($u['id'] ?? '', $id) === 0) {
                $targetUser = $u;
                break;
            }
        }

        if (!$targetUser) {
            $this->json(['success' => false, 'error' => 'Faculty record not found.'], 404);
            return;
        }

        if (empty($targetUser['email']) || strpos($targetUser['email'], '@') === false) {
            $this->json(['success' => false, 'error' => "Cannot send email: no valid email address registered for {$targetUser['name']}."], 400);
            return;
        }

        $dept = (strpos(strtoupper($targetUser['department'] ?? ''), 'COMPUTER') !== false || strtoupper($targetUser['id'] ?? '') === 'ADMINCE') ? 'CE' : 'IT';

        try {
            $auth = new AuthController();
            $sent = $auth->sendCredentialsEmail($targetUser, $dept);
            if ($sent) {
                $this->json([
                    'success' => true,
                    'message' => "Login credentials dispatched successfully to " . htmlspecialchars($targetUser['email']) . "!"
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'error'   => "Could not dispatch email. Please verify SMTP connection and host credentials."
                ], 500);
            }
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'error'   => "Error dispatching email: " . $e->getMessage()
            ], 500);
        }
    }
}
