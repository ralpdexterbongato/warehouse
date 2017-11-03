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
    public $notifyData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notifyData)
    {
      $this->notifyData=$notifyData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      chdir('c:/xampp/htdocs/gnokii');
      shell_exec('echo Your RV '.$this->notifyData->RVNo.' is now approved! | gnokii --sendsms '.$this->notifyData->RequisitionerMobile);
      Event(new NewRVApprovedEvent($this->notifyData));
    }
}
