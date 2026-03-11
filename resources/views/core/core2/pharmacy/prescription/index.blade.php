@extends('layouts.core2.app')
@section('title', 'Prescriptions')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">PHARMACY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Prescriptions</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage patient prescription records</p>
    </div>
    <a href="{{ route('core2.pharmacy.prescription.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Prescription ID</th>
                    <th class="px-8 py-6">Patient ID</th>
                    <th class="px-8 py-6">Doctor ID</th>
                    <th class="px-8 py-6">Date</th>
                    <th class="px-8 py-6">Drug ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900">{{ $r->prescription_id }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->patient_id ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->doctor_id ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->date ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->drug_id ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-20 text-center text-slate-300 font-bold italic">No prescription records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())
    <div class="mt-6">{{ $records->links() }}</div>
@endif
@endsection
