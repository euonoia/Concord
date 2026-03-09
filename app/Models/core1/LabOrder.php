<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $table = 'lab_orders_core1';

    protected $fillable = [
        'encounter_id',
        'test_name',
        'clinical_note',
        'status',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
