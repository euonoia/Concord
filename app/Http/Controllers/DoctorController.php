<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            'general_consultation' => ['Internal Medicine', 'General Physician'],
            'acute_care' => ['Internal Medicine', 'General Physician'],
            'well_child' => ['Pediatrics'],
            'mental_health' => ['Psychiatry', 'Psychology'],
            // followup, prescription_refill, diagnostic - show all doctors
        ];

        // Query Employee instead of the legacy users_core1
        $query = \App\Models\Employee::whereNotNull('specialization');

        // Apply specialization filter if mapping exists
        if (isset($specializationMap[$serviceType])) {
            $query->whereIn('specialization', $specializationMap[$serviceType]);
        }

        $doctors = $query->orderBy('first_name', 'asc')->get();

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
}
