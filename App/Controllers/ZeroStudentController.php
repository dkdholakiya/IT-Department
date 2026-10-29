<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class ZeroStudentController extends Controller
{
    /**
     * Render the Zero Student Report form (Protected)
     */
    public function index(): void
    {
        $this->view('zero-student.index');
    }
}
