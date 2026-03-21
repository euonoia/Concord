<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAssetManagementController extends Controller
{
    /**
     * Display the asset management dashboard.
     * Columns used: id, asset_code, asset_name, serial_number, category,
     *               location, status, condition_status, purchase_date,
     *               purchase_cost, supplier, warranty_expiry,
     *               last_maintained_at, notes, created_by, created_at, deleted_at
     */
    public function index(Request $request)
    {
        $query = DB::table('assets_logistics1')
            ->whereNull('deleted_at'); // respect soft deletes

        // Search by name, code, or serial number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('asset_name',    'like', "%$search%")
                  ->orWhere('asset_code',  'like', "%$search%")
                  ->orWhere('serial_number','like', "%$search%")
                  ->orWhere('supplier',    'like', "%$search%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by condition
        if ($request->filled('condition_status')) {
            $query->where('condition_status', $request->input('condition_status'));
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(10);

        // -------------------------------------------------------
        // Fleet vehicles from fleet_management_logistics2
        // -------------------------------------------------------
        $fleetQuery = DB::table('fleet_management_logistics2');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $fleetQuery->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%$search%")
                  ->orWhere('vehicle_type', 'like', "%$search%")
                  ->orWhere('model', 'like', "%$search%");
            });
        }

        if ($request->filled('fleet_status')) {
            $fleetQuery->where('status', $request->input('fleet_status'));
        }

        $fleet = $fleetQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'fleet_page');

        $activeTab = $request->get('tab', 'assets');

        // Vendors for supplier dropdown
        $vendors = DB::table('vendor_portal_logistics2')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->orderBy('vendor_name', 'asc')
            ->get();

        return view('admin._logistics1.assets.index', compact('assets', 'fleet', 'activeTab', 'vendors'));
    }

    /**
     * Store a new asset.
     * Inserts into: assets_logistics1
     * All columns: asset_code, asset_name, serial_number, category, location,
     *              status, condition_status, purchase_date, purchase_cost,
     *              supplier, warranty_expiry, last_maintained_at,
     *              notes, created_by, created_at, updated_at
     */
    /**
     * Auto-generate a unique asset code: AST-YYYYMMDD-XXXX
     */
    private function generateAssetCode(): string
    {
        do {
            $code = 'AST-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        } while (DB::table('assets_logistics1')->where('asset_code', $code)->exists());

        return $code;
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_name'    => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets_logistics1,serial_number',
            'category'      => 'required|in:Equipment,Furniture,IT Hardware,Tools',
            'location'      => 'nullable|string|max:255',
            'status'        => 'required|in:active,inactive,under_repair,disposed',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier'      => 'nullable|string|max:255',
            'asset_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        // Handle single image upload
        $imagePath = null;
        if ($request->hasFile('asset_image')) {
            $imagePath = $request->file('asset_image')->store('assets', 'public');
        }

        DB::table('assets_logistics1')->insert([
            'asset_code'       => $this->generateAssetCode(),
            'asset_name'       => $request->asset_name,
            'serial_number'    => $request->serial_number,
            'category'         => $request->category,
            'location'         => $request->location,
            'status'           => $request->status,
            'condition_status' => 'excellent',
            'purchase_date'    => now()->toDateString(),
            'purchase_cost'    => $request->purchase_cost ?? 0.00,
            'supplier'         => $request->supplier,
            'asset_image'      => $imagePath,
            'created_by'       => $employee ? $employee->employee_id : null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->back()->with('success', 'Asset added successfully.');
    }

    /**
     * Update an existing asset.
     * Updates: asset_name, serial_number, category, location, status,
     *          condition_status, purchase_date, purchase_cost, supplier,
     *          warranty_expiry, last_maintained_at, notes, updated_at
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_name'       => 'required|string|max:255',
            'serial_number'    => 'nullable|string|max:255|unique:assets_logistics1,serial_number,' . $id,
            'category'         => 'required|in:Equipment,Furniture,IT Hardware,Tools',
            'location'         => 'nullable|string|max:255',
            'status'           => 'required|in:active,inactive,under_repair,disposed',
            'condition_status' => 'required|in:excellent,good,fair,poor',
            'purchase_cost'    => 'nullable|numeric|min:0',
            'supplier'         => 'nullable|string|max:255',
            'asset_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $updateData = [
            'asset_name'       => $request->asset_name,
            'serial_number'    => $request->serial_number,
            'category'         => $request->category,
            'location'         => $request->location,
            'status'           => $request->status,
            'condition_status' => $request->condition_status,
            'purchase_cost'    => $request->purchase_cost ?? 0.00,
            'supplier'         => $request->supplier,
            'updated_at'       => now(),
        ];

        if ($request->hasFile('asset_image')) {
            $updateData['asset_image'] = $request->file('asset_image')->store('assets', 'public');
        }

        DB::table('assets_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update($updateData);

        return redirect()->back()->with('success', 'Asset updated successfully.');
    }

    /**
     * Soft-delete an asset.
     * Sets deleted_at timestamp instead of permanently removing the row.
     */
    public function destroy($id)
    {
        DB::table('assets_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }
}