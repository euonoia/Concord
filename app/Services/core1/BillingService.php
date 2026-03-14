<?php

namespace App\Services\core1;

use App\Models\user\Core\core1\Bill;
use App\Models\user\Core\core1\Payment;
use App\Models\core1\Encounter;
use App\Models\core1\Consultation;
use App\Models\core1\LabOrder;
use App\Models\core1\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    /**
     * Aggregate charges for an encounter and generate/update a bill.
     */
    public function aggregateCharges(Encounter $encounter)
    {
        return DB::transaction(function () use ($encounter) {
            $bill = Bill::firstOrNew(['encounter_id' => $encounter->id]);
            
            if (!$bill->exists) {
                $bill->bill_number = 'BILL-' . strtoupper(Str::random(8));
                $bill->patient_id = $encounter->patient_id;
                $bill->bill_date = now();
                $bill->status = 'pending';
            }

            $items = [];
            $subtotal = 0;

            // 1. Consultation Fees
            $consultation = Consultation::where('encounter_id', $encounter->id)->first();
            if ($consultation) {
                $items[] = ['desc' => 'General Consultation', 'qty' => 1, 'price' => 500];
                $subtotal += 500;
            }

            // 2. Lab Orders
            $labs = LabOrder::where('encounter_id', $encounter->id)->get();
            foreach ($labs as $lab) {
                $items[] = ['desc' => 'Lab: ' . $lab->test_name, 'qty' => 1, 'price' => 1200];
                $subtotal += 1200;
            }

            // 3. Prescriptions
            $rx = Prescription::where('encounter_id', $encounter->id)->get();
            foreach ($rx as $item) {
                $items[] = ['desc' => 'RX: ' . $item->medication, 'qty' => 1, 'price' => 300];
                $subtotal += 300;
            }

            $bill->items = $items;
            $bill->subtotal = $subtotal;
            $bill->tax = $subtotal * 0.12; // 12% VAT
            $bill->total = $bill->subtotal + $bill->tax;
            $bill->save();

            return $bill;
        });
    }

    /**
     * Record a payment against a bill.
     */
    public function recordPayment(Bill $bill, $amount, $method, $reference = null)
    {
        return DB::transaction(function () use ($bill, $amount, $method, $reference) {
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_reference' => $reference,
                'paid_at' => now()
            ]);

            // Update bill status
            $totalPaid = Payment::where('bill_id', $bill->id)->sum('amount');
            
            if ($totalPaid >= $bill->total) {
                $bill->status = 'paid';
            } elseif ($totalPaid > 0) {
                $bill->status = 'partial';
            }

            $bill->save();

            // If OPD encounter and paid, it might be ready for closing
            if ($bill->status === 'paid' && $bill->encounter->status === 'Pending Billing') {
                $bill->encounter->update(['status' => 'Closed']);
            }

            return $payment;
        });
    }
}
