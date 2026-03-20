@extends('layouts.core2.app')
@section('title', 'Patient Package Enrollment')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">MEDICAL PACKAGES › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Patient Package Enrollment</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage active patient package subscriptions</p>
    </div>
    <a href="{{ route('core2.medical-packages.enrollment.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Enrollment
    </a>
</div>

<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Patient ID</th>
                    <th class="px-8 py-6">Package Node</th>
                    <th class="px-8 py-6">Total Price</th>
                    <th class="px-8 py-6">Payment Status</th>
                    <th class="px-8 py-6">Progress</th>
                    <th class="px-8 py-6">Enrolled At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    {{-- Patient ID --}}
                    <td class="px-8 py-5 text-xs font-black text-slate-900">
                        <span class="bg-slate-100 px-3 py-1.5 rounded-lg">#{{ $r->patient_id }}</span>
                    </td>
                    
                    {{-- Package Node --}}
                    <td class="px-8 py-5 text-xs font-bold text-indigo-600 uppercase">
                        {{ $r->package_identifier }}
                    </td>
                    
                    {{-- Price --}}
                    <td class="px-8 py-5 text-xs font-black text-slate-900">
                        ₱{{ number_format($r->total_price, 2) }}
                    </td>
                    
                    {{-- Payment Badge --}}
                    <td class="px-8 py-5">
                        <span class="text-[9px] font-black px-3 py-1 rounded-full uppercase {{ $r->payment_status == 'Paid' ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-white' }}">
                            {{ $r->payment_status }}
                        </span>
                    </td>

                    {{-- Progress --}}
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-full" style="width: {{ $r->progress_percent }}%"></div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400">{{ $r->progress_percent }}%</span>
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">
                        {{ $r->enrolled_at ? $r->enrolled_at->format('M d, Y') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-20 text-center text-slate-300 font-bold italic">
                        No enrollment records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($records->hasPages())
    <div class="mt-6">{{ $records->links() }}</div>
@endif
@endsection