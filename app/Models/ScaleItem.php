<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScaleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'truck_load_id',
        'parent_log_id',
        'wood_category',
        'grade',
        'is_split',
        'split_group_id',
        'length',
        'diameter',
        'quantity',
        'volume',
        'total_volume',
        'price_per_cu_m',
        'subtotal',
    ];

    protected $casts = [
        'is_split' => 'boolean',
        'length' => 'float',
        'diameter' => 'integer',
        'quantity' => 'integer',
        'volume' => 'float',
        'total_volume' => 'float',
        'price_per_cu_m' => 'float',
        'subtotal' => 'float',
    ];

    public function truckLoad(): BelongsTo
    {
        return $this->belongsTo(TruckLoad::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_log_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_log_id');
    }

    public static function resolveEffectivePieceCount(int|float $quantity, bool $isSplit = false, bool $isChild = false): float
    {
        if (! $isSplit) {
            return (float) $quantity;
        }

        return $isChild ? 0.0 : (float) $quantity;
    }

    public static function resolveVolumeBasisQuantity(int|float $quantity, bool $isSplit = false, bool $isChild = false): float
    {
        if (! $isSplit) {
            return (float) $quantity;
        }

        return max(1.0, (float) $quantity);
    }

    /**
     * Brereton Volume Formula in Cubic Meters:
     * V = ((D1 + D2) / 2)^2 * L * 0.00007854
     * For a single standard log where D1 = D2 = diameter in cm, this becomes:
     * V = diameter^2 * L * 0.00007854
     */
    public static function calculateBreretonVolume(int $diameterCm, float $lengthM): float
    {
        return round($diameterCm * $diameterCm * $lengthM * 0.00007854, 3);
    }
}
