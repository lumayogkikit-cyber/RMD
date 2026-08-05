<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('suppliers', 'deleted_at')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('truck_loads', 'deleted_at')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('scale_items', 'deleted_at')) {
            Schema::table('scale_items', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scale_items', 'deleted_at')) {
            Schema::table('scale_items', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('truck_loads', 'deleted_at')) {
            Schema::table('truck_loads', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('suppliers', 'deleted_at')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
