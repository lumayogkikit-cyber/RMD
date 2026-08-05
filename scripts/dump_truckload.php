<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TruckLoad;
use Illuminate\Support\Facades\DB;

$id = $argv[1] ?? 8;

$truck = TruckLoad::with(['supplier','scaleItems'])->find($id);

echo "--- TRUCK_LOAD (id={$id}) ---\n";
if ($truck) {
    echo json_encode($truck->toArray(), JSON_PRETTY_PRINT) . "\n";
} else {
    echo "NOT FOUND\n";
}

echo "\n--- RAW scale_items rows for truck_load_id={$id} ---\n";
$rows = DB::table('scale_items')->where('truck_load_id', $id)->get();
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
