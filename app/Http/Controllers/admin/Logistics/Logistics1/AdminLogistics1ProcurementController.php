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
                  ->orWhere('drug_num', 'like', "%$search%");
            });
        }

        $inventory = $inventoryQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 2: VENDOR SELECTION
        // -------------------------------------------------------
        $vendors = DB::table('vendor_portal_logistics2')
            ->whereIn('status', ['active', 'pending_approval'])
            ->orderBy('vendor_name', 'asc')
            ->get();

        // -------------------------------------------------------
        // TAB 3: PURCHASE ORDERS
        // -------------------------------------------------------
        $poQuery = DB::table('purchase_orders_logistics1 as po')
            ->leftJoin('employees as req_emp', 'po.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'po.id', 'po.po_number', 'po.drug_num', 'po.drug_name',
                'po.requested_quantity', 'po.selected_supplier',
                'po.requested_date', 'po.expected_delivery_date',
                'po.status', 'po.requested_by', 'po.delivered_by',
                'po.address', 'po.amount', 'po.created_at', 'po.updated_at',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name as req_last_name'
            )
            ->whereNull('po.deleted_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $poQuery->where(function ($q) use ($search) {
                $q->where('po.drug_name',          'like', "%$search%")
                  ->orWhere('po.drug_num',          'like', "%$search%")
                  ->orWhere('po.selected_supplier', 'like', "%$search%")
                  ->orWhere('po.po_number',          'like', "%$search%");
            });
        }

        if ($request->filled('po_status')) {
            $poQuery->where('po.status', $request->input('po_status'));
        }

        $purchaseOrders = $poQuery->orderBy('po.created_at', 'desc')
            ->paginate(10)->withQueryString();

        // -------------------------------------------------------
        // TAB 4: PAYMENT PROCESSING
        // Fetched from vendor_bills
        // -------------------------------------------------------
        $payments = DB::table('vendor_bills as vb')
            ->leftJoin('purchase_orders_logistics1 as po', 'vb.po_number', '=', 'po.po_number')
            ->leftJoin('employees as req_emp', 'po.requested_by', '=', 'req_emp.employee_id')
            ->select(
                'vb.id', 'vb.invoice', 'vb.po_number', 'vb.delivery_date',
                'vb.supplier', 'vb.amount', 'vb.status as bill_status',
                'vb.created_at',
                'po.drug_name', 'po.drug_num', 'po.requested_quantity',
                'po.delivered_by', 'po.address',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name as req_last_name'
            )
            ->orderBy('vb.created_at', 'desc')
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
            'source'             => 'nullable|string|max:100',
            'amount'             => 'nullable|numeric|min:0',
            'vehicle'            => 'nullable|string|max:255',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Unable to identify the requesting employee.');
        }

        do {
            $poNumber = 'PO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        } while (DB::table('purchase_orders_logistics1')->where('po_number', $poNumber)->exists());

        $data = [
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
            'source'                 => $request->source ?? null,
            'amount'                 => $request->amount ?? 0.00,
            'vehicle'                => $request->vehicle ?? null,
            'created_at'             => now(),
            'updated_at'             => now(),
        ];

        DB::table('purchase_orders_logistics1')->insert($data);

        // Also insert into warehouse table if request came from inventory control
        if ($request->source === 'inventory_control') {
            DB::table('warehouse_purchaseorders_logistics1')->insert($data);
        }

        DB::table('drug_inventory_core2')
            ->where('drug_num', $request->drug_num)
            ->update(['status' => 'Ordered', 'updated_at' => now()]);

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => 'purchase_orders'])
            ->with('success', 'Purchase Order ' . $poNumber . ' created successfully.');
    }

    /**
     * Update procurement log status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,received,delivered,in_transit',
        ]);

        DB::table('purchase_orders_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'status'     => $request->status,
                'updated_at' => now(),
            ]);

        // When status is set to delivered, insert into vendor_bills
        if ($request->status === 'delivered') {
            $po = DB::table('purchase_orders_logistics1')->where('id', $id)->first();

            if ($po) {
                // Auto-generate unique invoice number
                do {
                    $invoice = 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
                } while (DB::table('vendor_bills')->where('invoice', $invoice)->exists());

                DB::table('vendor_bills')->insert([
                    'invoice'       => $invoice,
                    'po_number'     => $po->po_number,
                    'delivery_date' => now()->toDateString(),
                    'supplier'      => $po->selected_supplier,
                    'amount'        => $po->amount ?? 0.00,
                    'status'        => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        $tabMap = [
            'pending'    => 'needs_assessment',
            'approved'   => 'purchase_orders',
            'in_transit' => 'purchase_orders',
            'delivered'  => 'payment_processing',
            'received'   => 'payment_processing',
        ];

        $tab = $tabMap[$request->status] ?? 'needs_assessment';

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => $tab])
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Mark a vendor bill as paid
     */
    public function payBill($id)
    {
        DB::table('vendor_bills')
            ->where('id', $id)
            ->update([
                'status'     => 'paid',
                'updated_at' => now(),
            ]);

        // Also update the PO status to paid
        $bill = DB::table('vendor_bills')->where('id', $id)->first();
        if ($bill) {
            DB::table('purchase_orders_logistics1')
                ->where('po_number', $bill->po_number)
                ->update(['status' => 'paid', 'updated_at' => now()]);
        }

        return redirect()->route('admin.logistics1.procurement.index', ['tab' => 'payment_processing'])
            ->with('success', 'Bill marked as paid successfully.');
    }
    public function goodsReceipt(Request $request)
    {
        $vendor = $request->input('vendor');

        $pos = DB::table('vendor_bills as vb')
            ->leftJoin('purchase_orders_logistics1 as po', 'vb.po_number', '=', 'po.po_number')
            ->where('vb.supplier', $vendor)
            ->where('vb.status', 'paid')
            ->orderBy('vb.updated_at', 'desc')
            ->get([
                'vb.invoice', 'vb.po_number', 'vb.delivery_date',
                'vb.amount', 'vb.status as bill_status',
                'po.drug_name', 'po.drug_num', 'po.requested_quantity',
                'po.delivered_by',
            ])
            ->map(function ($row) {
                return [
                    'invoice'            => $row->invoice,
                    'po_number'          => $row->po_number,
                    'drug_num'           => $row->drug_num,
                    'drug_name'          => $row->drug_name,
                    'requested_quantity' => $row->requested_quantity,
                    'delivered_by'       => $row->delivered_by,
                    'delivery_date'      => \Carbon\Carbon::parse($row->delivery_date)->format('M d, Y'),
                    'amount'             => number_format($row->amount, 2),
                ];
            });

        return response()->json($pos);
    }
}