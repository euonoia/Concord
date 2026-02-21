<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\user\Hr\hr2\SuccessionPosition;

class SuccessorCandidate extends Model
{
    protected $table = 'successor_candidates_hr2';
    protected $guarded = [];
    public $timestamps = false;

    public function position()
    {
        return $this->belongsTo(
            SuccessionPosition::class,
            'branch_id',
            'branch_id'
        ); 
    }
}
