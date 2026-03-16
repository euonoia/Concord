<?php

namespace App\Models\core1;

use Illuminate\Database\Eloquent\Model;

class MedicationAdministration extends Model
{
    protected $table = 'medication_admin_core1';

    protected $fillable = [
        'prescription_id',
        'administered_by',
        'administered_at',
    ];

    protected $casts = [
        'administered_at' => 'datetime',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function administrator()
    {
        return $this->belongsTo(\App\Models\User::class, 'administered_by');
    }
}
