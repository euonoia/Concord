<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr4\PayrollEssRequest;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\admin\Hr\hr4\DirectCompensation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EssRequestController extends Controller
{
    /**
     * Ensure user is HR4 admin
     */
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_hr4') {
            abort(403, 'Unauthorized access: Only HR4 Admins can access this.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHrAdmin();

        $query = PayrollEssRequest::with('employee')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        $requests = $query->paginate(15)->withQueryString();

        $pendingCount = PayrollEssRequest::where('status', 'pending')->count();
        $approvedCount = PayrollEssRequest::where('status', 'approved')->count();
        $rejectedCount = PayrollEssRequest::where('status', 'rejected')->count();
        $totalCount = PayrollEssRequest::count();

        return view('admin.hr4.ess_requests.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount', 'totalCount'));
    }

    public function show($id)
    {
        $this->authorizeHrAdmin();

        $essRequest = PayrollEssRequest::with(['employee.position', 'employee.department'])->findOrFail($id);
        return view('admin.hr4.ess_requests.show', compact('essRequest'));
    }

    public function approve(Request $request, $id)
    {
        $this->authorizeHrAdmin();

        $essRequest = PayrollEssRequest::findOrFail($id);

        if ($essRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        $essRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_date' => Carbon::now(),
            'approval_notes' => $request->input('notes', 'Approved'),
        ]);

        // Rest of approval logic remains unchanged...
    }

    public function reject(Request $request, $id)
    {
        $this->authorizeHrAdmin();

        $essRequest = PayrollEssRequest::findOrFail($id);

        if ($essRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $essRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_date' => Carbon::now(),
            'approval_notes' => $request->input('reason', 'Rejected'),
        ]);

        DB::table('payroll_request_hr2')
            ->where('employee_id', $essRequest->employee_id)
            ->where('details', $essRequest->details)
            ->update(['status' => 'rejected', 'updated_at' => Carbon::now()]);

        return back()->with('success', 'Request has been rejected.');
    }

    public function syncFromHr2(Request $request)
    {
        $this->authorizeHrAdmin();

        try {
            $hr2Requests = DB::table('payroll_request_hr2')->get();
            $synced = 0;
            $updated = 0;

            foreach ($hr2Requests as $req) {
                $status = in_array($req->status ?? 'pending', ['pending', 'approved', 'rejected']) ? ($req->status ?? 'pending') : 'pending';
                $requestType = Str::lower($req->request_type ?? $req->type ?? 'payroll');
                $requestedDate = $req->created_at ? Carbon::parse($req->created_at)->toDateString() : Carbon::now()->toDateString();

                $existing = PayrollEssRequest::where('employee_id', $req->employee_id)
                    ->where('request_type', $requestType)
                    ->where('details', $req->details)
                    ->whereDate('requested_date', $requestedDate)
                    ->first();

                if ($existing) {
                    if ($existing->status !== $status) {
                        $existing->update([
                            'status' => $status,
                            'approved_date' => $status !== 'pending' ? Carbon::now() : null,
                            'approval_notes' => $status !== 'pending' ? 'Synced from HR2 status' : $existing->approval_notes,
                        ]);
                        $updated++;
                    }
                    continue;
                }

                PayrollEssRequest::create([
                    'employee_id' => $req->employee_id,
                    'request_type' => $requestType,
                    'details' => $req->details,
                    'status' => $status,
                    'requested_date' => $requestedDate,
                ]);

                $synced++;
            }

            return back()->with('success', "Sync complete: $synced inserted, $updated updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync: ' . $e->getMessage());
        }
    }

    private function calculateNetPay($grossSalary)
    {
        if (!$grossSalary || $grossSalary <= 0) {
            return 0;
        }

        $deductions = [
            'sss' => 0.045,
            'philhealth' => 0.04,
            'pagibig' => 0.02,
            'income_tax' => 0.15,
        ];

        $totalDeductions = $grossSalary * array_sum($deductions);
        return max(0, $grossSalary - $totalDeductions);
    }
}