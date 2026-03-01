<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\Employee;

class AdminAttendanceController extends Controller
{
    /**
     * Show the kiosk QR code page (LOCAL generation)
     */
    public function showStation()
    {
        // Generate secure UUID token
        $token = Str::uuid()->toString();

        // Store token for 30 seconds only
        Cache::put("attendance_token_$token", true, now()->addSeconds(30));

        // Pass RAW token to blade (NO JSON, NO API)
        return view('admin.hr3.attendance_station', compact('token'));
    }

    /**
     * Verify the scan from employee device
     */
    public function verifyScan(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = Auth::user();

        // Find employee record
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee record not found.'
            ], 404);
        }

        $token = $request->input('token');

        // Atomically pull token from cache (prevents reuse)
        $validToken = Cache::pull("attendance_token_$token");

        if (!$validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ], 422);
        }

        // Prevent double scan within 2 minutes
        $recent = AttendanceLog::where('employee_id', $employee->id)
            ->where('clock_in', '>=', now()->subMinutes(2))
            ->exists();

        if ($recent) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already logged recently.'
            ], 422);
        }

        // Store attendance
        AttendanceLog::create([
            'employee_id' => $employee->id,
            'department_id' => $employee->department_id,
            'qr_token' => $token,
            'clock_in' => now(),
            'device_fingerprint' => md5($request->header('User-Agent') ?? ''),
            'status' => 'on-time',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully.'
        ]);
    }
}