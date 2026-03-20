@extends('admin._logistics2.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card" style="background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 20px;">
        
        {{-- HEADER --}}
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #333;">
                <i class="bi bi-cart-check"></i> Pending Dispatch Requests
            </h3>
            <span class="badge bg-primary" style="background: #007bff; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                Action Required
            </span>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                
                {{-- TABLE HEADER --}}
                <thead>
                    <tr style="text-align: left; background: #f8f9fa;">
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Drug Info</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Supplier</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Qty</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Requester</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Delivery Address</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Time Received</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Assign Vehicle & Dispatch</th>
                    </tr>
                </thead>

                {{-- TABLE BODY --}}
                <tbody>
                    @forelse($incomingRequests as $request)
                        <tr style="border-bottom: 1px solid #eee;">
                            
                            {{-- DRUG INFO --}}
                            <td style="padding: 12px;">
                                <div style="font-weight: bold;">{{ $request->drug_name }}</div>
                                <code style="font-size: 0.75rem; color: #666;">{{ $request->drug_num }}</code>
                            </td>

                            {{-- SUPPLIER --}}
                            <td style="padding: 12px;">
                                <div style="font-size: 0.85rem; color: #555;">
                                    {{ $request->selected_supplier ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- QUANTITY --}}
                            <td style="padding: 12px;">
                                <span class="badge" style="background: #f1f3f5; color: #333; border: 1px solid #ddd; padding: 2px 8px; border-radius: 4px;">
                                    {{ $request->requested_quantity }}
                                </span>
                            </td>

                            {{-- REQUESTER --}}
                            <td style="padding: 12px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span>
                                        {{ $request->requester_fname ?? 'Unknown' }} 
                                        {{ $request->requester_lname ?? '' }}
                                    </span>
                                    <small style="color: #888; font-size: 0.7rem;">
                                        Emp ID: {{ $request->requested_by }}
                                    </small>
                                </div>
                            </td>

                            {{-- DELIVERY ADDRESS --}}
                            <td style="padding: 12px;">
                                <div style="font-size: 0.85rem; color: #555;">
                                    {{ $request->address ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- TIME --}}
                            <td style="padding: 12px;">
                                <div style="font-size: 0.85rem;">
                                    {{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}
                                </div>
                            </td>

                            {{-- ACTION --}}
                            <td style="padding: 12px;">
                                <form action="{{ route('admin.logistics2.vendor.process', $request->id) }}" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                    @csrf
                                    
                                    {{-- VEHICLE SELECT --}}
                                    <select name="plate_number" required 
                                        style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; font-size: 0.85rem; outline: none; min-width: 180px;">
                                        
                                        <option value="" disabled selected>-- Select Plate --</option>

                                        @forelse($availableFleet as $fleet)
                                            <option value="{{ $fleet->plate_number }}">
                                                {{ $fleet->plate_number }} ({{ $fleet->vehicle_type }})
                                            </option>
                                        @empty
                                            <option value="" disabled>No vehicles available</option>
                                        @endforelse
                                    </select>
                                    
                                    {{-- SUBMIT --}}
                                    <button type="submit" 
                                        class="btn btn-sm"
                                        {{ $availableFleet->isEmpty() ? 'disabled' : '' }}
                                        style="background: #28a745; color: white; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;">
                                        
                                        <i class="bi bi-truck"></i> Create Shipment
                                    </button>
                                </form>

                                {{-- NO VEHICLE WARNING --}}
                                @if($availableFleet->isEmpty())
                                    <small style="color: #dc3545; display: block; margin-top: 5px; font-size: 0.7rem;">
                                        * No vehicles available in fleet.
                                    </small>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
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