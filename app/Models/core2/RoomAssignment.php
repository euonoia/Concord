<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $table = 'room_assignments_core2';

    protected $fillable = [
        'assignment_id',
        'patient_id',
        'room',
        'date_assigned',
        'encounter_id',
        'bed_id_core1',
        'ward_name',
        'bed_number',
        'mrn',
        'status',
        'patient_name',
        'triage_summary',
        'triage_level',
        'request_type',
        'source_bed_id',
    ];
}
