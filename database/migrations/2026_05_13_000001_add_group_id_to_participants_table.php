<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('event_id')->constrained('groups');
        });

        foreach (DB::table('participants')->whereNull('group_id')->cursor() as $row) {
            $groupId = DB::table('events')->where('id', $row->event_id)->value('group_id');
            if ($groupId !== null) {
                DB::table('participants')->where('id', $row->id)->update(['group_id' => $groupId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};
