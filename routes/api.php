<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OTPController;
use App\Http\Controllers\Api\LabSyncApiController;
use App\Http\Controllers\Api\PharmacySyncApiController;
use App\Http\Controllers\Api\SurgeryDietSyncApiController;
use App\Http\Controllers\admin\Hr\hr4\PayrollApiController;

Route::get('/cloudflare/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Route::post('/send-otp', [OTPController::class, 'send']);
// Route::post('/verify-otp', [OTPController::class, 'verify']);

// ── Lab Sync API (Core 1 ↔ Core 2) ─────────────────────────────────────────
Route::prefix('lab-sync')->group(function () {
    Route::post('/order',       [LabSyncApiController::class, 'receiveOrder']);
    Route::post('/result',      [LabSyncApiController::class, 'sendResult']);
    Route::get('/status/{id}',  [LabSyncApiController::class, 'checkStatus']);
});

// ── Pharmacy Sync API (Core 1 ↔ Core 2) ───────────────────────────────────
Route::prefix('pharmacy-sync')->group(function () {
    Route::post('/dispense',    [PharmacySyncApiController::class, 'dispense']);
    Route::get('/status/{id}',  [PharmacySyncApiController::class, 'checkStatus']);
    Route::get('/search-drugs', [PharmacySyncApiController::class, 'searchDrugs']);
});

// ── Surgery & Diet Sync API (Core 1 ↔ Core 2) ───────────────────────────────
Route::prefix('surgery-diet-sync')->group(function () {
    Route::post('/order',       [SurgeryDietSyncApiController::class, 'receiveOrder']);
    Route::post('/result',      [SurgeryDietSyncApiController::class, 'sendResult']);
    Route::get('/status/{id}',  [SurgeryDietSyncApiController::class, 'checkStatus']);
});

// HR2 Payroll Request API
Route::prefix('/payroll')->group(function () {
    Route::post('/request-from-hr2', [PayrollApiController::class, 'submitPayrollRequest']);
    Route::get('/request/{id}', [PayrollApiController::class, 'getPayrollRequest']);
    Route::get('/employee/{employeeId}', [PayrollApiController::class, 'getEmployeePayroll']);
});

