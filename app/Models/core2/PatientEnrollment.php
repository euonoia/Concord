<?php

namespace App\Models\core2;

use Illuminate\Database\Eloquent\Model;

class PatientEnrollment extends Model
{
    protected $table = 'patient_enrollment_core2';

    protected $fillable = [
        'package_identifier',
        'package_description',
        'price_list_node',
        'included_services_state',
    ];
}
