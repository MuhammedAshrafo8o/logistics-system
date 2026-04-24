<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_weight', 10, 2)->nullable();
            $table->decimal('unit_length', 10, 2)->nullable();
            $table->decimal('unit_width', 10, 2)->nullable();
            $table->decimal('unit_height', 10, 2)->nullable();
            $table->boolean('is_fragile')->default(false);
            $table->boolean('requires_packaging')->default(false);
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('merchant_id');
            $table->index('sku');
            $table->index('barcode');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_products');
    }
};
