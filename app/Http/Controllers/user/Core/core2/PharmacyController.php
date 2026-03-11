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
            'expiry_date' => 'nullable|date',
            'supplier'    => 'nullable|string|max:100',
        ]);

        DrugInventory::create($validated);

        return redirect()->route('core2.pharmacy.drug-inventory.index')
            ->with('success', 'Drug inventory record added successfully.');
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
}
