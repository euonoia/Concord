@extends('admin.hr4.layouts.app')

@section('title', 'Detailed Payroll Report - HR4')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Detailed Payroll Report</h2>
            <div class="flex gap-4">
                <a href="{{ route('hr4.payroll_reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Back to Reports
                </a>
                <a href="{{ route('hr4.payroll_reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Export CSV
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->department_id }}" {{ $department == $dept->department_id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-blue-800">Total Records</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $totals['count'] }}</p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-green-800">Total Salary</h3>
                <p class="text-3xl font-bold text-green-600">₱{{ number_format($totals['salary'], 2) }}</p>
            </div>
            <div class="bg-red-50 p-6 rounded-lg border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-red-800">Total Deductions</h3>
                <p class="text-3xl font-bold text-red-600">₱{{ number_format($totals['deductions'], 2) }}</p>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-purple-800">Total Net Pay</h3>
                <p class="text-3xl font-bold text-purple-600">₱{{ number_format($totals['net_pay'], 2) }}</p>
            </div>
        </div>

        {{-- Payroll Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payroll->employee->first_name ?? 'N/A' }} {{ $payroll->employee->last_name ?? '' }}
                                <br><small class="text-gray-500">{{ $payroll->employee->employee_id ?? 'N/A' }}</small>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->employee->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payroll->employee->position->position_title ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->pay_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->salary, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payroll records found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payrolls->hasPages())
            <div class="mt-6">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>
@endsection