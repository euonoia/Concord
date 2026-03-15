<?php

namespace App\Http\Controllers\user\Core\core1\IPD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\core1\Admission;
use App\Models\core1\Encounter;
use App\Models\core1\Bed;
use App\Models\core1\Ward;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Services\core1\AdmissionService;

class AdmissionController extends Controller
{
    protected $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

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
            $encounter = Encounter::findOrFail($validated['encounter_id']);
            $bed = Bed::findOrFail($validated['bed_id']);

            $this->admissionService->admit($encounter, $bed);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Patient successfully admitted.'
                ]);
            }

            return redirect()->route('core1.inpatient.index')->with('success', 'Patient successfully admitted.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admission failed: ' . $e->getMessage()
                ], 400);
            }
            return back()->with('error', 'Admission failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle patient clinical discharge request.
     */
    public function requestDischarge(Request $request, Admission $admission)
    {
        $validated = $request->validate([
            'discharge_summary' => 'required|string',
            'final_diagnosis' => 'required|string',
        ]);

        try {
            $this->admissionService->requestDischarge($admission, $validated);

            return redirect()->route('core1.inpatient.index')->with('success', 'Discharge approved by doctor. Waiting for financial clearance.');
        } catch (\Exception $e) {
            return back()->with('error', 'Discharge initiation failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle patient final release.
     */
    public function finalizeDischarge(Request $request, Admission $admission)
    {
        try {
            $this->admissionService->finalizeDischarge($admission);

            return redirect()->route('core1.inpatient.index')->with('success', 'Patient successfully released and bed marked as available.');
        } catch (\Exception $e) {
            return back()->with('error', 'Final release failed: ' . $e->getMessage());
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
