<?php

namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLogistics1WarehouseController extends Controller
{
    public function index(Request $request)
    {
        // Query the specific table you mentioned
        $query = DB::table('drug_inventory_core2');

        // Optional: Simple search by drug name
        if ($request->filled('search')) {
            $query->where('drug_name', 'like', '%' . $request->search . '%');
        }

        $inventory = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin._logistics1.warehouse.index', compact('inventory'));
    }
}