<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts_hr3';

    protected $fillable = [
        'employee_id',
        'shift_name',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }
}