<?php

namespace App\Http\Controllers\admin\Financials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\user\Hr\hr3\ClaimsHr3;
use App\Models\admin\Financials\ReimburseFinancial;

class AdminFinancialsReimbursementController extends Controller
{

    private function authorizeFinancialAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_financials'])) {
            abort(403,'Unauthorized action for Financials.');
        }
    }


    public function index()
    {
        $this->authorizeFinancialAdmin();

        $claims = ClaimsHr3::with(['employee:id,employee_id,first_name,last_name'])
            ->where('status','approved')
            ->orderBy('created_at','desc')
            ->get();

        $reimbursed = ReimburseFinancial::with(['employee:id,employee_id,first_name,last_name'])
            ->orderBy('created_at','desc')
            ->get();

        return view(
            'admin.financials.disbursement.reimbursement',
            compact('claims','reimbursed')
        );
    }



   public function reimburse($id)
{
    $this->authorizeFinancialAdmin();

    try {

        DB::transaction(function () use ($id) {

            $claim = ClaimsHr3::findOrFail($id);

            if ($claim->status !== 'approved') {
                abort(400,'Claim not eligible for reimbursement.');
            }

            // Get logged in admin employee record
            $handler = \App\Models\Employee::where('user_id', Auth::id())->first();

            if (!$handler) {
                abort(500,'Employee profile for this admin was not found.');
            }

            ReimburseFinancial::create([
                'claim_id'     => $claim->claim_id,
                'employee_id'  => $claim->employee_id,
                'claim_type'   => $claim->claim_type,
                'amount'       => $claim->amount,
                'description'  => $claim->description,
                'receipt_path' => $claim->receipt_path,
                'validated_by' => $handler->employee_id
            ]);

            $claim->update([
                'status' => 'reimbursed'
            ]);

        });

        return redirect()->back()->with(
            'success',
            'Reimbursement completed successfully.'
        );

    } catch (\Exception $e) {

        Log::error("Financial Reimbursement Error: ".$e->getMessage());

        return redirect()->back()->with(
            'error',
            'Reimbursement failed: '.$e->getMessage()
        );
    }
}
}