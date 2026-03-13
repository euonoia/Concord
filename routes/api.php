<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OTPController;
use App\Http\Controllers\Api\LabSyncApiController;

Route::get('/cloudflare/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/send-otp', [OTPController::class, 'send']);
Route::post('/verify-otp', [OTPController::class, 'verify']);

// ── Lab Sync API (Core 1 ↔ Core 2) ─────────────────────────────────────────
Route::prefix('lab-sync')->group(function () {
    Route::post('/order',       [LabSyncApiController::class, 'receiveOrder']);
    Route::post('/result',      [LabSyncApiController::class, 'sendResult']);
    Route::get('/status/{id}',  [LabSyncApiController::class, 'checkStatus']);
});

