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
        Auth::requirePermission('ctlactivity.view', 'CTL Activity');
        $this->view('ctlactivity.index');
    }
}
