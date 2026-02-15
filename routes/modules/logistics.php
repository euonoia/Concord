<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('logistics.dashboard'); 
})->name('logistics.dashboard');

// Add payroll, staff management here