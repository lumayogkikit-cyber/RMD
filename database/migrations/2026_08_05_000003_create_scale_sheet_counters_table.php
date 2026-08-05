<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('scale_sheet_counters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->bigInteger('last_value')->default(0);
            $table->timestamps();
        });

        // Initialize counter with current max numeric scale_sheet_no or a sane default
        $max = (int) DB::table('truck_loads')->whereNotNull('scale_sheet_no')
            ->selectRaw('MAX(CAST(scale_sheet_no AS UNSIGNED)) as mx')
            ->value('mx');

        if ($max <= 0) {
            $max = 89270; // original baseline so next becomes 89271
        }

        DB::table('scale_sheet_counters')->insert([
            'name' => 'scale_sheet_no',
            'last_value' => $max,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('scale_sheet_counters');
    }
};
