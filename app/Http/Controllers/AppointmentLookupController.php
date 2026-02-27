<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentLookupController extends Controller
{
    /**
     * Search for an appointment by reference number.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'appointment_reference' => 'required|string'
        ]);

        $appointment = Appointment::with(['patient', 'doctor'])
                                  ->where('appointment_id', $request->appointment_reference)
                                  ->first();

        if (!$appointment) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No appointment found with that reference number. Please check and try again.'
                ], 404);
            }
            return back()->withInput()->withErrors([
                'appointment_reference' => 'No appointment found with that reference number. Please check and try again.',
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'appointment' => [
                    'appointment_no' => $appointment->appointment_id,
                    'name' => $appointment->patient ? $appointment->patient->name : 'Unknown',
                    'doctor_name' => $appointment->doctor ? $appointment->doctor->name : 'Not assigned',
                    'appointment_date' => Carbon::parse($appointment->appointment_date)->format('F j, Y'),
                    'appointment_time' => Carbon::parse($appointment->appointment_time)->format('g:i A'),
                    'service_type' => ucwords(str_replace('_', ' ', $appointment->type)),
                    'status' => $appointment->status,
                    'cancellation_reason' => $appointment->notes,
                ]
            ]);
        }

        return redirect()->route('landing.index')
            ->with('tracked_appointment', $appointment);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Request $request, $appointmentNo)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $appointment = Appointment::where('appointment_id', $appointmentNo)->firstOrFail();

        if (in_array($appointment->status, ['cancelled', 'completed'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This appointment cannot be cancelled.'
                ], 422);
            }
            return redirect()->route('landing.index')
                ->with('tracked_appointment', $appointment)
                ->withErrors(['cancel' => 'This appointment cannot be cancelled.']);
        }

        $appointment->update([
            'status' => 'cancelled',
            'notes' => "Cancellation Reason: " . $request->cancellation_reason . "\n" . $appointment->notes,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your appointment has been successfully cancelled.',
                'appointment' => [
                    'appointment_no' => $appointment->appointment_id,
                    'status' => $appointment->status,
                    'cancellation_reason' => $request->cancellation_reason,
                ]
            ]);
        }

        return redirect()->route('landing.index')
            ->with('tracked_appointment', $appointment)
            ->with('cancel_success', 'Your appointment has been successfully cancelled.');
    }
}
