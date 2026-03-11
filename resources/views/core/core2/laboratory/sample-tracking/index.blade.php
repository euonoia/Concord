@extends('layouts.core2.app')
@section('title', 'Sample Tracking')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">LABORATORY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Sample Tracking & LIS Integration</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Track laboratory samples and LIS sync</p>
    </div>
    <a href="{{ route('core2.laboratory.sample-tracking.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Sample ID</th>
                    <th class="px-8 py-6">Test Order ID</th>
                    <th class="px-8 py-6">Status</th>
                    <th class="px-8 py-6">Lab ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900">{{ $r->sample_id }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ $r->test_order_id ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold"><span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full">{{ $r->status ?? '—' }}</span></td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->lab_id ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-20 text-center text-slate-300 font-bold italic">No sample tracking records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif
@endsection
