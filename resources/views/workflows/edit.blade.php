@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 page-fade-up">
    <!-- Edit Header -->
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-8">
            <div>
                <span class="badge-sky text-xs font-black uppercase tracking-widest mb-3 inline-block">Catalog Administration</span>
                <h1 class="text-3xl font-black gradient-text tracking-tight">Edit Workflow: {{ $workflow->name }}</h1>
                <p class="text-sm font-bold text-slate-500 mt-1.5">Update process settings, SLA threshold, department ownership, and active state.</p>
            </div>
            <a href="{{ route('workflows.index') }}" class="text-xs font-black text-slate-500 hover:text-purpleSecondary transition">&larr; Cancel & Back</a>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-900 text-xs font-bold border border-rose-300 space-y-1">
                @foreach($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('workflows.update', $workflow->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Workflow Name *</label>
                    <input type="text" name="name" value="{{ old('name', $workflow->name) }}" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Unique Process Code *</label>
                    <input type="text" name="code" value="{{ old('code', $workflow->code) }}" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Category *</label>
                    <select name="category" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                        <option value="procurement" {{ old('category', $workflow->category) == 'procurement' ? 'selected' : '' }}>Procurement</option>
                        <option value="hr" {{ old('category', $workflow->category) == 'hr' ? 'selected' : '' }}>Human Resources</option>
                        <option value="finance" {{ old('category', $workflow->category) == 'finance' ? 'selected' : '' }}>Finance</option>
                        <option value="legal" {{ old('category', $workflow->category) == 'legal' ? 'selected' : '' }}>Legal & Compliance</option>
                        <option value="it" {{ old('category', $workflow->category) == 'it' ? 'selected' : '' }}>IT Operations</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Department Ownership</label>
                    <select name="department_id" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                        <option value="">-- Global / All Departments --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $workflow->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Process Description</label>
                <textarea name="description" rows="3" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">{{ old('description', $workflow->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">SLA Duration (Hours) *</label>
                    <input type="number" name="sla_hours" value="{{ old('sla_hours', $workflow->sla_hours) }}" min="1" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Catalog Status *</label>
                    <select name="is_active" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary focus:border-skyPrimary transition shadow-inner">
                        <option value="1" {{ old('is_active', $workflow->is_active) ? 'selected' : '' }}>Active (Available in Catalog)</option>
                        <option value="0" {{ !old('is_active', $workflow->is_active) ? 'selected' : '' }}>Inactive / Archived</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('workflows.builder', $workflow->id) }}" class="text-xs font-bold text-purpleSecondary hover:underline">
                    &rarr; Edit Drag & Drop Steps & Dynamic Form
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('workflows.index') }}" class="px-6 py-3 rounded-2xl border border-slate-300 text-xs font-black text-slate-600 hover:bg-creamBase transition">Cancel</a>
                    <button type="submit" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-black px-8 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition shadow-lg hover:scale-105">
                        Save Changes &rarr;
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
