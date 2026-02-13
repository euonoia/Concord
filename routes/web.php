<?php
use App\Http\Controllers\authentication\AuthController;
use App\Http\Controllers\authentication\EmployeeAuthController;

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('onboarding');
});

Route::resource('tasks', TaskController::class);


// --- Public Portal Routes ---
Route::prefix('portal')->group(function () {
    Route::get('/', function () {
        return view('authentication.login');
    })->name('portal.home');

    Route::get('/login', function () {
        return view('authentication.login');
    })->name('portal.login');

    Route::get('/register', function () {
        return view('authentication.register');
    })->name('portal.register');

    Route::post('/login', [AuthController::class, 'login'])->name('portal.login.submit');
    Route::post('/register', [AuthController::class, 'store'])->name('portal.register.submit');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('portal.logout');
});

// --- Protected Subsystem Routes ---
Route::middleware(['auth'])->group(function () {

    // 1. CORE / CLINICAL (Doctors, Nurses, Admin)
    Route::prefix('core')->group(function () {
        Route::get('/dashboard', function () { return view('core.dashboard'); })->name('core.dashboard');
        // Add clinical routes here
    });

    // 2. HR SUBSYSTEM
    Route::prefix('hr')->group(function () {
        Route::get('/dashboard', function () { return view('hr.dashboard'); })->name('hr.dashboard');
        // Add payroll, staff management here
    });

    // 3. LOGISTICS SUBSYSTEM
    Route::prefix('logistics')->group(function () {
        Route::get('/dashboard', function () { return view('logistics.dashboard'); })->name('logistics.dashboard');
        // Add inventory, procurement here
    });

    // 4. FINANCIALS SUBSYSTEM
    Route::prefix('financials')->group(function () {
        Route::get('/dashboard', function () { return view('financials.dashboard'); })->name('finance.dashboard');
        // Add billing, accounting here
    });

    // 5. PATIENT PORTAL
    Route::prefix('patient')->group(function () {
        Route::get('/dashboard', function () { return view('patient.dashboard'); })->name('patient.portal');
        // Add lab results, appointments here
    });
});
