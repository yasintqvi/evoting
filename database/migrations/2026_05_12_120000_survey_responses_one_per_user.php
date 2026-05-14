<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'DELETE sr1 FROM survey_responses sr1
                INNER JOIN survey_responses sr2
                ON sr1.survey_id = sr2.survey_id
                AND sr1.user_id = sr2.user_id
                AND sr1.user_id IS NOT NULL
                AND sr1.id < sr2.id'
            );
        } else {
            $ids = DB::table('survey_responses as sr1')
                ->join('survey_responses as sr2', function ($join) {
                    $join->on('sr1.survey_id', '=', 'sr2.survey_id')
                        ->on('sr1.user_id', '=', 'sr2.user_id')
                        ->whereColumn('sr2.id', '>', 'sr1.id');
                })
                ->whereNotNull('sr1.user_id')
                ->pluck('sr1.id')
                ->unique()
                ->values()
                ->all();

            if ($ids !== []) {
                DB::table('survey_responses')->whereIn('id', $ids)->delete();
            }
        }

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->unique(['survey_id', 'user_id'], 'survey_responses_survey_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropUnique('survey_responses_survey_user_unique');
        });
    }
};
