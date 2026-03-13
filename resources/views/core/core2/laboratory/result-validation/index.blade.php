@extends('layouts.core2.app')
@section('title', 'Result Entry & Validation')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">LABORATORY › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Result Entry & Validation</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Enter results, validate, and send to Core 1 Diagnostic Orders</p>
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
                    <th class="px-6 py-5">Priority</th>
                    <th class="px-6 py-5">Status</th>
                    <th class="px-6 py-5">Result</th>
                    <th class="px-6 py-5">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                @php
                    $statusColors = [
                        'Processing'  => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'border' => 'border-orange-200'],
                        'ResultReady' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
                        'Validated'   => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'border' => 'border-indigo-200'],
                        'Sent'        => ['bg' => 'bg-slate-50',   'text' => 'text-slate-500',   'border' => 'border-slate-200'],
                    ];
                    $sc = $statusColors[$r->status] ?? $statusColors['Processing'];

                    $statusLabel = match($r->status) {
                        'ResultReady' => 'Result Ready',
                        default       => $r->status ?? 'Processing',
                    };

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
                        @if($r->result_data)
                            <button type="button" onclick="viewResultData({{ $r->id }}, '{{ addslashes($r->test_name) }}', `{{ addslashes($r->result_data) }}`)"
                                class="text-indigo-600 hover:text-indigo-800 text-[10px] font-black uppercase underline underline-offset-2 transition">
                                View Data
                            </button>
                        @else
                            <span class="text-slate-300 text-[10px] italic">No result yet</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($r->status === 'Processing')
                                <button type="button" onclick="openResultEntryModal({{ $r->id }}, '{{ addslashes($r->test_name) }}')"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                    Enter Result
                                </button>
                            @endif

                            @if($r->status === 'ResultReady')
                                <form action="{{ route('core2.laboratory.result-validation.validate-send', $r->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-xl text-[10px] font-black uppercase transition shadow-sm">
                                        Validate & Send
                                    </button>
                                </form>
                            @endif

                            @if($r->status === 'Validated')
                                <span class="text-[10px] font-bold text-indigo-500 italic">Validated by {{ $r->validated_by_name ?? '—' }}</span>
                            @endif

                            @if($r->status === 'Sent')
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-[10px] font-bold text-slate-400 italic">Sent to Core 1</span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-20 text-center text-slate-300 font-bold italic">No orders awaiting results or validation.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mt-6">{{ $records->links() }}</div>@endif

{{-- Result Entry Modal --}}
<div id="resultEntryModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; width:500px; max-width:90%; padding:30px; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <h3 class="text-lg font-black text-slate-900 mb-1">Enter Lab Result</h3>
        <p id="resultEntryTestLabel" class="text-xs font-bold text-indigo-600 mb-4"></p>
        <form id="resultEntryForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Result Data (JSON)</label>
                <textarea name="result_data" id="resultDataInput" rows="8" required
                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                    placeholder='Loading example...'></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeResultEntryModal()" class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">Save Result</button>
            </div>
        </form>
    </div>
</div>

{{-- View Result Modal --}}
<div id="viewResultModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; width:550px; max-width:90%; padding:30px; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <h3 class="text-lg font-black text-slate-900 mb-1">Lab Result Data</h3>
        <p id="viewResultTestLabel" class="text-xs font-bold text-indigo-600 mb-4"></p>
        <pre id="viewResultData" class="bg-slate-50 rounded-xl p-4 text-sm font-mono text-slate-800 overflow-auto max-h-[400px] border border-slate-200"></pre>
        <div class="flex justify-end mt-4">
            <button type="button" onclick="closeViewResultModal()" class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Close</button>
        </div>
    </div>
</div>

<script>
const resultExamples = {
    'Complete Blood Count (CBC)': JSON.stringify({
        "wbc": "7.5 x10^3/uL",
        "rbc": "4.8 x10^6/uL",
        "hemoglobin": "14.2 g/dL",
        "hematocrit": "42.1 %",
        "platelets": "250 x10^3/uL",
        "mcv": "87.7 fL",
        "mch": "29.6 pg",
        "mchc": "33.7 g/dL",
        "neutrophils": "60 %",
        "lymphocytes": "30 %",
        "monocytes": "6 %",
        "eosinophils": "3 %",
        "basophils": "1 %"
    }, null, 2),

    'Urinalysis': JSON.stringify({
        "color": "Yellow",
        "clarity": "Clear",
        "specific_gravity": "1.020",
        "ph": "6.0",
        "protein": "Negative",
        "glucose": "Negative",
        "ketones": "Negative",
        "blood": "Negative",
        "leukocyte_esterase": "Negative",
        "nitrite": "Negative",
        "bilirubin": "Negative",
        "urobilinogen": "Normal",
        "wbc_micro": "0-2 /HPF",
        "rbc_micro": "0-1 /HPF",
        "bacteria": "None seen"
    }, null, 2),

    'Blood Chemistry Panel': JSON.stringify({
        "glucose_fasting": "95 mg/dL",
        "bun": "15 mg/dL",
        "creatinine": "1.0 mg/dL",
        "uric_acid": "5.5 mg/dL",
        "sgot_ast": "25 U/L",
        "sgpt_alt": "22 U/L",
        "alkaline_phosphatase": "70 U/L",
        "total_bilirubin": "0.8 mg/dL",
        "direct_bilirubin": "0.2 mg/dL",
        "total_protein": "7.2 g/dL",
        "albumin": "4.5 g/dL",
        "sodium": "140 mEq/L",
        "potassium": "4.2 mEq/L",
        "calcium": "9.5 mg/dL"
    }, null, 2),

    'Lipid Panel': JSON.stringify({
        "total_cholesterol": "195 mg/dL",
        "hdl_cholesterol": "55 mg/dL",
        "ldl_cholesterol": "120 mg/dL",
        "vldl_cholesterol": "20 mg/dL",
        "triglycerides": "100 mg/dL",
        "cholesterol_hdl_ratio": "3.5",
        "risk_category": "Desirable"
    }, null, 2),

    'Microbiology/Molecular Tests': JSON.stringify({
        "specimen_type": "Blood",
        "gram_stain": "No organisms seen",
        "culture_result": "No growth after 48 hours",
        "organism_identified": "None",
        "antibiotic_sensitivity": "N/A",
        "pcr_result": "Negative",
        "molecular_target": "COVID-19 SARS-CoV-2",
        "ct_value": "N/A",
        "interpretation": "Negative for target organism"
    }, null, 2)
};

function openResultEntryModal(orderId, testName) {
    const form = document.getElementById('resultEntryForm');
    form.action = `/core2/laboratory/result-validation/${orderId}/result`;

    document.getElementById('resultEntryTestLabel').innerText = testName || 'Lab Test';

    const textarea = document.getElementById('resultDataInput');
    textarea.value = resultExamples[testName] || JSON.stringify({"result": "Enter result here"}, null, 2);

    document.getElementById('resultEntryModal').style.display = 'flex';
}

function closeResultEntryModal() {
    document.getElementById('resultEntryModal').style.display = 'none';
}

function viewResultData(orderId, testName, rawData) {
    document.getElementById('viewResultTestLabel').innerText = testName || 'Lab Test';

    try {
        const parsed = JSON.parse(rawData);
        document.getElementById('viewResultData').textContent = JSON.stringify(parsed, null, 2);
    } catch (e) {
        document.getElementById('viewResultData').textContent = rawData;
    }

    document.getElementById('viewResultModal').style.display = 'flex';
}

function closeViewResultModal() {
    document.getElementById('viewResultModal').style.display = 'none';
}
</script>
@endsection
