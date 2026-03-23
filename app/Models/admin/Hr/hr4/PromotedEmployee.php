<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Model;

class PromotedEmployee extends Model
{
    protected $table = 'promoted_employees_hr4';

    protected $fillable = [
        'employee_id',
        'old_position_id',
        'new_position_id',
        'old_department_id',
        'new_department_id',
        'old_specialization',
        'new_specialization',
        'promoted_by',
        'promoted_at',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function oldPosition()
    {
        return $this->belongsTo(\App\Models\admin\Hr\hr2\DepartmentPositionTitle::class, 'old_position_id');
    }

    public function newPosition()
    {
        return $this->belongsTo(\App\Models\admin\Hr\hr2\DepartmentPositionTitle::class, 'new_position_id');
    }
}
