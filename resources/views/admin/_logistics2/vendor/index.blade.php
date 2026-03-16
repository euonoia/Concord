@extends('admin._logistics2.layouts.app') {{-- Make sure this matches your layout path --}}

@section('content')
<div class="container-fluid">
    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #333;"><i class="bi bi-cart-check"></i> Incoming Procurement Requests</h3>
            <span class="badge bg-primary" style="background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                Logistics 2 Portal
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8f9fa;">
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Drug #</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Drug Name</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Quantity</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Requester</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Date Created</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingRequests as $request)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">{{ $request->drug_num }}</td>
                            <td style="padding: 12px;"><strong>{{ $request->drug_name }}</strong></td>
                            <td style="padding: 12px;">{{ $request->requested_quantity }}</td>
                            <td style="padding: 12px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span>{{ $request->requester_fname }} {{ $request->requester_lname }}</span>
                                    <small class="text-muted" style="color: #888; font-size: 0.75rem;">ID: {{ $request->requested_by }}</small>
                                </div>
                            </td>
                            <td style="padding: 12px;">{{ \Carbon\Carbon::parse($request->created_at)->format('Y-m-d H:i') }}</td>
                            <td style="padding: 12px;">
                                <form action="{{ route('admin.logistics2.vendor.process', $request->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" style="background: #28a745; color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer;">
                                        <i class="bi bi-gear-fill"></i> Process Request
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                No pending procurement requests from Logistics 1.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection