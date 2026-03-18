<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVendorController extends Controller
{
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

        $availableFleet = DB::table('fleet_management_logistics2')
            ->where('status', 'available')
            ->get();

        return view('admin._logistics2.vendor.index', compact('incomingRequests', 'availableFleet'));
    }

 public function processRequest(Request $request, $id)
{
    $request->validate([
        'plate_number' => 'required|exists:fleet_management_logistics2,plate_number',
    ]);

    $employee = DB::table('employees')->where('user_id', Auth::id())->first();
    $handlerId = $employee ? $employee->employee_id : Auth::id();

    DB::transaction(function () use ($id, $handlerId, $request) {

        $originalRequest = DB::table('procurement_log_logistics2')->where('id', $id)->first();

        if (!$originalRequest) {
            abort(404, 'Request not found.');
        }

        $vehicle = DB::table('fleet_management_logistics2')
            ->where('plate_number', $request->plate_number)
            ->first();

        // Update Procurement Status + Delivered By
        DB::table('procurement_log_logistics2')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'delivered_by' => $handlerId,
                'updated_at' => now()
            ]);

        // Insert Vendor Log
        $vendorLogId = DB::table('vendor_logistics2')->insertGetId([
            'procurement_id' => $originalRequest->id,
            'drug_num'       => $originalRequest->drug_num,
            'drug_name'      => $originalRequest->drug_name,
            'quantity'       => $originalRequest->requested_quantity,
            'requested_by'   => $handlerId,
            'status'         => 'processing',
            'supplier_name'  => $originalRequest->selected_supplier, // preserve supplier
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Insert Vehicle Reservation WITH supplier
        DB::table('vehicle_reservations')->insert([
            'vendor_log_id'   => $vendorLogId,
            'drug_num'        => $originalRequest->drug_num,
            'drug_name'       => $originalRequest->drug_name,
            'quantity'        => $originalRequest->requested_quantity,
            'supplier'   => $originalRequest->selected_supplier,
            'vehicle_type'    => $vehicle->vehicle_type ?? 'Standard Truck',
            'plate_number'    => $request->plate_number,
            'delivery_status' => 'pending',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        //  Mark vehicle as in use
        DB::table('fleet_management_logistics2')
            ->where('plate_number', $request->plate_number)
            ->update([
                'status' => 'in_use',
                'updated_at' => now()
            ]);
    });

    return redirect()->back()->with('success', 'Request processed and supplier preserved.');
}
}