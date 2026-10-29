<?php
namespace App\Controllers;

use Core\Controller;

class SyllabusController extends Controller
{
    /**
     * Render the Syllabus & Previous Question Papers repository
     */
    public function index(): void
    {
        $this->view('syllabus-papers.index');
    }
}
