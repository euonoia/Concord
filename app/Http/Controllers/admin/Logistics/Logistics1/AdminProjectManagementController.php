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
     * Columns used: id, project_code, project_name, description,
     *               start_date, end_date, status, priority,
     *               budget, actual_cost, created_by, created_at, deleted_at
     */
    public function index(Request $request)
    {
        $query = DB::table('projects_logistics1')
            ->whereNull('deleted_at'); // respect soft deletes

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%$search%")
                  ->orWhere('project_code', 'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin._logistics1.project.index', compact('projects'));
    }

    /**
     * Store a new project.
     * Inserts into: projects_logistics1
     * All columns: project_code, project_name, description, start_date, end_date,
     *              status, priority, budget, actual_cost, created_by, created_at, updated_at
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
            'budget'        => $request->budget        ?? 0.00,
            'actual_cost'   => $request->actual_cost   ?? 0.00,
            'created_by'    => $employee ? $employee->employee_id : null,
            'created_at'    => now(),
            'updated_at'    => now(),
            // deleted_at left NULL (active record)
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    /**
     * Update an existing project.
     * Updates: project_name, description, start_date, end_date,
     *          status, priority, budget, actual_cost, updated_at
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
     * Sets deleted_at timestamp instead of permanently removing the row.
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