<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Hr\hr2\EssRequest;
use App\Models\admin\Hr\hr3\ArchivedLeaveHr3;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminLeaveManagementController extends Controller
{
    private function authorizeHr3Admin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3'])) {
            abort(403, 'Unauthorized action for HR3 Leave Management.');
        }
    }

    public function index()
    {
        $this->authorizeHr3Admin();

        $requests = EssRequest::with('employee')
            ->where('type', 'Leave')
            ->whereNotIn('status', ['approved','rejected','closed'])
            ->orderBy('created_at','desc')
            ->get();

        $archived = ArchivedLeaveHr3::with(['employee','handler'])
            ->orderBy('archived_at','desc')
            ->get();

        return view('admin.hr3.leave.index', compact('requests','archived'));
    }

    public function updateStatus(Request $request, $id)
    {
        $this->authorizeHr3Admin();

        $request->validate([
            'status' => 'required|in:approved,rejected,closed'
        ]);

        try {

            return DB::transaction(function () use ($request,$id) {

                $ess = EssRequest::findOrFail($id);

                // Get logged-in employee
                $handler = Employee::where('user_id', Auth::id())->first();

                if (!$handler) {
                    abort(500, 'Employee profile for this admin was not found.');
                }

                if (in_array($request->status,['approved','rejected'])) {

                    ArchivedLeaveHr3::create([
                        'original_request_id' => $ess->id,
                        'employee_id'         => $ess->employee_id,
                        'leave_type'          => $ess->type ?? 'Leave',
                        'details'             => $ess->details,
                        'start_date'          => $ess->leave_date,
                        'end_date'            => $ess->end_date,
                        'final_status'        => $request->status,

                        // store ONLY employee_id
                        'processed_by'        => $handler->employee_id,

                        'archived_at'         => now(),
                    ]);

                    if ($request->status === 'approved' && $ess->shift_id > 0) {
                        DB::table('shifts_hr3')
                            ->where('id',$ess->shift_id)
                            ->update(['is_active'=>0]);
                    }

                    $ess->delete();

                    $msg = "Request processed successfully.";

                } else {

                    $ess->update([
                        'status' => $request->status
                    ]);

                    $msg = "Status updated.";
                }

                return redirect()->back()->with('success',$msg);

            });

        } catch (\Exception $e) {

            Log::error("Leave Archive Error: ".$e->getMessage());

            return redirect()->back()->with(
                'error',
                "Process failed: ".$e->getMessage()
            );
        }
    }
}   