<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLogistics1WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'inventory_control');
        $search    = $request->input('search');

        // -------------------------------------------------------
        // TAB 1: RECEIVING & INSPECTION
        // Fetched from receiving_logistics1
        // -------------------------------------------------------
        $receivingQuery = DB::table('receiving_logistics1 as r')
            ->leftJoin('employees as req_emp', 'r.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'r.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name  as req_last_name'
            )
            ->whereNull('r.deleted_at')
            ->orderBy('r.created_at', 'desc');

        if ($search) {
            $receivingQuery->where(function ($q) use ($search) {
                $q->where('r.drug_name',        'like', "%$search%")
                  ->orWhere('r.drug_num',         'like', "%$search%")
                  ->orWhere('r.po_number',         'like', "%$search%")
                  ->orWhere('r.selected_supplier', 'like', "%$search%");
            });
        }

        if ($request->filled('receiving_status')) {
            $receivingQuery->where('r.status', $request->input('receiving_status'));
        }

        $receiving = $receivingQuery->paginate(10, ['*'], 'receiving_page')->withQueryString();

        // -------------------------------------------------------
        // TAB 2: INVENTORY CONTROL
        // Full inventory with stock status
        // -------------------------------------------------------
        $inventoryQuery = DB::table('drug_inventory_core2')
            ->orderBy('created_at', 'desc');

        if ($search) {
            $inventoryQuery->where(function ($q) use ($search) {
                $q->where('drug_name', 'like', "%$search%")
                  ->orWhere('drug_num',  'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $inventoryQuery->where('status', $request->input('status'));
        }

        $inventory = $inventoryQuery->paginate(10, ['*'], 'inventory_page')->withQueryString();

        // -------------------------------------------------------
        // TAB 3: DISPATCH & DISTRIBUTION
        // Items that have been dispatched / low stock / out of stock
        // -------------------------------------------------------
        $dispatchQuery = DB::table('drug_inventory_core2')
            ->whereIn('status', ['Low Stock', 'Critical', 'Out of Stock'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $dispatchQuery->where(function ($q) use ($search) {
                $q->where('drug_name', 'like', "%$search%")
                  ->orWhere('drug_num',  'like', "%$search%");
            });
        }

        $dispatch = $dispatchQuery->paginate(10, ['*'], 'dispatch_page')->withQueryString();

        // Delivered POs available to receive (for the Receive PO modal)
        $approvedPOs = DB::table('purchase_orders_logistics1')
            ->whereNull('deleted_at')
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin._logistics1.warehouse.index', compact(
            'activeTab',
            'receiving',
            'inventory',
            'dispatch',
            'approvedPOs'
        ));
    }

    /**
     * Receive a PO — inserts into receiving_logistics1
     */
    public function receivePo(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_orders_logistics1,id',
        ]);

        $po = DB::table('purchase_orders_logistics1')->where('id', $request->po_id)->first();

        // Insert into receiving_logistics1
        DB::table('receiving_logistics1')->insert([
            'po_number'              => $po->po_number,
            'drug_num'               => $po->drug_num,
            'drug_name'              => $po->drug_name,
            'requested_quantity'     => $po->requested_quantity,
            'selected_supplier'      => $po->selected_supplier,
            'requested_date'         => $po->requested_date,
            'expected_delivery_date' => $po->expected_delivery_date,
            'status'                 => $po->status,
            'requested_by'           => $po->requested_by,
            'notes'                  => $po->notes,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // Mark PO as ordered
        DB::table('purchase_orders_logistics1')
            ->where('id', $po->id)
            ->update(['status' => 'ordered', 'updated_at' => now()]);

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'receiving'])
            ->with('success', 'PO ' . $po->po_number . ' received and logged successfully.');
    }
}