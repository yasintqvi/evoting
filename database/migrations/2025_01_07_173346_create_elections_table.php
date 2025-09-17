<?php

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
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
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events');
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->string('type')->default(ElectionType::PUBLIC_JOINT);
            $table->string('status')->default(ElectionStatus::CREATED);
            $table->integer('normal_stock_count')->default(0);
            $table->integer('prefered_stock_count')->default(0);
            $table->integer('prefered_stock_weight')->default(0);
            $table->integer('main_member_count')->default(0);
            $table->integer('substitute_member_count')->default(0);
            $table->integer('incpector_main_member_count')->default(0);
            $table->integer('incpector_substitute_member_count')->default(0);
            $table->boolean('quorum_required')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
