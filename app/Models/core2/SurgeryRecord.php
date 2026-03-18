<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgeryRecord extends Model
{
    use HasFactory;

    protected $table = 'surgery_records_core2';

    protected $fillable = [
        'booking_id',
        'encounter_id',
        'surgeon_id',
        'anesthesia_type',
        'findings',
        'complications',
        'post_op_instructions',
        'start_time',
        'end_time',
    ];

    public function booking()
    {
        return $this->belongsTo(OperatingRoomBooking::class, 'booking_id');
    }
}
