<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminLogistics1ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'needs_assessment');

        // -------------------------------------------------------
        // TAB 1: NEEDS ASSESSMENT
        // Inventory items needing restock (Low Stock / Critical / Out of Stock)
        // -------------------------------------------------------
        $inventoryQuery = DB::table('drug_inventory_core2')
            ->whereIn('status', ['Low Stock', 'Critical', 'Out of Stock']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $inventoryQuery->where(function ($q) use ($search) {
                $q->where('drug_name', 'like', "%$search%")
                  ->orWhere('drug_num',  'like', "%$search%");
            });
        }

        $inventory = $inventoryQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 2: VENDOR SELECTION
        // Distinct suppliers from drug_inventory_core2 shown as cards
        // -------------------------------------------------------
        $vendors = DB::table('drug_inventory_core2')
            ->select('supplier')
            ->whereNotNull('supplier')
            ->where('supplier', '!=', '')
            ->distinct()
            ->orderBy('supplier', 'asc')
            ->get();

        // -------------------------------------------------------
        // TAB 3: PURCHASE ORDERS
        // Procurement logs that have been approved and are being processed
        // -------------------------------------------------------
        $purchaseOrders = DB::table('procurement_log_logistics2 as p')
            ->leftJoin('employees as req_emp', 'p.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'p.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name  as req_last_name'
            )
            ->whereIn('p.status', ['approved', 'ordered', 'shipped'])
            ->orderBy('p.created_at', 'desc')
            ->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 4: PAYMENT PROCESSING
        // Procurement logs that have been delivered — awaiting or completed payment
        // -------------------------------------------------------
        $payments = DB::table('procurement_log_logistics2 as p')
            ->leftJoin('employees as req_emp', 'p.requested_by', '=', 'req_emp.employee_id')
            ->leftJoin('employees as del_emp', 'p.delivered_by', '=', 'del_emp.employee_id')
            ->select(
                'p.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name  as req_last_name',
                'del_emp.first_name as del_first_name',
                'del_emp.last_name  as del_last_name'
            )
            ->whereIn('p.status', ['delivered', 'paid'])
            ->orderBy('p.created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('admin._logistics1.procurement.index', compact(
            'activeTab',
            'inventory',
            'vendors',
            'purchaseOrders',
            'payments'
        ));
    }

    /**
     * Submit a new restock request (from Needs Assessment tab)
     */
    public function store(Request $request)
    {
        $request->validate([
            'drug_num'           => 'required',
            'drug_name'          => 'required',
            'requested_quantity' => 'required|numeric|min:1',
            'selected_supplier'  => 'required',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Unable to identify the requesting employee.');
        }

        DB::table('procurement_log_logistics2')->insert([
            'drug_num'           => $request->drug_num,
            'drug_name'          => $request->drug_name,
            'selected_supplier'  => $request->selected_supplier,
            'requested_quantity' => $request->requested_quantity,
            'status'             => 'pending',
            'requested_by'       => $employee->employee_id,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => 'needs_assessment'])
            ->with('success', 'Restock request submitted to Logistics 2.');
    }

    /**
     * Update procurement log status (used by Vendor Selection, Purchase Orders, Payment tabs)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,ordered,shipped,delivered,paid,cancelled',
        ]);

        DB::table('procurement_log_logistics2')
            ->where('id', $id)
            ->update([
                'status'     => $request->status,
                'updated_at' => now(),
            ]);

        $tabMap = [
            'pending'   => 'needs_assessment',
            'approved'  => 'vendor_selection',
            'ordered'   => 'purchase_orders',
            'shipped'   => 'purchase_orders',
            'delivered' => 'payment_processing',
            'paid'      => 'payment_processing',
            'cancelled' => 'needs_assessment',
        ];

        $tab = $tabMap[$request->status] ?? 'needs_assessment';

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => $tab])
            ->with('success', 'Status updated successfully.');
    }
}