<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class EmployeeTrainingScore extends Model
{
    protected $table = 'employee_training_scores_hr2';

    protected $fillable = [
        'employee_id',      
        'competency_code',  
        'scores',           
        'total_score',       
        'status',          
        'evaluated_by',      
        'evaluated_at',     
    ];

    protected $casts = [
        'scores' => 'array', // Automatically decode/encode JSON
        'evaluated_at' => 'datetime'
    ];
}