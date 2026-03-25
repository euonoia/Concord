<?php

namespace App\Http\Requests\core1\Appointments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\user\Core\core1\Appointment;

use App\Models\admin\Hr\hr3\Shift;
use App\Models\Employee;

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
            'doctor_id' => ['required', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments_hr2,department_id'],
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
            // 1. Verify Doctor-Department Match
            if (!$this->isDoctorInDepartment()) {
                $validator->errors()->add('doctor_id', 'The selected doctor does not belong to the selected department/service.');
            }

            // 2. Verify Shift Schedule
            if (!$this->isWithinShift()) {
                $validator->errors()->add('appointment_time', 'The selected time is outside of the doctor\'s scheduled shift for this day.');
            }

            // 3. Verify Collision
            if ($this->hasConflict()) {
                $validator->errors()->add('appointment_time', 'The doctor is already booked for this time slot.');
            }
        });
    }

    private function isDoctorInDepartment(): bool
    {
        return Employee::where('user_id', $this->doctor_id)
            ->where('department_id', $this->department_id)
            ->exists();
    }

    private function isWithinShift(): bool
    {
        $dayOfWeek = \Carbon\Carbon::parse($this->appointment_date)->format('l');
        $employee = Employee::where('user_id', $this->doctor_id)->first();
        
        if (!$employee) return false;

        $shift = Shift::where('employee_id', $employee->employee_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$shift) return false;

        $startTime = \Carbon\Carbon::parse($shift->start_time)->format('H:i');
        $endTime = \Carbon\Carbon::parse($shift->end_time)->format('H:i');
        $appointmentTime = $this->appointment_time;

        return $appointmentTime >= $startTime && $appointmentTime < $endTime;
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
            ->whereNotIn('status', ['cancelled', 'no-show', 'declined'])
            ->exists();
    }
}
