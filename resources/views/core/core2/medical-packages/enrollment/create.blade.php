@extends('layouts.core2.app')
@section('title', 'Add Enrollment')

@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">MEDICAL PACKAGES › ENROLLMENT</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">New Patient Enrollment</h2>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('core2.medical-packages.enrollment.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Patient ID</label>
                <input type="text" name="patient_id" value="{{ old('patient_id') }}" required 
                       class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" 
                       placeholder="e.g., PAT-001">
                @error('patient_id')<p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Package Name/ID</label>
                <input type="text" name="package_identifier" value="{{ old('package_identifier') }}" required 
                       class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" 
                       placeholder="e.g., GOLD-WELLNESS-01">
                @error('package_identifier')<p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Package Price</label>
                <input type="number" step="0.01" name="total_price" value="{{ old('total_price') }}" required 
                       class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" 
                       placeholder="0.00">
                @error('total_price')<p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enrollment Date</label>
                <input type="date" name="enrollment_date" value="{{ date('Y-m-d') }}" required 
                       class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10">
                @error('enrollment_date')<p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition uppercase tracking-widest">
                    Commit to Database
                </button>
                <a href="{{ route('core2.medical-packages.enrollment.index') }}" 
                   class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition uppercase tracking-widest">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection