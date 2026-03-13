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
    /**
     * Authorization check consistent with your AdminEssController
     */
    private function authorizeHrAdmin()
    {
        // Adjust the role_slug to 'admin_hr3' or whichever role manages schedules
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3', 'admin_hr1', 'admin_hr2'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();
        $departments = Department::where('is_active', 1)->get();
        
        // Eager load applicant and validator (Employee)
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

        $applicants = ApplicantHr1::where('department_id', $dept)
            ->where('specialization', $spec)
            ->where('application_status', 'interview')
            ->select('id', 'first_name', 'last_name')
            ->get();

        return response()->json($applicants);
    }

    public function store(Request $request)
    {
        $this->authorizeHrAdmin();

        $request->validate([
            // Changed to string since you altered the column to VARCHAR
            'applicant_id'  => 'required|string', 
            'schedule_date' => 'required|date',
            'schedule_time' => 'required'
        ]);

        // Fetch the employee_id from the employees table associated with the logged-in User
        // This assumes your employees table has a 'user_id' column
        $employee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        // Fallback: If no employee record found, use the Auth ID, otherwise the employee_id
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
            // Log error but keep the schedule record
            Log::error("Mail failed: " . $e->getMessage());
        }

        return back()->with('success', 'Interview scheduled successfully.');
    }
}