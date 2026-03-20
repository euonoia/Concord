<?php

namespace App\Http\Controllers\admin\Logistics\Logistics2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDocumentTrackingLabOrdersController extends Controller
{
    /**
     * LAB ORDERS (with patient name)
     */
    public function index()
    {
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