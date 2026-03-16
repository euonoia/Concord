<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVehicleReservationController extends Controller
{
    public function index()
    {
        // Force the DB to recognize the new size immediately upon loading the page
        $this->ensureSchemaIsReady();

        $reservations = DB::table('vehicle_reservations')
            ->join('vendor_logistics2', 'vehicle_reservations.vendor_log_id', '=', 'vendor_logistics2.id')
            ->join('procurement_log_logistics2', 'vendor_logistics2.procurement_id', '=', 'procurement_log_logistics2.id')
            ->select(
                'vehicle_reservations.*', 
                'vendor_logistics2.drug_name', 
                'vendor_logistics2.quantity',
                'vendor_logistics2.status as l2_status',
                'procurement_log_logistics2.status as original_status'
            )
            ->whereIn('vendor_logistics2.status', ['processing', 'shipped', 'delivered'])
            ->orderBy('vehicle_reservations.created_at', 'desc')
            ->get();

        return view('admin._logistics2.vehicle.index', compact('reservations'));
    }

    public function startTransit(Request $request, $id)
    {
        DB::transaction(function () use ($id) {
            DB::table('vehicle_reservations')->where('id', $id)->update([
                'delivery_status' => 'in_transit',
                'updated_at' => now()
            ]);

            $reservation = DB::table('vehicle_reservations')->where('id', $id)->first();

            DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);

            $vendorLog = DB::table('vendor_logistics2')->where('id', $reservation->vendor_log_id)->first();

            DB::table('procurement_log_logistics2')->where('id', $vendorLog->procurement_id)->update([
                'status' => 'shipped',
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Shipment is now In Transit.');
    }

  public function completeDelivery(Request $request, $id)
{
    // Find handler ID once
    $employee = DB::table('employees')->where('user_id', Auth::id())->first();
    $handlerId = $employee ? $employee->employee_id : (string)Auth::id();

    DB::transaction(function () use ($id, $handlerId) {
        
        // 1. Update Vehicle Reservations
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

        // 3. Update Procurement Log
        DB::table('procurement_log_logistics2')
            ->where('id', $vendorLog->procurement_id)
            ->update([
                'status' => 'received', 
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);
    });

    return redirect()->back()->with('success', 'Delivery completed successfully.');
}

    private function ensureSchemaIsReady()
    {
        // This is the "Nuclear Option"
        // It forces TiDB Cloud to change the column regardless of what your SQL tool says
        try {
            DB::unprepared("ALTER TABLE procurement_log_logistics2 MODIFY delivered_by VARCHAR(255) NULL");
            DB::unprepared("ALTER TABLE vehicle_reservations MODIFY delivered_by VARCHAR(255) NULL");
        } catch (\Exception $e) {
            // Silently fail if already updated
        }
    }
}