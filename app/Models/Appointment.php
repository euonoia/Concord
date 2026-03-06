<?php

namespace App\Models;

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
        'approved_by',
        'approved_at',
        'rejection_reason',
        'triage_note',
        'vital_signs',
    ];

    /**
     * Get the patient associated with the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor associated with the appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $maxId = self::max('id') ?? 0;
                $model->id = $maxId + 1;
            }
        });
    }
}
