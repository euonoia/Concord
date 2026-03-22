<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class Discharge extends Model
{
    protected $table = 'discharges_core1';

    protected $fillable = [
        'encounter_id',
        'clearing_doctor_id',
        'discharge_summary',
        'final_diagnosis',
        'discharge_type',
        'condition_on_discharge',
        'follow_up_instructions',
        'follow_up_date',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function clearingDoctor()
    {
        return $this->belongsTo(\App\Models\User::class, 'clearing_doctor_id');
    }
}
