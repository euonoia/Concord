<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\admin\Hr\hr3\Shift; 
use App\Models\admin\Hr\hr3\AttendanceLog;
use App\Models\admin\Hr\hr4\DirectCompensation;
use App\Models\admin\Hr\hr2\DepartmentPositionTitle;
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
                
                $scheduledEnd = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->end_time);

                $scheduledStart = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->start_time);
                if ($scheduledEnd->lt($scheduledStart) && $now->gt($scheduledStart)) {
                    $scheduledEnd->addDay();
                }

                if ($now->lt($scheduledEnd)) {
                    $remaining = $now->diffForHumans($scheduledEnd, true);
                    return $this->handleResponse($request, false, "Shift incomplete. Your scheduled shift ends at " . $scheduledEnd->format('H:i') . " (in $remaining).", 422);
                }

                $workedHours = \App\Helpers\AttendanceHelper::calculateWorkedHours($existingLog->clock_in, $now);
                $overtimeHours = \App\Helpers\AttendanceHelper::calculateOvertimeHours($workedHours);
                $nightDiffHours = \App\Helpers\AttendanceHelper::calculateNightDiffHours($existingLog->clock_in, $now);

                $existingLog->update([
                    'clock_out' => $now,
                    'worked_hours' => $workedHours,
                    'overtime_hours' => $overtimeHours,
                    'night_diff_hours' => $nightDiffHours,
                ]);

                // Keep monthly direct compensation in sync when clocking out
                $month = $now->format('Y-m');
                $monthly = \App\Helpers\AttendanceHelper::getMonthlyHoursSummary($existingLog->employee_id, $month);

                // Get employee position for calculations
                $empRecord = Employee::where('employee_id', $existingLog->employee_id)->first();
                $position = $empRecord ? \App\Models\admin\Hr\hr2\DepartmentPositionTitle::find($empRecord->position_id) : null;
                $baseSalary = $position ? $position->base_salary : 0;
                $hourlyRate = $baseSalary > 0 ? $baseSalary / 160 : 0;

                // Calculate shift allowance based on clock-in times
                $shiftAllowance = \App\Models\admin\Hr\hr3\Shift::calculateMonthlyShiftAllowance($existingLog->employee_id, $month);

                // Calculate overtime pay (25% premium)
                $overtimePay = \App\Helpers\AttendanceHelper::calculateOvertimePay(
                    $monthly['overtime_hours'],
                    $hourlyRate,
                    1.25
                );

                // Calculate night differential pay (10% premium)
                $nightDiffPay = \App\Helpers\AttendanceHelper::calculateNightDiffPay(
                    $monthly['night_diff_hours'],
                    $hourlyRate,
                    0.10
                );

                DirectCompensation::updateOrCreate(
                    ['employee_id' => $existingLog->employee_id, 'month' => $month],
                    [
                        'worked_hours' => $monthly['worked_hours'],
                        'overtime_hours' => $monthly['overtime_hours'],
                        'night_diff_hours' => $monthly['night_diff_hours'],
                        'shift_allowance' => $shiftAllowance,
                        'overtime_pay' => $overtimePay,
                        'night_diff_pay' => $nightDiffPay,
                    ]
                );

                return $this->handleResponse($request, true, 'Clock-out recorded successfully!');
            }

            $now = now();
            $scheduledStart = Carbon::parse($now->format('Y-m-d') . ' ' . $assignedShift->start_time);
            
            $status = $now->gt($scheduledStart->addMinutes(15)) ? 'late' : 'on-time';

           $employee->load('position'); 

                AttendanceLog::create([
                    'employee_id'        => $employee->employee_id, 
                    'department_id'      => $employee->department_id, 
                    'specialization'     => $employee->position->specialization_name ?? $employee->specialization,
                    'position_title'     => $employee->position->position_title ?? 'Unassigned', // <--- This pulls the actual name
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
    return redirect()->route('hr.dashboard')->with('success', 'Attendance logged successfully!');    
    }
}