<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewRVEvent;
class NewRVCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $NotifyThisPerson;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($NotifyThisPerson)
    {
        $this->NotifyThisPerson=$NotifyThisPerson;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Event(new NewRVEvent($this->NotifyThisPerson));
    }
}
