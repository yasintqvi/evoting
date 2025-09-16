<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPasswordJob implements ShouldQueue
{
    use Queueable;
    public $password;

    /**
     * Create a new job instance.
     */
    public function __construct($password)
    {
        $this->password=$password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //send sms
    }
}
