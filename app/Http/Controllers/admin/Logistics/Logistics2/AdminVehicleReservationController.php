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
        // We now pull drug_name and quantity directly from vehicle_reservations 
        // for better consistency and performance.
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
            // 1. Update Vehicle Table
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
     * Mark the delivery as Complete
     */
    public function completeDelivery(Request $request, $id)
    {
        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? $employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $handlerId) {
            // 1. Update Vehicle Reservation
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'delivered',
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);

            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();

            // 2. Update Vendor Logistics
            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'delivered',
                'updated_at' => now()
            ]);

            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();

            // 3. Final update to Procurement Log
            DB::table('procurement_log_logistics2')
                ->where('id', $vendorLog->procurement_id)
                ->update([
                    'status' => 'received', 
                    'delivered_by' => $handlerId,
                    'updated_at' => now()
                ]);
        });

        return redirect()->back()->with('success', 'Delivery completed and logs synced.');
    }
}