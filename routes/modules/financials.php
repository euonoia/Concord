<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('financials.dashboard'); 
})->name('finance.dashboard');

// Add payroll, staff management here