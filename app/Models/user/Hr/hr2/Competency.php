<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $table = 'competency_hr2';

    protected $fillable = [
        'competency_code',
        'name',
        'description',
        'rotation_order',
        'competency_group',
        'department_id',
        'specialization_name',
        'is_active'
    ];
}