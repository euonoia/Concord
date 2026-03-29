<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminVendorPortalController extends Controller
{
    /**
     * Ensure user is Logistics2 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics2') {
            abort(403, 'Unauthorized access to Logistics2 Vendor Portal.');
        }
    }

    /**
     * Vendor List
     */
    public function index()
    {
        $this->authorizeLogisticsAdmin();

        $vendors = DB::table('vendor_portal_logistics2')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin._logistics2.vendor_portal.index', compact('vendors'));
    }

    /**
     * Generate Vendor Code Automatically
     */
    private function generateVendorCode()
    {
        $lastVendor = DB::table('vendor_portal_logistics2')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastVendor) {
            return 'VEN-0001';
        }

        $lastNumber = intval(substr($lastVendor->vendor_code, -4));
        $nextNumber = $lastNumber + 1;

        return 'VEN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show Create Vendor Form
     */
    public function create()
    {
        $this->authorizeLogisticsAdmin();

        $vendorCode = $this->generateVendorCode();

        return view('admin._logistics2.vendor_portal.create', compact('vendorCode'));
    }

    /**
     * Store Vendor
     */
    public function store(Request $request)
    {
        $this->authorizeLogisticsAdmin();

        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
        ]);

        $vendorCode = $this->generateVendorCode();

        DB::table('vendor_portal_logistics2')->insert([
            'vendor_code'           => $vendorCode,
            'vendor_name'           => $request->vendor_name,
            'contact_person'        => $request->contact_person,
            'email'                 => $request->email,
            'phone'                 => $request->phone,
            'address'               => $request->address,
            'category'              => $request->category,
            'tax_id'                => $request->tax_id,
            'business_permit'       => $request->business_permit,
            'status'                => 'active',
            'accreditation_date'    => $request->accreditation_date,
            'accreditation_expiry'  => $request->accreditation_expiry,
            'notes'                 => $request->notes,
            'created_by'            => Auth::id(),
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        return redirect()->route('admin.logistics2.vendor.portal.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Edit Vendor
     */
    public function edit($id)
    {
        $this->authorizeLogisticsAdmin();

        $vendor = DB::table('vendor_portal_logistics2')->where('id', $id)->first();

        return view('admin._logistics2.vendor_portal.edit', compact('vendor'));
    }

    /**
     * Update Vendor
     */
    public function update(Request $request, $id)
    {
        $this->authorizeLogisticsAdmin();

        DB::table('vendor_portal_logistics2')
            ->where('id', $id)
            ->update([
                'vendor_name'          => $request->vendor_name,
                'contact_person'       => $request->contact_person,
                'email'                => $request->email,
                'phone'                => $request->phone,
                'address'              => $request->address,
                'category'             => $request->category,
                'tax_id'               => $request->tax_id,
                'business_permit'      => $request->business_permit,
                'accreditation_date'   => $request->accreditation_date,
                'accreditation_expiry' => $request->accreditation_expiry,
                'notes'                => $request->notes,
                'updated_at'           => now(),
            ]);

        return redirect()->route('admin.logistics2.vendor.portal.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Soft Delete Vendor
     */
    public function destroy($id)
    {
        $this->authorizeLogisticsAdmin();

        DB::table('vendor_portal_logistics2')
            ->where('id', $id)
            ->update(['deleted_at' => now()]);

        return redirect()->back()->with('success', 'Vendor removed.');
    }
}