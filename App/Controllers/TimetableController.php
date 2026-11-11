<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class TimetableController extends Controller
{
    /**
     * Faculty Timetable Viewer
     */
    public function faculty(): void
    {
        Auth::requirePermission('timetable.view', 'Faculty Timetable');
        $this->view('timetable.index');
    }

    /**
     * Student Timetable Viewer & Uploader
     */
    public function student(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::requirePermission('student_timetable.upload', 'Student Timetable Upload');
        } else {
            Auth::requirePermission('student_timetable.view', 'Student Timetable');
        }
        $studentUploadDir = BASE_PATH . '/uploads/student_timetable/';
        if (!is_dir($studentUploadDir)) {
            @mkdir($studentUploadDir, 0777, true);
        }

        $parserFile = APP_PATH . '/Services/student-timetable-parser.php';
        if (file_exists($parserFile)) {
            require_once $parserFile;
        }

        $uploadMessage = '';
        $uploadSuccess = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['timetable_file'])) {
            $file = $_FILES['timetable_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, ['xlsx', 'xls'])) {
                $oldFiles = glob($studentUploadDir . '*.{xlsx,xls}', GLOB_BRACE);
                if ($oldFiles) {
                    foreach ($oldFiles as $oldFile) {
                        @unlink($oldFile);
                    }
                }
                @unlink($studentUploadDir . 'student_timetable_cache.json');

                $targetPath = $studentUploadDir . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $uploadSuccess = true;
                    $uploadMessage = "File uploaded successfully!";
                    if (function_exists('parseExcelToTtCache')) {
                        parseExcelToTtCache($targetPath, $studentUploadDir . 'student_timetable_cache.json');
                    }
                } else {
                    $uploadMessage = "Failed to move uploaded file.";
                }
            } else {
                $uploadMessage = "Invalid file type. Please upload an Excel (.xlsx or .xls) file.";
            }
        }

        $this->view('timetable.student', [
            'uploadMessage' => $uploadMessage,
            'uploadSuccess' => $uploadSuccess
        ]);
    }

    /**
     * Faculty Workload Summary Dashboard
     */
    public function workloadSummary(): void
    {
        Auth::requirePermission('workload.view', 'Faculty Workload Audit');
        $this->view('timetable.workload');
    }

    /**
     * Timetable Sync & Compiler Status Page
     */
    public function updateTimetable(): void
    {
        Auth::requirePermission('timetable.update', 'Timetable Compiler Status');
        $this->view('timetable.status');
    }
}
