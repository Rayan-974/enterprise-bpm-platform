@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-purpleSecondary tracking-tight">Workflow Designer & Form Builder</h1>
                <p class="text-xs text-slate-500 mt-1">Configure multi-level approval chains, conditional step logic, and dynamic form schemas.</p>
            </div>
            <a href="{{ route('workflows.index') }}" class="text-xs font-bold text-slate-500 hover:text-purpleSecondary">&larr; Back</a>
        </div>

        <form method="POST" action="{{ route('workflows.storeDesigner') }}" class="space-y-8">
            @csrf

            <!-- 1. Basic Metadata -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-purpleSecondary uppercase tracking-wider border-b border-slate-100 pb-2">1. Process Basic Configuration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Process Name *</label>
                        <input type="text" name="name" required placeholder="e.g. IT Equipment Purchasing" class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Process Code (Unique) *</label>
                        <input type="text" name="code" required placeholder="e.g. IT-PROC-01" class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category *</label>
                        <select name="category" required class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary">
                            <option value="procurement">Procurement</option>
                            <option value="hr">Human Resources</option>
                            <option value="finance">Finance</option>
                            <option value="legal">Legal & Compliance</option>
                            <option value="general">General Corporate</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Department</label>
                        <select name="department_id" class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary">
                            <option value="">-- All / Global --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Overall SLA (Hours) *</label>
                        <input type="number" name="sla_hours" value="48" required class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-skyPrimary" placeholder="Describe the purpose of this workflow..."></textarea>
                </div>
            </div>

            <!-- 2. Workflow Steps Chain Builder -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-sm font-bold text-purpleSecondary uppercase tracking-wider">2. Sequential & Parallel Approval Chain</h2>
                </div>

                <div id="steps-container" class="space-y-3">
                    <div class="p-4 rounded-xl border border-slate-200 bg-creamBase grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Step 1 Name</label>
                            <input type="text" name="steps[0][name]" value="Manager Approval" required class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Step Type</label>
                            <select name="steps[0][type]" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                                <option value="approval">Sequential Approval</option>
                                <option value="parallel">Parallel Approval</option>
                                <option value="decision">Decision Node</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Assignee Rule</label>
                            <select name="steps[0][assignee_type]" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                                <option value="manager">Requester Direct Manager</option>
                                <option value="department_head">Department Head</option>
                                <option value="role">Role (e.g. Manager / Admin)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">SLA (Hours)</label>
                            <input type="number" name="steps[0][sla_hours]" value="24" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Dynamic Form Builder -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-2">
                    <h2 class="text-sm font-bold text-purpleSecondary uppercase tracking-wider">3. Dynamic Form Builder</h2>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Form Title *</label>
                    <input type="text" name="form_title" value="Request Details Form" required class="w-full bg-creamBase border border-slate-300 rounded-xl px-4 py-2.5 text-sm">
                </div>

                <div id="fields-container" class="space-y-3">
                    <div class="p-4 rounded-xl border border-slate-200 bg-creamBase grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Field Label</label>
                            <input type="text" name="fields[0][label]" value="Total Amount (USD)" required class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Field Key / Name</label>
                            <input type="text" name="fields[0][field_name]" value="amount" required class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Input Type</label>
                            <select name="fields[0][field_type]" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs">
                                <option value="number">Number</option>
                                <option value="text">Text Input</option>
                                <option value="textarea">Textarea</option>
                                <option value="dropdown">Dropdown Select</option>
                                <option value="date">Date Picker</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2 pt-4">
                            <input type="checkbox" name="fields[0][is_required]" value="1" checked class="w-4 h-4 text-skyPrimary rounded">
                            <label class="text-xs font-bold text-slate-700">Required Field</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold px-8 py-3 rounded-xl text-sm transition shadow-lg">
                    Publish Workflow & Form &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
