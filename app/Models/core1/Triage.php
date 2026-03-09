<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class Triage extends Model
{
    protected $table = 'triage_core1';

    protected $fillable = [
        'encounter_id',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'spo2',
        'triage_level',
        'notes',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
