<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authentication\AuthController;
use App\Http\Controllers\admin\Hr\hr3\AdminAttendanceController;
use App\Http\Controllers\user\Hr\hr1\ApplicantController;
use App\Http\Controllers\hr\hr1\SocialRecognitionController;
use App\Http\Middleware\RedirectIfGuest;

// --- Public Routes ---
require base_path('routes/landing/landing.php');

// Home or onboarding (optional)
// Route::get('/', function () { return view('onboarding'); });

// Residency & Fellowship page — served with live job postings from DB
Route::get('/careers/residency-fellowship', function () {
    $postings = \Illuminate\Support\Facades\DB::table('job_postings_hr1')
        ->where('is_active', 1)
        ->orderBy('track_type')
        ->orderBy('id')
        ->get();
    return view('hr.hr1.residency_fellowship', compact('postings'));
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

// Social Recognition Interactions
Route::post('/recognition/{id}/like', [SocialRecognitionController::class, 'like'])->name('recognition.like');
Route::post('/recognition/{id}/comment', [SocialRecognitionController::class, 'comment'])->name('recognition.comment');

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