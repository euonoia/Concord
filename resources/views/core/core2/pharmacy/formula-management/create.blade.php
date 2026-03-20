@extends('layouts.core2.app')
@section('title', 'Add Formula')

@section('content')

<div class="max-w-3xl">

    <div class="mb-8">
        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">PHARMACY › FORMULA MANAGEMENT</p>
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">New Formula Record</h2>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">

        <form action="{{ route('core2.pharmacy.formula-management.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Formula ID --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Formula ID</label>
                <input type="text" name="formula_id" value="{{ old('formula_id') }}" required
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10"
                    placeholder="e.g. FRM-001">

                @error('formula_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Formula Name --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Formula Name</label>
                <input type="text" name="formula_name" value="{{ old('formula_name') }}" required
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10"
                    placeholder="Example: Pediatric Paracetamol Syrup">

                @error('formula_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Drug Reference --}}
            <div class="space-y-2">
    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Drug ID</label>

    <select name="drug_id" class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10 appearance-none">
        
        <option value="" disabled {{ old('drug_id') ? '' : 'selected' }}>
            Select a Drug
        </option>

        @foreach([
            'DRG-001 - Propofol',
            'DRG-002 - Fentanyl',
            'DRG-003 - Ceftriaxone',
            'DRG-004 - Vancomycin',
            'DRG-005 - Epinephrine',
            'DRG-006 - Heparin',
            'DRG-007 - Ondansetron',
            'DRG-008 - Midazolam',
            'DRG-009 - Normal Saline',
            'DRG-010 - Dopamine',
            'DRG-011 - Insulin Regular',
            'DRG-012 - Ketamine',
            'DRG-013 - Enoxaparin',
            'DRG-014 - Pantoprazole (IV)',
            'DRG-015 - Norepinephrine',
            'DRG-016 - Lidocaine',
            'DRG-017 - Piperacillin/Tazo',
            'DRG-018 - Magnesium Sulfate',
            'DRG-019 - Morphine',
            'DRG-020 - Metronidazole'
        ] as $drug)

        <option value="{{ $drug }}" {{ old('drug_id') == $drug ? 'selected' : '' }}>
            {{ $drug }}
        </option>

        @endforeach

    </select>

    @error('drug_id')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

            {{-- Ingredients --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ingredients List</label>
                <textarea name="ingredients_list" rows="4"
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10"
                    placeholder="Example:
• Paracetamol Powder
• Syrup Base
• Flavoring">
{{ old('ingredients_list') }}</textarea>

                @error('ingredients_list')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dosage --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dosage</label>
                <input type="text" name="dosage" value="{{ old('dosage') }}"
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10"
                    placeholder="Example: 120mg / 5ml">
            </div>

            {{-- Preparation Method --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Preparation Method</label>
                <textarea name="preparation_method" rows="3"
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10"
                    placeholder="Instructions on how to prepare the formula...">{{ old('preparation_method') }}</textarea>
            </div>

            {{-- Status --}}
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</label>

                <select name="status"
                    class="w-full bg-slate-50 border-none rounded-2xl p-5 font-bold outline-none focus:ring-4 ring-indigo-500/10">
                    <option value="ACTIVE">Active</option>
                    <option value="INACTIVE">Inactive</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4 pt-2">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-5 rounded-3xl font-black text-sm shadow-xl hover:bg-indigo-700 transition">
                    Commit to Database
                </button>

                <a href="{{ route('core2.pharmacy.formula-management.index') }}"
                    class="px-8 py-5 rounded-3xl border border-slate-200 font-black text-sm text-slate-600 hover:bg-slate-50 transition">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection