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
        'formula_id' => 'required|string|max:50',
        'formula_name' => 'required|string|max:100',
        'ingredients_list' => 'nullable|string',
        'drug_id' => 'nullable|string|max:50',
        'dosage' => 'nullable|string|max:100',
        'preparation_method' => 'nullable|string',
        'status' => 'nullable|string|max:20',
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
            'quantity'        => 'required|integer|min:1',
        ]);

        Prescription::create($validated);

        return redirect()->route('core2.pharmacy.prescription.index')
            ->with('success', 'Prescription record added successfully.');
    }

  public function dispense(Request $request, Prescription $prescription): RedirectResponse
{
    // 1. Change the query to search 'drug_name' since that is what is being passed
    $inventory = DrugInventory::where('drug_name', trim($prescription->drug_id))->first();

    // 2. If it still fails, try a 'LIKE' search as a safety net for minor typos
    if (!$inventory) {
        $inventory = DrugInventory::where('drug_name', 'LIKE', '%' . trim($prescription->drug_id) . '%')->first();
    }

    // 3. Error Handling
    if (!$inventory) {
        return back()->with('error', "Drug record not found. Could not find '{$prescription->drug_id}' in the inventory drug_name column.");
    }

    if ($inventory->quantity <= 0) {
        return back()->with('error', "Insufficient stock for {$inventory->drug_name}.");
    }

    // 4. Update the records
    \DB::transaction(function () use ($prescription, $inventory) {
        $inventory->decrement('quantity', 1);

        $prescription->update([
            'status'        => 'Dispensed',
            'dispensed_at'  => now(),
            'pharmacist_id' => auth()->id(),
        ]);

        if ($prescription->core1_prescription_id) {
            $core1 = \App\Models\core1\Prescription::find($prescription->core1_prescription_id);
            if ($core1) {
                $core1->update(['status' => 'Dispensed']);
            }
        }
    });

    return back()->with('success', "Medication dispensed. {$inventory->drug_name} stock reduced to {$inventory->quantity}.");
}}