<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\PriceMatrix;
use App\Models\TruckLoad;
use App\Models\ScaleItem;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class WoodScalingSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Default Users (Super Admin & Admin Scaler)
        $superAdmin = User::create([
            'name' => 'Super Admin Master',
            'email' => 'superadmin@rmd.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $scalerAdmin = User::create([
            'name' => 'Scaler Staff',
            'email' => 'scaler@rmd.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        AuditLog::create([
            'user_id' => $superAdmin->id,
            'user_name' => $superAdmin->name,
            'action' => 'System Initialized',
            'details' => 'Created initial Super Admin and Scaler Admin accounts.',
            'ip_address' => '127.0.0.1',
        ]);
        // 1. Create Suppliers
        $aldo = Supplier::create([
            'name' => 'ALDO BEHING',
            'contact_no' => '0917-555-0192',
            'address' => 'Butuan City, Agusan del Norte',
        ]);

        $juan = Supplier::create([
            'name' => 'JUAN DELA CRUZ',
            'contact_no' => '0928-888-2104',
            'address' => 'Prosperidad, Agusan del Sur',
        ]);

        $agusan = Supplier::create([
            'name' => 'AGUSAN TIMBER SUPPLIES',
            'contact_no' => '0905-123-4567',
            'address' => 'San Francisco, Agusan del Sur',
        ]);

        // 2. Purge non-Falcata categories and seed Pure FALCATA Price Matrix
        PriceMatrix::whereNotIn('category', ['FALCATA', 'SAWMILL'])->delete();

        $falcataRanges = [
            ['min' => 16, 'max' => 18, 'price' => 1400.00],
            ['min' => 20, 'max' => 24, 'price' => 1800.00],
            ['min' => 26, 'max' => 28, 'price' => 2350.00],
            ['min' => 30, 'max' => 38, 'price' => 2850.00],
            ['min' => 40, 'max' => 48, 'price' => 3150.00],
            ['min' => 50, 'max' => 58, 'price' => 3250.00],
            ['min' => 60, 'max' => 999, 'price' => 3350.00],
        ];

        $lengths = [1.30, 2.60];

        foreach ($lengths as $len) {
            foreach ($falcataRanges as $r) {
                PriceMatrix::updateOrCreate(
                    [
                        'category' => 'FALCATA',
                        'length' => $len,
                        'dia_min' => $r['min'],
                        'dia_max' => $r['max'],
                    ],
                    [
                        'price_per_cu_m' => $r['price'],
                    ]
                );
            }

            // Sawmill Grade spec for Falcata
            PriceMatrix::updateOrCreate(
                [
                    'category' => 'SAWMILL',
                    'length' => $len,
                    'dia_min' => 0,
                    'dia_max' => 0,
                ],
                [
                    'price_per_cu_m' => 1800.00,
                ]
            );
        }

        // 3. Create Sample Scale Sheet (Truck Load)
        $load = TruckLoad::create([
            'supplier_id' => $aldo->id,
            'truck_plate_no' => 'ADH-2525',
            'scale_sheet_no' => '089271',
            'invoice_no' => 'RMD-2026-0001',
            'status' => 'completed',
            'date_unload' => Carbon::now()->subDays(1),
            'date_scaled' => Carbon::now(),
            'drivers_assistance' => 500.00,
            'expenses_deduction' => 250.00,
            'travel_paper_deduction' => 300.00,
            'trucking_deduction' => 1200.00,
            'scaled_by' => 'J. Boholst (Scaler)',
            'notes' => 'First batch delivery of Falcata and Lauan logs.',
        ]);

        // Sample Items for Load 089271
        $itemsData = [
            ['cat' => 'FALCATA', 'len' => 2.50, 'dia' => 24, 'qty' => 10],
            ['cat' => 'FALCATA', 'len' => 2.50, 'dia' => 32, 'qty' => 8],
            ['cat' => 'FALCATA', 'len' => 3.00, 'dia' => 42, 'qty' => 5],
            ['cat' => 'LAUAN',   'len' => 2.50, 'dia' => 28, 'qty' => 6],
        ];

        $totalLogs = 0;
        $totalVol = 0.0;
        $grossVal = 0.0;

        foreach ($itemsData as $item) {
            $volPerLog = ScaleItem::calculateBreretonVolume($item['dia'], $item['len']);
            $totVol = round($volPerLog * $item['qty'], 3);
            $rate = PriceMatrix::matchRate($item['cat'], $item['len'], $item['dia']);
            $subtotal = round($totVol * $rate, 3);

            ScaleItem::create([
                'truck_load_id' => $load->id,
                'wood_category' => $item['cat'],
                'length' => $item['len'],
                'diameter' => $item['dia'],
                'quantity' => $item['qty'],
                'volume' => $volPerLog,
                'total_volume' => $totVol,
                'price_per_cu_m' => $rate,
                'subtotal' => $subtotal,
            ]);

            $totalLogs += $item['qty'];
            $totalVol += $totVol;
            $grossVal += $subtotal;
        }

        $totalDeductions = 500.00 + 250.00 + 300.00 + 1200.00;
        $netPayable = $grossVal - $totalDeductions;

        $load->update([
            'total_logs' => $totalLogs,
            'total_volume' => round($totalVol, 3),
            'gross_amount' => round($grossVal, 3),
            'total_deductions' => round($totalDeductions, 3),
            'net_payable' => round($netPayable, 3),
        ]);
    }
}
