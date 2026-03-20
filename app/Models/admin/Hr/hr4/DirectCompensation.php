<?php
namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectCompensation extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Connect to TiDB cloud

    protected $table = 'direct_compensations_hr4';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'month',
        'base_salary',
        'shift_allowance',
        'overtime_pay',
        'bonus',
        'worked_hours',
        'overtime_hours',
        'night_diff_hours',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'shift_allowance' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'worked_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'night_diff_hours' => 'decimal:2',
    ];

    // Relationship to employee
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    // Relationship to user (through employee)
    public function user()
    {
        return $this->hasOneThrough(\App\Models\User::class, \App\Models\Employee::class, 'employee_id', 'id', 'employee_id', 'user_id');
    }

    // Accessor for total compensation
    public function getTotalCompensationAttribute()
    {
        $trainingReward = $this->calculateTrainingReward();
        return $this->base_salary + $this->shift_allowance + $this->overtime_pay + $this->bonus + $trainingReward;
    }

    // Calculate training reward based on HR1 data
    public function calculateTrainingReward()
    {
        // Get the latest training performance for this employee from HR1
        $latestTraining = \App\Models\admin\Hr\hr4\TrainingPerformance::where('employee_id', $this->employee_id)
            ->where('status', 'completed')
            ->orderBy('evaluated_at', 'desc')
            ->first();

        if (!$latestTraining) {
            return 0; // No training data = no reward
        }

        $weightedAverage = $latestTraining->weighted_average ?? 0;

        if ($weightedAverage >= 95) {
            return 5000;
        } elseif ($weightedAverage >= 90) {
            return 3000;
        } elseif ($weightedAverage >= 85) {
            return 2000;
        } elseif ($weightedAverage >= 80) {
            return 1000;
        } else {
            return 0;
        }
    }

    // Accessor for training reward (for display purposes)
    public function getTrainingRewardAttribute()
    {
        return $this->calculateTrainingReward();
    }
}