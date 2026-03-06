<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\admin\Hr\hr3\AttendanceLog;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts_hr3';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'shift_name',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Calculate monthly shift allowance based on actual attendance
     */
    public static function calculateMonthlyShiftAllowance($employeeId, $month)
    {
        $totalAllowance = 0;

        // Define fixed shift allowances in ₱
        $shiftAllowances = [
            'Morning Shift'   => 0,
            'Afternoon Shift' => 50,
            'Night Shift'     => 100, // fixed
        ];

        $nightDiffPercent = 0.10; // 10% ND for 10 PM - 6 AM

        // Get all attendance logs for the employee in the month
        $attendances = AttendanceLog::with('shift', 'employee.position')
            ->where('employee_id', $employeeId)
            ->whereMonth('clock_in', Carbon::parse($month . '-01')->month)
            ->whereNotNull('clock_in')
            ->get();

        foreach ($attendances as $att) {

            // Only apply allowance if attendance has a scheduled shift
            if ($att->shift) {
                $shiftName = $att->shift->shift_name;
                $totalAllowance += $shiftAllowances[$shiftName] ?? 0;

                // Night differential calculation
                if ($shiftName === 'Night Shift' && $att->clock_out) {
                    $clockIn = $att->clock_in;
                    $clockOut = $att->clock_out;

                    // ND hours: 10 PM – 6 AM
                    $ndStart = $clockIn->copy()->hour(22)->minute(0);
                    $ndEnd   = $clockIn->copy()->addDay()->hour(6)->minute(0);

                    $ndHours = max(0, min($clockOut->timestamp, $ndEnd->timestamp) - max($clockIn->timestamp, $ndStart->timestamp)) / 3600;

                    $dailyRate = $att->employee->position->base_salary ?? 0;
                    $hourlyRate = $dailyRate / 8;

                    $totalAllowance += $hourlyRate * $ndHours * $nightDiffPercent;
                }
            }
        }

        return $totalAllowance;
    }
}