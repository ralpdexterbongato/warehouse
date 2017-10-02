<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewMREvent;
class NewMRCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $notifythis;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifythis)
    {
       $this->notifythis=$notifythis;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Event(new NewMREvent($this->notifythis));
    }
}
