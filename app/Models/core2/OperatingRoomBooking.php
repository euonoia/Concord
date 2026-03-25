<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class OperatingRoomBooking extends Model
{
    protected $table = 'operating_room_booking_core2';

    protected $fillable = [
        'operating_booking_id',
        'patient_id',
        'booking_date',
        'surgeon_id',
        'core1_surgery_order_id',
        'encounter_id',
        'status',
        'proposed_date',
        'proposed_time',
    ];

    public function surgeryOrder()
    {
        return $this->belongsTo(\App\Models\core1\SurgeryOrder::class, 'core1_surgery_order_id');
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }
}
