<?php

namespace App\Http\Requests\core1\Patients;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'                 => 'required|string|max:255',
            'middle_name'                => 'nullable|string|max:255',
            'last_name'                  => 'required|string|max:255',
            'date_of_birth'              => 'required|date',
            'gender'                     => 'required|in:male,female,other',
            'phone'                      => 'required|string|max:20',
            'email'                      => 'required|email|unique:patients_core1,email',
            'address'                    => 'nullable|string',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_phone'    => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'blood_type'                 => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-,Unknown',
            'allergies'                  => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'insurance_provider'         => 'nullable|string|max:255',
            'policy_number'              => 'nullable|string|max:255',
        ];
    }
}
