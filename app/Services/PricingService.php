<?php

namespace App\Services;

use App\Models\PriceMatrix;
use Illuminate\Support\Facades\Cache;

/**
 * PricingService: Centralized strict dynamic pricing lookups
 * 
 * Ensures:
 * - No caching of prices for scalers (always fresh from DB)
 * - Direct DB queries for current rates
 * - Superadmin prices take effect immediately
 */
class PricingService
{
    /**
     * Get all price matrix data fresh from DB (no cache for dynamic rates)
     * Used by scaler forms to ensure real-time rate updates
     */
    public static function getFreshPriceMatrix(): array
    {
        // Query directly from DB, no caching for strict real-time updates
        $prices = PriceMatrix::orderBy('category')
            ->orderBy('length')
            ->orderBy('dia_min')
            ->orderBy('dia_max')
            ->get()
            ->toArray();

        return $prices;
    }

    /**
     * Get matching rate for a specific log specification
     * Queries DB directly for strict accuracy - no cached rates
     * 
     * @param string $category Wood category (e.g., 'FALCATA', 'SAWMILL')
     * @param float $lengthM Log length in meters (1.0, 1.3, 2.6)
     * @param int $diameterCm Log diameter in centimeters
     * @param string $grade Grade type ('Good', 'Sawmill')
     * @return float Rate in ₱/m³ (0.00 if no match found)
     */
    public static function getRate(string $category, float $lengthM, int $diameterCm, string $grade = 'Good'): float
    {
        $normalizedCategory = strtoupper(trim($category));
        $normalizedGrade = strtoupper(trim($grade));

        // Handle Sawmill grade
        if ($normalizedGrade === 'SAWMILL' || $normalizedGrade === 'SAWMILL (SM)') {
            // First, try exact category match for sawmill
            $sawmillRate = PriceMatrix::where(function ($q) use ($normalizedCategory) {
                $q->where('category', $normalizedCategory)
                  ->orWhere('category', 'SAWMILL');
            })
            ->whereBetween('length', [$lengthM - 0.05, $lengthM + 0.05])
            ->where('dia_min', '<=', $diameterCm)
            ->where('dia_max', '>=', $diameterCm)
            ->orderByRaw("category = ? DESC", [$normalizedCategory])
            ->value('price_per_cu_m');

            if ($sawmillRate !== null) {
                return (float) $sawmillRate;
            }

            // Fallback: Try without length constraint
            $fallbackSawmill = PriceMatrix::where(function ($q) use ($normalizedCategory) {
                $q->where('category', $normalizedCategory)
                  ->orWhere('category', 'SAWMILL');
            })
            ->where('dia_min', '<=', $diameterCm)
            ->where('dia_max', '>=', $diameterCm)
            ->orderByRaw("category = ? DESC", [$normalizedCategory])
            ->value('price_per_cu_m');

            return $fallbackSawmill !== null ? (float) $fallbackSawmill : 1800.00;
        }

        // Handle Good grade - exact length match first
        $exactLengthMatch = PriceMatrix::where(function ($q) use ($normalizedCategory) {
            $q->where('category', $normalizedCategory)
              ->orWhere('category', 'FALCATA');
        })
        ->whereBetween('length', [$lengthM - 0.05, $lengthM + 0.05])
        ->where('dia_min', '<=', $diameterCm)
        ->where('dia_max', '>=', $diameterCm)
        ->orderByRaw("category = ? DESC", [$normalizedCategory])
        ->value('price_per_cu_m');

        if ($exactLengthMatch !== null) {
            return (float) $exactLengthMatch;
        }

        // Fallback: Any length for the category/diameter
        $fallbackMatch = PriceMatrix::where(function ($q) use ($normalizedCategory) {
            $q->where('category', $normalizedCategory)
              ->orWhere('category', 'FALCATA');
        })
        ->where('dia_min', '<=', $diameterCm)
        ->where('dia_max', '>=', $diameterCm)
        ->orderByRaw("category = ? DESC", [$normalizedCategory])
        ->value('price_per_cu_m');

        return $fallbackMatch !== null ? (float) $fallbackMatch : 0.00;
    }

    /**
     * Clear all pricing caches immediately after admin updates
     * Ensures scalers get fresh prices on next request
     */
    public static function clearPricingCache(): void
    {
        Cache::forget('active_price_matrix');
        Cache::forget('active_price_categories');
    }

    /**
     * Get all categories from price matrix (fresh from DB)
     */
    public static function getAllCategories(): array
    {
        return PriceMatrix::distinct('category')
            ->orderBy('category')
            ->pluck('category')
            ->map(fn ($cat) => strtoupper(trim($cat)))
            ->unique()
            ->values()
            ->toArray();
    }
}
