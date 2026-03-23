<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWarehousePurchaseOrdersController extends Controller
{
    /**
     * Show all purchase orders from Logistics1
     */
    public function index()
    {
        $purchaseOrders = DB::table('warehouse_purchaseorders_logistics1')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available vehicles only
        $vehicles = DB::table('fleet_management_logistics2')
            ->where('status', 'available')
            ->get();

        return view('admin._logistics2.warehouse_purchase_orders.index', compact('purchaseOrders', 'vehicles'));
    }

    /**
     * Assign vehicle and approve purchase order
     */
    public function assignVehicle(Request $request, $id)
    {
        $request->validate([
            'model_name' => 'required|string'
        ]);

        DB::table('warehouse_purchaseorders_logistics1')
            ->where('id', $id)
            ->update([
                'model_name' => $request->model_name,
                'status' => 'approved',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Vehicle assigned and purchase order approved.');
    }
}