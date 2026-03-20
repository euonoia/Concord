<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class PatientTransferManagement extends Model
{
    protected $table = 'patient_transfer_management_core2';

    protected $fillable = [
        'transfer_id',
        'patient_id',
        'encounter_id',
        'from_location',
        'to_location',
        'transfer_date',
    ];
}
