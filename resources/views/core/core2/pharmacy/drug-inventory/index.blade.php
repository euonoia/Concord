@extends('layouts.core2.app')
@section('title', 'Drug Inventory')
@section('content')

{{-- 1. Header Section: Flex row with Title and Buttons --}}
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">PHARMACY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Drug Inventory</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage pharmaceutical stock records</p>
    </div>

    <div class="flex gap-3">
        

        <a href="{{ route('core2.pharmacy.drug-inventory.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Request Form
        </a>
    </div> {{-- Correctly closed the button container --}}
</div> {{-- Correctly closed the header container --}}

{{-- 2. Table Section: Starts on a new line because the div above is closed --}}
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Drug Num</th>
                    <th class="px-8 py-6">Drug Name</th>
                    <th class="px-8 py-6">Quantity</th>
                    <th class="px-8 py-6">Expiry Date</th>
                    
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900">{{ $r->drug_num }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->drug_name }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->quantity }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->expiry_date ?? '—' }}</td>
                    
                </tr>
                @empty
                <tr>
                    {{-- Updated Colspan to 7 to match your header count --}}
                    <td colspan="7" class="py-20 text-center text-slate-300 font-bold italic">No drug inventory records found.</td>
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