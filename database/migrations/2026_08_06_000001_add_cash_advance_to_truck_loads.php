<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('truck_loads') && ! Schema::hasColumn('truck_loads', 'cash_advance')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->decimal('cash_advance', 10, 2)->default(0.00)->after('trucking_deduction');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('truck_loads') && Schema::hasColumn('truck_loads', 'cash_advance')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->dropColumn('cash_advance');
            });
        }
    }
};
