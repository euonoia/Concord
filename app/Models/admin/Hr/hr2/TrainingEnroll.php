<?php

namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\TrainingSessions;

class TrainingEnroll extends Model
{
    use HasFactory;

    protected $table = 'training_enrolls_hr2';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'training_id',
        'status',
    ];

    public function training()
    {
        return $this->belongsTo(TrainingSessions::class, 'training_id', 'training_id');
    }
}
