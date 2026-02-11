<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'employee_code', 'firstname', 'lastname', 'specialization', 'department_id', 'password'
    ];

    protected $hidden = ['password'];
}