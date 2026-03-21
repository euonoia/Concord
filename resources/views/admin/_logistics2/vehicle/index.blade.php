@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px;">
        
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #333;"><i class="bi bi-truck"></i> Vehicle Reservation & Shipments</h3>
            <div class="status-summary">
                <span class="badge" style="background: #17a2b8; color: white; padding: 5px 12px; border-radius: 15px;">
                    Logistics 2 Fleet
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8f9fa;">
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Item Details</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Vehicle Info</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Status</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Handler/Delivered By</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6;">Delivery Cost</th>
                        <th style="padding: 15px; border-bottom: 2px solid #dee2e6; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $row)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">
                                <div style="font-weight: bold; color: #007bff;">{{ $row->drug_name }}</div>
                                <div style="font-size: 0.85rem; color: #666;">SKU: {{ $row->drug_num }}</div>
                                <small class="text-muted">Quantity: <strong>{{ $row->quantity }}</strong> units</small>
                                <div style="font-size: 0.85rem; color: #555; margin-top: 3px;">
                                    Supplier: <strong>{{ $row->supplier ?? '—' }}</strong>
                                </div>
                            </td>

                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="bi bi-car-front-fill" style="font-size: 1.2rem; margin-right: 8px; color: #6c757d;"></i>
                                    <div>
                                        <strong>{{ $row->vehicle_type }}</strong><br>
                                        <code style="background: #e9ecef; padding: 2px 5px; border-radius: 3px; color: #e83e8c;">{{ $row->plate_number }}</code>
                                    </div>
                                </div>
                            </td>

                            <td style="padding: 15px;">
                                @if($row->delivery_status == 'delivered')
                                    <span style="color: #28a745; background: #d4edda; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-check-circle-fill"></i> DELIVERED
                                    </span>
                                @elseif($row->delivery_status == 'in_transit')
                                    <span style="color: #856404; background: #fff3cd; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-geo-alt-fill"></i> IN TRANSIT
                                    </span>
                                @else
                                    <span style="color: #004085; background: #cce5ff; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="bi bi-box-seam"></i> READY FOR PICKUP
                                    </span>
                                @endif
                            </td>

                            <td style="padding: 15px;">
                                @if($row->delivered_by)
                                    <div style="display: flex; flex-direction: column;">
                                        <span style="font-family: monospace; background: #f8f9fa; padding: 2px 6px; border: 1px solid #ddd; border-radius: 4px; width: fit-content;">
                                            ID: {{ $row->delivered_by }}
                                        </span>
                                        <small class="text-success" style="font-size: 0.7rem;">Verified Receipt</small>
                                    </div>
                                @else
                                    <span style="color: #bbb; font-style: italic;">Awaiting delivery...</span>
                                @endif
                            </td>

                            <td style="padding: 15px;">
                                <!-- Show the delivery cost if it exists -->
                                @if($row->delivery_cost)
                                    <span style="font-weight: bold;">₱ {{ number_format($row->delivery_cost, 2) }}</span>
                                @else
                                    <span style="color: #888; font-style: italic;">—</span>
                                @endif
                            </td>

                            <td style="padding: 15px; text-align: center;">
                                @if($row->delivery_status == 'pending')
                                    <!-- Ready for Pickup: input cost and dispatch -->
                                    <form action="{{ route('admin.logistics2.vehicle.transit', $row->id) }}" method="POST" style="display: flex; gap: 8px; align-items: center; justify-content: center;">
                                        @csrf
                                        <input type="number" name="cost" step="0.01" placeholder="Cost" required
                                            style="width: 100px; padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
                                        <button type="submit" class="btn-action" style="background: #17a2b8; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                                            <i class="bi bi-truck"></i> Dispatch
                                        </button>
                                    </form>
                                @elseif($row->delivery_status == 'in_transit')
                                    <!-- In Transit: just complete, cost is remembered -->
                                    <form action="{{ route('admin.logistics2.vehicle.complete', $row->id) }}" method="POST" onsubmit="return confirm('Mark as Delivered?')">
                                        @csrf
                                        <button type="submit" class="btn-action" style="background: #28a745; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                                            <i class="bi bi-check-lg"></i> Complete
                                        </button>
                                    </form>
                                @else
                                    <div style="color: #28a745; font-weight: bold;">
                                        <i class="bi bi-cloud-check"></i> Synced
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #999;">No active fleet shipments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection