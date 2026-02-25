<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departments_hr2';

    protected $fillable = [
        'department_id', 
        'name',
        'is_active',
    ];

    /**
     * Get the specializations for the department.
     */
    public function specializations(): HasMany
    {
        
        return $this->hasMany(DepartmentSpecialization::class, 'dept_code', 'department_id');
    }
}