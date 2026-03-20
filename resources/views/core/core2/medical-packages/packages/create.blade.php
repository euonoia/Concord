@extends('layouts.core2.app')
@section('title', 'Add Package')

@section('content')
<div class="mb-12 flex justify-between items-end">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">MEDICAL PACKAGES › PRICING</p>
        <h2 class="text-5xl font-black text-slate-900 tracking-tighter uppercase">New Package <span class="text-indigo-200">Definition</span></h2>
    </div>
    <a href="{{ route('core2.medical-packages.packages.index') }}" class="px-6 py-3 rounded-2xl border border-slate-200 font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition">
        Back to Workspace
    </a>
</div>

<form action="{{ route('core2.medical-packages.packages.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="font-black text-slate-900 uppercase tracking-tight">Package Identity</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">System Package ID</label>
                        <input type="text" name="package_id" value="{{ old('package_id') }}" required class="w-full bg-slate-50 border-2 border-transparent rounded-3xl p-5 font-bold outline-none focus:border-indigo-500/20 focus:bg-white transition" placeholder="e.g. PKG-2026-001">
                        @error('package_id')<p class="text-red-500 text-[10px] font-bold mt-1 px-2 uppercase">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Package Formal Name</label>
                        <input type="text" name="package_name" value="{{ old('package_name') }}" required class="w-full bg-slate-50 border-2 border-transparent rounded-3xl p-5 font-bold outline-none focus:border-indigo-500/20 focus:bg-white transition" placeholder="e.g. Executive Wellness Gold">
                        @error('package_name')<p class="text-red-500 text-[10px] font-bold mt-1 px-2 uppercase">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Service Inclusions & Breakdown</label>
                    <textarea name="includes_services" rows="6" class="w-full bg-slate-50 border-2 border-transparent rounded-[32px] p-8 font-bold outline-none focus:border-indigo-500/20 focus:bg-white transition" placeholder="List all services included in this bundle...">{{ old('includes_services') }}</textarea>
                    @error('includes_services')<p class="text-red-500 text-[10px] font-bold mt-1 px-2 uppercase">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-900 rounded-[40px] p-10 text-white shadow-2xl shadow-indigo-200">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zM17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/></svg>
                    </div>
                    <h3 class="font-black uppercase tracking-tight text-sm">Valuation</h3>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Net Package Price (PHP)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 font-black">₱</span>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01" class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 pl-10 font-black text-2xl outline-none focus:ring-4 ring-indigo-500/20 transition" placeholder="0.00">
                        </div>
                        @error('price')<p class="text-indigo-400 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p>@enderror
                    </div>

                    <div class="pt-4 space-y-3">
                        <button type="submit" class="w-full bg-indigo-600 text-white py-5 rounded-3xl font-black text-xs uppercase tracking-[2px] shadow-xl hover:bg-indigo-500 transition-all active:scale-95">
                            Commit to Database
                        </button>
                        <p class="text-[9px] text-center text-slate-500 font-bold uppercase tracking-tighter">By committing, this package will be immediately available for patient enrollment.</p>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-[32px] p-8 border border-indigo-100">
                <h4 class="text-indigo-900 font-black text-[10px] uppercase tracking-widest mb-2">Pro Tip</h4>
                <p class="text-indigo-700/70 text-xs font-bold leading-relaxed">Ensure the package price is lower than the sum of individual service costs to provide value to the patient.</p>
            </div>
        </div>
    </div>
</form>
@endsection