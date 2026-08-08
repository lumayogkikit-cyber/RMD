<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('truck_loads') && ! Schema::hasColumn('truck_loads', 'other_deduction_label')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->string('other_deduction_label', 150)->nullable()->after('cash_advance');
                $table->decimal('other_deduction_amount', 10, 2)->default(0.00)->after('other_deduction_label');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('truck_loads') && Schema::hasColumn('truck_loads', 'other_deduction_amount')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->dropColumn(['other_deduction_amount', 'other_deduction_label']);
            });
        }
    }
};
