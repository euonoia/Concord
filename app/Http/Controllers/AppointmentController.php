<?php

namespace App\Http\Controllers;

use App\Models\user\Core\core1\Appointment;
use App\Models\user\Core\core1\Patient;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        Log::info('AppointmentController::store called', $request->all());

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:male,female,other',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_zip' => 'required|string|max:20',
            'service_type' => 'required|string',
            'doctor_name' => 'nullable|string|max:255',
            'specialization' => 'nullable|string',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'reason_for_visit' => 'required|string',
            'insurance_provider' => 'nullable|string|max:255',
            'policy_number' => 'nullable|string|max:255',
            'medical_history_summary' => 'nullable|string',
            'terms' => 'required|accepted',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        // Backend validation: verify doctor specialization matches service type
        if (!empty($validated['doctor_name'])) {
            $specializationMap = [
                'general_consultation' => ['Internal Medicine', 'General Physician'],
                'acute_care' => ['Internal Medicine', 'General Physician'],
                'well_child' => ['Pediatrics'],
                'mental_health' => ['Psychiatry', 'Psychology'],
            ];

            if (isset($specializationMap[$validated['service_type']])) {
                $allowedSpecializations = $specializationMap[$validated['service_type']];
                
                if (isset($validated['specialization'])) {
                    $doctorSpecialization = $validated['specialization'];
                    if (!in_array($doctorSpecialization, $allowedSpecializations)) {
                        return back()
                            ->withInput()
                            ->withErrors(['doctor_name' => 'Selected doctor is not available for this service type.']);
                    }
                }
            }
        }

        DB::beginTransaction();

        try {
            // Find or create patient using unified duplicate logic
            $duplicates = Patient::detectDuplicates($validated);
            $patient = $duplicates->first();

            if (!$patient) {
                // Generate a unique patient_id for tracking (HMS-XXXX or P-XXXX format as per project)
                // We'll keep the existing P- prefix for online bookings as it distinguishes them.
                $patientIdStr = 'P-' . date('Y') . '-' . strtoupper(substr(uniqid(), -4));

                Patient::create([
                    'patient_id' => $patientIdStr,
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address_street'] . ', ' . $validated['address_city'] . ' ' . $validated['address_zip'],
                    'insurance_provider' => $validated['insurance_provider'],
                    'policy_number' => $validated['policy_number'],
                    'medical_history' => $validated['medical_history_summary'],
                    'status' => 'active',
                    'registration_status' => 'PRE_REGISTERED',
                ]);
                
                $patient = Patient::where('patient_id', $patientIdStr)->first();
            } else {
                $patient->update([
                    'insurance_provider' => $validated['insurance_provider'] ?? $patient->insurance_provider,
                    'policy_number' => $validated['policy_number'] ?? $patient->policy_number,
                ]);
            }

            // Find Doctor ID based on name.
            $doctorId = null;
            if (!empty($validated['doctor_name'])) {
                $doctorRecord = \App\Models\Employee::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $validated['doctor_name'])->first();
                if ($doctorRecord) {
                    $doctorId = $doctorRecord->user_id;
                }
            }

            // Map service_type to ENUM type ('consultation','follow-up','check-up','emergency')
            $typeEnum = 'consultation';
            if ($validated['service_type'] === 'acute_care') $typeEnum = 'emergency';
            else if ($validated['service_type'] === 'diagnostic') $typeEnum = 'check-up';
            else if ($validated['service_type'] === 'followup') $typeEnum = 'follow-up';

            // Generate unique appointment Id
            $appointmentIdStr = 'APT-' . date('Y') . '-' . strtoupper(uniqid());

            $appointment = Appointment::create([
                'appointment_id' => $appointmentIdStr,
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_date'] . ' ' . $validated['appointment_time'] . ':00',
                'type' => $typeEnum,
                'status' => 'pending',
                'reason' => $validated['reason_for_visit'],
            ]);

            DB::commit();
            Log::info('Creating new online appointment:', $appointment->toArray());

            \Illuminate\Support\Facades\Mail::raw($appointmentIdStr, function ($message) use ($validated) {
                $message->to($validated['email'])
                        ->subject('Your Appointment Reference Number');
            });

            return back()->with('success', 'Appointment booked successfully! Your reference number is in your email.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error booking appointment: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'There was an error booking your appointment. Please try again.']);
        }
    }
}
