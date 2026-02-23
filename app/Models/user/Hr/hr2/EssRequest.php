<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee; 

class EssRequest extends Model
{
    protected $table = 'ess_request_hr2';
   
    protected $primaryKey = 'ess_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

   
    public function employee()
    {
        // This links the employee_id in this table to the employee_id in the employees table
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}