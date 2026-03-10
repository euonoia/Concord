<?php

namespace App\Http\Controllers\hr1;

use App\Http\Controllers\Controller;
use App\Models\hr1\User;
use App\Models\hr1\Application_hr1;
use Illuminate\Http\Request;

class ApplicantController_hr1 extends Controller
{
    public function index()
    {
        $applicants = User::where('role', 'candidate')->with('applications_hr1')->get();
        return response()->json($applicants);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users_hr1',
            'password' => 'required|string|min:8',
            'position' => 'required|string|max:255',
            'status' => 'sometimes|in:Applicant,Candidate,Probation,Regular,Rejected',
            'job_posting_id' => 'nullable|exists:job_postings_hr1,id',
        ]);

        $status = $validated['status'] ?? 'Applicant';
        $employeeId = now()->format('Ymd') . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        $user = User::create([
            'employee_id' => $employeeId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'position' => $validated['position'],
            'role' => 'candidate',
            'status' => $status,
            'applied_date' => now(),
        ]);

        // If job_posting_id provided, create application to link applicant to job
        if (!empty($validated['job_posting_id'])) {
            Application_hr1::create([
                'user_id' => $user->id,
                'job_posting_id' => $validated['job_posting_id'],
                'status' => $status,
                'applied_date' => now(),
            ]);
        }

        return response()->json($user->load('applications_hr1'), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Applicant,Candidate,Probation,Regular,Rejected',
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $validated['status']]);

        // Keep related applications in sync with the candidate status
        Application_hr1::where('user_id', $user->id)
            ->update(['status' => $validated['status']]);

        return response()->json($user->load('applications_hr1'));
    }

    public function exportByStatus(Request $request)
    {
        $status = $request->query('status');
        $allowed = ['Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected'];
        if (!in_array($status, $allowed, true)) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        $rows = User::where('role', 'candidate')
            ->where('status', $status)
            ->orderBy('name')
            ->get();

        $filename = 'candidates_' . strtolower($status) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['User ID', 'Employee ID', 'Name', 'Email', 'Position', 'Status', 'Applied Date']);
            foreach ($rows as $user) {
                fputcsv($out, [
                    $user->id,
                    $user->employee_id ?? $user->id,
                    $user->name,
                    $user->email,
                    $user->position,
                    $user->status,
                    optional($user->applied_date)->toDateString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {
        $applicant = User::with('applications_hr1.jobPosting_hr1')->findOrFail($id);
        return response()->json($applicant);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users_hr1,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'position' => 'sometimes|string|max:255',
            'contact_no' => 'sometimes|string|max:20',
            'status' => 'sometimes|in:Applicant,Candidate,Probation,Regular,Rejected',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        // Keep related applications in sync when status changes via this endpoint
        if (isset($validated['status'])) {
            Application_hr1::where('user_id', $user->id)
                ->update(['status' => $validated['status']]);
        }
        return response()->json($user->load('applications_hr1'));
    }
}

