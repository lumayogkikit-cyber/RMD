<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceMatrix extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'length',
        'dia_min',
        'dia_max',
        'price_per_cu_m',
    ];

    protected $casts = [
        'length' => 'float',
        'dia_min' => 'integer',
        'dia_max' => 'integer',
        'price_per_cu_m' => 'float',
    ];

    /**
     * Helper to resolve matching rate for a category, length, diameter, and grade.
     * First queries dynamic PriceMatrix DB records set by Super Admin, then falls back to defaults.
     */
    public static function matchRate(string $category, float $length, int $diameter, string $grade = 'Good'): float
    {
        $normalizedGrade = strtoupper(trim($grade));
        if ($normalizedGrade === 'SAWMILL' || $normalizedGrade === 'SAWMILL (SM)') {
            $dbSawmill = static::where(function ($q) use ($category) {
                $q->where('category', strtoupper(trim($category)))
                  ->orWhere('category', 'SAWMILL');
            })->value('price_per_cu_m');

            if ($dbSawmill !== null) {
                return (float) $dbSawmill;
            }

            return 1800.00;
        }

        // Query database for matching category/length/diameter range
        $dbMatch = static::where(function ($q) use ($category) {
            $cat = strtoupper(trim($category));
            $q->where('category', $cat)
              ->orWhere('category', 'FALCATA');
        })
        ->where('dia_min', '<=', $diameter)
        ->where('dia_max', '>=', $diameter)
        ->value('price_per_cu_m');

        if ($dbMatch !== null) {
            return (float) $dbMatch;
        }
        // No explicit DB match found — return 0.00 so callers treat it as "no rate set".
        return 0.00;
    }
}
