<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;

class PayrollRequestHr2 extends Model
{
    protected $table = 'payroll_request_hr2';

    protected $fillable = [
        'ess_id',
        'employee_id',
        'salary',
        'details',
        'status'

    ];
}