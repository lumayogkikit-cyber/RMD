<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scale_items', function (Blueprint $table) {
            if (! Schema::hasColumn('scale_items', 'parent_log_id')) {
                $table->foreignId('parent_log_id')->nullable()->after('truck_load_id')->constrained('scale_items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scale_items', function (Blueprint $table) {
            if (Schema::hasColumn('scale_items', 'parent_log_id')) {
                $table->dropConstrainedForeignId('parent_log_id');
            }
        });
    }
};
