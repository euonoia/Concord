@extends('layouts.core2.app')
@section('title', 'Test Orders')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">LABORATORY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Test Ordering & Registration</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Incoming lab orders from Core 1 — collect samples to advance</p>
    </div>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl mb-6 text-sm font-bold">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl mb-6 text-sm font-bold">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-6 py-5">Order ID</th>
                    <th class="px-6 py-5">Patient</th>
                    <th class="px-6 py-5">Test Name</th>
                    <th class="px-6 py-5">Clinical Indication</th>
                    <th class="px-6 py-5">Doctor</th>
                    <th class="px-6 py-5">Priority</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                @php
                    $priorityColors = [
                        'STAT'    => ['bg' => 'bg-red-50',   'text' => 'text-red-700',    'border' => 'border-red-200'],
                        'Urgent'  => ['bg' => 'bg-orange-50','text' => 'text-orange-700', 'border' => 'border-orange-200'],
                        'Routine' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500',  'border' => 'border-slate-200'],
                    ];
                    $pc = $priorityColors[$r->priority ?? 'Routine'] ?? $priorityColors['Routine'];
                @endphp
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-xs font-black text-slate-900">{{ $r->order_id }}</td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-bold text-slate-900">{{ $r->patient_name ?? '—' }}</div>
                        @if($r->patient_mrn)
                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">{{ $r->patient_mrn }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-slate-800">{{ $r->test_name ?? $r->test_id ?? '—' }}</td>
                    <td class="px-6 py-4 text-[11px] text-slate-500 max-w-[200px] truncate">{{ $r->clinical_note ?? '—' }}</td>
                    <td class="px-6 py-4 text-xs font-semibold text-slate-600">{{ $r->ordering_doctor ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $pc['bg'] }} {{ $pc['text'] }} {{ $pc['border'] }} border">
                            {{ $r->priority ?? 'Routine' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-50 text-blue-700 border-blue-200 border">
                            Received
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('core2.laboratory.test-orders.collect-sample', $r->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                Collect Sample
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-20 text-center text-slate-300 font-bold italic">No incoming test orders.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif
@endsection
