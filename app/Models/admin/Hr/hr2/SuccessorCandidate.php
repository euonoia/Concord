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
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'employee_id', 
        'readiness',
        'effective_at',
        'development_plan',
    ];

    public function position()
    {
        return $this->belongsTo(SuccessionPosition::class, 'branch_id', 'branch_id');
    }

   
    public function employee()
    {
        
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}