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
        $essRequest = PayrollEssRequest::with(['employee.position', 'employee.department'])->findOrFail($id);
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

        // Create payroll entry for approved request based on employee_id + salary sources
        $employee = Employee::where('employee_id', $essRequest->employee_id)->first();

        if ($employee) {
            // Get HR2 request for reference
            $hr2Request = DB::table('payroll_request_hr2')
                ->where('employee_id', $essRequest->employee_id)
                ->where('details', $essRequest->details)
                ->orderByDesc('created_at')
                ->first();

            // Source 1: Latest direct compensation in HR4 (Primary), use latest available month up to current month
            $compensation = DirectCompensation::where('employee_id', $essRequest->employee_id)
                ->where('month', '<=', now()->format('Y-m'))
                ->orderByDesc('month')
                ->first();

            $salary = null;
            $netPay = null;

            if ($compensation && $compensation->total_compensation > 0) {
                $salary = $compensation->total_compensation;
                $netPay = $this->calculateNetPay($salary); // Calculate net pay with deductions
            }

            // Source 2: HR2 sync table salary/net_pay if DirectCompensation not available
            if (!$salary || $salary <= 0) {
                $salary = $hr2Request->salary ?? null;
                $netPay = $hr2Request->net_pay ?? null;
            }

            // Source 3: Position base salary fallback
            if ((!$salary || $salary <= 0) && $employee->position) {
                $salary = $employee->position->base_salary ?? 0;
            }

            // If net_pay not set, use salary as net
            if (!$netPay || $netPay <= 0) {
                $netPay = $salary;
            }

            // Persist computed salary/net_pay back to payroll_request_hr2 for reporting
            if ($hr2Request) {
                $updateData = [
                    'salary' => $salary,
                    'updated_at' => Carbon::now(),
                ];

                if (Schema::hasColumn('payroll_request_hr2', 'net_pay')) {
                    $updateData['net_pay'] = $netPay;
                }

                DB::table('payroll_request_hr2')
                    ->where('id', $hr2Request->id)
                    ->update($updateData);
            }

            // Ensure positive salary
            if ($salary > 0 && $netPay > 0) {
                // Avoid duplicates for same day & employee
                $existsPayroll = Payroll::where('employee_id', $employee->id)
                    ->whereDate('pay_date', Carbon::now())
                    ->exists();

                if (!$existsPayroll) {
                    Payroll::create([
                        'employee_id' => $employee->id,
                        'salary' => $salary,
                        'deductions' => max(0, $salary - $netPay),
                        'net_pay' => $netPay,
                        'pay_date' => Carbon::now()->toDateString(),
                    ]);
                }
            }
        }

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

    /**
     * Calculate net pay by applying standard deductions to gross salary
     */
    private function calculateNetPay($grossSalary)
    {
        if (!$grossSalary || $grossSalary <= 0) {
            return 0;
        }

        // Standard deduction rates (based on Philippine labor law)
        $deductions = [
            'sss' => 0.045,        // 4.5% SSS contribution
            'philhealth' => 0.04,  // 4% PhilHealth contribution
            'pagibig' => 0.02,     // 2% PAG-IBIG contribution
            'income_tax' => 0.15,  // Estimated 15% income tax (varies by bracket)
        ];

        $totalDeductionRate = array_sum($deductions); // Approximately 25.5%
        $totalDeductions = $grossSalary * $totalDeductionRate;

        $netPay = $grossSalary - $totalDeductions;

        // Ensure net pay is not negative
        return max(0, $netPay);
    }
}
