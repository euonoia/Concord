<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\admin\Hr\hr3\Shift; // Import the Shift model
use App\Models\Employee;
use Carbon\Carbon; 

class UserAttendanceController extends Controller
{
    public function scanView()
    {
        return view('hr.hr3.attendance_scan');
    }

    public function verify(Request $request)
    {
        if (!Auth::check()) { 
            return $this->handleResponse($request, false, 'Unauthorized', 401);
        }

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return $this->handleResponse($request, false, 'Employee record not found.', 404);
        }

        // 1. SHIFT CHECK: Does the user have a shift today?
        $today = now()->format('l'); // e.g., "Monday"
        $assignedShift = Shift::where('employee_id', $employee->employee_id)
            ->where('day_of_week', $today)
            ->where('is_active', 1)
            ->first();

        // If no shift is found for today, they cannot clock in
        if (!$assignedShift) {
            return $this->handleResponse($request, false, "Access Denied: You have no assigned shift for today ($today).", 403);
        }

        $existingLog = AttendanceLog::where('employee_id', $employee->employee_id)
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

        // Clock-In Validation
        if (!$existingLog) {
            $rawToken = $request->input('token');
            $tokenValue = str_contains($rawToken ?? '', '/') ? collect(explode('/', $rawToken))->last() : $rawToken;

            if (!$tokenValue) {
                return $this->handleResponse($request, false, 'Invalid Request: QR Token required to Clock In.', 400);
            }

            $validToken = Cache::pull("attendance_token_$tokenValue");
            if (!$validToken) {
                return $this->handleResponse($request, false, 'QR code has expired or is invalid.', 422);
            }
        } else {
            $tokenValue = $existingLog->qr_token; 
        }

        try {
            if ($existingLog) {
            
                $startTime = Carbon::parse($existingLog->clock_in);
                $now = now();
                
            
                $diffInMinutes = $startTime->diffInMinutes($now);
                $requiredMinutes = 8 * 60; 

                if ($diffInMinutes < $requiredMinutes) {
                    $remainingMinutes = $requiredMinutes - $diffInMinutes;
                    $hours = floor($remainingMinutes / 60);
                    $mins = $remainingMinutes % 60;
                    $timeStr = ($hours > 0) ? "$hours hours and $mins minutes" : "$mins minutes";
                    
                    return $this->handleResponse($request, false, "Shift incomplete. Minimum 8 hours required. You can clock out in $timeStr.", 422);
                }

                $existingLog->update(['clock_out' => $now]);
                return $this->handleResponse($request, true, 'Clock-out recorded successfully!');
            }

            // CLOCK IN LOGIC
            $now = now();
            $scheduledStart = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->start_time);
            
            // Determine status: Late if current time is more than 15 minutes past scheduled start
            $status = $now->gt($scheduledStart->addMinutes(15)) ? 'late' : 'on-time';

            AttendanceLog::create([
                'employee_id'        => $employee->employee_id, 
                'department_id'      => $employee->department_id, 
                'specialization'     => $employee->specialization,
                'position'           => $employee->position,
                'qr_token'           => $tokenValue,
                'clock_in'           => $now, 
                'device_fingerprint' => md5($request->userAgent() ?? ''),
                'status'             => $status, 
            ]);

            return $this->handleResponse($request, true, "Clock-in successful! You are marked as $status.");

        } catch (\Exception $e) {
            return $this->handleResponse($request, false, 'Server Error: ' . $e->getMessage(), 500);
        }
    }

    private function handleResponse(Request $request, bool $success, string $message, int $status = 200)
    {

        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $status);
        }

    
        if ($request->isMethod('post')) {
            return $success 
                ? redirect()->route('user.attendance.success')->with('status', $message)
                : redirect()->back()->with('error', $message); 
        }

    
        return $success 
            ? redirect()->route('user.attendance.success')->with('status', $message)
            : redirect()->route('user.attendance.scan')->with('error', $message);
    }

    public function success()
    {
        return view('hr.hr3.attendance_success');
    }
}