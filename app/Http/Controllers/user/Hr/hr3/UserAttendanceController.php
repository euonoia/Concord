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
    public function scanView()
    {
        return view('hr.hr3.attendance_scan');
    }

    public function verify(Request $request)
    {
        // 1. Authentication Check
        if (!Auth::check()) { 
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('portal.login');
        }

        // 2. Extract Token (works for POST body or GET query params)
        $rawToken = $request->input('token');
        
        // If the scanner picks up the full URL, extract just the UUID at the end
        $tokenValue = str_contains($rawToken, '/') ? collect(explode('/', $rawToken))->last() : $rawToken;

        if (!$tokenValue) {
            return $this->handleResponse($request, false, 'Invalid Request: No token provided.', 400);
        }

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return $this->handleResponse($request, false, 'Employee record not found.', 404);
        }

        // 3. Cache Validation (One-time use)
        $validToken = Cache::pull("attendance_token_$tokenValue");
        if (!$validToken) {
            return $this->handleResponse($request, false, 'QR code has expired or is invalid.', 422);
        }

        // 4. Anti-Spam Check (2 minutes)
        $recent = AttendanceLog::where('employee_id', $employee->id)
            ->where('clock_in', '>=', now()->subMinutes(2))
            ->exists();

        if ($recent) {
            return $this->handleResponse($request, false, 'Attendance already logged recently.', 422);
        }

        // 5. Database Entry
        try {
            AttendanceLog::create([
                'employee_id'        => $employee->id,
                'department_id'      => $employee->department_id, 
                'qr_token'           => $tokenValue,
                'clock_in'           => now(),
                'device_fingerprint' => md5($request->userAgent() ?? ''),
                'status'             => 'on-time',
            ]);

            return $this->handleResponse($request, true, 'Attendance recorded successfully!');

        } catch (\Exception $e) {
            return $this->handleResponse($request, false, 'Server Error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper to handle both AJAX (JS Scanner) and Direct Browser (Native Camera)
     */
    private function handleResponse(Request $request, bool $success, string $message, int $status = 200)
    {
        // If it's the JS scanner (AJAX)
        if ($request->expectsJson() || $request->isMethod('post')) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $status);
        }

        // If it's a native camera scan (GET redirect)
        if ($success) {
            return redirect()->route('user.attendance.success')->with('status', $message);
        }

        return redirect()->route('user.attendance.scan')->with('error', $message);
    }

    public function success()
    {
        return view('hr.hr3.attendance_success');
    }
}