@extends('admin.hr4.layouts.app')

@section('title', 'Employee Training Rewards - HR4 Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Employee Training Rewards</h2>
                <p class="text-gray-600 mt-1">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</p>
            </div>
            <a href="{{ route('hr4.training_rewards.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="bi bi-arrow-left mr-2"></i>Back to All Training Rewards
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                <i class="bi bi-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Employee Summary Card -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $trainingPerformances->count() }}</div>
                    <div class="text-blue-100">Total Trainings</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $totalRewards }}</div>
                    <div class="text-blue-100">Total Rewards Earned</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ number_format($averageGrade, 1) }}</div>
                    <div class="text-blue-100">Average Grade</div>
                </div>
            </div>
        </div>

        @if($trainingPerformances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weighted Average</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluation Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($trainingPerformances as $performance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $performance->training_name }}</td>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-1"></i>Completed
                                    </span>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Training Records</h3>
                <p class="text-gray-500">This employee has not completed any validated trainings from HR1 yet.</p>
            </div>
        @endif

        <!-- Reward Calculation Summary -->
        <div class="mt-8 bg-gray-50 p-6 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Reward Calculation Summary</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="font-medium text-gray-700 mb-3">Performance Distribution</h5>
                    <div class="space-y-2">
                        @php
                            $excellent = $trainingPerformances->filter(function($p) { return ($p->weighted_average ?? 0) >= 95; })->count();
                            $veryGood = $trainingPerformances->filter(function($p) { return ($p->weighted_average ?? 0) >= 90 && ($p->weighted_average ?? 0) < 95; })->count();
                            $good = $trainingPerformances->filter(function($p) { return ($p->weighted_average ?? 0) >= 85 && ($p->weighted_average ?? 0) < 90; })->count();
                            $satisfactory = $trainingPerformances->filter(function($p) { return ($p->weighted_average ?? 0) >= 80 && ($p->weighted_average ?? 0) < 85; })->count();
                            $below = $trainingPerformances->filter(function($p) { return ($p->weighted_average ?? 0) < 80; })->count();
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-green-600">Excellent (95-100):</span>
                            <span class="font-medium">{{ $excellent }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-600">Very Good (90-94):</span>
                            <span class="font-medium">{{ $veryGood }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-yellow-600">Good (85-89):</span>
                            <span class="font-medium">{{ $good }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-orange-600">Satisfactory (80-84):</span>
                            <span class="font-medium">{{ $satisfactory }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-red-600">Below Satisfactory (<80):</span>
                            <span class="font-medium">{{ $below }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h5 class="font-medium text-gray-700 mb-3">Reward Breakdown</h5>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Excellent Rewards (₱5,000 × {{ $excellent }}):</span>
                            <span class="font-medium text-green-600">₱{{ number_format($excellent * 5000) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Very Good Rewards (₱3,000 × {{ $veryGood }}):</span>
                            <span class="font-medium text-green-600">₱{{ number_format($veryGood * 3000) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Good Rewards (₱2,000 × {{ $good }}):</span>
                            <span class="font-medium text-green-600">₱{{ number_format($good * 2000) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Satisfactory Rewards (₱1,000 × {{ $satisfactory }}):</span>
                            <span class="font-medium text-green-600">₱{{ number_format($satisfactory * 1000) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between font-semibold">
                            <span>Total Rewards:</span>
                            <span class="text-green-600">₱{{ number_format($totalRewards) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Needed Positions Table -->
<div class="mt-12">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Recommended / Needed Positions</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position Title</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank Level</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Count</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($neededPositions as $pos)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $pos->department_name }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $pos->specialization_name ?? '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-semibold">{{ $pos->position_title }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $pos->rank_level }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $pos->employee_type }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">₱{{ number_format($pos->base_salary, 2) }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center font-bold">{{ $pos->required_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No needed positions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection