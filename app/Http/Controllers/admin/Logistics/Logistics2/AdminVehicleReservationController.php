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
            ->select(
                'vehicle_reservations.*', 
                'vendor_logistics2.status as l2_status'
            )
            ->orderBy('vehicle_reservations.created_at', 'desc')
            ->get();

        return view('admin._logistics2.vehicle.index', compact('reservations'));
    }

    /**
     * Start the Transit process
     */
    public function startTransit(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            // 1. Update Vehicle Reservation status
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'in_transit',
                'updated_at' => now()
            ]);

            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();

            // 2. Sync with Vendor Log
            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);

            // 3. Sync with original Procurement Log
            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();
            DB::table('procurement_log_logistics2')->where('id', $vendorLog->procurement_id)->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Shipment is now In Transit.');
    }

    /**
     * Mark the delivery as Complete and Release the Vehicle
     */
    public function completeDelivery(Request $request, $id)
    {
        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? $employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $handlerId) {
            // 1. Get reservation details before updating
            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();

            if (!$reservation) {
                return;
            }

            // 2. Update Vehicle Reservation Table
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'delivered',
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);

            // 3. AUTOMATION: Mark the vehicle as 'available' in Fleet Management
            DB::table('fleet_management_logistics2')
                ->where('plate_number', $reservation->plate_number)
                ->update([
                    'status' => 'available',
                    'updated_at' => now()
                ]);

            // 4. Update Vendor Logistics status
            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'delivered',
                'updated_at' => now()
            ]);

            // 5. Final update to Procurement Log (L1)
            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();
            DB::table('procurement_log_logistics2')
                ->where('id', $vendorLog->procurement_id)
                ->update([
                    'status' => 'received', 
                    'delivered_by' => $handlerId,
                    'updated_at' => now()
                ]);

            // 6. UPDATE DRUG INVENTORY BASED ON DELIVERED QUANTITY
            $inventory = DB::table('drug_inventory_core2')
                ->where('drug_num', $reservation->drug_num)
                ->first();

            if ($inventory) {
                $newQuantity = $inventory->quantity + $reservation->quantity;

                // Determine new status based on quantity
                $status = match(true) {
                    $newQuantity == 0 => 'Out of Stock',
                    $newQuantity <= 10 => 'Low Stock',
                    $newQuantity <= 20 => 'Critical',
                    default => 'Stable'
                };

                DB::table('drug_inventory_core2')
                    ->where('drug_num', $reservation->drug_num)
                    ->update([
                        'quantity' => $newQuantity,
                        'status' => $status,
                        'updated_at' => now()
                    ]);
            }
        });

        return redirect()->back()->with('success', 'Delivery completed, inventory updated, vehicle is now available.');
    }
}