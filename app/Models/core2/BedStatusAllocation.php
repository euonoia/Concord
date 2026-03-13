<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class BedStatusAllocation extends Model
{
    protected $table = 'bed_status_allocation_core2';

    protected $fillable = [
        'bed_id',
        'room_id',
        'status',
        'patient_id',
        'encounter_id',
        'bed_id_core1',
    ];
}
