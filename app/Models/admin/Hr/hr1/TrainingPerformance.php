<?php

namespace App\Models\admin\Hr\hr1;

use Illuminate\Database\Eloquent\Model;

class TrainingPerformance extends Model
{
    protected $connection = 'mysql'; // Connect to TiDB cloud
    protected $table = 'validated_training_performance_hr1';

    protected $fillable = [
        'employee_id',
        'training_name',
        'grade',
        'performance_score',
        'training_date',
        'validated_by',
        'status',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'training_date' => 'date',
    ];

    // Relationship to employee
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }
}
