<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class DepartmentPositionTitle extends Model
{
    protected $table = 'department_position_titles_hr2';

    protected $fillable = [
        'department_id',
        'specialization_name',
        'position_title',
        'rank_level',
        'is_active'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
}