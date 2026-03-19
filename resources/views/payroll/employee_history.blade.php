@extends('admin.hr4.layouts.app')

@section('title', 'Employee Payroll History - HR4')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Payroll History: {{ $employee->first_name }} {{ $employee->last_name }}</h2>
            <div class="flex gap-4">
                <a href="{{ route('hr4.payroll_reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Back to Reports
                </a>
                <a href="{{ route('hr4.payroll_reports.export', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')]) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Export History
                </a>
            </div>
        </div>

        {{-- Employee Info --}}
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Employee ID</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->employee_id }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Department</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Position</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->position->position_title ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-blue-800">Total Payrolls</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_payrolls'] }}</p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-green-800">Average Salary</h3>
                <p class="text-3xl font-bold text-green-600">₱{{ number_format($stats['average_salary'], 2) }}</p>
            </div>
            <div class="bg-red-50 p-6 rounded-lg border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-red-800">Average Deductions</h3>
                <p class="text-3xl font-bold text-red-600">₱{{ number_format($stats['average_deductions'], 2) }}</p>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg border-l-4 border-purple-500">
                <h3 class="text-lg font-semibold text-purple-800">Total Earned</h3>
                <p class="text-3xl font-bold text-purple-600">₱{{ number_format($stats['total_earned'], 2) }}</p>
            </div>
        </div>

        {{-- Payroll History Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payroll->pay_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->salary, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₱{{ number_format($payroll->deductions, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payroll records found for this employee.</td>
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