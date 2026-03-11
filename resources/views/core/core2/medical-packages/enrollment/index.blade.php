@extends('layouts.core2.app')
@section('title', 'Patient Package Enrollment')
@section('content')
<div class="flex justify-between items-start mb-8">
    <div>
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">MEDICAL PACKAGES › WORKSPACE</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Patient Package Enrollment</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Manage patient package enrollments</p>
    </div>
    <a href="{{ route('core2.medical-packages.enrollment.create') }}" class="bg-indigo-600 text-white px-7 py-4 rounded-2xl font-black text-xs uppercase flex items-center gap-3 shadow-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Record
    </a>
</div>
<div class="bg-white rounded-[40px] border border-slate-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <th class="px-8 py-6">Package Identifier</th>
                    <th class="px-8 py-6">Description</th>
                    <th class="px-8 py-6">Price List Node</th>
                    <th class="px-8 py-6">Included Services</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 text-xs font-black text-slate-900">{{ $r->package_identifier }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-700">{{ Str::limit($r->package_description, 40) ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->price_list_node ?? '—' }}</td>
                    <td class="px-8 py-5 text-xs font-semibold text-slate-500">{{ $r->included_services_state ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-20 text-center text-slate-300 font-bold italic">No enrollment records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($records->hasPages())
    <div class="mt-6">{{ $records->links() }}</div>
@endif
@endsection
