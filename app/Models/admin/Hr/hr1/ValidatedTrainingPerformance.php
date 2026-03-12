<?php

namespace App\Models\admin\Hr\hr1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidatedTrainingPerformance extends Model
{
    use HasFactory;

    // Table name based on your schema
    protected $table = 'validated_training_performance_hr1';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'employee_id',
        'weighted_average',
        'status',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'weighted_average' => 'float',
        'evaluated_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

  
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

   
    public function evaluator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'evaluated_by', 'employee_id');
    }
}