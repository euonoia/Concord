@extends('admin.hr4.layouts.app')

@section('title', 'ESS Request Details - HR4')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('hr4.ess_requests.index') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
            <i class="bi bi-arrow-left mr-2"></i>
            Back to ESS Requests
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Request Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">ESS Payroll Request</h1>
                        <p class="text-gray-600 mt-1">Request ID: #{{ $essRequest->id }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-bold
                        @if($essRequest->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($essRequest->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($essRequest->status) }}
                    </span>
                </div>

                <!-- Employee Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-person-badge text-blue-600 mr-2"></i>
                        Employee Information
                    </h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Full Name</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $essRequest->employee->first_name ?? 'N/A' }} {{ $essRequest->employee->last_name ?? '' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Employee ID</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $essRequest->employee_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Department</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $essRequest->employee->department->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Position</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $essRequest->employee->position->position_title ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-file-earmark-text text-blue-600 mr-2"></i>
                        Request Details
                    </h2>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Request Type</p>
                            <p class="text-base text-gray-900">{{ ucfirst($essRequest->request_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Request Date</p>
                            <p class="text-base text-gray-900">{{ $essRequest->requested_date->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Description</p>
                            <div class="bg-white border border-gray-200 p-4 rounded mt-2">
                                <p class="text-base text-gray-900 whitespace-pre-wrap">
                                    {{ $essRequest->details ?? 'No details provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Section -->
                @if($essRequest->status !== 'pending')
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-check-circle text-green-600 mr-2"></i>
                            Approval Information
                        </h2>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Approved By</p>
                                <p class="text-base text-gray-900">
                                    @if($essRequest->approvedBy)
                                        {{ $essRequest->approvedBy->name ?? $essRequest->approvedBy->username }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Approval Date</p>
                                <p class="text-base text-gray-900">
                                    {{ $essRequest->approved_date?->format('M d, Y H:i') ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Approval Notes</p>
                                <div class="bg-white border border-gray-200 p-4 rounded mt-2">
                                    <p class="text-base text-gray-900 whitespace-pre-wrap">
                                        {{ $essRequest->approval_notes ?? 'No notes provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-gear text-gray-600 mr-2"></i>
                    Actions
                </h2>

                @if($essRequest->status === 'pending')
                    <form method="POST" action="{{ route('hr4.ess_requests.approve', $essRequest->id) }}" class="mb-4">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="bi bi-check-circle mr-2"></i>
                            Approve Request
                        </button>
                    </form>

                    <button type="button" onclick="openRejectModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="bi bi-x-circle mr-2"></i>
                        Reject Request
                    </button>
                @endif

                <!-- Timeline -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                <div class="w-1 h-8 bg-gray-300 my-1"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                                <p class="text-xs text-gray-600">{{ $essRequest->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($essRequest->status !== 'pending')
                            <div class="flex">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 {{ $essRequest->status === 'approved' ? 'bg-green-600' : 'bg-red-600' }} rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $essRequest->status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $essRequest->approved_date?->format('M d, Y H:i') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">Additional Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium text-gray-900">{{ $essRequest->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Request ID:</span>
                            <span class="font-medium text-gray-900">#{{ $essRequest->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Reject Request</h2>
        <form method="POST" action="{{ route('hr4.ess_requests.reject', $essRequest->id) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                <textarea name="reason" rows="4" placeholder="Enter reason for rejection..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-red-500" required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition duration-200">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection
