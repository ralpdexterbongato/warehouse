<?php

namespace App\Listeners;

use App\Events\NewMRTEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMRTEventListener
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
     * @param  NewMRTEvent  $event
     * @return void
     */
    public function handle(NewMRTEvent $event)
    {
        //
    }
}
