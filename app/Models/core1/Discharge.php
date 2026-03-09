<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class Discharge extends Model
{
    protected $table = 'discharges_core1';

    protected $fillable = [
        'encounter_id',
        'discharge_summary',
        'final_diagnosis',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
