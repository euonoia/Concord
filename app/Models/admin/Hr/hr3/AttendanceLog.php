<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Connect to TiDB cloud

    protected $table = 'attendance_logs_hr3';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'department_id',
        'specialization',
        'position_title',
        'qr_token',
        'clock_in',
        'clock_out',
        'worked_hours',
        'overtime_hours',
        'night_diff_hours',
        'device_fingerprint',
        'status',
        'device_fingerprint',
        'worked_hours',
        'overtime_hours',
        'night_diff_hours',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'worked_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'night_diff_hours' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee associated with this attendance log
     */
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the shift associated with this attendance log
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'employee_id', 'employee_id')
            ->where('shifts_hr3.day_of_week', $this->getDayOfWeek());
    }

    /**
     * Get the day of week from clock_in timestamp
     */
    private function getDayOfWeek()
    {
        if ($this->clock_in) {
            return Carbon::parse($this->clock_in)->dayName;
        }
        return null;
    }

    /**
     * Get the department associated with this attendance log
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\admin\Hr\hr2\Department::class, 'department_id', 'id');
    }
}
