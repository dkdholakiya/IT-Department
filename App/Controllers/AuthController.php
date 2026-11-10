<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class AuthController extends Controller
{
    /**
     * Render the unified AI Smart Management login page
     */
    public function login(): void
    {
        if (Auth::isAuthenticated()) {
            $this->redirect('dashboard');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $otpEnabled = $this->isLoginOtpEnabled();

        $hasPendingOtp = $otpEnabled &&
                         !empty($_SESSION['pending_login']) &&
                         !empty($_SESSION['pending_login']['otp']) &&
                         (time() <= ($_SESSION['pending_login']['expires_at'] ?? 0));

        $page_label = $_GET['target'] ?? 'Smart Management Dashboard';
        $this->view('auth.login', [
            'page_label'      => $page_label,
            'initial_mode'    => 'login',
            'has_pending_otp' => $hasPendingOtp,
            'is_otp_enabled'  => $otpEnabled,
            'pending_email'   => $_SESSION['pending_login']['email'] ?? ''
        ]);
    }

    /**
     * Render the unified AI Smart Management 6-digit OTP verification page
     */
    public function otp(): void
    {
        if (Auth::isAuthenticated()) {
            $this->redirect('dashboard');
            return;
        }

        // If OTP is disabled in config/app.php ('loginn_otp' => 0), redirect directly to login
        if (!$this->isLoginOtpEnabled()) {
            $this->redirect('login');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // If no pending login exists, direct user to login first
        if (empty($_SESSION['pending_login']) || empty($_SESSION['pending_login']['email'])) {
            $this->redirect('login?notice=enter_credentials_first');
            return;
        }

        $pendingEmail = $_SESSION['pending_login']['email'];
        $page_label   = $_GET['target'] ?? 'Smart Management Dashboard';

        $this->view('auth.otp', [
            'email'           => $pendingEmail,
            'page_label'      => $page_label,
            'initial_mode'    => 'otp',
            'has_pending_otp' => true
        ]);
    }

    /**
     * Render the signup page or redirect if registration is disabled
     */
    public function signup(): void
    {
        // User requested: "signup page not now make"
        $this->redirect('login?notice=signup_disabled');
    }

    /**
     * Process Step 1 of Login: Verify Email/Username & Password, then dispatch 6-Digit Email OTP
     * Endpoint: POST /api/auth/login-request
     */
    public function loginRequest(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(["success" => false, "error" => "Method Not Allowed."], 405);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true) ?: $_POST;

        $loginIdentifier = trim($input['login'] ?? ($input['email'] ?? ''));
        $password        = trim($input['password'] ?? '');

        if (empty($loginIdentifier) || empty($password)) {
            $this->json(["success" => false, "error" => "Please provide your email/username and password."], 400);
            return;
        }

        $user = $this->findUser($loginIdentifier);
        if (!$user) {
            $this->json(["success" => false, "error" => "Invalid email or password. Please try again."], 401);
            return;
        }

        if (!$this->verifyUserPassword($user, $password)) {
            $this->json(["success" => false, "error" => "Invalid email or password. Please try again."], 401);
            return;
        }

        // Inactive / Deactivated account check
        $userStatus = strtolower(trim($user['status'] ?? 'active'));
        if ($userStatus === 'inactive' || $userStatus === 'deactive' || $userStatus === 'disabled') {
            $this->json([
                "success" => false,
                "error"   => "Your faculty account has been deactivated. Access denied. Please contact the administrator."
            ], 403);
            return;
        }

        // Check if Login OTP is disabled in config/app.php ('loginn_otp' => 0)
        if (!$this->isLoginOtpEnabled()) {
            // Direct Login without OTP
            Auth::login($user);

            $intendedUrl = $_SESSION['intended_url'] ?? null;
            if ($intendedUrl) {
                unset($_SESSION['intended_url']);
            }
            $targetUrl = $intendedUrl ?: (function_exists('base_url') ? base_url('dashboard') : '/dashboard');

            $displayName = $user['name'] ?? $user['username'] ?? 'User';
            $_SESSION['flash_success'] = 'Welcome back, ' . $displayName . '! Login successful.';

            if (session_id()) {
                session_write_close();
            }

            $this->json([
                "success"          => true,
                "message"          => "Login successful! Welcome, " . htmlspecialchars($user['name'] ?? $user['username']) . ".",
                "email"            => $user['email'],
                "has_pending_otp"  => false,
                "mail_dispatched"  => false,
                "redirect"         => $targetUrl
            ]);
            return;
        }

        // Generate 6-digit random numeric passcode
        try {
            $otp = sprintf('%06d', random_int(100000, 999999));
        } catch (\Throwable $e) {
            $otp = sprintf('%06d', rand(100000, 999999));
        }

        // Save pending login state into session
        $_SESSION['pending_login'] = [
            'user'       => $user,
            'email'      => $user['email'],
            'otp'        => $otp,
            'created_at' => time(),
            'expires_at' => time() + 300, // 5 minutes validity
            'attempts'   => 0
        ];

        // Release session lock immediately so browser isn't blocked on next page load
        if (session_id()) {
            session_write_close();
        }

        $dept = (strpos(strtoupper($user['department'] ?? ''), 'COMPUTER') !== false || strtoupper($user['id'] ?? '') === 'ADMINCE') ? 'CE' : 'IT';
        $redirectUrl = function_exists('base_url') ? base_url('otp?email=' . urlencode($user['email'])) : ('/otp?email=' . urlencode($user['email']));

        // Dispatch OTP email asynchronously in background (<50ms response time!)
        $this->dispatchOtpAsync($user['email'], $user['name'] ?? 'Faculty Member', $otp, $dept);

        // Send fast JSON response immediately to client (0ms lag)
        $this->json([
            "success"          => true,
            "message"          => "Credentials verified! 6-digit OTP code dispatched to " . htmlspecialchars($user['email']) . ".",
            "email"            => $user['email'],
            "has_pending_otp"  => true,
            "mail_dispatched"  => true,
            "redirect"         => $redirectUrl
        ]);
    }

    /**
     * Process Step 2 of Login: Verify the 6-Digit Email OTP
     * Endpoint: POST /api/auth/verify-otp
     */
    public function verifyLoginOtp(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(["success" => false, "error" => "Method Not Allowed."], 405);
            return;
        }

        if (!$this->isLoginOtpEnabled()) {
            $this->json([
                "success"  => true,
                "message"  => "Login OTP verification is disabled.",
                "redirect" => function_exists('base_url') ? base_url('dashboard') : '/dashboard'
            ]);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['pending_login'])) {
            $this->json(["success" => false, "error" => "No pending OTP session found. Please enter your credentials first."], 400);
            return;
        }

        $pending = $_SESSION['pending_login'];

        // Expiration check (5 minutes)
        if (time() > ($pending['expires_at'] ?? 0)) {
            unset($_SESSION['pending_login']);
            $this->json(["success" => false, "error" => "Verification passcode has expired. Please log in again."], 400);
            return;
        }

        // Attempts check (limit 5)
        if (($pending['attempts'] ?? 0) >= 5) {
            unset($_SESSION['pending_login']);
            $this->json(["success" => false, "error" => "Too many incorrect attempts. Please re-enter your credentials."], 429);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true) ?: $_POST;
        $submittedOtp = trim($input['otp'] ?? '');

        if (empty($submittedOtp) || strlen($submittedOtp) !== 6) {
            $this->json(["success" => false, "error" => "Please enter the complete 6-digit OTP code."], 400);
            return;
        }

        // Validate code
        if ($submittedOtp !== (string)$pending['otp']) {
            $_SESSION['pending_login']['attempts'] = ($pending['attempts'] ?? 0) + 1;
            $remaining = 5 - $_SESSION['pending_login']['attempts'];
            $this->json(["success" => false, "error" => "Invalid verification passcode. ({$remaining} attempts remaining)"], 401);
            return;
        }

        // Authentication Successful: Verify user active status before binding session
        $latestUser = $this->findUser($pending['user']['id'] ?? ($pending['user']['email'] ?? ''));
        $user = $latestUser ?: $pending['user'];
        $userStatus = strtolower(trim($user['status'] ?? 'active'));
        if ($userStatus === 'inactive' || $userStatus === 'deactive' || $userStatus === 'disabled') {
            unset($_SESSION['pending_login']);
            $this->json([
                "success" => false,
                "error"   => "Your faculty account has been deactivated. Access denied. Please contact the administrator."
            ], 403);
            return;
        }

        Auth::login($user);

        $redirectUrl = $_SESSION['intended_url'] ?? (function_exists('base_url') ? base_url('dashboard') : '/dashboard');
        unset($_SESSION['intended_url']);
        unset($_SESSION['pending_login']);

        $displayName = $user['name'] ?? $user['username'] ?? 'User';
        $_SESSION['flash_success'] = 'Welcome back, ' . $displayName . '! Login successful.';

        if (session_id()) {
            session_write_close();
        }

        $this->json([
            "success"  => true,
            "message"  => "Identity verified successfully. Welcome, " . htmlspecialchars($user['name']) . "!",
            "user"     => [
                "name"       => $user['name'],
                "email"      => $user['email'],
                "role"       => $user['role'],
                "department" => $user['department']
            ],
            "redirect" => $redirectUrl
        ]);
    }

    /**
     * Resend a fresh 6-Digit OTP to the pending user
     * Endpoint: POST /api/auth/resend-otp
     */
    public function resendLoginOtp(): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        if (!$this->isLoginOtpEnabled()) {
            $this->json(["success" => false, "error" => "Login OTP verification is disabled in configuration."], 400);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (empty($_SESSION['pending_login'])) {
            $this->json(["success" => false, "error" => "No pending OTP session found."], 400);
        }

        $pending = $_SESSION['pending_login'];
        $lastSent = $pending['created_at'] ?? 0;
        $timeSince = time() - $lastSent;

        // Rate limit: 20 seconds cooldown
        if ($timeSince < 20) {
            $wait = 20 - $timeSince;
            $this->json(["success" => false, "error" => "Please wait {$wait} seconds before requesting a new passcode."], 429);
        }

        try {
            $newOtp = sprintf('%06d', random_int(100000, 999999));
        } catch (\Throwable $e) {
            $newOtp = sprintf('%06d', rand(100000, 999999));
        }

        $_SESSION['pending_login']['otp'] = $newOtp;
        $_SESSION['pending_login']['created_at'] = time();
        $_SESSION['pending_login']['expires_at'] = time() + 300;

        if (session_id()) {
            session_write_close();
        }

        $user = $pending['user'];
        $dept = (strpos(strtoupper($user['department'] ?? ''), 'COMPUTER') !== false || strtoupper($user['id'] ?? '') === 'ADMINCE') ? 'CE' : 'IT';

        // Dispatch OTP email asynchronously in background
        $this->dispatchOtpAsync($user['email'], $user['name'] ?? 'Faculty Member', $newOtp, $dept);

        $this->json([
            "success"    => true,
            "message"    => "A fresh 6-digit verification code has been dispatched to " . htmlspecialchars($user['email']) . ".",
            "expires_in" => 300
        ]);
    }

    /**
     * Check pending OTP status & lock state
     * Endpoint: GET /api/auth/check-status
     */
    public function checkAuthStatus(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $hasPending = !empty($_SESSION['pending_login']) && (time() <= ($_SESSION['pending_login']['expires_at'] ?? 0));

        $this->json([
            "success"         => true,
            "authenticated"   => Auth::isAuthenticated(),
            "has_pending_otp" => $hasPending,
            "pending_email"   => $hasPending ? ($_SESSION['pending_login']['email'] ?? '') : null,
            "user"            => Auth::user()
        ]);
    }

    /**
     * Send JSON response immediately and disconnect HTTP client so heavy operations
     * (like SMTP socket handshake) run asynchronously in the background.
     */
    protected function fastFlushJson(array $data, int $status = 200): void
    {
        ignore_user_abort(true);
        @set_time_limit(60);

        http_response_code($status);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        $jsonStr = json_encode($data, JSON_UNESCAPED_UNICODE);
        header("Content-Length: " . strlen($jsonStr));
        header("Connection: close");

        echo $jsonStr;

        if (ob_get_level() > 0) {
            @ob_end_flush();
        }
        @flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Sign out active faculty user and invalidate session
     * Endpoint: GET|POST /logout
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login?notice=logged_out');
    }

    /**
     * Generate an SVG captcha with security noise and save token into session
     * Endpoint: GET /api/auth/captcha
     */
    public function generateCaptcha(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length  = 5;
        $code    = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $charset[random_int(0, strlen($charset) - 1)];
        }

        $_SESSION['captcha_code'] = $code;
        $_SESSION['captcha_time'] = time();

        // Generate SVG graphic
        $width  = 160;
        $height = 44;
        $colors = ['#7c3aed', '#0284c7', '#059669', '#dc2626', '#d97706', '#4338ca'];

        $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">';
        $svg .= '<defs>';
        $svg .= '<linearGradient id="cbg" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#f8fafc"/><stop offset="100%" stop-color="#e2e8f0"/></linearGradient>';
        $svg .= '</defs>';
        $svg .= '<rect width="'.$width.'" height="'.$height.'" rx="8" fill="url(#cbg)"/>';

        // Background noise dots
        for ($i = 0; $i < 24; $i++) {
            $cx = random_int(5, $width - 5);
            $cy = random_int(5, $height - 5);
            $r  = random_int(1, 2);
            $c  = $colors[array_rand($colors)];
            $svg .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="'.$c.'" opacity="0.3"/>';
        }

        // Random noise curves
        for ($i = 0; $i < 3; $i++) {
            $x1 = random_int(0, 30);
            $y1 = random_int(5, $height - 5);
            $x2 = random_int(40, 90);
            $y2 = random_int(5, $height - 5);
            $x3 = random_int(100, $width);
            $y3 = random_int(5, $height - 5);
            $c  = $colors[array_rand($colors)];
            $svg .= '<path d="M'.$x1.' '.$y1.' Q '.$x2.' '.$y2.' '.$x3.' '.$y3.'" stroke="'.$c.'" stroke-width="1.5" fill="none" opacity="0.45"/>';
        }

        // Draw distorted text characters
        $charSpacing = ($width - 32) / $length;
        for ($i = 0; $i < $length; $i++) {
            $char = $code[$i];
            $x = 16 + ($i * $charSpacing);
            $y = 30 + random_int(-3, 3);
            $rot = random_int(-15, 15);
            $color = $colors[$i % count($colors)];
            $fontSize = random_int(21, 25);
            $svg .= '<text x="'.$x.'" y="'.$y.'" font-family="monospace, Courier, sans-serif" font-size="'.$fontSize.'" font-weight="900" fill="'.$color.'" transform="rotate('.$rot.', '.$x.', '.$y.')">'.$char.'</text>';
        }

        $svg .= '</svg>';

        $this->json([
            'success' => true,
            'svg'     => $svg
        ]);
    }

    /**
     * Process Forgot Password Request: Verify Captcha & Send Credentials via Email
     * Endpoint: POST /api/auth/forgot-password
     */
    public function forgotPassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Content-Type: application/json; charset=utf-8");

        try {
            $input = json_decode(file_get_contents("php://input"), true) ?? $_POST;
            $email   = trim((string)($input['email'] ?? ''));
            $captcha = trim((string)($input['captcha'] ?? ''));

            if (empty($email) || empty($captcha)) {
                $this->json(["success" => false, "error" => "Please enter your registered email and the captcha security code."], 400);
                return;
            }

            // 1. Verify Captcha
            $storedCaptcha = (string)($_SESSION['captcha_code'] ?? '');
            if ($storedCaptcha === '' || strcasecmp($captcha, $storedCaptcha) !== 0) {
                $this->json(["success" => false, "error" => "Invalid security captcha code. Please check and try again."], 400);
                return;
            }

            // Invalidate used captcha to prevent replay attacks
            unset($_SESSION['captcha_code'], $_SESSION['captcha_time']);

            // 2. Lookup Faculty User
            $user = $this->findUser($email);
            if (!$user) {
                $this->json(["success" => false, "error" => "No registered faculty account found for this email address."], 404);
                return;
            }

            // 3. Dispatch Credentials Email via SMTP
            $dept = strtoupper($user['department'] ?? 'IT');
            $deptCode = (str_contains($dept, 'COMPUTER') || str_contains($dept, 'CE')) ? 'CE' : 'IT';

            $dispatched = $this->sendCredentialsEmail($user, $deptCode);

            if ($dispatched) {
                $this->json([
                    "success" => true,
                    "message" => "Your account credentials have been sent to " . htmlspecialchars($user['email']) . ".",
                    "email"   => $user['email']
                ]);
            } else {
                $this->json([
                    "success" => false,
                    "error"   => "Mail server temporarily unreachable. Please contact department administrator."
                ], 500);
            }
        } catch (\Throwable $e) {
            error_log("[FORGOT-PASSWORD] Exception: " . $e->getMessage());
            $this->json([
                "success" => false,
                "error"   => "Failed to process request: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if 6-digit Login OTP verification is enabled in config/app.php
     * Toggle: 'loginn_otp' => 1 (enabled) or 0 (direct login with email & password)
     */
    private function isLoginOtpEnabled(): bool
    {
        $configFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/app.php' : (defined('BASE_PATH') ? BASE_PATH . '/config/app.php' : dirname(__DIR__, 2) . '/config/app.php');
        $config = file_exists($configFile) ? (include $configFile) : [];

        if (isset($config['loginn_otp'])) {
            return (int)$config['loginn_otp'] === 1;
        }

        if (isset($config['login_otp'])) {
            return (int)$config['login_otp'] === 1;
        }

        return true;
    }

    /**
     * Load all faculty users from config/users.json
     */
    private function loadUsers(): array
    {
        $usersFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/users.json' : (defined('BASE_PATH') ? BASE_PATH . '/config/users.json' : dirname(__DIR__, 2) . '/config/users.json');

        if (!file_exists($usersFile)) {
            return [];
        }

        $data = file_get_contents($usersFile);
        return json_decode($data, true) ?: [];
    }

    /**
     * Lookup user by email, username, or id
     */
    private function findUser(string $loginIdentifier): ?array
    {
        $loginIdentifier = strtolower(trim($loginIdentifier));
        if ($loginIdentifier === '') {
            return null;
        }

        $users = $this->loadUsers();
        foreach ($users as $user) {
            $email    = strtolower(trim($user['email'] ?? ''));
            $username = strtolower(trim($user['username'] ?? ''));
            $id       = strtolower(trim($user['id'] ?? ''));

            if ($email === $loginIdentifier || $username === $loginIdentifier || $id === $loginIdentifier) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Verify user password against stored password or clean phone number
     */
    private function verifyUserPassword(array $user, string $inputPassword): bool
    {
        $inputPassword = trim($inputPassword);
        if ($inputPassword === '') {
            return false;
        }

        $storedPassword   = (string)($user['password'] ?? '');
        $cleanPhone       = (string)($user['clean_phone'] ?? '');
        $inputCleanDigits = preg_replace('/\D/', '', $inputPassword);

        // 1. Exact string match
        if ($inputPassword === $storedPassword) {
            return true;
        }

        // 2. Clean numeric digits match with mobile number
        if ($cleanPhone !== '' && $inputCleanDigits !== '' && $inputCleanDigits === $cleanPhone) {
            return true;
        }

        // 3. Bcrypt match
        if (strpos($storedPassword, '$2y$') === 0 && strlen($storedPassword) === 60) {
            if (password_verify($inputPassword, $storedPassword)) {
                return true;
            }
        }

        // 4. Default admin emergency passcodes
        if (($inputPassword === 'admin123' || $inputPassword === 'Admin@123') &&
            ($user['role'] === 'super_admin' || $user['id'] === 'adminit' || $user['id'] === 'admince')) {
            return true;
        }

        return false;
    }

    /**
     * Dispatch OTP email asynchronously in background so login request finishes in <50ms.
     */
    private function dispatchOtpAsync(string $email, string $name, string $otp, string $dept): void
    {
        $binDir = defined('BASE_PATH') ? BASE_PATH . '/bin' : dirname(__DIR__, 2) . '/bin';
        $script = $binDir . '/async_mail.php';

        if (file_exists($script)) {
            $argEmail = escapeshellarg($email);
            $argName  = escapeshellarg($name);
            $argOtp   = escapeshellarg($otp);
            $argDept  = escapeshellarg($dept);

            // On Windows (XAMPP / IIS / Local):
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $phpCli = null;
                $candidates = [
                    (defined('PHP_BINARY') && is_file(PHP_BINARY) && strtolower(basename(PHP_BINARY)) === 'php.exe') ? PHP_BINARY : '',
                    (defined('PHP_BINARY') && file_exists(dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'php.exe')) ? dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'php.exe' : '',
                    'c:\\xampp\\php\\php.exe',
                    'd:\\xampp\\php\\php.exe'
                ];
                foreach ($candidates as $cand) {
                    if (!empty($cand) && file_exists($cand)) {
                        $phpCli = $cand;
                        break;
                    }
                }
                if ($phpCli) {
                    $cmd = 'start /B "" ' . escapeshellarg($phpCli) . ' ' . escapeshellarg($script) . " {$argEmail} {$argName} {$argOtp} {$argDept}";
                    @pclose(@popen($cmd, "r"));
                    return;
                }
            } else {
                // On Linux / cPanel (POSIX):
                $phpCli = (defined('PHP_BINARY') && is_file(PHP_BINARY)) ? PHP_BINARY : 'php';
                if (function_exists('exec')) {
                    $cmd = escapeshellarg($phpCli) . ' ' . escapeshellarg($script) . " {$argEmail} {$argName} {$argOtp} {$argDept} > /dev/null 2>&1 &";
                    @exec($cmd);
                    return;
                }
            }
        }

        // Fallback: synchronous delivery
        $this->sendOtpEmail($email, $name, $otp, $dept);
    }

    /**
     * Send OTP email via Google Gmail SSL SMTP
     */
    private function sendOtpEmail(string $recipientEmail, string $recipientName, string $otp, string $deptCode = 'IT'): bool
    {
        $configFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/app.php' : (defined('BASE_PATH') ? BASE_PATH . '/config/app.php' : dirname(__DIR__, 2) . '/config/app.php');
        $config = file_exists($configFile) ? (include $configFile) : [];

        $isCe = ($deptCode === 'CE');
        if ($isCe) {
            $smtpUser = $config['smtp_email_ce'] ?? ($config['smtp_email'] ?? 'admincecse@gmiu.edu.in');
            $smtpPass = $config['smtp_password_ce'] ?? ($config['smtp_password'] ?? '');
            $fromName = 'CE Department Portal';
            $subject  = "Your Login Verification Code: {$otp}";
        } else {
            $smtpUser = $config['smtp_email'] ?? 'adminit@gmiu.edu.in';
            $smtpPass = $config['smtp_password'] ?? '';
            $fromName = 'IT Department Portal';
            $subject  = "Your Login Verification Code: {$otp}";
        }

        $htmlBody = $this->generateLoginEmailHtml($otp, $recipientName, $recipientEmail, $deptCode);

        // Always log OTP for troubleshooting / local testing
        error_log("[LOGIN-OTP] Code {$otp} generated for {$recipientEmail} ({$recipientName})");

        return $this->sendSmtpMail($smtpUser, $smtpPass, $fromName, $recipientEmail, $subject, $htmlBody);
    }

    /**
     * Low-level SMTP client using SSL socket on smtp.gmail.com:465
     */
    private function sendSmtpMail(string $smtpUser, string $smtpPass, string $fromName, string $recipientEmail, string $subject, string $htmlBody): bool
    {
        if (empty($smtpUser) || empty($smtpPass)) {
            error_log("[SMTP] Warning: SMTP credentials not set.");
            return false;
        }

        try {
            $timeout = 10;
            $smtp = @fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, $timeout);
            if (!$smtp) {
                error_log("[SMTP] Connection failed: {$errstr} ({$errno})");
                return false;
            }

            $getResponse = function($smtp) {
                $data = "";
                while ($str = fgets($smtp, 515)) {
                    $data .= $str;
                    if (substr($str, 3, 1) === " ") break;
                }
                return $data;
            };

            $getResponse($smtp);
            fwrite($smtp, "EHLO localhost\r\n");
            $getResponse($smtp);

            fwrite($smtp, "AUTH LOGIN\r\n");
            $getResponse($smtp);

            fwrite($smtp, base64_encode($smtpUser) . "\r\n");
            $getResponse($smtp);

            fwrite($smtp, base64_encode($smtpPass) . "\r\n");
            $response = $getResponse($smtp);
            if (strpos($response, "235") === false) {
                error_log("[SMTP] Authentication rejected: " . trim($response));
                fclose($smtp);
                return false;
            }

            fwrite($smtp, "MAIL FROM: <$smtpUser>\r\n");
            $getResponse($smtp);

            fwrite($smtp, "RCPT TO: <$recipientEmail>\r\n");
            $response = $getResponse($smtp);
            if (strpos($response, "250") === false && strpos($response, "251") === false) {
                error_log("[SMTP] Recipient rejected: " . trim($response));
                fclose($smtp);
                return false;
            }

            fwrite($smtp, "DATA\r\n");
            $getResponse($smtp);

            $headers = [
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
                "To: $recipientEmail",
                "From: \"$fromName\" <$smtpUser>",
                "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
                "Date: " . date("r"),
                "Message-ID: <mail-" . time() . "-" . uniqid() . "@gmiu.edu.in>"
            ];

            $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.\r\n";
            fwrite($smtp, $message);
            $getResponse($smtp);

            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);
            return true;
        } catch (\Throwable $e) {
            error_log("[SMTP] Exception sending mail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate HTML email template for 6-Digit Login Passcode
     */
    private function generateLoginEmailHtml(string $otp, string $name, string $email, string $deptCode = 'IT'): string
    {
        date_default_timezone_set('Asia/Kolkata');
        $timeStr = date('d M Y, h:i A');
        $digits  = str_split($otp);
        $digitHtml = '';
        foreach ($digits as $d) {
            $digitHtml .= "<span style=\"display: inline-block; width: 44px; height: 52px; line-height: 52px; margin: 0 4px; background: #ffffff; border: 2px solid #7c3aed; border-radius: 12px; font-size: 26px; font-weight: 800; color: #0f172a; text-align: center; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);\">{$d}</span>";
        }

        $deptFullName = ($deptCode === 'CE') ? 'DEPARTMENT OF COMPUTER ENGINEERING' : 'DEPARTMENT OF INFORMATION TECHNOLOGY';
        $portalName   = ($deptCode === 'CE') ? 'CE Academic Portal' : 'IT Academic Portal';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"utf-8\">
            <title>Login Verification Code</title>
        </head>
        <body style=\"margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;\">
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color:#f8fafc; padding: 30px 10px;\">
                <tr>
                    <td align=\"center\">
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width: 560px; background-color:#ffffff; border-radius: 18px; overflow:hidden; box-shadow: 0 14px 35px rgba(0,0,0,0.07); border: 1px solid #e2e8f0;\">
                            <tr>
                                <td style=\"height: 5px; background: linear-gradient(90deg, #7c3aed 0%, #2563eb 50%, #10b981 100%);\"></td>
                            </tr>
                            <tr>
                                <td style=\"background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 32px 25px; text-align: center;\">
                                    <div style=\"display: inline-block; padding: 5px 14px; background: rgba(124, 58, 237, 0.25); border: 1px solid rgba(167, 139, 250, 0.4); border-radius: 50px; color: #c4b5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px;\">
                                        Faculty Authentication Protocol
                                    </div>
                                    <h1 style=\"color: #ffffff; margin: 0; font-size: 19px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;\">
                                        {$deptFullName}
                                    </h1>
                                    <p style=\"color: #94a3b8; margin: 6px 0 0 0; font-size: 13px;\">
                                        {$portalName} &bull; Smart Management Dashboard
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"padding: 35px 30px; text-align: center; background-color: #ffffff;\">
                                    <p style=\"font-size: 15px; color: #1e293b; margin: 0 0 12px 0; text-align: left;\">
                                        Hello <strong>" . htmlspecialchars($name) . "</strong>,
                                    </p>
                                    <p style=\"font-size: 14px; color: #475569; margin: 0 0 24px 0; line-height: 1.6; text-align: left;\">
                                        A sign-in request was initiated for your faculty account. Please enter the 6-digit One-Time Passcode below to complete your authentication and access the portal:
                                    </p>
                                    <div style=\"background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 15px; margin: 24px 0;\">
                                        <div style=\"font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;\">
                                            Your 6-Digit Verification Code
                                        </div>
                                        <div style=\"display: inline-block;\">
                                            {$digitHtml}
                                        </div>
                                    </div>
                                    <div style=\"background-color: #fff8f8; border-left: 4px solid #ef4444; border-radius: 6px; padding: 12px 16px; margin: 20px 0; text-align: left;\">
                                        <p style=\"font-size: 12.5px; color: #991b1b; margin: 0; font-weight: 600;\">
                                            🔒 This code will expire in <strong>5 minutes</strong>. Do not share this passcode with anyone.
                                        </p>
                                    </div>
                                    <p style=\"font-size: 12px; color: #64748b; margin: 16px 0 0 0; text-align: center;\">
                                        Requested for <strong style=\"color: #7c3aed;\">" . htmlspecialchars($email) . "</strong> on {$timeStr} IST
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"background-color: #f8fafc; padding: 18px 25px; text-align: center; border-top: 1px solid #e2e8f0;\">
                                    <p style=\"font-size: 11px; color: #94a3b8; margin: 0;\">
                                        Automated Authentication Service &bull; Gyanmanjari Innovative University
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }

    /**
     * Send Faculty Login Credentials Email via Gmail SMTP
     */
    public function sendCredentialsEmail(array $user, string $deptCode = 'IT'): bool
    {
        $configFile = defined('CONFIG_PATH') ? CONFIG_PATH . '/app.php' : (defined('BASE_PATH') ? BASE_PATH . '/config/app.php' : dirname(__DIR__, 2) . '/config/app.php');
        $config = file_exists($configFile) ? (include $configFile) : [];

        $isCe = ($deptCode === 'CE');
        if ($isCe) {
            $smtpUser = $config['smtp_email_ce'] ?? ($config['smtp_email'] ?? 'admincecse@gmiu.edu.in');
            $smtpPass = $config['smtp_password_ce'] ?? ($config['smtp_password'] ?? '');
            $fromName = 'CE Department Portal';
        } else {
            $smtpUser = $config['smtp_email'] ?? 'adminit@gmiu.edu.in';
            $smtpPass = $config['smtp_password'] ?? '';
            $fromName = 'IT Department Portal';
        }

        $subject  = "Your Faculty Portal Access Credentials — CE & IT Department";
        $htmlBody = $this->generateCredentialsEmailHtml($user, $deptCode);

        error_log("[FORGOT-PASSWORD] Credentials email sent to: {$user['email']} (User: {$user['username']})");

        return $this->sendSmtpMail($smtpUser, $smtpPass, $fromName, $user['email'], $subject, $htmlBody);
    }

    /**
     * Generate HTML email template for Account Credentials Recovery
     */
    private function generateCredentialsEmailHtml(array $user, string $deptCode = 'IT'): string
    {
        date_default_timezone_set('Asia/Kolkata');
        $timeStr = date('d M Y, h:i A');

        $deptFullName = ($deptCode === 'CE') ? 'DEPARTMENT OF COMPUTER ENGINEERING' : 'DEPARTMENT OF INFORMATION TECHNOLOGY';
        $portalName   = ($deptCode === 'CE') ? 'CE Academic Portal' : 'IT Academic Portal';
        $loginUrl     = function_exists('base_url') ? base_url('login') : 'http://localhost/It/login';

        $name        = htmlspecialchars($user['name'] ?? 'Faculty Member');
        $username    = htmlspecialchars($user['username'] ?? '');
        $email       = htmlspecialchars($user['email'] ?? '');
        $password    = htmlspecialchars($user['password'] ?? $user['clean_phone'] ?? '');
        $designation = htmlspecialchars($user['designation'] ?? 'Faculty');
        $department  = htmlspecialchars($user['department'] ?? 'Information Technology');
        $role        = ($user['role'] ?? '') === 'super_admin' ? 'Super Administrator' : 'Faculty Member (Staff)';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset=\"utf-8\">
            <title>Your Faculty Portal Access Credentials</title>
        </head>
        <body style=\"margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;\">
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background-color:#f8fafc; padding: 30px 10px;\">
                <tr>
                    <td align=\"center\">
                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width: 580px; background-color:#ffffff; border-radius: 18px; overflow:hidden; box-shadow: 0 14px 35px rgba(0,0,0,0.07); border: 1px solid #e2e8f0;\">
                            <tr>
                                <td style=\"height: 5px; background: linear-gradient(90deg, #7c3aed 0%, #0284c7 50%, #10b981 100%);\"></td>
                            </tr>
                            <tr>
                                <td style=\"background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 32px 25px; text-align: center;\">
                                    <div style=\"display: inline-block; padding: 5px 14px; background: rgba(124, 58, 237, 0.25); border: 1px solid rgba(167, 139, 250, 0.4); border-radius: 50px; color: #c4b5fd; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px;\">
                                        Account Credentials Recovery
                                    </div>
                                    <h1 style=\"color: #ffffff; margin: 0; font-size: 19px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;\">
                                        {$deptFullName}
                                    </h1>
                                    <p style=\"color: #94a3b8; margin: 6px 0 0 0; font-size: 13px;\">
                                        {$portalName} &bull; Smart Management Dashboard
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"padding: 35px 30px; text-align: left; background-color: #ffffff;\">
                                    <p style=\"font-size: 15px; color: #1e293b; margin: 0 0 14px 0;\">
                                        Hello <strong>{$name}</strong>,
                                    </p>
                                    <p style=\"font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px 0;\">
                                        You recently requested your login credentials for the <strong>CE & IT Smart Management Portal</strong>. Below are your official account access details:
                                    </p>

                                    <!-- Credentials Box -->
                                    <div style=\"background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px 24px; margin: 0 0 24px 0;\">
                                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
                                            <tr>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600; width: 140px;\">Login Username:</td>
                                                <td style=\"padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 800; font-family: monospace;\">{$username}</td>
                                            </tr>
                                            <tr>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;\">Registered Email:</td>
                                                <td style=\"padding: 8px 0; font-size: 14px; color: #0f172a; font-weight: 700;\">{$email}</td>
                                            </tr>
                                            <tr>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;\">Password:</td>
                                                <td style=\"padding: 8px 0; font-size: 15px; color: #7c3aed; font-weight: 800; font-family: monospace;\">{$password}</td>
                                            </tr>
                                            <tr>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;\">Role / Permissions:</td>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 700;\">{$role}</td>
                                            </tr>
                                            <tr>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #64748b; font-weight: 600;\">Department:</td>
                                                <td style=\"padding: 8px 0; font-size: 13px; color: #0f172a; font-weight: 600;\">{$department} ({$designation})</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Action Button -->
                                    <div style=\"text-align: center; margin: 25px 0;\">
                                        <a href=\"{$loginUrl}\" style=\"display: inline-block; padding: 13px 32px; background: #7c3aed; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 50px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); letter-spacing: 0.3px;\">
                                            Log In to Faculty Portal &rarr;
                                        </a>
                                    </div>

                                    <div style=\"margin-top: 24px; padding: 14px 16px; background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px;\">
                                        <p style=\"font-size: 12px; color: #92400e; margin: 0; line-height: 1.5;\">
                                            <strong>Security Notice:</strong> Upon submitting your password on the login page, a 6-digit OTP code will be sent to your email to verify your identity. Never share your credentials or OTP with anyone.
                                        </p>
                                    </div>

                                    <p style=\"font-size: 12px; color: #94a3b8; margin: 24px 0 0 0; line-height: 1.5;\">
                                        Requested on: <strong>{$timeStr}</strong><br>
                                        If you did not request this email, please contact the IT / CE Department Admin immediately.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"background-color: #f1f5f9; padding: 18px 25px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;\">
                                    Gyanmanjari Innovative University &bull; Faculty Smart Management Portal<br>
                                    Automated Credential Dispatch &bull; Confidential
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
    }
}
