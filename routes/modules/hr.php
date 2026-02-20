<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\hr2\CompetencyController;

Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

Route::resource('competencies', CompetencyController::class);