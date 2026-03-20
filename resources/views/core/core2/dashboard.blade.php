@extends('layouts.core2.app')

@section('title', 'Dashboard')

@section('content')
<header class="mb-10">
    <h2 class="text-4xl font-black text-slate-900 tracking-tight">Clinical Dashboard</h2>
    <p class="text-slate-500 font-semibold mt-1">Core 2 — Cross-Departmental Performance Tracking</p>
</header>

{{-- Summary Card --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-between h-40">
        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
        <div>
            <h3 class="text-3xl font-black">{{ $totalRecords }}</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Total System Records</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-between h-40">
        <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
        <div>
            <h3 class="text-3xl font-black">{{ array_sum($totals['pharmacy']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Pharmacy Records</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-between h-40">
        <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <div>
            <h3 class="text-3xl font-black">{{ array_sum($totals['laboratory']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Laboratory Records</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-between h-40">
        <svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <div>
            <h3 class="text-3xl font-black">{{ array_sum($totals['bed_linen']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Bed & Linen Records</p>
        </div>
    </div>
</div>

{{-- Department Breakdown --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    @foreach([
        ['label' => 'Pharmacy', 'color' => 'violet', 'route' => 'core2.pharmacy.drug-inventory.index', 'items' => [
            ['name' => 'Drug Inventory', 'count' => $totals['pharmacy']['drug_inventory'], 'route' => 'core2.pharmacy.drug-inventory.index'],
            ['name' => 'Formula Management', 'count' => $totals['pharmacy']['formula_management'], 'route' => 'core2.pharmacy.formula-management.index'],
            ['name' => 'Prescriptions', 'count' => $totals['pharmacy']['prescriptions'], 'route' => 'core2.pharmacy.prescription.index'],
        ]],
        ['label' => 'Medical Packages', 'color' => 'blue', 'route' => 'core2.medical-packages.packages.index', 'items' => [
            ['name' => 'Package Definition & Pricing', 'count' => $totals['medical_packages']['packages'], 'route' => 'core2.medical-packages.packages.index'],
            ['name' => 'Patient Package Enrollment', 'count' => $totals['medical_packages']['enrollment'], 'route' => 'core2.medical-packages.enrollment.index'],
        ]],
        ['label' => 'Laboratory', 'color' => 'emerald', 'route' => 'core2.laboratory.test-orders.index', 'items' => [
            ['name' => 'Test Orders', 'count' => $totals['laboratory']['test_orders'], 'route' => 'core2.laboratory.test-orders.index'],
            ['name' => 'Sample Tracking & LIS', 'count' => $totals['laboratory']['sample_tracking'], 'route' => 'core2.laboratory.sample-tracking.index'],
            ['name' => 'Result Validation', 'count' => $totals['laboratory']['result_validation'], 'route' => 'core2.laboratory.result-validation.index'],
        ]],
        ['label' => 'Surgery & Diet', 'color' => 'red', 'route' => 'core2.surgery-diet.or-booking.index', 'items' => [
            ['name' => 'OR Booking', 'count' => $totals['surgery_diet']['or_booking'], 'route' => 'core2.surgery-diet.or-booking.index'],
            ['name' => 'Nutritional Assessment', 'count' => $totals['surgery_diet']['nutritional'], 'route' => 'core2.surgery-diet.nutritional.index'],
            ['name' => 'Utilization Reporting', 'count' => $totals['surgery_diet']['utilization'], 'route' => 'core2.surgery-diet.utilization.index'],
        ]],
        ['label' => 'Bed & Linen', 'color' => 'orange', 'route' => 'core2.bed-linen.room-assignment.index', 'items' => [
            ['name' => 'Room Assignment', 'count' => $totals['bed_linen']['room_assignment'], 'route' => 'core2.bed-linen.room-assignment.index'],
            ['name' => 'Bed Status & Allocation', 'count' => $totals['bed_linen']['bed_status'], 'route' => 'core2.bed-linen.bed-status.index'],
            ['name' => 'Patient Transfer', 'count' => $totals['bed_linen']['patient_transfer'], 'route' => 'core2.bed-linen.patient-transfer.index'],
            ['name' => 'Housekeeping Status', 'count' => $totals['bed_linen']['house_keeping'], 'route' => 'core2.bed-linen.house-keeping.index'],
        ]],
    ] as $dept)
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-5">{{ $dept['label'] }}</h3>
        <div class="space-y-3">
            @foreach($dept['items'] as $item)
            <a href="{{ route($item['route']) }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition group">
                <span class="text-sm font-semibold text-slate-600 group-hover:text-indigo-600 transition">{{ $item['name'] }}</span>
                <span class="text-xs font-black bg-slate-100 text-slate-500 px-3 py-1 rounded-full">{{ $item['count'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach

</div>
@endsection
