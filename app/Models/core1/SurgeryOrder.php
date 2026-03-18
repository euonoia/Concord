<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\User;

class SurgeryOrder extends Model
{
    use HasFactory;

    protected $table = 'surgery_orders_core1';

    protected $fillable = [
        'encounter_id',
        'patient_id',
        'doctor_id',
        'procedure_name',
        'priority',
        'clinical_indication',
        'status',
        'core2_order_id',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class, 'encounter_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
