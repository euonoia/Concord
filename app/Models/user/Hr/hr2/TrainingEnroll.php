<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class TrainingEnroll extends Model
{
    protected $table = 'training_enrolls_hr2';
    
    // TiDB usually uses 'id' as the auto-increment primary key
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Safety: Allow these columns to be written to
    protected $fillable = [
        'employee_id',
        'training_id',
        'status',
    ];
}