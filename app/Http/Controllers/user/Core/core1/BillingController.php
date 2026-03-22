<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\user\Core\core1\Bill;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['patient', 'validator'])->latest()->paginate(20);
        return view('core.core1.billing.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $bill->load('patient');
        
        if (request()->ajax()) {
            return response()->json([
                'bill' => $bill,
                'patient' => $bill->patient,
                'items' => $bill->items ?? []
            ]);
        }
        
        return view('core.core1.billing.show', compact('bill'));
    }
}

