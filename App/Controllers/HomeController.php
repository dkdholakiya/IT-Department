<?php
namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    /**
     * Render the main departmental portal landing page
     */
    public function index(): void
    {
        $this->view('home.index');
    }
}
