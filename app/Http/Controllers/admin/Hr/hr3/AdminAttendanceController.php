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

            // Update the QR to point to the new auto-verify route
            $qrValue = route('attendance.verify', ['token' => $token]);

            return view('admin.hr3.attendance_station', compact('token', 'qrValue'));
        }
}