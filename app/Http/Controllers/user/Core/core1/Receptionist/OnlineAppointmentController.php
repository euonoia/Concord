<?php

namespace App\Http\Controllers\user\Core\core1\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
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
                'status' => 'approved',
                'approved_by' => auth('core1')->id(),
                'approved_at' => now(),
            ]);

            // Send email
            if ($appointment->email) {
                Mail::to($appointment->email)->send(new AppointmentApprovedMail($appointment));
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
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Send email
            if ($appointment->email) {
                Mail::to($appointment->email)->send(new AppointmentRejectedMail($appointment));
            }
        });

        return back()->with('success', 'Appointment rejected and email sent.');
    }
}
