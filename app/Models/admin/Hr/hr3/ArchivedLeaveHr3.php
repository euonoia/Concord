<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class ArchivedLeaveHr3 extends Model
{
    protected $table = 'archived_leave_hr3';

    public $timestamps = false;

    protected $fillable = [
        'original_request_id',
        'employee_id',
        'leave_type',
        'details',
        'start_date',
        'end_date',
        'final_status',
        'processed_by',
        'archived_at'
    ];

    public function employee()
    {
        return $this->belongsTo(
            Employee::class,
            'employee_id',
            'employee_id'
        );
    }

    public function handler()
    {
        return $this->belongsTo(
            Employee::class,
            'processed_by',
            'employee_id'
        );
    }
}