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
      // $text = 'this is a message';
      // $number = '09105717885';
      // chdir('c:/xampp/htdocs/gnokii');
      // shell_exec('echo finally workssss 150 | gnokii --sendsms 09105717885');
      //still searching how to make this work inside queue ps:this is already working in controllers
      Event(new NewApprovedMIRSEvent($this->tobenotify));
    }
}
