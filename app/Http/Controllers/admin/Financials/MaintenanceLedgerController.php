<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MaintenanceLedgerController extends Controller
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
     * Display all maintenance ledger entries
     */
    public function index()
    {
        $this->authorizeFinancialAdmin(); // enforce role check

        $ledgerEntries = DB::table('maintenance_ledger_financials')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('admin.financials.ledger.maintenance', compact('ledgerEntries'));
    }
}