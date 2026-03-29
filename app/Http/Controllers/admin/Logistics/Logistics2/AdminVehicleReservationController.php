<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVehicleReservationController extends Controller
{
    /**
     * Ensure user is Logistics2 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics2') {
            abort(403, 'Unauthorized access to Logistics2 Vehicle Reservations.');
        }
    }

    /**
     * Display the Vehicle Reservations & Shipments
     */
    public function index()
    {
        $this->authorizeLogisticsAdmin();

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
        $this->authorizeLogisticsAdmin();

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
        $this->authorizeLogisticsAdmin();

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

            // 5. Insert into Audit Log for delivery WITH stored cost and address
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