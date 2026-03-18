@extends('layouts.core2.app')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Medical Packages › Management</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">Package Definition & Pricing</h2>
    </div>
    <a href="{{ route('core2.medical-packages.packages.create') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:bg-indigo-700 transition">
        New Package
    </a>
</div>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (bundle includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- THE GRID (Strictly for Cards) --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($records as $r)
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-8 flex flex-col justify-between hover:shadow-md transition">
            <div>
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter">
                        {{ $r->package_identifier }}
                    </span>
                    <span class="text-indigo-600 font-black text-lg">
                        ₱{{ number_format($r->price_list_node, 2) }}
                    </span>
                </div>
                
                <h3 class="text-3xl font-black text-slate-900 mb-6 uppercase tracking-tight">
                    {{ $r->package_description }}
                </h3>

                <div class="space-y-2">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Inclusions Highlight</p>
                    <div class="flex flex-wrap gap-2">
                        @if($r->included_services_state)
                            @foreach(array_slice($r->included_services_state, 0, 2) as $service)
                                <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-xl text-[10px] font-bold italic">
                                    ✓ {{ $service }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-8">
                {{-- Toggle Button --}}
                <button type="button" 
    data-bs-toggle="modal" 
    data-bs-target="#packageModal{{ $r->id }}" 
    class="w-full bg-slate-50 text-slate-400 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition">
    View Full Details
</button>
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-20 bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
            <p class="text-slate-400 font-black uppercase text-xs">No Medical Package Nodes Found.</p>
        </div>
    @endforelse
</div>

<div class="mt-10">
    {{ $records->links() }}
</div>

{{-- MODAL CONTAINER (Moved outside the grid to prevent layout breaking) --}}
@foreach($records as $r)
<div class="modal fade" id="packageModal{{ $r->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $r->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 rounded-[50px] shadow-2xl overflow-hidden">
      <div class="modal-body p-12 bg-white">
        <div class="d-flex justify-content-between align-items-start mb-10">
          <h2 class="text-5xl font-black text-slate-900 tracking-tighter uppercase">
            {{ $r->package_description }}
          </h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

                <!-- modal content here -->
<div class="grid grid-cols-2 gap-6 mb-10">
                        <div class="bg-slate-50 rounded-[32px] p-8 text-center border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">ID Identifier</p>
                            <p class="text-xl font-black text-slate-700">{{ $r->package_identifier }}</p>
                        </div>
                        <div class="bg-indigo-50 rounded-[32px] p-8 text-center border border-indigo-100">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">Total Pricing</p>
                            <p class="text-4xl font-black text-indigo-600">₱{{ number_format($r->price_list_node, 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-10">
                        <div>
                            <h4 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-6">Included Services</h4>
                            <div class="space-y-3">
                                @foreach($r->included_services_state ?? [] as $service)
                                    <div class="bg-emerald-50/50 p-4 rounded-2xl text-slate-600 font-bold text-sm flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div> {{ $service }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-rose-400 uppercase tracking-widest mb-6">Explicit Exclusions</h4>
                            <div class="space-y-3">
                                @foreach($r->excluded_services_state ?? [] as $exclusion)
                                    <div class="bg-slate-50 p-4 rounded-2xl text-slate-400 font-bold text-sm flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-rose-400"></div> {{ $exclusion }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
           
            </div>
        </div>
    </div>
</div>
@endforeach

                

@endsection