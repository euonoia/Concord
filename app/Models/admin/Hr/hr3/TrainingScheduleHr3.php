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
        'presented_by', 
        'trainer_id',
        'notes'
    ];

    // The Trainee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
    // The Trainer (HR2 Admin)
    public function trainer() {
        return $this->belongsTo(\App\Models\Employee::class, 'trainer_id', 'employee_id');
    }

    // Relationship for the logged-in user who scheduled it
    public function presenter() {
        return $this->belongsTo(\App\Models\Employee::class, 'presented_by', 'employee_id');
}
    
}