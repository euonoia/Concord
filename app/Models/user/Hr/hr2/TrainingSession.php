<?php

namespace App\Models\Hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\hr2\TrainingEnroll;

class TrainingSession extends Model
{
    protected $table = 'training_sessions_hr2';

    protected $primaryKey = 'training_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function enrolls()
    {
        return $this->hasMany(
            TrainingEnroll::class,
            'training_id',
            'training_id'
        );
    }
}
