<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientPackageEnrollment extends Model
{
    use SoftDeletes;

    // This MUST match the table name shown in your SQL Editor exactly
    protected $table = 'patient_enrollment_core2'; 

    protected $fillable = [
        'patient_id',
        'package_id',
        'package_identifier',
        'package_description',
        'total_price',
        'amount_paid',
        'payment_status',
        'progress_percent',
        'status',
        'enrolled_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}