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
    public $timestamps = false;

    protected $fillable = [
        'position_title',
        'branch_id',
        'criticality',
    ];

    // Relationship to candidates
    public function candidates()
    {
        return $this->hasMany(SuccessorCandidate::class, 'branch_id', 'branch_id');
    }
}
