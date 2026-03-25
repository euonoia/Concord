<?php

namespace App\Http\Controllers\user\Core\core2;

use App\Http\Controllers\Controller;
use App\Models\core2\DrugInventory;
use App\Models\core2\FormulaManagement;
use App\Models\core2\Prescription;
use App\Models\core2\PackageDefinitionPricing;
use App\Models\core2\PatientEnrollment;
use App\Models\core2\TestOrder;
use App\Models\core2\SampleTracking;
use App\Models\core2\ResultValidation;
use App\Models\core2\OperatingRoomBooking;
use App\Models\core2\NutritionalAssessment;
use App\Models\core2\UtilizationReporting;
use App\Models\core2\RoomAssignment;
use App\Models\core2\BedStatusAllocation;
use App\Models\core2\PatientTransferManagement;
use App\Models\core2\HouseKeepingStatus;

class Core2DashboardController extends Controller
{
    public function index()
    {
        $totals = [
            'pharmacy' => [
                'drug_inventory'    => DrugInventory::count(),
                'formula_management'=> FormulaManagement::count(),
                'prescriptions'     => Prescription::count(),
            ],
            'medical_packages' => [
                'packages'   => PackageDefinitionPricing::count(),
                'enrollment' => PatientEnrollment::count(),
            ],
            'laboratory' => [
                'test_orders'      => TestOrder::count(),
                'sample_tracking'  => SampleTracking::count(),
                'result_validation'=> ResultValidation::count(),
            ],
            'surgery_diet' => [
                'or_booking'   => OperatingRoomBooking::count(),
                'nutritional'  => NutritionalAssessment::count(),
                'utilization'  => UtilizationReporting::count(),
            ],
            'bed_linen' => [
                'room_assignment'  => RoomAssignment::count(),
                'bed_status'       => BedStatusAllocation::count(),
                'patient_transfer' => PatientTransferManagement::count(),
                'house_keeping'    => HouseKeepingStatus::count(),
            ],
        ];

        $totalRecords = collect($totals)->flatten()->sum();

        return view('core.core2.dashboard', compact('totals', 'totalRecords'));
    }
}
