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
        'position_id',
        'qr_token',
        'clock_in',
        'clock_out',
        'device_fingerprint',
        'status',
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

}