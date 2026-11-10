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
$r->addRoute(['GET', 'OPTIONS'], '/api/faculty', 'FacultyController@apiList');
$r->addRoute(['GET', 'OPTIONS'], '/api/faculty.php', 'FacultyController@apiList');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/save', 'FacultyController@apiSave');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/save.php', 'FacultyController@apiSave');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/toggle-status', 'FacultyController@apiToggleStatus');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/toggle-status.php', 'FacultyController@apiToggleStatus');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/delete', 'FacultyController@apiDelete');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/delete.php', 'FacultyController@apiDelete');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/send-credentials', 'FacultyController@apiSendCredentials');
$r->addRoute(['POST', 'OPTIONS'], '/api/faculty/send-credentials.php', 'FacultyController@apiSendCredentials');

$r->addRoute('GET', '/calendar', 'CalendarController@index');
$r->addRoute('GET', '/calendar.php', 'CalendarController@index');

$r->addRoute('GET', '/report', 'ReportController@index');
$r->addRoute('GET', '/report.php', 'ReportController@index');

$r->addRoute('GET', '/syllabus-papers', 'SyllabusController@index');
$r->addRoute('GET', '/syllabus-papers.php', 'SyllabusController@index');

// ── Smart Management Dashboard ──
$r->addRoute('GET', '/dashboard', 'DashboardController@index');
$r->addRoute('GET', '/dashboard.php', 'DashboardController@index');

// ── Protected Pages ──
$r->addRoute('GET', '/zero-student-report', 'ZeroStudentController@index');
$r->addRoute('GET', '/zero-student-report.php', 'ZeroStudentController@index');
$r->addRoute(['GET', 'POST'], '/zero-student-create', 'ZeroStudentController@create');
$r->addRoute(['GET', 'POST'], '/zero-student-create.php', 'ZeroStudentController@create');
$r->addRoute(['GET', 'POST', 'DELETE', 'OPTIONS'], '/api/zero-student-records', 'ZeroStudentController@records');
$r->addRoute(['GET', 'POST', 'DELETE', 'OPTIONS'], '/api/zero-student-records.php', 'ZeroStudentController@records');

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
$r->addRoute(['GET', 'POST'], '/login', 'AuthController@login');
$r->addRoute(['GET', 'POST'], '/login.php', 'AuthController@login');
$r->addRoute(['GET', 'POST'], '/signin', 'AuthController@login');
$r->addRoute(['GET', 'POST'], '/signin.php', 'AuthController@login');

$r->addRoute(['GET', 'POST'], '/logout', 'AuthController@logout');
$r->addRoute(['GET', 'POST'], '/logout.php', 'AuthController@logout');

$r->addRoute(['GET', 'POST'], '/signup', 'AuthController@signup');
$r->addRoute(['GET', 'POST'], '/signup.php', 'AuthController@signup');
$r->addRoute(['GET', 'POST'], '/register', 'AuthController@signup');
$r->addRoute(['GET', 'POST'], '/register.php', 'AuthController@signup');

$r->addRoute(['GET', 'POST'], '/otp', 'AuthController@otp');
$r->addRoute(['GET', 'POST'], '/otp.php', 'AuthController@otp');
$r->addRoute(['GET', 'POST'], '/verify-otp', 'AuthController@otp');
$r->addRoute(['GET', 'POST'], '/verify-otp.php', 'AuthController@otp');

// Dedicated JSON Auth API endpoints
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/login-request', 'AuthController@loginRequest');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/login-request.php', 'AuthController@loginRequest');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/verify-otp', 'AuthController@verifyLoginOtp');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/verify-otp.php', 'AuthController@verifyLoginOtp');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/resend-otp', 'AuthController@resendLoginOtp');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/resend-otp.php', 'AuthController@resendLoginOtp');
$r->addRoute(['GET', 'OPTIONS'], '/api/auth/check-status', 'AuthController@checkAuthStatus');
$r->addRoute(['GET', 'OPTIONS'], '/api/auth/check-status.php', 'AuthController@checkAuthStatus');
$r->addRoute(['GET', 'OPTIONS'], '/api/auth/captcha', 'AuthController@generateCaptcha');
$r->addRoute(['GET', 'OPTIONS'], '/api/auth/captcha.php', 'AuthController@generateCaptcha');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/forgot-password', 'AuthController@forgotPassword');
$r->addRoute(['POST', 'OPTIONS'], '/api/auth/forgot-password.php', 'AuthController@forgotPassword');

$r->addRoute(['GET', 'POST', 'OPTIONS'], '/verify-password', 'AuthController@loginRequest');
$r->addRoute(['GET', 'POST', 'OPTIONS'], '/verify-password.php', 'AuthController@loginRequest');
$r->addRoute(['POST', 'OPTIONS'], '/register-account', 'AuthController@registerAccount');
$r->addRoute(['POST', 'OPTIONS'], '/register-account.php', 'AuthController@registerAccount');

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
