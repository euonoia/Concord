<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVendorController extends Controller
{
    /**
     * Display the procurement requests for the Vendor to see.
     */
    public function index()
    {
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
     * Process the request: Update L1 log and Insert into L2 log.
     */
   public function processRequest(Request $request, $id)
{
    $employee = DB::table('employees')->where('user_id', Auth::id())->first();
    $handlerId = $employee ? (string)$employee->employee_id : (string)Auth::id();

    DB::transaction(function () use ($id, $handlerId, $request) {
        // 1. Update L1 Log
        DB::table('procurement_log_logistics2')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);

        $originalRequest = DB::table('procurement_log_logistics2')->where('id', $id)->first();

        // 2. Insert into L2 Vendor Log and get the ID
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

        // 3. IMPORTANT: Create the Vehicle Reservation entry
        // This makes the item appear on your Vehicle Reservation & Shipments page
        DB::table('vehicle_reservations')->insert([
            'vendor_log_id'   => $vendorLogId,
            'vehicle_type'    => $request->vehicle_type ?? 'Standard Truck',
            'plate_number'    => $request->plate_number ?? 'PENDING',
            'delivery_status' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    });

    return redirect()->back()->with('success', 'Dispatched to Vehicle Reservations.');
}
}