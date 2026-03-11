@extends('layouts.core2.app')
@section('title', 'Add Nutritional Assessment')
@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">SURGERY & DIET › NUTRITIONAL</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">New Assessment</h2>
    </div>
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('core2.surgery-diet.nutritional.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enrollment ID</label>
                <input type="text" name="enrollment_id" value="{{ old('enrollment_id') }}" required class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. ENR-001">
                @error('enrollment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Patient ID</label>
                <input type="number" name="patient_id" value="{{ old('patient_id') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Patient ID">
                @error('patient_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Package ID</label>
                <input type="text" name="package_id" value="{{ old('package_id') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="Package ID">
                @error('package_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enrollment Status</label>
                <input type="text" name="enrollment_status" value="{{ old('enrollment_status') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. Active / Completed">
                @error('enrollment_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition">Commit to Database</button>
                <a href="{{ route('core2.surgery-diet.nutritional.index') }}" class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
