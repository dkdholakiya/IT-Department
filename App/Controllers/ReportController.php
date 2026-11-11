<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class ReportController extends Controller
{
    /**
     * Render the Daily Faculty Workload & Lecture Activity Report form
     */
    public function index(): void
    {
        Auth::requirePermission('report.view', 'Workload Reports');
        $this->view('report.index');
    }
}
