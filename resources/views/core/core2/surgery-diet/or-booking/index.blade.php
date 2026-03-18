@extends('layouts.core2.app')
@section('title', 'Operating Room Booking')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">SURGERY & DIET › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Operating Room Booking</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage surgical procedure bookings</p>
    </div>
    <a href="{{ route('core2.surgery-diet.or-booking.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Patient</th>
                    <th class="px-8 py-6">Procedure / Order</th>
                    <th class="px-8 py-6 text-center">Schedule</th>
                    <th class="px-8 py-6 text-center">Priority</th>
                    <th class="px-8 py-6">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900 border-l-4 border-rose-500">
                        <div class="flex flex-col gap-1">
                            <span class="text-rose-600 font-black">{{ $r->surgeryOrder->patient->name ?? $r->patient->name ?? 'Unknown' }}</span>
                            <span class="text-[9px] text-slate-400 uppercase">MRN: {{ $r->surgeryOrder->patient->mrn ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-xs font-bold text-slate-700">
                        <div class="flex flex-col gap-1">
                            <span class="text-slate-900">{{ $r->surgeryOrder->procedure_name ?? 'Pending Session Details' }}</span>
                            <span class="text-[9px] text-slate-400 italic">Ref: {{ $r->operating_booking_id }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <div class="flex flex-col gap-1 items-center">
                            <span class="text-slate-700 font-bold px-3 py-1 bg-slate-50 rounded-lg">{{ $r->proposed_date ? \Carbon\Carbon::parse($r->proposed_date)->format('M d, Y') : 'TBD' }}</span>
                            <span class="text-[10px] text-slate-400 font-black tracking-widest uppercase">{{ $r->proposed_time ? \Carbon\Carbon::parse($r->proposed_time)->format('h:i A') : '--:--' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php
                            $priority = $r->surgeryOrder->priority ?? 'Routine';
                            $pColor = match($priority) {
                                'STAT' => 'bg-rose-100 text-rose-700',
                                'Urgent' => 'bg-amber-100 text-amber-700',
                                default => 'bg-slate-100 text-slate-600'
                            };
                        @endphp
                        <span class="px-3 py-1 {{ $pColor }} rounded-lg font-black text-[9px] uppercase tracking-wider">{{ $priority }}</span>
                    </td>
                    <td class="px-8 py-5 text-xs">
                        <span class="px-3 py-1 {{ $r->status === 'Completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }} rounded-full font-black text-[10px] uppercase tracking-tighter">
                            {{ $r->status ?? 'Received' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-20 text-center text-slate-300 font-bold italic">No OR booking records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif
@endsection
