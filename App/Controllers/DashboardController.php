<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class DashboardController extends Controller
{
    /**
     * Render the Smart Management Dashboard (Protected)
     */
    public function index(): void
    {
        Auth::requirePermission('dashboard.view', 'Smart Management Dashboard');
        $this->view('dashboard.index');
    }
}
