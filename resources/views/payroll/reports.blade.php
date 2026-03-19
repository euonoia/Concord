@extends('admin.hr4.layouts.app')

@section('title', 'Payroll Reports - HR4')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Payroll Reports & Analytics</h2>
            <div class="flex gap-4">
                <a href="{{ route('hr4.payroll_reports.detailed') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Detailed Report
                </a>
                <a href="{{ route('hr4.payroll_reports.export', ['start_date' => date('Y-m-01'), 'end_date' => date('Y-m-t')]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Export CSV
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <input type="month" name="month" value="{{ $month }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input type="number" name="year" value="{{ $year }}" min="2020" max="2030" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                        Update Report
                    </button>
                </div>
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-blue-800">Total Employees</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $employeeCount }}</p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-green-800">Total Salary</h3>
                <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalSalary, 2) }}</p>
            </div>
            <div class="bg-red-50 p-6 rounded-lg border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-red-800">Total Deductions</h3>
                <p class="text-3xl font-bold text-red-600">₱{{ number_format($totalDeductions, 2) }}</p>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-purple-800">Net Pay</h3>
                <p class="text-3xl font-bold text-purple-600">₱{{ number_format($totalNetPay, 2) }}</p>
            </div>
        </div>

        {{-- Year-to-Date Summary --}}
        <div class="bg-yellow-50 p-6 rounded-lg mb-8 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Year-to-Date Total ({{ $year }})</h3>
            <p class="text-3xl font-bold text-yellow-600">₱{{ number_format($ytdTotal, 2) }}</p>
        </div>

        {{-- Department Breakdown --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Department Breakdown</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departmentBreakdown as $dept)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dept->department }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dept->employees }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($dept->total_salary, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($dept->total_deductions, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($dept->total_net_pay, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payroll data found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Payrolls --}}
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Payrolls</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monthlyPayrolls->take(10) as $payroll)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $payroll->employee->first_name ?? 'N/A' }} {{ $payroll->employee->last_name ?? '' }}
                                    <br><small class="text-gray-500">{{ $payroll->employee->employee_id ?? 'N/A' }}</small>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->pay_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->salary, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->deductions, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('hr4.payroll_reports.employee', $payroll->employee_id) }}" class="text-blue-600 hover:text-blue-900">View History</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No payroll records found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

    <!-- Summary -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">₱{{ number_format($totalSalary, 2) }}</p>
                <p class="text-sm text-gray-600">Total Salary</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">₱{{ number_format($totalDeductions, 2) }}</p>
                <p class="text-sm text-gray-600">Total Deductions</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($totalNetPay, 2) }}</p>
                <p class="text-sm text-gray-600">Total Net Pay</p>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Payroll Records</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Employee</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Salary</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Deductions</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Net Pay</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Pay Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Department</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Position</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Salary Range</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="px-4 py-2">{{ $payroll->id }}</td>
                            <td class="px-4 py-2">{{ $payroll->employee->first_name ?? 'N/A' }} {{ $payroll->employee->last_name ?? '' }}</td>
                            <td class="px-4 py-2">₱{{ number_format($payroll->salary, 2) }}</td>
                            <td class="px-4 py-2">₱{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="px-4 py-2 font-semibold text-green-700">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="px-4 py-2">{{ $payroll->pay_date->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $payroll->employee->department->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $payroll->employee->position->position_title ?? 'N/A' }}</td>
                            <td class="px-4 py-2">₱{{ number_format($payroll->employee->position->base_salary ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">No payroll records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection