@extends('layouts.core2.app')
@section('title', 'Add Enrollment')
@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">MEDICAL PACKAGES › ENROLLMENT</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">New Enrollment</h2>
    </div>
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('core2.medical-packages.enrollment.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Package Identifier</label>
                <input type="text" name="package_identifier" value="{{ old('package_identifier') }}" required class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Identifier">
                @error('package_identifier')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Package Description</label>
                <textarea name="package_description" rows="3" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Description...">{{ old('package_description') }}</textarea>
                @error('package_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Price List Node</label>
                <input type="text" name="price_list_node" value="{{ old('price_list_node') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Department node">
                @error('price_list_node')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Included Services State</label>
                <input type="text" name="included_services_state" value="{{ old('included_services_state') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Current state">
                @error('included_services_state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition">Commit to Database</button>
                <a href="{{ route('core2.medical-packages.enrollment.index') }}" class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
