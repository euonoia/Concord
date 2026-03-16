@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px;">
        
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #333;"><i class="bi bi-truck"></i> Vehicle Reservation & Shipments</h3>
            <div class="status-summary">
                <span class="badge bg-info" style="background: #17a2b8; color: white; padding: 5px 12px; border-radius: 15px;">
                    Logistics 2 Fleet
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8f9fa;">
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Shipment Details</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Vehicle Info</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Status</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Handler/Delivered By</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $row)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">
                                <div style="font-weight: bold; color: #007bff;">{{ $row->drug_name }}</div>
                                <small class="text-muted">Quantity: {{ $row->quantity }} units</small>
                                
                                @if($row->original_status == 'approved' || $row->original_status == 'delivered')
                                    <div style="margin-top: 5px;">
                                        <span style="font-size: 0.7rem; background: #e3f2fd; color: #0d47a1; padding: 2px 8px; border-radius: 10px; border: 1px solid #bbdefb;">
                                            <i class="bi bi-shield-check"></i> L1 VERIFIED
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="bi bi-car-front-fill" style="font-size: 1.2rem; margin-right: 8px; color: #6c757d;"></i>
                                    <div>
                                        <strong>{{ $row->vehicle_type }}</strong><br>
                                        <code style="background: #e9ecef; padding: 2px 5px; border-radius: 3px;">{{ $row->plate_number }}</code>
                                    </div>
                                </div>
                            </td>

                            <td style="padding: 15px;">
                                @if($row->delivery_status == 'delivered')
                                    <span style="color: #28a745; background: #d4edda; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-check-circle-fill"></i> DELIVERED
                                    </span>
                                @elseif($row->delivery_status == 'in_transit' || $row->l2_status == 'shipped')
                                    <span style="color: #856404; background: #fff3cd; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-geo-alt-fill"></i> IN TRANSIT
                                    </span>
                                @else
                                    <span style="color: #004085; background: #cce5ff; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-box-seam"></i> PROCESSING
                                    </span>
                                @endif
                            </td>

                            <td style="padding: 15px;">
                                @if($row->delivered_by)
                                    <span style="font-family: monospace; background: #f8f9fa; padding: 2px 6px; border: 1px solid #ddd; border-radius: 4px;">
                                        {{ $row->delivered_by }}
                                    </span>
                                @else
                                    <span style="color: #bbb; font-style: italic;">On the road...</span>
                                @endif
                            </td>

                            <td style="padding: 15px; text-align: center;">
                                {{-- Action for Processing status --}}
                                @if($row->delivery_status == 'pending' || $row->l2_status == 'processing')
                                    <form action="{{ route('admin.logistics2.vehicle.transit', $row->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-action" style="background: #17a2b8; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                                            <i class="bi bi-truck"></i> Start Transit
                                        </button>
                                    </form>

                                {{-- Action for In Transit status --}}
                                @elseif($row->delivery_status == 'in_transit')
                                    <form action="{{ route('admin.logistics2.vehicle.complete', $row->id) }}" method="POST" onsubmit="return confirm('Verify item receipt and mark as Delivered?')">
                                        @csrf
                                        <button type="submit" class="btn-action" style="background: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                                            <i class="bi bi-check-lg"></i> Mark Delivered
                                        </button>
                                    </form>

                                {{-- Finished State --}}
                                @else
                                    <div style="color: #28a745; font-weight: bold;">
                                        <i class="bi bi-check-all" style="font-size: 1.2rem;"></i> Logs Synced
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                                <i class="bi bi-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                No active shipments found in the fleet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-action:hover {
        filter: brightness(90%);
        transform: translateY(-1px);
        transition: 0.2s;
    }
    .table tr:hover {
        background-color: #f9f9f9;
    }
</style>
@endsection