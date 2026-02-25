<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\SuccessorCandidate;

class SuccessionPosition extends Model
{
    use HasFactory;

    protected $table = 'succession_positions_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'position_id',    
        'position_title',
        'department_id',
        'department_name',
        'specialization',
        'criticality',
        'is_active',
    ];

    // Relationship to candidates
    public function candidates()
    {
        return $this->hasMany(SuccessorCandidate::class, 'position_id', 'id');
    }

    // Relationship to department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    // Scope for active positions only
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}