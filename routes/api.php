<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OTPController;

Route::get('/cloudflare/ping', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/send-otp', [OTPController::class, 'send']);
Route::post('/verify-otp', [OTPController::class, 'verify']);

