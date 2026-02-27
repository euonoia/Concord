<?php
// routes/modules/admin.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
use App\Http\Controllers\admin\Hr\hr2\AdminEssController;

// --- Admin Dashboard ---
Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// --- HR2 Department AJAX Endpoints ---
Route::prefix('hr2')->group(function () {
    // Specializations by Department
    Route::get('/departments/{dept_code}/specializations', [AdminSuccessionController::class, 'getSpecializations'])
        ->name('departments.specializations');

    // Positions by Department + Specialization
    Route::get('/departments/{dept_code}/positions', [AdminSuccessionController::class, 'getPositions'])
        ->name('departments.positions');
    // Employees by Department and specialization
    Route::get('/departments/{dept_id}/employees', [AdminSuccessionController::class, 'getEmployeesByDeptAndSpec']);
});

// --- HR2 Admin Resource Routes ---
Route::resource('competencies', CompetencyController::class);
Route::resource('learning', AdminLearningController::class);
Route::resource('training', AdminTrainingController::class);


// --- Succession Planning Routes ---
Route::prefix('succession')->group(function () {
    Route::get('/', [AdminSuccessionController::class, 'index'])->name('succession.index');

    // Candidates
    Route::post('/candidate', [AdminSuccessionController::class, 'storeCandidate'])->name('succession.candidate.store');
    Route::delete('/candidate/{id}', [AdminSuccessionController::class, 'destroyCandidate'])->name('succession.candidate.destroy');
    
    //promote candidate
    Route::post('/candidate/{id}/promote', [AdminSuccessionController::class, 'promoteCandidate'])->name('succession.candidate.promote');
});

// --- ESS Module Routes ---
Route::prefix('ess')->group(function () {
    Route::get('/', [AdminEssController::class, 'index'])->name('ess.index');
    Route::patch('/{id}/status', [AdminEssController::class, 'updateStatus'])->name('ess.updateStatus');
});