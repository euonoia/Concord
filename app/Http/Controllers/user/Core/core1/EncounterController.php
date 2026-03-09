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
            'type' => 'required|string|in:OPD,IPD,Operating Room',
            'chief_complaint' => 'nullable|string'
        ]);
        
        $validated['doctor_id'] = auth()->id();
        $validated['status'] = 'Active';

        $encounter = Encounter::create($validated);

        if ($encounter->type === 'IPD') {
            return redirect()->route('core1.admissions.create', ['encounter_id' => $encounter->id]);
        }

        return redirect()->back()->with('success', 'Encounter active.');
    }
}
