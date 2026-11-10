<?php
namespace Core;

/**
 * Authentication & Session Guard for Protected Resources
 * Manages user authentication, role enforcement, and session states without a database.
 */
class Auth
{
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
            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            }

            // If request is AJAX or JSON, respond with 401 JSON
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error'   => "Authentication required to access {$resourceLabel}.",
                    'redirect' => function_exists('base_url') ? base_url('login') : '/login'
                ]);
                exit;
            }

            $loginUrl = function_exists('base_url') ? base_url('login') : '/login';
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
     * Determine if current session is authenticated.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        return !empty($_SESSION['authenticated']) &&
               $_SESSION['authenticated'] === true &&
               !empty($_SESSION['user']) &&
               is_array($_SESSION['user']);
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
     * Get active user's role ('super_admin' | 'staff').
     */
    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    /**
     * Check if active user has Super Admin role.
     */
    public static function isSuperAdmin(): bool
    {
        return self::role() === 'super_admin';
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

        $_SESSION['authenticated'] = true;
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
