<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDocumentTrackingLabOrdersController extends Controller
{
    /**
     * Ensure user is Logistics2 admin
     */
    private function authorizeLogisticsAdmin()
    {
        if (!Auth::check() || Auth::user()->role_slug !== 'admin_logistics2') {
            abort(403, 'Unauthorized access to Logistics2 Document Tracking.');
        }
    }

    /**
     * LAB ORDERS (with patient name)
     */
    public function index()
    {
        $this->authorizeLogisticsAdmin();

        $labOrders = DB::table('lab_orders_core1 as lab')
            ->leftJoin('patients_core1 as p', 'lab.patient_id', '=', 'p.id')
            ->select(
                'lab.*',
                'p.first_name',
                'p.last_name'
            )
            ->orderBy('lab.created_at', 'desc')
            ->get();

        return view('admin._logistics2.document.index', compact('labOrders'));
    }

    /**
     * VIEW LAB RESULT (safe JSON decode)
     */
    public function viewResult($id)
    {
        $this->authorizeLogisticsAdmin();

        $order = DB::table('lab_orders_core1 as lab')
            ->leftJoin('patients_core1 as p', 'lab.patient_id', '=', 'p.id')
            ->select('lab.*', 'p.first_name', 'p.last_name')
            ->where('lab.id', $id)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Lab order not found.');
        }

        $result = [];

        if (!empty($order->result_data)) {
            // First decode
            $decoded = json_decode($order->result_data, true);

            // If still string → decode again (double encoded JSON)
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            if (is_array($decoded)) {
                $result = $decoded;
            }
        }

        return view('admin._logistics2.document.result', compact('order', 'result'));
    }

    /**
     * DIET ORDERS (with patient name)
     */
    public function dietIndex()
    {
        $this->authorizeLogisticsAdmin();

        $dietOrders = DB::table('diet_orders_core1 as diet')
            ->leftJoin('patients_core1 as p', 'diet.patient_id', '=', 'p.id')
            ->select(
                'diet.*',
                'p.first_name',
                'p.last_name'
            )
            ->orderBy('diet.created_at', 'desc')
            ->get();

        return view('admin._logistics2.document.diet', compact('dietOrders'));
    }

    /**
     * SURGERY ORDERS (with patient name)
     */
    public function surgeryIndex()
    {
        $this->authorizeLogisticsAdmin();

        $surgeryOrders = DB::table('surgery_orders_core1 as s')
            ->leftJoin('patients_core1 as p', 's.patient_id', '=', 'p.id')
            ->select(
                's.*',
                'p.first_name',
                'p.last_name'
            )
            ->orderBy('s.proposed_date', 'desc')
            ->orderBy('s.proposed_time', 'desc')
            ->get();

        return view('admin._logistics2.document.surgery', compact('surgeryOrders'));
    }
}