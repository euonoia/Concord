<?php

namespace App\Http\Controllers\Hr\hr1;

use App\Http\Controllers\Controller;
use App\Models\Hr\hr1\User;
use App\Models\Hr\hr1\JobPosting_hr1;
use App\Models\Hr\hr1\Application_hr1;
use App\Models\Hr\hr1\Recognition_hr1;
use App\Models\Hr\hr1\OnboardingTask_hr1;
use App\Models\Hr\hr1\EvaluationCriterion_hr1;
use App\Models\Hr\hr1\AwardCategory_hr1;
use App\Models\Hr\hr1\LearningModule_hr1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController_hr1 extends Controller
{
    public function index(Request $request)
    {
        // Get role from authenticated user, fallback to 'admin' if not authenticated
        $user = Auth::user();
        // Change FROM:
        $role = $request->get('role', $user ? $user->role : 'admin');
        
        $tab = $request->get('tab', 'dashboard');

        // Validate role
        if (!in_array($role, ['admin', 'staff', 'candidate'])) {
            $role = 'admin';
        }

        // Calculate dynamic analytics
        $totalApplicants = User::where('role', 'candidate')->count();
        $totalJobs = JobPosting_hr1::where('status', 'Open')->count();
        $totalRecognitions = Recognition_hr1::count();
        $pendingTasks = OnboardingTask_hr1::where('completed', false)->count();

        // Candidate status breakdown (based on candidate user status)
        $statusLabels = ['Applicant', 'Candidate', 'Probation', 'Regular', 'Rejected'];
        $rawStatusCounts = User::where('role', 'candidate')
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusCounts = [];
        foreach ($statusLabels as $label) {
            $statusCounts[$label] = (int) ($rawStatusCounts[$label] ?? 0);
        }
        
        // Calculate offer acceptance rate
        $offeredCount = Application_hr1::where('status', 'Offer')->count();
        $onboardingCount = Application_hr1::where('status', 'Onboarding')->count();
        $totalOffered = $offeredCount + $onboardingCount;
        $offerAcceptanceRate = $totalOffered > 0 ? round(($onboardingCount / $totalOffered) * 100) : 0;
        
        // Calculate average time to hire (days between applied_date and onboarding)
        $avgTimeToHire = Application_hr1::where('status', 'Onboarding')
            ->whereNotNull('applied_date')
            ->selectRaw('AVG(DATEDIFF(NOW(), applied_date)) as avg_days')
            ->value('avg_days') ?? 0;
        $avgTimeToHire = round($avgTimeToHire);
        
        // Calculate training compliance (completed learning modules / total assigned)
        $totalAssignedModules = \DB::table('user_learning_modules_hr1')->count();
        $completedModules = \DB::table('user_learning_modules_hr1')->where('completed', 1)->count();
        $trainingCompliance = $totalAssignedModules > 0 ? round(($completedModules / $totalAssignedModules) * 100) : 0;
        
        // Get onboarding candidates (in-process: Candidate / Probation)
        $onboardingCandidates = User::where('role', 'candidate')
            ->whereIn('status', ['Candidate', 'Probation'])
            ->with(['applications_hr1' => function($query) {
                $query->whereIn('status', ['Candidate', 'Probation']);
            }])
            ->get()
            ->map(function($candidate) {
                // Get applications with onboarding status
                $applications = $candidate->applications_hr1->filter(function($app) {
                    return in_array($app->status, ['Candidate', 'Probation']);
                });
                
                // Get tasks for each application
                foreach ($applications as $app) {
                    $app->tasks = DB::table('applicant_tasks_hr1')
                        ->where('user_id', $candidate->id)
                        ->where('job_posting_id', $app->job_posting_id)
                        ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
                        ->select('applicant_tasks_hr1.*', 'tasks_hr1.title as task_title', 'tasks_hr1.description as task_description')
                        ->get();
                }
                
                // If candidate has applications, add job info
                if ($applications->count() > 0) {
                    $firstApp = $applications->first();
                    $candidate->job_title = $firstApp->jobPosting_hr1->title ?? null;
                    $candidate->job_id = $firstApp->job_posting_id ?? null;
                    $candidate->application_id = $firstApp->id ?? null;
                }
                
                return $candidate;
            });
        
        // Get candidate tasks for admin view
        $candidateTasks = DB::table('applicant_tasks_hr1')
            ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
            ->leftJoin('users_hr1', 'applicant_tasks_hr1.user_id', '=', 'users_hr1.id')
            ->leftJoin('job_postings_hr1', 'applicant_tasks_hr1.job_posting_id', '=', 'job_postings_hr1.id')
            ->select('applicant_tasks_hr1.*', 
                     'tasks_hr1.title as task_title', 
                     'tasks_hr1.description as task_description',
                     'users_hr1.name as user_name',
                     'job_postings_hr1.title as job_title')
            ->whereIn('users_hr1.status', ['Candidate', 'Probation'])
            ->get();
        
        // Get task sets and question sets from database
        $taskSets = DB::table('task_sets_hr1')
            ->leftJoin('tasks_hr1', 'task_sets_hr1.id', '=', 'tasks_hr1.task_set_id')
            ->select('task_sets_hr1.*', DB::raw('GROUP_CONCAT(tasks_hr1.id) as task_ids'))
            ->groupBy('task_sets_hr1.id')
            ->get()
            ->map(function($ts) {
                $ts->tasks = DB::table('tasks_hr1')->where('task_set_id', $ts->id)->get();
                return $ts;
            });
        
        $questionSets = DB::table('question_sets_hr1')
            ->leftJoin('questions_hr1', 'question_sets_hr1.id', '=', 'questions_hr1.question_set_id')
            ->select('question_sets_hr1.*', DB::raw('GROUP_CONCAT(questions_hr1.id) as question_ids'))
            ->groupBy('question_sets_hr1.id')
            ->get()
            ->map(function($qs) {
                $qs->questions = DB::table('questions_hr1')->where('question_set_id', $qs->id)->get();
                $posting_id = $qs->job_posting_id ?? null;
                $qs->job_title = $posting_id
                    ? optional(DB::table('job_postings_hr1')->where('id', $posting_id)->first())->title
                    : null;
                return $qs;
            });

        // Build assessment scores from applicant_responses_hr1 (user_id, question_set_id, score)
        $assessmentScores = DB::table('applicant_responses_hr1')
            ->join('users_hr1', 'applicant_responses_hr1.user_id', '=', 'users_hr1.id')
            ->select(
                'applicant_responses_hr1.user_id',
                'applicant_responses_hr1.question_set_id',
                'users_hr1.name',
                'users_hr1.email',
                DB::raw('SUM(COALESCE(CAST(applicant_responses_hr1.response_value AS DECIMAL(10,2)), 0)) as total_score')
            )
            ->groupBy('applicant_responses_hr1.user_id', 'applicant_responses_hr1.question_set_id', 'users_hr1.name', 'users_hr1.email')
            ->get()
            ->map(function($row) {
                $score = is_numeric($row->total_score) ? round((float) $row->total_score, 2) : 0;
                return (object)[
                    'user_id' => $row->user_id,
                    'id' => $row->user_id,
                    'question_set_id' => $row->question_set_id,
                    'name' => $row->name,
                    'email' => $row->email,
                    'total_score' => $score,
                    'score' => $score,
                ];
            });
        
        // Get admin profile
        $adminProfile = User::where('role', 'admin')->first();
        
        // Get current candidate user (for candidate role)
        $currentCandidate = null;
        $myApplications = collect();
        $myTasks = collect();
        $myQuestionSets = collect();
        $myLearningModules = collect();
        $candidateProfile = null;
        $candidateJob = null;

        if ($role === 'candidate') {
            // Get the first candidate as example, or use authenticated user
            $currentCandidate = $user && $user->role === 'candidate' ? $user : User::where('role', 'candidate')->first();
            
            if ($currentCandidate) {
                $candidateProfile = $currentCandidate;
                $myApplications = Application_hr1::where('user_id', $currentCandidate->id)
                    ->with('jobPosting_hr1')
                    ->get();
                
                // Get tasks for this candidate
                $myTasks = OnboardingTask_hr1::where('user_id', $currentCandidate->id)->get();
                
                // Get applicant tasks (from applicant_tasks_hr1)
                $applicantTasks = DB::table('applicant_tasks_hr1')
                    ->where('applicant_tasks_hr1.user_id', $currentCandidate->id)
                    ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
                    ->leftJoin('job_postings_hr1', 'applicant_tasks_hr1.job_posting_id', '=', 'job_postings_hr1.id')
                    ->select('applicant_tasks_hr1.*', 'tasks_hr1.title as task_title', 'tasks_hr1.description as task_description', 
                             'job_postings_hr1.title as job_title', 'job_postings_hr1.id as job_id')
                    ->get();
                
                // Candidate's primary job (set when admin created user - first application in Candidate/Probation)
                $primaryApp = $myApplications->whereIn('status', ['Candidate', 'Probation'])->first() ?? $myApplications->first();
                $candidateJob = null;
                if ($primaryApp && $primaryApp->jobPosting_hr1) {
                    $candidateJob = (object)[
                        'id' => $primaryApp->job_posting_id,
                        'title' => $primaryApp->jobPosting_hr1->title,
                        'department' => $primaryApp->jobPosting_hr1->department ?? null,
                    ];
                }

                // Get question sets assigned to candidate
                $myQuestionSets = DB::table('question_sets_hr1')
                    ->where('is_active', true)
                    ->leftJoin('questions_hr1', 'question_sets_hr1.id', '=', 'questions_hr1.question_set_id')
                    ->select('question_sets_hr1.*')
                    ->groupBy('question_sets_hr1.id')
                    ->get()
                    ->map(function($qs) use ($currentCandidate) {
                        $qs->questions = DB::table('questions_hr1')->where('question_set_id', $qs->id)->get();
                        // Check if candidate has responses
                        $qs->responses = DB::table('applicant_responses_hr1')
                            ->where('user_id', $currentCandidate->id)
                            ->where('question_set_id', $qs->id)
                            ->get();
                        $qs->progress = $qs->questions->count() > 0 
                            ? round(($qs->responses->count() / $qs->questions->count()) * 100) 
                            : 0;
                        $qs->completed = $qs->responses->count() === $qs->questions->count() && $qs->questions->count() > 0;
                        // Compute score from numeric response_value (e.g. rating 1-5)
                        $qs->score = $qs->responses->sum(function ($r) {
                            $v = $r->response_value;
                            return is_numeric($v) ? (float) $v : 0;
                        });
                        return $qs;
                    });
                
                // Get learning modules for candidate
                $myLearningModules = DB::table('user_learning_modules_hr1')
                    ->where('user_id', $currentCandidate->id)
                    ->join('learning_modules_hr1', 'user_learning_modules_hr1.learning_module_id', '=', 'learning_modules_hr1.id')
                    ->select('learning_modules_hr1.*', 'user_learning_modules_hr1.completed', 'user_learning_modules_hr1.id as assignment_id')
                    ->get();
            }
        }

        $data = [
            'role' => $role,
            'activeTab' => $tab,
            'applicants' => User::where('role', 'candidate')->with('applications_hr1')->get(),
            'jobs' => JobPosting_hr1::where('status', 'Open')->with('applications_hr1.user')->get(),
            'recognitions' => Recognition_hr1::latest()->get(),
            'tasks' => OnboardingTask_hr1::all(),
            'awardCategories' => AwardCategory_hr1::all(),
            'evalCriteria' => EvaluationCriterion_hr1::all(),
            'availableModules' => LearningModule_hr1::all(),
            'taskSets' => $taskSets,
            'questionSets' => $questionSets,
            'assessmentScores' => $assessmentScores,
            'onboardingCandidates' => $onboardingCandidates,
            'candidateTasks' => $candidateTasks ?? collect(),
            'adminProfile' => $adminProfile,
            // Candidate-specific data
            'currentCandidate' => $currentCandidate,
            'myApplications' => $myApplications,
            'myTasks' => $myTasks,
            'applicantTasks' => $applicantTasks ?? collect(),
            'myQuestionSets' => $myQuestionSets,
            'myLearningModules' => $myLearningModules,
            'candidateProfile' => $candidateProfile,
            'candidateJob' => $candidateJob ?? null,
            // Analytics
            'analytics' => [
                'totalApplicants' => $totalApplicants,
                'offerAcceptanceRate' => $offerAcceptanceRate,
                'avgTimeToHire' => $avgTimeToHire,
                'trainingCompliance' => $trainingCompliance,
                'totalJobs' => $totalJobs,
                'pendingTasks' => $pendingTasks,
                'totalRecognitions' => $totalRecognitions,
                'statusCounts' => $statusCounts,
            ],
        ];

        // Return role-specific dashboard view
        return view("hr.hr1.user_hr1.{$role}.dashboard", $data);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users_hr1,email,' . $user->id,
            'contact_no' => 'sometimes|string|max:20',
            'date_of_employment' => 'sometimes|date',
            'profile_picture' => 'sometimes|string', // For now, accept data URL
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    public function updateCandidateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'candidate') {
            // Fallback: get first candidate for testing
            $user = User::where('role', 'candidate')->first();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users_hr1,email,' . $user->id,
            'contact_no' => 'sometimes|string|max:20',
            'position' => 'sometimes|string|max:255',
            'profile_picture' => 'sometimes|string', // For now, accept data URL
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    public function updateCandidateStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'candidate') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Candidate,Probation,Regular,Rejected',
        ]);

        $current = $user->status ?? 'Candidate';
        $next = $validated['status'];

        if ($current === 'Candidate' && !in_array($next, ['Probation', 'Rejected'], true)) {
            return response()->json(['error' => 'Invalid transition'], 422);
        }
        if ($current === 'Probation' && !in_array($next, ['Regular', 'Probation', 'Rejected'], true)) {
            return response()->json(['error' => 'Invalid transition'], 422);
        }

        $user->update(['status' => $next]);

        return response()->json($user);
    }
}

