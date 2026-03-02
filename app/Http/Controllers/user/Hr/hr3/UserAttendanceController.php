<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\Shift; 
use App\Models\admin\Hr\hr3\AttendanceLog;
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

        $today = now()->format('l'); 
        $assignedShift = Shift::where('employee_id', $employee->employee_id)
            ->where('day_of_week', $today)
            ->where('is_active', 1)
            ->first();

        if (!$assignedShift) {
            return $this->handleResponse($request, false, "Access Denied: You have no assigned shift for today ($today).", 403);
        }

        $existingLog = AttendanceLog::where('employee_id', $employee->employee_id)
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

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
                $now = now();
                
                // FIXED LOGIC: Instead of checking for 8 hours of duration,
                // we check if the current time is at or past the scheduled end_time.
                $scheduledEnd = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->end_time);

                // Handle Night Shift cross-over (ends the next day)
                $scheduledStart = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->start_time);
                if ($scheduledEnd->lt($scheduledStart) && $now->gt($scheduledStart)) {
                    $scheduledEnd->addDay();
                }

                if ($now->lt($scheduledEnd)) {
                    $remaining = $now->diffForHumans($scheduledEnd, true);
                    return $this->handleResponse($request, false, "Shift incomplete. Your scheduled shift ends at " . $scheduledEnd->format('H:i') . " (in $remaining).", 422);
                }

                $existingLog->update(['clock_out' => $now]);
                return $this->handleResponse($request, true, 'Clock-out recorded successfully!');
            }

            $now = now();
            $scheduledStart = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->start_time);
            
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