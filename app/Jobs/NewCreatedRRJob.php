<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewRREvent;
class NewCreatedRRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $NotifableName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($NotifableName)
    {
        $this->NotifableName=$NotifableName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Event(new NewRREvent($this->NotifableName->first));
      Event(new NewRREvent($this->NotifableName->second));
      Event(new NewRREvent($this->NotifableName->third));
    }
}
