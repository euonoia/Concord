<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{

    protected $table = 'competency_hr2';

    protected $fillable = [
        'competency_code',
        'name',
        'dept_code',
        'department_id',
        'specialization_name',
        'competency_group',
        'description'
    ];


public function department()
{
    return $this->belongsTo(\App\Models\admin\Hr\hr2\Department::class, 'department_id', 'department_id');
}

}