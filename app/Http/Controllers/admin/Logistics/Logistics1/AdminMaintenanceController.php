<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminMaintenanceController extends Controller
{
    public function index()
    {
        // Get vehicles that are currently in maintenance
        $maintenanceFleet = DB::table('fleet_management_logistics2')
            ->where('status', 'maintenance')
            ->get();

        // Get the latest audit logs for repairs
        $repairLogs = DB::table('audit_logistics2')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin._logistics1.maintenance.index', compact('maintenanceFleet', 'repairLogs'));
    }

    public function recordRepair(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'repair_type' => 'required',
            'cost' => 'nullable|numeric'
        ]);

        $vehicle = DB::table('fleet_management_logistics2')->where('id', $request->vehicle_id)->first();

        DB::transaction(function () use ($request, $vehicle) {
            // 1. Record the repair in the flexible audit table
            DB::table('audit_logistics2')->insert([
                'reference_id' => $request->vehicle_id,
                'category'     => 'Maintenance',
                'action'       => $request->repair_type,
                'details'      => "Repair performed on " . $vehicle->plate_number,
                'cost'         => $request->cost ?? 0,
                'performed_by' => Auth::user()->name,
                'created_at'   => now()
            ]);

            // 2. Update the vehicle's last maintenance date and set back to available
            DB::table('fleet_management_logistics2')->where('id', $request->vehicle_id)->update([
                'status' => 'available',
                'last_maintained_at' => now(),
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Repair recorded. Vehicle ' . $vehicle->plate_number . ' is now back in service.');
    }
}