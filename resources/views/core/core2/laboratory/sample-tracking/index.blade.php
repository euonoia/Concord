@extends('layouts.core2.app')
@section('title', 'Sample Tracking')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">LABORATORY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Sample Tracking & LIS Integration</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Track collected samples — start processing when ready</p>
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
                    <th class="px-6 py-5">Barcode</th>
                    <th class="px-6 py-5">Collected</th>
                    <th class="px-6 py-5">Priority</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                @php
                    $statusColors = [
                        'SampleCollected' => ['bg' => 'bg-amber-50',  'text' => 'text-amber-700',  'border' => 'border-amber-200', 'label' => 'Sample Collected'],
                        'Processing'      => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200','label' => 'Processing'],
                    ];
                    $sc = $statusColors[$r->status] ?? $statusColors['SampleCollected'];

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
                    <td class="px-6 py-4 text-xs font-bold text-slate-800">{{ $r->test_name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @if($r->sample_barcode)
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] font-mono font-bold text-slate-700 tracking-wide">
                                {{ $r->sample_barcode }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($r->sample_collected_at)
                            <div class="text-[11px] font-semibold text-slate-700">{{ $r->sample_collected_at->format('M d, Y H:i') }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">by {{ $r->sample_collected_by ?? '—' }}</div>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $pc['bg'] }} {{ $pc['text'] }} {{ $pc['border'] }} border">
                            {{ $r->priority ?? 'Routine' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }} border">
                            {{ $sc['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($r->status === 'SampleCollected')
                            <form action="{{ route('core2.laboratory.sample-tracking.start-processing', $r->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                    Start Processing
                                </button>
                            </form>
                        @elseif($r->status === 'Processing')
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-orange-600 italic">In Progress</span>
                                @if($r->processing_started_at)
                                    <span class="text-[9px] text-slate-400 ml-1">since {{ $r->processing_started_at->format('H:i') }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-20 text-center text-slate-300 font-bold italic">No samples in tracking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif
@endsection
