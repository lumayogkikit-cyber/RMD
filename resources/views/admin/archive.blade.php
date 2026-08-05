@extends('layouts.app')

@section('title', 'Archive Center - Super Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Archive Center</h1>
            <p class="text-sm text-slate-400">Manage archived Truck Loads and Suppliers. Restore or permanently delete records.</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-800 rounded-lg text-slate-200">Back to Dashboard</a>
        </div>
    </div>

    <div class="glass-panel p-4 rounded-xl border border-slate-800">
        <nav class="mb-4">
            <button id="tab-truckloads" class="px-4 py-2 bg-amber-500 text-black font-bold rounded-l">Archived Truck Loads</button>
            <button id="tab-suppliers" class="px-4 py-2 bg-slate-900 text-slate-300 rounded-r">Archived Suppliers</button>
        </nav>

        <div id="panel-truckloads">
            <div class="overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs text-slate-200">
                    <thead class="bg-slate-900 uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2">Scale Sheet</th>
                            <th class="px-3 py-2">Supplier</th>
                            <th class="px-3 py-2">Deleted At</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($archivedTruckLoads as $t)
                            <tr>
                                <td class="px-3 py-2 font-mono">#{{ $t->scale_sheet_no }} <span class="text-amber-400">({{ $t->invoice_no ?? 'N/A' }})</span></td>
                                <td class="px-3 py-2">{{ $t->supplier->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $t->deleted_at->format('M d, Y H:i') }}</td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="{{ route('admin.archive.truckloads.restore', $t->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[13px]">Restore</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.archive.truckloads.force_delete', $t->id) }}" class="inline-block ml-2" onsubmit="return confirm('Warning: This action is permanent and cannot be undone. Are you sure?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded text-[13px]">Force Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-400">No archived truck loads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $archivedTruckLoads->links() }}</div>
        </div>

        <div id="panel-suppliers" class="hidden">
            <div class="overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs text-slate-200">
                    <thead class="bg-slate-900 uppercase text-slate-400">
                        <tr>
                            <th class="px-3 py-2">Supplier</th>
                            <th class="px-3 py-2">Contact</th>
                            <th class="px-3 py-2">Deleted At</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($archivedSuppliers as $s)
                            <tr>
                                <td class="px-3 py-2 font-bold uppercase">{{ $s->name }}</td>
                                <td class="px-3 py-2">{{ $s->contact_no ?: '—' }}</td>
                                <td class="px-3 py-2">{{ $s->deleted_at->format('M d, Y H:i') }}</td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="{{ route('admin.archive.suppliers.restore', $s->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[13px]">Restore</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.archive.suppliers.force_delete', $s->id) }}" class="inline-block ml-2" onsubmit="return confirm('Warning: This action is permanent and cannot be undone. Are you sure?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white rounded text-[13px]">Force Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-400">No archived suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $archivedSuppliers->links() }}</div>
        </div>
    </div>
</div>

<script>
    document.getElementById('tab-truckloads').addEventListener('click', function () {
        document.getElementById('panel-truckloads').classList.remove('hidden');
        document.getElementById('panel-suppliers').classList.add('hidden');
        this.classList.add('bg-amber-500');
        document.getElementById('tab-suppliers').classList.remove('bg-amber-500');
    });

    document.getElementById('tab-suppliers').addEventListener('click', function () {
        document.getElementById('panel-suppliers').classList.remove('hidden');
        document.getElementById('panel-truckloads').classList.add('hidden');
        this.classList.add('bg-amber-500');
        document.getElementById('tab-truckloads').classList.remove('bg-amber-500');
    });
</script>

@endsection
