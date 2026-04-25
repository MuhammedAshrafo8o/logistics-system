<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_cash_closures', function (Blueprint $table) {
            $table->date('closure_date')->nullable()->after('driver_id');
            $table->index('closure_date');
        });
    }

    public function down(): void
    {
        Schema::table('driver_cash_closures', function (Blueprint $table) {
            $table->dropIndex(['closure_date']);
            $table->dropColumn('closure_date');
        });
    }
};
