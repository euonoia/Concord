<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class SuccessorCandidate extends Model
{
    use HasFactory;

    protected $table = 'successor_candidates_hr2';
    public $timestamps = true; 

    protected $fillable = [
        'branch_id',
        'employee_id',
        'target_position_id',
        'readiness',
        'performance_score',
        'potential_score',
        'retention_risk',
        'effective_at',
        'development_plan',
        'is_active'
    ];

    public function position() {
        return $this->belongsTo(SuccessionPosition::class, 'branch_id', 'branch_id');
    }

    public function employee() {
        
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}