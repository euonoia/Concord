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
    ];
}
