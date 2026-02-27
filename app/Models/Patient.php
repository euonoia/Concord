<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients_core1';

    protected $fillable = [
        'patient_id',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'blood_type',
        'allergies',
        'medical_history',
        'insurance_provider',
        'policy_number',
        'status',
        'care_type',
        'assigned_nurse_id',
        'admission_date',
        'doctor_id',
        'reason',
        'last_visit',
    ];

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
