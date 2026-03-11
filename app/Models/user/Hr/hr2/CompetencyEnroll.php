<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class CompetencyEnroll extends Model
{
    protected $table = 'competency_enroll_hr2';

    protected $fillable = [
        'employee_id',
        'competency_code',
        'status',
        'enrolled_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime'
    ];
}