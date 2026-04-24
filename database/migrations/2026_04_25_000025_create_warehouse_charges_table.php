<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
            $table->foreignId('warehouse_product_id')->nullable()->constrained('warehouse_products')->nullOnDelete();
            $table->string('type');
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->date('charge_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('merchant_id');
            $table->index('warehouse_id');
            $table->index('order_id');
            $table->index('shipment_id');
            $table->index('warehouse_product_id');
            $table->index('type');
            $table->index('status');
            $table->index('charge_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_charges');
    }
};
