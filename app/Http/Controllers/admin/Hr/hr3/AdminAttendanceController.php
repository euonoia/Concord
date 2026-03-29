<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminAttendanceController extends Controller
{
    /**
     * Ensure only HR3 users can access this controller
     */
    private function authorizeHr3()
    {
        $user = Auth::user();
        if (!$user || $user->role_slug !== 'admin_hr3') {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * Show the attendance station with auto-expiring QR token
     */
    public function showStation()
    {
        $this->authorizeHr3();

        // Generate a unique token for QR scanning
        $token = Str::uuid()->toString();

        // Cache the token for 30 seconds for verification
        Cache::put("attendance_token_$token", true, now()->addSeconds(30));

        // QR points to the auto-verify route with token
        $qrValue = route('attendance.verify', ['token' => $token]);

        return view('admin.hr3.attendance_station', compact('token', 'qrValue'));
    }
}