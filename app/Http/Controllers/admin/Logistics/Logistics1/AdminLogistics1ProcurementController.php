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
        // -------------------------------------------------------
        $vendors = DB::table('vendor_portal_logistics2')
            ->whereNull('deleted_at')
            ->whereIn('status', ['active', 'pending_approval'])
            ->orderBy('vendor_name', 'asc')
            ->get();

        // -------------------------------------------------------
        // TAB 3: PURCHASE ORDERS
        // -------------------------------------------------------
        $poQuery = DB::table('purchase_orders_logistics1 as po')
            ->leftJoin('employees as req_emp', 'po.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'po.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name  as req_last_name'
            )
            ->whereNull('po.deleted_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $poQuery->where(function ($q) use ($search) {
                $q->where('po.drug_name',         'like', "%$search%")
                  ->orWhere('po.drug_num',         'like', "%$search%")
                  ->orWhere('po.selected_supplier','like', "%$search%")
                  ->orWhere('po.po_number',         'like', "%$search%");
            });
        }

        if ($request->filled('po_status')) {
            $poQuery->where('po.status', $request->input('po_status'));
        }

        $purchaseOrders = $poQuery->orderBy('po.created_at', 'desc')
            ->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 4: PAYMENT PROCESSING
        // -------------------------------------------------------
        $payments = DB::table('purchase_orders_logistics1 as po')
            ->leftJoin('employees as req_emp', 'po.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'po.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name  as req_last_name'
            )
            ->whereNull('po.deleted_at')
            ->whereIn('po.status', ['delivered', 'paid'])
            ->orderBy('po.created_at', 'desc')
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
            'delivered_by'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'notes'              => 'nullable|string',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Unable to identify the requesting employee.');
        }

        // Auto-generate unique PO number
        do {
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        } while (DB::table('purchase_orders_logistics1')->where('po_number', $poNumber)->exists());

        DB::table('purchase_orders_logistics1')->insert([
            'po_number'              => $poNumber,
            'drug_num'               => $request->drug_num,
            'drug_name'              => $request->drug_name,
            'selected_supplier'      => $request->selected_supplier,
            'requested_quantity'     => $request->requested_quantity,
            'requested_date'         => $request->requested_date ?? now()->toDateString(),
            'expected_delivery_date' => $request->expected_delivery_date ?? now()->addMonth()->toDateString(),
            'status'                 => 'pending',
            'requested_by'           => $employee->employee_id,
            'delivered_by'           => $request->delivered_by,
            'address'                => $request->address,
            'notes'                  => $request->notes,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // Mark inventory as Ordered
        DB::table('drug_inventory_core2')
            ->where('drug_num', $request->drug_num)
            ->update([
                'status'     => 'Ordered',
                'updated_at' => now(),
            ]);

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => 'purchase_orders'])
            ->with('success', 'Purchase Order ' . $poNumber . ' created successfully.');
    }

    /**
     * Update procurement log status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,ordered,shipped,delivered,paid,cancelled',
        ]);

        DB::table('purchase_orders_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
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