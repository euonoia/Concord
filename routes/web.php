<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authentication\AuthController;
use App\Http\Controllers\admin\Hr\hr3\AdminAttendanceController;
use App\Http\Controllers\user\Hr\hr1\ApplicantController;
use App\Http\Middleware\RedirectIfGuest;

// --- Public Routes ---
require base_path('routes/landing/landing.php');

// Home or onboarding (optional)
// Route::get('/', function () { return view('onboarding'); });
Route::get('/ping', fn() => 'pong');

// Residency & Fellowship page
Route::get('/careers/residency-fellowship', function () {
    return view('hr.hr1.residency_fellowship');
})->name('careers.residency');

// --- Applicant Routes ---
Route::prefix('careers')->group(function () {

    // Show application form
    Route::get('/apply', [ApplicantController::class, 'showApplicationForm'])
        ->name('careers.apply');

    // Submit application form
    Route::post('/apply', [ApplicantController::class, 'store'])
        ->name('careers.apply.store');

    // AJAX route to get positions by department
    Route::get('/get-positions', [ApplicantController::class, 'getPositions'])
        ->name('careers.getPositions');
});

// --- Attendance Station (Public QR display) ---
Route::get('/attendance/station', [AdminAttendanceController::class, 'showStation'])
     ->name('hr3.attendance.station');

// --- Employee QR Verification (requires auth) ---
Route::middleware(['auth'])->group(function () {
    Route::post('/hr/hr3/attendance/verify', [AdminAttendanceController::class, 'verifyScan'])
         ->name('hr3.attendance.verify');

    // Other HR module routes
    Route::prefix('hr')->group(base_path('routes/modules/hr.php'));
});

// --- Portal Authentication Routes ---
Route::prefix('portal')->group(function () {
    Route::get('/', fn() => view('authentication.login'))->name('portal.home');
    Route::get('/login', fn() => view('authentication.login'))->name('portal.login');
    Route::get('/register', fn() => view('authentication.register'))->name('portal.register');

    Route::post('/login', [AuthController::class, 'login'])->name('portal.login.submit');
    Route::post('/register', [AuthController::class, 'store'])->name('portal.register.submit');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('portal.logout');

    // 2FA Routes
    Route::get('/2fa', [AuthController::class, 'show2fa'])->name('portal.2fa');
    Route::post('/2fa', [AuthController::class, 'verify2fa'])->name('portal.2fa.verify');
    Route::get('/2fa/resend', [AuthController::class, 'resend2fa'])->name('portal.2fa.resend');
});

// --- Protected Subsystem Routes (requires redirect if guest) ---
Route::middleware([RedirectIfGuest::class])->group(function () {
    Route::prefix('admin')->group(base_path('routes/modules/admin.php'));
    Route::prefix('core')->group(base_path('routes/modules/core.php'));
    Route::prefix('hr')->group(base_path('routes/modules/hr.php'));
    Route::prefix('logistics')->group(base_path('routes/modules/logistics.php'));
    Route::prefix('financials')->group(base_path('routes/modules/financials.php'));
    Route::prefix('patient')->group(base_path('routes/modules/patients.php'));
});