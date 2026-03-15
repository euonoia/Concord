@extends('admin.financials.layouts.app')

@section('title', 'Financial Reimbursements')

@section('content')

<div class="container">

    <h2>Approved Claims (Waiting Reimbursement)</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <table class="table table-bordered">

        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Employee</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Receipt</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($claims as $claim)

                <tr>

                    <td>{{ $claim->claim_id }}</td>

                    <td>
                        {{ $claim->employee->first_name ?? '' }}
                        {{ $claim->employee->last_name ?? 'N/A' }}
                    </td>

                    <td>{{ $claim->claim_type }}</td>

                    <td>
                        ₱ {{ number_format($claim->amount, 2) }}
                    </td>

                    <td>{{ $claim->description }}</td>

                    <td>
                        @if($claim->receipt_path)
                            <a href="{{ asset('storage/' . $claim->receipt_path) }}" target="_blank">
                                View
                            </a>
                        @else
                            No Receipt
                        @endif
                    </td>

                    <td>
                        <form method="POST"
                              action="{{ route('financials.reimbursement.process', $claim->id) }}">
                            
                            @csrf

                            <button class="btn btn-success">
                                Reimburse
                            </button>

                        </form>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" style="text-align:center;">
                        No approved claims available.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>



    <h2 class="mt-5">Reimbursed Claims</h2>

    <table class="table table-striped">

        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Employee</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

            @forelse($reimbursed as $r)

                <tr>

                    <td>{{ $r->claim_id }}</td>

                    <td>
                        {{ $r->employee->first_name ?? '' }}
                        {{ $r->employee->last_name ?? 'N/A' }}
                    </td>

                    <td>{{ $r->claim_type }}</td>

                    <td>
                        ₱ {{ number_format($r->amount, 2) }}
                    </td>

                    <td>{{ $r->created_at }}</td>

                </tr>

            @empty

                <tr>
                    <td colspan="5" style="text-align:center;">
                        No reimbursements yet.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection