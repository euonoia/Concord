<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompetencyCompletion extends Model
{
    protected $table = 'employee_competency_completion_hr2';

    protected $fillable = [
        'employee_id',
        'competency_code',
        'status',
        'completed_at'
    ];
}