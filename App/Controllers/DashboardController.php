<?php
namespace App\Controllers;

use Core\Controller;

class DashboardController extends Controller
{
    /**
     * Render the Smart Management Dashboard
     */
    public function index(): void
    {
        $this->view('dashboard.index');
    }
}
