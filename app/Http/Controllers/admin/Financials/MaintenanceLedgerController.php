<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MaintenanceLedgerController extends Controller
{
    public function index()
    {
        $ledgerEntries = DB::table('maintenance_ledger_financials')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('admin.financials.ledger.maintenance', compact('ledgerEntries'));
    }
}