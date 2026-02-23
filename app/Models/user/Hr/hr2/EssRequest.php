<?php

namespace App\Models\user\Hr\hr2;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee; // Point to your main Employee model

class EssRequest extends Model
{
    protected $table = 'ess_request_hr2';
    // If 'id' is your auto-incrementing column, keep it. 
    // If you want 'ess_id' (like ESS0001) to be the primary, keep your current settings.
    protected $primaryKey = 'ess_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    /**
     * Define the relationship to the Employee model.
     */
    public function employee()
    {
        // This links the employee_id in this table to the employee_id in the employees table
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}