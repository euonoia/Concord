<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr3\Shift;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class AdminShiftController extends Controller
{
    private function authorizeHrAdmin() {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index() {
        $this->authorizeHrAdmin();

        // Fetch unique departments from existing employees
        $departments = Employee::select('department_id')->whereNotNull('department_id')->distinct()->get();
        
        // Fetch all shifts grouped by department for the list view
        $shifts = Shift::with(['employee'])->orderBy('created_at', 'desc')->get();

        return view('admin.hr3.shifts', compact('departments', 'shifts'));
    }

    public function getEmployeesByDept($dept_id) {
        $employees = Employee::where('department_id', $dept_id)->get()->groupBy('specialization');
        return response()->json($employees);
    }

   public function store(Request $request) {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'shift_name'  => 'required|in:Morning Shift,Afternoon Shift,Night Shift',
            'days'        => 'required|array|min:1',
        ]);

        // Define fixed hospital hours
        $hours = match($request->shift_name) {
            'Morning Shift'   => ['start' => '08:00:00', 'end' => '17:00:00'],
            'Afternoon Shift' => ['start' => '14:00:00', 'end' => '22:00:00'],
            'Night Shift'     => ['start' => '22:00:00', 'end' => '06:00:00'],
        };

        foreach ($request->days as $day) {
            Shift::create([
                'employee_id' => $request->employee_id,
                'shift_name'  => $request->shift_name,
                'day_of_week' => $day,
                'start_time'  => $hours['start'],
                'end_time'    => $hours['end'],
                'is_active'   => 1
            ]);
        }

        return redirect()->back()->with('success', 'Shifts assigned successfully.');
    }

    public function destroy($id) {
        Shift::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Shift removed.');
    }
}