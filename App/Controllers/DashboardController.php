<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Services\DashboardDataService;

class DashboardController extends Controller
{
    /**
     * Render the Smart Management Dashboard (Protected)
     */
    public function index(): void
    {
        Auth::requirePermission('dashboard.view', 'Smart Management Dashboard');
        
        $currentUser = Auth::user();
        $dashboardData = DashboardDataService::getDashboardMetrics($currentUser);

        $this->view('dashboard.index', [
            'dashboardData' => $dashboardData,
            'currentUser'   => $currentUser
        ]);
    }

    /**
     * JSON API Endpoint: Get dynamic dashboard metrics
     * Endpoint: GET /api/dashboard/metrics
     */
    public function apiMetrics(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            exit(0);
        }

        if (!Auth::isAuthenticated()) {
            $this->json(['success' => false, 'error' => 'Authentication required.'], 401);
            return;
        }

        $currentUser = Auth::user();
        $roleOverride = !empty($_GET['role']) ? trim($_GET['role']) : null;
        $facultyIdOverride = !empty($_GET['faculty_id']) ? trim($_GET['faculty_id']) : null;

        $metrics = DashboardDataService::getDashboardMetrics($currentUser, $roleOverride, $facultyIdOverride);

        $this->json([
            'success' => true,
            'data'    => $metrics
        ]);
    }
}
