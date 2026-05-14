<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedInteger('delegated_normal_stock_count')->default(0)->after('prefered_stock_count');
            $table->unsignedInteger('delegated_prefered_stock_count')->default(0)->after('delegated_normal_stock_count');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['delegated_normal_stock_count', 'delegated_prefered_stock_count']);
        });
    }
};
