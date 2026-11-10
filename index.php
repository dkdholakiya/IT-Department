<?php
/**
 * GMIU CE & IT Department Portal — Front Controller (FastRoute)
 */

define('BASE_PATH', __DIR__);

// 1. Load Composer Autoloader with Resilient Self-Healing Fallback
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    // Fallback: If composer autoloader is missing, register self-healing PSR-4 autoloader
    $fastRouteSrc = BASE_PATH . '/vendor/nikic/fast-route/src';
    if (file_exists($fastRouteSrc . '/functions.php')) {
        require_once $fastRouteSrc . '/functions.php';
    }
    spl_autoload_register(function ($class) use ($fastRouteSrc) {
        if (strpos($class, 'FastRoute\\') === 0) {
            $file = $fastRouteSrc . '/' . str_replace('\\', '/', substr($class, 10)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        } elseif (strpos($class, 'Core\\') === 0) {
            $file = BASE_PATH . '/Core/' . str_replace('\\', '/', substr($class, 5)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        } elseif (strpos($class, 'App\\') === 0) {
            $file = BASE_PATH . '/App/' . str_replace('\\', '/', substr($class, 4)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}

// 2. Initialize Core Bootstrap
require_once BASE_PATH . '/Core/bootstrap.php';

// 3. Initialize FastRoute Dispatcher
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    require_once BASE_PATH . '/routes/web.php';
});

// 4. Extract HTTP Method and Clean URI
$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Strip query string (?foo=bar) and decode
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Strip the base subdirectory (e.g. /IT or /it) dynamically from the URI (case-insensitive)
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(dirname($scriptName), '/');

if (!empty($basePath) && $basePath !== '/' && $basePath !== '.') {
    if (stripos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
}
$uri = '/' . trim($uri, '/');
$uri = $uri ?: '/';

// Detect whether incoming request expects a JSON API response
$isApiRequest = (
    strpos($uri, '/api/') === 0 ||
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
    (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
);

// 5. Dispatch Route
try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            if ($isApiRequest) {
                while (ob_get_level()) { @ob_end_clean(); }
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => "API endpoint not found: [{$httpMethod} {$uri}]"], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $errorController = new App\Controllers\ErrorController();
            $errorController->notFound();
            break;

        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            if ($isApiRequest) {
                while (ob_get_level()) { @ob_end_clean(); }
                http_response_code(405);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => "Method not allowed for endpoint: [{$httpMethod} {$uri}]"], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $errorController = new App\Controllers\ErrorController();
            $errorController->methodNotAllowed();
            break;

        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars    = $routeInfo[2] ?? [];

            if (is_string($handler) && strpos($handler, '@') !== false) {
                [$controllerName, $method] = explode('@', $handler);
                $controllerClass = "App\\Controllers\\$controllerName";

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $method)) {
                        $controller->$method($vars);
                    } else {
                        throw new RuntimeException("Method [$method] not found on controller [$controllerClass].");
                    }
                } else {
                    throw new RuntimeException("Controller class [$controllerClass] not found.");
                }
            } elseif (is_callable($handler)) {
                call_user_func($handler, $vars);
            }
            break;
    }
} catch (\Throwable $e) {
    error_log("Application Execution Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    if ($isApiRequest) {
        while (ob_get_level()) { @ob_end_clean(); }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => "Server error: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $errorController = new App\Controllers\ErrorController();
    $errorController->serverError($e->getMessage());
}