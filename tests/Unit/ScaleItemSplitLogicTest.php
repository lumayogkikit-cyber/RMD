<?php

namespace Tests\Unit;

use App\Models\ScaleItem;
use PHPUnit\Framework\TestCase;

class ScaleItemSplitLogicTest extends TestCase
{
    public function test_split_child_rows_count_as_zero_for_totals_but_still_use_one_log_for_volume(): void
    {
        $this->assertEquals(1.0, ScaleItem::resolveEffectivePieceCount(1, true, false));
        $this->assertEquals(0.0, ScaleItem::resolveEffectivePieceCount(0, true, true));
        $this->assertEquals(1.0, ScaleItem::resolveVolumeBasisQuantity(0, true, true));
        $this->assertEquals(5.0, ScaleItem::resolveVolumeBasisQuantity(5, false, false));
    }
}
