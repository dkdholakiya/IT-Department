<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class ReportController extends Controller
{
    private string $jsonFile;

    public function __construct()
    {
        $dir = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2)) . '/uploads/reports';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $this->jsonFile = $dir . '/records.json';
        if (!file_exists($this->jsonFile)) {
            file_put_contents($this->jsonFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    /**
     * Render the Report Management Directory List (Index View)
     */
    public function index(): void
    {
        Auth::requirePermission('report.view', 'Workload & Activity Reports');
        $records = $this->getRecordsData();
        $user = Auth::user();
        if (!Auth::canViewAllFaculty()) {
            $records = $this->filterRecordsForUser($records, $user);
        }
        $this->view('report.index', [
            'records'          => $records,
            'canViewAll'       => Auth::canViewAllFaculty(),
            'currentUser'      => $user,
            'canCreateReport'  => Auth::can('report.create'),
            'canEditReport'    => Auth::can('report.edit'),
            'canDeleteReport'  => Auth::can('report.delete'),
            'canExportReport'  => Auth::can('report.export')
        ]);
    }

    /**
     * Render the Report Create or Edit Form
     */
    public function create(): void
    {
        $id = $_GET['id'] ?? null;
        $record = null;
        $isEdit = false;

        if ($id) {
            Auth::requirePermission('report.edit', 'Edit Workload Report');
            $record = $this->findRecordById($id);
            if (!$record) {
                $redirectUrl = function_exists('base_url') ? base_url('/report') : '/report';
                header("Location: {$redirectUrl}");
                exit;
            }

            // Verify ownership if non-admin
            if (!Auth::canViewAllFaculty() && !$this->isRecordOwnedByUser($record, Auth::user())) {
                http_response_code(403);
                header('Content-Type: text/html; charset=utf-8');
                echo "<h1>403 Forbidden</h1><p>You do not have permission to edit this report because it belongs to another faculty member.</p>";
                exit;
            }

            $isEdit = true;
        } else {
            Auth::requirePermission('report.create', 'Create Workload Report');
        }

        $this->view('report.create', [
            'record'           => $record,
            'isEdit'           => $isEdit,
            'canChangeFaculty' => Auth::canViewAllFaculty(),
            'currentUser'      => Auth::user()
        ]);
    }

    /**
     * API Endpoint: Get, Create, Update, or Delete Report records
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

        // ── DELETE: Delete Record (Supports standard HTTP DELETE and POST with action=delete) ──
        $isDelete = ($method === 'DELETE');
        $input = null;

        if ($method === 'POST') {
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            if (!$input && !empty($_POST)) {
                $input = $_POST;
            }

            if (
                ($_GET['action'] ?? '') === 'delete' ||
                (isset($input['action']) && $input['action'] === 'delete') ||
                (isset($input['_method']) && strtoupper($input['_method']) === 'DELETE')
            ) {
                $isDelete = true;
            }
        }

        if ($isDelete) {
            Auth::requirePermission('report.delete', 'Delete Workload Report');

            $id = $_GET['id'] ?? null;
            if (!$id && isset($input['id'])) {
                $id = $input['id'];
            }
            if (!$id && $method === 'DELETE') {
                $rawInput = file_get_contents('php://input');
                $parsed = json_decode($rawInput, true);
                $id = $parsed['id'] ?? null;
            }

            if (!$id) {
                $this->json(['success' => false, 'error' => 'Missing record ID for deletion'], 400);
                return;
            }

            $record = $this->findRecordById($id);
            if (!$record) {
                $this->json(['success' => false, 'error' => 'Report record not found'], 404);
                return;
            }

            // Ownership check for non-admin
            if (!Auth::canViewAllFaculty() && !$this->isRecordOwnedByUser($record, Auth::user())) {
                $this->json([
                    'success' => false,
                    'error'   => 'Access Denied: You cannot delete a report filed by another faculty member.'
                ], 403);
                return;
            }

            $records = array_values(array_filter($this->getRecordsData(), fn($r) => ($r['id'] ?? '') !== $id));
            $this->saveRecordsSafely($records);

            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }

            $this->json([
                'success'   => true,
                'deletedId' => $id,
                'remaining' => count($records)
            ]);
            return;
        }

        // ── POST: Create or Update Record ──
        if ($method === 'POST') {
            if ($input === null) {
                $rawInput = file_get_contents('php://input');
                $input = json_decode($rawInput, true);
                if (!$input && !empty($_POST)) {
                    $input = $_POST;
                }
            }

            if (empty($input) || !is_array($input)) {
                $this->json(['success' => false, 'error' => 'Invalid or empty report payload'], 400);
                return;
            }

            $records = $this->getRecordsData();
            $targetId = $input['id'] ?? null;
            $existingIndex = -1;

            if ($targetId) {
                foreach ($records as $idx => $r) {
                    if (($r['id'] ?? '') === $targetId) {
                        $existingIndex = $idx;
                        break;
                    }
                }
            }

            // UPDATE MODE
            if ($existingIndex >= 0) {
                Auth::requirePermission('report.edit', 'Edit Workload Report');
                $existingRecord = $records[$existingIndex];

                if (!Auth::canViewAllFaculty() && !$this->isRecordOwnedByUser($existingRecord, Auth::user())) {
                    $this->json([
                        'success' => false,
                        'error'   => 'Access Denied: You do not have permission to modify another faculty member\'s report.'
                    ], 403);
                    return;
                }

                // Preserve original ID, createdAt, and createdBy
                $input['id'] = $existingRecord['id'];
                $input['createdAt'] = $existingRecord['createdAt'] ?? date('c');
                $input['createdBy'] = $existingRecord['createdBy'] ?? $this->formatUserAuditData(Auth::user());
                $input['updatedAt'] = date('c');
                $input['updatedBy'] = $this->formatUserAuditData(Auth::user());

                $records[$existingIndex] = $input;

                $this->saveRecordsSafely($records);

                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }

                $this->json([
                    'success' => true,
                    'action'  => 'updated',
                    'record'  => $input
                ]);
                return;
            }

            // CREATE MODE
            Auth::requirePermission('report.create', 'Create Workload Report');

            $input['id'] = 'rep_' . time() . '_' . bin2hex(random_bytes(3));
            $input['createdAt'] = date('c');
            $input['createdBy'] = $this->formatUserAuditData(Auth::user());

            array_unshift($records, $input);

            $this->saveRecordsSafely($records);

            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }

            $this->json([
                'success' => true,
                'action'  => 'created',
                'total'   => count($records),
                'record'  => $input
            ]);
            return;
        }

        // ── GET: Read records or single record ──
        Auth::requirePermission('report.view', 'Workload & Activity Reports');

        $singleId = $_GET['id'] ?? null;
        if ($singleId) {
            $record = $this->findRecordById($singleId);
            if (!$record) {
                $this->json(['success' => false, 'error' => 'Report record not found'], 404);
                return;
            }

            if (!Auth::canViewAllFaculty() && !$this->isRecordOwnedByUser($record, Auth::user())) {
                $this->json([
                    'success' => false,
                    'error'   => 'Access Denied: You cannot view this report.'
                ], 403);
                return;
            }

            $this->json(['success' => true, 'record' => $record]);
            return;
        }

        $records = $this->getRecordsData();
        $user = Auth::user();
        if (!Auth::canViewAllFaculty()) {
            $records = $this->filterRecordsForUser($records, $user);
        }

        $this->json([
            'success' => true,
            'count'   => count($records),
            'records' => $records
        ]);
    }

    /**
     * Find single record by ID
     */
    private function findRecordById(string $id): ?array
    {
        $records = $this->getRecordsData();
        foreach ($records as $r) {
            if (($r['id'] ?? '') === $id) {
                return $r;
            }
        }
        return null;
    }

    /**
     * Filter report records strictly to the authenticated faculty user
     */
    private function filterRecordsForUser(array $records, ?array $user): array
    {
        if (!$user) {
            return [];
        }

        return array_values(array_filter($records, fn($r) => $this->isRecordOwnedByUser($r, $user)));
    }

    /**
     * Check if a report record belongs to the specified user
     */
    private function isRecordOwnedByUser(array $record, ?array $user): bool
    {
        if (!$user) {
            return false;
        }

        $userId       = trim($user['id'] ?? '');
        $userEmail    = strtolower(trim($user['email'] ?? ''));
        $userInitials = strtoupper(trim($user['initials'] ?? ''));
        $userName     = strtolower(trim($user['name'] ?? ''));
        $userEmpId    = strtolower(trim($user['empId'] ?? ''));

        // 1. Check createdBy audit fields
        $createdById    = trim($record['createdBy']['id'] ?? '');
        $createdByEmail = strtolower(trim($record['createdBy']['email'] ?? ''));
        $createdByInit  = strtoupper(trim($record['createdBy']['initials'] ?? ''));

        if ($userId !== '' && $createdById === $userId) return true;
        if ($userEmail !== '' && $createdByEmail === $userEmail) return true;
        if ($userInitials !== '' && $createdByInit === $userInitials) return true;

        // 2. Check direct faculty fields (nested or flat)
        $rFacultyEmail = strtolower(trim($record['faculty']['email'] ?? $record['facultyEmail'] ?? ''));
        $rFacultyEmpId = strtolower(trim($record['faculty']['empId'] ?? $record['facultyEmpId'] ?? ''));
        $rFacultyInit  = strtoupper(trim($record['faculty']['initials'] ?? $record['facultyInitials'] ?? ''));
        $rFacultyName  = strtolower(trim($record['faculty']['name'] ?? $record['facultySearch'] ?? $record['facultyName'] ?? ''));

        if ($userEmail !== '' && $rFacultyEmail === $userEmail) return true;
        if ($userEmpId !== '' && $rFacultyEmpId === $userEmpId) return true;
        if ($userInitials !== '' && $rFacultyInit !== '' && $rFacultyInit === $userInitials) return true;
        if ($userName !== '' && $rFacultyName !== '' && (str_contains($rFacultyName, $userName) || str_contains($userName, $rFacultyName))) return true;

        return false;
    }

    /**
     * Helper to package audit user info
     */
    private function formatUserAuditData(?array $user): array
    {
        return [
            'id'          => $user['id'] ?? '',
            'username'    => $user['username'] ?? '',
            'name'        => $user['name'] ?? '',
            'email'       => strtolower(trim($user['email'] ?? '')),
            'initials'    => strtoupper(trim($user['initials'] ?? '')),
            'department'  => $user['department'] ?? '',
            'designation' => $user['designation'] ?? '',
            'role'        => Auth::userRole()
        ];
    }

    /**
     * Read records safely from JSON file
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

    /**
     * Save records safely using file locking (LOCK_EX)
     */
    private function saveRecordsSafely(array $records): bool
    {
        $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return (bool)file_put_contents($this->jsonFile, $json, LOCK_EX);
    }
}
