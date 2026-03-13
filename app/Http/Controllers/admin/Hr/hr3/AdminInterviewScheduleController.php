<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\admin\Hr\hr1\ApplicantHr1;
use App\Models\admin\Hr\hr2\Department;
use App\Models\admin\Hr\hr3\InterviewScheduleHr3;
use App\Mail\InterviewScheduleMail;

class AdminInterviewScheduleController extends Controller
{
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();
        $departments = Department::where('is_active', 1)->get();
        // Eager load applicant relationship
        $schedules = InterviewScheduleHr3::with(['applicant', 'validator'])->latest()->get();
        
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

        // Target 'application_id' based on your table schema
        $applicants = ApplicantHr1::where('department_id', $dept)
            ->where('specialization', $spec)
            ->where('application_status', 'interview')
            ->select('id', 'application_id', 'first_name', 'last_name')
            ->get();

        return response()->json($applicants);
    }

    public function store(Request $request)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            'applicant_id'  => 'required', // This is the primary key 'id'
            'schedule_date' => 'required|date',
            'schedule_time' => 'required'
        ]);

        $employee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        $validatedBy = $employee ? $employee->employee_id : Auth::id();

        $schedule = InterviewScheduleHr3::create([
            'applicant_id'  => $request->applicant_id, 
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time,
            'location'      => $request->location,
            'notes'         => $request->notes,
            'validated_by'  => $validatedBy
        ]);

        $applicant = ApplicantHr1::findOrFail($request->applicant_id);

        try {
            Mail::to($applicant->email)->send(
                new InterviewScheduleMail($applicant, $schedule)
            );
        } catch (\Exception $e) {
            Log::error("Mail failed: " . $e->getMessage());
        }

        return back()->with('success', 'Interview scheduled successfully.');
    }
}