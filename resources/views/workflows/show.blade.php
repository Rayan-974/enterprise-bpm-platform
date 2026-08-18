@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 page-fade-up">
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-8">
            <div>
                <span class="badge-sky text-xs font-black uppercase tracking-widest mb-3 inline-block">{{ $workflow->category }}</span>
                <h1 class="text-3xl font-black gradient-text tracking-tight">{{ $workflow->name }}</h1>
                <p class="text-sm font-bold text-slate-500 mt-1.5">{{ $workflow->description }}</p>
            </div>
            <a href="{{ route('workflows.index') }}" class="text-xs font-black text-slate-500 hover:text-purpleSecondary transition">&larr; Back to Catalog</a>
        </div>

        <!-- Dynamic Form -->
        <form method="POST" action="{{ route('workflows.submit', $workflow->id) }}" class="space-y-6">
            @csrf

            @if($workflow->activeFormTemplate && $workflow->activeFormTemplate->fields->count() > 0)
                @foreach($workflow->activeFormTemplate->fields as $field)
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">
                            {{ $field->label }} @if($field->is_required) <span class="text-rose-500">*</span> @endif
                        </label>

                        @if($field->field_type === 'textarea')
                            <textarea name="{{ $field->field_name }}" rows="3" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_name) }}</textarea>
                        
                        @elseif($field->field_type === 'dropdown')
                            <select name="{{ $field->field_name }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                                <option value="">-- Select {{ $field->label }} --</option>
                                @if(is_array($field->options))
                                    @foreach($field->options as $opt)
                                        <option value="{{ $opt }}" {{ old($field->field_name) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                @endif
                            </select>
                        
                        @elseif($field->field_type === 'date')
                            <input type="date" name="{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                        
                        @elseif($field->field_type === 'number')
                            <input type="number" step="any" name="{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                        
                        @else
                            <input type="text" name="{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                        @endif

                        @error($field->field_name)
                            <p class="text-xs text-rose-600 mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            @else
                <p class="text-sm text-slate-500 font-bold italic">No custom fields defined for this workflow template.</p>
            @endif

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('workflows.index') }}" class="px-6 py-3 rounded-2xl border border-slate-300 text-xs font-black text-slate-600 hover:bg-creamBase transition">Cancel</a>
                <button type="submit" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-9 py-4 rounded-2xl text-sm uppercase tracking-wider transition shadow-xl hover:scale-105">
                    Submit Request &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
