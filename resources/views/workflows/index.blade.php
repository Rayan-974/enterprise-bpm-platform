@extends('layouts.app')

@section('content')
<div class="space-y-6 page-fade-up">
    <!-- Top Header Row -->
    <div class="bg-white/90 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-6 shiny-card">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold gradient-text tracking-tight">Enterprise Workflows Catalog</h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1.5">Select a workflow process to initiate or design a new custom process.</p>
        </div>
        <a href="{{ route('workflows.create') }}" class="shine-sweep bg-skyPrimary hover:bg-skyHover text-purpleSecondary font-bold px-6 py-3.5 rounded-2xl text-xs sm:text-sm transition shadow-lg hover:scale-105 active:scale-95 self-start md:self-center whitespace-nowrap uppercase tracking-wider min-h-[44px] flex items-center justify-center">
            + Workflow Designer & Form Builder
        </a>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex space-x-3 border-b border-slate-200 overflow-x-auto pb-3">
        <a href="{{ route('workflows.index') }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ !$category ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            All Categories
        </a>
        <a href="{{ route('workflows.index', ['category' => 'payments']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'payments' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Payments & Claims
        </a>
        <a href="{{ route('workflows.index', ['category' => 'procurement']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'procurement' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Procurement
        </a>
        <a href="{{ route('workflows.index', ['category' => 'hr']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'hr' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Human Resources
        </a>
        <a href="{{ route('workflows.index', ['category' => 'finance']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'finance' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Finance
        </a>
        <a href="{{ route('workflows.index', ['category' => 'legal']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'legal' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            Legal & Compliance
        </a>
        <a href="{{ route('workflows.index', ['category' => 'it']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'it' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            IT Operations
        </a>
        <a href="{{ route('workflows.index', ['category' => 'general']) }}" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition uppercase tracking-wider whitespace-nowrap {{ $category === 'general' ? 'bg-purpleSecondary text-white shadow-md font-bold' : 'bg-white text-slate-700 hover:bg-creamBase' }}">
            General Corporate
        </a>
    </div>

    <!-- Workflows Grid -->
    @if($workflows->isEmpty())
        <div class="p-12 text-center bg-white/90 backdrop-blur-md rounded-3xl border border-slate-200/80 shadow-xl">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-purpleSecondary/10 text-purpleSecondary mb-4 shadow-sm text-2xl">
                📂
            </div>
            <h3 class="text-lg font-extrabold text-purpleSecondary">No Workflows Available</h3>
            <p class="text-xs font-semibold text-slate-500 mt-1 max-w-md mx-auto">No workflows found under category '<span class="font-extrabold text-slate-900 capitalize">{{ $category ?? 'All' }}</span>'.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($workflows as $wf)
                <div class="bg-white/90 backdrop-blur-md rounded-3xl p-6 sm:p-7 shadow-xl border border-slate-200/80 hover:border-skyPrimary transition-all duration-300 shiny-card flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="badge-sky text-xs font-bold uppercase tracking-widest">{{ $wf->category }}</span>
                            <span class="text-xs text-slate-500 font-semibold">SLA: {{ $wf->sla_hours }}h</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-purpleSecondary mb-2 tracking-tight">{{ $wf->name }}</h3>
                        <p class="text-xs font-medium text-slate-600 line-clamp-3 mb-6 leading-relaxed">{{ $wf->description }}</p>

                        <!-- Steps Timeline Preview -->
                        <div class="bg-creamBase/80 p-4 rounded-2xl mb-6 border border-slate-200/80">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Approval Chain ({{ $wf->steps->count() }} Steps)</div>
                            <div class="space-y-2">
                                @foreach($wf->steps as $s)
                                    <div class="flex items-center text-xs text-slate-800 font-semibold">
                                        <span class="w-5 h-5 rounded-full bg-skyPrimary text-purpleSecondary font-bold text-center leading-5 text-xs mr-2.5 shadow-sm">{{ $s->step_order }}</span>
                                        <span>{{ $s->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <a href="{{ route('workflows.show', $wf->id) }}" class="shine-sweep block w-full bg-purpleSecondary hover:bg-purpleHover text-white font-bold py-3.5 px-6 rounded-2xl text-xs uppercase tracking-wider text-center transition shadow-md hover:scale-105 min-h-[44px] flex items-center justify-center">
                            Initiate Request &rarr;
                        </a>

                        @if(auth()->check() && auth()->user()->hasRole(['Super Admin', 'Department Admin']))
                            <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                                <a href="{{ route('workflows.edit', $wf->id) }}" class="flex-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold py-2 px-3 rounded-xl text-xs text-center transition shadow-sm">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('workflows.destroy', $wf->id) }}" onsubmit="return confirm('Are you sure you want to delete workflow definition \'{{ $wf->name }}\' from the catalog?');" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold py-2 px-3 rounded-xl text-xs text-center transition shadow-sm">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
