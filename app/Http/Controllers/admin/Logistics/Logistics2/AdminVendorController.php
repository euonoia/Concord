<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVendorController extends Controller
{
    /**
     * Display the procurement requests and available fleet.
     */
    public function index()
    {
        // 1. Fetch incoming procurement requests
        $incomingRequests = DB::table('procurement_log_logistics2')
            ->select(
                'procurement_log_logistics2.*',
                'employees.first_name as requester_fname',
                'employees.last_name as requester_lname'
            )
            ->leftJoin('employees', 'procurement_log_logistics2.requested_by', '=', 'employees.employee_id')
            ->where('procurement_log_logistics2.status', 'pending')
            ->orderBy('procurement_log_logistics2.created_at', 'desc')
            ->get();

        // 2. Fetch only AVAILABLE vehicles for the assignment dropdown
        $availableFleet = DB::table('fleet_management_logistics2')
            ->where('status', 'available')
            ->get();

        return view('admin._logistics2.vendor.index', compact('incomingRequests', 'availableFleet'));
    }

    /**
     * Process the request: Update L1 log, Insert into L2 log, Create Reservation, and Lock Vehicle.
     */
    public function processRequest(Request $request, $id)
    {
        // Validate that a plate number was actually selected
        $request->validate([
            'plate_number' => 'required|exists:fleet_management_logistics2,plate_number',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? (string)$employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $handlerId, $request) {
            // 1. Get original procurement details
            $originalRequest = DB::table('procurement_log_logistics2')->where('id', $id)->first();

            if (!$originalRequest) {
                return redirect()->back()->with('error', 'Request not found.');
            }

            // 2. Get vehicle details to ensure we use the correct vehicle_type from the fleet table
            $vehicle = DB::table('fleet_management_logistics2')
                ->where('plate_number', $request->plate_number)
                ->first();

            // 3. Update Procurement Log (L1) status
            DB::table('procurement_log_logistics2')->where('id', $id)->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);

            // 4. Insert into L2 Vendor Log
            $vendorLogId = DB::table('vendor_logistics2')->insertGetId([
                'procurement_id' => $originalRequest->id,
                'drug_num'      => $originalRequest->drug_num,
                'drug_name'     => $originalRequest->drug_name,
                'quantity'      => $originalRequest->requested_quantity,
                'requested_by'  => $handlerId,
                'status'        => 'processing',
                'supplier_name' => 'Internal Warehouse',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 5. Create Vehicle Reservation with the REAL plate number
            DB::table('vehicle_reservations')->insert([
                'vendor_log_id'   => $vendorLogId,
                'drug_num'        => $originalRequest->drug_num,
                'drug_name'       => $originalRequest->drug_name,
                'quantity'        => $originalRequest->requested_quantity,
                'vehicle_type'    => $vehicle->vehicle_type ?? 'Standard Truck', 
                'plate_number'    => $request->plate_number, // The actual plate selected
                'delivery_status' => 'pending',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 6. AUTOMATION: Mark this vehicle as 'in_use' so it can't be picked again
            DB::table('fleet_management_logistics2')
                ->where('plate_number', $request->plate_number)
                ->update([
                    'status' => 'in_use',
                    'updated_at' => now()
                ]);
        });

        return redirect()->back()->with('success', 'Request processed. Vehicle ' . $request->plate_number . ' has been dispatched.');
    }
}