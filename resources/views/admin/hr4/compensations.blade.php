@extends('admin.hr4.layouts.app')

@section('title', 'Direct Compensation - HR4 Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Direct & Indirect Compensation - {{ date('F Y', strtotime($month . '-01')) }}</h2>
        </div>

        {{-- Generate Compensation Form --}}
        <form method="POST" action="{{ route('hr4.direct_compensation.generate') }}" class="mb-6 flex items-center gap-4 bg-gray-50 p-4 rounded-lg">
            @csrf
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                <input type="month" name="month" value="{{ $month }}" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center">
                    <i class="bi bi-calculator mr-2"></i>
                    Generate Compensation
                </button>
            </div>
        </form>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                <i class="bi bi-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($compensations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift Allowance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Reward</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Average</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($compensations as $comp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $comp->employee_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">{{ $comp->user ? $comp->user->username : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $comp->employee ? $comp->employee->first_name . ' ' . $comp->employee->last_name : 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($comp->base_salary, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($comp->shift_allowance, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($comp->overtime_pay, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">{{ number_format($comp->training_reward, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $latestTraining = \App\Models\admin\Hr\hr4\TrainingPerformance::where('employee_id', $comp->employee_id)
                                            ->where('status', 'completed')
                                            ->orderBy('evaluated_at', 'desc')
                                            ->first();
                                    @endphp
                                    {{ $latestTraining ? number_format($latestTraining->weighted_average, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($comp->bonus, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($comp->total_compensation, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No compensation records found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-cash-stack text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Compensation Data</h3>
                <p class="text-gray-500 mb-4">Generate compensation for the selected month to view records.</p>
            </div>
        @endif
    </div>
</div>
@endsection