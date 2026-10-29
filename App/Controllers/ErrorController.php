<?php
namespace App\Controllers;

use Core\Controller;

class ErrorController extends Controller
{
    /**
     * 404 Not Found Error
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors.404', [
            'status' => 404,
            'title' => 'Page Not Found',
            'description' => 'The requested page or route could not be found on this server.'
        ]);
    }

    /**
     * 405 Method Not Allowed Error
     */
    public function methodNotAllowed(): void
    {
        http_response_code(405);
        $this->view('errors.405', [
            'status' => 405,
            'title' => 'Method Not Allowed',
            'description' => 'The HTTP method used is not supported for this route.'
        ]);
    }

    /**
     * 500 Internal Server Error
     *
     * @param string $message
     */
    public function serverError(string $message = ''): void
    {
        http_response_code(500);
        $this->view('errors.500', [
            'status' => 500,
            'title' => 'Internal Server Error',
            'description' => $message ?: 'An unexpected error occurred while processing your request.'
        ]);
    }

    /**
     * Dynamic Error Page handler for /error route
     *
     * @param array $vars
     */
    public function show(array $vars = []): void
    {
        $code = (int)($_GET['code'] ?? $_SERVER['REDIRECT_STATUS'] ?? 404);
        if ($code < 400 || $code > 599) {
            $code = 404;
        }
        http_response_code($code);

        $error_titles = [
            400 => "Bad Request",
            401 => "Unauthorized Access",
            403 => "Access Forbidden",
            404 => "Page Not Found",
            500 => "Internal Server Error",
            503 => "Service Unavailable"
        ];

        $title = $error_titles[$code] ?? "Unexpected Error";
        $this->view('errors.error', [
            'status' => $code,
            'title' => $title
        ]);
    }
}
