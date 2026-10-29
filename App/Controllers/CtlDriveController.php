<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class CtlDriveController extends Controller
{
    /**
     * Render the Drive Folder Scanner & Syllabus Repository (Protected)
     */
    public function index(): void
    {
        $this->view('ctldrive.index');
    }
}
