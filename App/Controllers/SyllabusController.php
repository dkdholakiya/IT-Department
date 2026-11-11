<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class SyllabusController extends Controller
{
    /**
     * Render the Syllabus & Previous Question Papers repository
     */
    public function index(): void
    {
        Auth::requirePermission('syllabus.view', 'Syllabus & Exam Papers');
        $this->view('syllabus-papers.index');
    }
}
