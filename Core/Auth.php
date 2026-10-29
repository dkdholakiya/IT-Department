<?php
namespace Core;

/**
 * Authentication & Session Guard for Protected Resources
 */
class Auth
{
    /**
     * Check if user is authenticated; if not, render auth-login view and terminate request.
     *
     * @param string $pageLabel Descriptive title for the protected resource
     * @return bool
     */
    public static function check(string $pageLabel = 'this protected resource'): bool
    {
        $passwordRequired = (int)app_config('password_required', 0);

        $authenticated = false;
        if ($passwordRequired === 0) {
            $authenticated = true;
        } elseif (!empty($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            $authenticated = true;
            // One-time session token: reset so refresh re-validates password
            $_SESSION['authenticated'] = false;
        }

        if (!$authenticated) {
            $page_label = $pageLabel;
            $viewPath = APP_PATH . '/Views/auth/login.php';
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                http_response_code(401);
                echo "Unauthorized Access: Password Required.";
            }
            exit;
        }

        return true;
    }

    /**
     * Determine if current session is authenticated without terminating.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        $passwordRequired = (int)app_config('password_required', 0);
        if ($passwordRequired === 0) {
            return true;
        }
        return !empty($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    /**
     * Set session authentication state.
     *
     * @param bool $state
     */
    public static function setAuthenticated(bool $state = true): void
    {
        $_SESSION['authenticated'] = $state;
    }

    /**
     * Clear authentication session.
     */
    public static function clear(): void
    {
        $_SESSION['authenticated'] = false;
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
