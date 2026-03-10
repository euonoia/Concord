<?php

namespace App\Http\Controllers\hr1;

use App\Http\Controllers\Controller;
use App\Models\hr1\OnboardingTask_hr1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnboardingController_hr1 extends Controller
{
    public function index()
    {
        $tasks = OnboardingTask_hr1::all();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'category' => 'required|in:Pre-onboarding,Orientation,IT Setup,Training',
            'assigned_to' => 'required|in:admin,staff,candidate',
            'user_id' => 'nullable|exists:users_hr1,id',
        ]);

        $task = OnboardingTask_hr1::create($validated);
        return response()->json($task, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $task = OnboardingTask_hr1::findOrFail($id);
        $task->update(['completed' => $validated['completed']]);
        return response()->json($task);
    }

    public function taskSets()
    {
        $taskSets = DB::table('task_sets_hr1')
            ->leftJoin('tasks_hr1', 'task_sets_hr1.id', '=', 'tasks_hr1.task_set_id')
            ->select('task_sets_hr1.*')
            ->groupBy('task_sets_hr1.id')
            ->get()
            ->map(function($ts) {
                $ts->tasks = DB::table('tasks_hr1')->where('task_set_id', $ts->id)->get();
                return $ts;
            });
        return response()->json($taskSets);
    }

    public function storeTaskSet(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $taskSet = DB::table('task_sets_hr1')->insertGetId([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $taskSetData = DB::table('task_sets_hr1')->where('id', $taskSet)->first();
        $taskSetData->tasks = [];
        
        return response()->json($taskSetData, 201);
    }

    public function updateTaskSet(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::table('task_sets_hr1')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        $taskSet = DB::table('task_sets_hr1')->where('id', $id)->first();
        $taskSet->tasks = DB::table('tasks_hr1')->where('task_set_id', $id)->get();
        
        return response()->json($taskSet);
    }

    public function destroyTaskSet($id)
    {
        DB::table('task_sets_hr1')->where('id', $id)->delete();
        return response()->json(['message' => 'Task set deleted successfully']);
    }

    public function applicantTasks()
    {
        $tasks = DB::table('applicant_tasks_hr1')
            ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
            ->leftJoin('users_hr1', 'applicant_tasks_hr1.user_id', '=', 'users_hr1.id')
            ->leftJoin('job_postings_hr1', 'applicant_tasks_hr1.job_posting_id', '=', 'job_postings_hr1.id')
            ->select('applicant_tasks_hr1.*', 
                     'tasks_hr1.title as task_title', 
                     'tasks_hr1.description as task_description',
                     'users_hr1.name as user_name',
                     'job_postings_hr1.title as job_title')
            ->get();
        return response()->json($tasks);
    }

    /**
     * Create a new task/requirement for a specific candidate and job.
     */
    public function storeApplicantTask(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users_hr1,id',
            'job_posting_id' => 'required|exists:job_postings_hr1,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        // Create the base task definition
        $taskId = DB::table('tasks_hr1')->insertGetId([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'task_set_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attach task to the candidate for this job
        $applicantTaskId = DB::table('applicant_tasks_hr1')->insertGetId([
            'user_id' => $validated['user_id'],
            'job_posting_id' => $validated['job_posting_id'],
            'task_id' => $taskId,
            'due_date' => isset($validated['due_date']) ? $validated['due_date'] : null,
            'completed' => false,
            'completed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task = DB::table('applicant_tasks_hr1')
            ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
            ->where('applicant_tasks_hr1.id', $applicantTaskId)
            ->select(
                'applicant_tasks_hr1.*',
                'tasks_hr1.title as task_title',
                'tasks_hr1.description as task_description'
            )
            ->first();

        return response()->json($task, 201);
    }

    public function updateApplicantTaskStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $task = DB::table('applicant_tasks_hr1')->where('id', $id)->first();
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        DB::table('applicant_tasks_hr1')
            ->where('id', $id)
            ->update([
                'completed' => $validated['completed'],
                'completed_at' => $validated['completed'] ? now() : null,
                'updated_at' => now()
            ]);

        $updatedTask = DB::table('applicant_tasks_hr1')
            ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
            ->where('applicant_tasks_hr1.id', $id)
            ->select('applicant_tasks_hr1.*', 'tasks_hr1.title as task_title')
            ->first();

        return response()->json($updatedTask);
    }

    public function updateApplicantTask(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $task = DB::table('applicant_tasks_hr1')->where('id', $id)->first();
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Update the task in tasks_hr1 table
        if (isset($validated['title']) || isset($validated['description'])) {
            DB::table('tasks_hr1')
                ->where('id', $task->task_id)
                ->update(array_filter([
                    'title' => $validated['title'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'updated_at' => now()
                ]));
        }

        // Update applicant task status and due_date if provided
        $taskUpdate = ['updated_at' => now()];
        if (isset($validated['completed'])) {
            $taskUpdate['completed'] = $validated['completed'];
            $taskUpdate['completed_at'] = $validated['completed'] ? now() : null;
        }
        if (array_key_exists('due_date', $validated)) {
            $taskUpdate['due_date'] = $validated['due_date'];
        }
        if (count($taskUpdate) > 1) {
            DB::table('applicant_tasks_hr1')->where('id', $id)->update($taskUpdate);
        }

        $updatedTask = DB::table('applicant_tasks_hr1')
            ->leftJoin('tasks_hr1', 'applicant_tasks_hr1.task_id', '=', 'tasks_hr1.id')
            ->where('applicant_tasks_hr1.id', $id)
            ->select('applicant_tasks_hr1.*', 'tasks_hr1.title as task_title', 'tasks_hr1.description as task_description')
            ->first();

        return response()->json($updatedTask);
    }

    public function deleteApplicantTask($id)
    {
        $task = DB::table('applicant_tasks_hr1')->where('id', $id)->first();
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        DB::table('applicant_tasks_hr1')->where('id', $id)->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}

