# Migration Plan: IT Project to FastRoute with Database-Ready Architecture

## 1. Executive Summary & Audit Findings

### 1.1 Project Context
* **Current Working Branch**: `develop` (Strictly isolated from `main`; no deployments or pushes to `main`).
* **Target Project**: `c:\xampp\htdocs\IT` (Department of Information Technology & Computer Engineering Portal).
* **Reference Project (Read-Only)**: `C:\xampp\htdocs\HRM_2.0` (Inspected for architectural ideas; zero modifications made or planned).

---

## 2. Current IT Project Architecture

### 2.1 File & Directory Inventory
The current IT project is a flat PHP application running on Apache/XAMPP with extensionless URL rewriting in `.htaccess`:
* **Main Landing Page**: `index.php` (Direct view with IT/CE department switcher and 3D UI cards).
* **Core Views / Pages**:
  * `faculty.php` — Faculty directory & department staff profiles.
  * `calendar.php` — Academic calendar and departmental event scheduler.
  * `report.php` — Daily workload & lecture activity reporting form.
  * `zero-student-report.php` — Zero attendance reporting workflow (auth-protected).
  * `ctlactivity.php` — Continuous Teaching & Learning activity tracker (auth-protected).
  * `ctldrive.php` — Course syllabus & Google Drive repository scanner (auth-protected).
  * `syllabus-papers.php` — Previous year question papers and syllabus viewer.
  * `timetable.php` — Interactive faculty timetable grid.
  * `student-timetable.php` — Interactive student timetable viewer & Excel upload.
  * `workload-summary.php` — Faculty workload analytics & summary dashboard.
  * `update-timetable.php` — Status feedback page for timetable compilation.
  * `error.php` — Custom HTTP error page handler (400, 401, 403, 404, 500, 503).
* **Security & Auth**:
  * `auth-check.php` — Session guard requiring password authentication if enabled.
  * `auth-login-page.php` — Glassmorphism modal/page for unlocking protected pages.
  * `verify-password.php` — AJAX/POST endpoint validating bcrypt/plaintext password.
* **Backend Services & API Endpoints**:
  * `otp-service.php` — 6-digit email OTP generator & validator for report submission.
  * `send-email.php` — Direct SMTP email dispatching service via Gmail SSL port 465 with Google Sheets audit tracking.
  * `proxy-sheets.php` — Backend cURL proxy routing requests to Google Apps Script Web Apps with request deduplication.
  * `sync-timetables.php` — Live background Google Sheets synchronizer.
  * `student-timetable-parser.php` — OpenXML zip extractor and parser for student timetable.
  * `timetable-compiler.php` — Faculty timetable compiler outputting to `assets/js/timetableData.js`.
  * `scrape.php` — Real-time syllabus and paper scraper.
* **Partials & Shared Components**:
  * `auto-cache-bust.php` — Asset versioning helper (`v_asset()`).
  * `fab-nav.php` — Floating Action Button navigation.
  * `footer.php` — Department footer.
  * `theme-toggle.php` — Dark / Light theme toggle.
  * `config.php` — System configuration (SMTP keys, Google Sheets WebApp URLs, toggles).
* **Static Assets & Uploads**:
  * `assets/` — CSS, JavaScript, vendor libraries (Bootstrap 5, SheetJS, html2pdf), images.
  * `uploads/` — Timetable spreadsheets, JSON cache files, `.zs_dedup_cache.json`.
  * `scratch/` — Working folder for unzipping Excel archives.

---

## 3. Comparison with Reference Project (`HRM_2.0`)

| Dimension | HRM_2.0 (Reference) | Current IT Project | Proposed IT FastRoute Architecture |
| :--- | :--- | :--- | :--- |
| **Routing** | `nikic/fast-route: ^1.3` in `index.php` + `routes/web.php` | Apache `.htaccess` rewriting to `.php` files | `nikic/fast-route: ^1.3` + Subdirectory-aware dispatcher |
| **Structure** | MVC (`App/Controllers`, `App/Views`, `Core/`) | Flat root PHP files | Clean MVC (`App/Controllers`, `App/Views`, `Core/`) |
| **Autoloading** | Composer PSR-4 (`App\\`, `Core\\`) | None (`require_once` / `include`) | Composer PSR-4 (`App\\`, `Core\\`) |
| **Database** | Eager PDO connection in `Core/Model.php` constructor | None (Uses Google Sheets + local JSON caches) | **Lazy, Optional PDO**: No startup connection, safe fallbacks |
| **Views** | `$this->view('name', $data)` using layout wrapper | Embedded HTML/PHP files | `$this->view('name', $data)` with layout/noLayout options |
| **Config** | `config/database.php`, `Core/bootstrap.php` | `config.php` | `config/app.php`, `config/database.php` (DB-ready) |

### Key Architectural Differences & Adaptations:
1. **Lazy Database vs. Eager Database**:
   * In HRM_2.0, `Core\Model::__construct()` calls `$this->connect()` immediately. If MySQL is off, HRM_2.0 crashes.
   * In the IT project, we will **never connect eagerly**. `Core\Database` will only connect when a query or connection is explicitly requested. When no database credentials or service exist, all pages render normally without errors.
2. **Subdirectory Agnostic Dispatching**:
   * Both HRM_2.0 and IT run under subdirectories on localhost (`/HRM_2.0/` and `/IT/`).
   * We will adopt and refine the URL stripping logic:
     `$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');`
     `$uri = '/' . ltrim(substr($uri, strlen($basePath)), '/');`
3. **No Disruption of Existing Frontend/AJAX Logic**:
   * All frontend JS scripts fetch endpoints like `verify-password`, `otp-service`, `proxy-sheets`, `send-email`.
   * FastRoute routes will map both extensionless and `.php` endpoints (`/otp-service` and `/otp-service.php`) to avoid breaking any legacy or AJAX calls.

---

## 4. Environment & Compatibility Report

* **PHP Version**: `PHP 8.2.12 (ZTS Visual C++ 2019 x64)` located at `C:\xampp\php\php.exe`.
* **Composer**: `Composer 2.10.2` available at `C:\xampp\htdocs\ipo\composer.phar`.
* **FastRoute Compatibility**: `nikic/fast-route: ^1.3` is fully compatible with PHP 8.2.
* **Working Tree State**:
  * Uncommitted user modifications in `assets/js/timetableData.js` and `uploads/student_timetable/student_timetable_cache.json` are preserved.

---

## 5. Database Dependency Report

* **Current Database Calls**: `0` (Zero SQL queries, Zero PDO/MySQLi connections).
* **Current Persistence Storage**:
  1. Google Apps Script Web Apps (Google Sheets) for:
     * Faculty Workload Submissions (`sheets_webapp_url`)
     * Zero Student Attendance Logs (`zero_sheets_webapp_url`)
     * Mail Transmission Audit Logs (`mail_tracking_webapp_url`)
     * Timetable Auto-Sync (`student_timetable_webapp_url`, `faculty_timetable_webapp_url`)
  2. Local JSON file caches:
     * `uploads/student_timetable/student_timetable_cache.json`
     * `uploads/.zs_dedup_cache.json`
     * `assets/js/timetableData.js`
  3. Browser LocalStorage (for drafts, themes, department selection).
  4. PHP Session (`$_SESSION['authenticated']`, `$_SESSION['otp_store']`).
* **Conclusion**:
  * All features currently work 100% without a database server.
  * In the migration, we will introduce a **Database-Ready** layer (`Core\Database`, `Core\Model`, `config/database.php`) that stays completely idle and dormant until database features are introduced in future phases.

---

## 6. Route Inventory & Mapping

| Route URI | HTTP Methods | Current File | Target Controller & Action | Auth Protected? |
| :--- | :--- | :--- | :--- | :--- |
| `/` or `/index` | GET | `index.php` | `App\Controllers\HomeController@index` | No |
| `/faculty` | GET | `faculty.php` | `App\Controllers\FacultyController@index` | No |
| `/calendar` | GET | `calendar.php` | `App\Controllers\CalendarController@index` | No |
| `/report` | GET | `report.php` | `App\Controllers\ReportController@index` | No |
| `/zero-student-report` | GET | `zero-student-report.php` | `App\Controllers\ZeroStudentController@index` | Yes (`auth-check`) |
| `/ctlactivity` | GET | `ctlactivity.php` | `App\Controllers\CtlActivityController@index` | Yes (`auth-check`) |
| `/ctldrive` | GET | `ctldrive.php` | `App\Controllers\CtlDriveController@index` | Yes (`auth-check`) |
| `/syllabus-papers` | GET | `syllabus-papers.php` | `App\Controllers\SyllabusController@index` | No |
| `/timetable` | GET | `timetable.php` | `App\Controllers\TimetableController@faculty` | No |
| `/student-timetable` | GET, POST | `student-timetable.php` | `App\Controllers\TimetableController@student` | No |
| `/workload-summary` | GET | `workload-summary.php` | `App\Controllers\TimetableController@workloadSummary` | No |
| `/update-timetable` | GET | `update-timetable.php` | `App\Controllers\TimetableController@updateTimetable` | No |
| `/verify-password` | GET, POST, OPTIONS | `verify-password.php` | `App\Controllers\AuthController@verifyPassword` | No (Auth Handler) |
| `/otp-service` | GET, POST, OPTIONS | `otp-service.php` | `App\Controllers\ApiController@otpService` | No (API Endpoint) |
| `/send-email` | POST, OPTIONS | `send-email.php` | `App\Controllers\ApiController@sendEmail` | No (API Endpoint) |
| `/proxy-sheets` | POST, OPTIONS | `proxy-sheets.php` | `App\Controllers\ApiController@proxySheets` | Conditional (for `zero`) |
| `/sync-timetables` | GET, POST | `sync-timetables.php` | `App\Controllers\ApiController@syncTimetables` | Conditional |
| `/scrape` | GET, POST, OPTIONS | `scrape.php` | `App\Controllers\ApiController@scrape` | No (API Endpoint) |
| `/error` | GET | `error.php` | `App\Controllers\ErrorController@show` | No |

*Note: For maximum backwards compatibility, route aliases with `.php` (e.g. `/report.php`, `/verify-password.php`) will also be handled gracefully.*

---

## 7. Recommended Migration Steps (Phased Execution)

### Step 1: Composer & Autoloading Setup
* Create `composer.json` requiring `"nikic/fast-route": "^1.3"` and defining PSR-4 namespaces:
  ```json
  {
      "name": "gmiu/it-department-portal",
      "description": "GMIU CE & IT Department Faculty Portal with FastRoute",
      "type": "project",
      "require": {
          "nikic/fast-route": "^1.3"
      },
      "autoload": {
          "psr-4": {
              "App\\": "App/",
              "Core\\": "Core/"
          }
      }
  }
  ```
* Run `composer install` via `C:\xampp\php\php.exe C:\xampp\htdocs\ipo\composer.phar install`.

### Step 2: Establish Core Framework Infrastructure
* **`Core/bootstrap.php`**:
  * Define `BASE_PATH`, `APP_PATH`, `CORE_PATH`.
  * Compute dynamic `BASE_URL` with subdirectory support (`/IT`).
  * Initialize secure session settings and timezone (`Asia/Kolkata`).
  * Load `Core/helpers.php` (including `v_asset()`).
* **`Core/helpers.php`**:
  * Define `v_asset($path)` and `base_url($path)`.
* **`Core/Controller.php`**:
  * Base controller with `view($viewName, $data)`, `json($data, $statusCode)`, `redirect($url)`.
* **`Core/Auth.php`**:
  * Reusable session authentication check (replacing duplicate `auth-check.php` logic while maintaining exact behavior).
* **`Core/Database.php` & `Core/Model.php` (Database-Ready)**:
  * Lazy PDO connection factory.
  * Does NOT connect on startup.
  * Returns `null` or throws clean user-friendly exception if persistent storage is queried without configuration.
* **`config/app.php` & `config/database.php`**:
  * Move configuration parameters to `config/app.php`.
  * Add template database configuration in `config/database.php` (no real secrets committed).
  * Retain `config.php` as a backward-compatibility proxy.

### Step 3: Implement App Architecture & Controllers
* Build controllers in `App/Controllers/`:
  * `HomeController.php`
  * `FacultyController.php`
  * `CalendarController.php`
  * `ReportController.php`
  * `ZeroStudentController.php`
  * `CtlActivityController.php`
  * `CtlDriveController.php`
  * `SyllabusController.php`
  * `TimetableController.php`
  * `AuthController.php`
  * `ApiController.php`
  * `ErrorController.php`
* Build services in `App/Services/`:
  * `OtpService.php`
  * `EmailService.php`
  * `SheetsProxyService.php`
  * `TimetableService.php`

### Step 4: Organize Views
* Move existing presentation code into `App/Views/`:
  * `App/Views/home.php`
  * `App/Views/faculty.php`
  * `App/Views/calendar.php`
  * `App/Views/report.php`
  * `App/Views/zero-student-report.php`
  * `App/Views/ctlactivity.php`
  * `App/Views/ctldrive.php`
  * `App/Views/syllabus-papers.php`
  * `App/Views/timetable.php`
  * `App/Views/student-timetable.php`
  * `App/Views/workload-summary.php`
  * `App/Views/update-timetable.php`
  * `App/Views/auth-login.php`
  * `App/Views/errors/404.php`, `500.php`, `405.php`
  * `App/Views/partials/fab-nav.php`, `footer.php`, `theme-toggle.php`

### Step 5: FastRoute Routing & Front Controller
* **`routes/web.php`**:
  * Register all GET, POST, and OPTIONS routes with FastRoute route collector.
* **`index.php` (Front Controller)**:
  * Load `vendor/autoload.php`.
  * Load `Core/bootstrap.php`.
  * Create FastRoute dispatcher with caching toggle.
  * Extract request method and clean subdirectory-agnostic URI.
  * Dispatch route to controller action or error handler.
* **`.htaccess`**:
  * Update rewrite rules to route all non-file/non-directory requests through `index.php`.
  * Preserve direct access to `assets/` and `uploads/`.

---

## 8. Potential Risks & Mitigation Strategies

1. **Subdirectory URL Prefix Mismatch (e.g., `/IT/report` vs `/report`)**:
   * *Mitigation*: The front controller calculates `$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')` and strips it from `$_SERVER['REQUEST_URI']`. Tested to work on both `http://localhost/IT/` and production root domain.
2. **AJAX Endpoints Expecting Exact JSON Responses**:
   * *Mitigation*: `ApiController` and `AuthController` methods will use identical response schemas and HTTP status codes (400, 401, 403, 405, 429, 500, 200).
3. **Database Connection Failures during Migration**:
   * *Mitigation*: Database connection is strictly lazy. No database connection will be attempted anywhere during application bootstrap or on existing pages.
4. **Preservation of User Changes**:
   * *Mitigation*: `assets/js/timetableData.js` and `uploads/student_timetable/student_timetable_cache.json` will not be touched or overwritten.

---

## 9. Verification & Testing Checklist

- [x] FastRoute dispatches `/`, `/faculty`, `/calendar`, `/report`, `/timetable`, `/student-timetable`, `/workload-summary`, `/syllabus-papers`.
- [x] Protected pages (`/zero-student-report`, `/ctlactivity`, `/ctldrive`) enforce authentication properly via `auth-login.php`.
- [x] AJAX `/verify-password` (GET status, POST verify, GET clear) responds identically.
- [x] `/otp-service` (check_status, send_otp, verify_otp) functions as expected.
- [x] `/proxy-sheets` successfully proxies to Google Sheets.
- [x] `/sync-timetables` handles background timetable sync.
- [x] 404 Not Found and 405 Method Not Allowed error pages render correctly.
- [x] Application starts and runs with zero database dependencies.
- [x] Working tree clean on `develop`, without changes to `main`.

---

## 10. Implementation Summary

* **Architecture**: Clean MVC with `nikic/fast-route: ^1.3` and PSR-4 autoloading (`App\`, `Core\`).
* **Views Organization**: All main page views are standardized as `index.php` within their respective module folders (e.g., `App/Views/home/index.php`, `App/Views/timetable/index.php`, `App/Views/report/index.php`).
* **Database State**: Fully database-ready via `Core\Database` and `Core\Model`, with zero startup connection requirements and 100% functionality without a database server.
* **Compatibility**: 100% backwards-compatible routing for legacy `.php` URLs and existing AJAX endpoints.
