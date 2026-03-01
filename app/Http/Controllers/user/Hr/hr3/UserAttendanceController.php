<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr3\AttendanceLog; // Your existing model
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    // Points to views/hr/hr3/attendance_scan.blade.php
    public function scanView() 
    {
        return view('hr.hr3.attendance_scan');
    }

    // This handles the data sent by the phone camera
    public function verify(Request $request, $station) 
    {
        $user = Auth::user();

        // 1. Prevent "Double Scans" within 2 minutes
        $recent = AttendanceLog::where('employee_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($recent) {
            return response()->json([
                'success' => false, 
                'message' => 'Attendance already logged recently.'
            ], 422);
        }

        // 2. Save to your 'attendance_logs_hr3' table in TiDB
        try {
            AttendanceLog::create([
                'employee_id'          => $user->id,
                'hospital_location_id' => $station,
                'clock_in'             => now(),
                'device_fingerprint'   => $request->userAgent(),
                'status'               => 'on-time'
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }
}