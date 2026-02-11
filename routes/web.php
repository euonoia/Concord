<?php
use App\Http\Controllers\authentication\AuthController;

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('onboarding');
});

Route::get('/login', function () {
    return view('authentication.login');
})->name('login')->middleware('guest');


Route::resource('tasks', TaskController::class);

Route::prefix('portal')->group(function () {
    
    // 1. Show Login View
    Route::get('/', function () {
        return view('authentication.login');
    })->name('portal.home');

    Route::get('/login', function () {
        return view('authentication.login');
    })->name('portal.login');

    // 2. Show Register View
    Route::get('/register', function () {
        return view('authentication.register');
    })->name('portal.register');

    // 3. Logic Processes
    Route::post('/login', [AuthController::class, 'login'])->name('portal.login.submit');
    Route::post('/register', [AuthController::class, 'store'])->name('portal.register.submit');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('portal.logout');
});

// 4. Protected Core Routes (The "Inside" of the App)
Route::middleware(['auth'])->prefix('core')->group(function () {
    Route::get('/dashboard', function () {
        return view('core.dashboard');
    })->name('dashboard');
});