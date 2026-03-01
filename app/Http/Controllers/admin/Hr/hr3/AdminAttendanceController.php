<?php
namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    /**
     * Show the kiosk QR code page
     */
    public function showStation()
    {
        // 1. Generate a dynamic token
        $token = Str::random(40);

        // 2. Store token in cache for 60 seconds
        Cache::put('attendance_token_'.$token, true, 60);

        // 3. Generate QR payload (JSON encoded)
        $qrPayload = json_encode([
            'token' => $token
        ]);

        // 4. Generate QR code URL (external API)
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=" . urlencode($qrPayload);

        return view('admin.hr3.attendance_station', compact('qrCodeUrl'));
    }

    /**
     * Verify the scan from an employee
     */
    public function verifyScan(Request $request)
    {
        $user = Auth::user();

        // 1. Find employee record
        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found.'
            ], 404);
        }

        $token = $request->input('token');

        // 2. Validate token from cache
        if (!Cache::has('attendance_token_'.$token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ], 422);
        }

        // 3. Prevent double scan (2-minute window)
        $recent = AttendanceLog::where('employee_id', $employee->id)
            ->where('clock_in', '>=', now()->subMinutes(2))
            ->first();

        if ($recent) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already logged recently.'
            ], 422);
        }

        // 4. Log attendance in TiDB (this is safe — only successful scans)
        AttendanceLog::create([
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
            'qr_token' => $token,
            'clock_in' => now(),
            'device_fingerprint' => md5($request->header('User-Agent')),
            'status' => 'on-time',
        ]);

        // 5. Remove token from cache so it cannot be reused
        Cache::forget('attendance_token_'.$token);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully.'
        ]);
    }
}