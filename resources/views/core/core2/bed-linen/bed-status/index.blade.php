@extends('layouts.core2.app')
@section('title', 'Bed Status & Allocation')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">BED & LINEN › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Bed Status & Allocation</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Monitor bed occupancy and allocation</p>
    </div>
    <a href="{{ route('core2.bed-linen.bed-status.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden flex flex-col" style="min-height: 600px;">
    {{-- Zone Tab Strip Container --}}
    <div id="bpZoneTabsContainer" class="flex gap-0 bg-slate-50 border-b border-slate-200 shrink-0 overflow-x-auto p-2">
        <!-- Tabs will be rendered here -->
    </div>

    {{-- Floor Plan Body --}}
    <div id="floorMapContent" class="flex-1 overflow-y-auto p-6 bg-slate-50 relative">
        <div class="flex items-center justify-center h-32 text-slate-400 font-bold">
            <svg class="w-6 h-6 animate-spin mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Loading floor map…
        </div>
    </div>

    {{-- Legend --}}
    <div class="shrink-0 border-t border-slate-200 bg-white p-4">
        <div class="flex flex-wrap gap-4 mb-2 font-sans">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
                <span class="w-3 h-3 rounded-sm bg-emerald-50 border-2 border-emerald-300 block"></span> Available
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
                <span class="w-3 h-3 rounded-sm bg-red-50 border-2 border-red-300 block"></span> Occupied
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
                <span class="w-3 h-3 rounded-sm bg-amber-50 border-2 border-amber-300 block"></span> Cleaning
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
                <span class="w-3 h-3 rounded-sm bg-slate-100 border-2 border-slate-400 block"></span> Maintenance
            </div>
        </div>
        <p class="text-xs text-slate-400 font-medium italic">Click on a bed to update its status (excluding Active Admissions).</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     BED STATUS UPDATE MODAL
     ═══════════════════════════════════════════════════════════════════════════ --}}
<div id="updateStatusModal" class="fixed inset-0 z-[9999] hidden items-center justify-center flex bg-black/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl w-[400px] shadow-2xl overflow-hidden transform scale-95 transition-transform" id="updateStatusModalInner">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-slate-800 tracking-tight">Update Bed Status</h3>
                    <p class="text-xs font-semibold text-slate-500" id="updateModalBedLabel">Ward - Room - Bed</p>
                </div>
            </div>
            <button type="button" onclick="closeUpdateModal()" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="p-6">
            <input type="hidden" id="updateBedId" value="">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-3">Select New Status</label>
            <div class="grid grid-cols-2 gap-3" id="statusOptionsGrid">
                <!-- Status options dynamically generated -->
            </div>
            <div id="updateModalError" class="hidden mt-4 p-3 bg-red-50 text-red-700 rounded-xl text-xs font-semibold border border-red-200">
                <!-- Error text -->
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" onclick="closeUpdateModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 transition">Cancel</button>
            <button type="button" onclick="saveBedStatus()" id="saveStatusBtn" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 shadow-sm hover:bg-indigo-700 transition flex items-center gap-2">
                Save Changes
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* Core 1 Bed UI styles ported */
.bp-zone-tab.active { background: #fff !important; }
.bp-bed-wrap:hover .bp-bed:not(.bp-bed-occupied) { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); }
</style>
<script>
    let currentSelectedBedId = null;
    let currentSelectedBedStatus = null;
    let autoRefreshInterval = null;
    let isInitialLoad = true;
    
    // Core 1 definitions
    const bpZones = {
        'ICU': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>', color: '#dc2626' },
        'ER': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" /></svg>', color: '#ea580c' },
        'WARD': { icon: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" /></svg>', color: '#2563eb' },
        'OR': { icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" /></svg>', color: '#7c3aed' }
    };

    const statusConfig = {
        'Available':   { bg: '#f0fff4', border: '#86efac', text: '#166534', pillow: '#bbf7d0', label: 'Available' },
        'Occupied':    { bg: '#fff5f5', border: '#fca5a5', text: '#991b1b', pillow: '#fecaca', label: 'Occupied' },
        'Cleaning':    { bg: '#fffbeb', border: '#fcd34d', text: '#92400e', pillow: '#fde68a', label: 'Cleaning' },
        'Maintenance': { bg: '#f8fafc', border: '#94a3b8', text: '#334155', pillow: '#cbd5e1', label: 'Maintenance' },
    };

    document.addEventListener('DOMContentLoaded', () => {
        loadFloorMap();
        
        // Auto-refresh every 15 seconds
        autoRefreshInterval = setInterval(() => {
            // Don't refresh if the modal is open to prevent UI jarring
            if(document.getElementById('updateStatusModal').classList.contains('hidden')) {
                loadFloorMap();
            }
        }, 15000);
    });

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
        
        // Only show spinner on very first load
        if (isInitialLoad) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-40 text-slate-400 font-bold font-sans">
                    <svg class="w-6 h-6 animate-spin mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Loading floor map…
                </div>`;
            tabsContainer.innerHTML = '';
        }

        // Store current active zone to maintain it across refreshes
        let activeZone = document.querySelector('.bp-zone-tab.active')?.getAttribute('data-zone') || null;

        fetch('{{ route("core2.bed-linen.floor-map-data") }}')
            .then(r => r.json())
            .then(wards => {
                isInitialLoad = false;
                
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
                    
                    tabsContainer.innerHTML += `
                        <button type="button" class="bp-zone-tab" data-zone="${zk}" onclick="switchBpZone('${zk}')"
                            style="padding:16px 24px; border:none; outline:none; font-family:'Inter', sans-serif; font-size:14px; font-weight:700; cursor:pointer; border-bottom:3px solid transparent; display:flex; align-items:center; gap:8px; transition:all 0.15s; white-space:nowrap; color:#64748b; background:none;">
                            ${bpZones[zk].icon} ${zk}
                            <span style="font-size:12px; font-weight:600; background:${empty ? '#e2e8f0' : '#eef2ff'}; color:${empty ? '#94a3b8' : '#4f46e5'}; padding:2px 10px; border-radius:20px; margin-left:4px;">
                                ${bedsCount}
                            </span>
                        </button>
                    `;
                });
                
                if(!activeZone) activeZone = firstZone || 'WARD';

                // Render Content
                let html = '';
                Object.keys(grouped).forEach(zk => {
                    html += `<div id="bp-zone-${zk}" class="bp-zone-panel" style="display:none; font-family:'Inter', sans-serif;">`;
                    
                    if(grouped[zk].length === 0) {
                        html += `
                            <div style="text-align:center; padding:60px 0; color:#cbd5e1;">
                                <svg class="w-14 h-14 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="font-bold text-lg tracking-tight">No wards configured for ${zk}</p>
                            </div>
                        `;
                    } else {
                        grouped[zk].forEach(ward => {
                            html += `<div class="bp-ward" style="margin-bottom:32px;">`;
                            html += `
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
                                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span style="font-size:16px; font-weight:900; text-transform:uppercase; letter-spacing:0.5px; color:#0f172a;">${ward.name}</span>
                                </div>
                            `;
                            
                            ward.rooms.forEach(room => {
                                html += `<div class="bp-room" style="background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; box-shadow:0 1px 2px 0 rgba(0,0,0,0.05); margin-bottom:16px;">`;
                                html += `
                                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #f1f5f9;">
                                        <span style="font-size:14px; font-weight:800; background:#f1f5f9; color:#334155; padding:4px 14px; border-radius:20px;">Room ${room.room_number}</span>
                                        <span style="font-size:12px; font-weight:600; color:#94a3b8;">${room.room_type || ''}</span>
                                    </div>
                                    <div style="display:flex; flex-wrap:wrap; gap:16px;">
                                `;
                                
                                room.beds.forEach(bed => {
                                    // Parse status, defaulting to Available if unrecognized
                                    let rawStatus = bed.status;
                                    let safeStatus = Object.keys(statusConfig).find(k => k.toLowerCase() === rawStatus.toLowerCase()) || 'Available';
                                    let cfg = statusConfig[safeStatus];
                                    
                                    const isOccupied = safeStatus === 'Occupied';
                                    const firstName = bed.patient_name ? bed.patient_name.split(' ')[0] : null;
                                    const tooltip = isOccupied && bed.patient_name ? `${bed.patient_name}\n${bed.mrn ? 'MRN: '+bed.mrn : ''}` : safeStatus;
                                    
                                    html += `
                                        <div class="bp-bed-wrap" style="position:relative;">
                                            <div class="bp-bed bp-bed-${safeStatus.toLowerCase()}"
                                                 data-bed-id="${bed.id}"
                                                 data-bed-status="${safeStatus}"
                                                 data-bed-label="${ward.name} &mdash; Room ${room.room_number} &mdash; Bed ${bed.bed_number}"
                                                 title="${tooltip}"
                                                 onclick="openUpdateModal(this)"
                                                 style="
                                                    width:80px; height:96px;
                                                    border-radius:12px;
                                                    border:2px solid ${cfg.border};
                                                    background:${cfg.bg};
                                                    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:5px;
                                                    cursor:pointer;
                                                    transition:all 0.15s;
                                                    position:relative; overflow:hidden;
                                                    user-select:none;
                                                 ">
                                                
                                                <svg class="w-3.5 h-3.5" style="color:${cfg.text};" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" /></svg>
                                                <div style="width:52px; height:26px; border-radius:5px 5px 8px 8px; background:${cfg.pillow};"></div>
                                                
                                                <span style="font-size:11px; font-weight:800; color:${cfg.text}; text-transform:uppercase;">${bed.bed_number}</span>
                                                
                                                ${isOccupied && firstName ? `<span style="font-size:10px; font-weight:800; color:#991b1b; max-width:72px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-align:center; line-height:1.1; margin-top:-2px;">${firstName}</span>` : ''}
                                                ${!isOccupied && safeStatus !== 'Available' ? `<span style="font-size:9px; color:${cfg.text}; font-weight:800; text-transform:uppercase;">${safeStatus}</span>` : ''}
                                                
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
                
                // Update HTML only after building everything to prevent flicker
                if (tabsContainer.innerHTML !== document.createRange().createContextualFragment('').innerHTML) {
                    // Only update tabs if they changed significantly, or if we didn't have any yet
                    const currentTabsHtml = tabsContainer.innerHTML;
                    tabsContainer.innerHTML = ''; // This is a bit dirty but it ensures we re-bind the onClick properly in this simple script 
                    // (Actually we can't do innerHTML comparison easily due to spacing differences if we rebuild it every time. 
                    // We'll trust the framework to handle DOM diffs decently fast, but it's pure JS, so we just overwrite it).
                }
                
                // Rebuild tabs manually to avoid DOM issues
                tabsContainer.innerHTML = '';
                Object.keys(grouped).forEach(zk => {
                    const empty = grouped[zk].length === 0;
                    let bedsCount = 0;
                    grouped[zk].forEach(w => w.rooms.forEach(r => bedsCount += r.beds.length));
                    
                    const isActive = zk === activeZone;
                    
                    tabsContainer.innerHTML += `
                        <button type="button" class="bp-zone-tab ${isActive ? 'active' : ''}" data-zone="${zk}" onclick="switchBpZone('${zk}')"
                            style="padding:16px 24px; border:none; outline:none; font-family:'Inter', sans-serif; font-size:14px; font-weight:700; cursor:pointer; 
                            border-bottom:3px solid ${isActive ? bpZones[zk].color : 'transparent'}; 
                            display:flex; align-items:center; gap:8px; transition:all 0.15s; white-space:nowrap; 
                            color:${isActive ? bpZones[zk].color : '#64748b'}; 
                            background:${isActive ? '#fff' : 'none'};">
                            ${bpZones[zk].icon} ${zk}
                            <span style="font-size:12px; font-weight:600; background:${empty ? '#e2e8f0' : '#eef2ff'}; color:${empty ? '#94a3b8' : '#4f46e5'}; padding:2px 10px; border-radius:20px; margin-left:4px;">
                                ${bedsCount}
                            </span>
                        </button>
                    `;
                });

                container.innerHTML = html;
                switchBpZone(activeZone);
            })
            .catch(err => {
                if(isInitialLoad) {
                    container.innerHTML = `<p style="text-align:center; color:#ef4444; font-weight:bold; padding:50px 0; font-size:16px;">Failed to load floor map data.</p>`;
                }
                console.error(err);
            });
    }

    function openUpdateModal(el) {
        const bedId = el.getAttribute('data-bed-id');
        const status = el.getAttribute('data-bed-status');
        const label = el.getAttribute('data-bed-label');

        currentSelectedBedId = bedId;
        currentSelectedBedStatus = status;

        document.getElementById('updateBedId').value = bedId;
        document.getElementById('updateModalBedLabel').innerHTML = label;
        document.getElementById('updateModalError').classList.add('hidden');
        
        const saveBtn = document.getElementById('saveStatusBtn');
        saveBtn.disabled = false;
        saveBtn.innerHTML = 'Save Changes';
        saveBtn.className = "px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 shadow-sm hover:bg-indigo-700 transition flex items-center gap-2";

        // Generate status options visually
        const grid = document.getElementById('statusOptionsGrid');
        grid.innerHTML = '';
        
        ['Available', 'Cleaning', 'Maintenance'].forEach(s => {
            const isCurrent = s === status;
            const cfg = statusConfig[s];
            grid.innerHTML += `
                <label class="cursor-pointer border-2 rounded-xl p-3 flex items-center gap-3 transition ${isCurrent ? 'ring-2 ring-indigo-500 ring-offset-1 border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-indigo-300'}"
                       style="background-color: ${isCurrent ? '' : '#fff'}">
                    <input type="radio" name="new_status" value="${s}" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300" ${isCurrent ? 'checked' : ''}>
                    <div style="color:${cfg.text}; font-weight:800; font-size:13px; display:flex; align-items:center; gap:6px;">
                        <span style="width:12px; height:12px; border-radius:3px; background:${cfg.bg}; border:2px solid ${cfg.border}; display:inline-block;"></span>
                        ${s}
                    </div>
                </label>
            `;
        });

        // If currently occupied, we show a special message and disable manual changes unless discharging
        if (status === 'Occupied') {
            grid.innerHTML = `
                <div class="col-span-2 p-4 bg-red-50 text-red-700 rounded-xl border border-red-200 flex gap-3 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <span class="font-bold">Bed is Occupied.</span><br>
                        You cannot change the status here. The patient must be transferred or discharged first using the respective modules.
                    </div>
                </div>
            `;
            saveBtn.disabled = true;
            saveBtn.className = "px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-400 shadow-sm cursor-not-allowed flex items-center gap-2";
        }

        const modal = document.getElementById('updateStatusModal');
        const modalInner = document.getElementById('updateStatusModalInner');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Small delay for transition
        setTimeout(() => {
            modalInner.classList.remove('scale-95');
            modalInner.classList.add('scale-100');
        }, 10);
    }

    function closeUpdateModal() {
        const modal = document.getElementById('updateStatusModal');
        const modalInner = document.getElementById('updateStatusModalInner');
        
        modalInner.classList.remove('scale-100');
        modalInner.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    function saveBedStatus() {
        const selectedRadio = document.querySelector('input[name="new_status"]:checked');
        if(!selectedRadio) return;
        
        const newStatus = selectedRadio.value;
        if(newStatus === currentSelectedBedStatus) {
            closeUpdateModal();
            return;
        }

        const btn = document.getElementById('saveStatusBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving…`;
        const errDiv = document.getElementById('updateModalError');
        errDiv.classList.add('hidden');

        let updateUrl = '{{ route("core2.bed-linen.bed-status.update", ":id") }}'.replace(':id', currentSelectedBedId);

        fetch(updateUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(r => r.json().then(data => ({status: r.status, body: data})))
        .then(res => {
            if (res.status === 200 && res.body.success) {
                closeUpdateModal();
                showFlash(res.body.message, 'success');
                // Reload floor map to reflect changes
                loadFloorMap();
            } else {
                throw new Error(res.body.message || 'Update failed.');
            }
        })
        .catch(err => {
            console.error(err);
            errDiv.innerHTML = err.message;
            errDiv.classList.remove('hidden');
            btn.disabled = false;
            btn.innerHTML = 'Save Changes';
        });
    }

    function showFlash(message, type = 'success') {
        const flash = document.createElement('div');
        const color = type === 'success' ? 'emerald' : 'red';
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
            : '<svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            
        flash.className = `core2-flash mx-10 mt-6 bg-${color}-50 border border-${color}-200 text-${color}-800 rounded-2xl px-6 py-4 text-sm font-semibold flex items-center gap-3 fixed top-4 right-4 z-[10000] shadow-lg transition-opacity opacity-0`;
        flash.innerHTML = `${icon} ${message}`;
        document.body.appendChild(flash);
        
        requestAnimationFrame(() => flash.style.opacity = '1');
        setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 300); }, 4000);
    }
</script>
@endpush
