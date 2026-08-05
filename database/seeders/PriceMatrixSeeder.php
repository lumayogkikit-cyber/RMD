<?php

namespace Database\Seeders;

use App\Models\PriceMatrix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceMatrixSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PriceMatrix::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $prices = [
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 16, 'dia_max' => 18, 'price_per_cu_m' => 1400],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 20, 'dia_max' => 24, 'price_per_cu_m' => 1800],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 0,  'dia_max' => 0,  'price_per_cu_m' => 1800],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 26, 'dia_max' => 28, 'price_per_cu_m' => 2350],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 30, 'dia_max' => 38, 'price_per_cu_m' => 2850],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 40, 'dia_max' => 48, 'price_per_cu_m' => 3150],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 50, 'dia_max' => 58, 'price_per_cu_m' => 3250],
            ['category' => 'Peelable / F1(1.3/2.6)', 'dia_min' => 60, 'dia_max' => 999, 'price_per_cu_m' => 3350],
        ];

        foreach ($prices as $item) {
            PriceMatrix::create([
                'category' => $item['category'],
                'length' => 2.6,
                'dia_min' => $item['dia_min'],
                'dia_max' => $item['dia_max'],
                'price_per_cu_m' => $item['price_per_cu_m'],
            ]);
        }
    }
}
