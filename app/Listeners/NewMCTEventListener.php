<?php

namespace App\Listeners;

use App\Events\NewMCTEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMCTEventListener
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
     * @param  NewMCTEvent  $event
     * @return void
     */
    public function handle(NewMCTEvent $event)
    {
        //
    }
}
