<?php

namespace App\Console\Commands;

use App\Models\Election;
use App\Models\Survey;
use Illuminate\Console\Command;

class CloseExpiredVoting extends Command
{
    /**
     * @var string
     */
    protected $signature = 'voting:close-expired';

    /**
     * @var string
     */
    protected $description = 'Finalize ongoing elections and active surveys whose end time has passed';

    public function handle(): int
    {
        $elections = 0;
        Election::expiredOngoing()->each(function (Election $election) use (&$elections) {
            $elections += (int) $election->finalizeIfExpired();
        });

        $surveys = 0;
        Survey::expiredActive()->each(function (Survey $survey) use (&$surveys) {
            $surveys += (int) $survey->finalizeIfExpired();
        });

        $this->info("Closed {$elections} election(s) and {$surveys} survey(s).");

        return self::SUCCESS;
    }
}
