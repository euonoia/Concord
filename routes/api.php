<?php

use Illuminate\Support\Facades\Route;

Route::get('/cloudflare/ping', function () {
    return response()->json(['status' => 'ok']);
});
