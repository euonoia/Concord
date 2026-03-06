<?php
namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectCompensation extends Model
{
    use HasFactory;

    protected $table = 'direct_compensations_hr4';
    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'month',
        'base_salary',
        'shift_allowance',
        'overtime_pay',
        'bonus',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'shift_allowance' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
    ];

    // Relationship to employee
    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

  
}