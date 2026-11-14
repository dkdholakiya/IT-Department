<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class ZeroStudentController extends Controller
{
    private string $jsonFile;

    public function __construct()
    {
        $dir = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/uploads/zero_student';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $this->jsonFile = $dir . '/records.json';
        if (!file_exists($this->jsonFile)) {
            file_put_contents($this->jsonFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Render the Zero Student Incident Directory List (Index View)
     */
    public function index(): void
    {
        Auth::requirePermission('zero_student.view', 'Zero Student Directory');
        $records = $this->getRecordsData();
        $user = Auth::user();
        if (!Auth::canViewAllFaculty()) {
            $records = $this->filterRecordsForUser($records, $user);
        }
        $this->view('zero-student.index', ['records' => $records]);
    }

    /**
     * Render the Zero Student Create Form
     */
    public function create(): void
    {
        Auth::requirePermission('zero_student.create', 'Create Zero Student Record');
        $this->view('zero-student.create');
    }

    /**
     * API Endpoint: Get, Add (Single or Batch), or Delete Zero Student records
     */
    public function records(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'OPTIONS') {
            http_response_code(200);
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
            exit;
        }

        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input && !empty($_POST)) {
                $input = $_POST;
            }

            if (isset($input['batch']) && is_array($input['batch'])) {
                Auth::requirePermission('zero_student.import', 'Batch Import Zero Student Records');
            } else {
                Auth::requirePermission('zero_student.create', 'Create Zero Student Record');
            }

            $records = $this->getRecordsData();

            // Support batch import (array of items) or single record
            if (isset($input['batch']) && is_array($input['batch'])) {
                foreach ($input['batch'] as $item) {
                    $item['id'] = $item['id'] ?? ('zs_' . time() . '_' . bin2hex(random_bytes(3)));
                    $item['createdAt'] = $item['createdAt'] ?? date('c');
                    array_unshift($records, $item);
                }
            } elseif (!empty($input)) {
                $input['id'] = $input['id'] ?? ('zs_' . time() . '_' . bin2hex(random_bytes(3)));
                $input['createdAt'] = $input['createdAt'] ?? date('c');
                array_unshift($records, $input);
            }

            file_put_contents($this->jsonFile, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->json([
                'success' => true,
                'total' => count($records),
                'record' => $input ?? null
            ]);
            return;
        }

        if ($method === 'DELETE') {
            Auth::requirePermission('zero_student.delete', 'Delete Zero Student Record');
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
            }
            if ($id) {
                $records = array_values(array_filter($this->getRecordsData(), fn($r) => ($r['id'] ?? '') !== $id));
                file_put_contents($this->jsonFile, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $this->json(['success' => true, 'remaining' => count($records)]);
                return;
            }
            $this->json(['success' => false, 'error' => 'Missing record ID for deletion'], 400);
            return;
        }

        // Default GET: Return records (filtered for non-admin)
        Auth::requirePermission('zero_student.view', 'Zero Student Directory');
        $records = $this->getRecordsData();
        $user = Auth::user();
        if (!Auth::canViewAllFaculty()) {
            $records = $this->filterRecordsForUser($records, $user);
        }
        $this->json([
            'success' => true,
            'count' => count($records),
            'records' => $records
        ]);
    }

    /**
     * Filter incident records strictly to the authenticated faculty user
     */
    private function filterRecordsForUser(array $records, ?array $user): array
    {
        if (!$user) {
            return [];
        }
        $userInitials = strtoupper(trim($user['initials'] ?? ''));
        $userEmail    = strtolower(trim($user['email'] ?? ''));
        $userName     = strtolower(trim($user['name'] ?? ''));

        return array_values(array_filter($records, function ($r) use ($userInitials, $userEmail, $userName) {
            $rInitials = strtoupper(trim($r['facultyInitials'] ?? ''));
            $rEmail    = strtolower(trim($r['facultyEmail'] ?? ''));
            $rName     = strtolower(trim($r['facultyName'] ?? ''));

            if ($userInitials !== '' && $rInitials === $userInitials) {
                return true;
            }
            if ($userEmail !== '' && $rEmail === $userEmail) {
                return true;
            }
            if ($userName !== '' && $rName === $userName) {
                return true;
            }
            return false;
        }));
    }

    /**
     * Helper to read records safely from JSON file
     */
    private function getRecordsData(): array
    {
        if (file_exists($this->jsonFile)) {
            $content = file_get_contents($this->jsonFile);
            $data = json_decode($content, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }
}
