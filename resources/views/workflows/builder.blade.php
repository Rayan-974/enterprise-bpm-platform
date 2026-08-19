@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-[1600px] mx-auto page-fade-up">
    <!-- Header with BPMN Export/Import & Versioning Actions -->
    <div class="bg-white/90 backdrop-blur-md p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shiny-card">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <span class="badge-sky text-xs font-black uppercase">Interactive Canvas</span>
                @if($workflow)
                    <span class="badge-purple text-xs font-black">Version V{{ $workflow->version }}</span>
                @endif
            </div>
            <h1 class="text-3xl font-black gradient-text tracking-tight">
                {{ $workflow ? 'Workflow Canvas: ' . $workflow->name : 'Interactive Drag & Drop Workflow Builder' }}
            </h1>
            <p class="text-sm font-bold text-slate-500 mt-1">Drag step nodes to add or re-order steps on the canvas. Click "Save & Deploy Canvas Workflow" to save your sequence.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($workflow)
                <a href="{{ route('workflows.exportBpmn', $workflow->id) }}" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-black px-6 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition shadow-lg hover:scale-105">
                    Export BPMN 2.0 XML
                </a>
                <form method="POST" action="{{ route('workflows.createVersion', $workflow->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-6 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition shadow-lg hover:scale-105">
                        Create New Version (V{{ $workflow->version + 1 }})
                    </button>
                </form>
            @endif

            <!-- Import BPMN Trigger Modal -->
            <button onclick="document.getElementById('import-bpmn-modal').classList.remove('hidden')" class="shine-sweep bg-slate-800 hover:bg-slate-900 text-white font-black px-6 py-3.5 rounded-2xl text-xs uppercase tracking-wider transition shadow-lg hover:scale-105">
                Import BPMN 2.0 XML
            </button>
        </div>
    </div>

    <!-- Process Optimization Suggestions Drawer -->
    @if(!empty($aiSuggestions))
        <div class="bg-gradient-to-r from-purpleSecondary via-purpleHover to-purpleSecondary p-7 rounded-3xl shadow-2xl text-white border border-skyPrimary/40 shiny-card">
            <h2 class="text-lg font-black tracking-tight flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-skyPrimary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Smart Process Optimization Recommendations
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($aiSuggestions as $s)
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/20">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-sm text-skyPrimary">{{ $s['title'] }}</span>
                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full bg-white/20 tracking-wider">{{ $s['severity'] }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-200 mt-2 leading-relaxed">{{ $s['message'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Drag & Drop Interactive Canvas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Palette Tool Box -->
        <div class="bg-white/90 backdrop-blur-md rounded-3xl p-7 shadow-xl border border-slate-200/80 space-y-4">
            <h2 class="text-xs font-black text-purpleSecondary uppercase tracking-widest border-b border-slate-100 pb-3">Available Step Nodes</h2>
            
            <div id="node-palette" class="space-y-4">
                <div draggable="true" ondragstart="dragStart(event, 'approval')" class="p-4 rounded-2xl bg-creamBase border-2 border-dashed border-purpleSecondary/40 cursor-grab hover:border-purpleSecondary transition shiny-card">
                    <div class="flex items-center space-x-3">
                        <span class="w-9 h-9 rounded-xl bg-purpleSecondary text-white font-black flex items-center justify-center text-sm shadow-md">S</span>
                        <div>
                            <h3 class="font-black text-slate-900 text-sm">Sequential Approval Step</h3>
                            <p class="text-[11px] font-bold text-slate-500">Requires sign-off before advancing</p>
                        </div>
                    </div>
                </div>

                <div draggable="true" ondragstart="dragStart(event, 'parallel')" class="p-4 rounded-2xl bg-creamBase border-2 border-dashed border-skyPrimary/70 cursor-grab hover:border-skyPrimary transition shiny-card">
                    <div class="flex items-center space-x-3">
                        <span class="w-9 h-9 rounded-xl bg-skyPrimary text-purpleSecondary font-black flex items-center justify-center text-sm shadow-md">P</span>
                        <div>
                            <h3 class="font-black text-slate-900 text-sm">Parallel Approval Step</h3>
                            <p class="text-[11px] font-bold text-slate-500">Concurrent multi-party review</p>
                        </div>
                    </div>
                </div>

                <div draggable="true" ondragstart="dragStart(event, 'decision')" class="p-4 rounded-2xl bg-creamBase border-2 border-dashed border-amber-400 cursor-grab hover:border-amber-500 transition shiny-card">
                    <div class="flex items-center space-x-3">
                        <span class="w-9 h-9 rounded-xl bg-amber-500 text-white font-black flex items-center justify-center text-sm shadow-md">D</span>
                        <div>
                            <h3 class="font-black text-slate-900 text-sm">Decision Node Rule</h3>
                            <p class="text-[11px] font-bold text-slate-500">Evaluates amount/conditions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Drop Zone -->
        <div class="lg:col-span-2 bg-white/90 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-lg font-black gradient-text">Active Process Sequence Canvas</h2>
                <button type="button" onclick="addStepCard()" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition shadow-lg">
                    + Add Step Node
                </button>
            </div>

            <form method="POST" action="{{ route('workflows.storeDesigner') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="workflow_id" value="{{ $workflow ? $workflow->id : '' }}">
                <input type="hidden" name="name" value="{{ $workflow ? $workflow->name : 'Custom Visual Workflow' }}">
                <input type="hidden" name="code" value="{{ $workflow ? $workflow->code : 'WF-VISUAL-' . rand(100,999) }}">
                <input type="hidden" name="category" value="{{ $workflow ? $workflow->category : 'general' }}">
                <input type="hidden" name="sla_hours" value="{{ $workflow ? $workflow->sla_hours : 48 }}">
                <input type="hidden" name="form_title" value="{{ $workflow && $workflow->activeFormTemplate ? $workflow->activeFormTemplate->title : 'Request Form' }}">

                @if($workflow && $workflow->activeFormTemplate && $workflow->activeFormTemplate->fields->count() > 0)
                    @foreach($workflow->activeFormTemplate->fields as $fIndex => $field)
                        <input type="hidden" name="fields[{{ $fIndex }}][label]" value="{{ $field->label }}">
                        <input type="hidden" name="fields[{{ $fIndex }}][field_name]" value="{{ $field->field_name }}">
                        <input type="hidden" name="fields[{{ $fIndex }}][field_type]" value="{{ $field->field_type }}">
                        <input type="hidden" name="fields[{{ $fIndex }}][is_required]" value="{{ $field->is_required ? '1' : '0' }}">
                        @if($field->options)
                            <input type="hidden" name="fields[{{ $fIndex }}][options]" value="{{ is_array($field->options) ? implode(',', $field->options) : $field->options }}">
                        @endif
                    @endforeach
                @else
                    <input type="hidden" name="fields[0][label]" value="Request Details">
                    <input type="hidden" name="fields[0][field_name]" value="description">
                    <input type="hidden" name="fields[0][field_type]" value="text">
                @endif

                <!-- Drop Target Canvas Container -->
                <div id="canvas-dropzone" ondragover="allowDrop(event)" ondrop="drop(event)" class="min-h-[300px] p-6 rounded-3xl bg-creamBase/60 border-2 border-dashed border-slate-300 space-y-4">
                    @if($workflow && $workflow->steps->count() > 0)
                        @foreach($workflow->steps as $index => $step)
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-md flex items-center justify-between gap-4 draggable-step shiny-card cursor-move" draggable="true">
                                <div class="flex items-center space-x-4">
                                    <span class="step-badge w-9 h-9 rounded-xl bg-purpleSecondary text-white font-black flex items-center justify-center text-sm shadow-md">{{ $index + 1 }}</span>
                                    <div>
                                        <input type="text" name="steps[{{ $index }}][name]" value="{{ $step->name }}" class="step-name-input font-black text-slate-900 text-sm bg-transparent border-b border-slate-300 focus:border-skyPrimary focus:outline-none">
                                        <p class="text-xs font-bold text-slate-500 mt-1">Type: {{ $step->type }} | Assignee: {{ $step->assignee_type }} | SLA: {{ $step->sla_hours }}h</p>
                                        <input type="hidden" name="steps[{{ $index }}][type]" value="{{ $step->type }}" class="step-type-input">
                                        <input type="hidden" name="steps[{{ $index }}][assignee_type]" value="{{ $step->assignee_type }}" class="step-assignee-input">
                                        <input type="hidden" name="steps[{{ $index }}][sla_hours]" value="{{ $step->sla_hours }}" class="step-sla-input">
                                    </div>
                                </div>
                                <button type="button" onclick="removeStepCard(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs">Remove</button>
                            </div>
                        @endforeach
                    @else
                        <div id="empty-canvas-placeholder" class="text-center py-12 text-slate-400 font-bold text-sm">
                            Drag step nodes from the left palette or click "+ Add Step Node" to start visual workflow assembly.
                        </div>
                    @endif
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-black px-9 py-4 rounded-2xl text-sm uppercase tracking-wider transition shadow-xl hover:scale-105">
                        Save & Deploy Canvas Workflow &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import BPMN 2.0 XML Modal -->
<div id="import-bpmn-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-6 hidden">
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-slate-200 shiny-card">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <h3 class="text-xl font-black text-purpleSecondary">Import BPMN 2.0 XML File</h3>
            <button onclick="document.getElementById('import-bpmn-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-black">&times;</button>
        </div>

        <form method="POST" action="{{ route('workflows.importBpmn') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-700 uppercase tracking-widest mb-2">Select BPMN 2.0 XML Document *</label>
                <input type="file" name="bpmn_file" accept=".xml,.bpmn" required class="w-full bg-creamBase border border-slate-300 rounded-2xl p-3.5 text-xs font-bold">
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('import-bpmn-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-black text-slate-600">Cancel</button>
                <button type="submit" class="shine-sweep bg-purpleSecondary hover:bg-purpleHover text-white font-black px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow">Import Process</button>
            </div>
        </form>
    </div>
</div>

<script>
function allowDrop(ev) { ev.preventDefault(); }
function dragStart(ev, type) { ev.dataTransfer.setData("type", type); }

function drop(ev) {
    ev.preventDefault();
    const type = ev.dataTransfer.getData("type");
    if (type) {
        addStepCard(type);
    }
}

let draggedCard = null;

function updateStepIndices() {
    const container = document.getElementById('canvas-dropzone');
    const placeholder = document.getElementById('empty-canvas-placeholder');
    const cards = container.querySelectorAll('.draggable-step');

    if (cards.length > 0 && placeholder) {
        placeholder.style.display = 'none';
    } else if (cards.length === 0 && placeholder) {
        placeholder.style.display = 'block';
    }

    cards.forEach((card, idx) => {
        const badge = card.querySelector('.step-badge');
        if (badge) badge.textContent = idx + 1;

        const nameInput = card.querySelector('.step-name-input');
        if (nameInput) nameInput.name = `steps[${idx}][name]`;

        const typeInput = card.querySelector('.step-type-input');
        if (typeInput) typeInput.name = `steps[${idx}][type]`;

        const assigneeInput = card.querySelector('.step-assignee-input');
        if (assigneeInput) assigneeInput.name = `steps[${idx}][assignee_type]`;

        const slaInput = card.querySelector('.step-sla-input');
        if (slaInput) slaInput.name = `steps[${idx}][sla_hours]`;
    });
}

function bindCardDragEvents(card) {
    card.addEventListener('dragstart', function(e) {
        draggedCard = this;
        e.dataTransfer.setData("text/plain", "reorder");
        setTimeout(() => this.classList.add('opacity-40'), 0);
    });

    card.addEventListener('dragend', function(e) {
        this.classList.remove('opacity-40');
        draggedCard = null;
        updateStepIndices();
    });

    card.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (draggedCard && draggedCard !== this) {
            const container = document.getElementById('canvas-dropzone');
            const rect = this.getBoundingClientRect();
            const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
            container.insertBefore(draggedCard, next ? this.nextSibling : this);
        }
    });
}

function addStepCard(type = 'approval') {
    const container = document.getElementById('canvas-dropzone');
    const placeholder = document.getElementById('empty-canvas-placeholder');
    if (placeholder) placeholder.style.display = 'none';

    const count = container.querySelectorAll('.draggable-step').length + 1;
    const card = document.createElement('div');
    card.className = 'p-5 rounded-2xl bg-white border border-slate-200 shadow-md flex items-center justify-between gap-4 draggable-step shiny-card cursor-move page-fade-up';
    card.setAttribute('draggable', 'true');
    card.innerHTML = `
        <div class="flex items-center space-x-4">
            <span class="step-badge w-9 h-9 rounded-xl bg-purpleSecondary text-white font-black flex items-center justify-center text-sm shadow-md">${count}</span>
            <div>
                <input type="text" name="steps[${count-1}][name]" value="Step ${count} (${type.toUpperCase()})" class="step-name-input font-black text-slate-900 text-sm bg-transparent border-b border-slate-300 focus:border-skyPrimary focus:outline-none">
                <p class="text-xs font-bold text-slate-500 mt-1">Type: ${type} | Assignee: manager | SLA: 24h</p>
                <input type="hidden" name="steps[${count-1}][type]" value="${type}" class="step-type-input">
                <input type="hidden" name="steps[${count-1}][assignee_type]" value="manager" class="step-assignee-input">
                <input type="hidden" name="steps[${count-1}][sla_hours]" value="24" class="step-sla-input">
            </div>
        </div>
        <button type="button" onclick="removeStepCard(this)" class="text-rose-500 hover:text-rose-700 font-black text-xs">Remove</button>
    `;

    container.appendChild(card);
    bindCardDragEvents(card);
    updateStepIndices();
}

function removeStepCard(btn) {
    const card = btn.closest('.draggable-step');
    if (card) {
        card.remove();
        updateStepIndices();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.draggable-step').forEach(card => bindCardDragEvents(card));
    updateStepIndices();
});
</script>
@endsection
