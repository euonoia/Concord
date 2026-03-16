<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescriptions_core1';

    protected $fillable = [
        'encounter_id',
        'medication',
        'dosage',
        'instructions',
        'duration',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function administrations()
    {
        return $this->hasMany(MedicationAdministration::class);
    }
}
