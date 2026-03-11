@extends('layouts.core2.app')
@section('title', 'Add Utilization Report')
@section('content')
<div class="max-w-2xl">
    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">SURGERY & DIET › UTILIZATION</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">New Utilization Report</h2>
    </div>
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('core2.surgery-diet.utilization.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Report ID</label>
                <input type="text" name="report_id" value="{{ old('report_id') }}" required class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. RPT-001">
                @error('report_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Module Name</label>
                <input type="text" name="module_name" value="{{ old('module_name') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. Laboratory, Pharmacy">
                @error('module_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Usage Metrics</label>
                <input type="text" name="usage_metrics" value="{{ old('usage_metrics') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. 350 visits">
                @error('usage_metrics')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reporting Period</label>
                <input type="date" name="reporting_period" value="{{ old('reporting_period') }}" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10">
                @error('reporting_period')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-4 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition">Commit to Database</button>
                <a href="{{ route('core2.surgery-diet.utilization.index') }}" class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
