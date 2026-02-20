<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'phone',
        'department',
        'specialization',
        'hire_date',
        'is_on_duty',
    ];

    /**
     * Get the user account associated with the employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}