<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TruckLoad;
use App\Models\ScaleItem;
use Illuminate\Support\Facades\DB;

$id = $argv[1] ?? 8;

try {
    $truckLoad = TruckLoad::with(['supplier', 'scaleItems', 'items', 'logItems', 'details'])->find($id);
} catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
    $truckLoad = TruckLoad::with(['supplier', 'scaleItems'])->find($id);
}
if (! $truckLoad) {
    $truckLoad = TruckLoad::with(['supplier', 'scaleItems'])->findOrFail($id);
}

$invoiceNumber = $truckLoad->invoice_no ?? ('RMD-' . date('Y') . '-' . sprintf('%04d', $truckLoad->id));
$preparedOn = optional($truckLoad->updated_at ?? $truckLoad->created_at)->format('M d, Y') ?? now()->format('M d, Y');

$supplierName = $truckLoad->supplier->name ?? ($truckLoad->supplier_name ?? null) ?? 'N/A';
$truckPlate = $truckLoad->truck_plate_no ?? $truckLoad->truck_plate ?? $truckLoad->plate_number ?? 'Empty';

$itemsCollection = $truckLoad->scaleItems ?? collect();
if ($itemsCollection->isEmpty()) {
    $itemsCollection = collect(DB::table('scale_items')->where('truck_load_id', $truckLoad->id)->get());
}

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

$logItems = DB::table('scale_items')->where('truck_load_id', $truckLoad->id)->get();

$html = view('scaling.show', [
    'scaleSheet' => $truckLoad,
    'invoiceNumber' => $invoiceNumber,
    'preparedOn' => $preparedOn,
    'breakdownBrackets' => $breakdownBrackets,
    'supplierName' => $supplierName,
    'truckPlate' => $truckPlate,
    'logItems' => $logItems,
])->render();

// print first 4000 chars
echo substr($html, 0, 4000);
