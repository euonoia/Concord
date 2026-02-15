<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('core.dashboard'); 
})->name('core.dashboard');

// Add payroll, staff management here