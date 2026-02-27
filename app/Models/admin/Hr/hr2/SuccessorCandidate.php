<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the correct Position model used in your Controller
use App\Models\admin\Hr\hr2\DepartmentPositionTitle; 
use App\Models\Employee;

class SuccessorCandidate extends Model
{
    use HasFactory;

    protected $table = 'successor_candidates_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'position_id',        
        'employee_id',
        'department_id',
        'specialization',
        'readiness',
        'performance_score',
        'potential_score',
        'retention_risk',
        'effective_at',
        'development_plan',
        'is_active',
    ];

    /**
     * The Target Position being planned for.
     */
    public function position()
    {
        // Changed to DepartmentPositionTitle to match your Controller logic
        return $this->belongsTo(DepartmentPositionTitle::class, 'position_id', 'id');
    }

    /**
     * The Employee selected as a potential successor.
     */
    public function employee()
    {
        // We link via 'employee_id' string because that is your foreign key
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}