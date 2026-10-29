<?php
namespace App\Controllers;

use Core\Controller;

class FacultyController extends Controller
{
    /**
     * Render the Faculty Directory page
     */
    public function index(): void
    {
        $this->view('faculty.index');
    }
}
