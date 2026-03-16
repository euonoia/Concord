<?php
namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminBillARController extends Controller
{
    /**
     * View all pending Receivables (AR)
     * This represents the "Receivable Data" arrow in your BPA
     */
    public function indexAR()
    {
        $receivables = DB::table('bills_core1')
            ->leftJoin('patients_core1', 'bills_core1.patient_id', '=', 'patients_core1.id')
            ->select('bills_core1.*', 'patients_core1.first_name', 'patients_core1.last_name')
            ->where('bills_core1.status', 'pending') // Only show what we haven't collected
            ->orderBy('due_date', 'asc')
            ->get();

        return view('admin.financials.apar.receivables', compact('receivables'));
    }

    /**
     * Move a bill from "Core" to "Validated AR"
     * This is the "Invoice Approval" step
     */
    public function approveForCollection($id)
    {
        DB::table('bills_core1')->where('id', $id)->update([
            'status' => 'approved_for_collection',
            'updated_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Bill validated and moved to Collections.');
    }
}