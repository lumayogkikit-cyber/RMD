<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$dbName = config('database.connections.' . config('database.default') . '.database');
$driver = config('database.connections.' . config('database.default') . '.driver');
$host = config('database.connections.' . config('database.default') . '.host');
$user = config('database.connections.' . config('database.default') . '.username');

function existsTable($dbName, $table) {
    return (bool) DB::selectOne('SELECT 1 AS found FROM information_schema.tables WHERE table_schema = ? AND table_name = ?', [$dbName, $table]);
}

function getColumns($dbName, $table) {
    $cols = DB::select('SELECT column_name, data_type, column_type, is_nullable FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position', [$dbName, $table]);
    return array_map(fn($c) => "$c->column_name ($c->column_type, nullable={$c->is_nullable})", $cols);
}

function getForeignKeys($dbName, $table) {
    $fks = DB::select('SELECT constraint_name, column_name, referenced_table_name, referenced_column_name FROM information_schema.key_column_usage WHERE table_schema = ? AND table_name = ? AND referenced_table_name IS NOT NULL', [$dbName, $table]);
    return array_map(fn($fk) => "$fk->constraint_name: $fk->column_name -> $fk->referenced_table_name($fk->referenced_column_name)", $fks);
}

function rowCount($table) {
    return DB::table($table)->count();
}

function sampleRows($table, $limit = 2) {
    return DB::table($table)->limit($limit)->get();
}

echo "DATABASE CONNECTION\n";
echo "driver=$driver\n";
echo "database=$dbName\n";
echo "host=$host\n";
echo "username=$user\n";
echo "\n";

$tables = ['users', 'suppliers', 'truck_loads', 'scale_items', 'sessions', 'cache', 'jobs', 'price_matrices', 'audit_logs'];
foreach ($tables as $table) {
    $exists = existsTable($dbName, $table);
    echo "table=$table exists=" . ($exists ? 'yes' : 'no') . "\n";
    if ($exists) {
        echo " columns:\n";
        foreach (getColumns($dbName, $table) as $col) {
            echo "  - $col\n";
        }
        echo " foreign_keys:\n";
        foreach (getForeignKeys($dbName, $table) as $fk) {
            echo "  - $fk\n";
        }
        echo " count=" . rowCount($table) . "\n";
    }
    echo "\n";
}

// Orphan checks
if (existsTable($dbName, 'scale_items') && existsTable($dbName, 'truck_loads')) {
    $orphanItems = DB::table('scale_items')
        ->leftJoin('truck_loads', 'scale_items.truck_load_id', '=', 'truck_loads.id')
        ->whereNull('truck_loads.id')
        ->count();
    echo "orphaned_scale_items=$orphanItems\n";
}

if (existsTable($dbName, 'truck_loads') && existsTable($dbName, 'suppliers')) {
    $truckLoadsMissingSupplier = DB::table('truck_loads')
        ->leftJoin('suppliers', 'truck_loads.supplier_id', '=', 'suppliers.id')
        ->whereNotNull('truck_loads.supplier_id')
        ->whereNull('suppliers.id')
        ->count();
    echo "truck_loads_missing_supplier=$truckLoadsMissingSupplier\n";
}

if (existsTable($dbName, 'truck_loads') && existsTable($dbName, 'suppliers')) {
    $truckLoadsMissingSupplier = DB::table('truck_loads')
        ->leftJoin('suppliers', 'truck_loads.supplier_id', '=', 'suppliers.id')
        ->whereNotNull('truck_loads.supplier_id')
        ->whereNull('suppliers.id')
        ->count();
    echo "truck_loads_missing_supplier=$truckLoadsMissingSupplier\n";
}

echo "\n";

if (existsTable($dbName, 'truck_loads')) {
    $netSum = DB::table('truck_loads')->sum('net_payable');
    $volumeSum = DB::table('truck_loads')->sum('total_volume');
    echo "truck_loads.total_net_payable=$netSum\n";
    echo "truck_loads.total_volume=$volumeSum\n";
}

if (existsTable($dbName, 'scale_items')) {
    $itemsSubtotalSum = DB::table('scale_items')->sum('subtotal');
    echo "scale_items.total_subtotal=$itemsSubtotalSum\n";
}

echo "\nSAMPLE DATA\n";
foreach (['suppliers', 'truck_loads', 'scale_items'] as $table) {
    if (existsTable($dbName, $table)) {
        echo "sample $table:\n";
        $rows = sampleRows($table);
        foreach ($rows as $row) {
            echo '  ' . json_encode($row) . "\n";
        }
        echo "\n";
    }
}

if (existsTable($dbName, 'migrations')) {
    echo "MIGRATIONS TABLE\n";
    $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
    foreach ($migrations as $migration) {
        echo "  - {$migration->migration} (batch {$migration->batch})\n";
    }
    echo "\n";
}
