<?php
namespace App\Controllers;

use Core\Controller;

class CalendarController extends Controller
{
    /**
     * Render the Academic Calendar & Events page
     */
    public function index(): void
    {
        $this->view('calendar.index');
    }
}
