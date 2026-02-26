<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments_core1';

   protected $fillable = [
    'appointment_id',
    'patient_id',
    'doctor_id',
    'appointment_date',
    'appointment_time',
    'type',
    'status',
    'notes',
    'reason',
    'triage_note',
    'vital_signs'
];


    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\core1\User::class, 'doctor_id');
    }
}
