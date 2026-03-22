@extends('layouts.core2.app')
@section('title', 'Restock Request')
@section('content')

{{-- Alpine.js wrapper for the notification --}}
<div x-data="{ showNotification: false }" class="max-w-2xl relative">
    
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-[-20px]"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed top-10 right-10 z-50 bg-emerald-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 font-black text-sm uppercase tracking-wider"
         style="display: none;">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        Restock Request Sent Successfully!
    </div>

    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">PHARMACY › LOGISTICS</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight uppercase">Restock Request</h2>
        <p class="text-slate-500 font-bold text-sm mt-1">Request additional supply from the main warehouse.</p>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        {{-- Form prevents actual submission to show the notification pop-up --}}
        <form @submit.prevent="showNotification = true; setTimeout(() => showNotification = false, 4000)" class="space-y-6">
            
            {{-- Drug Number (Matches the Table) --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Drug Number</label>
                <input type="text" name="drug_num" required class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. DRG-001">
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Requested Quantity --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Requested Quantity</label>
                    <input type="number" name="quantity" required min="1" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10" placeholder="e.g. 100">
                </div>
                
                {{-- Urgency Level --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Urgency Level</label>
                    <select class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10 cursor-pointer">
                        <option>Normal</option>
                        <option class="text-amber-600">Urgent</option>
                        <option class="text-red-600">Critical (Stock Out)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition uppercase tracking-widest">
                    Send Restock Request
                </button>
                <a href="{{ route('core2.pharmacy.drug-inventory.index') }}" class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition uppercase">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection