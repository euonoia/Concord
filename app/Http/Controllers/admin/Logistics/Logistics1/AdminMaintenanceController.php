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
        $maintenanceFleet = DB::table('fleet_management_logistics2')
            ->where('status', 'maintenance')
            ->get();

        $repairLogs = DB::table('audit_logistics2')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent financial entries for a mini-ledger on the side
        $financials = DB::table('maintenance_ledger_financials')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        return view('admin._logistics1.maintenance.index', compact('maintenanceFleet', 'repairLogs', 'financials'));
    }

    public function recordRepair(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'repair_type' => 'required',
            'cost' => 'nullable|numeric|min:0'
        ]);

        $vehicle = DB::table('fleet_management_logistics2')->where('id', $request->vehicle_id)->first();
        $cost = $request->cost ?? 0;

        DB::transaction(function () use ($request, $vehicle, $cost) {
            // 1. Record the activity in Audit Table
            $auditId = DB::table('audit_logistics2')->insertGetId([
                'reference_id' => $request->vehicle_id,
                'category'     => 'Maintenance',
                'action'       => $request->repair_type,
                'details'      => "Complete overhaul/repair for vehicle " . $vehicle->plate_number,
                'cost'         => $cost,
                'performed_by' => Auth::user()->name ?? 'System Admin',
                'created_at'   => now()
            ]);

            // 2. Record the financial transaction in the new Ledger table
            DB::table('maintenance_ledger_financials')->insert([
                'audit_id'         => $auditId,
                'vehicle_plate'    => $vehicle->plate_number,
                'repair_type'      => $request->repair_type,
                'amount'           => $cost,
                'payment_status'   => 'paid',
                'transaction_date' => now()->toDateString(),
                'created_at'       => now()
            ]);

            // 3. Update Vehicle Status
            DB::table('fleet_management_logistics2')->where('id', $request->vehicle_id)->update([
                'status' => 'available',
                'last_maintained_at' => now(),
                'updated_at' => now()
            ]);
        });

        return redirect()->back()->with('success', 'Repair completed. Financial ledger updated.');
    }
}