<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class TrainingEnroll extends Model
{
    protected $table = 'training_enrolls_hr2';

    protected $fillable = [
        'employee_id',
        'training_id',
        'status',
    ];

    public $timestamps = false;
}
