<?php

namespace App\Models\admin\Financials;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class ReimburseFinancial extends Model
{

    protected $table = 'reimburse_financials';

    protected $fillable = [
        'claim_id',
        'employee_id',
        'claim_type',
        'amount',
        'description',
        'receipt_path',
        'validated_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','employee_id');
    }
}