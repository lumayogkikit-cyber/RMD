<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// minimal test data
$reportRows = [
    ['date' => date('M d, Y'), 'supplier' => 'Test Supplier', 'truck_plate' => 'ABC-123', 'total_pieces' => 1, 'total_volume' => 0.5, 'gross_amount' => 1000, 'total_deductions' => 0, 'net_payout' => 1000]
];
$grandTotals = ['total_volume' => 0.5, 'gross' => 1000, 'deductions' => 0, 'net' => 1000];
$periodLabel = 'Test Period';
$selectedScope = 'weekly';
$selectedWeek = '01';
$selectedMonth = date('m');
$selectedYear = date('Y');

$html = view('reports.weekly', compact('reportRows','grandTotals','periodLabel','selectedScope','selectedWeek','selectedMonth','selectedYear'))->render();
$target = __DIR__ . '/../storage/logs/reports_weekly_test.html';
file_put_contents($target, $html);
echo "Saved rendered weekly report to: $target\n";
