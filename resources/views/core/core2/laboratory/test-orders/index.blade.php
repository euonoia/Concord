@extends('layouts.core2.app')
@section('title', 'Test Orders')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">LABORATORY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Test Ordering & Registration</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage laboratory test orders synced from Core 1</p>
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
                    $statusColors = [
                        'Received'        => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'border' => 'border-blue-200'],
                        'SampleCollected' => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-200'],
                        'Processing'      => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'border' => 'border-orange-200'],
                        'ResultReady'     => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                        'Validated'       => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200'],
                        'Sent'            => ['bg' => 'bg-slate-50',   'text' => 'text-slate-500',   'border' => 'border-slate-200'],
                    ];
                    $sc = $statusColors[$r->status] ?? $statusColors['Received'];

                    $priorityColors = [
                        'STAT'    => ['bg' => 'bg-red-50',   'text' => 'text-red-700',    'border' => 'border-red-200'],
                        'Urgent'  => ['bg' => 'bg-orange-50','text' => 'text-orange-700', 'border' => 'border-orange-200'],
                        'Routine' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500',  'border' => 'border-slate-200'],
                    ];
                    $pc = $priorityColors[$r->priority ?? 'Routine'] ?? $priorityColors['Routine'];

                    $statusLabel = match($r->status) {
                        'SampleCollected' => 'Sample Collected',
                        'ResultReady'     => 'Result Ready',
                        default           => $r->status ?? 'Received',
                    };
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
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }} border">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- Workflow action buttons based on current status --}}
                            @if($r->status === 'Received')
                                <form action="{{ route('core2.laboratory.test-orders.update-status', $r->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="SampleCollected">
                                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                        Collect Sample
                                    </button>
                                </form>
                            @endif

                            @if($r->status === 'SampleCollected')
                                <form action="{{ route('core2.laboratory.test-orders.update-status', $r->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Processing">
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                        Processing
                                    </button>
                                </form>
                            @endif

                            @if($r->status === 'Processing')
                                <button type="button" onclick="openResultEntryModal({{ $r->id }})" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                    Enter Result
                                </button>
                            @endif

                            @if($r->status === 'ResultReady')
                                <form action="{{ route('core2.laboratory.test-orders.validate-send', $r->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                        Validate & Send
                                    </button>
                                </form>
                            @endif

                            @if($r->status === 'Sent')
                                <span class="text-[10px] font-bold text-slate-400 italic">Completed</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-20 text-center text-slate-300 font-bold italic">No test order records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif

{{-- Result Entry Modal --}}
<div id="resultEntryModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; width:500px; max-width:90%; padding:30px; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <h3 class="text-lg font-black text-slate-900 mb-4">Enter Lab Result</h3>
        <form id="resultEntryForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Result Data (JSON or Text)</label>
                <textarea name="result_data" rows="6" required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                    placeholder='e.g. {"wbc": "7.5", "rbc": "4.8", "hemoglobin": "14.2"}'></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeResultEntryModal()" class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">Save Result</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResultEntryModal(orderId) {
    const form = document.getElementById('resultEntryForm');
    form.action = `/core2/laboratory/test-orders/${orderId}/result`;
    document.getElementById('resultEntryModal').style.display = 'flex';
}

function closeResultEntryModal() {
    document.getElementById('resultEntryModal').style.display = 'none';
}
</script>
@endsection
