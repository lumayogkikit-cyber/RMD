<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\PriceMatrix;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = PriceMatrix::distinct()->pluck('category')->filter()->unique();

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name]);
        }
    }
}
