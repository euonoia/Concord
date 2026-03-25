<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class HiredUser extends Model
{
    protected $connection = 'mysql'; // Connect to TiDB cloud

    protected $table = 'hired_users_hr4';

    protected $fillable = [
        'hr4_job_id',
        'employee_id',
        'full_name',
        'hired_at',
    ];

    protected $casts = [
        'hired_at' => 'datetime',
    ];

    // Relationship to available job
    public function job()
    {
        return $this->belongsTo(AvailableJob::class, 'hr4_job_id', 'id');
    }

    // Relationship to employee
    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id', 'employee_id');
    }
}
