<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\UserCardController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    //  Subject
    Route::resource('/subjects', SubjectsController::class);

    // Schedule
    Route::resource('/schedules', SchedulesController::class);

    // Class
    Route::resource('/class', ClassController::class);
    Route::get('/class/{id}/edit-assign', [ClassController::class, 'editAssign'])
    ->name('class.edit-assign');
    Route::put('/class/{id}/update-assign', [ClassController::class, 'updateAssign'])
    ->name('class.update-assign');

    // Attendances
    Route::resource('/attendances', AttendanceController::class);
    Route::get('/students/find-by-nisn/{nisn}', [AttendanceController::class, 'findByNisn']);
    Route::post('/attendances/scan-qr', [AttendanceController::class, 'scanQrAttendance'])
    ->name('attendances.scan-qr');
    Route::get('/attendances/trigger-alpha-otomatis', [AttendanceController::class, 'triggerAlpaOtomatis']);
    Route::get('attendances/data', [AttendanceController::class, 'getData'])->name('attendances.data');

    // RFID
    Route::post('/rfid-detect', [RFIDController::class, 'detectRFID'])
    ->withoutMiddleware(VerifyCsrfToken::class)->withoutMiddleware(['auth', 'auth:sanctum']);
    Route::get('/get-latest-rfid', [RFIDController::class, 'getLatestRFID'])->name('get.latest.rfid');
    Route::post('/clear-rfid-cache', [RFIDController::class, 'clearRFIDCache'])->name('clear.rfid');

    // Permission
    Route::resource('/permissions', PermissionController::class);

    // Role
    Route::resource('/roles', RoleController::class);

    // User
    Route::resource('/users', UserController::class);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
    ->name('users.toggle-active');
    // Show User
    Route::get('/student', [UserCardController::class, 'student'])->name('user.student');
    Route::post('/student/print', [UserCardController::class, 'stprint'])->name('student.print');    
    // Show Teacher
    Route::get('/teacher', [UserCardController::class, 'teacher'])->name('user.teacher');
    Route::post('/teacher/print', [UserCardController::class, 'tcprint'])->name('teacher.print');
    
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
