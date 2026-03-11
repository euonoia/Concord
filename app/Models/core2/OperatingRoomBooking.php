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
    ];
}
