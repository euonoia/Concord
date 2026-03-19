<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'salary',
        'deductions',
        'net_pay',
        'pay_date',
    ];

    protected $connection = 'mysql';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
