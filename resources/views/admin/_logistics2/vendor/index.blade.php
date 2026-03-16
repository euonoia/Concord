@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #333;"><i class="bi bi-cart-check"></i> Pending Dispatch Requests</h3>
            <span class="badge bg-primary" style="background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                Action Required
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8f9fa;">
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Drug Info</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Qty</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Requester</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Time Received</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Dispatch Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingRequests as $request)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">
                                <div style="font-weight: bold;">{{ $request->drug_name }}</div>
                                <code style="font-size: 0.75rem; color: #666;">{{ $request->drug_num }}</code>
                            </td>
                            <td style="padding: 12px;">
                                <span class="badge" style="background: #f1f3f5; color: #333; border: 1px solid #ddd;">{{ $request->requested_quantity }}</span>
                            </td>
                            <td style="padding: 12px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span>{{ $request->requester_fname }} {{ $request->requester_lname }}</span>
                                    <small style="color: #888; font-size: 0.7rem;">Emp ID: {{ $request->requested_by }}</small>
                                </div>
                            </td>
                            <td style="padding: 12px;">
                                <div style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</div>
                            </td>
                            <td style="padding: 12px;">
                                <form action="{{ route('admin.logistics2.vendor.process', $request->id) }}" method="POST">
                                    @csrf
                                    {{-- HIDDEN INPUTS FOR VEHICLE DEFAULTS --}}
                                    <input type="hidden" name="vehicle_type" value="Standard Logistics Truck">
                                    <input type="hidden" name="plate_number" value="FOR-ASSIGNMENT">
                                    
                                    <button type="submit" class="btn btn-sm" style="background: #28a745; color: white; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                        <i class="bi bi-truck"></i> Create Shipment
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                                No pending requests from Logistics 1.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection