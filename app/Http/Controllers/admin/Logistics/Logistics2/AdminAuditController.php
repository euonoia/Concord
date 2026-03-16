<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAuditController extends Controller
{
    public function index()
    {
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