<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
