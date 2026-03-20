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

        return view('admin._logistics1.assets.index', compact('assets'));
    }

    /**
     * Store a new asset.
     * Inserts into: assets_logistics1
     * All columns: asset_code, asset_name, serial_number, category, location,
     *              status, condition_status, purchase_date, purchase_cost,
     *              supplier, warranty_expiry, last_maintained_at,
     *              notes, created_by, created_at, updated_at
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_code'        => 'required|string|max:100|unique:assets_logistics1,asset_code',
            'asset_name'        => 'required|string|max:255',
            'serial_number'     => 'nullable|string|max:255|unique:assets_logistics1,serial_number',
            'category'          => 'required|string|max:100',
            'location'          => 'nullable|string|max:255',
            'status'            => 'required|in:active,inactive,under_repair,disposed',
            'condition_status'  => 'required|in:excellent,good,fair,poor',
            'purchase_date'     => 'nullable|date',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'supplier'          => 'nullable|string|max:255',
            'warranty_expiry'   => 'nullable|date',
            'last_maintained_at'=> 'nullable|date',
            'notes'             => 'nullable|string',
        ]);

        $employee = DB::table('employees')->where('user_id', Auth::id())->first();

        DB::table('assets_logistics1')->insert([
            'asset_code'         => $request->asset_code,
            'asset_name'         => $request->asset_name,
            'serial_number'      => $request->serial_number,
            'category'           => $request->category,
            'location'           => $request->location,
            'status'             => $request->status,
            'condition_status'   => $request->condition_status,
            'purchase_date'      => $request->purchase_date,
            'purchase_cost'      => $request->purchase_cost      ?? 0.00,
            'supplier'           => $request->supplier,
            'warranty_expiry'    => $request->warranty_expiry,
            'last_maintained_at' => $request->last_maintained_at,
            'notes'              => $request->notes,
            'created_by'         => $employee ? $employee->employee_id : null,
            'created_at'         => now(),
            'updated_at'         => now(),
            // deleted_at left NULL (active record)
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
            'asset_name'        => 'required|string|max:255',
            'serial_number'     => 'nullable|string|max:255|unique:assets_logistics1,serial_number,' . $id,
            'category'          => 'required|string|max:100',
            'location'          => 'nullable|string|max:255',
            'status'            => 'required|in:active,inactive,under_repair,disposed',
            'condition_status'  => 'required|in:excellent,good,fair,poor',
            'purchase_date'     => 'nullable|date',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'supplier'          => 'nullable|string|max:255',
            'warranty_expiry'   => 'nullable|date',
            'last_maintained_at'=> 'nullable|date',
            'notes'             => 'nullable|string',
        ]);

        DB::table('assets_logistics1')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'asset_name'         => $request->asset_name,
                'serial_number'      => $request->serial_number,
                'category'           => $request->category,
                'location'           => $request->location,
                'status'             => $request->status,
                'condition_status'   => $request->condition_status,
                'purchase_date'      => $request->purchase_date,
                'purchase_cost'      => $request->purchase_cost      ?? 0.00,
                'supplier'           => $request->supplier,
                'warranty_expiry'    => $request->warranty_expiry,
                'last_maintained_at' => $request->last_maintained_at,
                'notes'              => $request->notes,
                'updated_at'         => now(),
            ]);

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