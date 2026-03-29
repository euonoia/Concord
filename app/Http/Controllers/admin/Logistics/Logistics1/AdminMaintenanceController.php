<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminMaintenanceController extends Controller
{
    /**
     * Ensure user is Logistics1 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics1') {
            abort(403, 'Unauthorized access to Logistics1 Maintenance.');
        }
    }

    /**
     * Display fleet maintenance dashboard
     */
    public function index()
    {
        $this->authorizeLogisticsAdmin();

        // Vehicles currently under maintenance
        $maintenanceFleet = DB::table('fleet_management_logistics2')
            ->where('status', 'maintenance')
            ->get();

        // Recent maintenance logs
        $repairLogs = DB::table('audit_logistics2')
            ->where('category', 'Maintenance')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent financial entries
        $financials = DB::table('maintenance_ledger_financials')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        return view(
            'admin._logistics1.maintenance.index',
            compact('maintenanceFleet', 'repairLogs', 'financials')
        );
    }

    /**
     * Record a repair and update fleet status
     */
    public function recordRepair(Request $request)
    {
        $this->authorizeLogisticsAdmin();

        $request->validate([
            'vehicle_id'  => 'required|exists:fleet_management_logistics2,id',
            'repair_type' => 'required|string',
            'cost'        => 'nullable|numeric|min:0',
        ]);

        // Get vehicle info
        $vehicle = DB::table('fleet_management_logistics2')
            ->where('id', $request->vehicle_id)
            ->first();

        $cost = $request->cost ?? 0;

        // Get logged-in user's employee ID
        $loggedInEmployee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        DB::transaction(function () use ($request, $vehicle, $cost, $loggedInEmployee) {
            // 1. Record maintenance in audit log
            $auditId = DB::table('audit_logistics2')->insertGetId([
                'reference_id' => $vehicle->id,
                'category'     => 'Maintenance',
                'action'       => $request->repair_type,
                'details'      => "Maintenance performed on vehicle " . $vehicle->plate_number,
                'cost'         => $cost,
                'performed_by' => $loggedInEmployee
                    ? $loggedInEmployee->employee_id
                    : 'SYSTEM', // fallback if employee not found
                'created_at'   => now(),
            ]);

            // 2. Record financial ledger entry
            DB::table('maintenance_ledger_financials')->insert([
                'audit_id'         => $auditId,
                'vehicle_plate'    => $vehicle->plate_number,
                'repair_type'      => $request->repair_type,
                'amount'           => $cost,
                'payment_status'   => 'unpaid',
                'transaction_date' => now()->toDateString(),
                'created_at'       => now(),
            ]);

            // 3. Update vehicle status to available
            DB::table('fleet_management_logistics2')
                ->where('id', $vehicle->id)
                ->update([
                    'status'            => 'available',
                    'last_maintained_at'=> now(),
                    'updated_at'        => now(),
                ]);
        });

        return redirect()->back()->with(
            'success',
            'Repair recorded successfully. Logged-in employee recorded as performer.'
        );
    }
}