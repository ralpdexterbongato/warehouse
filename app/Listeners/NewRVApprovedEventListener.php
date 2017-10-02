<?php

namespace App\Listeners;

use App\Events\NewRVApprovedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRVApprovedEventListener
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
     * @param  NewRVApprovedEvent  $event
     * @return void
     */
    public function handle(NewRVApprovedEvent $event)
    {
        //
    }
}
