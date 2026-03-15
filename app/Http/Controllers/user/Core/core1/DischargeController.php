<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\core1\Admission;
use App\Services\core1\AdmissionService;
use Illuminate\Http\Request;

class DischargeController extends Controller
{
    protected AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function index()
    {
        $user = Auth::user();

        // Query active admissions (IPD)
        $query = Admission::with(['encounter.patient', 'encounter.doctor', 'bed.room.ward'])
            ->where('status', 'Admitted');

        // Doctor sees only their admitted patients
        if ($user->role_slug === 'doctor') {
            $query->whereHas('encounter', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        $admissions = $query->latest('admission_date')->paginate(10);

        return view('core.core1.discharge.index', compact('admissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|exists:admissions_core1,id',
            'discharge_summary' => 'required|string',
            'final_diagnosis' => 'required|string',
        ]);

        try {
            $admission = Admission::findOrFail($request->admission_id);
            
            $this->admissionService->discharge($admission, [
                'discharge_summary' => $request->discharge_summary,
                'final_diagnosis' => $request->final_diagnosis,
            ]);

            return redirect()->back()->with('success', 'Patient discharged successfully. Billing has been notified.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Discharge failed: ' . $e->getMessage());
        }
    }
}
