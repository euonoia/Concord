<?php

namespace App\Http\Requests\core1\Appointments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\core1\Appointment;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients_core1,id'],
            'doctor_id' => ['required', 'exists:users_core1,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'type' => ['required', 'string', Rule::in(['consultation', 'follow-up', 'emergency', 'surgery', 'check-up'])],
            'reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasConflict()) {
                $validator->errors()->add('appointment_time', 'The doctor is already booked for this time slot.');
            }
        });
    }

    private function hasConflict(): bool
    {
        $fullTime = $this->appointment_date . ' ' . $this->appointment_time;
        try {
            $formattedTime = \Carbon\Carbon::parse($fullTime)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return false;
        }

        return Appointment::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->where('appointment_time', $formattedTime)
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->exists();
    }
}
