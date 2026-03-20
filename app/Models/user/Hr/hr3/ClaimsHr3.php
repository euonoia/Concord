<?php

namespace App\Models\user\Hr\hr3;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class ClaimsHr3 extends Model
{
    protected $table = 'claims_hr3';
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    protected $fillable = [
        'claim_id',
        'employee_id',
        'claim_type',
        'description',
        'receipt_path',
        'amount',
        'status',
        'validated_by',
    ];

    // Submitter
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Approver / validator
    public function validator()
    {
        return $this->belongsTo(Employee::class, 'validated_by', 'employee_id');
    }
}