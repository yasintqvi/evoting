<?php

namespace App\Jobs;

use App\Models\ShortMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ClearMessageJob implements ShouldQueue
{
    use Queueable;

    private string $to;

    /**
     * Create a new job instance.
     */
    public function __construct(string $to)
    {
        $this->to = $to;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $messages = ShortMessage::where('to', '=', $this->to)->get();
        foreach ($messages as $message) {
            $message->delete();
        }
    }
}
