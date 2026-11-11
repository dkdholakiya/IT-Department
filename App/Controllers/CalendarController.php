<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class CalendarController extends Controller
{
    /**
     * Render the Academic Calendar & Events page
     */
    public function index(): void
    {
        Auth::requirePermission('calendar.view', 'Academic Calendar');
        $this->view('calendar.index');
    }
}
