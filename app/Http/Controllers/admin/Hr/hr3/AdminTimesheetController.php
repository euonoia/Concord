<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr3\AttendanceLog;
use Illuminate\Support\Facades\Auth;

class AdminTimesheetController extends Controller
{
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'hr_admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->authorizeHrAdmin();

        // Loading the employee relationship which contains the position and specialization
        $logs = AttendanceLog::with(['employee', 'department'])
            ->orderBy('clock_in', 'desc')
            ->get();

        return view('admin.hr3.timesheet', compact('logs'));
    }
}