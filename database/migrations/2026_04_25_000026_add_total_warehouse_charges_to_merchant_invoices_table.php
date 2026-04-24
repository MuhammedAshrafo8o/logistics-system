<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_invoices', function (Blueprint $table) {
            $table->decimal('total_warehouse_charges', 12, 2)->default(0)->after('total_shipping_fees');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_invoices', function (Blueprint $table) {
            $table->dropColumn('total_warehouse_charges');
        });
    }
};
