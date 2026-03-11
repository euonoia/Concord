<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\PackageDefinitionPricing;
use App\Models\core2\PatientEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class MedicalPackagesController extends Controller
{
    // ── Package Definition & Pricing ────────────────────────────────────────────

    public function packagesIndex(Request $request)
    {
        $records = PackageDefinitionPricing::latest()->paginate(15);
        return view('core.core2.medical-packages.packages.index', compact('records'));
    }

    public function packagesCreate()
    {
        return view('core.core2.medical-packages.packages.create');
    }

    public function packagesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id'       => 'required|string|max:50',
            'package_name'     => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'includes_services'=> 'nullable|string',
        ]);

        PackageDefinitionPricing::create($validated);

        return redirect()->route('core2.medical-packages.packages.index')
            ->with('success', 'Package record added successfully.');
    }

    // ── Patient Package Enrollment ──────────────────────────────────────────────

    public function enrollmentIndex(Request $request)
    {
        $records = PatientEnrollment::latest()->paginate(15);
        return view('core.core2.medical-packages.enrollment.index', compact('records'));
    }

    public function enrollmentCreate()
    {
        return view('core.core2.medical-packages.enrollment.create');
    }

    public function enrollmentStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_identifier'     => 'required|string|max:50',
            'package_description'    => 'nullable|string',
            'price_list_node'        => 'nullable|string|max:100',
            'included_services_state'=> 'nullable|string|max:100',
        ]);

        PatientEnrollment::create($validated);

        return redirect()->route('core2.medical-packages.enrollment.index')
            ->with('success', 'Enrollment record added successfully.');
    }
}
