<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\user\Core\core1\Appointment;

class DoctorController extends Controller
{
    /**
     * Get doctors filtered by service type
     */
    public function getByServiceType(Request $request): JsonResponse
    {
        $serviceType = $request->query('service_type');

        if (!$serviceType) {
            return response()->json([
                'error' => 'Service type is required'
            ], 400);
        }

        // Define service type to specialization mapping
        $specializationMap = [
            'general_consultation' => [
                'Internal Medicine', 'General Physician', 'General Internal Medicine',
                'Rheumatology', 'Cardiology', 'Nephrology', 'Neurocritical Care'
            ],
            'acute_care' => [
                'Internal Medicine', 'General Physician', 'General Internal Medicine',
                'Neurocritical Care', 'Interventional Radiology'
            ],
            'well_child' => ['Pediatrics', 'Pediatric Cardiology', 'Pediatric Pulmonology'],
            'mental_health' => ['Psychiatry', 'Psychology'],
            'diagnostic' => ['Interventional Radiology'],
            // followup, prescription_refill - show all doctors
        ];

        // Query Employee
        $query = \App\Models\Employee::whereNotNull('specialization');

        // Apply specialization filter if mapping exists
        if (isset($specializationMap[$serviceType])) {
            $query->whereIn('specialization', $specializationMap[$serviceType]);
        }

        $doctors = $query->orderBy('first_name', 'asc')->get();

        // Fallback: If no doctors found for specific mapping, return all doctors
        if ($doctors->isEmpty()) {
            $doctors = \App\Models\Employee::whereNotNull('specialization')
                ->orderBy('first_name', 'asc')
                ->get();
        }

        return response()->json([
            'doctors' => $doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->user_id,
                    'name' => $doctor->first_name . ' ' . $doctor->last_name,
                    'specialization' => $doctor->specialization,
                ];
            })
        ]);
    }

    /**
     * Public API to check doctor availability based on shifts
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $date = $request->query('date');
        $doctorId = $request->query('doctor_id');

        if (!$date || !$doctorId) {
            return response()->json(['error' => 'Missing date or doctor'], 400);
        }

        $dayOfWeek = Carbon::parse($date)->format('l');
        
        $employee = Employee::where('user_id', $doctorId)->first();
        if (!$employee) {
            return response()->json(['error' => 'Doctor profile not found'], 404);
        }

        $shift = Shift::where('employee_id', $employee->employee_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$shift) {
            return response()->json(['slots' => [], 'message' => 'Doctor has no active shift on this day.']);
        }

        $start = Carbon::parse($date . ' ' . $shift->start_time);
        $end = Carbon::parse($date . ' ' . $shift->end_time);
        $interval = 30; // minutes

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'no-show', 'declined'])
            ->pluck('appointment_time')
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
