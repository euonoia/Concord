<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminProjectManagementController extends Controller
{
    /**
     * Display the project management dashboard.
     *
     * Tabs:
     *   - projects                    : Master list of all projects (default)
     *   - planning_scheduling         : Timeline view — start/end dates, priority, status
     *   - reporting_monitoring        : Budget vs actual cost, status & priority summaries
     *   - communication_collaboration : Project messages/notes and team member activity
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'planning_scheduling');

        // -------------------------------------------------------
        // TAB 1: PROJECTS (master list — unchanged)
        // -------------------------------------------------------
        $query = DB::table('projects_logistics1')
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%$search%")
                  ->orWhere('project_code', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 2: PLANNING & SCHEDULING
        // Shows all active/upcoming projects with timeline details.
        // Columns: project_code, project_name, start_date, end_date,
        //          status, priority, budget
        // Ordered by start_date ascending so nearest deadlines appear first.
        // -------------------------------------------------------
        $planningQuery = DB::table('projects_logistics1')
            ->whereNull('deleted_at')
            ->whereIn('status', ['planned', 'ongoing', 'on_hold'])
            ->select(
                'id',
                'project_code',
                'project_name',
                'description',
                'start_date',
                'end_date',
                'status',
                'priority',
                'budget'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');
            $planningQuery->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%$search%")
                  ->orWhere('project_code', 'like', "%$search%");
            });
        }

        if ($request->filled('planning_status')) {
            $planningQuery->where('status', $request->input('planning_status'));
        }

        if ($request->filled('planning_priority')) {
            $planningQuery->where('priority', $request->input('planning_priority'));
        }

        $planning = $planningQuery
            ->orderBy('start_date', 'asc')
            ->paginate(10, ['*'], 'planning_page')
            ->withQueryString();

        // -------------------------------------------------------
        // TAB 3: REPORTING & MONITORING
        // Shows all projects with financial and progress tracking.
        // Columns: project_code, project_name, status, priority,
        //          budget, actual_cost, start_date, end_date
        // Also includes aggregate summary stats for the dashboard cards.
        // -------------------------------------------------------
        $reportingQuery = DB::table('projects_logistics1')
            ->whereNull('deleted_at')
            ->select(
                'id',
                'project_code',
                'project_name',
                'status',
                'priority',
                'budget',
                'actual_cost',
                'start_date',
                'end_date',
                'created_at'
            );

        if ($request->filled('search')) {
            $search = $request->input('search');
            $reportingQuery->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%$search%")
                  ->orWhere('project_code', 'like', "%$search%");
            });
        }

        if ($request->filled('reporting_status')) {
            $reportingQuery->where('status', $request->input('reporting_status'));
        }

        $reporting = $reportingQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'reporting_page')
            ->withQueryString();

        // --- Summary stats for Reporting & Monitoring cards ---
        $reportingStats = DB::table('projects_logistics1')
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*)                                        AS total_projects,
                SUM(CASE WHEN status = 'planned'    THEN 1 ELSE 0 END) AS total_planned,
                SUM(CASE WHEN status = 'ongoing'    THEN 1 ELSE 0 END) AS total_ongoing,
                SUM(CASE WHEN status = 'completed'  THEN 1 ELSE 0 END) AS total_completed,
                SUM(CASE WHEN status = 'on_hold'    THEN 1 ELSE 0 END) AS total_on_hold,
                COALESCE(SUM(budget),      0)                  AS total_budget,
                COALESCE(SUM(actual_cost), 0)                  AS total_actual_cost
            ")
            ->first();

        // --- Priority breakdown for Reporting & Monitoring ---
        $priorityBreakdown = DB::table('projects_logistics1')
            ->whereNull('deleted_at')
            ->selectRaw("priority, COUNT(*) as count")
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');

        // -------------------------------------------------------
        // TAB 4: COMMUNICATION & COLLABORATION
        // No table yet — open tab placeholder.
        // Replace collect() with real queries once the table is ready.
        // -------------------------------------------------------
        $communications = collect();
        $teamMembers    = collect();
        $projectOptions = collect();

        return view('admin._logistics1.project.index', compact(
            'activeTab',
            'projects',
            'planning',
            'reporting',
            'reportingStats',
            'priorityBreakdown',
            'communications',
            'teamMembers',
            'projectOptions'
        ));
    }

    /**
     * Store a new project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_code'  => 'required|string|max:100|unique:projects_logistics1,project_code',
            'project_name'  => 'required|string|max:255',
            'description'   => 'nullable|string',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:planned,ongoing,completed,on_hold',
            'priority'      => 'required|in:low,normal,high,critical',
            'budget'        => 'nullable|numeric|min:0',
            'actual_cost'   => 'nullable|numeric|min:0',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        DB::table('projects_logistics1')->insert([
            'project_code'  => $request->project_code,
            'project_name'  => $request->project_name,
            'description'   => $request->description,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'status'        => $request->status,
            'priority'      => $request->priority,
            'budget'        => $request->budget      ?? 0.00,
            'actual_cost'   => $request->actual_cost ?? 0.00,
            'created_by'    => $employee ? $employee->employee_id : null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    /**
     * Update an existing project.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'project_name'  => 'required|string|max:255',
            'description'   => 'nullable|string',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'status'        => 'required|in:planned,ongoing,completed,on_hold',
            'priority'      => 'required|in:low,normal,high,critical',
            'budget'        => 'nullable|numeric|min:0',
            'actual_cost'   => 'nullable|numeric|min:0',
        ]);

        DB::table('projects_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'project_name'  => $request->project_name,
                'description'   => $request->description,
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date,
                'status'        => $request->status,
                'priority'      => $request->priority,
                'budget'        => $request->budget      ?? 0.00,
                'actual_cost'   => $request->actual_cost ?? 0.00,
                'updated_at'    => now(),
            ]);

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    /**
     * Soft-delete a project.
     */
    public function destroy($id)
    {
        DB::table('projects_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}