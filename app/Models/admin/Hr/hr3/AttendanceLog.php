<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs_hr3';

    // Fillable fields for mass assignment
        protected $fillable = [
        'employee_id',
        'department_id',
        'specialization',
        'position_title',
        'shift_name',        
        'qr_token',
        'clock_in',
        'clock_out',
        'status',
        'worked_hours',     
        'overtime_hours',    
        'night_diff_hours',  
        'shift_allowance',    
        'overtime_pay',       
        'night_diff_pay',     
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(\App\Models\admin\Hr\hr2\Department::class, 'department_id', 'department_id');
    }
        public function shift()
    {
        return $this->hasOne(\App\Models\admin\Hr\hr3\Shift::class, 'employee_id', 'employee_id')
            ->whereDate('start_time', $this->clock_in ? $this->clock_in->format('Y-m-d') : now()->format('Y-m-d'))
            ->whereIn('shift_name', ['Morning Shift', 'Afternoon Shift', 'Night Shift'])
            ->where('is_active', true);
    }
}