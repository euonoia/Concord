<?php

namespace App\Http\Controllers\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\Appointment;
use App\Models\core1\Patient;
use App\Models\core1\User;
use App\Models\core1\WaitingList;
use App\Http\Requests\core1\Appointments\StoreAppointmentRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
 public function index(Request $request)
{
    $view = $request->get('view', 'month');
    $dateParam = $request->get('date', now()->format('Y-m-d'));
    $date = Carbon::parse($dateParam);
    $currentDate = $date->format('Y-m-d');

    $query = Appointment::with(['patient', 'doctor']);

    // Doctor sees ONLY own appointments
    if (auth()->user()->role === 'doctor') {
        $query->where('doctor_id', auth()->id());
    }

    if ($view === 'month') {
        $query->whereBetween('appointment_time', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth()
        ]);
    }

    elseif ($view === 'week') {
        $query->whereBetween('appointment_time', [
            $date->copy()->startOfWeek(),
            $date->copy()->endOfWeek()
        ]);
    }

    elseif ($view === 'day') {
        $query->whereBetween('appointment_time', [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        ]);
    }

    $appointments = $query
        ->orderBy('appointment_time')
        ->get();

    return view(
        'core1.appointments.index',
        compact('appointments', 'view', 'currentDate')
    );
}

public function create()
{
    $patients = Patient::all();

    $patients->each(function($patient) {
        // Patient is considered "already booked" only if they have an active appointment
        // Active appointments: scheduled, accepted, pending
        $patient->hasUpcomingAppointment = $patient->appointments()
            ->whereIn('status', ['scheduled', 'accepted', 'pending', 'waiting', 'in_consultation', 'consulted', 'completed'])
            ->exists();
    });

    $doctors = User::where('role', 'doctor')->get();

    return view('core.core1.appointments.create', compact('patients', 'doctors'));
}



    public function store(StoreAppointmentRequest $request)
{
    $validated = $request->validated();

    $fullTime = $validated['appointment_date'] . ' ' . $validated['appointment_time'];
    $validated['appointment_time'] = Carbon::parse($fullTime)->format('Y-m-d H:i:s');
    
    $validated['appointment_id'] = 'APT-' . uniqid();
    $validated['status'] = 'pending'; // <-- pending instead of scheduled

    Appointment::create($validated);

    return redirect()->route('core1.appointments.index')->with('success', 'Appointment booked successfully.');
}

    public function show(Appointment $appointment)
{
    $appointment->load(['patient', 'doctor']);
    $patients = Patient::all();
    $doctors = User::where('role', 'doctor')->get();
    return view('core.core1.appointments.show', compact('appointment','patients','doctors'));
}


    public function update(Request $request, Appointment $appointment)
{
    // Only doctor can update status
    if (auth()->user()->role === 'doctor') {
        $validated = $request->validate([
            'status' => 'required|in:pending,scheduled,declined,completed,cancelled,no-show'
        ]);
        $appointment->update($validated);
        return redirect()->back()->with('success', 'Status updated successfully.');
    } else {
        // Admin/Receptionist can edit info except status
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients_core1,id',
            'doctor_id' => 'required|exists:users_core1,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'type' => 'required|string',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        $appointment->update($validated);
        return redirect()->route('core1.appointments.show', $appointment)
         ->with('success', 'Appointment updated successfully.');


    }
}

public function accept(Appointment $appointment)
{
    $appointment->update(['status' => 'scheduled']);
    return redirect()->back()->with('success', 'Appointment accepted.');
}

public function decline(Appointment $appointment)
{
    $appointment->update(['status' => 'declined']);

    // 🔄 Clear inpatient/outpatient info if patient was admitted
    $appointment->patient->update([
        'care_type' => null,
        'doctor_id' => null,      // optional: unassign doctor
        'admission_date' => null,
        'reason' => null,
    ]);

    return redirect()->back()->with('success', 'Appointment declined and patient moved back to general list.');
}


    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        
        // Check waiting list
        $waiting = WaitingList::where('doctor_id', $appointment->doctor_id)
            ->where('status', 'pending')
            ->where(function($q) use ($appointment) {
                $q->whereNull('preferred_date')
                  ->orWhere('preferred_date', $appointment->appointment_date);
            })
            ->first();
            
        $msg = 'Appointment cancelled.';
        if ($waiting) {
            $waiting->update(['status' => 'notified']);
            $msg .= " Slot opened! Notified waiting patient: {$waiting->patient->name}.";
        }

        return redirect()->route('core1.appointments.index')->with('success', $msg);
    }

    /**
     * API Method to check availability
     */
    public function checkAvailability(Request $request)
    {
        $date = $request->get('date');
        $doctorId = $request->get('doctor_id');

        if (!$date || !$doctorId) {
            return response()->json(['error' => 'Missing date or doctor'], 400);
        }

        // Assume 09:00 to 17:00
        $start = Carbon::parse($date . ' 09:00:00');
        $end = Carbon::parse($date . ' 17:00:00');
        $interval = 30; // minutes

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no-show'])
            ->pluck('appointment_time') // This fetches DateTime objects or strings
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        $slots = [];
        $current = $start->copy();

        while ($current->lt($end)) {
            $timeStr = $current->format('H:i');
            $status = in_array($timeStr, $bookedSlots) ? 'booked' : 'available';
            
            $slots[] = [
                'time' => $timeStr,
                'status' => $status
            ];
            
            $current->addMinutes($interval);
        }

        return response()->json(['slots' => $slots]);
    }
}
