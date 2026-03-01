<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class AttendanceLog extends Model
{
    use HasFactory;

    // Following your pattern of explicit table naming
    protected $table = 'attendance_logs_hr3';

    protected $fillable = [
        'employee_id',
        'hospital_location_id',
        'clock_in',
        'device_fingerprint',
        'status' // e.g., 'on-time', 'late'
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}