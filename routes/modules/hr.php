<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\CompetencyController;

Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

// Competencies CRUD
Route::resource('competencies', CompetencyController::class);