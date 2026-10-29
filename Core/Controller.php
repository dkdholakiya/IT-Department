<?php
namespace Core;

use RuntimeException;

/**
 * Base Controller for IT Department Portal
 */
abstract class Controller
{
    /**
     * Render a view file with extracted data.
     * Maps dot notation to Views subfolder and index files.
     * E.g. 'home.index' => App/Views/home/index.php
     *      'timetable.index' => App/Views/timetable/index.php
     *      'report.index' => App/Views/report/index.php
     *
     * @param string $view Dot notation view path
     * @param array $data Variables to pass to the view
     */
    protected function view(string $view, array $data = []): void
    {
        $relativePath = str_replace('.', DIRECTORY_SEPARATOR, $view);
        $viewPath = rtrim(APP_PATH, '/\\') . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . $relativePath . '.php';

        if (!file_exists($viewPath)) {
            throw new RuntimeException("View [$view] not found at [$viewPath].");
        }

        // Make data available inside the view
        extract($data);

        require $viewPath;
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     */
    protected function json(mixed $data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        foreach ($headers as $name => $value) {
            header("$name: $value");
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a route or absolute URL.
     *
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        if (preg_match('/^https?:\/\//i', $url)) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . base_url($url));
        }
        exit;
    }

    /**
     * Redirect back to referer or fallback.
     *
     * @param string $fallback
     */
    protected function redirectBack(string $fallback = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        if ($referer && strpos($referer, BASE_URL) !== false) {
            header('Location: ' . $referer);
            exit;
        }
        $this->redirect($fallback);
    }
}
