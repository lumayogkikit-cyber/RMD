@extends('layouts.app')

@section('title', 'Super Admin Control Panel - RMD Corp')

@section('content')
<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/30">
                    <i class="fa-solid fa-crown text-amber-400"></i> Master Override Panel
                </span>
                <span class="text-xs text-slate-400 font-mono">Logged in: {{ Auth::user()->name }} (Super Admin)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                <i class="fa-solid fa-sliders text-amber-400"></i> Super Admin Master Settings Panel
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('scaling.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-xl font-bold text-xs transition-all border border-amber-500/30 shadow-md flex items-center gap-2">
                <i class="fa-solid fa-gauge-high text-amber-400"></i> Scaler Dashboard
            </a>
            <a href="{{ route('admin.archive.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-rose-300 rounded-xl font-bold text-xs transition-all border border-rose-500/30 shadow-md flex items-center gap-2">
                <i class="fa-solid fa-box-archive text-rose-400"></i> Archive Center
            </a>
        </div>
    </div>

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Active Scale Sheets</p>
                <h3 class="text-2xl font-extrabold text-white mt-1">{{ number_format($completedLoads->total()) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
                <i class="fa-solid fa-file-invoice text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Pricing Specs Rows</p>
                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">{{ number_format($priceMatrices->count()) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                <i class="fa-solid fa-tags text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Registered Suppliers</p>
                <h3 class="text-2xl font-extrabold text-sky-400 mt-1">{{ number_format($suppliers->count()) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center border border-sky-500/30">
                <i class="fa-solid fa-truck-field text-lg"></i>
            </div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Staff Accounts</p>
                <h3 class="text-2xl font-extrabold text-amber-300 mt-1">{{ number_format($staffUsers->count()) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/30 text-amber-300 flex items-center justify-center border border-amber-500/40">
                <i class="fa-solid fa-users text-lg"></i>
            </div>
        </div>
    </div>

    <!-- PRIMARY MODULE 1: DYNAMIC LOG PRICE MATRIX MANAGEMENT -->
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 shadow-xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-800 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-tags text-amber-500"></i> Dynamic Log Price Matrix
                </h2>
                <p class="text-xs text-slate-400">Official buying prices for Peelable / F1(1.3/2.6)</p>
            </div>

            <button type="submit" form="price-matrix-form" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                Save Pricing
            </button>
        </div>

        <form id="price-matrix-form" method="POST" action="{{ route('admin.prices.update') }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="p-3">SIZES</th>
                            <th class="p-3 text-center">VOLUME</th>
                            <th class="p-3 text-right">PRICE (₱)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @php
                            $groupedPrices = $priceMatrices->groupBy('category');
                            $inputIndex = 0;
                        @endphp

                        @foreach($groupedPrices as $category => $items)
                        <tr class="bg-slate-900/40 font-sans font-bold text-amber-400">
                            <td colspan="3" class="p-2 text-xs uppercase tracking-widest">
                                {{ ucwords(strtolower($category)) }} <br> <span class="text-slate-400 font-normal">F1 (1.3 / 2.6)</span>
                            </td>
                        </tr>

                        @foreach($items as $item)
                        <tr class="hover:bg-slate-800/40">
                            <td class="p-3 font-semibold text-white">
                                @if($item->dia_min === 0 && $item->dia_max === 0)
                                    SM
                                @elseif($item->dia_max >= 999)
                                    {{ $item->dia_min }}-UP
                                @else
                                    {{ $item->dia_min }}-{{ $item->dia_max }}
                                @endif
                            </td>
                            <td class="p-3 text-center text-slate-500">-</td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <input type="hidden" name="prices[{{ $inputIndex }}][id]" value="{{ $item->id }}">
                                    <input type="number" step="0.01" min="0" name="prices[{{ $inputIndex }}][price]" value="{{ number_format($item->price_per_cu_m, 2, '.', '') }}" class="w-24 bg-slate-950 border border-slate-700 text-emerald-400 font-mono font-bold text-right text-xs rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                                    @php $inputIndex++; @endphp
                                    <span class="font-bold text-emerald-400">₱</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <!-- PRIMARY MODULE 2: HISTORIC SCALE SHEET MASTER OVERRIDES & DYNAMIC STATUS TOGGLE -->
    <div class="glass-panel p-6 rounded-2xl border border-slate-800 shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
            <div>
                <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-400"></i> Historic Scale Sheet Master Overrides
                </h2>
                <p class="text-xs text-slate-400">Interactively toggle sheet status (Draft vs Finalized) or delete erroneous scale sheet records.</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-800 rounded-xl">
            <table class="w-full text-left text-xs text-slate-200">
                <thead class="bg-slate-900/90 uppercase font-semibold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Scale Sheet / Invoice No</th>
                        <th class="px-4 py-3">Supplier Name</th>
                        <th class="px-4 py-3 text-center">Interactive Status Toggle</th>
                        <th class="px-4 py-3 text-right">Net Payable</th>
                        <th class="px-4 py-3 text-center">Master Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($completedLoads as $load)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-amber-400">
                                #{{ $load->scale_sheet_no }} <span class="text-slate-400 font-normal">({{ $load->invoice_no ?? 'N/A' }})</span>
                            </td>
                            <td class="px-4 py-3 font-semibold uppercase text-white">{{ $load->supplier->name ?? 'Unknown' }}</td>
                            
                            <!-- Interactive Status Dropdown Toggle -->
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('admin.scaling.status', $load->id) }}" class="inline-block">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="bg-slate-950 border text-xs font-bold rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer transition-all {{ $load->status === 'completed' ? 'text-emerald-400 border-emerald-500/40 bg-emerald-950/20' : 'text-amber-300 border-amber-500/40 bg-amber-950/20' }}">
                                        <option value="draft" {{ $load->status === 'draft' ? 'selected' : '' }}>
                                            🔓 Draft / Unlocked
                                        </option>
                                        <option value="completed" {{ $load->status === 'completed' ? 'selected' : '' }}>
                                            🔒 Finalized / Locked
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-400">₱ {{ number_format($load->net_payable, 2) }}</td>
                            <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                                <a href="{{ route('scaling.show', $load->id) }}" class="px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                    <i class="fa-solid fa-receipt"></i> Invoice
                                </a>

                                <form method="POST" action="{{ route('admin.scaling.destroy', $load->id) }}" onsubmit="return confirm('SUPER ADMIN OVERRIDE: Delete scale sheet #{{ $load->scale_sheet_no }} permanently?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">No scale sheets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($completedLoads->hasPages())
            <div class="mt-3">
                {{ $completedLoads->links() }}
            </div>
        @endif
    </div>

    <!-- SECONDARY MODULES: STAFF ACCOUNTS, SUPPLIER REGISTRY & AUDIT LOGS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- STAFF ACCOUNTS REGISTRY -->
        <div class="lg:col-span-4 glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
            <div class="border-b border-slate-800 pb-2">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-sky-400"></i> Staff Account Management
                </h3>
            </div>

            <!-- Create Staff Form -->
            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-2.5 bg-slate-900/60 p-3.5 rounded-xl border border-slate-800">
                @csrf
                <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Add Staff User</div>
                <div>
                    <input type="text" name="name" required placeholder="Full Name" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <input type="email" name="email" required placeholder="Email Address" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="password" name="password" required placeholder="Password" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-amber-500 outline-none">
                    <select name="role" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-amber-500 outline-none">
                        <option value="admin">Scaler Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition-all">
                    Create User
                </button>
            </form>

            <div class="divide-y divide-slate-800/80 border border-slate-800 rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                @foreach($staffUsers as $stUser)
                    <div class="p-2.5 bg-slate-900/40 flex items-center justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-white">{{ $stUser->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $stUser->email }}</div>
                        </div>
                        @if($stUser->id !== Auth::id())
                            <form method="POST" action="{{ route('admin.staff.toggle', $stUser->id) }}">
                                @csrf
                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold transition-all {{ $stUser->isActive() ? 'bg-rose-950 text-rose-300 border border-rose-500/30' : 'bg-emerald-950 text-emerald-300 border border-emerald-500/30' }}">
                                    {{ $stUser->isActive() ? 'Suspend' : 'Activate' }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SUPPLIER REGISTRY -->
        <div class="lg:col-span-4 glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
            <div class="border-b border-slate-800 pb-2">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-truck-moving text-emerald-400"></i> Supplier Registry
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-2.5 bg-slate-900/60 p-3.5 rounded-xl border border-slate-800">
                @csrf
                <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Add Supplier</div>
                <input type="text" name="name" required placeholder="Supplier Name" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                <input type="text" name="contact_no" placeholder="Contact No (optional)" class="w-full bg-slate-950 border border-slate-700 text-slate-100 text-xs rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition-all">
                    Add Supplier
                </button>
            </form>

            <div class="divide-y divide-slate-800/80 border border-slate-800 rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                @forelse($suppliers as $supplier)
                    <div class="p-2.5 bg-slate-900/40 flex items-center justify-between gap-2">
                        <div>
                            <div class="text-xs font-bold text-white uppercase">{{ $supplier->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $supplier->contact_no ?: 'No contact' }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier->id) }}" onsubmit="return confirm('Delete supplier?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 hover:bg-rose-500 text-white">
                                Delete
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-4 text-slate-500 text-xs text-center">No suppliers recorded.</div>
                @endforelse
            </div>
        </div>

        <!-- REAL-TIME AUDIT LOGS -->
        <div class="lg:col-span-4 glass-panel p-5 rounded-2xl border border-slate-800 shadow-xl space-y-4">
            <div class="border-b border-slate-800 pb-2">
                <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-amber-400"></i> Audit Log Trail
                </h3>
            </div>

            <div class="overflow-x-auto border border-slate-800 rounded-xl max-h-80 overflow-y-auto">
                <table class="w-full text-left text-xs text-slate-200">
                    <thead class="bg-slate-900 uppercase text-slate-400 font-semibold border-b border-slate-800 sticky top-0">
                        <tr>
                            <th class="px-2.5 py-2">Time</th>
                            <th class="px-2.5 py-2">User</th>
                            <th class="px-2.5 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-2.5 py-1.5 font-mono text-[11px] text-slate-400">{{ $log->created_at->format('M d H:i') }}</td>
                                <td class="px-2.5 py-1.5 font-bold text-white text-[11px]">{{ $log->user_name }}</td>
                                <td class="px-2.5 py-1.5 text-[11px] text-amber-300 font-semibold">{{ $log->action }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-slate-500 italic">No audit logs.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
