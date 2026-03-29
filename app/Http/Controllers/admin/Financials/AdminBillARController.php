<?php
namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminBillARController extends Controller
{
    /**
     * Ensure only authorized Financials admins can access these methods
     */
    private function authorizeFinancialAdmin()
    {
        // Only users with these role slugs can access
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_financials'])) {
            abort(403, 'Unauthorized action for Financials.');
        }
    }

    /**
     * View all pending Receivables (AR)
     * This represents the "Receivable Data" arrow in your BPA
     */
    public function indexAR()
    {
        $this->authorizeFinancialAdmin(); // enforce role check

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
        $this->authorizeFinancialAdmin(); // enforce role check

        DB::table('bills_core1')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Bill validated and moved to Collections.');
    }
}