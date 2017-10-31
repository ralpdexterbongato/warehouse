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
    public $tobenotify;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tobenotify)
    {
        $this->tobenotify=$tobenotify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      chdir('c:/xampp/htdocs/gnokii');
      $output=shell_exec('echo '.$this->tobenotify->Requisitioner.' | gnokii --sendsms 09105717885');
      //fixed
      Event(new NewApprovedMIRSEvent($this->tobenotify));
    }
}
