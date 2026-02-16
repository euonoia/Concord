<?php
// routes/modules/admin.php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

