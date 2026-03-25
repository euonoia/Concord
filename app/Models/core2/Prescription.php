<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescriptions_core2';

    protected $fillable = [
        'prescription_id',
        'patient_id',
        'doctor_id',
        'date',
        'drug_id',
        'core1_prescription_id',
        'dispensed_at',
        'pharmacist_id',
        'status',
        'quantity',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];
}
