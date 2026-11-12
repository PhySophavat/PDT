<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CourseSubjectController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ForgotPasswordController;

// Root URL: If logged in -> Dashboard, otherwise -> Direct Login Form (HTTP 200 with OG Meta Tags for Telegram/Facebook)
Route::get('/', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(AuthController::class)->showLoginForm($request);
})->name('home');
Route::post('/', [AuthController::class, 'login']);

// Primary Portal URL - Main Sharing URL for Facebook/Telegram/Messenger
Route::get('/portal', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(AuthController::class)->showLoginForm($request);
})->name('login');
Route::post('/portal', [AuthController::class, 'login']);

Route::get('/login', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(AuthController::class)->showLoginForm($request);
});
Route::post('/login', [AuthController::class, 'login']);

// Legacy URL - redirect to /portal (clean primary URL)
Route::get('/portal-sala-access-2026', function () {
    return redirect('/portal', 301);
});
Route::post('/portal-sala-access-2026', [AuthController::class, 'login']);

// Password Reset & Email OTP Routes (with Resend Countdown 60s & Max 5 Attempts)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.show');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,60');

Route::get('/auth/quick-login', [AuthController::class, 'directLogin'])->name('auth.quick-login');
Route::get('/csrf-token', fn() => response()->json(['csrf_token' => csrf_token()]))->name('csrf-token');

// Real-Time App Build Version Endpoint for Production Auto-Updater
Route::get('/api/app-version', function () {
    $manifestPath = public_path('build/manifest.json');
    $version = 'v1';
    $mtime = 0;
    if (file_exists($manifestPath)) {
        $mtime = filemtime($manifestPath);
        $version = substr(md5_file($manifestPath), 0, 16);
    }
    $dataHash = 'd_' . \App\Models\Student::count() . '_' . \App\Models\Book::count();
    return response()->json([
        'version' => $version,
        'timestamp' => $mtime,
        'data_hash' => $dataHash,
        'time' => now()->toIso8601String(),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
      ->header('Pragma', 'no-cache')
      ->header('Expires', '0');
})->name('api.app-version');

// PWA Fallbacks & SEO
Route::get('/robots.txt', fn() => response()->file(public_path('robots.txt'), ['Content-Type' => 'text/plain']));
Route::get('/manifest.json', fn() => response()->file(public_path('manifest.json'), ['Content-Type' => 'application/manifest+json']));
Route::get('/site.webmanifest', fn() => response()->file(public_path('site.webmanifest'), ['Content-Type' => 'application/manifest+json']));
Route::get('/sw.js', fn() => response()->file(public_path('sw.js'), ['Content-Type' => 'application/javascript']));
Route::get('/icon-192.png', fn() => response()->file(public_path('icon-192.png'), ['Content-Type' => 'image/png']));
Route::get('/icon-512.png', fn() => response()->file(public_path('icon-512.png'), ['Content-Type' => 'image/png']));
Route::get('/apple-touch-icon.png', fn() => response()->file(public_path('apple-touch-icon.png'), ['Content-Type' => 'image/png']));


Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Database Health Check
Route::get('/health-db', function () {
    try {
        $db = \Illuminate\Support\Facades\DB::connection();
        $pdo = $db->getPdo();
        $tables = $db->select('SHOW TABLES');
        $driver = $db->getDriverName();
        $students = \App\Models\Student::count();
        $teachers = \App\Models\Teacher::count();
        $scores = \App\Models\ExaminationScore::count();
        return response()->json([
            'status' => 'connected',
            'driver' => $driver,
            'database' => $db->getDatabaseName(),
            'tables_count' => count($tables),
            'students_count' => $students,
            'teachers_count' => $teachers,
            'scores_count' => $scores,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'driver' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
        ], 500);
    }
});

// Public Student Registration via Link & QR Code
use App\Http\Controllers\PublicRegistrationController;
Route::get('/register-student', [PublicRegistrationController::class, 'show'])->name('public.register-student');
Route::post('/register-student', [PublicRegistrationController::class, 'store'])->name('public.register-student.post')->middleware('throttle:10,60');

// Public Teacher Registration via Link & QR Code
Route::get('/register-teacher', [TeacherController::class, 'showPublicRegistration'])->name('public.register-teacher');
Route::post('/register-teacher', [TeacherController::class, 'submitPublicRegistration'])->name('public.register-teacher.post');

// Public Parent Registration via Link & QR Code
Route::get('/register-parent', [ParentController::class, 'showPublicRegistration'])->name('public.register-parent');
Route::post('/register-parent', [ParentController::class, 'submitPublicRegistration'])->name('public.register-parent.post');

// Public Student Leave Request via Link & QR Code
Route::get('/leave-request', [AttendanceController::class, 'showPublicLeaveForm'])->name('public.leave-request');
Route::post('/leave-request', [AttendanceController::class, 'submitPublicLeaveForm'])->name('public.leave-request.post');
Route::get('/api/public/students-by-class', [AttendanceController::class, 'getPublicStudentsByClass'])->name('public.students-by-class');

// Public Teacher Leave Request via Link & QR Code
Route::get('/teacher-leave-request', [TeacherController::class, 'showPublicLeaveForm'])->name('public.teacher-leave-request');
Route::post('/teacher-leave-request', [TeacherController::class, 'submitPublicLeaveForm'])->name('public.teacher-leave-request.post');

// Shared closure to render Vue 3 Single Page Application
$spa = fn() => view('spa');

// Public Examination Ranking Sheet View (Telegram / Facebook Sharing & Direct Parent/Student View)
Route::get('/examinations/ranking-sheet', [ExaminationController::class, 'rankingSheet'])->name('examinations.ranking-sheet.default');
Route::get('/examinations/{examination}/ranking-sheet', [ExaminationController::class, 'rankingSheet'])->where('examination', '.*')->name('examinations.ranking-sheet');

// Public Digital Library View (Telegram / Facebook Sharing & Direct Public Browsing)
Route::get('/library', $spa)->name('library.index');
Route::get('/api/library', [ApiController::class, 'library']);
Route::post('/api/library/books/{id}/broken-link', [ApiController::class, 'toggleBrokenLink']);
Route::post('/api/library/books/{id}/view', [ApiController::class, 'recordBookView']);

Route::middleware(['auth'])->group(function () use ($spa) {

    // JSON API Endpoints for Vue 3 frontend
    Route::prefix('api')->group(function () {
        Route::get('/debug-test', fn() => response()->json(['inside_api' => true]));
        Route::get('/dashboard', [ApiController::class, 'dashboard']);
        Route::get('/notifications/pending', [ApiController::class, 'getPendingNotifications']);
        Route::get('/students', [ApiController::class, 'students']);
        Route::post('/students', [ApiController::class, 'storeStudent']);
        Route::post('/students/scan-ocr', [ApiController::class, 'scanStudentsOcr']);
        Route::post('/students/bulk-store', [ApiController::class, 'bulkStoreStudents']);
        Route::post('/students/bulk-delete', [ApiController::class, 'bulkDeleteStudents']);
        Route::put('/students/{student}', [ApiController::class, 'updateStudent']);
        Route::delete('/students/{student}', [ApiController::class, 'destroyStudent']);
        Route::post('/students/{student}/approve', [ApiController::class, 'approveStudent']);
        Route::post('/students/{student}/reject', [ApiController::class, 'rejectStudent']);
        Route::post('/students/{student}/restore', [ApiController::class, 'restoreStudent']);
        Route::get('/teachers', [ApiController::class, 'teachers']);
        Route::post('/teachers', [ApiController::class, 'storeTeacher']);
        Route::put('/teachers/{teacher}', [ApiController::class, 'updateTeacher']);
        Route::delete('/teachers/{teacher}', [ApiController::class, 'destroyTeacher']);
        Route::post('/teachers/{teacher}/approve', [ApiController::class, 'approveTeacher']);
        Route::get('/classes', [ApiController::class, 'classes']);
        Route::post('/classes', [ApiController::class, 'storeClass']);
        Route::put('/classes/{class}', [ApiController::class, 'updateClass']);
        Route::delete('/classes/{class}', [ApiController::class, 'destroyClass']);
        Route::post('/classes/transfer', [ApiController::class, 'transferStudent']);
        Route::post('/classes/promote', [ApiController::class, 'promoteStudents']);
        Route::get('/attendance', [ApiController::class, 'attendance']);
        Route::post('/attendance', [ApiController::class, 'saveAttendance']);
        Route::get('/teacher-attendance', [ApiController::class, 'teacherAttendance']);
        Route::post('/teacher-attendance', [ApiController::class, 'saveTeacherAttendance']);
        Route::get('/leave-requests', [AttendanceController::class, 'getLeaveRequests']);
        Route::post('/leave-requests', [AttendanceController::class, 'submitPublicLeaveForm']);
        Route::post('/leave-requests/{id}/approve', [AttendanceController::class, 'approveLeaveRequest']);
        Route::post('/leave-requests/{id}/reject', [AttendanceController::class, 'rejectLeaveRequest']);
        Route::post('/leave-requests/{id}/restore', [AttendanceController::class, 'restoreLeaveRequest']);
        Route::get('/teacher-leave-requests', [TeacherController::class, 'getLeaveRequests']);
        Route::post('/teacher-leave-requests/{id}/approve', [TeacherController::class, 'approveLeaveRequest']);
        Route::post('/teacher-leave-requests/{id}/reject', [TeacherController::class, 'rejectLeaveRequest']);
        Route::post('/teacher-leave-requests/{id}/restore', [TeacherController::class, 'restoreLeaveRequest']);
        Route::get('/academic-years', [ApiController::class, 'academicYears']);
        Route::post('/academic-years', [ApiController::class, 'storeAcademicYear']);
        Route::put('/academic-years/{academicYear}', [ApiController::class, 'updateAcademicYear']);
        Route::post('/academic-years/{academicYear}/set-current', [ApiController::class, 'setCurrentAcademicYear']);
        Route::get('/course-subjects', [ApiController::class, 'courseSubjects']);
        Route::post('/course-subjects', [ApiController::class, 'storeCourseSubject']);
        Route::put('/course-subjects/{courseSubject}', [ApiController::class, 'updateCourseSubject']);
        Route::post('/course-subjects/{courseSubject}/class-configs', [ApiController::class, 'saveSubjectClassConfigs']);
        Route::delete('/course-subjects/{courseSubject}', [ApiController::class, 'destroyCourseSubject']);
        Route::get('/timetables', [ApiController::class, 'timetables']);
        Route::post('/timetables', [ApiController::class, 'storeTimetable']);
        Route::delete('/timetables/{timetable}', [ApiController::class, 'destroyTimetable']);
        Route::get('/examinations', [ApiController::class, 'examinations']);
        Route::post('/examinations', [ApiController::class, 'storeExamination']);
        Route::put('/examinations/{examination}', [ApiController::class, 'updateExamination']);
        Route::delete('/examinations/{examination}', [ApiController::class, 'destroyExamination']);
        Route::get('/examinations/{examination}/scores', [ApiController::class, 'getExamScores']);
        Route::post('/examinations/{examination}/scores', [ApiController::class, 'saveExamScores']);
        Route::get('/examinations/{examination}/matrix-scores', [ApiController::class, 'getExamMatrixScores']);
        Route::post('/examinations/{examination}/matrix-scores', [ApiController::class, 'saveExamMatrixScores']);
        Route::post('/examinations/{examination}/import-scores', [ApiController::class, 'importExamScores']);
        Route::get('/parents', [ApiController::class, 'parents']);
        Route::post('/parents', [ApiController::class, 'storeParent']);
        Route::put('/parents/{parent}', [ApiController::class, 'updateParent']);
        Route::delete('/parents/{parent}', [ApiController::class, 'destroyParent']);
        Route::post('/parents/{parent}/approve', [ApiController::class, 'approveParent']);
        Route::get('/finance', [ApiController::class, 'finance']);
        Route::post('/finance/invoices', [ApiController::class, 'storeInvoice']);
        Route::post('/finance/payments', [ApiController::class, 'storePayment']);
        Route::post('/finance/expenses', [ApiController::class, 'storeExpense']);
        Route::post('/library/extract-pdf', [ApiController::class, 'extractPdf']);
        Route::post('/library/books', [ApiController::class, 'storeBook']);
        Route::delete('/library/books/{id}', [ApiController::class, 'deleteBook']);
        Route::post('/library/books/{id}/view', [ApiController::class, 'recordBookView']);
        Route::post('/library/books/{id}/broken-link', [ApiController::class, 'toggleBrokenLink']);
        Route::post('/library/subjects', [ApiController::class, 'storeSubject']);
        Route::put('/library/subjects/{id}', [ApiController::class, 'updateSubject']);
        Route::delete('/library/subjects/{id}', [ApiController::class, 'deleteSubject']);
        Route::post('/library/borrow', [ApiController::class, 'borrowBook']);
        Route::put('/library/borrow/{borrow}', [ApiController::class, 'updateBorrow']);
        Route::delete('/library/borrow/{borrow}', [ApiController::class, 'deleteBorrow']);
        Route::post('/library/return/{borrow}', [ApiController::class, 'returnBook']);
        Route::get('/library/requests', [ApiController::class, 'getBookRequests']);
        Route::post('/library/requests', [ApiController::class, 'storeBookRequest']);
        Route::post('/library/requests/{id}/publish', [ApiController::class, 'publishBookRequestToLibrary']);
        Route::put('/library/requests/{id}/status', [ApiController::class, 'updateBookRequestStatus']);
        Route::delete('/library/requests/{id}', [ApiController::class, 'deleteBookRequest']);
        Route::get('/announcements', [ApiController::class, 'announcements']);
        Route::post('/announcements', [ApiController::class, 'storeAnnouncement']);
        Route::delete('/announcements/{announcement}', [ApiController::class, 'destroyAnnouncement']);
        Route::get('/reports', [ApiController::class, 'reports']);
        Route::get('/settings', [ApiController::class, 'settings']);
        Route::post('/settings', [ApiController::class, 'updateSettings']);
        Route::post('/settings/gemini-key', [ApiController::class, 'saveGeminiApiKey']);
        Route::get('/settings/backup', [ApiController::class, 'backupDatabase'])->name('settings.backup');
        Route::get('/settings/backup-excel', [ApiController::class, 'backupExcelData'])->name('settings.backup-excel');
        Route::post('/settings/reset-demo-data', [ApiController::class, 'resetDemoData']);
        Route::match(['get', 'post'], '/seed', [ApiController::class, 'seedDemoDatabase']);
        Route::get('/roles', [ApiController::class, 'roles']);
        Route::post('/roles/permissions', [ApiController::class, 'updateRolePermissions']);

        // Personal Profile (Student, Teacher, Admin)
        Route::get('/user/profile', [ApiController::class, 'getProfile'])->name('api.profile.get');
        Route::put('/user/profile', [ApiController::class, 'updateProfile'])->name('api.profile.update');
        Route::post('/user/password', [ApiController::class, 'updatePassword'])->name('api.profile.password');
        Route::post('/user/language', [ApiController::class, 'updateLanguage'])->name('api.profile.language');
        Route::post('/user/avatar', [ApiController::class, 'updateAvatar'])->name('api.profile.avatar');

        // System Activity Logs (Super Admin Only)
        Route::middleware(['super_admin'])->group(function () {
            Route::get('/system-logs', [\App\Http\Controllers\ActivityLogController::class, 'index']);
        });
    });

    // 1. Dashboard, Profile, Security & Language (Account Settings)
    Route::get('/dashboard', $spa)->name('dashboard');
    Route::get('/profile', $spa)->name('auth.profile');
    Route::get('/security', $spa)->name('auth.security');
    Route::get('/language', $spa)->name('auth.language');

    // 2. Student Management (Vue 3 UI + Backend API/Actions)
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
    Route::get('/students', $spa)->name('students.index');
    Route::get('/students/create', $spa)->name('student.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::post('/students/scan-ocr', [ApiController::class, 'scanStudentsOcr'])->name('students.scan-ocr');
    Route::post('/students/bulk-store', [ApiController::class, 'bulkStoreStudents'])->name('students.bulk-store');
    Route::post('/students/bulk-delete', [ApiController::class, 'bulkDeleteStudents'])->name('students.bulk-delete');
    Route::get('/students/{student}', $spa)->name('students.show');
    Route::get('/students/{student}/edit', $spa)->name('student.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/student', $spa)->name('students.index.legacy');
    Route::get('/student/create', $spa)->name('students.create');
    Route::post('/student', [StudentController::class, 'store'])->name('student.store');
    Route::get('/student/{student}', $spa)->name('student.show');
    Route::get('/student/{student}/edit', $spa)->name('students.edit');
    Route::put('/student/{student}', [StudentController::class, 'update'])->name('student.update');
    Route::delete('/student/{student}', [StudentController::class, 'destroy'])->name('student.destroy');

    // 3. Teacher Management (Vue 3 UI + Backend Actions)
    Route::get('/teachers', $spa)->name('teachers.index');
    Route::get('/teachers/create', $spa)->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}', $spa)->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', $spa)->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::get('/teacher', $spa)->name('teacher.index');

    // 4. Class & Section Management (Vue 3 UI + Backend Actions)
    Route::get('/classes', $spa)->name('classes.index');
    Route::get('/class', $spa)->name('class.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('class.store');
    Route::put('/classes/{class}', [ClassController::class, 'update'])->name('class.update');
    Route::post('/classes/transfer', [ClassController::class, 'transferStudent'])->name('class.transfer');
    Route::post('/classes/promote', [ClassController::class, 'promoteStudents'])->name('class.promote');
    Route::get('/class/{name}', $spa)->name('class.show');
    Route::get('/classes/{name}', $spa)->name('classes.show');

    // 5. Course Subjects Management (Catalog)
    Route::get('/course-subjects', $spa)->name('course-subjects.index');
    Route::post('/course-subjects', [CourseSubjectController::class, 'store'])->name('course-subjects.store');
    Route::put('/course-subjects/{course_subject}', [CourseSubjectController::class, 'update'])->name('course-subjects.update');
    Route::delete('/course-subjects/{course_subject}', [CourseSubjectController::class, 'destroy'])->name('course-subjects.destroy');

    // 6. Academic Years Management
    Route::get('/academic-years', $spa)->name('academic-years.index');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])
        ->name('academic-years.set-current');

    // 7. Timetable Management
    Route::get('/timetables', $spa)->name('timetables.index');
    Route::post('/timetables', [TimetableController::class, 'store'])->middleware('role:admin,super_admin,teacher')->name('timetables.store');
    Route::delete('/timetables/{timetable}', [TimetableController::class, 'destroy'])->middleware('role:admin,super_admin,teacher')->name('timetables.destroy');

    // 8. Attendance Management (Master Template)
    Route::get('/attendance', $spa)->name('attendance.index');
    Route::get('/attendances', $spa);
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/teachers', $spa)->name('attendance.teachers');
    Route::get('/teacher-attendance', $spa)->name('teacher-attendance.index');
    Route::get('/attendances/teachers', $spa);
    Route::post('/attendance/teachers', [AttendanceController::class, 'storeTeachers'])->name('attendance.teachers.store');

    // 9. Examination & Results Management
    Route::get('/examinations', $spa)->name('examinations.index');
    Route::get('/results', $spa)->name('result.index');
    Route::get('/results-overview', $spa)->name('results.index');
    Route::post('/examinations', [ExaminationController::class, 'store'])->name('examinations.store');
    Route::get('/examinations/{examination}/scores', $spa)->name('examinations.scores');
    Route::post('/examinations/{examination}/scores', [ExaminationController::class, 'storeScores'])->name('examinations.scores.store');
    Route::get('/examinations/{examination}/report-card/{student}', [ExaminationController::class, 'reportCard'])->name('examinations.report-card');
    Route::post('/examinations/ranking-sheet/settings', [ExaminationController::class, 'saveRankingSettings'])->name('examinations.ranking-sheet.settings');

    // 10. Parent & Guardian Management
    Route::get('/parents', $spa)->name('parents.index');
    Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
    Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');

    // 11. Finance & Payments Management
    Route::get('/finance', $spa)->name('finance.index');
    Route::post('/finance/invoices', [FinanceController::class, 'storeInvoice'])->name('finance.invoices.store');
    Route::post('/finance/store-invoice', [FinanceController::class, 'storeInvoice'])->name('finance.store_invoice');
    Route::post('/finance/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');
    Route::post('/finance/store-payment', [FinanceController::class, 'storePayment'])->name('finance.store_payment');
    Route::post('/finance/expenses', [FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
    Route::post('/finance/store-expense', [FinanceController::class, 'storeExpense'])->name('finance.store_expense');
    Route::get('/finance/payments/{payment}/receipt', [FinanceController::class, 'printReceipt'])->name('finance.receipt');

    // 12. Library Management
    Route::post('/library/books', [LibraryController::class, 'storeBook'])->name('library.books.store');
    Route::post('/library/store-book', [LibraryController::class, 'storeBook'])->name('library.store_book');
    Route::post('/library/extract-pdf', [LibraryController::class, 'extractPdf'])->name('library.extract_pdf');
    Route::post('/library/borrow', [LibraryController::class, 'borrowBook'])->name('library.borrow');
    Route::post('/library/borrow-book', [LibraryController::class, 'borrowBook'])->name('library.borrow_book');
    Route::post('/library/return/{borrow}', [LibraryController::class, 'returnBook'])->name('library.return');

    // Requests & Feedback (សំណើ និងមតិយោបល់)
    Route::get('/requests', $spa)->name('requests.index');
    Route::get('/feedback', $spa);

    // 13. Announcements & Notifications
    Route::get('/announcements', $spa)->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // 14. Reports & Analytics
    Route::get('/reports', $spa)->name('reports.index');

    // 15. System Settings (Admin & Super Admin Only)
    Route::middleware(['role:admin,super_admin'])->group(function () use ($spa) {
        Route::get('/settings', $spa)->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // 16. Roles & Permissions Management
        Route::get('/roles', $spa)->name('roles.index');
    });
    Route::get('/role', $spa)->name('role.index');
    Route::get('/permissions', $spa)->name('permissions.index');
    Route::get('/users/{user}/permissions', $spa)->name('user.permissions.edit');
    Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])->name('user.permissions.update');

    // 17. Score & Subject Legacy Routes
    Route::get('/students/{student}/scores', $spa)->name('subject.create');
    Route::post('/students/{student}/scores', [SubjectController::class, 'store'])->name('subject.store');
    Route::get('/scores', $spa)->name('subject.index');
    Route::get('/students/{student}/scores/view', $spa)->name('subject.show');
    Route::delete('/scores/{subject}', [SubjectController::class, 'destroy'])->name('subject.destroy');
    Route::get('students/{student}/subjects/create', $spa)->name('subject.create.legacy');
    Route::get('students/{student}/subjects', $spa)->name('subject.show.legacy');

    // 18. System Activity Logs (Super Admin Only)
    Route::middleware(['super_admin'])->group(function () use ($spa) {
        Route::get('/system-logs', $spa)->name('system-logs.index');
    });

    // Master Catch-All for Vue 3 SPA routing
    Route::get('/spa/{any?}', $spa)->where('any', '.*')->name('spa');
});
