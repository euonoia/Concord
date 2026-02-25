<?php
namespace App\Models\admin\Hr\hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\admin\Hr\hr2\Department;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentSpecialization extends Model
{
    protected $table = 'department_specializations_hr2';

    protected $fillable = [
        'dept_code', // The VARCHAR foreign key
        'specialization_name',
        'is_active',
    ];

    public function department(): BelongsTo
    {
        // 'dept_code' is the foreign key on this table
        // 'department_id' is the owner key on the Departments table
        return $this->belongsTo(Department::class, 'dept_code', 'department_id');
    }
}