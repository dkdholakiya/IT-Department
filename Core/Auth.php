<?php
namespace Core;

/**
 * Authentication & Session Guard for Protected Resources
 * Manages user authentication, role enforcement, and session states without a database.
 */
class Auth
{
    /**
     * Session lifetime: exactly 24 hours in seconds (24 * 60 * 60)
     */
    const SESSION_LIFETIME = 86400;

    /**
     * Cached roles and permission matrices loaded from config/roles.json
     * @var array|null
     */
    private static ?array $rolesConfig = null;

    /**
     * Load roles configuration from config/roles.json
     */
    public static function getRolesConfig(): array
    {
        if (self::$rolesConfig !== null) {
            return self::$rolesConfig;
        }

        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
        $rolesFile = $base . '/config/roles.json';

        if (file_exists($rolesFile)) {
            $content = file_get_contents($rolesFile);
            $parsed = json_decode($content, true);
            if (is_array($parsed)) {
                self::$rolesConfig = $parsed;
                return self::$rolesConfig;
            }
        }

        // Resilient fallback default role configuration
        self::$rolesConfig = [
            'modules' => [],
            'roles' => [
                'super_admin' => [
                    'key' => 'super_admin',
                    'name' => 'Super Admin (Developer)',
                    'permissions' => ['*']
                ],
                'hod' => [
                    'key' => 'hod',
                    'name' => 'Head of Department (HOD)',
                    'permissions' => []
                ],
                'sub_admin' => [
                    'key' => 'sub_admin',
                    'name' => 'Sub Admin',
                    'permissions' => []
                ],
                'faculty' => [
                    'key' => 'faculty',
                    'name' => 'Faculty Member',
                    'permissions' => ['dashboard.view', 'calendar.view', 'timetable.view', 'student_timetable.view', 'report.view', 'syllabus.view']
                ]
            ]
        ];

        return self::$rolesConfig;
    }

    /**
     * Normalize role identifier for consistency.
     * Legacy 'staff' is mapped to 'faculty'.
     */
    public static function normalizeRole(?string $role): string
    {
        $role = strtolower(trim($role ?? 'faculty'));
        if ($role === 'staff' || empty($role)) {
            return 'faculty';
        }
        return $role;
    }

    /**
     * Extract all unique permission IDs across all modules.
     */
    public static function allPermissionIds(): array
    {
        $config = self::getRolesConfig();
        $perms = [];
        foreach ($config['modules'] ?? [] as $m) {
            foreach ($m['permissions'] ?? [] as $p) {
                if (!empty($p['id'])) {
                    $perms[] = $p['id'];
                }
            }
        }
        return array_values(array_unique($perms));
    }

    /**
     * Check if a specific permission is granted to a user.
     * Super Admin has wildcard full access (*).
     *
     * @param string $permission Permission ID key (e.g. 'faculty.edit', 'dashboard.view')
     * @param array|null $user Optional user data array (defaults to active session user)
     * @return bool
     */
    public static function can(string $permission, ?array $user = null): bool
    {
        if ($user === null) {
            $user = self::user();
        }

        if (empty($user) || !is_array($user)) {
            return false;
        }

        $role = self::normalizeRole($user['role'] ?? 'faculty');

        // Super Admin (Developer) bypasses all permission restrictions
        if ($role === 'super_admin') {
            return true;
        }

        $config = self::getRolesConfig();
        $roleDef = $config['roles'][$role] ?? null;

        if (!$roleDef || empty($roleDef['permissions']) || !is_array($roleDef['permissions'])) {
            return false;
        }

        // Wildcard permission check
        if (in_array('*', $roleDef['permissions'], true)) {
            return true;
        }

        // Direct permission check
        return in_array($permission, $roleDef['permissions'], true);
    }

    /**
     * Retrieve all active permission keys granted to the current user.
     *
     * @param array|null $user
     * @return array
     */
    public static function userPermissions(?array $user = null): array
    {
        if ($user === null) {
            $user = self::user();
        }

        if (empty($user)) {
            return [];
        }

        $role = self::normalizeRole($user['role'] ?? 'faculty');

        // Super Admin has all permission IDs
        if ($role === 'super_admin') {
            return self::allPermissionIds();
        }

        $config = self::getRolesConfig();
        $roleDef = $config['roles'][$role] ?? null;

        return is_array($roleDef['permissions'] ?? null) ? $roleDef['permissions'] : [];
    }

    /**
     * Enforce granular permission guard on protected controller action.
     * Renders a 403 Forbidden page or returns 403 JSON if unauthorized.
     *
     * @param string $permission Permission key
     * @param string $resourceLabel Descriptive resource label
     */
    public static function requirePermission(string $permission, string $resourceLabel = 'Protected Resource'): void
    {
        // 1. Enforce authentication first
        self::check($resourceLabel);

        // 2. Enforce permission
        if (!self::can($permission)) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                      (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);

            if ($isAjax) {
                while (ob_get_level()) { @ob_end_clean(); }
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error'   => "Access Denied: You do not have permission [{$permission}] to access {$resourceLabel}."
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            while (ob_get_level()) { @ob_end_clean(); }
            http_response_code(403);
            header('Content-Type: text/html; charset=utf-8');

            $status = 403;
            $title = "Access Forbidden";
            $description = "You do not have the required permission [{$permission}] to access {$resourceLabel}. Please contact your Department Administrator.";

            $errorView = defined('APP_PATH') ? APP_PATH . '/Views/errors/error.php' : dirname(__DIR__) . '/App/Views/errors/error.php';
            if (file_exists($errorView)) {
                require $errorView;
            } else {
                echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>403 Forbidden</title></head><body style='font-family:sans-serif;padding:40px;text-align:center;'><h1>403 Forbidden</h1><p>{$description}</p><p><a href='/it/dashboard' style='color:#10b981;font-weight:bold;'>Return to Dashboard</a></p></body></html>";
            }
            exit;
        }
    }

    /**
     * Check if user is authenticated; if not, redirect to login page.
     * Optionally enforce a specific role (e.g. 'super_admin').
     *
     * @param string $resourceLabel Descriptive title for the protected resource
     * @param string|null $requiredRole Optional required role ('super_admin', 'staff')
     * @return bool
     */
    public static function check(string $resourceLabel = 'Dashboard', ?string $requiredRole = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (!self::isAuthenticated()) {
            $isExpired = !empty($_SESSION['session_expired_flag']);
            unset($_SESSION['session_expired_flag']);

            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            }

            $noticeParam = $isExpired ? 'notice=session_expired' : 'notice=auth_required';

            // If request is AJAX or JSON, respond with 401 JSON
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'expired' => $isExpired,
                    'error'   => $isExpired 
                        ? "Your session has expired after 24 hours. Please log in again." 
                        : "Authentication required to access {$resourceLabel}.",
                    'redirect' => function_exists('base_url') ? base_url("login?{$noticeParam}") : "/login?{$noticeParam}"
                ]);
                exit;
            }

            $loginUrl = function_exists('base_url') ? base_url("login?{$noticeParam}") : "/login?{$noticeParam}";
            header("Location: {$loginUrl}");
            exit;
        }

        // Active account check: force logout if account has been deactivated
        $user = self::user();
        $userStatus = strtolower(trim($user['status'] ?? 'active'));
        if ($userStatus === 'inactive' || $userStatus === 'deactive' || $userStatus === 'disabled') {
            self::logout();
            $loginUrl = function_exists('base_url') ? base_url('login?notice=account_deactivated') : '/login?notice=account_deactivated';
            header("Location: {$loginUrl}");
            exit;
        }

        // Role check if specified
        if ($requiredRole !== null && self::role() !== $requiredRole) {
            if ($requiredRole === 'super_admin' && !self::isSuperAdmin()) {
                http_response_code(403);
                header('Content-Type: text/html; charset=utf-8');
                echo "<h1>403 Forbidden</h1><p>Super Administrator privileges are required to access this resource.</p>";
                exit;
            }
        }

        return true;
    }

    /**
     * Determine if current session is authenticated and within 24-hour lifetime.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['authenticated']) ||
            $_SESSION['authenticated'] !== true ||
            empty($_SESSION['user']) ||
            !is_array($_SESSION['user'])) {
            return false;
        }

        // Enforce 24-Hour Expiration Check
        if (self::isSessionExpired()) {
            self::expireSession();
            return false;
        }

        return true;
    }

    /**
     * Check if active session has exceeded the 24-hour lifetime limit.
     *
     * @return bool
     */
    public static function isSessionExpired(): bool
    {
        if (empty($_SESSION['login_expires_at'])) {
            // For active sessions initialized before timestamp implementation, assign 24h from now
            if (!empty($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
                $_SESSION['login_time'] = time();
                $_SESSION['login_expires_at'] = time() + self::SESSION_LIFETIME;
            }
            return false;
        }

        return time() > (int)$_SESSION['login_expires_at'];
    }

    /**
     * Invalidate session due to 24-hour expiration and flag for user notification.
     */
    public static function expireSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION['authenticated'] = false;
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['login_expires_at']);
        $_SESSION['session_expired_flag'] = true;
    }

    /**
     * Get the Unix timestamp when active session expires (or null if unauthenticated).
     *
     * @return int|null
     */
    public static function sessionExpiresAt(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            return null;
        }

        return !empty($_SESSION['login_expires_at']) ? (int)$_SESSION['login_expires_at'] : null;
    }

    /**
     * Get remaining seconds until active session expires.
     *
     * @return int
     */
    public static function sessionRemainingSeconds(): int
    {
        $expiresAt = self::sessionExpiresAt();
        if (!$expiresAt) return 0;
        return max(0, $expiresAt - time());
    }

    /**
     * Retrieve active authenticated user data array.
     *
     * @return array|null
     */
    public static function user(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        return $_SESSION['user'] ?? null;
    }

    /**
     * Get active user's unique ID.
     */
    public static function id(): ?string
    {
        return self::user()['id'] ?? null;
    }

    /**
     * Get active user's username.
     */
    public static function username(): ?string
    {
        return self::user()['username'] ?? null;
    }

    /**
     * Get active user's full name.
     */
    public static function name(): ?string
    {
        return self::user()['name'] ?? null;
    }

    /**
     * Get active user's email address.
     */
    public static function email(): ?string
    {
        return self::user()['email'] ?? null;
    }

    /**
     * Get active user's raw role ('super_admin' | 'hod' | 'sub_admin' | 'faculty' | 'staff').
     */
    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    /**
     * Get active user's normalized role.
     */
    public static function userRole(): string
    {
        return self::normalizeRole(self::role());
    }

    /**
     * Check if active user has Super Admin (Developer) role.
     */
    public static function isSuperAdmin(): bool
    {
        return self::userRole() === 'super_admin';
    }

    /**
     * Check if active user has Head of Department (HOD) role.
     */
    public static function isHod(): bool
    {
        return self::userRole() === 'hod';
    }

    /**
     * Check if active user has Sub Admin role.
     */
    public static function isSubAdmin(): bool
    {
        return self::userRole() === 'sub_admin';
    }

    /**
     * Check if active user has Faculty (Staff) role.
     */
    public static function isFaculty(): bool
    {
        return self::userRole() === 'faculty';
    }

    /**
     * Authenticate and bind user profile into active session.
     *
     * @param array $user User data array
     */
    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Prevent session fixation
        @session_regenerate_id(true);

        $now = time();
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = $now;
        $_SESSION['login_expires_at'] = $now + self::SESSION_LIFETIME; // Exactly 24 hours
        unset($_SESSION['session_expired_flag']);

        $_SESSION['user'] = [
            'id'          => $user['id'] ?? '',
            'username'    => $user['username'] ?? '',
            'name'        => $user['name'] ?? '',
            'email'       => strtolower(trim($user['email'] ?? '')),
            'role'        => $user['role'] ?? 'staff',
            'department'  => $user['department'] ?? '',
            'designation' => $user['designation'] ?? '',
            'empId'       => $user['empId'] ?? '',
            'initials'    => $user['initials'] ?? '',
            'avatarClass' => $user['avatarClass'] ?? ''
        ];

        // Clean up pending login state if any
        unset($_SESSION['pending_login']);
    }

    /**
     * Terminate authenticated session and clear all authentication tokens.
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION['authenticated'] = false;
        unset($_SESSION['user']);
        unset($_SESSION['login_time']);
        unset($_SESSION['login_expires_at']);
        unset($_SESSION['session_expired_flag']);
        unset($_SESSION['pending_login']);

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        @session_destroy();
    }

    /**
     * Legacy compatibility method
     */
    public static function setAuthenticated(bool $state = true): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['authenticated'] = $state;
    }

    /**
     * Legacy compatibility method
     */
    public static function clear(): void
    {
        self::logout();
    }
}
