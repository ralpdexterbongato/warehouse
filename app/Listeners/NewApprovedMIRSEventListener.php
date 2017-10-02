<?php

namespace App\Listeners;

use App\Events\NewApprovedMIRSEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewApprovedMIRSEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewApprovedMIRSEvent  $event
     * @return void
     */
    public function handle(NewApprovedMIRSEvent $event)
    {
        //
    }
}
