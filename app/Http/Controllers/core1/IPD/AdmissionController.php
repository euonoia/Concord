<?php

namespace App\Http\Controllers\core1\IPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Admission;
use App\Models\core1\Encounter;
use App\Models\core1\Bed;
use App\Models\core1\Ward;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function create(Request $request)
    {
        $encounterId = $request->query('encounter_id');
        $encounter = Encounter::with('patient')->findOrFail($encounterId);
        
        $wards = Ward::with(['rooms.beds' => function($query) {
            $query->where('status', 'Available');
        }])->get();

        return view('core.core1.ipd.admissions.create', compact('encounter', 'wards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'encounter_id' => 'required|exists:encounters_core1,id',
            'bed_id' => 'required|exists:beds_core1,id',
        ]);

        try {
            DB::beginTransaction();

            $bed = Bed::where('id', $validated['bed_id'])->lockForUpdate()->firstOrFail();
            if ($bed->status !== 'Available') {
                return back()->withInput()->with('error', 'Selected bed is no longer available.');
            }

            $admission = Admission::create([
                'encounter_id' => $validated['encounter_id'],
                'bed_id' => $validated['bed_id'],
                'admission_date' => Carbon::now(),
                'status' => 'Admitted'
            ]);

            $bed->update(['status' => 'Occupied']);
            
            Encounter::where('id', $validated['encounter_id'])->update(['type' => 'IPD']);

            DB::commit();

            return redirect()->route('core1.ipd.dashboard')->with('success', 'Patient successfully admitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Admission failed: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $admissions = Admission::with(['encounter.patient', 'bed.room.ward'])
            ->where('status', 'Admitted')
            ->get();
            
        return view('core.core1.ipd.dashboard', compact('admissions'));
    }
}
