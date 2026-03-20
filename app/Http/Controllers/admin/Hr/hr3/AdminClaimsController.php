<?php

namespace App\Http\Controllers\admin\Hr\hr3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user\Hr\hr3\ClaimsHr3;
use App\Models\Employee;

class AdminClaimsController extends Controller
{
    // Only HR3 admins
    private function authorizeHrAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role_slug, ['admin_hr3'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    // List all claims with employee and validator info
    public function index()
    {
        $this->authorizeHrAdmin();

        $claims = ClaimsHr3::with(['employee', 'validator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.hr3.claims.index', compact('claims'));
    }

    // Approve claim
    public function approve($id)
    {
        $this->authorizeHrAdmin();

        $claim = ClaimsHr3::find($id);
        if (!$claim) return redirect()->back()->with('error', 'Claim not found.');

        $employee = Employee::where('user_id', Auth::id())->first();
        $claim->status = 'approved';
        $claim->validated_by = $employee ? $employee->employee_id : null;
        $claim->save();

        return redirect()->back()->with('success', "Claim {$claim->id} approved successfully.");
    }

    // Reject claim
    public function reject($id)
    {
        $this->authorizeHrAdmin();

        $claim = ClaimsHr3::find($id);
        if (!$claim) return redirect()->back()->with('error', 'Claim not found.');

        $employee = Employee::where('user_id', Auth::id())->first();
        $claim->status = 'rejected';
        $claim->validated_by = $employee ? $employee->employee_id : null;
        $claim->save();

        return redirect()->back()->with('success', "Claim {$claim->id} rejected successfully.");
    }
}