<?php
/**
 * GMIU CE & IT Department Portal — Front Controller (FastRoute)
 */

define('BASE_PATH', __DIR__);

// 1. Load Composer Autoloader
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    http_response_code(500);
    exit("Server Error: Composer autoloader not found. Please run composer install.");
}
require_once $autoloader;

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

// 5. Dispatch Route
try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $errorController = new App\Controllers\ErrorController();
            $errorController->notFound();
            break;

        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
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
    $errorController = new App\Controllers\ErrorController();
    $errorController->serverError($e->getMessage());
}