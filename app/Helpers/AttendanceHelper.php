<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\admin\Hr\hr3\AttendanceLog;

class AttendanceHelper
{
    /**
     * Calculate worked hours from clock_in and clock_out
     *
     * @param Carbon $clockIn
     * @param Carbon $clockOut
     * @return float
     */
    public static function calculateWorkedHours(Carbon $clockIn, Carbon $clockOut): float
    {
        if (!$clockIn || !$clockOut) {
            return 0;
        }

        $diffMinutes = $clockIn->diffInMinutes($clockOut);
        return round($diffMinutes / 60, 2);
    }

    /**
     * Calculate overtime hours (beyond 8 hours per day)
     *
     * @param float $workedHours
     * @return float
     */
    public static function calculateOvertimeHours(float $workedHours): float
    {
        return round(max(0, $workedHours - 8), 2);
    }

    /**
     * Calculate night differential hours (10 PM - 6 AM)
     *
     * @param Carbon $clockIn
     * @param Carbon $clockOut
     * @return float
     */
    public static function calculateNightDiffHours(Carbon $clockIn, Carbon $clockOut): float
    {
        if (!$clockIn || !$clockOut || !$clockIn->lessThan($clockOut)) {
            return 0;
        }

        $ndMinutes = 0;
        $periodStart = $clockIn->copy();
        $periodEnd = $clockOut->copy();

        // Walk by day chunks to support multi-day and over-midnight spans.
        $cursor = $periodStart->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($periodEnd)) {
            $nightStart = $cursor->copy()->setTime(22, 0, 0);
            $nightEnd = $cursor->copy()->addDay()->setTime(6, 0, 0);

            $segmentStart = $periodStart->greaterThan($nightStart) ? $periodStart->copy() : $nightStart;
            $segmentEnd = $periodEnd->lessThan($nightEnd) ? $periodEnd->copy() : $nightEnd;

            if ($segmentStart->lessThan($segmentEnd)) {
                $ndMinutes += $segmentStart->diffInMinutes($segmentEnd);
            }

            $cursor->addDay();
        }

        return round(max(0, $ndMinutes / 60), 2);
    }

    /**
     * Get monthly attendance hours summary for an employee
     *
     * @param int|string $employeeId
     * @param string $month Format: 'Y-m'
     * @return array
     */
    public static function getMonthlyHoursSummary(int|string $employeeId, string $month = null): array
    {
        $employeeId = (int)$employeeId; // Ensure it's an int
        $month = $month ?? now()->format('Y-m');

        $attendances = AttendanceLog::where('employee_id', $employeeId)
            ->whereMonth('clock_in', date('m', strtotime($month)))
            ->whereYear('clock_in', date('Y', strtotime($month)))
            ->get();

        $totalWorked = 0;
        $totalOvertime = 0;
        $totalND = 0;

        foreach ($attendances as $att) {
            if ($att->clock_in && $att->clock_out) {
                $worked = self::calculateWorkedHours($att->clock_in, $att->clock_out);
                $totalWorked += $worked;
                $totalOvertime += self::calculateOvertimeHours($worked);
                $totalND += self::calculateNightDiffHours($att->clock_in, $att->clock_out);
            }
        }

        return [
            'worked_hours' => round($totalWorked, 2),
            'overtime_hours' => round($totalOvertime, 2),
            'night_diff_hours' => round($totalND, 2),
            'attendance_count' => $attendances->count(),
            'month' => $month,
        ];
    }

    /**
     * Get daily attendance with detailed hours breakdown
     *
     * @param int $employeeId
     * @param string $date Format: 'Y-m-d'
     * @return array|null
     */
    public static function getDailyAttendanceDetail(int $employeeId, string $date): ?array
    {
        $attendance = AttendanceLog::where('employee_id', $employeeId)
            ->whereDate('clock_in', $date)
            ->first();

        if (!$attendance) {
            return null;
        }

        return [
            'date' => $date,
            'clock_in' => $attendance->clock_in->format('H:i:s'),
            'clock_out' => $attendance->clock_out?->format('H:i:s'),
            'worked_hours' => self::calculateWorkedHours($attendance->clock_in, $attendance->clock_out),
            'overtime_hours' => self::calculateOvertimeHours(
                self::calculateWorkedHours($attendance->clock_in, $attendance->clock_out)
            ),
            'night_diff_hours' => self::calculateNightDiffHours($attendance->clock_in, $attendance->clock_out),
        ];
    }

    /**
     * Calculate overtime pay based on hours and hourly rate
     *
     * @param float $overtimeHours
     * @param float $hourlyRate
     * @param float $multiplier Pay multiplier for overtime (default 1.25 for 25%)
     * @return float
     */
    public static function calculateOvertimePay(float $overtimeHours, float $hourlyRate, float $multiplier = 1.25): float
    {
        return round($overtimeHours * $hourlyRate * $multiplier, 2);
    }

    /**
     * Calculate night differential pay based on hours and hourly rate
     *
     * @param float $ndHours
     * @param float $hourlyRate
     * @param float $percentage Night diff percentage (default 0.10 for 10%)
     * @return float
     */
    public static function calculateNightDiffPay(float $ndHours, float $hourlyRate, float $percentage = 0.10): float
    {
        return round($ndHours * $hourlyRate * $percentage, 2);
    }

    /**
     * Get all attendance records for a date range with hours
     *
     * @param int $employeeId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getAttendanceRange(int $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = AttendanceLog::where('employee_id', $employeeId)
            ->whereBetween('clock_in', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('clock_in', 'asc')
            ->get();

        return $attendances->map(function ($att) {
            return [
                'date' => $att->clock_in->format('Y-m-d'),
                'clock_in' => $att->clock_in->format('H:i:s'),
                'clock_out' => $att->clock_out?->format('H:i:s'),
                'worked_hours' => self::calculateWorkedHours($att->clock_in, $att->clock_out),
                'overtime_hours' => self::calculateOvertimeHours(
                    self::calculateWorkedHours($att->clock_in, $att->clock_out)
                ),
                'night_diff_hours' => self::calculateNightDiffHours($att->clock_in, $att->clock_out),
            ];
        })->toArray();
    }
}
