<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminBillsCollectionController extends Controller
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
     * List only bills that have been APPROVED by AR
     */
    public function index()
    {
        $this->authorizeFinancialAdmin(); // enforce role check

        $bills = DB::table('bills_core1')
            ->leftJoin('patients_core1', 'bills_core1.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_core1.*',
                'patients_core1.first_name as patient_first_name',
                'patients_core1.last_name as patient_last_name'
            )
            ->where('bills_core1.status', 'approved') 
            ->orderBy('bill_date', 'desc')
            ->get();

        return view('admin.financials.bills.index', compact('bills'));
    }

    /**
     * Show single bill details
     */
    public function show($id)
    {
        $this->authorizeFinancialAdmin(); // enforce role check

        $bill = DB::table('bills_core1')
            ->leftJoin('patients_core1', 'bills_core1.patient_id', '=', 'patients_core1.id')
            ->select(
                'bills_core1.*',
                'patients_core1.first_name as patient_first_name',
                'patients_core1.last_name as patient_last_name'
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
        $this->authorizeFinancialAdmin(); // enforce role check

        $bill = DB::table('bills_core1')
            ->where('id', $id)
            ->where('status', 'approved') 
            ->first();

        if (!$bill) {
            return redirect()->route('financials.bills.index')
                             ->with('error', 'Bill not found or not yet approved by AR.');
        }

        $now = Carbon::now();

        $loggedInEmployee = DB::table('employees')
            ->where('user_id', Auth::id())
            ->first();

        $validatorIdOrName = $loggedInEmployee
            ? $loggedInEmployee->employee_id 
            : 'SYSTEM';

        DB::transaction(function () use ($id, $bill, $request, $now, $validatorIdOrName) {
            
            DB::table('bills_core1')->where('id', $id)->update([
                'status' => 'paid',
                'payment_method' => $request->payment_method ?? 'cash',
                'paid_at' => $now,
                'validated_by' => $validatorIdOrName,
                'updated_at' => $now,
            ]);

            DB::table('bills_ledger_financials')->insert([
                'bill_number' => $bill->bill_number,
                'patient_id' => $bill->patient_id,
                'encounter_id' => $bill->encounter_id,
                'bill_date' => $bill->bill_date,
                'due_date' => $bill->due_date,
                'items' => $bill->items,
                'subtotal' => $bill->subtotal,
                'tax' => $bill->tax,
                'discount' => $bill->discount,
                'total' => $bill->total,
                'status' => 'paid',
                'payment_method' => $request->payment_method ?? 'cash',
                'paid_at' => $now,
                'validated_by' => $validatorIdOrName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return redirect()->route('financials.bills.index')
                         ->with('success', 'Payment successful! Record moved to Ledger.');
    }
}