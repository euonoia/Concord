<?php
namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\admin\Hr\hr3\AttendanceLog;

class AttendanceController extends Controller
{
    public function showStation()
    {
        // 1. Generate the secure, signed link
        $signedUrl = URL::temporarySignedRoute(
            'hr3.attendance.verify', 
            now()->addSeconds(60), 
            ['location' => 'MAIN_HOSPITAL']
        );

        // 2. Encode the URL so it can be passed safely to the QR API
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=" . urlencode($signedUrl);

        return view('admin.hr3.attendance_station', compact('qrCodeUrl'));
    }

    public function verifyScan(Request $request, $location)
    {
        // ... (Keep your verifyScan logic from the previous step) ...
        $user = Auth::user();
        
        AttendanceLog::create([
            'employee_id' => $user->id,
            'hospital_location_id' => $location,
            'clock_in' => now(),
            'device_fingerprint' => md5($request->header('User-Agent')),
        ]);

        return view('admin.hr3.attendance_success', compact('user'));
    }
}
