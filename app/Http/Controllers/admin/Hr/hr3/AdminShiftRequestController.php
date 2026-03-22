<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\admin\Hr\hr3\Shift;

class AdminShiftRequestController extends Controller
{
    private function authorizeHr3()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr3') {
            abort(403, 'Unauthorized');
        }
    }

    public function index()
    {
        $this->authorizeHr3();

        $requests = DB::table('shifts_hr3 as s')
            ->join('employees as e', 's.employee_id', '=', 'e.employee_id')
            ->select(
                's.id',
                's.shift_name',
                's.day_of_week',
                's.start_time',
                's.end_time',
                's.status',
                's.created_at',
                'e.employee_id',
                'e.first_name',
                'e.last_name',
                'e.specialization'
            )
            ->where('s.status', 'pending')
            ->orderBy('s.created_at', 'desc')
            ->get();

        return view('admin.hr3.shift_requests', compact('requests'));
    }

    public function approve($id)
    {
        $this->authorizeHr3();

        DB::table('shifts_hr3')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'is_active' => 1
            ]);

        return back()->with('success', 'Shift request approved.');
    }

    public function reject($id)
    {
        $this->authorizeHr3();

        DB::table('shifts_hr3')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'is_active' => 0
            ]);

        return back()->with('success', 'Shift request rejected.');
    }
}