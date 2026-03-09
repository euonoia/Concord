@extends('core.core1.layouts.app')

@section('title', 'Medical Records')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/core1/example.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Medical Records</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($records as $patient)
                        @php
                            $latestRecord = $patient->medicalRecords->first();
                            $latestAppointment = $patient->appointments->first();
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $patient->name }}</td>
                            <td class="px-6 py-4">
                                @if($latestRecord)
                                    {{ $latestRecord->record_type }}
                                @elseif($latestAppointment)
                                    {{ ucfirst($latestAppointment->type ?? 'N/A') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($latestRecord)
                                    {{ $latestRecord->record_date->format('M d, Y') }}
                                @elseif($latestAppointment)
                                    {{ \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" onclick="openRecordModal('{{ route('core1.medical-records.show', $patient->id) }}')" 
                                        class="core1-icon-action text-blue" title="View Record Details border-0 bg-transparent">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500 italic">No records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">
        {{ $records->links() }}
    </div>
</div>

<!-- Medical Record Modal -->
<div id="medicalRecordModal" class="fixed inset-0 z-[1060] overflow-y-auto" style="display:none;" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" style="z-index:-1;" onclick="closeRecordModal()"></div>
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-4xl" style="max-height: 90vh; display: flex; flex-direction: column;">
            
            <div class="px-6 pt-6 pb-4 border-b border-gray-200 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#eff6ff;">
                        <i class="fas fa-file-medical" style="color:#2563eb;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Medical Record Details</h3>
                    </div>
                </div>
                <button type="button" onclick="closeRecordModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="px-6 py-5 overflow-y-auto" style="flex: 1;" id="modalContentWrapper">
                <!-- Loading Spinner -->
                <div id="modalLoader" class="flex flex-col items-center justify-center py-10" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600 font-medium">Loading record details...</p>
                </div>
                
                <!-- Injected Content -->
                <div id="modalContentInner" class="w-full"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openRecordModal(url) {
        const modal = document.getElementById('medicalRecordModal');
        const loader = document.getElementById('modalLoader');
        const content = document.getElementById('modalContentInner');
        
        // Show modal and loader, clear old content
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        loader.style.display = 'flex';
        content.innerHTML = '';

        // Fetch the raw partial view via AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error loading details');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Remove the duplicate header title and Back button inside the fetched view since the modal has one
            const titles = doc.querySelectorAll('h1');
            titles.forEach(h => {
                if (h.innerText.includes('Medical Record Details')) {
                    const row = h.closest('.flex.justify-between');
                    if(row) row.remove();
                }
            });

            content.innerHTML = doc.body.innerHTML;
            loader.style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching record:', error);
            content.innerHTML = `<div class="p-8 text-center" style="color:#dc2626; font-weight:bold;"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load record details. Please try again.</div>`;
            loader.style.display = 'none';
        });
    }

    function closeRecordModal() {
        const modal = document.getElementById('medicalRecordModal');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore background scrolling
    }
</script>
@endsection