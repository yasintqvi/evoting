<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropUnique('elections_slug_unique');
        });

        Schema::table('elections', function (Blueprint $table) {
            $table->unique(['event_id', 'slug'], 'elections_event_id_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropUnique('elections_event_id_slug_unique');
        });

        Schema::table('elections', function (Blueprint $table) {
            $table->unique('slug', 'elections_slug_unique');
        });
    }
};
