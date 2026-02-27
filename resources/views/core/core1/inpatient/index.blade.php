@extends('layouts.core1.layouts.app')

@section('title', 'Inpatient Management')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="core1-container">
    <div class="core1-flex-between core1-header">
        <div>
            <h1 class="core1-title">Inpatient Management</h1>
            <p class="core1-subtitle">Manage admitted patients and bed allocation</p>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="core1-stats-grid">
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-door-closed text-blue mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['current_inpatients'] }}</p>
                <p class="text-xs text-gray">Current Inpatients</p>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-activity text-red mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['occupied'] }}</p>
                <p class="text-xs text-gray">Bed Occupancies</p>
            </div>
        </div>
        
        <div class="core1-stat-card">
            <div class="d-flex flex-col">
                <i class="bi bi-bed-front text-green mb-10 core1-icon-stats"></i>
                <p class="core1-title">{{ $stats['discharges_today'] }}</p>
                <p class="text-xs text-gray">Discharges Today</p>
            </div>
        </div>
    </div>
    <div class="d-flex justify-end mt-15">
    @if(auth()->user()->role !== 'doctor')
        <a href="{{ route('core1.patients.create') }}" class="core1-btn core1-btn-primary">
            <i class="bi bi-plus"></i>
            <span class="ml-10">Admit Patient</span>
        </a>
    @endif
</div>

    <!-- Tabs Section -->
    <div class="core1-card no-hover p-0 overflow-hidden mt-30">
        <div class="core1-tabs-header border-bottom">
            <button class="core1-tab-btn active" onclick="switchTab(event, 'inpatient-list')">
                <i class="bi bi-person-lines-fill mr-5"></i> Inpatient List
            </button>
            <button class="core1-tab-btn" onclick="switchTab(event, 'bed-allocation')">
                <i class="bi bi-bed-front mr-5"></i> Bed Allocation
            </button>
        </div>

        <div class="tab-content p-25">
            <!-- Inpatient List Tab -->
            <div id="inpatient-list" class="core1-tab-pane active">
                <h3 class="mb-20 text-sm font-bold">Admitted Patients</h3>
                <div class="core1-table-container shadow-none border">
                    <table class="core1-table">
                        <thead>
                            <tr>
                                <th>Inpatient ID</th>
                                <th>Patient</th>
                                <th>Bed</th>
                                <th>Admission Date</th>
                                <th>Doctor</th>
                                <th>Nurse</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    @foreach($inpatients as $inp)
        <tr>
            {{-- Inpatient ID --}}
            <td>{{ $inp->id }}</td>

            {{-- Patient Name --}}
            <td><a href="#" class="text-blue">{{ $inp->name }}</a></td>

            {{-- Bed --}}
            <td>
                <span class="core1-badge-teal">
                    {{ $inp->bed ?? 'N/A' }}
                </span>
            </td>

            {{-- Admission Date --}}
            <td>
                {{ $inp->admission_date ? \Carbon\Carbon::parse($inp->admission_date)->format('M d, Y') : 'N/A' }}
            </td>

            {{-- Doctor --}}
            <td>
                {{ $inp->doctor?->name ?? 'N/A' }}
            </td>

            {{-- Nurse --}}
<td>
    <div class="text-sm text-dark">
        {{ $inp->assignedNurse?->name ?? 'Unassigned' }}
    </div>
</td>

            {{-- Reason --}}
            <td>
                {{ $inp->reason ?? 'N/A' }}
            </td>

            {{-- Status --}}
<td>
    @php
        $isPriority = auth()->user()->role === 'nurse' && $inp->assigned_nurse_id === auth()->user()->id;
    @endphp
    <span class="text-{{ $inp->status === 'inactive' ? 'red' : 'green' }} font-bold">
        {{ ucfirst($inp->status ?? 'active') }} 
        @if($isPriority)
            (PRIORITY)
        @endif
    </span>
</td>

            {{-- Actions: Edit button to change status --}}
            <td class="text-right">
                <form action="{{ route('core1.inpatients.deactivate', $inp) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
    class="core1-btn-sm core1-btn-outline {{ $inp->status === 'inactive' ? 'text-green' : 'text-orange' }}">
    
    @if($inp->status === 'inactive')
        <i class="bi bi-check-circle"></i> Activate
    @else
        <i class="bi bi-x-circle"></i> Deactivate
    @endif
</button>

                </form>
            </td>
        </tr>
    @endforeach
</tbody>

                    </table>
                </div>
            </div>

            <!-- Bed Allocation Tab -->
            <div id="bed-allocation" class="core1-tab-pane">
                <h3 class="mb-20 text-sm font-bold">Bed Status Overview</h3>
                <div class="core1-bed-grid">
                    @foreach($beds as $bed)
                        <div class="core1-bed-card {{ $bed['bg'] }}">
                            <div class="d-flex justify-between items-start mb-10">
                                <div>
                                    <div class="font-bold text-sm">{{ $bed['id'] }}</div>
                                    <div class="text-xs text-gray">{{ $bed['type'] }}</div>
                                </div>
                                <i class="bi bi-bed-front text-gray"></i>
                            </div>
                            
                            @if($bed['status'] !== 'available' && $bed['status'] !== 'cleaning')
                                <div class="text-sm font-medium">{{ $bed['patient'] }}</div>
                                <div class="text-xs text-gray mb-10">{{ $bed['patient_id'] }}</div>
                                <span class="core1-status-tag core1-tag-{{ $bed['status'] }}">{{ ucfirst($bed['status']) }}</span>
                            @else
                                <div class="text-center mt-10">
                                    <span class="core1-status-tag core1-tag-{{ $bed['status'] }}">{{ ucfirst($bed['status']) }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(evt, tabId) {
    const tabPanes = document.getElementsByClassName('core1-tab-pane');
    for (let i = 0; i < tabPanes.length; i++) {
        tabPanes[i].classList.remove('active');
    }
    const tabBtns = document.getElementsByClassName('core1-tab-btn');
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove('active');
    }
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}
</script>
@endsection
