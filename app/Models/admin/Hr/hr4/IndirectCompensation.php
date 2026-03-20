<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class IndirectCompensation extends Model
{
    use HasFactory;

    protected $table = 'indirect_compensations_hr4';

    protected $fillable = [
        'employee_id',
        'month',
        'benefit_name',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
