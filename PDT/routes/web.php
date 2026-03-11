<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Models\Role;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ResultController;
use GuzzleHttp\Middleware;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Http;
// use App\Http\Controllers\UserPermissionController;
// use App\Http\Controllers\StudentController;
// use App\Http\Controllers\UserPermissionControlle
// use App\Http\Controllers\SubjectController;

Route::middleware(['auth'])->group(function () {
    // Subject/Score Management
    Route::get('/students/{student}/scores', [SubjectController::class, 'create'])
        ->name('subject.create');
    Route::post('/students/{student}/scores', [SubjectController::class, 'store'])
        ->name('subject.store');
    Route::get('/scores', [SubjectController::class, 'index'])
        ->name('subject.index');
    Route::get('/students/{student}/scores/view', [SubjectController::class, 'show'])
        ->name('subject.show');
    Route::delete('/scores/{subject}', [SubjectController::class, 'destroy'])
        ->name('subject.destroy');
});
Route::middleware(['auth'])->group(function () {
    // User Permission Management
    Route::get('/users/{user}/permissions', [UserPermissionController::class, 'edit'])
        ->name('user.permissions.edit');
    Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])
        ->name('user.permissions.update');
    
    // Teacher Management
    Route::resource('teacher', TeacherController::class);
    
    // Student Management
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('student.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('student.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});
// User Permission Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/users/{user}/permissions', [UserPermissionController::class, 'edit'])
        ->name('user.permissions.edit');
    Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])
        ->name('user.permissions.update');
});
// Student Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('student.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('student.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});

// User Permission Management Routes
Route::get('/users/{user}/permissions', [UserPermissionController::class, 'edit'])
    ->name('user.permissions.edit');
Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])
    ->name('user.permissions.update');

/// User Permission Management Routes
Route::get('/users/{user}/permissions', [TeacherController::class, 'editPermissions'])
    ->name('user.permissions.edit');
Route::put('/users/{user}/permissions', [TeacherController::class, 'updatePermissions'])
    ->name('user.permissions.update');

// use App\Http\Controllers\UserPermissionController;

Route::prefix('users')->group(function () {
    Route::get('{user}/permissions', [UserPermissionController::class, 'edit'])->name('user.permissions.edit');
    Route::put('{user}/permissions', [UserPermissionController::class, 'update'])->name('user.permissions.update');
});

// Include your class routesuse App\Http\Controllers\ClassController;

Route::get('/class/{name}', [ClassController::class, 'show'])->name('class.show');

require __DIR__.'/class.php';
///permission teacher
Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])
     ->name('user.permissions.edit');

Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])
     ->name('user.permissions.update');

/////rank
use App\Http\Controllers\RankController;
Route::get('/rank/{className}', [RankController::class, 'show'])->name('rank.index');


Route::get('/rank/{className}', [RankController::class, 'index'])->name('rank.index');

// use App\Http\Controllers\ResultController;

Route::get('/result', [ResultController::class, 'index'])->name('result.index');
Route::get('/result/{className}', [ResultController::class, 'show'])->name('result.show');



Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');



Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [AuthController::class, 'login']);                       
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');      

Route::middleware(['auth'])->group(function () {

Route::get('/profile',[DashboardController::class, 'profile'])->name('auth.profile');
Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
////teacher

Route::get('/teacher', [TeacherController::class, 'index'])->name('teacher.index');
Route::get('/teacher/create', [TeacherController::class, 'create'])->name('teacher.create');
Route::post('/teacher', [TeacherController::class, 'store'])->name('teacher.store'); 
Route::get('/teacher/{teacher}/edit', [TeacherController::class, 'edit'])->name('teacher.edit');
Route::put('/teacher/{teacher}', [TeacherController::class, 'update'])->name('teacher.update');
Route::delete('/teacher/{teacher}', [TeacherController::class, 'destroy'])->name('teacher.destroy');

////student
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

// use App\Http\Controllers\ScoreController;
// Route::get('/subject', [SubjectController::class, 'index'])
//     ->name('subject.index')
//     ->middleware('auth');  
    // use App\Http\Controllers\SubjectController;
Route::get('students/{student}/subjects/create', [SubjectController::class, 'create'])->name('subject.create');

Route::post('students/{student}/subjects', [SubjectController::class, 'store']) ->name('subject.store');

Route::get('students/{student}/subjects', [SubjectController::class, 'show'])->name('subject.show');
Route::post('students/{student}/subjects', [SubjectController::class, 'store'])->name('subject.store');
/////class
Route::get('/class',[ClassController::class,'index'])->name('class.index');
Route::get('subjects/7A', [SubjectController::class, 'show'])->name('subject.show.7A');
Route::get('students/{student}/subjects', [SubjectController::class, 'show'])->name('subject.show');
Route::get('subjects/{class}', [SubjectController::class, 'show'])->name('subject.show');

Route::resource('roles', RoleController::class);
// Route::resource('subjects', SubjectController::class);


Route::resource('/subject', SubjectController::class);
Route::resource('/role', RoleController::class);
Route::resource('/teacher',TeacherController::class);
Route::resource('/student', StudentController::class);

////export user
Route::get('/export-users', [UserController::class, 'exportAllUsers']);


// use App\Http\Controllers\ExportController;

Route::get('/export-all', [ExportController::class, 'exportAll']);

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Route;

Route::get('/users', function () {
    // 1. Call the public API
    $response = Http::get('https://jsonplaceholder.typicode.com/users');

    // 2. Convert JSON to PHP array
    $users = $response->json();

    // 3. Send data to the view
    return view('users.index', compact('users'));
});


});