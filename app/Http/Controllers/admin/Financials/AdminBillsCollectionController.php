<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminBillsCollectionController extends Controller
{
    /**
     * List all bills with patient names
     */
    public function index()
    {
        $bills = DB::table('bills_core1')
            ->leftJoin('patients_core1', 'bills_core1.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_core1.*',
                'patients_core1.first_name',
                'patients_core1.last_name'
            )
            ->orderBy('bill_date', 'desc')
            ->get();

        return view('admin.financials.bills.index', compact('bills'));
    }

    /**
     * Show single bill details with patient name
     */
    public function show($id)
    {
        $bill = DB::table('bills_core1')
            ->leftJoin('patients_core1', 'bills_core1.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_core1.*',
                'patients_core1.first_name',
                'patients_core1.last_name'
            )
            ->where('bills_core1.id', $id)
            ->first();

        if (!$bill) {
            return redirect()->route('financials.bills.index')
                             ->with('error', 'Bill not found.');
        }

        $bill->items = json_decode($bill->items, true);

        return view('admin.financials.bills.show', compact('bill'));
    }

    /**
     * Mark a bill as paid
     */
    public function markAsPaid(Request $request, $id)
    {
        $bill = DB::table('bills_core1')->where('id', $id)->first();

        if (!$bill) {
            return redirect()->route('financials.bills.index')
                             ->with('error', 'Bill not found.');
        }

        DB::table('bills_core1')->where('id', $id)->update([
            'status' => 'paid',
            'payment_method' => $request->payment_method ?? 'cash',
            'paid_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('financials.bills.index')
                         ->with('success', 'Bill marked as paid.');
    }
}