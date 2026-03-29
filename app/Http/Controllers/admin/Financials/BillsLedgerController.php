<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BillsLedgerController extends Controller
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
     * Display all paid bills in the Bills Ledger
     */
    public function index()
    {
        $this->authorizeFinancialAdmin(); // enforce role check

        $ledgerEntries = DB::table('bills_ledger_financials')
            ->leftJoin('patients_core1', 'bills_ledger_financials.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_ledger_financials.*',
                'patients_core1.first_name as patient_first_name',
                'patients_core1.last_name as patient_last_name'
            )
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('admin.financials.ledger.bills_ledger', compact('ledgerEntries'));
    }

    /**
     * Show single ledger entry
     */
    public function show($id)
    {
        $this->authorizeFinancialAdmin(); // enforce role check

        $entry = DB::table('bills_ledger_financials')
            ->leftJoin('patients_core1', 'bills_ledger_financials.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_ledger_financials.*',
                'patients_core1.first_name as patient_first_name',
                'patients_core1.last_name as patient_last_name'
            )
            ->where('bills_ledger_financials.id', $id)
            ->first();

        if (!$entry) {
            return redirect()->route('financials.bills-ledger.index')
                             ->with('error', 'Ledger entry not found.');
        }

        // Decode items JSON for display
        $entry->items = json_decode($entry->items, true);

        return view('admin.financials.ledger.show_bill_ledger', compact('entry'));
    }
}