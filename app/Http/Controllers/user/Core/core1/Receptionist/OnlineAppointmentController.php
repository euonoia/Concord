<?php

namespace App\Http\Controllers\user\Core\core1\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\Appointment;
use App\Models\user\Core\core1\Patient;
use App\Http\Requests\core1\Patients\OnlineBookingRequest;
use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentRejectedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OnlineAppointmentController extends Controller
{
    public function approve(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Appointment is not pending.');
        }

        DB::transaction(function () use ($appointment) {
            $appointment->update([
                'status'      => 'scheduled',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            if ($appointment->patient && $appointment->patient->email) {
                Mail::to($appointment->patient->email)->send(new AppointmentApprovedMail($appointment));
            }
        });

        return back()->with('success', 'Appointment approved and email sent.');
    }

    public function reject(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Appointment is not pending.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $appointment) {
            $appointment->update([
                'status'           => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            if ($appointment->patient && $appointment->patient->email) {
                Mail::to($appointment->patient->email)->send(new AppointmentRejectedMail($appointment));
            }
        });

        return back()->with('success', 'Appointment rejected and email sent.');
    }

    public function bookOnline(OnlineBookingRequest $request)
    {
        $validated = $request->validated();

        $duplicates = Patient::detectDuplicates([
            'phone'      => $validated['phone'] ?? '',
            'email'      => $validated['email'] ?? '',
            'first_name' => $validated['first_name'] ?? '',
            'last_name'  => $validated['last_name'] ?? '',
        ]);

        if ($duplicates->isNotEmpty()) {
            return response()->json([
                'status'     => 'duplicate',
                'duplicates' => $duplicates->map(fn($p) => [
                    'id'                  => $p->id,
                    'name'                => $p->name,
                    'registration_status' => $p->registration_status ?? 'REGISTERED',
                ]),
            ], 409);
        }

        DB::transaction(function () use ($validated) {
            $patient = Patient::create([
                'first_name'          => $validated['first_name'],
                'last_name'           => $validated['last_name'],
                'phone'               => $validated['phone'],
                'email'               => $validated['email'],
                'registration_status' => 'PRE_REGISTERED',
                'mrn'                 => null,
                'patient_id'          => null,
                'status'              => 'active',
            ]);

            Appointment::create([
                'appointment_id'   => 'APT-' . uniqid(),
                'patient_id'       => $patient->id,
                'doctor_id'        => $validated['doctor_id'],
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_date'] . ' ' . $validated['appointment_time'],
                'type'             => $validated['type'],
                'status'           => 'pending',
            ]);
        });

        return response()->json(['status' => 'success'], 201);
    }
}
