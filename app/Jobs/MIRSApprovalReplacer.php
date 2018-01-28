<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\NewApprovedMIRSEvent;
class MIRSApprovalReplacer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $NotifData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($NotifData)
    {
        $this->NotifData=$NotifData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        chdir('c:/xampp/htdocs/gnokii');
        shell_exec('echo The MIRS '.$this->NotifData->MIRSNo.' is now approved by '.$this->NotifData->ApprovalReplacer.' in behalf of you | gnokii --sendsms '.$this->NotifData->GMMobile);
        shell_exec('echo Your MIRS '.$this->NotifData->MIRSNo.' is now approved! | gnokii --sendsms '.$this->NotifData->RequisitionerMobile);
    }
}
