<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\Hr\hr2\UserCompetencyController;
use App\Http\Controllers\user\Hr\hr2\UserLearningController;
use App\Http\Controllers\user\Hr\hr2\UserTrainingController;
use App\Http\Controllers\user\Hr\hr2\UserSuccessionController;
use App\Http\Controllers\user\Hr\hr2\UserEssController;

Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

// --- Hr2 employee ---
Route::get('/my-competencies', [UserCompetencyController::class, 'index'])->name('user.competencies.index');
Route::get('/learning', [UserLearningController::class, 'index'])->name('user.learning.index');
Route::post('/learning/enroll/{id}', [UserLearningController::class, 'enroll'])->name('user.learning.enroll');
// Training Routes
Route::get('/my-training', [UserTrainingController::class, 'index'])->name('user.training.index');
Route::any('/training/enroll/{id}', [UserTrainingController::class, 'enroll'])->name('user.training.enroll');
// Succession Planning Route
Route::get('/my-succession', [UserSuccessionController::class, 'index'])->name('user.succession.index');
// ESS (Employee Self-Service) Routes
Route::get('/my-requests', [UserEssController::class, 'index'])->name('user.ess.index');
Route::post('/my-requests/store', [UserEssController::class, 'store'])->name('user.ess.store');
