<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminAuditController extends Controller
{
    /**
     * Ensure user is Logistics2 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics2') {
            abort(403, 'Unauthorized access to Logistics2 Audit.');
        }
    }

    /**
     * Display audit logs dashboard
     */
    public function index()
    {
        $this->authorizeLogisticsAdmin();

        // Fetch all logs from the shared table
        $logs = DB::table('audit_logistics2')
            ->leftJoin('fleet_management_logistics2', 'audit_logistics2.reference_id', '=', 'fleet_management_logistics2.id')
            ->select('audit_logistics2.*', 'fleet_management_logistics2.plate_number')
            ->orderBy('audit_logistics2.created_at', 'desc')
            ->get();

        // Calculate some stats for the top cards
        $totalExpenses = DB::table('audit_logistics2')->sum('cost');
        $repairCount = DB::table('audit_logistics2')->where('category', 'Maintenance')->count();

        return view('admin._logistics2.audit.index', compact('logs', 'totalExpenses', 'repairCount'));
    }
}