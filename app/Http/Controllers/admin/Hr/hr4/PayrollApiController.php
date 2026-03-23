<?php

namespace App\Http\Controllers\admin\Hr\hr4;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\admin\Hr\hr4\DirectCompensation;
use Carbon\Carbon;

class PayrollApiController extends Controller
{
    /**
     * Submit payroll request from HR2
     */
    public function submitPayrollRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
                'salary' => 'nullable|numeric|min:0', // HR2 can send this but we'll override with DirectCompensation
                'net_pay' => 'nullable|numeric|min:0',
                'details' => 'nullable|string',
                'request_type' => 'nullable|string|in:payroll,bonus,deduction',
            ]);

            // Always get salary from latest DirectCompensation to ensure consistency
            $employee = Employee::where('employee_id', $validated['employee_id'])->first();
            $salary = null;

            if ($employee) {
                $compensation = DirectCompensation::where('employee_id', $validated['employee_id'])
                    ->orderByDesc('month')
                    ->first();

                if ($compensation) {
                    $salary = $compensation->base_salary + $compensation->shift_allowance + $compensation->overtime_pay + $compensation->bonus + $compensation->training_reward;
                }

                // Fallback to position if no compensation
                if (!$salary || $salary <= 0) {
                    $salary = $employee->position->base_salary ?? 0;
                }
            }

            // Use HR2's net_pay if provided, otherwise default to salary
            $netPay = $validated['net_pay'] ?? null;
            if (!$netPay || $netPay <= 0) {
                $netPay = $salary;
            }

            $salary = $salary !== null ? $salary : 0;
            $netPay = $netPay !== null ? $netPay : 0;

            // Create entry in payroll_request_hr2 table
            $payrollRequest = DB::table('payroll_request_hr2')->insertGetId([
                'employee_id' => $validated['employee_id'],
                'salary' => $salary,
                'net_pay' => $netPay,
                'details' => $validated['details'] ?? null,
                'request_type' => $validated['request_type'] ?? 'payroll',
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payroll request submitted successfully',
                'request_id' => $payrollRequest,
                'employee_id' => $validated['employee_id'],
                'status' => 'pending',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting payroll request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get payroll request details
     */
    public function getPayrollRequest($id)
    {
        try {
            $payrollRequest = DB::table('payroll_request_hr2')->find($id);

            if (!$payrollRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payroll request not found',
                ], 404);
            }

            // Get employee details
            $employee = Employee::where('employee_id', $payrollRequest->employee_id)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $payrollRequest->id,
                    'employee_id' => $payrollRequest->employee_id,
                    'employee_name' => $employee ? $employee->first_name . ' ' . $employee->last_name : 'N/A',
                    'salary' => $payrollRequest->salary,
                    'net_pay' => $payrollRequest->net_pay,
                    'details' => $payrollRequest->details,
                    'request_type' => $payrollRequest->request_type,
                    'status' => $payrollRequest->status,
                    'created_at' => $payrollRequest->created_at,
                    'updated_at' => $payrollRequest->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payroll request: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get employee payroll information
     * This endpoint retrieves the salary/net_pay for an employee
     */
    public function getEmployeePayroll($employeeId)
    {
        try {
            $employee = Employee::where('employee_id', $employeeId)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            // Get salary from multiple sources
            $salary = null;
            $netPay = null;
            $source = null;

            // Source 1: Latest direct compensation in HR4
            $compensation = DirectCompensation::where('employee_id', $employeeId)
                ->orderByDesc('month')
                ->first();

            if ($compensation) {
                $salary = $compensation->base_salary + $compensation->shift_allowance + 
                          $compensation->overtime_pay + $compensation->bonus + $compensation->training_reward;
                $source = 'Direct Compensation (HR4)';
            }

            // Source 2: Position base salary fallback
            if (!$salary || $salary <= 0) {
                $position = $employee->position;
                if ($position) {
                    $salary = $position->base_salary;
                    $source = 'Position Base Salary';
                }
            }

            // If net_pay not calculated, use salary
            if (!$netPay || $netPay <= 0) {
                $netPay = $salary ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'employee_id' => $employeeId,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'department' => $employee->department->name ?? 'N/A',
                    'position' => $employee->position->position_title ?? 'N/A',
                    'salary' => $salary ?? 0,
                    'net_pay' => $netPay ?? 0,
                    'salary_source' => $source,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving employee payroll: ' . $e->getMessage(),
            ], 500);
        }
    }
}
