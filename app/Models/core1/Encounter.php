<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\core1\Triage;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;

class Encounter extends Model
{
    /** @use HasFactory<\Database\Factories\Core1\EncounterFactory> */
    use HasFactory;

    protected $table = 'encounters_core1';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'type',
        'status',
        'chief_complaint',
    ];

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id');
    }

    public function admission()
    {
        return $this->hasOne(Admission::class);
    }

    public function triage()
    {
        return $this->hasOne(Triage::class, 'encounter_id')->latestOfMany();
    }

    public function triages()
    {
        return $this->hasMany(Triage::class, 'encounter_id')->latest();
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class, 'encounter_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'encounter_id');
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'encounter_id');
    }
}
