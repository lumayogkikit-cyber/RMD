<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TruckLoad;
use Illuminate\Support\Str;

$selectedScope = 'monthly';
$selectedMonth = date('m');
$selectedYear = date('Y');

$query = TruckLoad::with(['scaleItems', 'supplier'])->whereYear('date_scaled', $selectedYear);
if ($selectedScope === 'monthly' && !empty($selectedMonth)) {
    $query->whereMonth('date_scaled', $selectedMonth);
}

$truckLoads = $query->orderBy('date_scaled')->get();
$reportRows = [];

foreach ($truckLoads as $load) {
    $dateScaled = \Carbon\Carbon::parse($load->date_scaled);
    $rowKey = $load->id;
    $reportRows[$rowKey] = [
        'id' => $load->id,
        'sheet_no' => $load->scale_sheet_no ?? $load->invoice_no ?? ('#' . $load->id),
        'date' => $dateScaled->format('M d, Y'),
        'supplier' => $load->supplier->name ?? 'Unknown',
        'truck_plate' => $load->truck_plate_no,
        'total_pieces' => (int) $load->total_logs,
        'total_volume' => (float) $load->total_volume,
        'gross_amount' => (float) $load->gross_amount,
        'total_deductions' => (float) $load->total_deductions,
        'net_payout' => (float) $load->net_payable,
    ];
}

$reportRows = array_values($reportRows);
$grandTotals = [
    'total_volume' => array_sum(array_column($reportRows, 'total_volume')),
    'gross' => array_sum(array_column($reportRows, 'gross_amount')),
    'deductions' => array_sum(array_column($reportRows, 'total_deductions')),
    'net' => array_sum(array_column($reportRows, 'net_payout')),
];
$periodLabel = date('Y-m');

// Render PDF via facade
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('reportRows', 'grandTotals', 'periodLabel'));
$out = $pdf->output();
$target = __DIR__ . '/../storage/logs/export_test.pdf';
file_put_contents($target, $out);
echo "Saved PDF to: $target\n";
