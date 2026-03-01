<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminAttendanceController extends Controller
{
    /**
     * Show the kiosk QR code page (local generation)
     */
    public function showStation()
    {
        // Generate secure UUID token
        $token = Str::uuid()->toString();

        // Store token for 30 seconds only
        Cache::put("attendance_token_$token", true, now()->addSeconds(30));

        // Example station ID (you can dynamically set per kiosk)
        $stationId = 1;

        // Encode token + station as JSON for scanner
        $qrPayload = json_encode([
            'station' => $stationId,
            'token'   => $token
        ]);

    
        return view('admin.hr3.attendance_station', compact('qrPayload'));
    }
}