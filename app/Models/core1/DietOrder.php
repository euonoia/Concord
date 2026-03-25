<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Models\User;

class DietOrder extends Model
{
    use HasFactory;

    protected $table = 'diet_orders_core1';

    protected $fillable = [
        'encounter_id',
        'patient_id',
        'doctor_id',
        'diet_type',
        'instructions',
        'status',
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
