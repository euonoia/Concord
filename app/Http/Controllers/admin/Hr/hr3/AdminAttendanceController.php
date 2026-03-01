<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminAttendanceController extends Controller
{
    public function showStation()
    {
        $token = Str::uuid()->toString();

        Cache::put("attendance_token_$token", true, now()->addSeconds(30));

        return view('admin.hr3.attendance_station', compact('token'));
    }
}