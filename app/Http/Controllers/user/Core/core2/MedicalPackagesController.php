<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\PackageDefinitionPricing;
use App\Models\core2\PatientEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\core2\PatientPackageEnrollment; // This solves your error
use App\Models\core2\MedicalPackage;


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

   public function enrollmentStore(Request $request)
{
    $validated = $request->validate([
        'patient_id'      => 'required|string',
        'package_id'      => 'required|exists:package_definition_pricing_core2,id',
        'enrollment_date' => 'required|date',
    ]);

    // Fetch the package definition to "snapshot" the data
    $package = \App\Models\core2\MedicalPackage::findOrFail($validated['package_id']);

    \App\Models\core2\PatientPackageEnrollment::create([
        'patient_id'          => $validated['patient_id'],
        'package_id'          => $package->id,
        'package_identifier'  => $package->package_identifier, // From DB image
        'package_description' => $package->package_description,
        'total_price'         => $package->price_list_node,    // From DB image
        'amount_paid'         => 0.00,
        'payment_status'      => 'Partial',
        'progress_percent'    => 0,
        'status'              => 'active',
        'enrolled_at'         => $validated['enrollment_date'],
        'expires_at'          => \Carbon\Carbon::parse($validated['enrollment_date'])->addDays(30),
    ]);

    return view('core.core2.medical-packages.enrollment.index', compact('records'));
    }

    // Also double-check your other methods follow the same naming pattern
    public function enrollmentCreate()
    {
        $packages = MedicalPackage::all();
        return view('core.core2.medical-packages.enrollment.create', compact('packages'));
    }

}
