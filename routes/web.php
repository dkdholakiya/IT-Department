<?php
/**
 * FastRoute Web Routes Definition
 * Maps HTTP request methods and URI endpoints to Controller actions.
 *
 * @var FastRoute\RouteCollector $r
 */

// ── Front Pages ──
$r->addRoute('GET', '/', 'HomeController@index');
$r->addRoute('GET', '/index', 'HomeController@index');
$r->addRoute('GET', '/index.php', 'HomeController@index');

$r->addRoute('GET', '/faculty', 'FacultyController@index');
$r->addRoute('GET', '/faculty.php', 'FacultyController@index');

$r->addRoute('GET', '/calendar', 'CalendarController@index');
$r->addRoute('GET', '/calendar.php', 'CalendarController@index');

$r->addRoute('GET', '/report', 'ReportController@index');
$r->addRoute('GET', '/report.php', 'ReportController@index');

$r->addRoute('GET', '/syllabus-papers', 'SyllabusController@index');
$r->addRoute('GET', '/syllabus-papers.php', 'SyllabusController@index');

// ── Protected Pages ──
$r->addRoute('GET', '/zero-student-report', 'ZeroStudentController@index');
$r->addRoute('GET', '/zero-student-report.php', 'ZeroStudentController@index');

$r->addRoute('GET', '/ctlactivity', 'CtlActivityController@index');
$r->addRoute('GET', '/ctlactivity.php', 'CtlActivityController@index');

$r->addRoute('GET', '/ctldrive', 'CtlDriveController@index');
$r->addRoute('GET', '/ctldrive.php', 'CtlDriveController@index');

// ── Timetable Module ──
$r->addRoute('GET', '/timetable', 'TimetableController@faculty');
$r->addRoute('GET', '/timetable.php', 'TimetableController@faculty');

$r->addRoute(['GET', 'POST'], '/student-timetable', 'TimetableController@student');
$r->addRoute(['GET', 'POST'], '/student-timetable.php', 'TimetableController@student');

$r->addRoute('GET', '/workload-summary', 'TimetableController@workloadSummary');
$r->addRoute('GET', '/workload-summary.php', 'TimetableController@workloadSummary');

$r->addRoute('GET', '/update-timetable', 'TimetableController@updateTimetable');
$r->addRoute('GET', '/update-timetable.php', 'TimetableController@updateTimetable');

// ── Authentication & Password Check ──
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/verify-password', 'AuthController@verifyPassword');
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/verify-password.php', 'AuthController@verifyPassword');

// ── Backend API & Services ──
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/otp-service', 'ApiController@otpService');
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/otp-service.php', 'ApiController@otpService');

$r->addRoute(['POST', 'OPTIONS'], '/send-email', 'ApiController@sendEmail');
$r->addRoute(['POST', 'OPTIONS'], '/send-email.php', 'ApiController@sendEmail');

$r->addRoute(['POST', 'OPTIONS'], '/proxy-sheets', 'ApiController@proxySheets');
$r->addRoute(['POST', 'OPTIONS'], '/proxy-sheets.php', 'ApiController@proxySheets');

$r->addRoute(['GET', 'POST'], '/sync-timetables', 'ApiController@syncTimetables');
$r->addRoute(['GET', 'POST'], '/sync-timetables.php', 'ApiController@syncTimetables');

$r->addRoute(['GET', 'POST', 'OPTIONS'], '/scrape', 'ApiController@scrape');
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/scrape.php', 'ApiController@scrape');

// ── Error Handling ──
$r->addRoute('GET', '/error', 'ErrorController@show');
$r->addRoute('GET', '/error.php', 'ErrorController@show');
