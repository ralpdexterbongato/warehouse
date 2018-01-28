<?php

namespace App\Listeners;

use App\Events\GlobalNotifWarehouseEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GlobalNotifWarehouseEventListener
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
     * @param  GlobalNotifWarehouseEvent  $event
     * @return void
     */
    public function handle(GlobalNotifWarehouseEvent $event)
    {
        //
    }
}
