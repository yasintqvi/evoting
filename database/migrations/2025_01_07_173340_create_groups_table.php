<?php

use App\Enums\GroupType;
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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default(GroupType::COOPERTAIVE);
            $table->string('normal_stock_count')->default(0);
            $table->string('prefered_stock_count')->default(0);
            $table->string('prefered_stock_weight')->default(0);
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
