<?php

namespace App\Listeners;

use App\Events\NewMREvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMREventListener
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
     * @param  NewMREvent  $event
     * @return void
     */
    public function handle(NewMREvent $event)
    {
        //
    }
}
