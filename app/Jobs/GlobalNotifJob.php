<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\GlobalNotifEvent;
class GlobalNotifJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $ReceiverID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ReceiverID)
    {
        $this->ReceiverID = $ReceiverID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Event(new GlobalNotifEvent($this->ReceiverID));
    }
}
