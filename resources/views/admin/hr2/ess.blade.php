@extends('admin.layouts.app')

@section('title', 'ESS Requests - HR2 Admin')

@section('content')
<div class="container">
    <h2>Employee Self-Service Requests</h2>

    {{-- Success Alert --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Type</th>
                <th>Details</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
                @php
                    // Dynamic styling based on status
                    $statusColor = match($r->status) {
                        'approved' => 'green',
                        'rejected' => 'red',
                        'closed'   => 'gray',
                        default    => 'orange', // pending
                    };
                    
                    // Button logic
                    $isFinalized = in_array($r->status, ['approved', 'rejected', 'closed']);
                    $isClosed = ($r->status == 'closed');
                @endphp
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>
                        <strong>{{ $r->employee->first_name ?? 'N/A' }} {{ $r->employee->last_name ?? '' }}</strong>
                    </td>
                    <td>{{ ucfirst($r->type) }}</td>
                    <td><small>{{ $r->details }}</small></td>
                    <td>
                        <strong style="color: {{ $statusColor }};">
                            {{ ucfirst($r->status) }}
                        </strong>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($r->created_at)->format('M d, Y') }}</td>
                    <td>
                        {{-- Updated route to ess.updateStatus --}}
                        <form method="POST" action="{{ route('ess.updateStatus', $r->id) }}">
                            @csrf
                            @method('PATCH') {{-- Using PATCH as defined in the routes earlier --}}
                            
                            <button type="submit" name="status" value="approved" 
                                style="color: green; cursor: {{ $isFinalized ? 'not-allowed' : 'pointer' }}" 
                                {{ $isFinalized ? 'disabled' : '' }}>
                                Approve
                            </button>

                            <button type="submit" name="status" value="rejected" 
                                style="color: red; cursor: {{ $isFinalized ? 'not-allowed' : 'pointer' }}" 
                                {{ $isFinalized ? 'disabled' : '' }}>
                                Reject
                            </button>

                            <button type="submit" name="status" value="closed" 
                                style="color: gray; cursor: {{ $isClosed ? 'not-allowed' : 'pointer' }}" 
                                {{ $isClosed ? 'disabled' : '' }}>
                                Close
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">No requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection