<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Encounter;
use Carbon\Carbon;

class EncounterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients_core1,id',
            'type' => 'required|string|in:OPD,IPD,Operating Room,Pending',
            'chief_complaint' => 'nullable|string'
        ]);
        
        $validated['doctor_id'] = auth()->id();
        $validated['status'] = 'Active';

        // Prevent double triage / duplicate active encounters
        $existingActive = Encounter::where('patient_id', $validated['patient_id'])
            ->where('status', '!=', 'Closed')
            ->exists();

        if ($existingActive) {
            return redirect()->back()->with('error', 'Patient already has an active encounter.');
        }

        $encounter = Encounter::create($validated);

        if ($encounter->type === 'IPD') {
            return redirect()->route('core1.admissions.create', ['encounter_id' => $encounter->id]);
        }

        return redirect()->back()->with('success', 'Encounter active.');
    }
}
