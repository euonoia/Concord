<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultations_core1';

    protected $fillable = [
        'encounter_id',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'doctor_notes',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
