@extends('admin.hr4.layouts.app')

@section('title', 'Training Rewards Management - HR4 Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Training Rewards Management</h2>
            <div class="text-sm text-gray-600">
                <i class="bi bi-info-circle mr-1"></i>
                Training grades from HR1 are automatically converted to rewards
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                <i class="bi bi-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($trainingPerformances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weighted Average</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluation Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($trainingPerformances as $performance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $performance->employee->first_name ?? 'Unknown' }} {{ $performance->employee->last_name ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ $performance->employee_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $performance->training_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ number_format($performance->weighted_average ?? 0, 1) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $grade = $performance->weighted_average ?? 0;
                                        if ($grade >= 95) {
                                            $level = 'Excellent';
                                            $color = 'text-green-800 bg-green-100';
                                        } elseif ($grade >= 90) {
                                            $level = 'Very Good';
                                            $color = 'text-blue-800 bg-blue-100';
                                        } elseif ($grade >= 85) {
                                            $level = 'Good';
                                            $color = 'text-yellow-800 bg-yellow-100';
                                        } elseif ($grade >= 80) {
                                            $level = 'Satisfactory';
                                            $color = 'text-orange-800 bg-orange-100';
                                        } else {
                                            $level = 'Below Satisfactory';
                                            $color = 'text-red-800 bg-red-100';
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ $level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    @if($grade >= 95)
                                        ₱5,000
                                    @elseif($grade >= 90)
                                        ₱3,000
                                    @elseif($grade >= 85)
                                        ₱2,000
                                    @elseif($grade >= 80)
                                        ₱1,000
                                    @else
                                        ₱0
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $performance->evaluated_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('hr4.training_rewards.show', $performance->employee) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $trainingPerformances->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="bi bi-trophy text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Training Performance Data</h3>
                <p class="text-gray-500">Training performance data from HR1 will appear here once validated.</p>
            </div>
        @endif

        <div class="mt-8 bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Reward Structure</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-green-50 p-3 rounded border-l-4 border-green-500">
                    <div class="font-semibold text-green-800">95-100: Excellent</div>
                    <div class="text-green-600">₱5,000 reward</div>
                </div>
                <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-500">
                    <div class="font-semibold text-blue-800">90-94: Very Good</div>
                    <div class="text-blue-600">₱3,000 reward</div>
                </div>
                <div class="bg-yellow-50 p-3 rounded border-l-4 border-yellow-500">
                    <div class="font-semibold text-yellow-800">85-89: Good</div>
                    <div class="text-yellow-600">₱2,000 reward</div>
                </div>
                <div class="bg-orange-50 p-3 rounded border-l-4 border-orange-500">
                    <div class="font-semibold text-orange-800">80-84: Satisfactory</div>
                    <div class="text-orange-600">₱1,000 reward</div>
                </div>
                <div class="bg-red-50 p-3 rounded border-l-4 border-red-500">
                    <div class="font-semibold text-red-800">Below 80: Below Satisfactory</div>
                    <div class="text-red-600">₱0 reward</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection