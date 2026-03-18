<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class OperatingRoomBooking extends Model
{
    protected $table = 'operating_room_booking_core2';

    protected $fillable = [
        'enrollment_id',
        'patient_id',
        'package_id',
        'enrollment_status',
        'core1_diet_order_id',
        'encounter_id',
        'core1_surgery_order_id',
        'status',
        'sync_status', // Added sync_status
        'sync_date',   // Added sync_date
    ];
}
