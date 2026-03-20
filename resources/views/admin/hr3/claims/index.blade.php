@extends('admin.hr3.layouts.app')

@section('title','Claims & Reimbursement')

@section('content')
<div class="container" style="padding: 20px;">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card" style="padding: 20px;">
        <h3>Claims & Reimbursement</h3>
        <div style="overflow-x:auto;">
            <table class="table table-bordered table-hover mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Claim Type</th>
                        <th>Details</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Validated By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>
                                {{ $c->employee ? $c->employee->first_name . ' ' . $c->employee->last_name : $c->employee_id }}
                            </td>
                            <td>{{ $c->claim_type }}</td>
                            <td>{{ $c->description ?? '-' }}</td>
                            <td>₱{{ number_format($c->amount, 2) }}</td>
                            <td>
                                <span class="badge 
                                    @if($c->status=='approved') bg-success 
                                    @elseif($c->status=='rejected') bg-danger 
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $c->validator ? $c->validator->first_name . ' ' . $c->validator->last_name : '-' }}
                            </td>
                            <td>
                                @if($c->status == 'pending')
                                    <form action="{{ route('admin.hr3.claims.approve', $c->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.hr3.claims.reject', $c->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No claims found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection