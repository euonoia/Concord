<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\TrainingEnroll;

class TrainingSessions extends Model
{
    use HasFactory;

    protected $table = 'training_sessions_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true; 

    protected $fillable = [
        'training_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'trainer',
        'capacity',
    ];

    // Relationship to enrolls
    public function enrolls()
    {
        return $this->hasMany(TrainingEnroll::class, 'training_id', 'training_id');
    }
}
