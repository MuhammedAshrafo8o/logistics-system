<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('shipment_number')->unique();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 50);
            $table->foreignId('delivery_governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('delivery_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->text('delivery_address');
            $table->decimal('cod_amount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->string('status')->default('pending_pickup');
            $table->text('tracking_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('order_id');
            $table->index('shipment_number');
            $table->index('merchant_id');
            $table->index('status');
            $table->index(['delivery_governorate_id', 'delivery_area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
