@extends('admin.hr4.layouts.app')

@section('title', 'ESS Payroll Requests - HR4')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="bi bi-file-earmark-text text-blue-600 mr-3"></i>
                    ESS Payroll Requests
                </h1>
                <p class="text-gray-600 mt-2">Manage employee self-service payroll requests</p>
            </div>
            <button onclick="syncRequests()" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center">
                <i class="bi bi-arrow-clockwise mr-2"></i>
                Sync from HR2
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center">
                <i class="bi bi-check-circle-fill mr-3"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center">
                <i class="bi bi-exclamation-circle-fill mr-3"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <form method="GET" action="{{ route('hr4.ess_requests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">All Types</option>
                        <option value="payroll" {{ request('type') == 'payroll' ? 'selected' : '' }}>Payroll</option>
                        <option value="bonus" {{ request('type') == 'bonus' ? 'selected' : '' }}>Bonus</option>
                        <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>Deduction</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <input type="date" name="date_from" class="w-full border border-gray-300 rounded px-3 py-2" value="{{ request('date_from') }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                        <i class="bi bi-search mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('hr4.ess_requests.index') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-4 rounded transition duration-200 text-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</p>
                    </div>
                    <i class="bi bi-clock-history text-3xl text-yellow-400"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Approved</p>
                        <p class="text-2xl font-bold text-green-600">{{ $approvedCount ?? 0 }}</p>
                    </div>
                    <i class="bi bi-check-circle text-3xl text-green-400"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-lg border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800">Rejected</p>
                        <p class="text-2xl font-bold text-red-600">{{ $rejectedCount ?? 0 }}</p>
                    </div>
                    <i class="bi bi-x-circle text-3xl text-red-400"></i>
                </div>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-800">Total</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $totalCount ?? 0 }}</p>
                    </div>
                    <i class="bi bi-files text-3xl text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full bg-white">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Employee</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Request Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Details</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests ?? [] as $request)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="bi bi-person text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $request->employee->first_name ?? 'N/A' }} {{ $request->employee->last_name ?? '' }}</p>
                                        <p class="text-sm text-gray-600">ID: {{ $request->employee_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    {{ ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($request->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full flex items-center w-fit">
                                        <i class="bi bi-clock-history mr-1"></i> Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full flex items-center w-fit">
                                        <i class="bi bi-check-circle mr-1"></i> Approved
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full flex items-center w-fit">
                                        <i class="bi bi-x-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $request->requested_date?->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="truncate block w-32">
                                    {{ Str::limit($request->details, 30) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('hr4.ess_requests.show', $request->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @if($request->status === 'pending')
                                        <button onclick="approveRequest({{ $request->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                        <button onclick="rejectRequest({{ $request->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition duration-200">
                                            <i class="bi bi-x"></i> Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <i class="bi bi-inbox text-4xl text-gray-300 block mb-2"></i>
                                <p>No ESS requests found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($requests) && $requests->hasPages())
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Hidden Forms for Actions -->
<form id="approveForm" method="POST" style="display:none;">
    @csrf
</form>

<form id="rejectForm" method="POST" style="display:none;">
    @csrf
</form>

<form id="syncForm" method="POST" action="{{ route('hr4.ess_requests.sync') }}" style="display:none;">
    @csrf
</form>

<script>
function approveRequest(id) {
    if (confirm('Are you sure you want to approve this request?')) {
        const form = document.getElementById('approveForm');
        form.action = `/admin/hr4/ess-requests/${id}/approve`;
        form.submit();
    }
}

function rejectRequest(id) {
    const reason = prompt('Enter reason for rejection:');
    if (reason !== null) {
        const form = document.getElementById('rejectForm');
        form.action = `/admin/hr4/ess-requests/${id}/reject`;
        
        // Add hidden input for reason
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        
        form.submit();
    }
}

function syncRequests() {
    if (confirm('This will sync all pending ESS requests from HR2. Continue?')) {
        document.getElementById('syncForm').submit();
    }
}
</script>
@endsection
