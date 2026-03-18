<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrainingPerformance extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Connect to the main database where HR1 table exists

    protected $table = 'test.validated_training_performance_hr1'; // HR1 table name

    protected $fillable = [
        'employee_id',
        'weighted_average',
        'status',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
        'weighted_average' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true; // HR1 table has created_at and updated_at timestamps

    /**
     * Get the employee that owns the training performance.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Calculate training reward based on weighted average.
     */
    public function getTrainingRewardAttribute()
    {
        $grade = $this->weighted_average ?? 0;

        if ($grade >= 95) {
            return 5000;
        } elseif ($grade >= 90) {
            return 3000;
        } elseif ($grade >= 85) {
            return 2000;
        } elseif ($grade >= 80) {
            return 1000;
        } else {
            return 0;
        }
    }

    /**
     * Get performance level based on weighted average.
     */
    public function getPerformanceLevelAttribute()
    {
        $grade = $this->weighted_average ?? 0;

        if ($grade >= 95) {
            return 'Excellent';
        } elseif ($grade >= 90) {
            return 'Very Good';
        } elseif ($grade >= 85) {
            return 'Good';
        } elseif ($grade >= 80) {
            return 'Satisfactory';
        } else {
            return 'Below Satisfactory';
        }
    }
}