<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\PriceMatrix;
use App\Models\Category;
use App\Models\TruckLoad;
use App\Models\ScaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB as FacadesDB;

class ScalingController extends Controller
{
    /**
     * Dashboard / Scale Sheets Listing
     */
    /**
     * Dashboard / Scale Sheets Listing with Unified Filters
     */
    public function index(Request $request)
    {
        $baseQuery = TruckLoad::with('supplier');
        $this->applyScaleSheetFilters($baseQuery, $request);

        // Compute dynamic aggregate metrics for filtered results
        $filteredQuery = clone $baseQuery;
        $totalLoads = $filteredQuery->count();
        $totalLogsAll = (int) $filteredQuery->sum('total_logs');
        $totalVolumeAll = (float) $filteredQuery->sum('total_volume');
        $totalNetPayable = (float) $filteredQuery->sum('net_payable');

        $truckLoads = $baseQuery->latest('date_scaled')->latest('id')->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('scaling.index', compact(
            'truckLoads',
            'suppliers',
            'totalLoads',
            'totalLogsAll',
            'totalVolumeAll',
            'totalNetPayable'
        ));
    }

    /**
     * Helper to apply unified filters across scale sheet queries
     */
    protected function applyScaleSheetFilters($query, Request $request)
    {
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('scale_sheet_no', 'like', "%{$search}%")
                  ->orWhere('truck_plate_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $dateScope = $request->input('date_scope');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($dateScope === 'today') {
            $query->whereDate('date_scaled', \Carbon\Carbon::today());
        } elseif ($dateScope === 'this_week') {
            $query->whereBetween('date_scaled', [\Carbon\Carbon::now()->startOfWeek()->toDateString(), \Carbon\Carbon::now()->endOfWeek()->toDateString()]);
        } elseif ($dateScope === 'this_month') {
            $query->whereMonth('date_scaled', date('m'))->whereYear('date_scaled', date('Y'));
        } elseif ($dateScope === 'custom' || ($dateFrom || $dateTo)) {
            if ($dateFrom) {
                $query->whereDate('date_scaled', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('date_scaled', '<=', $dateTo);
            }
        }

        return $query;
    }

    /**
     * Generate PDF report for selected filtered scale sheets.
     */
    public function exportPdfReport(Request $request)
    {
        $query = TruckLoad::with(['scaleItems', 'supplier']);
        $this->applyScaleSheetFilters($query, $request);

        $truckLoads = $query->orderBy('date_scaled')->get();
        $reportRows = [];

        foreach ($truckLoads as $load) {
            $dateScaled = \Carbon\Carbon::parse($load->date_scaled);
            $reportRows[] = [
                'sheet_no' => $load->scale_sheet_no ?? $load->invoice_no ?? ('#' . $load->id),
                'supplier_name' => $load->supplier->name ?? 'Unknown',
                'truck_plate' => $load->truck_plate_no,
                'scaled_date' => $dateScaled->format('M d, Y'),
                'total_logs' => (int) $load->total_logs,
                'total_volume' => (float) $load->total_volume,
                'net_payout' => (float) $load->net_payable,
            ];
        }

        $grandTotals = [
            'total_logs' => array_sum(array_column($reportRows, 'total_logs')),
            'total_volume' => array_sum(array_column($reportRows, 'total_volume')),
            'net' => array_sum(array_column($reportRows, 'net_payout')),
        ];

        $reportType = 'Summary';
        if ($request->input('date_scope') === 'this_week') {
            $reportType = 'Weekly';
        } elseif ($request->input('date_scope') === 'this_month') {
            $reportType = 'Monthly';
        } elseif ($request->input('date_scope') === 'custom' || $request->filled('date_from') || $request->filled('date_to')) {
            $reportType = 'Custom';
        }

        $periodLabel = 'Scale Sheet Summary Report';
        if ($request->input('date_scope') === 'today') {
            $periodLabel = 'Today (' . date('M d, Y') . ')';
        } elseif ($request->input('date_scope') === 'this_week') {
            $periodLabel = 'This Week (' . \Carbon\Carbon::now()->startOfWeek()->format('M d') . ' - ' . \Carbon\Carbon::now()->endOfWeek()->format('M d, Y') . ')';
        } elseif ($request->input('date_scope') === 'this_month') {
            $periodLabel = date('F Y');
        } elseif ($request->filled('date_from') || $request->filled('date_to')) {
            $fromStr = $request->date_from ? \Carbon\Carbon::parse($request->date_from)->format('M d, Y') : 'Start';
            $toStr = $request->date_to ? \Carbon\Carbon::parse($request->date_to)->format('M d, Y') : 'Present';
            $periodLabel = "{$fromStr} to {$toStr}";
        }

        $dateGenerated = \Carbon\Carbon::now()->format('M d, Y');
        $generatedBy = auth()->user()?->name ?? auth()->user()?->email ?? 'System';

        $pdf = Pdf::loadView('reports.summary-pdf', compact('reportRows', 'grandTotals', 'periodLabel', 'dateGenerated', 'generatedBy', 'reportType'))
            ->setPaper('a4', 'portrait');

        $safeLabel = Str::slug($periodLabel) ?: 'scale-sheet-report';
        $safeLabel = substr($safeLabel, 0, 120);
        $fileName = 'rmd-scale-sheet-report-' . strtolower($safeLabel) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Print-ready Logging & Wood Scaling Summary Report
     */
    public function printSummaryReport(Request $request)
    {
        $query = TruckLoad::with(['scaleItems', 'supplier']);
        $this->applyScaleSheetFilters($query, $request);

        $truckLoads = $query->orderBy('date_scaled')->get();
        $reportRows = [];

        foreach ($truckLoads as $load) {
            $dateScaled = \Carbon\Carbon::parse($load->date_scaled);
            $reportRows[] = [
                'sheet_no' => $load->scale_sheet_no ?? $load->invoice_no ?? ('#' . $load->id),
                'supplier_name' => $load->supplier->name ?? 'Unknown',
                'truck_plate' => $load->truck_plate_no,
                'scaled_date' => $dateScaled->format('M d, Y'),
                'total_logs' => (int) $load->total_logs,
                'total_volume' => (float) $load->total_volume,
                'net_payout' => (float) $load->net_payable,
            ];
        }

        $grandTotals = [
            'total_logs' => array_sum(array_column($reportRows, 'total_logs')),
            'total_volume' => array_sum(array_column($reportRows, 'total_volume')),
            'net' => array_sum(array_column($reportRows, 'net_payout')),
        ];

        $reportType = 'Summary';
        if ($request->input('date_scope') === 'this_week') {
            $reportType = 'Weekly';
        } elseif ($request->input('date_scope') === 'this_month') {
            $reportType = 'Monthly';
        } elseif ($request->input('date_scope') === 'custom' || $request->filled('date_from') || $request->filled('date_to')) {
            $reportType = 'Custom';
        }

        $periodLabel = 'Scale Sheet Summary Report';
        if ($request->input('date_scope') === 'today') {
            $periodLabel = 'Today (' . date('M d, Y') . ')';
        } elseif ($request->input('date_scope') === 'this_week') {
            $periodLabel = 'This Week (' . \Carbon\Carbon::now()->startOfWeek()->format('M d') . ' - ' . \Carbon\Carbon::now()->endOfWeek()->format('M d, Y') . ')';
        } elseif ($request->input('date_scope') === 'this_month') {
            $periodLabel = date('F Y');
        } elseif ($request->filled('date_from') || $request->filled('date_to')) {
            $fromStr = $request->date_from ? \Carbon\Carbon::parse($request->date_from)->format('M d, Y') : 'Start';
            $toStr = $request->date_to ? \Carbon\Carbon::parse($request->date_to)->format('M d, Y') : 'Present';
            $periodLabel = "{$fromStr} to {$toStr}";
        }

        $dateGenerated = \Carbon\Carbon::now()->format('M d, Y');
        $generatedBy = auth()->user()?->name ?? auth()->user()?->email ?? 'System';

        return view('reports.summary-print', compact(
            'reportRows',
            'grandTotals',
            'periodLabel',
            'reportType',
            'dateGenerated',
            'generatedBy'
        ));
    }

    /**
     * Show Scale Sheet Entry Form with Dynamic Matrix
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $existingSuppliers = Supplier::orderBy('name')->pluck('name');
        $priceMatrices = Cache::remember('active_price_matrix', 3600, function () {
            return PriceMatrix::orderBy('category')->orderBy('length')->get();
        });

        $categories = Cache::remember('active_price_categories', 3600, function () {
            return Category::orderBy('name')->pluck('name');
        });

        // Auto-generate next Scale Sheet Number
        $lastRecord = TruckLoad::whereNotNull('scale_sheet_no')
            ->orderByRaw('CAST(scale_sheet_no AS UNSIGNED) DESC')
            ->first();

        if ($lastRecord && is_numeric($lastRecord->scale_sheet_no)) {
            $nextNumber = (int) $lastRecord->scale_sheet_no + 1;
        } else {
            $nextNumber = 89271;
        }

        $defaultSheetNo = null;
        $counter = FacadesDB::table('scale_sheet_counters')->where('name', 'scale_sheet_no')->first();
        if ($counter) {
            $defaultSheetNo = (string) ($counter->last_value + 1);
        }

        return view('scaling.create', compact('suppliers', 'existingSuppliers', 'priceMatrices', 'defaultSheetNo', 'categories'));
    }

    /**
     * Store Scale Sheet and Item Matrix in DB Transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:150',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'truck_plate_no' => 'required|string|max:50',
            'date_unload' => 'required|date',
            'date_scaled' => 'required|date',
            'scaled_by' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'drivers_assistance' => 'nullable|numeric|min:0',
            'expenses_deduction' => 'nullable|numeric|min:0',
            'travel_paper_deduction' => 'nullable|numeric|min:0',
            'trucking_deduction' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $rawSupplierName = trim($request->input('supplier_name') ?? '');
            if (! $rawSupplierName && $request->filled('supplier_id')) {
                $existingSupplierObj = Supplier::find($request->input('supplier_id'));
                $rawSupplierName = $existingSupplierObj ? $existingSupplierObj->name : '';
            }
            if (! $rawSupplierName) {
                $rawSupplierName = 'UNSPECIFIED SUPPLIER';
            }

            $supplier = Supplier::firstOrCreate(['name' => strtoupper($rawSupplierName)]);

            $driversAssistance = (float) ($validated['drivers_assistance'] ?? 0);
            $expensesDeduction = (float) ($validated['expenses_deduction'] ?? 0);
            $travelPaper = (float) ($validated['travel_paper_deduction'] ?? 0);
            $truckingDeduction = (float) ($validated['trucking_deduction'] ?? 0);

            // Invoice number generation
            $currentYear = date('Y', strtotime($validated['date_scaled']));
            $lastId = TruckLoad::whereYear('created_at', $currentYear)->max('id') ?? 0;
            $invoiceNo = sprintf('RMD-%s-%04d', $currentYear, $lastId + 1);

            // Reserve atomic scale_sheet_no
            $sheetNo = null;
            $row = FacadesDB::table('scale_sheet_counters')->where('name', 'scale_sheet_no')->lockForUpdate()->first();
            if (!$row) {
                FacadesDB::table('scale_sheet_counters')->insert(['name' => 'scale_sheet_no', 'last_value' => 89270, 'created_at' => now(), 'updated_at' => now()]);
                $row = FacadesDB::table('scale_sheet_counters')->where('name', 'scale_sheet_no')->first();
            }
            $next = $row->last_value + 1;
            FacadesDB::table('scale_sheet_counters')->where('id', $row->id)->update(['last_value' => $next, 'updated_at' => now()]);
            $sheetNo = (string) $next;

            // Create TruckLoad record
            $truckLoad = TruckLoad::create([
                'supplier_id' => $supplier->id,
                'truck_plate_no' => strtoupper(trim($request->input('truck_plate_no'))),
                'scale_sheet_no' => $sheetNo,
                'invoice_no' => $invoiceNo,
                'status' => 'completed',
                'date_unload' => $validated['date_unload'],
                'date_scaled' => $validated['date_scaled'],
                'scaled_by' => $validated['scaled_by'] ?? (Auth::user()?->name ?? 'Scaler Staff'),
                'notes' => $validated['notes'] ?? null,
                'drivers_assistance' => $driversAssistance,
                'expenses_deduction' => $expensesDeduction,
                'travel_paper_deduction' => $travelPaper,
                'trucking_deduction' => $truckingDeduction,
            ]);

            $totalLogs = 0;
            $totalVolume = 0.0;
            $grossAmount = 0.0;

            foreach ($validated['items'] as $item) {
                $category = strtoupper(trim($item['category'] ?? 'UNKNOWN'));
                $grade = $item['grade'] ?? 'Good';
                $length = (float) ($item['length'] ?? 2.6);
                $diameter = (int) ($item['diameter'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $isSplit = isset($item['is_split']) && $item['is_split'];

                $volPerLog = ScaleItem::calculateBreretonVolume($diameter, $length);
                $effectivePieceCount = ScaleItem::resolveEffectivePieceCount($quantity, $isSplit, false);
                $calculationQuantity = ScaleItem::resolveVolumeBasisQuantity($quantity, $isSplit, false);
                $totVol = round($volPerLog * $calculationQuantity, 3);

                $rate = PriceMatrix::matchRate($category, $length, $diameter, $grade);
                $subtotal = round($totVol * $rate, 2);

                ScaleItem::create([
                    'truck_load_id' => $truckLoad->id,
                    'parent_log_id' => $item['parent_log_id'] ?? null,
                    'wood_category' => $category,
                    'grade' => $grade,
                    'is_split' => $isSplit,
                    'split_group_id' => $item['split_group_id'] ?? null,
                    'length' => $length,
                    'diameter' => $diameter,
                    'quantity' => $quantity,
                    'volume' => $volPerLog,
                    'total_volume' => $totVol,
                    'price_per_cu_m' => $rate,
                    'subtotal' => $subtotal,
                ]);

                $totalLogs += $effectivePieceCount;
                $totalVolume += $totVol;
                $grossAmount += $subtotal;
            }

            $totalDeductions = $driversAssistance + $expensesDeduction + $travelPaper + $truckingDeduction;
            $netPayable = $grossAmount - $totalDeductions;

            $truckLoad->update([
                'total_logs' => $totalLogs,
                'total_volume' => round($totalVolume, 3),
                'gross_amount' => round($grossAmount, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_payable' => round($netPayable, 2),
            ]);

            DB::commit();

            return redirect()->route('scaling.invoice.print', $truckLoad->id)
                ->with('success', "Invoice #{$truckLoad->invoice_no} (Scale Sheet #{$truckLoad->scale_sheet_no}) generated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Scaling store failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Error saving scale sheet: ' . $e->getMessage());
        }
    }

    /**
     * Printable Official Scale Sheet & Invoice Screen
     */
    public function show($id)
    {
        // try multiple eager-load variations to avoid missing relations
        try {
            $truckLoad = TruckLoad::with(['supplier', 'scaleItems', 'items', 'logItems', 'details'])->find($id);
        } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
            $truckLoad = TruckLoad::with(['supplier', 'scaleItems'])->find($id);
        }

        // fallback: if not found via id as primary, try route-model binding style
        if (! $truckLoad) {
            $truckLoad = TruckLoad::with(['supplier', 'scaleItems'])->findOrFail($id);
        }

        // compute friendly view variables with safe fallbacks for possible column name variations
        $invoiceNumber = $truckLoad->invoice_no ?? ('RMD-' . date('Y') . '-' . sprintf('%04d', $truckLoad->id));
        $preparedOn = optional($truckLoad->updated_at ?? $truckLoad->created_at)->format('M d, Y') ?? now()->format('M d, Y');

        // supplier name fallback: relation -> flat column
        $supplierName = $truckLoad->supplier->name ?? ($truckLoad->supplier_name ?? null) ?? 'N/A';

        // truck plate fallback variations
        $truckPlate = $truckLoad->truck_plate_no ?? $truckLoad->truck_plate ?? $truckLoad->plate_number ?? 'Empty';

        // ensure we explicitly load scale items for this truck load (avoid accidental global collections)
        $itemsCollection = ScaleItem::where('truck_load_id', $truckLoad->id)->get();

        // prepare bracket aggregation (same logic as printInvoice but tolerant to missing keys)
        $bracketOrder = ['20-24', 'Sawmill (SM)', '26-28', '30-38', '40-48', '50-58', '60-UP'];
        $brackets = $itemsCollection->map(function ($item) {
            $grade = $item->grade ?? 'Good';
            $dia = (int) ($item->diameter ?? 0);

            if ($grade === 'Sawmill' || str_contains($grade, 'Sawmill')) {
                $bracket = 'Sawmill (SM)';
            } elseif ($dia <= 24) {
                $bracket = '20-24';
            } elseif ($dia <= 28) {
                $bracket = '26-28';
            } elseif ($dia <= 38) {
                $bracket = '30-38';
            } elseif ($dia <= 48) {
                $bracket = '40-48';
            } elseif ($dia <= 58) {
                $bracket = '50-58';
            } else {
                $bracket = '60-UP';
            }

            return [
                'bracket' => $bracket,
                'pieces' => (int) ($item->quantity ?? 0),
                'total_volume' => (float) ($item->total_volume ?? 0.0),
                'rate' => (float) ($item->price_per_cu_m ?? 0.0),
                'subtotal' => (float) ($item->subtotal ?? 0.0),
            ];
        })->groupBy('bracket')->map(function ($items, $bracket) {
            return [
                'bracket' => $bracket,
                'pieces' => $items->sum('pieces'),
                'total_volume' => $items->sum('total_volume'),
                'rate' => $items->avg('rate'),
                'subtotal' => $items->sum('subtotal'),
            ];
        })->toArray();

        $breakdownBrackets = collect($bracketOrder)->map(function ($bracket) use ($brackets) {
            return $brackets[$bracket] ?? [
                'bracket' => $bracket,
                'pieces' => 0,
                'total_volume' => 0.0,
                'rate' => 0.0,
                'subtotal' => 0.0,
            ];
        })->values()->toArray();

        // raw fallback items (for debugging if needed in the view)
        $logItems = DB::table('scale_items')->where('truck_load_id', $truckLoad->id)->get();

        return view('scaling.show', [
            'scaleSheet' => $truckLoad,
            'truckLoad' => $truckLoad,
            'invoiceNumber' => $invoiceNumber,
            'preparedOn' => $preparedOn,
            'breakdownBrackets' => $breakdownBrackets,
            'supplierName' => $supplierName,
            'truckPlate' => $truckPlate,
            'logItems' => $logItems,
        ]);
    }

    /**
     * Dedicated Print Preview and Official Company Layout Invoice
     */
    public function printInvoice(TruckLoad $truckLoad)
    {
        $truckLoad->loadMissing(['supplier', 'scaleItems']);

        // Group scale items into standard size/grade brackets
        $bracketOrder = ['20-24', 'Sawmill (SM)', '26-28', '30-38', '40-48', '50-58', '60-UP'];
        $groupedBrackets = [];

        foreach ($bracketOrder as $b) {
            $groupedBrackets[$b] = [
                'bracket' => $b,
                'pieces' => 0,
                'total_volume' => 0.0,
                'rate' => 0.0,
                'subtotal' => 0.0,
            ];
        }

        // explicitly query scale items for this truck load to avoid pulling unrelated rows
        $items = ScaleItem::where('truck_load_id', $truckLoad->id)->get();

        foreach ($items as $item) {
            $grade = $item->grade ?? 'Good';
            $dia = (int) $item->diameter;

            if ($grade === 'Sawmill') {
                $b = 'Sawmill (SM)';
            } elseif ($dia <= 24) {
                $b = '20-24';
            } elseif ($dia <= 28) {
                $b = '26-28';
            } elseif ($dia <= 38) {
                $b = '30-38';
            } elseif ($dia <= 48) {
                $b = '40-48';
            } elseif ($dia <= 58) {
                $b = '50-58';
            } else {
                $b = '60-UP';
            }

            $effectivePieceCount = ScaleItem::resolveEffectivePieceCount((float) $item->quantity, (bool) $item->is_split, ! is_null($item->parent_log_id));
            $groupedBrackets[$b]['pieces'] += $effectivePieceCount;
            $groupedBrackets[$b]['total_volume'] += (float) $item->total_volume;
            $groupedBrackets[$b]['rate'] = (float) $item->price_per_cu_m;
            $groupedBrackets[$b]['subtotal'] += (float) $item->subtotal;
        }

        // zero-out subtotals for brackets that have zero pieces (defensive: avoid aggregating stray volumes)
        foreach ($groupedBrackets as $k => $row) {
            if (($row['pieces'] ?? 0) <= 0) {
                $groupedBrackets[$k]['subtotal'] = 0.0;
                $groupedBrackets[$k]['total_volume'] = 0.0;
            }
        }

        $breakdownBrackets = array_filter($groupedBrackets, fn($row) => $row['pieces'] > 0 || $row['total_volume'] > 0);
        if (empty($breakdownBrackets)) {
            $breakdownBrackets = $groupedBrackets;
        }

        $invoiceNumber = $truckLoad->invoice_no ?? ('RMD-' . date('Y') . '-' . sprintf('%04d', $truckLoad->id));
        $preparedOn = optional($truckLoad->updated_at ?? $truckLoad->created_at)->format('M d, Y') ?? now()->format('M d, Y');
        $supplierName = $truckLoad->supplier->name ?? ($truckLoad->supplier_name ?? null) ?? 'N/A';
        $truckPlate = $truckLoad->truck_plate_no ?? $truckLoad->truck_plate ?? $truckLoad->plate_number ?? 'Empty';

        return view('scaling.show', [
            'scaleSheet' => $truckLoad,
            'truckLoad' => $truckLoad,
            'invoiceNumber' => $invoiceNumber,
            'preparedOn' => $preparedOn,
            'breakdownBrackets' => $breakdownBrackets,
            'supplierName' => $supplierName,
            'truckPlate' => $truckPlate,
        ]);
    }

    public function downloadInvoicePdf(TruckLoad $truckLoad)
    {
        $truckLoad->loadMissing(['supplier', 'scaleItems']);

        $bracketOrder = ['20-24', 'Sawmill (SM)', '26-28', '30-38', '40-48', '50-58', '60-UP'];
        $groupedBrackets = [];

        foreach ($bracketOrder as $b) {
            $groupedBrackets[$b] = [
                'bracket' => $b,
                'pieces' => 0,
                'total_volume' => 0.0,
                'rate' => 0.0,
                'subtotal' => 0.0,
            ];
        }

        // explicitly query scale items for this truck load to avoid pulling unrelated rows
        $items = ScaleItem::where('truck_load_id', $truckLoad->id)->get();

        foreach ($items as $item) {
            $grade = $item->grade ?? 'Good';
            $dia = (int) $item->diameter;

            if ($grade === 'Sawmill') {
                $b = 'Sawmill (SM)';
            } elseif ($dia <= 24) {
                $b = '20-24';
            } elseif ($dia <= 28) {
                $b = '26-28';
            } elseif ($dia <= 38) {
                $b = '30-38';
            } elseif ($dia <= 48) {
                $b = '40-48';
            } elseif ($dia <= 58) {
                $b = '50-58';
            } else {
                $b = '60-UP';
            }

            $effectivePieceCount = ScaleItem::resolveEffectivePieceCount((float) $item->quantity, (bool) $item->is_split, ! is_null($item->parent_log_id));
            $groupedBrackets[$b]['pieces'] += $effectivePieceCount;
            $groupedBrackets[$b]['total_volume'] += (float) $item->total_volume;
            $groupedBrackets[$b]['rate'] = (float) $item->price_per_cu_m;
            $groupedBrackets[$b]['subtotal'] += (float) $item->subtotal;
        }

        // zero-out subtotals for brackets that have zero pieces (defensive)
        foreach ($groupedBrackets as $k => $row) {
            if (($row['pieces'] ?? 0) <= 0) {
                $groupedBrackets[$k]['subtotal'] = 0.0;
                $groupedBrackets[$k]['total_volume'] = 0.0;
            }
        }

        $breakdownBrackets = array_filter($groupedBrackets, fn($row) => $row['pieces'] > 0 || $row['total_volume'] > 0);
        if (empty($breakdownBrackets)) {
            $breakdownBrackets = $groupedBrackets;
        }

        $invoiceNumber = $truckLoad->invoice_no ?? ('RMD-' . date('Y') . '-' . sprintf('%04d', $truckLoad->id));
        $preparedOn = optional($truckLoad->updated_at ?? $truckLoad->created_at)->format('M d, Y') ?? now()->format('M d, Y');
        $supplierName = $truckLoad->supplier->name ?? ($truckLoad->supplier_name ?? null) ?? 'N/A';
        $truckPlate = $truckLoad->truck_plate_no ?? $truckLoad->truck_plate ?? $truckLoad->plate_number ?? 'Empty';

        $pdf = Pdf::loadView('scaling.invoice-pdf-template', compact(
            'truckLoad',
            'invoiceNumber',
            'preparedOn',
            'breakdownBrackets',
            'supplierName',
            'truckPlate'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
            'defaultFont' => 'DejaVu Sans',
        ]);

        return $pdf->download('scale-sheet-' . ($truckLoad->scale_sheet_no ?: $truckLoad->id) . '.pdf');
    }


    protected function resolvePeriodLabel(?string $scope, ?string $week, ?string $month, ?string $year): string
    {
        $scope = strtolower(trim((string) ($scope ?? '')));
        $year = $year ?: date('Y');

        if ($scope === 'weekly' && $week) {
            // week expected in 'WW' format (ISO week)
            try {
                $weekStart = \Carbon\Carbon::now()->setISODate((int) $year, (int) $week)->startOfWeek(\Carbon\Carbon::MONDAY);
                return 'Week ' . $week . ' (' . $weekStart->format('M d') . ' - ' . $weekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('M d, Y') . ')';
            } catch (\Exception $e) {
                Log::warning('Failed to parse ISO week for period label', ['week' => $week, 'year' => $year, 'error' => $e->getMessage(), 'user_id' => auth()->id()]);
                // fallback label
                return 'Week ' . ($week ?: 'N/A') . ' ' . $year;
            }
        }

        if ($scope === 'monthly' && !empty($month)) {
            try {
                return \Carbon\Carbon::createFromDate((int) $year, (int) $month, 1)->translatedFormat('F Y');
            } catch (\Exception $e) {
                return (string) $year;
            }
        }

        if ($scope === 'yearly') {
            return (string) $year;
        }

        return \Carbon\Carbon::createFromDate((int) $year, 1, 1)->translatedFormat('Y');
    }

    /**
     * Delete a Scale Sheet record
     */
    public function destroy(TruckLoad $truckLoad)
    {
        if (auth()->user()?->role !== 'super_admin') {
            abort(403, 'Unauthorized. Only Super Admin can delete scale sheets.');
        }

        $sheetNo = $truckLoad->scale_sheet_no;
        $truckLoad->delete();

        return redirect()->route('scaling.index')
            ->with('success', "Scale Sheet #{$sheetNo} deleted successfully.");
    }
}
