<?php

namespace App\Http\Controllers\user\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\user\Hr\hr3\ClaimsHr3;
use Carbon\Carbon;

class UserClaimsController extends Controller
{
    public function store(Request $request)
    {
        $employee = Employee::where('user_id', Auth::id())->first();
        if (!$employee) return redirect()->back()->with('error', 'Employee not found.');

        $request->validate([
            'type' => 'required|in:Claim',
            'details' => 'required|string|max:2000',
            'claim_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'receipt' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $last = ClaimsHr3::orderBy('created_at','desc')->first();
        $lastNumber = $last ? (int) preg_replace('/\D/', '', $last->claim_id) : 0;
        $claim_id = 'CLM' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

        $receiptPath = $request->hasFile('receipt') ? $request->file('receipt')->store('claims_receipts','public') : null;

        ClaimsHr3::create([
            'claim_id' => $claim_id,
            'employee_id' => $employee->employee_id,
            'type' => 'Claim',
            'details' => $request->details,
            'claim_type' => $request->claim_type,
            'amount' => $request->amount,
            'receipt_path' => $receiptPath,
            'status' => 'pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', "Claim $claim_id submitted successfully.");
    }
}