@extends('admin.hr3.layouts.app')

@section('title', 'Leave Management - HR3')

@section('content')
<div class="container-fluid" style="padding: 20px;">
    
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
        <div>
            <h2 style="margin: 0; color: #333; font-weight: 700;">
                <i class="bi bi-calendar2-check-fill" style="color: #0d6efd;"></i> Leave Management
            </h2>
            <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 0.9em;">
                Manage pending applications and view history
            </p>
        </div>

        <div style="display: flex; gap: 10px;">
            <div style="background: #fff8eb; border: 1px solid #ffeeba; padding: 10px 15px; border-radius: 8px; text-align: center;">
                <small style="color: #856404; display: block; font-size: 0.7em; font-weight: 700;">PENDING</small>
                <strong style="font-size: 1.1em;">{{ $requests->count() }}</strong>
            </div>

            <div style="background: #f0fff4; border: 1px solid #c6f6d5; padding: 10px 15px; border-radius: 8px; text-align: center;">
                <small style="color: #276749; display: block; font-size: 0.7em; font-weight: 700;">ARCHIVED</small>
                <strong style="font-size: 1.1em;">{{ $archived->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden;">
        
        <table style="width: 100%; border-collapse: collapse;">
            
            <thead style="background: #343a40; color: white;">
                <tr>
                    <th style="padding: 15px;">REQ ID</th>
                    <th style="padding: 15px;">EMPLOYEE</th>
                    <th style="padding: 15px;">DETAILS</th>
                    <th style="padding: 15px;">DATES</th>
                    <th style="padding: 15px;">STATUS</th>
                    <th style="padding: 15px; text-align: center;">HANDLED BY</th>
                </tr>
            </thead>

            <tbody>

                {{-- ================= PENDING ================= --}}
                <tr style="background: #fdfaf3;">
                    <td colspan="6" style="padding: 8px 15px; font-size: 0.7em; font-weight: 800; color: #9a6700;">
                        INBOX: PENDING APPROVAL
                    </td>
                </tr>

                @forelse($requests as $r)
                <tr style="border-bottom: 1px solid #eee;">
                    
                    <td style="padding: 15px;">
                        #{{ $r->id }}
                    </td>

                    <td style="padding: 15px;">
                        <strong>
                            {{ $r->employee->first_name ?? 'Unknown' }}
                            {{ $r->employee->last_name ?? '' }}
                        </strong>
                        <br>
                        <small class="text-muted">
                            ID: {{ $r->employee_id }}
                        </small>
                    </td>

                    <td style="padding: 15px;">
                        {{ \Illuminate\Support\Str::limit($r->details, 40) }}
                    </td>

                    <td style="padding: 15px;">
                        {{ \Carbon\Carbon::parse($r->leave_date)->format('M d, Y') }}
                    </td>

                    <td style="padding: 15px;">
                        <span class="badge bg-warning text-dark">
                            PENDING
                        </span>
                    </td>

                    <td style="padding: 15px; text-align: center;">
                        <form method="POST"
                              action="{{ route('admin.hr3.leave.update', $r->id) }}"
                              style="display: flex; gap: 5px; justify-content: center;">
                            
                            @csrf

                            <button type="submit"
                                    name="status"
                                    value="approved"
                                    class="btn btn-sm btn-success">
                                Approve
                            </button>

                            <button type="submit"
                                    name="status"
                                    value="rejected"
                                    class="btn btn-sm btn-danger">
                                Reject
                            </button>

                        </form>
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            No pending requests.
                        </td>
                    </tr>
                @endforelse


                {{-- ================= ARCHIVED ================= --}}
                <tr style="background: #f0fff4; border-top: 1px solid #c6f6d5;">
                    <td colspan="6" style="padding: 8px 15px; font-size: 0.7em; font-weight: 800; color: #276749;">
                        HISTORY: ARCHIVED APPLICATIONS
                    </td>
                </tr>

                @forelse($archived as $a)
                <tr style="border-bottom: 1px solid #eee; background: #fafdfb;">

                    <td style="padding: 15px; color: #999;">
                        #{{ $a->original_request_id }}
                    </td>

                    <td style="padding: 15px;">
                        <strong>
                            {{ $a->employee->first_name ?? 'N/A' }}
                            {{ $a->employee->last_name ?? '' }}
                        </strong>
                    </td>

                    <td style="padding: 15px;">
                        {{ \Illuminate\Support\Str::limit($a->details, 30) }}
                    </td>

                    <td style="padding: 15px;">
                        {{ \Carbon\Carbon::parse($a->start_date)->format('M d, Y') }}
                    </td>

                    <td style="padding: 15px;">
                        <span class="badge {{ $a->final_status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($a->final_status) }}
                        </span>
                    </td>

                    {{-- FIXED HANDLED BY --}}
                    <td style="padding: 15px; text-align: center;">
                        <div style="background:#fff;padding:6px 12px;border-radius:6px;border:1px dashed #28a745;display:inline-block;">
                            
                            <i class="bi bi-person-check-fill" style="color:#28a745;"></i>

                            <span style="color:#2d3748;font-weight:600;font-size:0.85em;">
                                {{ $a->handler->first_name ?? 'Unknown' }}
                                {{ $a->handler->last_name ?? '' }}
                            </span>

                        </div>
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            No archived records.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>
</div>
@endsection