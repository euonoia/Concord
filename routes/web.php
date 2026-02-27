<?php
use App\Http\Controllers\authentication\AuthController;
use App\Http\Middleware\RedirectIfGuest;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
require base_path('routes/landing/landing.php');

// Route::get('/', function () {
//     return view('onboarding');
// });
Route::get('/careers/residency-fellowship', function () {
    return view('hr.hr1.residency_fellowship');
})->name('careers.residency');

Route::prefix('portal')->group(function () {
    Route::get('/', function () { return view('authentication.login'); })->name('portal.home');
    Route::get('/login', function () { return view('authentication.login'); })->name('portal.login');
    Route::get('/register', function () { return view('authentication.register'); })->name('portal.register');

    Route::post('/login', [AuthController::class, 'login'])->name('portal.login.submit');
    Route::post('/register', [AuthController::class, 'store'])->name('portal.register.submit');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('portal.logout');
});

// --- Protected Subsystem Routes ---
Route::middleware([RedirectIfGuest::class])->group(function () {
    Route::prefix('admin')->group(base_path('routes/modules/admin.php'));
    Route::prefix('core')->group(base_path('routes/modules/core.php'));
    Route::prefix('hr')->group(base_path('routes/modules/hr.php'));
    Route::prefix('logistics')->group(base_path('routes/modules/logistics.php'));
    Route::prefix('financials')->group(base_path('routes/modules/financials.php'));
    Route::prefix('patient')->group(base_path('routes/modules/patients.php'));
});