<?php

namespace App\Http\Controllers\user\Core\core1;

use App\Http\Controllers\Controller;
use App\Models\core1\Bill;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $bills = Bill::with('patient')->latest()->paginate(20);
        return view('core.core1.billing.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $bill->load('patient');
        return view('core.core1.billing.show', compact('bill'));
    }
}

