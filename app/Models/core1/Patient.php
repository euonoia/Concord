<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\core1\Appointment;
use App\Models\core1\MedicalRecord;
use App\Models\core1\Bill;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients_core1';

    // <--- Replace this $fillable with the updated version
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
        'blood_type',
        'assigned_nurse_id',
        'allergies',
        'medical_history',
        'status',
        'last_visit',
        'care_type',
        'admission_date',
        'doctor_id',
        'reason',
        'insurance_provider',
        'policy_number',
        'emergency_contact_relation'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_visit' => 'datetime',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function assignedNurse()
    {
        return $this->belongsTo(User::class, 'assigned_nurse_id');
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id'); // doctor relation
    }
}
