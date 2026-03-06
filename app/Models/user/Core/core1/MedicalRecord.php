<?php

namespace App\Models\user\Core\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_records_core1';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'record_type',
        'diagnosis',
        'treatment',
        'prescription',
        'notes',
        'record_date',
        'attachments',
    ];

    protected $casts = [
        'record_date' => 'datetime',
        'attachments' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id');
    }

    public function patientAppointments()
{
    return $this->hasManyThrough(
        Appointment::class,
        Patient::class,
        'id',
        'patient_id',
        'patient_id',
        'id'
    );
}
}
