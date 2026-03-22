<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $table = 'lab_orders_core1';

    protected $fillable = [
        'encounter_id',
        'patient_id',
        'doctor_id',
        'test_name',
        'clinical_note',
        'priority',
        'status',
        'sync_status',
        'core2_order_id',
        'result_data',
        'result_received_at',
    ];

    protected $casts = [
        'result_received_at' => 'datetime',
        'result_data' => 'array',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id');
    }
}
