<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OTPController;
use App\Http\Controllers\admin\Hr\hr4\PayrollApiController;

Route::get('/cloudflare/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/send-otp', [OTPController::class, 'send']);
Route::post('/verify-otp', [OTPController::class, 'verify']);

// HR2 Payroll Request API
Route::prefix('/payroll')->group(function () {
    Route::post('/request-from-hr2', [PayrollApiController::class, 'submitPayrollRequest']);
    Route::get('/request/{id}', [PayrollApiController::class, 'getPayrollRequest']);
    Route::get('/employee/{employeeId}', [PayrollApiController::class, 'getEmployeePayroll']);
});

