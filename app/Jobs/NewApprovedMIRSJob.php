<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewApprovedMIRSEvent;
class NewApprovedMIRSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $role;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($role)
    {
        $this->role=$role;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Event(new NewApprovedMIRSEvent($this->role));
    }
}
