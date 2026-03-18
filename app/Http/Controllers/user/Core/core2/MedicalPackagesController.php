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
    // Ensure this exact name matches your web.php route
    public function packagesIndex(Request $request)
    {
        // Fetching 12 records per page to fit the grid layout
        $records = PackageDefinitionPricing::latest()->paginate(12);
        return view('core.core2.medical-packages.packages.index', compact('records'));
    }

    public function packagesCreate()
    {
        return view('core.core2.medical-packages.packages.create');
    }

    public function packagesStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id'        => 'required|string|max:50|unique:package_definition_pricing_core2,package_identifier',
            'package_name'      => 'required|string|max:255',
            'price'             => 'required|numeric|min:0',
            'includes_services' => 'nullable|string',
            'excludes_services' => 'nullable|string',
        ]);

        // Helper to transform textarea strings into clean JSON arrays
        $processInput = function($input) {
            return $input ? collect(preg_split('/[\n,]+/', $input))
                ->map(fn($item) => trim($item))
                ->filter()
                ->values()
                ->all() : [];
        };

        PackageDefinitionPricing::create([
            'package_identifier'      => $validated['package_id'],
            'package_description'     => $validated['package_name'],
            'price_list_node'         => $validated['price'],
            'included_services_state' => $processInput($request->includes_services),
            'excluded_services_state' => $processInput($request->excludes_services),
            'status'                  => 'active',
        ]);

        return redirect()->route('core2.medical-packages.packages.index')
            ->with('success', 'Package node successfully committed to the database.');
    
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
