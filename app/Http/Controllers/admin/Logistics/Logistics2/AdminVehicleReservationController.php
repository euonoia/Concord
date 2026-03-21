<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVehicleReservationController extends Controller
{
    /**
     * Display the Vehicle Reservations & Shipments
     */
    public function index()
    {
        $reservations = DB::table('vehicle_reservations')
            ->leftJoin('vendor_logistics2', 'vehicle_reservations.vendor_log_id', '=', 'vendor_logistics2.id')
            ->leftJoin('purchase_orders_logistics1 as po', 'vendor_logistics2.procurement_id', '=', 'po.id')
            ->select(
                'vehicle_reservations.*', 
                'vendor_logistics2.status as l2_status',
                'po.address',
                'po.selected_supplier',
                'po.status as l1_status'
            )
            ->orderBy('vehicle_reservations.created_at', 'desc')
            ->get();

        return view('admin._logistics2.vehicle.index', compact('reservations'));
    }

    /**
     * Dispatch vehicle and store cost temporarily
     */
    public function startTransit(Request $request, $id)
    {
        $request->validate([
            'cost' => 'required|numeric|min:0'
        ]);

        $cost = $request->input('cost');

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? $employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $cost, $handlerId) {
            // 1. Update Vehicle Reservation to in_transit and store cost
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'in_transit',
                'delivery_cost' => $cost,
                'updated_at' => now()
            ]);

            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();

            // 2. Update Vendor Logistics
            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);

            // 3. Update Purchase Orders (L1) to in_transit
            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();
            DB::table('purchase_orders_logistics1')->where('id', $vendorLog->procurement_id)->update([
                'status' => 'in_transit',
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Shipment dispatched and status updated to in_transit.');
    }

    /**
     * Complete Delivery and store in Audit + update inventory
     */
    public function completeDelivery(Request $request, $id)
    {
        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? $employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $handlerId) {
            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();
            if (!$reservation) return;

            // 1. Update Vehicle Reservation Table
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'delivered',
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);

            // 2. Mark the vehicle as 'available'
            DB::table('fleet_management_logistics2')
                ->where('plate_number', $reservation->plate_number)
                ->update([
                    'status' => 'available',
                    'updated_at' => now()
                ]);

            // 3. Update Vendor Logistics status
            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'delivered',
                'updated_at' => now()
            ]);

            // 4. Update Purchase Orders (L1) to delivered
            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();
            DB::table('purchase_orders_logistics1')->where('id', $vendorLog->procurement_id)->update([
                'status' => 'delivered',
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);

            // 5. Update Drug Inventory
            $inventory = DB::table('drug_inventory_core2')->where('drug_num', $reservation->drug_num)->first();
            if ($inventory) {
                $newQuantity = $inventory->quantity + $reservation->quantity;
                $status = match(true) {
                    $newQuantity == 0 => 'Out of Stock',
                    $newQuantity <= 10 => 'Low Stock',
                    $newQuantity <= 20 => 'Critical',
                    default => 'Stable'
                };
                DB::table('drug_inventory_core2')->where('drug_num', $reservation->drug_num)->update([
                    'quantity' => $newQuantity,
                    'status' => $status,
                    'updated_at' => now()
                ]);
            }

            // 6. Insert into Audit Log for delivery WITH stored cost and address
            DB::table('audit_logistics2')->insert([
                'reference_id' => $reservation->id,
                'category' => 'Delivery',
                'action' => 'Vehicle Delivery Completed',
                'details' => "Delivered {$reservation->quantity} units of {$reservation->drug_name} (SKU: {$reservation->drug_num}) via vehicle {$reservation->plate_number} to address: {$reservation->address}",
                'performed_by' => $handlerId,
                'cost' => $reservation->delivery_cost ?? 0.00,
            ]);
        });

        return redirect()->back()->with('success', 'Delivery completed, inventory updated, vehicle is now available, and status updated to delivered.');
    }
}