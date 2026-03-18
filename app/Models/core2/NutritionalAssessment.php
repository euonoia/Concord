<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class NutritionalAssessment extends Model
{
    protected $table = 'nutritional_assessment_consultation_core2';

    protected $fillable = [
        'enrollment_id',
        'patient_id',
        'package_id',
        'enrollment_status',
        'core1_diet_order_id',
        'encounter_id',
    ];

    public function dietOrder()
    {
        return $this->belongsTo(\App\Models\core1\DietOrder::class, 'core1_diet_order_id');
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }
}
