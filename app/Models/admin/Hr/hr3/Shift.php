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
     * Calculate monthly shift allowance based on actual clock-in time
     */
    public static function calculateMonthlyShiftAllowance($employeeId, $month)
    {
        $totalAllowance = 0;

        // Define shift allowances based on clock-in time (₱ per day)
        $shiftAllowances = [
            'morning'   => 200,   // 06:00 - 14:00
            'afternoon' => 250,   // 14:00 - 22:00
            'night'     => 300,   // 22:00 - 06:00
        ];

        // Get all attendance logs for the employee in the month
        $attendances = AttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('clock_in', Carbon::parse($month . '-01')->month)
            ->whereNotNull('clock_in')
            ->get();

        foreach ($attendances as $att) {
            $clockInHour = $att->clock_in->hour;

            // Determine shift based on clock-in time
            $shiftType = self::getShiftTypeFromClockIn($clockInHour);

            // Add daily shift allowance
            $totalAllowance += $shiftAllowances[$shiftType] ?? 0;
        }

        return $totalAllowance;
    }

    /**
     * Determine shift type based on clock-in hour
     */
    private static function getShiftTypeFromClockIn($hour)
    {
        if ($hour >= 6 && $hour < 14) {
            return 'morning';    // 06:00 - 13:59
        } elseif ($hour >= 14 && $hour < 22) {
            return 'afternoon';  // 14:00 - 21:59
        } else {
            return 'night';      // 22:00 - 05:59 (including midnight crossover)
        }
    }
}