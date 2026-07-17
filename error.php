<?php
// Retrieve error code
$status = $_SERVER['REDIRECT_STATUS'] ?? $_GET['code'] ?? 404;
$status = (int)$status;

// Validate code and set messages
$error_titles = [
    400 => "Bad Request",
    401 => "Unauthorized Access",
    403 => "Access Forbidden",
    404 => "Page Not Found",
    500 => "Internal Server Error",
    503 => "Service Unavailable"
];

$error_descriptions = [
    400 => "The request could not be understood by the server due to malformed syntax.",
    401 => "You must authenticate to access this resource.",
    403 => "You do not have permission to access this directory or page on this server.",
    404 => "The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.",
    500 => "The server encountered an internal error or misconfiguration and was unable to complete your request.",
    503 => "The server is temporarily unable to service your request due to maintenance downtime or capacity problems."
];

$title = $error_titles[$status] ?? "Unexpected Error";
$description = $error_descriptions[$status] ?? "An unexpected error occurred while processing your request.";

// Set proper HTTP response code
http_response_code($status);

// Calculate base directory dynamically (subdirectory-safe)
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_dir = '/' . trim(str_replace('error.php', '', $script_name), '/') . '/';
$base_dir = ($base_dir === '//') ? '/' : $base_dir;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $status; ?> <?php echo htmlspecialchars($title); ?> — Faculty Portal</title>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Lora:ital,wght@0,400..700;1,400..700&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Share+Tech&display=swap" rel="stylesheet">

    <!-- Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/portal.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
        }

        .error-card {
            background: rgba(11, 21, 48, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 50px 40px;
            max-width: 550px;
            width: 100%;
            text-align: center;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            animation: card-appear 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--red-bright) 0%, var(--blue) 50%, var(--cyan) 100%);
            opacity: 0.8;
        }

        @keyframes card-appear {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .error-code {
            font-family: 'Share Tech', monospace;
            font-size: 110px;
            font-weight: 800;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, #ffedd5 0%, var(--red-bright) 50%, #7f1d1d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 40px rgba(231, 76, 60, 0.15);
            animation: pulse-code 3s ease-in-out infinite;
        }

        body.ce-active .error-code {
            background: linear-gradient(135deg, #e0f2fe, var(--blue) 50%, #1e3a8a);
            text-shadow: 0 0 40px rgba(37, 99, 235, 0.15);
        }

        @keyframes pulse-code {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.03);
            }
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(20px, 3.5vw, 28px);
            font-weight: 800;
            color: var(--white);
            margin-top: 10px;
            margin-bottom: 16px;
        }

        .error-desc {
            font-family: 'Lora', serif;
            font-size: 14.5px;
            line-height: 1.8;
            color: var(--text-muted);
            margin-bottom: 34px;
        }

        .error-home-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--red-bright) 0%, var(--red) 60%, #7f1d1d 100%);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: var(--radius-xs);
            font-family: 'Merriweather Sans', sans-serif;
            font-weight: 700;
            font-size: 14.5px;
            transition: var(--transition);
            box-shadow: 0 6px 20px var(--red-glow);
            width: fit-content;
            margin: 0 auto;
        }

        body.ce-active .error-home-btn {
            background: linear-gradient(135deg, #3b82f6 0%, var(--blue) 60%, #1e3a8a);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .error-home-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 28px var(--red-glow);
            color: white;
        }

        body.ce-active .error-home-btn:hover {
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.5);
        }

        .error-home-btn svg {
            transition: transform 0.3s;
        }

        .error-home-btn:hover svg {
            transform: translateX(-4px);
        }
    </style>
</head>
<body class="<?php echo (isset($_COOKIE['portal_dept']) && $_COOKIE['portal_dept'] === 'CE') ? 'ce-active' : ''; ?>">

    <!-- Background particles -->
    <div class="particles" aria-hidden="true">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Glowing Orbs -->
    <div class="orb orb-1" aria-hidden="true"></div>
    <div class="orb orb-2" aria-hidden="true"></div>
    <div class="orb orb-3" aria-hidden="true"></div>

    <div class="error-container">
        <div class="error-card">
            <h1 class="error-code"><?php echo $status; ?></h1>
            <h2 class="error-title"><?php echo htmlspecialchars($title); ?></h2>
            <p class="error-desc"><?php echo htmlspecialchars($description); ?></p>
            
            <a href="<?php echo htmlspecialchars($base_dir); ?>" class="error-home-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                <span>Back to Portal Home</span>
            </a>
        </div>
    </div>

    <script>
        // Adjust background particles/orbs based on local storage department selection
        const savedDept = localStorage.getItem('portal_dept');
        if (savedDept === 'CE') {
            document.body.classList.add('ce-active');
        } else {
            document.body.classList.remove('ce-active');
        }
    </script>
</body>
</html>
