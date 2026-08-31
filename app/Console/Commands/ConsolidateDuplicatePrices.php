<?php

namespace App\Console\Commands;

use App\Models\PriceMatrix;
use Illuminate\Console\Command;

class ConsolidateDuplicatePrices extends Command
{
    protected $signature = 'prices:consolidate-duplicates {--dry-run}';
    protected $description = 'Identify and consolidate duplicate price entries for the same category/diameter range';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Scanning for duplicate price entries...');
        
        $duplicates = PriceMatrix::selectRaw('category, dia_min, dia_max, COUNT(*) as count')
            ->groupBy('category', 'dia_min', 'dia_max')
            ->having('count', '>', 1)
            ->orderBy('category')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✓ No duplicates found. All category/diameter ranges are unique.');
            return 0;
        }

        $this->warn('Found ' . $duplicates->count() . ' duplicate category/diameter combinations:');
        $this->line('');

        foreach ($duplicates as $dup) {
            $this->line("Category: <info>{$dup->category}</info> | Diameter: <info>{$dup->dia_min}-{$dup->dia_max}</info>");
            
            $rows = PriceMatrix::where('category', $dup->category)
                ->where('dia_min', $dup->dia_min)
                ->where('dia_max', $dup->dia_max)
                ->orderBy('length')
                ->get(['id', 'length', 'price_per_cu_m']);

            foreach ($rows as $row) {
                $this->line("  - ID {$row->id}: {$row->length}m @ ₱" . number_format($row->price_per_cu_m, 2));
            }
            
            if (!$dryRun) {
                // Keep the row with the HIGHEST price (likely the most recent update)
                // and consolidate all others into it, then delete the rest
                $keepRow = $rows->sortByDesc('price_per_cu_m')->first();
                $deleteRows = $rows->where('id', '!=', $keepRow->id);
                
                foreach ($deleteRows as $deleteRow) {
                    $this->line("  <error>Deleting ID {$deleteRow->id} ({$deleteRow->length}m @ ₱" . number_format($deleteRow->price_per_cu_m, 2) . ")</error>");
                    $deleteRow->delete();
                }
                
                $this->line("  <info>Keeping ID {$keepRow->id} ({$keepRow->length}m @ ₱" . number_format($keepRow->price_per_cu_m, 2) . ") as master price</info>");
            }
            
            $this->line('');
        }

        if ($dryRun) {
            $this->info('DRY RUN MODE: No changes made. Run without --dry-run to consolidate.');
        } else {
            $this->info('✓ Duplicate entries consolidated. Cache cleared.');
            \App\Services\PricingService::clearPricingCache();
        }

        return 0;
    }
}
