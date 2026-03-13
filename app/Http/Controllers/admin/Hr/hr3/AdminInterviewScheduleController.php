<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use App\Models\admin\Hr\hr1\ApplicantHr1;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr3\InterviewScheduleHr3;

class AdminInterviewScheduleController extends Controller
{
    public function index()
    {
        $departments = Department::where('is_active', 1)->get();
        $schedules = InterviewScheduleHr3::with('applicant')->latest()->get();
        return view('admin.hr3.schedule.index', compact('departments', 'schedules'));
    }

    public function getSpecializations($dept)
    {
        // Only show specializations that actually have applicants waiting for interview
        $specializations = ApplicantHr1::where('department_id', $dept)
            ->where('application_status', 'interview') 
            ->select('specialization')
            ->distinct()
            ->get();

        return response()->json($specializations);
    }

 public function getInterviewApplicants(Request $request, $dept)
{
    // This pulls 'spec' from the URL (e.g., ?spec=Pulmonology%20%2F%20...)
    $spec = $request->query('spec'); 

    $applicants = ApplicantHr1::where('department_id', $dept)
        ->where('specialization', $spec)
        ->where('application_status', 'interview') // Jayson matches this, Jane does not
        ->select('id', 'first_name', 'last_name')
        ->get();

    return response()->json($applicants);
}
    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required'
        ]);

        InterviewScheduleHr3::create([
            'applicant_id' => $request->applicant_id,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time,
            'location' => $request->location,
            'notes' => $request->notes,
            'validated_by' => Auth::id()
        ]);

        return back()->with('success', 'Interview scheduled successfully');
    }
}