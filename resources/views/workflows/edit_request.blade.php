@extends('layouts.app')

@section('content')
<div class="w-full space-y-6 page-fade-up">
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 shiny-card">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-5 mb-8 gap-4">
            <div>
                <span class="badge-sky text-xs font-black uppercase tracking-widest mb-3 inline-block">Update Request Details</span>
                <h1 class="text-3xl font-black gradient-text tracking-tight">Edit Request #{{ $instance->uuid }}</h1>
                <p class="text-sm font-bold text-slate-500 mt-1.5">Process: {{ $instance->definition->name }}</p>
            </div>
            <a href="{{ route('workflows.myRequests') }}" class="text-xs font-black text-slate-500 hover:text-purpleSecondary transition">&larr; Back to My Requests</a>
        </div>

        <!-- Dynamic Edit Form -->
        <form method="POST" action="{{ route('workflows.updateRequest', $instance->uuid) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @if($instance->definition->activeFormTemplate && $instance->definition->activeFormTemplate->fields->count() > 0)
                @foreach($instance->definition->activeFormTemplate->fields as $field)
                    @php
                        $val = $instance->payload[$field->field_name] ?? null;
                    @endphp
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">
                            {{ $field->label }} @if($field->is_required) <span class="text-rose-500">*</span> @endif
                        </label>

                        @if($field->field_type === 'textarea')
                            <textarea name="{{ $field->field_name }}" rows="3" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_name, is_string($val) ? $val : '') }}</textarea>

                        @elseif($field->field_type === 'dropdown')
                            <select name="{{ $field->field_name }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                                <option value="">-- Select {{ $field->label }} --</option>
                                @if(is_array($field->options))
                                    @foreach($field->options as $opt)
                                        <option value="{{ $opt }}" {{ old($field->field_name, $val) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                @endif
                            </select>

                        @elseif($field->field_type === 'file')
                            @if(is_array($val) && isset($val['url'], $val['name']))
                                <div class="mb-2 p-3 bg-sky-50 rounded-xl border border-sky-200 text-xs font-bold text-slate-700 flex items-center justify-between">
                                    <span>Current Attachment: 📎 {{ $val['name'] }} ({{ $val['size'] ?? '' }})</span>
                                    <a href="{{ $val['url'] }}" target="_blank" download class="text-purpleSecondary hover:underline font-black">Download &rarr;</a>
                                </div>
                            @endif
                            <input type="file" name="{{ $field->field_name }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-purpleSecondary file:text-white hover:file:bg-purpleHover">

                        @elseif($field->field_type === 'date')
                            <input type="date" name="{{ $field->field_name }}" value="{{ old($field->field_name, is_string($val) ? $val : '') }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>

                        @elseif($field->field_type === 'number')
                            <input type="number" step="any" name="{{ $field->field_name }}" value="{{ old($field->field_name, is_numeric($val) ? $val : '') }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>

                        @else
                            <input type="text" name="{{ $field->field_name }}" value="{{ old($field->field_name, is_string($val) ? $val : '') }}" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-4 text-base font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" {{ $field->is_required ? 'required' : '' }}>
                        @endif

                        @error($field->field_name)
                            <p class="text-xs text-rose-600 mt-1.5 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            @endif

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('workflows.myRequests') }}" class="px-6 py-3 rounded-2xl border border-slate-300 text-xs font-black text-slate-600 hover:bg-creamBase transition">Cancel</a>
                <button type="submit" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-black px-9 py-4 rounded-2xl text-sm uppercase tracking-wider transition shadow-xl hover:scale-105">
                    Save Request Updates &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
