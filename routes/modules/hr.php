<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('hr.dashboard'); 
})->name('hr.dashboard');

// Add payroll, staff management here