<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminLogistics1ProcurementController extends Controller
{
    /**
     * Ensure user is Logistics1 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics1') {
            abort(403, 'Unauthorized access to Logistics1 Procurement.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeLogisticsAdmin();

        // Inventory for items needing restock
        $inventoryQuery = DB::table('drug_inventory_core2')
            ->whereIn('status', ['Low Stock', 'Critical', 'Out of Stock']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $inventoryQuery->where(function($q) use ($search) {
                $q->where('drug_name', 'like', "%$search%")
                  ->orWhere('drug_num', 'like', "%$search%");
            });
        }

        $inventory = $inventoryQuery->paginate(10);

        // Procurement logs with employee names
        $logs = DB::table('procurement_log_logistics2 as p')
            ->leftJoin('employees as req_emp', 'p.requested_by', '=', 'req_emp.employee_id')
            ->leftJoin('employees as del_emp', 'p.delivered_by', '=', 'del_emp.employee_id')
            ->select(
                'p.*',
                'req_emp.first_name as req_first_name',
                'req_emp.last_name as req_last_name',
                'del_emp.first_name as del_first_name',
                'del_emp.last_name as del_last_name'
            )
            ->orderBy('p.created_at', 'desc')
            ->paginate(10);

        return view('admin._logistics1.procurement.index', compact('inventory', 'logs'));
    }

    public function store(Request $request)
    {
        $this->authorizeLogisticsAdmin();

        $request->validate([
            'drug_num' => 'required',
            'drug_name' => 'required',
            'requested_quantity' => 'required|numeric|min:1',
            'selected_supplier' => 'required'
        ]);

        // Get current authenticated employee
        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Unable to identify the requesting employee.');
        }

        // Store just the employee ID
        DB::table('procurement_log_logistics2')->insert([
            'drug_num' => $request->drug_num,
            'drug_name' => $request->drug_name,
            'selected_supplier' => $request->selected_supplier,
            'requested_quantity' => $request->requested_quantity,
            'status' => 'pending',
            'requested_by' => $employee->employee_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Restock request submitted to Logistics 2.');
    }
}