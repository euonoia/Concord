<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class TestOrder extends Model
{
    protected $table = 'test_orders_core2';

    protected $fillable = [
        'order_id',
        'patient_id',
        'test_id',
        'date_ordered',
        'core1_lab_order_id',
        'encounter_id',
        'test_name',
        'clinical_note',
        'ordering_doctor',
        'patient_name',
        'patient_mrn',
        'priority',
        'status',
        'result_data',
        'validated_by_name',
        'result_sent_at',
    ];

    protected $casts = [
        'date_ordered'   => 'date',
        'result_sent_at' => 'datetime',
    ];
}
