<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVendorController extends Controller
{
    /**
     * Display the procurement requests for the Vendor (Logistics 2) to see.
     */
    public function index()
    {
        // Join with employees to show who requested the items
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

        return view('admin._logistics2.vendor.index', compact('incomingRequests'));
    }

    /**
     * Process the request: Update L1 log, Insert into L2 log, and Create Vehicle Reservation.
     */
    public function processRequest(Request $request, $id)
    {
        $employee = DB::table('employees')->where('user_id', Auth::id())->first();
        $handlerId = $employee ? (string)$employee->employee_id : (string)Auth::id();

        DB::transaction(function () use ($id, $handlerId, $request) {
            // 1. Get the original procurement details to maintain consistency
            $originalRequest = DB::table('procurement_log_logistics2')->where('id', $id)->first();

            if (!$originalRequest) {
                return redirect()->back()->with('error', 'Request not found.');
            }

            // 2. Update Procurement Log (L1) status
            DB::table('procurement_log_logistics2')->where('id', $id)->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);

            // 3. Insert into L2 Vendor Log and get the ID for the vehicle link
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

            // 4. Create the Vehicle Reservation WITH duplicated item data for consistency
            DB::table('vehicle_reservations')->insert([
                'vendor_log_id'   => $vendorLogId,
                'drug_num'        => $originalRequest->drug_num,
                'drug_name'       => $originalRequest->drug_name,
                'quantity'        => $originalRequest->requested_quantity,
                'vehicle_type'    => $request->vehicle_type ?? 'Standard Truck',
                'plate_number'    => $request->plate_number ?? 'PENDING',
                'delivery_status' => 'pending',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Request processed. Item is now in Vehicle Reservations.');
    }
}