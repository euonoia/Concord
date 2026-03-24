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

        // TAB 1: RECEIVING & INSPECTION
        $receivingQuery = DB::table('receiving_logistics1 as r')
            ->leftJoin('employees as req_emp', 'r.requested_by', '=', 'req_emp.employee_id')
            ->select('r.*', 'req_emp.first_name as req_first_name', 'req_emp.last_name as req_last_name')
            ->where(function ($q) {
                $q->whereNull('r.deleted_at')->orWhere('r.deleted_at', '');
            })
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

        // TAB 2: INVENTORY CONTROL
        $inventoryQuery = DB::table('drug_inventory_core2')
            ->orderByRaw("FIELD(status, 'Out of Stock', 'Critical', 'Low Stock', 'Stable')")
            ->orderBy('drug_name', 'asc');

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

        // TAB 3: DISPATCH & DISTRIBUTION
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

        // Approved POs for Receive PO modal (from both tables)
        $approvedPOs = DB::table('purchase_orders_logistics1')
            ->whereNull('deleted_at')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get()
            ->concat(
                DB::table('warehouse_purchaseorders_logistics1')
                    ->whereNull('deleted_at')
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->get()
            )
            ->sortByDesc('created_at')
            ->values();

        // Vendors for Request Stock modal
        $vendors = DB::table('vendor_portal_logistics2')
            ->whereIn('status', ['active', 'pending_approval'])
            ->orderBy('vendor_name', 'asc')
            ->get();

        return view('admin._logistics1.warehouse.index', compact(
            'activeTab', 'receiving', 'inventory', 'dispatch', 'approvedPOs', 'vendors'
        ));
    }

    public function receivePo(Request $request)
    {
        $request->validate([
            'po_id'     => 'required',
            'po_source' => 'required|in:purchase_orders,warehouse_purchaseorders',
        ]);

        $table = $request->po_source === 'warehouse_purchaseorders'
            ? 'warehouse_purchaseorders_logistics1'
            : 'purchase_orders_logistics1';

        $po = DB::table($table)->where('id', $request->po_id)->first();

        if (!$po) {
            // Try the other table as fallback
            $fallback = $table === 'warehouse_purchaseorders_logistics1'
                ? 'purchase_orders_logistics1'
                : 'warehouse_purchaseorders_logistics1';
            $po = DB::table($fallback)->where('id', $request->po_id)->first();
            if ($po) $table = $fallback;
        }

        if (!$po) {
            return redirect()->back()->with('error', 'Purchase Order not found.');
        }

        DB::table('receiving_logistics1')->insert([
            'po_number'              => $po->po_number,
            'drug_num'               => $po->drug_num,
            'drug_name'              => $po->drug_name,
            'requested_quantity'     => $po->requested_quantity,
            'selected_supplier'      => $po->selected_supplier,
            'requested_date'         => $po->requested_date,
            'expected_delivery_date' => $po->expected_delivery_date,
            'status'                 => 'receiving',
            'requested_by'           => $po->requested_by,
            'notes'                  => $po->notes ?? null,
            'inspector'              => $request->inspector ?? null,
            'vehicle'                => $po->model_name ?? null,
            'amount'                 => $po->amount ?? 0.00,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        DB::table($table)
            ->where('id', $po->id)
            ->update(['status' => 'receiving', 'updated_at' => now()]);

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'receiving'])
            ->with('success', 'PO ' . $po->po_number . ' received and logged successfully.');
    }

    /**
     * Update receiving record status
     */
    public function updateReceiving(Request $request, $id)
    {
        $request->validate([
            'status'          => 'required|in:pending,approved,ordered,shipped,delivered,paid,cancelled',
            'actual_quantity' => 'nullable|numeric|min:0',
            'bad_orders'      => 'nullable|numeric|min:0',
            'inspector'       => 'nullable|string|max:255',
        ]);

        $updateData = [
            'status'     => $request->status,
            'updated_at' => now(),
        ];

        if ($request->status === 'delivered') {
            $updateData['actual_quantity'] = $request->actual_quantity;
            $updateData['bad_orders']      = $request->bad_orders ?? 0;
            $updateData['inspector']       = $request->inspector;
        }

        DB::table('receiving_logistics1')
            ->where('id', $id)
            ->update($updateData);

        // Update drug inventory when status is set to delivered
        if ($request->status === 'delivered') {
            $receiving = DB::table('receiving_logistics1')->where('id', $id)->first();
            $receivedQty = $request->actual_quantity ?? $receiving->requested_quantity;
            $badOrders   = $request->bad_orders ?? 0;
            $netQty      = max(0, $receivedQty - $badOrders);

            $inventory = DB::table('drug_inventory_core2')
                ->where('drug_num', $receiving->drug_num)
                ->first();

            if ($inventory) {
                $newQuantity = $inventory->quantity + $netQty;
                $status = 'Stable';
                if ($newQuantity == 0)      $status = 'Out of Stock';
                elseif ($newQuantity <= 10) $status = 'Low Stock';
                elseif ($newQuantity <= 20) $status = 'Critical';

                DB::table('drug_inventory_core2')
                    ->where('drug_num', $receiving->drug_num)
                    ->update([
                        'quantity'   => $newQuantity,
                        'status'     => $status,
                        'updated_at' => now(),
                    ]);
            } else {
                $newQuantity = $netQty;
                $status = 'Stable';
                if ($newQuantity == 0)      $status = 'Out of Stock';
                elseif ($newQuantity <= 10) $status = 'Low Stock';
                elseif ($newQuantity <= 20) $status = 'Critical';

                DB::table('drug_inventory_core2')->insert([
                    'drug_num'   => $receiving->drug_num,
                    'drug_name'  => $receiving->drug_name,
                    'quantity'   => $newQuantity,
                    'status'     => $status,
                    'supplier'   => $receiving->selected_supplier,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'receiving'])
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Soft-delete a receiving record
     */
    public function destroyReceiving($id)
    {
        DB::table('receiving_logistics1')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'receiving'])
            ->with('success', 'Record deleted successfully.');
    }

    /**
     * Update inventory item
     */
    public function updateInventory(Request $request, $id)
    {
        $request->validate([
            'quantity'    => 'required|numeric|min:0',
            'status'      => 'required|in:Stable,Low Stock,Critical,Out of Stock',
            'supplier'    => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        DB::table('drug_inventory_core2')
            ->where('id', $id)
            ->update([
                'quantity'    => $request->quantity,
                'status'      => $request->status,
                'supplier'    => $request->supplier,
                'expiry_date' => $request->expiry_date,
                'updated_at'  => now(),
            ]);

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'inventory_control'])
            ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Delete inventory item
     */
    public function destroyInventory($id)
    {
        DB::table('drug_inventory_core2')
            ->where('id', $id)
            ->delete();

        return redirect()->route('admin.logistics1.warehouse.index', ['tab' => 'inventory_control'])
            ->with('success', 'Inventory item deleted successfully.');
    }
}