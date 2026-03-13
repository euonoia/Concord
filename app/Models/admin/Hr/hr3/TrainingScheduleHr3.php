<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class TrainingScheduleHr3 extends Model
{
    protected $table = 'training_schedule_hr3';

    protected $fillable = [
        'employee_id',
        'competency_code',
        'training_date',
        'training_time',
        'venue',
        'trainer_id',
        'notes'
    ];

    // The Trainee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // The Admin/Trainer who created the record
    public function trainer()
    {
        return $this->belongsTo(Employee::class, 'trainer_id', 'employee_id');
    }

    
}