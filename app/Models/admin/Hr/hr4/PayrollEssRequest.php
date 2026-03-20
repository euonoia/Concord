<?php

namespace App\Models\admin\Hr\hr4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Employee;
use App\Models\User;

class PayrollEssRequest extends Model
{
    protected $table = 'payroll_ess_requests_hr4';

    protected $fillable = [
        'employee_id',
        'request_type',
        'details',
        'status',
        'approved_by',
        'approval_notes',
        'requested_date',
        'approved_date',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'approved_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
