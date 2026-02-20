<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\Hr\hr2\AdminLearningController;
use App\Http\Controllers\admin\Hr\hr2\CompetencyController;
use App\Http\Controllers\admin\Hr\hr2\AdminTrainingController;

Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

// --- Hr2 Admin ---
Route::resource('competencies', CompetencyController::class);
Route::resource('learning', AdminLearningController::class);
Route::resource('training', AdminTrainingController::class);