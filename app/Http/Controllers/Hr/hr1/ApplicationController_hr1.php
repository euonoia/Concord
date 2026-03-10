<?php

namespace App\Http\Controllers\Hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\Hr\hr1\Application_hr1;
use App\Models\Hr\hr1\JobPosting_hr1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ApplicationController_hr1 extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_posting_id' => 'required|exists:job_postings_hr1,id',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:5120',
        ]);

        // Check if user already applied
        $existing = Application_hr1::where('user_id', Auth::id())
            ->where('job_posting_id', $validated['job_posting_id'])
            ->first();
        
        if ($existing) {
            return response()->json(['error' => 'You have already applied to this job'], 400);
        }

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documents[] = $file->store('applications_hr1', 'public');
            }
        }

        $application = Application_hr1::create([
            'user_id' => Auth::id() ?? 5, // Fallback for testing
            'job_posting_id' => $validated['job_posting_id'],
            'status' => 'Applicant',
            'applied_date' => now(),
            'documents' => $documents,
        ]);

        return response()->json($application->load('jobPosting_hr1'), 201);
    }

    public function show($id)
    {
        $application = Application_hr1::with('jobPosting_hr1', 'user')->findOrFail($id);
        return response()->json($application);
    }

    public function update(Request $request, $id)
    {
        $application = Application_hr1::findOrFail($id);
        $user = Auth::user();
        
        // Check ownership or admin role
        if ($application->user_id != Auth::id() && (!$user || $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:Applicant,Candidate,Probation,Regular,Rejected',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:5120',
        ]);

        $updateData = [];
        
        // Allow status update for admins
        if (isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
            // Also update user status if application status changes
            if ($application->user) {
                $application->user->update(['status' => $validated['status']]);
            }
        }

        // Handle documents update (only for applicants)
        if ($application->user_id == Auth::id() && $request->hasFile('documents')) {
            $documents = $application->documents ?? [];
            foreach ($request->file('documents') as $file) {
                $documents[] = $file->store('applications_hr1', 'public');
            }
            $updateData['documents'] = $documents;
        }

        $application->update($updateData);

        return response()->json($application->load('jobPosting_hr1', 'user'));
    }

    public function destroy($id)
    {
        $application = Application_hr1::findOrFail($id);
        $user = Auth::user();
        
        // Check ownership or admin role
        if ($application->user_id != Auth::id() && (!$user || $user->role !== 'admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $application->delete();
        return response()->json(['message' => 'Application deleted successfully']);
    }

    public function scheduleInterview(Request $request, $id)
    {
        $validated = $request->validate([
            'interview_date' => 'required|date',
            'interview_location' => 'required|string|max:255',
            'interview_description' => 'nullable|string',
        ]);

        $application = Application_hr1::with('user', 'jobPosting_hr1')->findOrFail($id);
        $application->update([
            'interview_date' => $validated['interview_date'],
            'interview_location' => $validated['interview_location'],
            'interview_description' => $validated['interview_description'] ?? '',
            'status' => 'Candidate',
        ]);

        if ($application->user) {
            $application->user->update(['status' => 'Candidate']);

            try {
                $jobTitle = $application->jobPosting_hr1->title ?? 'the position';
                $body = "You are invited for an interview for {$jobTitle}.\n\n"
                    . "Date & time: {$validated['interview_date']}\n"
                    . "Location: {$validated['interview_location']}\n\n"
                    . "Additional information:\n"
                    . ($validated['interview_description'] ?? '');

                Mail::raw($body, function ($message) use ($application) {
                    $message->to($application->user->email)
                        ->subject('Interview Invitation');
                });
            } catch (\Throwable $e) {
                // Don't block scheduling if mail fails in local setups
            }
        }

        return response()->json($application);
    }
}

