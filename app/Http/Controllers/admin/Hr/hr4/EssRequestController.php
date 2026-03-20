<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use App\Http\Controllers\Controller;
use App\Models\admin\Hr\hr4\PayrollEssRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EssRequestController extends Controller
{
    public function index(Request $request)
    {
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
        $essRequest = PayrollEssRequest::with('employee')->findOrFail($id);
        return view('admin.hr4.ess_requests.show', compact('essRequest'));
    }

    public function approve(Request $request, $id)
    {
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

        DB::table('payroll_request_hr2')
            ->where('employee_id', $essRequest->employee_id)
            ->where('details', $essRequest->details)
            ->update(['status' => 'approved', 'updated_at' => Carbon::now()]);

        return back()->with('success', 'Request has been approved.');
    }

    public function reject(Request $request, $id)
    {
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
        try {
            $hr2Requests = DB::table('payroll_request_hr2')->get();
            $synced = 0;
            $updated = 0;

            foreach ($hr2Requests as $req) {
                $status = in_array($req->status ?? 'pending', ['pending', 'approved', 'rejected']) ? ($req->status ?? 'pending') : 'pending';
                // payroll_request_hr2 has no type field; map to payroll by default.
                $requestType = $req->request_type ?? ($req->type ?? 'payroll');
                $requestType = Str::lower($requestType);
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
}
