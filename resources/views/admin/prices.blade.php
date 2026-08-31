@extends('layouts.app')

@section('title', 'Price Matrix Management - RMD Corp')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Price Matrix Management</h1>
            <p class="text-slate-400 mt-1 text-sm">Manage official log pricing brackets and category rates in a dedicated admin interface.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-800 text-amber-300 text-xs font-semibold hover:bg-slate-700 transition-all">Back to Dashboard</a>
    </div>

    <!-- Category Management Section -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-4">Manage Categories</h2>

        <div class="mb-4 flex items-start justify-between gap-4">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="name" required placeholder="New Category e.g. Peelable / F1" class="bg-slate-900 border border-slate-700 text-slate-200 px-3 py-2 rounded-lg text-sm">
                <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-bold">+ Add New Category</button>
            </form>

            <div class="text-xs text-slate-400">Existing Categories:</div>
        </div>

        <div class="mb-4 grid gap-2">
            @foreach($categories as $cat)
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.categories.update', $cat->id) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name" value="{{ $cat->name }}" class="bg-slate-900 border border-slate-700 text-slate-200 px-3 py-2 rounded-lg text-sm w-64">
                        <button type="submit" class="px-3 py-2 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-lg text-sm font-bold">Save</button>
                    </form>
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('Delete category and related price rows?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-lg text-sm font-bold">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Unified Pricing Matrix Display Component -->
    @component('components.pricing-matrix-display', [
        'mode' => 'admin',
        'priceMatrices' => $priceMatrices,
        'categories' => $categories,
        'title' => 'Official Price Matrix'
    ])
    @endcomponent

    <!-- Info Cards -->
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 bg-slate-950/80">
            <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-2">Structure</h3>
            <p class="text-xs text-slate-400">Prices are stored by category, length, diameter bracket, and per-cubic-meter rate. This pricing matrix is used by scalers to calculate rates in real-time.</p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 bg-slate-950/80">
            <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-2">Real-Time Updates</h3>
            <p class="text-xs text-slate-400">Changes to prices are reflected immediately in all scaler views. The system fetches fresh rates on each page load and every 5 minutes automatically.</p>
        </div>
    </div>
</div>
@endsection
