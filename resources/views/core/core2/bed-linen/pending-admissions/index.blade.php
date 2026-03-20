@extends('layouts.core2.app')
@section('title', 'Pending Admissions — Room Assignment')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">BED & LINEN › PENDING ADMISSIONS</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Room Assignment Queue</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Patients recommended for admission awaiting bed allocation</p>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('core2.bed-linen.bed-status.index') }}" 
           class="bg-white border border-indigo-200 text-indigo-600 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-50 transition shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Initiate Transfer
        </a>
        <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-3 rounded-2xl text-xs font-black uppercase">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $records->total() }} Pending
        </span>
    </div>
</div>

{{-- ── Pending Queue Table ────────────────────────────────────────────────── --}}
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm mb-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="pendingTable">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Patient</th>
                    <th class="px-8 py-6">MRN</th>
                    <th class="px-8 py-6">Triage</th>
                    <th class="px-8 py-6">Acuity</th>
                    <th class="px-8 py-6">Queued At</th>
                    <th class="px-8 py-6">Status</th>
                    <th class="px-8 py-6 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors" id="row-{{ $r->id }}">
                    <td class="px-8 py-5">
                        <p class="text-xs font-black text-slate-900">{{ $r->patient_name ?? 'Unknown' }}</p>
                        <p class="text-[10px] text-slate-400">Encounter #{{ $r->encounter_id }}</p>
                    </td>
                    <td class="px-8 py-5 text-xs font-mono font-bold text-indigo-700">{{ $r->mrn ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->triage_summary ?? 'No vitals' }}</td>
                    <td class="px-8 py-5">
                        @php
                            $lvl = $r->triage_level;
                            $colors = [
                                '1' => 'bg-red-100 text-red-700 border-red-200',
                                '2' => 'bg-orange-100 text-orange-700 border-orange-200',
                                '3' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                '4' => 'bg-blue-100 text-blue-700 border-blue-200',
                                '5' => 'bg-green-100 text-green-700 border-green-200',
                            ];
                            $badge = $colors[$lvl] ?? 'bg-slate-100 text-slate-500 border-slate-200';
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black border {{ $badge }}">
                            {{ $lvl ? "Level $lvl" : '—' }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->date_assigned ?? $r->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="px-8 py-5">
                        @if($r->request_type === 'Transfer')
                            <span class="inline-block px-4 py-1.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase">Transfer</span>
                            <div class="text-[10px] text-slate-400 mt-1 font-bold">From Bed #{{ $r->source_bed_id ?? 'N/A' }}</div>
                        @else
                            <span class="inline-block px-4 py-1.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 border border-amber-200 uppercase">Pending</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button onclick="openFloorMap({{ $r->id }}, {{ json_encode($r->patient_name ?? 'Unknown') }}, '{{ $r->mrn ?? '' }}', {{ $r->encounter_id ?? 'null' }}, {{ $r->request_type === 'Transfer' ? 'true' : 'false' }})"
                                class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-wide hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $r->request_type === 'Transfer' ? 'Process Transfer' : 'Assign Bed' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-20 text-center text-slate-300 font-bold italic">No patients pending bed assignment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())<div class="mb-8">{{ $records->links() }}</div>@endif

{{-- ── Recently Assigned ──────────────────────────────────────────────────── --}}
@if($assigned->isNotEmpty())
<div class="mb-8">
    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight mb-4">Recently Assigned</h3>
    <div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="px-8 py-5">Patient</th>
                        <th class="px-8 py-5">MRN</th>
                        <th class="px-8 py-5">Room / Bed</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assigned as $a)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-4 text-xs font-black text-slate-900">{{ $a->patient_name ?? 'Unknown' }}</td>
                        <td class="px-8 py-4 text-xs font-mono font-bold text-indigo-700">{{ $a->mrn ?? '—' }}</td>
                        <td class="px-8 py-4 text-xs font-semibold text-slate-600">{{ $a->room ?? '—' }}</td>
                        <td class="px-8 py-4">
                            <span class="inline-block px-4 py-1.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">Assigned</span>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <button onclick="openFloorMap({{ $a->id }}, {{ json_encode($a->patient_name ?? 'Unknown') }}, '{{ $a->mrn ?? '' }}', {{ $a->encounter_id ?? 'null' }}, true)"
                                    class="text-indigo-600 hover:text-indigo-900 text-[10px] font-black uppercase tracking-widest border border-indigo-200 hover:border-indigo-400 px-4 py-2 rounded-xl transition bg-indigo-50/30">
                                Transfer
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     2D FLOOR MAP MODAL (Core 1 Style)
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="floorMapModal" class="fixed inset-0 z-[9999] hidden items-center justify-center flex" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white" style="width:820px; max-width:95%; max-height:90vh; display:flex; flex-direction:column; padding:0; overflow:hidden; border-radius:14px; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.25);">

        {{-- Modal Header --}}
        <div style="padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-shrink:0; background:#fff;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:40px; height:40px; border-radius:10px; background:#eef2ff; color:#4f46e5; display:flex; align-items:center; justify-content:center;">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 style="margin:0; font-size:17px; font-weight:900; color:#0f172a; font-family:'Inter', sans-serif;" id="floorMapModalTitle">Admit Patient</h3>
                    <p style="margin:0; font-size:12px; color:#64748b; font-family:'Inter', sans-serif;" id="floorMapPatientInfo">Select an available bed from the floor plan below</p>
                </div>
            </div>
            <button type="button" onclick="closeFloorMap()" style="background:transparent; border:none; color:#94a3b8; cursor:pointer; line-height:1; padding:0;">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
            
            {{-- Zone Tab Strip Container (Injected by JS) --}}
            <div id="bpZoneTabsContainer" style="display:flex; gap:0; background:#f8f9fb; border-bottom:1px solid #e2e8f0; flex-shrink:0;">
                <!-- Tabs will be rendered here -->
            </div>

            {{-- Floor Plan Body --}}
            <div id="floorMapContent" style="flex:1; overflow-y:auto; padding:20px 24px; background:#f8f9fb;">
                <div class="flex items-center justify-center h-32 text-slate-400 font-bold">
                    <svg class="w-6 h-6 animate-spin mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Loading floor map…
                </div>
            </div>

            {{-- Legend + Selection Bar + Actions --}}
            <div style="flex-shrink:0; border-top:1px solid #e2e8f0; background:#fff; padding:14px 24px;">
                {{-- Legend --}}
                <div style="display:flex; gap:16px; margin-bottom:12px; flex-wrap:wrap; font-family:'Inter', sans-serif;">
                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b;">
                        <span style="width:12px; height:12px; border-radius:3px; background:#f0fff4; border:2px solid #86efac; display:inline-block;"></span> Available
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b;">
                        <span style="width:12px; height:12px; border-radius:3px; background:#fff5f5; border:2px solid #fca5a5; display:inline-block;"></span> Occupied
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b;">
                        <span style="width:12px; height:12px; border-radius:3px; background:#fffbeb; border:2px solid #fcd34d; display:inline-block;"></span> Cleaning
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:#64748b;">
                        <span style="width:12px; height:12px; border-radius:3px; background:rgba(37,99,235,0.12); border:2px solid #2563eb; display:inline-block;"></span> Selected
                    </div>
                </div>
                {{-- Selection indicator + actions --}}
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; font-family:'Inter', sans-serif;">
                    <div id="bpSelectionBar" style="font-size:13px; color:#64748b; font-style:italic;">
                        No bed selected — click an available bed above
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="button" tabindex="-1" onclick="closeFloorMap()" style="padding:10px 18px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#475569; font-weight:700; font-size:13px; cursor:pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Cancel</button>
                        <button type="button" id="confirmAllocationBtn" onclick="confirmAllocation()" disabled style="padding:10px 18px; border-radius:8px; border:none; background:#94a3b8; color:#fff; font-weight:700; font-size:13px; cursor:not-allowed; display:flex; align-items:center; gap:8px; transition:all 0.2s;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span id="confirmBtnText">Admit Patient</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* Core 1 Bed UI styles ported */
.bp-zone-tab.active { background: #fff !important; }
.bp-bed-wrap:hover .bp-bed-available { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); }
</style>
<script>
    let currentRoomAssignmentId = null;
    let selectedBedId = null;
    let selectedBedLabel = '';
    let isTransferMode = false;
    let currentEncounterId = null;
    
    // Core 1 definitions
    const bpZones = {
        'ICU': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>', color: '#dc2626' },
        'ER': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" /></svg>', color: '#ea580c' },
        'WARD': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" /></svg>', color: '#2563eb' },
        'OR': { icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" /></svg>', color: '#7c3aed' }
    };

    function openFloorMap(id, patientName, mrn, encounterId, transfer = false) {
        currentRoomAssignmentId = id;
        currentEncounterId = encounterId;
        isTransferMode = transfer;
        selectedBedId = null;
        selectedBedLabel = '';
        
        const btn = document.getElementById('confirmAllocationBtn');
        btn.disabled = true;
        btn.style.background = '#94a3b8';
        btn.style.cursor = 'not-allowed';
        
        document.getElementById('floorMapModalTitle').textContent = isTransferMode ? 'Transfer Patient' : 'Admit Patient';
        document.getElementById('confirmBtnText').textContent = isTransferMode ? 'Execute Transfer' : 'Admit Patient';
        
        document.getElementById('bpSelectionBar').innerHTML = 'No bed selected &mdash; click an available bed above';
        document.getElementById('floorMapPatientInfo').textContent =
            `${patientName} ${mrn ? '(MRN: ' + mrn + ')' : ''} — Encounter #${encounterId}`;
        
        const modal = document.getElementById('floorMapModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        loadFloorMap();
    }

    function closeFloorMap() {
        document.getElementById('floorMapModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function switchBpZone(zoneKey) {
        document.querySelectorAll('.bp-zone-tab').forEach(t => {
            t.classList.remove('active');
            t.style.borderBottomColor = 'transparent';
            t.style.color = '#64748b';
            t.style.background = 'none';
        });
        document.querySelectorAll('.bp-zone-panel').forEach(p => p.style.display = 'none');
        
        const tab = document.querySelector(`.bp-zone-tab[data-zone="${zoneKey}"]`);
        if(tab) {
            tab.classList.add('active');
            tab.style.borderBottomColor = bpZones[zoneKey].color;
            tab.style.color = bpZones[zoneKey].color;
            tab.style.background = '#fff';
        }
        
        const panel = document.getElementById(`bp-zone-${zoneKey}`);
        if(panel) panel.style.display = 'block';
    }

    function loadFloorMap() {
        const container = document.getElementById('floorMapContent');
        const tabsContainer = document.getElementById('bpZoneTabsContainer');
        
        container.innerHTML = `
            <div class="flex items-center justify-center h-40 text-slate-400 font-bold font-sans">
                <svg class="w-6 h-6 animate-spin mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Loading floor map…
            </div>`;
        tabsContainer.innerHTML = '';

        fetch('{{ route("core2.bed-linen.floor-map-data") }}')
            .then(r => r.json())
            .then(wards => {
                // Group by zone
                const grouped = { 'ICU': [], 'ER': [], 'WARD': [], 'OR': [] };
                wards.forEach(w => {
                    const z = (w.type || 'WARD').toUpperCase();
                    if(grouped[z]) grouped[z].push(w); else grouped['WARD'].push(w);
                });
                
                // Render Tabs
                let firstZone = null;
                Object.keys(grouped).forEach(zk => {
                    const empty = grouped[zk].length === 0;
                    if(!empty && !firstZone) firstZone = zk;
                    
                    let bedsCount = 0;
                    grouped[zk].forEach(w => {
                        w.rooms.forEach(r => bedsCount += r.beds.length);
                    });
                    
                    const color = bpZones[zk].color;
                    const isActive = false; // Set after render
                    
                    tabsContainer.innerHTML += `
                        <button type="button" class="bp-zone-tab" data-zone="${zk}" onclick="switchBpZone('${zk}')"
                            style="padding:12px 20px; border:none; outline:none; font-family:'Inter', sans-serif; font-size:13px; font-weight:600; cursor:pointer; border-bottom:2px solid transparent; display:flex; align-items:center; gap:7px; transition:all 0.15s; white-space:nowrap; color:#64748b; background:none;">
                            ${bpZones[zk].icon} ${zk}
                            <span style="font-size:11px; font-weight:500; background:${empty ? '#e2e8f0' : '#eef2ff'}; color:${empty ? '#94a3b8' : '#4f46e5'}; padding:2px 8px; border-radius:20px; margin-left:2px;">
                                ${bedsCount}
                            </span>
                        </button>
                    `;
                });
                
                if(!firstZone) firstZone = 'WARD'; // Fallback

                // Render Content
                let html = '';
                Object.keys(grouped).forEach(zk => {
                    html += `<div id="bp-zone-${zk}" class="bp-zone-panel" style="display:none; font-family:'Inter', sans-serif;">`;
                    
                    if(grouped[zk].length === 0) {
                        html += `
                            <div style="text-align:center; padding:50px 0; color:#cbd5e1;">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="font-bold">No wards configured for ${zk}</p>
                            </div>
                        `;
                    } else {
                        grouped[zk].forEach(ward => {
                            html += `<div class="bp-ward" style="margin-bottom:24px;">`;
                            html += `
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span style="font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#1e293b;">${ward.name}</span>
                                </div>
                            `;
                            
                            ward.rooms.forEach(room => {
                                html += `<div class="bp-room" style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:12px;">`;
                                html += `
                                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9;">
                                        <span style="font-size:12px; font-weight:800; background:#f1f5f9; color:#334155; padding:3px 12px; border-radius:20px;">Room ${room.room_number}</span>
                                        <span style="font-size:11px; font-weight:600; color:#94a3b8;">${room.room_type || ''}</span>
                                    </div>
                                    <div style="display:flex; flex-wrap:wrap; gap:12px;">
                                `;
                                
                                room.beds.forEach(bed => {
                                    const bedStatus = bed.status.toLowerCase();
                                    const isAvailable = bedStatus === 'available';
                                    const isOccupied = bedStatus === 'occupied';
                                    
                                    const firstName = bed.patient_name ? bed.patient_name.split(' ')[0] : null;
                                    const tooltip = isOccupied && bed.patient_name ? `${bed.patient_name}\n${bed.mrn ? 'MRN: '+bed.mrn : ''}` : bed.status;
                                    
                                    const borderColor = isAvailable ? '#86efac' : (isOccupied ? '#fca5a5' : '#fcd34d');
                                    const bgColor = isAvailable ? '#f0fff4' : (isOccupied ? '#fff5f5' : '#fffbeb');
                                    const textColor = isAvailable ? '#166534' : (isOccupied ? '#991b1b' : '#92400e');
                                    const pillowColor = isAvailable ? '#bbf7d0' : (isOccupied ? '#fecaca' : '#fde68a');
                                    
                                    html += `
                                        <div class="bp-bed-wrap" style="position:relative;">
                                            <div class="bp-bed bp-bed-${bedStatus}"
                                                 data-bed-id="${bed.id}"
                                                 data-bed-label="${ward.name} &mdash; Room ${room.room_number} &mdash; Bed ${bed.bed_number}"
                                                 ${isAvailable ? 'onclick="selectBed(this)"' : ''}
                                                 title="${tooltip}"
                                                 style="
                                                    width:72px; height:84px;
                                                    border-radius:10px;
                                                    border:2px solid ${borderColor};
                                                    background:${bgColor};
                                                    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;
                                                    cursor:${isAvailable ? 'pointer' : 'not-allowed'};
                                                    opacity:${isAvailable ? '1' : '0.65'};
                                                    transition:all 0.15s;
                                                    position:relative; overflow:hidden;
                                                    user-select:none;
                                                 ">
                                                
                                                <svg class="w-2.5 h-2.5" style="color:${textColor};" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" /></svg>
                                                <div style="width:48px; height:24px; border-radius:4px 4px 6px 6px; background:${pillowColor};"></div>
                                                
                                                <span style="font-size:10px; font-weight:800; color:${textColor}; text-transform:uppercase;">${bed.bed_number}</span>
                                                
                                                ${isOccupied && firstName ? `<span style="font-size:9px; font-weight:700; color:#991b1b; max-width:68px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:center; line-height:1.1; margin-top:-2px;">${firstName}</span>` : ''}
                                                ${!isAvailable && !isOccupied ? `<span style="font-size:8px; color:#92400e; font-weight:700; text-transform:uppercase;">Cleaning</span>` : ''}
                                                
                                                <div class="bp-bed-check" style="display:none; position:absolute; inset:0; background:rgba(37,99,235,0.12); border-radius:8px; align-items:center; justify-content:center;">
                                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                                
                                html += `</div></div>`; // End room, beds
                            });
                            html += `</div>`; // End ward
                        });
                    }
                    html += `</div>`; // End zone
                });
                
                container.innerHTML = html;
                switchBpZone(firstZone);
            })
            .catch(err => {
                container.innerHTML = `<p style="text-align:center; color:#f87171; font-weight:bold; padding:40px 0;">Failed to load floor map.</p>`;
                console.error(err);
            });
    }

    function selectBed(el) {
        document.querySelectorAll('.bp-bed').forEach(c => {
            c.style.boxShadow = 'none';
            c.style.borderColor = c.classList.contains('bp-bed-available') ? '#86efac' : c.style.borderColor;
            const check = c.querySelector('.bp-bed-check');
            if(check) check.style.display = 'none';
        });
        
        el.style.borderColor = '#2563eb';
        el.style.boxShadow = '0 0 0 4px rgba(37,99,235,0.2)';
        const check = el.querySelector('.bp-bed-check');
        if(check) check.style.display = 'flex';
        
        selectedBedId = el.getAttribute('data-bed-id');
        selectedBedLabel = el.getAttribute('data-bed-label');
        
        document.getElementById('bpSelectionBar').innerHTML = `<span style="color:#1e293b; font-weight:700; font-style:normal;">Selected:</span> <span style="color:#2563eb; font-weight:800;">${selectedBedLabel}</span>`;
        
        const btn = document.getElementById('confirmAllocationBtn');
        btn.disabled = false;
        btn.style.background = '#4f46e5';
        btn.style.cursor = 'pointer';
    }
    function confirmAllocation() {
        if (!selectedBedId) return;

        const btn = document.getElementById('confirmAllocationBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing…`;

        const endpoint = isTransferMode 
            ? '{{ route("core2.bed-linen.patient-transfer.execute") }}'
            : '{{ route("core2.bed-linen.allocate-bed") }}';

        const body = isTransferMode
            ? { encounter_id: currentEncounterId, new_bed_id: selectedBedId }
            : { room_assignment_id: currentRoomAssignmentId, bed_id: selectedBedId };

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(body)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeFloorMap();
                
                if (isTransferMode) {
                    // Refresh view or show success
                    location.reload(); 
                } else {
                    const row = document.getElementById('row-' + currentRoomAssignmentId);
                    if (row) {
                        row.style.transition = 'opacity 0.4s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 400);
                    }
                }

                const flash = document.createElement('div');
                flash.className = 'core2-flash-success mx-10 mt-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-6 py-4 text-sm font-semibold flex items-center gap-3 fixed top-4 right-4 z-[10000] shadow-lg';
                flash.innerHTML = `<svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ${data.message}`;
                document.body.appendChild(flash);
                setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 300); }, 4000);
            } else {
                alert(data.message || 'Action failed.');
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> ${isTransferMode ? 'Execute Transfer' : 'Admit Patient'}`;
            }
        })
        .catch(err => {
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> ${isTransferMode ? 'Execute Transfer' : 'Admit Patient'}`;
            console.error(err);
        });
    }
</script>
@endpush
