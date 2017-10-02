<?php

namespace App\Listeners;

use App\Events\NewPOEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPOEventListener
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
     * @param  NewPOEvent  $event
     * @return void
     */
    public function handle(NewPOEvent $event)
    {
        //
    }
}
