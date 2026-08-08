<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TruckLoad extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'truck_plate_no',
        'scale_sheet_no',
        'invoice_no',
        'status',
        'date_unload',
        'date_scaled',
        'drivers_assistance',
        'expenses_deduction',
        'travel_paper_deduction',
        'trucking_deduction',
        'cash_advance',
        'other_deduction_label',
        'other_deduction_amount',
        'total_logs',
        'total_volume',
        'gross_amount',
        'total_deductions',
        'net_payable',
        'scaled_by',
        'notes',
    ];

    protected $casts = [
        'date_unload' => 'date',
        'date_scaled' => 'date',
        'drivers_assistance' => 'float',
        'expenses_deduction' => 'float',
        'travel_paper_deduction' => 'float',
        'trucking_deduction' => 'float',
        'cash_advance' => 'float',
        'other_deduction_amount' => 'float',
        'total_logs' => 'integer',
        'total_volume' => 'float',
        'gross_amount' => 'float',
        'total_deductions' => 'float',
        'net_payable' => 'float',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scaleItems(): HasMany
    {
        return $this->hasMany(ScaleItem::class);
    }

    /**
     * Cascade soft-delete / restore behaviour for related scale items
     */
    protected static function booted()
    {
        static::deleting(function (TruckLoad $truckLoad) {
            if ($truckLoad->isForceDeleting()) {
                // permanently delete children when force deleting
                $truckLoad->scaleItems()->withTrashed()->get()->each(function ($item) {
                    $item->forceDelete();
                });
            } else {
                // soft delete children
                $truckLoad->scaleItems()->delete();
            }
        });

        static::restored(function (TruckLoad $truckLoad) {
            // restore children when parent is restored
            $truckLoad->scaleItems()->withTrashed()->restore();
        });
    }
}
