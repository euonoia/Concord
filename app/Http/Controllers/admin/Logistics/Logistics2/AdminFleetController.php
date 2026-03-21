<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFleetController extends Controller
{
    public function index()
    {
        $fleet = DB::table('fleet_management_logistics2')->get();
        return view('admin._logistics2.fleet.index', compact('fleet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|unique:fleet_management_logistics2,plate_number',
            'vehicle_type' => 'required',
            'model_name'   => 'nullable|string'
        ]);

        DB::table('fleet_management_logistics2')->insert([
            'plate_number' => strtoupper($request->plate_number),
            'vehicle_type' => $request->vehicle_type,
            'model_name'   => $request->model_name,
            'status'       => 'available',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'New vehicle added to fleet.');
    }

    public function updateStatus(Request $request, $id)
    {
        // Allows manual toggle to 'maintenance' or 'available'
        DB::table('fleet_management_logistics2')->where('id', $id)->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Vehicle status updated.');
    }
}