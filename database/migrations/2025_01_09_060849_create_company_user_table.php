<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_company', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('company_id')->constrained('companies');
            $table->integer('normal_stock_count')->default(1);
            $table->integer('prefered_stock_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_company');
    }
};
