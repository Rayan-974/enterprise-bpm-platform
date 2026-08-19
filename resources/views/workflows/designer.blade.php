@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 page-fade-up">
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 shiny-card">
        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-8">
            <div>
                <span class="badge-sky text-xs font-black uppercase tracking-widest mb-2 inline-block">Visual Designer</span>
                <h1 class="text-3xl font-black gradient-text tracking-tight">Workflow Designer & Dynamic Form Builder</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Configure custom approval chains, role assignment rules, dynamic input fields, and SLA thresholds.</p>
            </div>
            <a href="{{ route('workflows.index') }}" class="text-xs font-black text-slate-500 hover:text-purpleSecondary transition">&larr; Back to Catalog</a>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-900 text-xs font-bold border border-rose-300 space-y-1">
                @foreach($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('workflows.storeDesigner') }}" class="space-y-8">
            @csrf

            <!-- 1. Basic Process Configuration -->
            <div class="space-y-4">
                <h2 class="text-sm font-black text-purpleSecondary uppercase tracking-widest border-b border-slate-100 pb-3 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-purpleSecondary text-white font-black text-xs flex items-center justify-center">1</span>
                    Process Basic Configuration
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Process Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Employee Travel & Expense Request" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Process Code (Unique) *</label>
                        <input type="text" name="code" required placeholder="e.g. HR-TRAVEL-01" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary uppercase transition shadow-inner">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Category *</label>
                        <select name="category" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner">
                            <option value="payments">Payments, Billing & Claims</option>
                            <option value="procurement">Procurement & Purchasing</option>
                            <option value="hr">Human Resources & Onboarding</option>
                            <option value="finance">Finance & Accounting</option>
                            <option value="legal">Legal, Contracts & Compliance</option>
                            <option value="it">IT & Cyber Security Operations</option>
                            <option value="sales">Sales, Marketing & Commercial</option>
                            <option value="customer_service">Customer Service & Support</option>
                            <option value="general">General Corporate & Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Department</label>
                        <select name="department_id" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner">
                            <option value="">-- All / Global --</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Overall SLA (Hours) *</label>
                        <input type="number" name="sla_hours" value="48" min="1" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Process Description</label>
                    <textarea name="description" rows="2" class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner" placeholder="Describe the purpose and scope of this workflow..."></textarea>
                </div>
            </div>

            <!-- 2. Approval Chain Steps Builder -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-black text-purpleSecondary uppercase tracking-widest flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-purpleSecondary text-white font-black text-xs flex items-center justify-center">2</span>
                        Sequential & Parallel Approval Chain
                    </h2>
                    <button type="button" onclick="addDesignerStep()" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-black px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition shadow">
                        + Add Approval Step
                    </button>
                </div>

                <div id="steps-container" class="space-y-3">
                    <div class="step-row p-5 rounded-2xl border border-slate-200/80 bg-creamBase/60 grid grid-cols-1 md:grid-cols-4 gap-3 items-center shiny-card">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Step 1 Name</label>
                            <input type="text" name="steps[0][name]" value="Direct Manager Review" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Step Type</label>
                            <select name="steps[0][type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                                <option value="approval">Sequential Approval</option>
                                <option value="parallel">Parallel Approval</option>
                                <option value="decision">Decision Node</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Assignee Rule</label>
                            <select name="steps[0][assignee_type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                                <option value="manager">Requester Direct Manager</option>
                                <option value="department_head">Department Head</option>
                                <option value="role">Role (e.g. Manager / Admin)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">SLA (Hours)</label>
                            <input type="number" name="steps[0][sla_hours]" value="24" min="1" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Dynamic Form Builder -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-black text-purpleSecondary uppercase tracking-widest flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-purpleSecondary text-white font-black text-xs flex items-center justify-center">3</span>
                        Dynamic Form Builder
                    </h2>
                    <button type="button" onclick="addDesignerField()" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition shadow">
                        + Add Custom Form Field
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-widest mb-1.5">Form Title *</label>
                    <input type="text" name="form_title" value="Process Request Details" required class="w-full bg-creamBase/80 border border-slate-300 rounded-2xl px-5 py-3 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-skyPrimary transition shadow-inner">
                </div>

                <div id="fields-container" class="space-y-3">
                    <!-- Default Field 1: Request Title -->
                    <div class="field-row p-5 rounded-2xl border border-slate-200/80 bg-creamBase/60 grid grid-cols-1 md:grid-cols-4 gap-3 items-center shiny-card">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Label *</label>
                            <input type="text" name="fields[0][label]" value="Request Title" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Key *</label>
                            <input type="text" name="fields[0][field_name]" value="title" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Input Type</label>
                            <select name="fields[0][field_type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                                <option value="text">Text Input</option>
                                <option value="number">Number</option>
                                <option value="textarea">Textarea</option>
                                <option value="dropdown">Dropdown Select</option>
                                <option value="date">Date Picker</option>
                                <option value="file">File / Document Attachment Upload</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                                <input type="checkbox" name="fields[0][is_required]" value="1" checked class="rounded border-slate-300 text-skyPrimary">
                                <span>Required</span>
                            </label>
                            <button type="button" onclick="removeDesignerRow(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs">Remove</button>
                        </div>
                    </div>

                    <!-- Default Field 2: Justification / Details -->
                    <div class="field-row p-5 rounded-2xl border border-slate-200/80 bg-creamBase/60 grid grid-cols-1 md:grid-cols-4 gap-3 items-center shiny-card">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Label *</label>
                            <input type="text" name="fields[1][label]" value="Details & Justification" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Key *</label>
                            <input type="text" name="fields[1][field_name]" value="description" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Input Type</label>
                            <select name="fields[1][field_type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                                <option value="textarea" selected>Textarea</option>
                                <option value="text">Text Input</option>
                                <option value="number">Number</option>
                                <option value="dropdown">Dropdown Select</option>
                                <option value="date">Date Picker</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                                <input type="checkbox" name="fields[1][is_required]" value="1" checked class="rounded border-slate-300 text-skyPrimary">
                                <span>Required</span>
                            </label>
                            <button type="button" onclick="removeDesignerRow(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-9 py-4 rounded-2xl text-sm uppercase tracking-wider transition shadow-xl hover:scale-105">
                    Publish Workflow & Form &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addDesignerStep() {
    const container = document.getElementById('steps-container');
    const idx = container.querySelectorAll('.step-row').length;
    const row = document.createElement('div');
    row.className = 'step-row p-5 rounded-2xl border border-slate-200/80 bg-creamBase/60 grid grid-cols-1 md:grid-cols-4 gap-3 items-center shiny-card page-fade-up';
    row.innerHTML = `
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Step ${idx + 1} Name</label>
            <input type="text" name="steps[${idx}][name]" value="Step ${idx + 1} Sign-off" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Step Type</label>
            <select name="steps[${idx}][type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                <option value="approval">Sequential Approval</option>
                <option value="parallel">Parallel Approval</option>
                <option value="decision">Decision Node</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Assignee Rule</label>
            <select name="steps[${idx}][assignee_type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                <option value="manager">Requester Direct Manager</option>
                <option value="department_head">Department Head</option>
                <option value="role">Role (e.g. Manager / Admin)</option>
            </select>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex-1 mr-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">SLA (Hours)</label>
                <input type="number" name="steps[${idx}][sla_hours]" value="24" min="1" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
            </div>
            <button type="button" onclick="removeDesignerRow(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs mt-4">Remove</button>
        </div>
    `;
    container.appendChild(row);
}

function addDesignerField() {
    const container = document.getElementById('fields-container');
    const idx = container.querySelectorAll('.field-row').length;
    const row = document.createElement('div');
    row.className = 'field-row p-5 rounded-2xl border border-slate-200/80 bg-creamBase/60 grid grid-cols-1 md:grid-cols-4 gap-3 items-center shiny-card page-fade-up';
    row.innerHTML = `
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Label *</label>
            <input type="text" name="fields[${idx}][label]" placeholder="e.g. Priority / Vendor / Date" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Field Key *</label>
            <input type="text" name="fields[${idx}][field_name]" placeholder="e.g. custom_field_${idx + 1}" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
        </div>
        <div>
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Input Type</label>
            <select name="fields[\${idx}][field_type]" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-800">
                <option value="text">Text Input</option>
                <option value="number">Number</option>
                <option value="textarea">Textarea</option>
                <option value="dropdown">Dropdown Select</option>
                <option value="date">Date Picker</option>
                <option value="file">File / Document Attachment Upload</option>
            </select>
        </div>
        <div class="flex items-center justify-between pt-2">
            <label class="flex items-center space-x-2 text-xs font-bold text-slate-700">
                <input type="checkbox" name="fields[${idx}][is_required]" value="1" checked class="rounded border-slate-300 text-skyPrimary">
                <span>Required</span>
            </label>
            <button type="button" onclick="removeDesignerRow(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs">Remove</button>
        </div>
    `;
    container.appendChild(row);
}

function removeDesignerRow(btn) {
    const row = btn.closest('.step-row, .field-row');
    if (row) row.remove();
}
</script>
@endsection
