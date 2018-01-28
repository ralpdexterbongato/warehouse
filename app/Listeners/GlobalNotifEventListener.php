<?php

namespace App\Listeners;

use App\Events\GlobalNotifEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GlobalNotifEventListener
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
     * @param  GlobalNotifEvent  $event
     * @return void
     */
    public function handle(GlobalNotifEvent $event)
    {
        //
    }
}
