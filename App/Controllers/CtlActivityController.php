<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class CtlActivityController extends Controller
{
    /**
     * Render the Continuous Teaching & Learning (CTL) Activity Dashboard (Protected)
     */
    public function index(): void
    {
        $this->view('ctlactivity.index');
    }
}
