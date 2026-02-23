@extends('layouts.dashboard.app')

@section('title', 'Self-Service Portal')

@section('content')
<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
        
        <div class="card" style="padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd; height: fit-content;">
            <h3>Submit New Request</h3>
            <form action="{{ route('user.ess.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label>Request Type</label>
                    <select name="type" class="form-control" style="width: 100%; padding: 8px; border-radius: 4px;" required>
                        <option value="Profile Update">Profile Update</option>
                        <option value="Document Request">Document Request (COE/Payslip)</option>
                        <option value="Grievance">Grievance / Feedback</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Details</label>
                    <textarea name="details" rows="5" class="form-control" style="width: 100%; padding: 8px; border-radius: 4px;" placeholder="Explain your request..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
            </form>
        </div>

        <div class="card" style="padding: 20px; background: #fff; border-radius: 8px; border: 1px solid #ddd;">
            <h3>Request History</h3>
            <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee; text-align: left;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><strong>{{ $req->ess_id }}</strong></td>
                        <td style="padding: 10px;">{{ $req->type }}</td>
                        <td style="padding: 10px;">
                            @php
                                $color = match($req->status) {
                                    'pending' => '#f39c12',
                                    'approved' => '#27ae60',
                                    'rejected' => '#e74c3c',
                                    default => '#7f8c8d'
                                };
                            @endphp
                            <span style="background: {{ $color }}; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td style="padding: 10px; font-size: 0.85rem; color: #666;">
                            {{ $req->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #999;">No requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection