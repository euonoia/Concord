<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\DrugInventory;
use App\Models\core2\FormulaManagement;
use App\Models\core2\Prescription;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PharmacyController extends Controller
{
    // ── Drug Inventory ─────────────────────────────────────────────────────────

    public function drugInventoryIndex(Request $request)
    {
        $records = DrugInventory::latest()->paginate(15);
        return view('core.core2.pharmacy.drug-inventory.index', compact('records'));
    }

    public function drugInventoryCreate()
    {
        return view('core.core2.pharmacy.drug-inventory.create');
    }

    public function drugInventoryStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'drug_num'    => 'required|string|max:50',
            'drug_name'   => 'required|string|max:100',
            'quantity'    => 'required|integer|min:0',

        ]);

        DrugInventory::create($validated);

        return redirect()->route('core2.pharmacy.drug-inventory.index')
            ->with('success', 'Drug inventory record added successfully.');
    }
    public function drugInventoryRequest()
{
    return view('core.core2.pharmacy.drug-inventory.requestform');
}

    // ── Formula Management ─────────────────────────────────────────────────────

    public function formulaManagementIndex(Request $request)
    {
        $records = FormulaManagement::latest()->paginate(15);
        return view('core.core2.pharmacy.formula-management.index', compact('records'));
    }

    public function formulaManagementCreate()
    {
        return view('core.core2.pharmacy.formula-management.create');
    }

    public function formulaManagementStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'formula_id'       => 'required|string|max:50',
            'formula_name'     => 'required|string|max:100',
            'ingredients_list' => 'nullable|string',
            'drug_id'          => 'nullable|string|max:50',
        ]);

        FormulaManagement::create($validated);

        return redirect()->route('core2.pharmacy.formula-management.index')
            ->with('success', 'Formula management record added successfully.');
    }

    // ── Prescriptions ──────────────────────────────────────────────────────────

    public function prescriptionIndex(Request $request)
    {
        $records = Prescription::latest()->paginate(15);
        return view('core.core2.pharmacy.prescription.index', compact('records'));
    }

    public function prescriptionCreate()
    {
        return view('core.core2.pharmacy.prescription.create');
    }

    public function prescriptionStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prescription_id' => 'required|string|max:50',
            'patient_id'      => 'nullable|integer',
            'doctor_id'       => 'nullable|integer',
            'date'            => 'nullable|date',
            'drug_id'         => 'nullable|string|max:50',
        ]);

        Prescription::create($validated);

        return redirect()->route('core2.pharmacy.prescription.index')
            ->with('success', 'Prescription record added successfully.');
    }

  public function dispense(Request $request, Prescription $prescription): RedirectResponse
{
    // 1. Find the drug using the drug_num from the prescription
    $inventory = DrugInventory::where('drug_num', $prescription->drug_num)->first();

    // 2. Check if the drug exists and has stock
    if (!$inventory) {
        return back()->with('error', 'Drug record not found.');
    }

    if ($inventory->quantity <= 0) {
        return back()->with('error', 'Out of stock! Cannot dispense ' . $inventory->drug_name);
    }

    // 3. Update everything in one go
    \DB::transaction(function () use ($prescription, $inventory) {
        // Reduce the inventory quantity by 1 (or $prescription->quantity if you have it)
        $inventory->decrement('quantity', 1);

        // Mark prescription as Dispensed
        $prescription->update([
            'status'        => 'Dispensed',
            'dispensed_at'  => now(),
            'pharmacist_id' => auth()->id(),
        ]);

        // 4. Sync to Core 1 if link exists
        if ($prescription->core1_prescription_id) {
            $core1 = \App\Models\core1\Prescription::find($prescription->core1_prescription_id);
            if ($core1) {
                $core1->update(['status' => 'Dispensed']);
            }
        }
    });

    return back()->with('success', 'Stock updated for ' . $inventory->drug_name);
}}