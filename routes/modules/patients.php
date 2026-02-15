<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('patients.dashboard'); 
})->name('patients.dashboard');

// Add payroll, staff management here