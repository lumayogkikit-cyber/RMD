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

    <div class="glass-panel p-6 rounded-3xl border border-slate-800 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-4">Official Price Matrix</h2>

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

        <div class="overflow-x-auto border border-slate-800 rounded-2xl">
            <table class="w-full text-left text-xs text-slate-200">
                <thead class="bg-slate-900/90 uppercase font-semibold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">SIZES / BRACKET</th>
                        <th class="px-4 py-3">CATEGORY</th>
                        <th class="px-4 py-3 text-right">PRICE (₱/M³)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($priceMatrices as $pm)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-slate-300">
                                @if($pm->category === 'SAWMILL' || ($pm->dia_min == 0 && $pm->dia_max == 0))
                                    SM
                                @elseif($pm->dia_max >= 999)
                                    {{ $pm->dia_min }}-UP
                                @else
                                    {{ $pm->dia_min }}-{{ $pm->dia_max }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-white uppercase">{{ $pm->category }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-400">₱ {{ number_format($pm->price_per_cu_m, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-slate-500 italic">No price specs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="glass-panel p-5 rounded-2xl border border-slate-800 bg-slate-950/80">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-2">Structure</h3>
                <p class="text-xs text-slate-400">Prices are stored by category, length, diameter bracket, and per-cubic-meter rate.</p>
            </div>
            <div class="glass-panel p-5 rounded-2xl border border-slate-800 bg-slate-950/80">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wide mb-2">Note</h3>
                <p class="text-xs text-slate-400">This is a read-only view for now; editing should be handled through the Super Admin price matrix form.</p>
            </div>
        </div>
    </div>
</div>
@endsection
