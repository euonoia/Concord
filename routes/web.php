<?php
use App\Http\Controllers\authentication\AuthController;
use App\Http\Controllers\authentication\EmployeeAuthController;

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

// --- Employee/Staff Routes ---
Route::prefix('staff')->group(function () {
    
   
    Route::get('/', function () {
        return redirect()->route('staff.login');
    });

  
    Route::get('/login', [EmployeeAuthController::class, 'showLogin'])->name('staff.login');

  
    Route::get('/register', function () {
        return view('authentication.employee_register');
    })->name('staff.register');

   
    Route::post('/login', [EmployeeAuthController::class, 'login'])->name('staff.login.submit');
    Route::post('/register', [EmployeeAuthController::class, 'store'])->name('staff.register.submit');
    Route::post('/logout', [EmployeeAuthController::class, 'destroy'])->name('staff.logout');
    
});
Route::middleware(['auth:employee'])->group(function () {
    
    Route::get('/hr/dashboard', function () {
       
        return view('hr.dashboard'); 
    })->name('hr.dashboard');

});