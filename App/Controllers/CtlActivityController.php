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
        Auth::check('CTL Activity Dashboard');
        $this->view('ctlactivity.index');
    }
}
