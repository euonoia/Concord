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

        // --- Fetch assigned shift based on today ---
        $now = now();
        $today = $now->format('Y-m-d');

        $assignedShift = Shift::where('employee_id', $employee->employee_id)
            ->whereDate('start_time', '<=', $now) // check shift that includes today
            ->where('is_active', 1)
            ->first();

        if (!$assignedShift) {
            return $this->handleResponse($request, false, "Access Denied: You have no assigned shift today.", 403);
        }

        // --- Check if already clocked in ---
        $existingLog = AttendanceLog::where('employee_id', $employee->employee_id)
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

        // --- Handle token ---
        if (!$existingLog) {
            $rawToken = $request->input('token');
            $tokenValue = str_contains($rawToken ?? '', '/') 
                ? collect(explode('/', $rawToken))->last() 
                : $rawToken;

            if (!$tokenValue) {
                return $this->handleResponse($request, false, 'Invalid Request: QR Token required.', 400);
            }

            $validToken = Cache::pull("attendance_token_$tokenValue");

            if (!$validToken) {
                return $this->handleResponse($request, false, 'QR code has expired or is invalid.', 422);
            }
        } else {
            $tokenValue = $existingLog->qr_token;
        }

        try {
            // -------------------
            // Clock-out scenario
            // -------------------
            if ($existingLog) {

                $now = now();

                $scheduledStart = Carbon::parse($today . ' ' . $assignedShift->start_time);
                $scheduledEnd   = Carbon::parse($today . ' ' . $assignedShift->end_time);

                // Handle overnight shifts
                if ($scheduledEnd->lt($scheduledStart)) {
                    $scheduledEnd->addDay();
                }

                if ($now->lt($scheduledEnd)) {
                    $remaining = $now->diffForHumans($scheduledEnd, true);
                    return $this->handleResponse($request, false, "Shift incomplete. Your scheduled shift ends at " . $scheduledEnd->format('H:i') . " (in $remaining).", 422);
                }

                // --- Calculations ---
                $clockIn = Carbon::parse($existingLog->clock_in);
                $workedHours = $clockIn->diffInMinutes($now) / 60;

                $scheduledHours = $scheduledStart->diffInMinutes($scheduledEnd) / 60;
                $overtimeHours = max(0, $workedHours - $scheduledHours);

                // Night differential 22:00 - 06:00
                $nightStart = Carbon::parse($clockIn->format('Y-m-d') . ' 22:00:00');
                $nightEnd   = Carbon::parse($clockIn->format('Y-m-d') . ' 06:00:00')->addDay();
                $nightDiffHours = 0;

                if ($now->gt($nightStart) || $clockIn->lt($nightEnd)) {
                    $start = max($clockIn->timestamp, $nightStart->timestamp);
                    $end   = min($now->timestamp, $nightEnd->timestamp);
                    if ($end > $start) {
                        $nightDiffHours = ($end - $start) / 3600;
                    }
                }

                // Shift allowance
                $shiftAllowances = [
                    'Morning Shift' => 0,
                    'Afternoon Shift' => 50,
                    'Night Shift' => 100,
                ];
                $shiftAllowance = $shiftAllowances[$assignedShift->shift_name] ?? 0;

                // Overtime & night diff pay
                $employee->load('position');
                $baseSalary = $employee->position->base_salary ?? 0;
                $dailyRate  = $baseSalary / 22;
                $hourlyRate  = $dailyRate / 8;

                $overtimePay  = $overtimeHours * ($hourlyRate * 1.25);
                $nightDiffPay = $nightDiffHours * ($hourlyRate * 0.10);

                // --- Update attendance log ---
                $existingLog->update([
                    'clock_out'        => $now,
                    'worked_hours'     => $workedHours,
                    'overtime_hours'   => $overtimeHours,
                    'night_diff_hours' => $nightDiffHours,
                    'shift_name'       => $assignedShift->shift_name,
                    'shift_allowance'  => $shiftAllowance,
                    'overtime_pay'     => $overtimePay,
                    'night_diff_pay'   => $nightDiffPay,
                ]);

                return $this->handleResponse($request, true, 'Clock-out recorded successfully!');
            }

            // -------------------
            // Clock-in scenario
            // -------------------
            $status = $now->gt(Carbon::parse($today . ' ' . $assignedShift->start_time)->addMinutes(15)) 
                ? 'late' : 'on-time';

            $employee->load('position');

            AttendanceLog::create([
                'employee_id'        => $employee->employee_id, 
                'department_id'      => $employee->department_id, 
                'specialization'     => $employee->position->specialization_name ?? $employee->specialization,
                'position_title'     => $employee->position->position_title ?? 'Unassigned',
                'shift_name'         => $assignedShift->shift_name,  // <-- Add shift_name
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

        return $success
            ? redirect()->route('user.attendance.success')->with('status', $message)
            : redirect()->back()->with('error', $message);
    }

    public function success()
    {
        return redirect()->route('hr.dashboard')->with('success','Attendance logged successfully!');
    }
}