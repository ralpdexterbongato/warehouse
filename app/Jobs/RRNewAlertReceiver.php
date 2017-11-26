<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RRNewAlertReceiver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $AlertForReceiver;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($AlertForReceiver)
    {
        $this->AlertForReceiver = $AlertForReceiver;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        chdir('c:/xampp/htdocs/gnokii');
        shell_exec('echo Your RV '.$this->AlertForReceiver->RVNo.' item/items has arrived! Please LOGIN and SIGNATURE the RR '.$this->AlertForReceiver->RRNo.' thank you | gnokii --sendsms '.$this->AlertForReceiver->Mobile);
    }
}
