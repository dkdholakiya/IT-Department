<?php
namespace App\Controllers;

use Core\Controller;

class ApiController extends Controller
{
    /**
     * 6-Digit Email OTP Service (Generate & Verify)
     */
    public function otpService(): void
    {
        $serviceFile = APP_PATH . '/Services/otp-service.php';
        if (file_exists($serviceFile)) {
            require $serviceFile;
            exit;
        }
        $this->json(['success' => false, 'error' => 'OTP Service not available.'], 500);
    }

    /**
     * Send Email Service via Gmail SMTP Socket
     */
    public function sendEmail(): void
    {
        $serviceFile = APP_PATH . '/Services/send-email.php';
        if (file_exists($serviceFile)) {
            require $serviceFile;
            exit;
        }
        $this->json(['success' => false, 'error' => 'Email Service not available.'], 500);
    }

    /**
     * Google Sheets Proxy Service with Anti-Duplication
     */
    public function proxySheets(): void
    {
        $serviceFile = APP_PATH . '/Services/proxy-sheets.php';
        if (file_exists($serviceFile)) {
            require $serviceFile;
            exit;
        }
        $this->json(['success' => false, 'error' => 'Sheets Proxy Service not available.'], 500);
    }

    /**
     * Background Google Sheets Timetable Live Sync Handler
     */
    public function syncTimetables(): void
    {
        $serviceFile = APP_PATH . '/Services/sync-timetables.php';
        if (file_exists($serviceFile)) {
            require $serviceFile;
            exit;
        }
        $this->json(['success' => false, 'error' => 'Sync Service not available.'], 500);
    }

    /**
     * Real-time Syllabus & Previous Exam Papers Web Scraper
     */
    public function scrape(): void
    {
        $serviceFile = APP_PATH . '/Services/scrape.php';
        if (file_exists($serviceFile)) {
            require $serviceFile;
            exit;
        }
        $this->json(['success' => false, 'error' => 'Scraper Service not available.'], 500);
    }
}
