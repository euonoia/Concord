<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminLogistics1ProcurementController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('drug_inventory_core2')->get();
        
        // Fetch logs to show history
        $logs = DB::table('procurement_log_logistics2')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin._logistics1.procurement.index', compact('suppliers', 'logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'drug_num' => 'required',
            'requested_quantity' => 'required|numeric|min:1',
            'selected_supplier' => 'required'
        ]);

        DB::table('procurement_log_logistics2')->insert([
            'drug_num' => $request->drug_num,
            'drug_name' => $request->drug_name,
            'selected_supplier' => $request->selected_supplier,
            'requested_quantity' => $request->requested_quantity,
            'status' => 'pending',
            'requested_by' => Auth::id() ?? 1, // Fallback for testing
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Restock request submitted to Procurement Log.');
    }
}