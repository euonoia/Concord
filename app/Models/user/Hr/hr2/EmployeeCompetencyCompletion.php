<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class EmployeeCompetencyCompletion extends Model
{
    protected $table = 'employee_competency_completion_hr2';

   protected $fillable = [
    'employee_id',
    'competency_code',
    'completed_at',
    'verified_by',       
    'verification_notes', 
    'status',           
   
];
}