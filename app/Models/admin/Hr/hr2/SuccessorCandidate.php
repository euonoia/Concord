<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\SuccessionPosition;
use App\Models\Employee;

class SuccessorCandidate extends Model
{
    use HasFactory;

    protected $table = 'successor_candidates_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'position_id',        
        'target_position_id',
        'readiness',
        'performance_score',
        'potential_score',
        'retention_risk',
        'development_plan',
        'effective_at',
        'is_active',
    ];

    public function position()
    {
        return $this->belongsTo(SuccessionPosition::class, 'position_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}