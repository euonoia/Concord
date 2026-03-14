@extends('layouts.core2.app')
@section('title', 'Drug Inventory')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">PHARMACY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Drug Inventory</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage pharmaceutical stock records</p>
    </div>
    <div class="flex gap-3">
        {{-- New Request Form Button --}}
        {{-- Update the route name below to an existing route or remove the button if not needed --}}
       <a href="{{ route('core2.pharmacy.drug-inventory.request') }}" class="bg-white text-slate-700 border border-slate-200 px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-sm hover:bg-slate-50 transition">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    Request Form
</a>

    <a href="{{ route('core2.pharmacy.drug-inventory.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Drug Num</th>
                    <th class="px-8 py-6">Drug Name</th>
                    <th class="px-8 py-6">Quantity</th>
                    <th class="px-8 py-6">Expiry Date</th>
                    <th class="px-8 py-6">Supplier</th>
                    <th class="px-8 py-6">status</th>
                    <th class="px-8 py-6">Created</th>
                     

                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900">{{ $r->drug_num }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->drug_name }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->quantity }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->expiry_date ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->supplier ?? '—' }}</td>
                    <td class="px-8 py-5">
    @if($r->quantity <= 10)
        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-600 border border-red-100">
            Pending
        </span>
    @else
        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
            Full Stock
        </span>
    @endif
</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->status }}</td>
                    <td class="px-8 py-5 text-xs text-slate-400">{{ $r->created_at->format('M d, Y') }}</td>
                </tr>
               
                @empty
                <tr><td colspan="6" class="py-20 text-center text-slate-300 font-bold italic">No drug inventory records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())
    <div class="mt-6">{{ $records->links() }}</div>
@endif
@endsection
