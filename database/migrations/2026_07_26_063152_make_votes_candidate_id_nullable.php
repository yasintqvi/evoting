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
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->unsignedBigInteger('candidate_id')->nullable()->change();
            $table->foreign('candidate_id')
                ->references('id')
                ->on('candidates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->unsignedBigInteger('candidate_id')->nullable(false)->change();
            $table->foreign('candidate_id')
                ->references('id')
                ->on('candidates');
        });
    }
};
