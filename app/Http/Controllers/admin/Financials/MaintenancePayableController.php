<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenancePayableController extends Controller
{
    /**
     * List all unpaid maintenance costs
     */
    public function index()
    {
        $payables = DB::table('maintenance_ledger_financials')
            ->where('payment_status', 'unpaid')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('admin.financials.apar.maintenance_payable', compact('payables'));
    }

    /**
     * Mark a maintenance payable as paid
     */
    public function markAsPaid($id)
    {
        DB::table('maintenance_ledger_financials')
            ->where('id', $id)
            ->update([
                'payment_status' => 'paid',
                'updated_at' => Carbon::now()
            ]);

        return redirect()->back()->with('success', 'Maintenance payable marked as paid.');
    }
}