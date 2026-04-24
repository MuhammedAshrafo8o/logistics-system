<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_invoices', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('issued_at');
            $table->timestamp('generated_at')->nullable()->after('file_path');
            $table->unsignedInteger('download_count')->default(0)->after('generated_at');
            $table->timestamp('last_downloaded_at')->nullable()->after('download_count');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'file_path',
                'generated_at',
                'download_count',
                'last_downloaded_at',
            ]);
        });
    }
};
