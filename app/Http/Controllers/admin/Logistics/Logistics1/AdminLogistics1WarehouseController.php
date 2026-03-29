<?php
namespace App\Http\Controllers\admin\Logistics\Logistics1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminLogistics1WarehouseController extends Controller
{
    /**
     * Ensure user is Logistics1 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics1') {
            abort(403, 'Unauthorized access to Logistics1 Warehouse.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeLogisticsAdmin();

        $query = DB::table('drug_inventory_core2');

        // Search logic
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('drug_name', 'like', '%' . $request->search . '%')
                  ->orWhere('drug_num', 'like', '%' . $request->search . '%');
            });
        }

        // Fetching with pagination
        $inventory = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin._logistics1.warehouse.index', compact('inventory'));
    }
}