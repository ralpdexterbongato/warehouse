<?php

namespace App\Listeners;

use App\Events\NewMIRSEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMIRSEventListener
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
     * @param  NewMIRSEvent  $event
     * @return void
     */
    public function handle(NewMIRSEvent $event)
    {
        //
    }
}
