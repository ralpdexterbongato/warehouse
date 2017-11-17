<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewPOEvent;
class NewCreatedPOJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $NotifyId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($NotifyId)
    {
        $this->NotifyId=$NotifyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Event(new NewPOEvent($this->NotifyId));
    }
}
