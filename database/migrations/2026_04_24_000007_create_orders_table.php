<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone', 50);
            $table->string('customer_phone_alt', 50)->nullable();

            $table->foreignId('delivery_governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('delivery_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->text('delivery_address');
            $table->text('delivery_notes')->nullable();

            $table->foreignId('pickup_governorate_id')->nullable()->constrained('governorates')->nullOnDelete();
            $table->foreignId('pickup_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->text('pickup_address')->nullable();
            $table->text('pickup_notes')->nullable();

            $table->decimal('cod_amount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);

            $table->string('fulfillment_type')->default('pickup_from_merchant');
            $table->boolean('is_fragile')->default(false);
            $table->boolean('allow_inspection')->default(false);
            $table->boolean('requires_packaging')->default(false);
            $table->text('package_notes')->nullable();

            $table->string('source')->default('manual');
            $table->string('external_source')->nullable();
            $table->string('external_order_id')->nullable();
            $table->string('external_order_number')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->text('review_reason')->nullable();

            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('merchant_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('source');
            $table->index(['external_source', 'external_order_id']);
            $table->index(['delivery_governorate_id', 'delivery_area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
