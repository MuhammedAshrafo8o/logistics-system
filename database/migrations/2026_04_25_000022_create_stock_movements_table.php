<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('warehouse_product_id')->constrained('warehouse_products')->cascadeOnDelete();
            $table->string('type');
            $table->integer('quantity');
            $table->unsignedInteger('before_available')->default(0);
            $table->unsignedInteger('after_available')->default(0);
            $table->unsignedInteger('before_reserved')->default(0);
            $table->unsignedInteger('after_reserved')->default(0);
            $table->unsignedInteger('before_damaged')->default(0);
            $table->unsignedInteger('after_damaged')->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('warehouse_id');
            $table->index('warehouse_product_id');
            $table->index('type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
