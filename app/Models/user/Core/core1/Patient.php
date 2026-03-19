<?php

namespace App\Models\user\Core\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients_core1';

    // <--- Replace this $fillable with the updated version
    protected $fillable = [
        'mrn',
        'first_name',
        'middle_name',
        'last_name',
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
        'emergency_contact_relation',
        'registration_status',
        'created_by',
        'updated_by',
        'merged_into_id',
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

    public function encounters()
    {
        return $this->hasMany(\App\Models\core1\Encounter::class, 'patient_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function assignedNurse()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_nurse_id');
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? \Illuminate\Support\Carbon::parse($this->date_of_birth)->age : null;
    }

    public function getNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'doctor_id'); // doctor relation
    }

    public static function generateMRN(): string
    {
        $year = now()->year;
        $lastNumber = static::where('mrn', 'like', "MRN-{$year}-%")
            ->selectRaw("MAX(CAST(SUBSTRING(mrn, 10, 6) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        return 'MRN-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function detectDuplicates(array $data): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('first_name', 'like', $data['first_name'] ?? '')
            ->where('last_name', 'like', $data['last_name'] ?? '')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', $data['date_of_birth'] ?? null)
            ->where('email', $data['email'] ?? '')
            ->whereNotIn('registration_status', ['MERGED'])
            ->get();
    }
}
