<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->foreignId('parent_election_id')
                ->nullable()
                ->after('event_id')
                ->constrained('elections')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_election_id');
        });
    }
};
