@extends('layouts.app')

@section('title', 'Edit Scale Sheet #' . $sheet->scale_sheet_no . ' - RMD Corp')

@section('content')
@php
    // Accept multiple possible model variable names passed from controller
    if (!isset($truckLoad) && isset($sheet)) {
        $truckLoad = $sheet;
    } elseif (!isset($truckLoad) && isset($scaling)) {
        $truckLoad = $scaling;
    }
@endphp

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('scaling.index') }}" class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Back to Scale Sheets
            </a>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <span>Edit Scale Sheet</span>
                <span class="text-xs bg-emerald-500/20 text-emerald-300 font-semibold px-3 py-1 rounded-full border border-emerald-500/30">
                    #{{ $sheet->scale_sheet_no }}
                </span>
            </h1>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Invoice No.</span>
            <p class="font-mono text-xl font-bold text-amber-400">{{ $sheet->invoice_no }}</p>
        </div>
    </div>

    <form action="{{ route('scaling.update', ['scaling' => $sheet->id ?? request()->route('scaling')]) }}" method="POST" id="scaleEditForm" class="space-y-8">
        @csrf
        @method('PUT')

        @if(session('error'))
            <div class="rounded-2xl border border-rose-500 bg-rose-950/20 p-4 text-rose-200">
                <p class="font-semibold">Submission failed:</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-rose-500 bg-rose-950/20 p-4 text-rose-200">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="glass-panel p-6 rounded-2xl border border-slate-800 shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-truck-ramp-box text-amber-500"></i> Scale Sheet Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Supplier Name</label>
                    <input type="text" value="{{ $sheet->supplier->name ?? 'N/A' }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-amber-400 font-bold text-sm focus:outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Truck Plate No.</label>
                    <input type="text" value="{{ $sheet->truck_plate_no }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Scaled By</label>
                    <input type="text" value="{{ $sheet->scaled_by }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Date Scaled</label>
                    <input type="text" value="{{ \Carbon\Carbon::parse($sheet->date_scaled)->format('M d, Y') }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Date Unloaded</label>
                    <input type="text" value="{{ \Carbon\Carbon::parse($sheet->date_unload)->format('M d, Y') }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 text-sm focus:outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5 text-slate-400">Gross Amount</label>
                    <input type="text" id="grossAmountDisplay" value="₱ {{ number_format($sheet->gross_amount, 2) }}" readonly class="w-full bg-slate-900/80 border border-slate-700 rounded-xl px-4 py-3 text-emerald-300 text-sm focus:outline-none cursor-not-allowed font-mono">
                </div>
            </div>
        </div>

        <div class="glass-panel p-6 rounded-2xl border border-slate-800 shadow-xl space-y-6">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                <i class="fa-solid fa-file-invoice-dollar text-amber-500"></i> Deductions & Notes
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="drivers_assistance" class="block text-xs font-semibold text-slate-400 mb-1">Driver's Assistance (₱)</label>
                    <input type="number" step="0.01" min="0" name="drivers_assistance" id="drivers_assistance" value="{{ old('drivers_assistance', $sheet->drivers_assistance ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="expenses_deduction" class="block text-xs font-semibold text-slate-400 mb-1">Expenses Deduction (₱)</label>
                    <input type="number" step="0.01" min="0" name="expenses_deduction" id="expenses_deduction" value="{{ old('expenses_deduction', $sheet->expenses_deduction ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="travel_paper_deduction" class="block text-xs font-semibold text-slate-400 mb-1">Travel Paper / Permit (₱)</label>
                    <input type="number" step="0.01" min="0" name="travel_paper_deduction" id="travel_paper_deduction" value="{{ old('travel_paper_deduction', $sheet->travel_paper_deduction ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="trucking_deduction" class="block text-xs font-semibold text-slate-400 mb-1">Trucking Deduction (₱)</label>
                    <input type="number" step="0.01" min="0" name="trucking_deduction" id="trucking_deduction" value="{{ old('trucking_deduction', $sheet->trucking_deduction ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="cash_advance" class="block text-xs font-semibold text-slate-400 mb-1">Cash Advance (₱)</label>
                    <input type="number" step="0.01" min="0" name="cash_advance" id="cash_advance" value="{{ old('cash_advance', $sheet->cash_advance ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="other_deduction_label" class="block text-xs font-semibold text-slate-400 mb-1">Other Deduction Label</label>
                    <input type="text" name="other_deduction_label" id="other_deduction_label" value="{{ old('other_deduction_label', $sheet->other_deduction_label) }}" placeholder="e.g. Fuel / Inspection" class="w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
                <div>
                    <label for="other_deduction_amount" class="block text-xs font-semibold text-slate-400 mb-1">Other Deduction Amount (₱)</label>
                    <input type="number" step="0.01" min="0" name="other_deduction_amount" id="other_deduction_amount" value="{{ old('other_deduction_amount', $sheet->other_deduction_amount ?? '0.00') }}" class="deduction-input w-full bg-slate-900 border border-slate-700 text-slate-100 font-mono text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-amber-500 outline-none">
                </div>
            </div>

            <div class="pt-2">
                <label for="notes" class="block text-xs font-semibold text-slate-400 mb-1">Notes / Operational Remarks</label>
                <textarea name="notes" id="notes" rows="3" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none">{{ old('notes', $sheet->notes) }}</textarea>
            </div>
        </div>

        <div class="lg:col-span-5 glass-panel p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between space-y-6">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-calculator text-amber-400"></i> Summary
                </h2>
                <div class="space-y-3 mt-4 text-sm text-slate-300">
                        <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                        <span>Gross Amount</span>
                        <span class="font-mono font-bold text-white text-base" id="summaryGrossVal">₱ {{ number_format($sheet->gross_amount, 2) }}</span>
                    </div>
                        <div class="flex justify-between items-center text-rose-400">
                        <span>Less: Total Deductions</span>
                        <span class="font-mono font-bold text-lg" id="summaryDeductions">- ₱ {{ number_format($sheet->total_deductions, 2) }}</span>
                    </div>
                        <div class="flex justify-between items-center text-emerald-400">
                        <span>Add Driver's Assistance</span>
                        <span class="font-mono font-bold text-lg" id="summaryDriverAssistance">+ ₱ {{ number_format($sheet->drivers_assistance, 2) }}</span>
                    </div>
                        <div class="border-t border-slate-800 pt-3 flex justify-between items-center font-semibold text-white text-base">
                        <span>Net Amount Payable</span>
                        <span class="font-mono text-emerald-300" id="summaryNetPayable">₱ {{ number_format($sheet->net_payable, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 pt-4">
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-2xl px-6 py-3 text-sm transition-all">Save Changes</button>
                <a href="{{ route('scaling.show', ['scaling' => $sheet->id ?? request()->route('scaling')]) }}" class="text-center text-xs uppercase tracking-[0.3em] text-slate-400 hover:text-slate-200">Cancel and return to invoice</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function recalculateDeductions() {
        const grossAmount = parseFloat({{ $sheet->gross_amount }});
        const driversAssistance = parseFloat(document.getElementById('drivers_assistance').value) || 0;
        const expensesDeduction = parseFloat(document.getElementById('expenses_deduction').value) || 0;
        const travelPaper = parseFloat(document.getElementById('travel_paper_deduction').value) || 0;
        const truckingDeduction = parseFloat(document.getElementById('trucking_deduction').value) || 0;
        const cashAdvance = parseFloat(document.getElementById('cash_advance').value) || 0;
        const otherDeductionAmount = parseFloat(document.getElementById('other_deduction_amount').value) || 0;

        const totalDeductions = expensesDeduction + travelPaper + truckingDeduction + cashAdvance + otherDeductionAmount;
        const netPayable = grossAmount - totalDeductions + driversAssistance;

        document.getElementById('summaryDeductions').textContent = `- ₱ ${totalDeductions.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('summaryDriverAssistance').textContent = `+ ₱ ${driversAssistance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('summaryNetPayable').textContent = `₱ ${netPayable.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.deduction-input').forEach(input => {
            input.addEventListener('input', recalculateDeductions);
        });

        const otherDeductionLabelInput = document.getElementById('other_deduction_label');
        if (otherDeductionLabelInput) {
            otherDeductionLabelInput.addEventListener('input', recalculateDeductions);
            otherDeductionLabelInput.addEventListener('change', recalculateDeductions);
        }

        recalculateDeductions();
    });
</script>
@endpush
@endsection
