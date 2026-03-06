<?php

namespace App\Http\Requests\core1\Patients;

use Illuminate\Foundation\Http\FormRequest;

class OnlineBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'required|email|max:255',
            'doctor_id'        => 'required|exists:users_core1,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'type'             => 'required|string|max:100',
        ];
    }
}
