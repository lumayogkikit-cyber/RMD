<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Price Matrices Table
        Schema::create('price_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // FALCATA, LAUAN, YEMANE, etc.
            $table->decimal('length', 8, 2); // In meters (e.g. 2.50, 3.00, 4.00)
            $table->integer('dia_min'); // Minimum diameter in cm (inclusive)
            $table->integer('dia_max'); // Maximum diameter in cm (inclusive)
            $table->decimal('price_per_cu_m', 10, 2); // Price per cubic meter
            $table->timestamps();
        });

        // 3. Truck Loads (Scale Sheets Header) Table
        Schema::create('truck_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('truck_plate_no');
            $table->string('scale_sheet_no')->unique();
            $table->string('invoice_no')->nullable()->unique(); // e.g. RMD-2026-0001
            $table->enum('status', ['draft', 'completed'])->default('completed'); // Status tracking
            $table->date('date_unload');
            $table->date('date_scaled');
            
            // Deductions
            $table->decimal('drivers_assistance', 10, 2)->default(0.00);
            $table->decimal('expenses_deduction', 10, 2)->default(0.00);
            $table->decimal('travel_paper_deduction', 10, 2)->default(0.00);
            $table->decimal('trucking_deduction', 10, 2)->default(0.00);
            
            // Calculated Financials & Aggregates
            $table->integer('total_logs')->default(0);
            $table->decimal('total_volume', 10, 4)->default(0.0000); // sum of cu.m
            $table->decimal('gross_amount', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_payable', 10, 2)->default(0.00);
            
            $table->string('scaled_by')->default('Scaler Staff');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 4. Scale Items (Individual Log Entries) Table
        Schema::create('scale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_load_id')->constrained('truck_loads')->onDelete('cascade');
            $table->foreignId('parent_log_id')->nullable()->constrained('scale_items')->nullOnDelete();
            $table->string('wood_category'); // FALCATA, LAUAN, YEMANE
            $table->enum('grade', ['Good', 'Sawmill'])->default('Good'); // Good vs Sawmill (SM)
            $table->boolean('is_split')->default(false); // Whether log was cut/split due to inner rot
            $table->string('split_group_id')->nullable(); // UUID/Group ID linking 2 pieces of 1 physical log
            $table->decimal('length', 8, 2); // length in meters
            $table->integer('diameter'); // diameter in cm
            $table->integer('quantity')->default(1); // count of logs with this spec
            $table->decimal('volume', 10, 4); // Brereton volume per log: (0.7854 * d^2 * L) / 10000
            $table->decimal('total_volume', 10, 4); // volume * quantity
            $table->decimal('price_per_cu_m', 10, 2); // rate based on category, length, diameter & grade
            $table->decimal('subtotal', 10, 2); // total_volume * price_per_cu_m
            $table->softDeletes();
            $table->timestamps();
        });

        // 5. Audit Logs Table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->default('System');
            $table->string('action'); // e.g. "Price Matrix Updated", "Scale Sheet Unlocked"
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('scale_items');
        Schema::dropIfExists('truck_loads');
        Schema::dropIfExists('price_matrices');
        Schema::dropIfExists('suppliers');
    }
};
