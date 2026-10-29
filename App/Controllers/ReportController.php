<?php
namespace App\Controllers;

use Core\Controller;

class ReportController extends Controller
{
    /**
     * Render the Daily Faculty Workload & Lecture Activity Report form
     */
    public function index(): void
    {
        $this->view('report.index');
    }
}
