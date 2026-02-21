<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;
use App\Http\Controllers\admin\Hr\hr2\AdminSuccessionController;
use App\Http\Controllers\admin\Hr\hr2\AdminEssController;
use App\Http\Controllers\user\Hr\hr2\UserCompetencyController;
Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

// --- Hr2 Admin ---
Route::resource('competencies', CompetencyController::class);
Route::resource('learning', AdminLearningController::class);
Route::resource('training', AdminTrainingController::class);
Route::prefix('succession')->group(function () {
    Route::get('/', [AdminSuccessionController::class, 'index'])->name('succession.index');
    Route::post('/position', [AdminSuccessionController::class, 'storePosition'])->name('succession.position.store');
    Route::post('/candidate', [AdminSuccessionController::class, 'storeCandidate'])->name('succession.candidate.store');
    Route::delete('/position/{id}', [AdminSuccessionController::class, 'destroyPosition'])->name('succession.position.destroy');
    Route::delete('/candidate/{id}', [AdminSuccessionController::class, 'destroyCandidate'])->name('succession.candidate.destroy');
});
Route::prefix('ess')->group(function () {
    Route::get('/', [AdminEssController::class, 'index'])->name('ess.index');
    Route::patch('/{id}/status', [AdminEssController::class, 'updateStatus'])->name('ess.updateStatus');
});
// --- Hr2 employee ---

Route::get('/my-competencies', [UserCompetencyController::class, 'index'])->name('user.competencies.index');
