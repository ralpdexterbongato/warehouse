<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewRVApprovedEvent;
class NewRVApprovedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $notifyWarehouse;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifyWarehouse)
    {
      $this->notifyWarehouse=$notifyWarehouse;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Event(new NewRVApprovedEvent($this->notifyWarehouse));
    }
}
