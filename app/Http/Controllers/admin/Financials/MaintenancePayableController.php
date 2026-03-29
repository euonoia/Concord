<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MaintenancePayableController extends Controller
{
    /**
     * Ensure only authorized Financials admins can access these methods
     */
    private function authorizeFinancialAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_financials'])) {
            abort(403, 'Unauthorized action for Financials.');
        }
    }

    /**
     * List all unpaid maintenance costs
     */
    public function index()
    {
        $this->authorizeFinancialAdmin(); // enforce role check

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
        $this->authorizeFinancialAdmin(); // enforce role check

        DB::table('maintenance_ledger_financials')
            ->where('id', $id)
            ->update([
                'payment_status' => 'paid',
                'updated_at' => Carbon::now()
            ]);

        return redirect()->back()->with('success', 'Maintenance payable marked as paid.');
    }
}