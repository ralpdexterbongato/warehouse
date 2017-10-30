<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewMIRSEvent;
class SendMIRSNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $newmirs;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($newmirs)
    {
        $this->newmirs=$newmirs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        event(new NewMIRSEvent($this->newmirs));
    }
}
