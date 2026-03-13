<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\admin\Hr\hr1\ApplicantHr1;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr3\InterviewScheduleHr3;
use App\Mail\InterviewScheduleMail;

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
        $specializations = ApplicantHr1::where('department_id', $dept)
            ->where('application_status', 'interview')
            ->select('specialization')
            ->distinct()
            ->get();

        return response()->json($specializations);
    }

    public function getInterviewApplicants(Request $request, $dept)
    {
        $spec = $request->query('spec');

        $applicants = ApplicantHr1::where('department_id', $dept)
            ->where('specialization', $spec)
            ->where('application_status', 'interview')
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

        $schedule = InterviewScheduleHr3::create([
            'applicant_id' => $request->applicant_id,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time,
            'location' => $request->location,
            'notes' => $request->notes,
            'validated_by' => Auth::id()
        ]);

   
        $applicant = ApplicantHr1::findOrFail($request->applicant_id);

        
        Mail::to($applicant->email)->send(
            new InterviewScheduleMail($applicant, $schedule)
        );

        return back()->with('success', 'Interview scheduled and email sent.');
    }
}