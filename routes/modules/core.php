<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () { 
    return view('core.dashboard'); 
})->name('core.dashboard');

require __DIR__ . '/../core/core1/core1.php';
