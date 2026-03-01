<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\Employee;

class UserAttendanceController extends Controller

{
    /**
     * Show the scanner page
     */
    public function scanView()
    {
        return view('hr.hr3.attendance_scan'); 
        // Frontend handles QR scanning and POST token
    }

    /**
     * Handle QR verification and log attendance
     */
    public function verify(Request $request)
    {
        
         if (!Auth::check()) { 
            return redirect()->route('portal.login'); 
        }
    
        $request->validate([
            'token'   => 'required|string',
            'station' => 'required|integer',
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

      
        $validToken = Cache::pull("attendance_token_$token");
        if (!$validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ], 422);
        }

      
        $recent = AttendanceLog::where('employee_id', $employee->id)
            ->where('clock_in', '>=', now()->subMinutes(2))
            ->exists();

        if ($recent) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already logged recently.'
            ], 422);
        }

      
        try {
            AttendanceLog::create([
                'employee_id'        => $employee->id,
                'department_id'      => $employee->department_id, 
                'qr_token'           => $token,
                'clock_in'           => now(),
                'device_fingerprint' => md5($request->userAgent() ?? ''),
                'status'             => 'on-time',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}