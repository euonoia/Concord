<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Hr\UserDashboardController;
use App\Http\Controllers\user\Hr\hr2\UserCompetencyController;
use App\Http\Controllers\user\Hr\hr2\UserLearningController;
use App\Http\Controllers\user\Hr\hr2\UserTrainingController;
use App\Http\Controllers\user\Hr\hr2\UserSuccessionController;
use App\Http\Controllers\user\Hr\hr2\UserEssController;

// Import the new HR3 Controller
use App\Http\Controllers\user\Hr\hr3\UserAttendanceController;

// Dashboard 
Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('hr.dashboard');

// --- START OF HR2 Department (Development & Planning) ---
Route::get('/my-competencies', [UserCompetencyController::class, 'index'])->name('user.competencies.index');
Route::get('/learning', [UserLearningController::class, 'index'])->name('user.learning.index');
Route::post('/learning/enroll/{id}', [UserLearningController::class, 'enroll'])->name('user.learning.enroll');
Route::get('/my-training', [UserTrainingController::class, 'index'])->name('user.training.index');
Route::any('/training/enroll/{id}', [UserTrainingController::class, 'enroll'])->name('user.training.enroll');
Route::get('/my-succession', [UserSuccessionController::class, 'index'])->name('user.succession.index');
Route::get('/my-requests', [UserEssController::class, 'index'])->name('user.ess.index');
Route::post('/my-requests/store', [UserEssController::class, 'store'])->name('user.ess.store');
// --- END OF HR2 Department ---

// --- START OF HR3 Department (Attendance & Timekeeping) ---
Route::prefix('attendance')->group(function () {
    // 1. The Mobile Scanner View
    Route::get('/scan', [UserAttendanceController::class, 'scanView'])
        ->name('user.attendance.scan');

    // 2. The Verification Handshake (The URL encoded in the QR)
    // Protected by 'signed' middleware to ensure the 60s window and logged-in user only
    Route::post('/verify/{station}', [UserAttendanceController::class, 'verify'])
        ->name('attendance.verify')
        ->middleware(['auth', 'signed']);
        
    // 3. Success Feedback Page
    Route::get('/success', [UserAttendanceController::class, 'success'])
        ->name('user.attendance.success');
});
// --- END OF HR3 Department ---